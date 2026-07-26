@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="hidden sm:block text-3xl font-extrabold text-heading tracking-tight mb-2">Manajemen User</h1>
            <p class="text-body">Kelola hak akses pengguna sistem (Admin, Wali Kelas, dan Guru Mata Pelajaran).</p>
        </div>
        <div>
            <button type="button" onclick="openCreateModal()" class="w-full sm:w-auto text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-semibold leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer inline-flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah User
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/30 rounded-base text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/30 rounded-base text-red-800 dark:text-red-300 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span class="font-medium text-sm">{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/30 rounded-base text-red-800 dark:text-red-300 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <span class="font-bold text-sm">Terjadi kesalahan validasi:</span>
            <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- List Card (Full Width) -->
    <div class="bg-transparent sm:bg-neutral-primary-soft border-0 sm:border border-default rounded-none sm:rounded-base p-0 sm:p-6 shadow-none sm:shadow-sm">
        <h3 class="text-lg font-bold text-heading border-b border-default pb-3 mb-4">Daftar Pengguna</h3>
        
        <!-- Search & Filter Area -->
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." 
                       class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand">
            </div>
            <div class="w-full sm:w-48">
                <select name="role" onchange="this.form.submit()" 
                        class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading focus:outline-none focus:border-brand">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="wali_kelas" {{ request('role') === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                    <option value="mapel" {{ request('role') === 'mapel' ? 'selected' : '' }}>Guru Mapel</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="bg-brand hover:bg-brand-strong text-white px-4 py-2 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer">
                    Cari
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-base text-sm font-semibold border border-default hover:bg-neutral-tertiary text-body transition-all duration-200">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-default">
                        <th class="pb-3 text-xs font-bold text-heading uppercase tracking-wider min-w-[150px]">Nama</th>
                        <th class="pb-3 text-xs font-bold text-heading uppercase tracking-wider min-w-[150px]">Email</th>
                        <th class="pb-3 text-xs font-bold text-heading uppercase tracking-wider min-w-[100px]">Peran</th>
                        <th class="pb-3 text-xs font-bold text-heading uppercase tracking-wider text-right min-w-[120px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default">
                    @forelse($users as $user)
                        <tr class="hover:bg-neutral-secondary-soft transition-colors duration-150">
                            <td class="py-3.5 text-sm font-semibold text-heading">{{ $user->name }}</td>
                            <td class="py-3.5 text-sm text-body">{{ $user->email }}</td>
                            <td class="py-3.5 text-sm">
                                @if($user->role === 'admin')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 dark:bg-purple-950/20 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-900/30">
                                        Admin
                                    </span>
                                @elseif($user->role === 'wali_kelas')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-950/20 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-900/30">
                                        Wali Kelas
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900/30">
                                        Guru Mapel
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 text-sm text-right">
                                <div class="inline-flex items-center gap-2">
                                    <!-- Edit button -->
                                    <button type="button" 
                                            onclick="editUser({{ json_encode($user) }})"
                                            class="text-brand hover:text-brand-strong bg-transparent font-semibold text-xs py-1.5 px-3 border border-default hover:border-brand rounded-base transition-all duration-200 cursor-pointer">
                                        Ubah
                                    </button>

                                    <!-- Delete button with self safeguard -->
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-fg-danger-strong hover:text-red-700 bg-transparent font-semibold text-xs py-1.5 px-3 border border-default hover:border-fg-danger-strong rounded-base transition-all duration-200 cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-fg-disabled font-medium py-1.5 px-3 border border-default rounded-base cursor-not-allowed bg-neutral-secondary-soft">
                                            Self
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-sm text-fg-disabled">
                                Belum ada data user. Silakan tambahkan baru dengan tombol "Tambah User" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form User (Tambah / Edit) -->
<div id="user-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-neutral-primary-soft border border-default rounded-base shadow-sm p-4 md:p-6">
            <!-- Modal header -->
            <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                <h3 id="form-title" class="text-lg font-bold text-heading">
                    Tambah User
                </h3>
                <button type="button" onclick="closeUserModal()" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center cursor-pointer">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <form id="user-form" action="{{ route('admin.users.store') }}" method="POST" class="p-4 space-y-4">
                @csrf
                <div id="method-container"></div>
                <input type="hidden" name="edit_user_id" id="edit_user_id" value="{{ old('edit_user_id') }}">

                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('name') border-fg-danger-strong @enderror" 
                           placeholder="contoh: Budi Setiawan">
                    @error('name')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('email') border-fg-danger-strong @enderror" 
                           placeholder="contoh: budi@nilaiq.com">
                    @error('email')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('password') border-fg-danger-strong @enderror" 
                           placeholder="••••••••">
                    <span id="password-hint" class="text-[11px] text-body mt-1 block hidden">Kosongkan jika tidak ingin mengubah password.</span>
                    @error('password')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Select -->
                <div>
                    <label for="role" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Peran (Role)</label>
                    <select name="role" id="role" required
                            class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('role') border-fg-danger-strong @enderror">
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="wali_kelas" {{ old('role') === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                        <option value="mapel" {{ old('role') === 'mapel' ? 'selected' : '' }}>Guru Mapel</option>
                    </select>
                    @error('role')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Buttons -->
                <div class="flex items-center justify-end gap-3 border-t border-default pt-4 mt-6">
                    <button type="button" onclick="closeUserModal()" class="px-4 py-2 rounded-base text-sm font-semibold border border-default hover:bg-neutral-tertiary text-body transition-all duration-200">
                        Batal
                    </button>
                    <button type="submit" class="bg-brand hover:bg-brand-strong text-white px-5 py-2 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const defaultAction = "{{ route('admin.users.store') }}";

    function openUserModal() {
        document.getElementById('user-modal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('user-modal').classList.add('hidden');
    }

    function openCreateModal() {
        // Reset form header and action URL
        document.getElementById('form-title').innerText = 'Tambah User';
        const form = document.getElementById('user-form');
        form.action = defaultAction;
        form.reset();

        document.getElementById('edit_user_id').value = '';

        // Reset password required attribute and hide hint
        const passwordInput = document.getElementById('password');
        passwordInput.setAttribute('required', 'required');
        document.getElementById('password-hint').classList.add('hidden');

        // Clear PUT method input
        document.getElementById('method-container').innerHTML = '';

        openUserModal();
    }

    function editUser(userData) {
        // Change form header and action URL
        document.getElementById('form-title').innerText = 'Ubah User';
        const form = document.getElementById('user-form');
        form.action = `/admin/users/${userData.id}`;

        // Set inputs
        document.getElementById('edit_user_id').value = userData.id;
        document.getElementById('name').value = userData.name;
        document.getElementById('email').value = userData.email;
        document.getElementById('role').value = userData.role;
        
        // Remove password required attribute and show hint
        const passwordInput = document.getElementById('password');
        passwordInput.removeAttribute('required');
        document.getElementById('password-hint').classList.remove('hidden');

        // Add PUT method input
        const methodContainer = document.getElementById('method-container');
        methodContainer.innerHTML = '@method("PUT")';

        openUserModal();
        document.getElementById('name').focus();
    }

    // Automatically open modal and restore state if validation fails
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            const editUserId = "{{ old('edit_user_id') }}";
            if (editUserId) {
                // Restore edit mode
                document.getElementById('form-title').innerText = 'Ubah User';
                const form = document.getElementById('user-form');
                form.action = `/admin/users/${editUserId}`;
                
                // Keep password optional
                const passwordInput = document.getElementById('password');
                passwordInput.removeAttribute('required');
                document.getElementById('password-hint').classList.remove('hidden');
                
                // Add PUT method input
                const methodContainer = document.getElementById('method-container');
                methodContainer.innerHTML = '@method("PUT")';
            } else {
                // Restore create mode
                document.getElementById('form-title').innerText = 'Tambah User';
                const passwordInput = document.getElementById('password');
                passwordInput.setAttribute('required', 'required');
                document.getElementById('password-hint').classList.add('hidden');
                document.getElementById('method-container').innerHTML = '';
            }
            openUserModal();
        });
    @endif
</script>
@endsection
