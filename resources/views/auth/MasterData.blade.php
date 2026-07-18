@extends('layouts.main')

@section('title', 'Pengaturan Master Data')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-heading tracking-tight mb-2">Pengaturan Master Data</h1>
        <p class="text-body">Kelola profil sekolah, identitas guru, spesifikasi mata pelajaran, dan bobot nilai.</p>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/30 rounded-base text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <form action="{{ route('master-data.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- 1. School Information Card (Matching sketch layout with logo circle on top left) -->
        <div class="bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Circular Logo Uploader on top-left / left side -->
                <div class="flex flex-col items-center justify-start shrink-0">
                    <label class="block text-sm font-bold text-heading mb-3 self-start">Logo Sekolah</label>
                    <div class="relative group cursor-pointer">
                        <div class="h-32 w-32 rounded-full overflow-hidden border-2 border-dashed border-default hover:border-brand flex items-center justify-center bg-neutral-secondary-medium transition-all duration-200" id="logo-preview-container">
                            @if($setting->school_logo)
                                <img src="{{ asset('storage/' . $setting->school_logo) }}" alt="Logo" class="h-full w-full object-cover" id="logo-preview-img">
                            @else
                                <div class="text-center p-3 text-body" id="logo-placeholder">
                                    <svg class="mx-auto h-8 w-8 text-neutral-400 mb-1" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="text-xs font-semibold text-brand">Unggah</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="school_logo_file" id="school_logo_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" onchange="previewLogo(this)">
                    </div>
                    <span class="text-[11px] text-body mt-2">Format: JPG, PNG (Maks. 2MB)</span>
                    @error('school_logo_file')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- School Form Inputs -->
                <div class="flex-1 space-y-4">
                    <h3 class="text-lg font-bold text-heading border-b border-default pb-2">Profil Sekolah</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="school_name" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Nama Sekolah</label>
                            <input type="text" name="school_name" id="school_name" value="{{ old('school_name', $setting->school_name) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('school_name') border-fg-danger-strong @enderror" placeholder="contoh: SMA Negeri 1 Jakarta">
                            @error('school_name')
                                <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="npsn" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">NPSN</label>
                            <input type="text" name="npsn" id="npsn" value="{{ old('npsn', $setting->npsn) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('npsn') border-fg-danger-strong @enderror" placeholder="8 digit Nomor Pokok Sekolah Nasional">
                            @error('npsn')
                                <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="principal_name" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Nama Kepala Sekolah</label>
                        <input type="text" name="principal_name" id="principal_name" value="{{ old('principal_name', $setting->principal_name) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('principal_name') border-fg-danger-strong @enderror" placeholder="Nama lengkap kepala sekolah beserta gelar">
                        @error('principal_name')
                            <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="school_address" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Alamat Sekolah</label>
                        <textarea name="school_address" id="school_address" rows="3" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('school_address') border-fg-danger-strong @enderror" placeholder="Alamat lengkap sekolah">{{ old('school_address', $setting->school_address) }}</textarea>
                        @error('school_address')
                            <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Teacher Information Card -->
        <div class="bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-heading border-b border-default pb-2">Identitas Guru</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="teacher_name" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Nama Guru</label>
                    <input type="text" name="teacher_name" id="teacher_name" value="{{ old('teacher_name', $setting->teacher_name) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('teacher_name') border-fg-danger-strong @enderror" placeholder="Nama lengkap guru">
                    @error('teacher_name')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="teacher_nip" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">NIP</label>
                    <input type="text" name="teacher_nip" id="teacher_nip" value="{{ old('teacher_nip', $setting->teacher_nip) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('teacher_nip') border-fg-danger-strong @enderror" placeholder="18 digit Nomor Induk Pegawai">
                    @error('teacher_nip')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="teacher_email" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" name="teacher_email" id="teacher_email" value="{{ old('teacher_email', $setting->teacher_email) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('teacher_email') border-fg-danger-strong @enderror" placeholder="guru@sekolah.sch.id">
                    @error('teacher_email')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="teacher_phone" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Nomor Telepon</label>
                    <input type="text" name="teacher_phone" id="teacher_phone" value="{{ old('teacher_phone', $setting->teacher_phone) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('teacher_phone') border-fg-danger-strong @enderror" placeholder="contoh: 08123456789">
                    @error('teacher_phone')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 3. Subject Specifications & Grading Weights Card -->
        <div class="bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm space-y-6">
            <h3 class="text-lg font-bold text-heading border-b border-default pb-2">Pengaturan Akademik & Penilaian</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Subject specs -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-heading uppercase tracking-wider">Spesifikasi Mata Pelajaran</h4>
                    
                    <div>
                        <label for="subject_name" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Nama Mata Pelajaran</label>
                        <input type="text" name="subject_name" id="subject_name" value="{{ old('subject_name', $setting->subject_name) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('subject_name') border-fg-danger-strong @enderror" placeholder="contoh: Matematika Peminatan">
                        @error('subject_name')
                            <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="minimum_score" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Kriteria Ketuntasan Minimal (KKM)</label>
                        <input type="number" step="0.01" name="minimum_score" id="minimum_score" value="{{ old('minimum_score', $setting->minimum_score) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('minimum_score') border-fg-danger-strong @enderror" placeholder="contoh: 75.00">
                        @error('minimum_score')
                            <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Grading weights -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-heading uppercase tracking-wider">Bobot Penilaian</h4>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-brand-soft text-brand" id="weights-total-badge">Total: 100%</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="daily_test_weight" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Bobot Nilai Harian</label>
                            <input type="number" step="0.01" name="daily_test_weight" id="daily_test_weight" value="{{ old('daily_test_weight', $setting->daily_test_weight ?? 0.30) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('daily_test_weight') border-fg-danger-strong @enderror" oninput="calculateTotalWeights()">
                            @error('daily_test_weight')
                                <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="assignment_weight" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Bobot Nilai Tugas</label>
                            <input type="number" step="0.01" name="assignment_weight" id="assignment_weight" value="{{ old('assignment_weight', $setting->assignment_weight ?? 0.20) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('assignment_weight') border-fg-danger-strong @enderror" oninput="calculateTotalWeights()">
                            @error('assignment_weight')
                                <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="midterm_weight" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Bobot Nilai UTS</label>
                            <input type="number" step="0.01" name="midterm_weight" id="midterm_weight" value="{{ old('midterm_weight', $setting->midterm_weight ?? 0.25) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('midterm_weight') border-fg-danger-strong @enderror" oninput="calculateTotalWeights()">
                            @error('midterm_weight')
                                <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="final_weight" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Bobot Nilai UAS</label>
                            <input type="number" step="0.01" name="final_weight" id="final_weight" value="{{ old('final_weight', $setting->final_weight ?? 0.25) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('final_weight') border-fg-danger-strong @enderror" oninput="calculateTotalWeights()">
                            @error('final_weight')
                                <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Submit Actions -->
        <div class="flex items-center justify-end gap-4 border-t border-default pt-6">
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-base text-sm font-semibold border border-default hover:bg-neutral-tertiary text-body transition-all duration-200">Batal</a>
            <button type="submit" class="bg-brand hover:bg-brand-strong text-white px-6 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer">Simpan Pengaturan</button>
        </div>
    </form>
</div>

<script>
    // Live logo preview implementation
    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const container = document.getElementById('logo-preview-container');
                let img = document.getElementById('logo-preview-img');
                
                // If image element doesn't exist, create it and remove placeholder
                if (!img) {
                    const placeholder = document.getElementById('logo-placeholder');
                    if (placeholder) {
                        placeholder.remove();
                    }
                    
                    img = document.createElement('img');
                    img.id = 'logo-preview-img';
                    img.className = 'h-full w-full object-cover';
                    container.appendChild(img);
                }
                
                img.src = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Interactive weight indicator
    function calculateTotalWeights() {
        const daily = parseFloat(document.getElementById('daily_test_weight').value) || 0;
        const assignment = parseFloat(document.getElementById('assignment_weight').value) || 0;
        const midterm = parseFloat(document.getElementById('midterm_weight').value) || 0;
        const final = parseFloat(document.getElementById('final_weight').value) || 0;
        
        let total = daily + assignment + midterm + final;
        
        // Handle decimals vs percentage representation
        if (total <= 1.05 && total > 0.05) {
            total = total * 100;
        }
        
        const badge = document.getElementById('weights-total-badge');
        badge.innerText = 'Total: ' + total.toFixed(0) + '%';
        
        if (total.toFixed(0) === '100') {
            badge.className = 'text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300';
        } else {
            badge.className = 'text-xs font-semibold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/30 dark:text-rose-300';
        }
    }

    // Run on initial load
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotalWeights();
    });
</script>
@endsection
