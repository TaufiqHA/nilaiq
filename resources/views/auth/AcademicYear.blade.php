@extends('layouts.main')

@section('title', 'Tahun Ajaran')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="hidden sm:block text-3xl font-extrabold text-heading tracking-tight mb-2">Tahun Ajaran</h1>
            <p class="text-body">Kelola tahun ajaran aktif dan semester (Ganjil/Genap) untuk sistem penilaian.</p>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- 1. Form Card (1 Column wide on lg) -->
        <div class="w-full bg-transparent sm:bg-neutral-primary-soft border-0 sm:border border-default rounded-none sm:rounded-base p-0 sm:p-6 shadow-none sm:shadow-sm self-start">
            <h3 class="text-lg font-bold text-heading border-b border-default pb-3 mb-4" id="form-title">Tambah Tahun Ajaran</h3>
            
            <form id="academic-year-form" action="{{ route('academic-years.store') }}" method="POST" class="space-y-4">
                @csrf
                <div id="method-container"></div>

                <!-- Year Input -->
                <div>
                    <label for="year" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                    <input type="text" name="year" id="year" value="{{ old('year') }}" required
                           class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('year') border-fg-danger-strong @enderror" 
                           placeholder="contoh: 2025/2026">
                    <span class="text-[11px] text-body mt-1 block">Format: YYYY/YYYY (misal: 2025/2026)</span>
                    @error('year')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Semester Select -->
                <div>
                    <label for="semester" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Semester</label>
                    <select name="semester" id="semester" required
                            class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('semester') border-fg-danger-strong @enderror">
                        <option value="GANJIL" {{ old('semester') === 'GANJIL' ? 'selected' : '' }}>GANJIL</option>
                        <option value="GENAP" {{ old('semester') === 'GENAP' ? 'selected' : '' }}>GENAP</option>
                    </select>
                    @error('semester')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Checkbox -->
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-brand focus:ring-brand bg-neutral-secondary-medium border-default">
                    <label for="is_active" class="text-sm font-medium text-heading">Setel sebagai Aktif</label>
                </div>
                <span class="text-[11px] text-body block leading-normal">Mengaktifkan tahun ajaran ini akan otomatis menonaktifkan tahun ajaran lainnya.</span>

                <!-- Form Buttons -->
                <div class="flex items-center justify-end gap-3 border-t border-default pt-4 mt-6">
                    <button type="button" id="btn-cancel" class="hidden px-4 py-2 rounded-base text-sm font-semibold border border-default hover:bg-neutral-tertiary text-body transition-all duration-200">
                        Batal
                    </button>
                    <button type="submit" class="bg-brand hover:bg-brand-strong text-white px-5 py-2 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. List Card (2 Columns wide on lg) -->
        <div class="lg:col-span-2 bg-transparent sm:bg-neutral-primary-soft border-0 sm:border border-default rounded-none sm:rounded-base p-0 sm:p-6 shadow-none sm:shadow-sm">
            <h3 class="text-lg font-bold text-heading border-b border-default pb-3 mb-4">Daftar Tahun Ajaran</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-default">
                            <th class="pb-3 text-xs font-bold text-heading uppercase tracking-wider min-w-[150px]">Tahun Ajaran</th>
                            <th class="pb-3 text-xs font-bold text-heading uppercase tracking-wider min-w-[120px]">Semester</th>
                            <th class="pb-3 text-xs font-bold text-heading uppercase tracking-wider min-w-[120px]">Status</th>
                            <th class="pb-3 text-xs font-bold text-heading uppercase tracking-wider text-right min-w-[150px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-default">
                        @forelse($academicYears as $year)
                            <tr class="hover:bg-neutral-secondary-soft transition-colors duration-150">
                                <td class="py-3.5 text-sm font-semibold text-heading">{{ $year->year }}</td>
                                <td class="py-3.5 text-sm text-body">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-secondary-medium border border-default text-heading">
                                        {{ $year->semester }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-sm">
                                    @if($year->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/30">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-secondary-medium border border-default text-fg-disabled">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-sm text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Edit button triggers Javascript fill -->
                                        <button type="button" 
                                                onclick="editAcademicYear({{ json_encode($year) }})"
                                                class="text-brand hover:text-brand-strong bg-transparent font-semibold text-xs py-1.5 px-3 border border-default hover:border-brand rounded-base transition-all duration-200 cursor-pointer">
                                            Ubah
                                        </button>

                                        <!-- Delete button triggers form submission -->
                                        <form action="{{ route('academic-years.delete', $year->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-fg-danger-strong hover:text-red-700 bg-transparent font-semibold text-xs py-1.5 px-3 border border-default hover:border-fg-danger-strong rounded-base transition-all duration-200 cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-sm text-fg-disabled">
                                    Belum ada data tahun ajaran. Silakan tambahkan baru di form sebelah kiri.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const defaultAction = "{{ route('academic-years.store') }}";

    function editAcademicYear(yearData) {
        // Change form header and action URL
        document.getElementById('form-title').innerText = 'Ubah Tahun Ajaran';
        const form = document.getElementById('academic-year-form');
        form.action = `/academic-years/${yearData.id}`;

        // Set inputs
        document.getElementById('year').value = yearData.year;
        document.getElementById('semester').value = yearData.semester;
        document.getElementById('is_active').checked = !!yearData.is_active;

        // Add PUT method input
        const methodContainer = document.getElementById('method-container');
        methodContainer.innerHTML = '@method("PUT")';

        // Show cancel button
        const btnCancel = document.getElementById('btn-cancel');
        btnCancel.classList.remove('hidden');

        // Scroll to form on mobile
        form.scrollIntoView({ behavior: 'smooth' });
        document.getElementById('year').focus();
    }

    document.getElementById('btn-cancel').addEventListener('click', function() {
        // Reset form header and action URL
        document.getElementById('form-title').innerText = 'Tambah Tahun Ajaran';
        const form = document.getElementById('academic-year-form');
        form.action = defaultAction;
        form.reset();

        // Clear PUT method input
        document.getElementById('method-container').innerHTML = '';

        // Hide cancel button
        this.classList.add('hidden');
    });
</script>
@endsection
