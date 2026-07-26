<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - NilaiQ</title>
    
    <!-- Inline script to prevent FOUC (flash of incorrect theme) -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Meta tags for SEO -->
    <meta name="description" content="Manage your academic tracking and view grades on NilaiQ.">
    
    <!-- Vite assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-secondary-medium min-h-screen relative overflow-x-hidden">
    <!-- Mobile Header / Top Bar -->
    <div class="flex items-center gap-3 p-3 sm:hidden bg-neutral-primary-soft border-b border-default sticky top-0 z-30">
        <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar" type="button" class="text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-base text-sm p-2 focus:outline-none">
           <span class="sr-only">Open sidebar</span>
           <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h10"/>
           </svg>
        </button>
        <span class="text-lg font-bold text-heading">@yield('title')</span>
    </div>

    <!-- Sidebar Inclusion -->
    @include('components.sidebar')

    <!-- Main Content Wrapper -->
    <div class="p-4 sm:ml-64">
        @yield('content')
    </div>

    <!-- Global Scan Loading Overlay -->
    <div id="scan-loading-overlay" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-neutral-primary-soft p-6 rounded-base border border-default shadow-lg max-w-sm w-full text-center mx-4">
            <svg class="animate-spin h-10 w-10 text-brand mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <h3 class="text-lg font-bold text-heading mb-1">Menganalisis Foto Nilai</h3>
            <p class="text-sm text-body">Membaca tulisan tangan nilai siswa menggunakan AI. Mohon tunggu beberapa saat...</p>
        </div>
    </div>

    <script>
        function compressImage(file, maxWidth = 1200, maxHeight = 1200, quality = 0.8) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = event => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > maxWidth) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width = Math.round((width * maxHeight) / height);
                                height = maxHeight;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob(blob => {
                            if (blob) {
                                const compressedFile = new File([blob], file.name, {
                                    type: file.type,
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            } else {
                                reject(new Error('Canvas to Blob conversion failed'));
                            }
                        }, file.type, quality);
                    };
                    img.onerror = err => reject(err);
                };
                reader.onerror = err => reject(err);
            });
        }

        window.scanGrades = function(inputElement, studentsList) {
            if (!inputElement.files || inputElement.files.length === 0) {
                return;
            }
            
            const file = inputElement.files[0];
            const overlay = document.getElementById('scan-loading-overlay');
            if (overlay) {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            }

            compressImage(file)
            .catch(err => {
                console.warn('Image compression failed, using original file:', err);
                return file;
            })
            .then(finalFile => {
                const formData = new FormData();
                formData.append('image', finalFile);
                formData.append('students', JSON.stringify(studentsList.map(s => ({ id: s.id, name: s.name }))));

                return fetch('{{ route("score-scan") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();
                    if (!response.ok) {
                        throw data;
                    }
                    return data;
                } else {
                    const text = await response.text();
                    console.error('Raw response:', text);
                    const match = text.match(/<title>(.*?)<\/title>/i);
                    const errorTitle = match ? match[1] : 'Internal Server Error';
                    throw { message: `Server returned non-JSON response (${response.status}): ${errorTitle}. Silakan periksa console log browser untuk detail error.` };
                }
            })
            .then(result => {
                if (result.success && result.data) {
                    let successCount = 0;
                    result.data.forEach(item => {
                        const studentId = item.student_id;
                        const score = item.score;
                        
                        const scoreInput = document.getElementById(`score-${studentId}`);
                        if (scoreInput) {
                            scoreInput.disabled = false;
                            scoreInput.value = score !== null ? score : '';
                            
                            // Mark as unsaved so user can review and save
                            if (typeof window.markUnsaved === 'function') {
                                window.markUnsaved(studentId);
                            } else if (typeof markUnsaved === 'function') {
                                markUnsaved(studentId);
                            }
                            successCount++;
                        }
                    });
                    
                    if (typeof showToast === 'function') {
                        showToast(`Berhasil mendeteksi nilai untuk ${successCount} siswa.`);
                    } else if (typeof window.showToast === 'function') {
                        window.showToast(`Berhasil mendeteksi nilai untuk ${successCount} siswa.`);
                    } else {
                        alert(`Berhasil mendeteksi nilai untuk ${successCount} siswa.`);
                    }
                } else {
                    alert('Gagal mendeteksi nilai dari gambar: ' + (result.message || 'Format tidak dikenal.'));
                }
            })
            .catch(error => {
                console.error('Scan Error:', error);
                alert('Gagal memproses gambar: ' + (error.message || 'Terjadi kesalahan pada server/API. Pastikan GEMINI_API_KEY sudah dikonfigurasi di file .env.'));
            })
            .finally(() => {
                inputElement.value = '';
                if (overlay) {
                    overlay.classList.remove('flex');
                    overlay.classList.add('hidden');
                }
            });
        };
    </script>
</body>
</html>
