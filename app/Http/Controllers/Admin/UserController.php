<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->orderBy('name', 'asc')->get();

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('auth.admin.user', compact('users'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:admin,wali_kelas,mapel'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'User created successfully.',
                'data' => $user,
            ], 201);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, User $user): JsonResponse|View
    {
        if ($request->wantsJson()) {
            return response()->json($user);
        }

        $users = User::orderBy('name', 'asc')->get();

        return view('auth.admin.user', compact('users', 'user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:admin,wali_kelas,mapel'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'User updated successfully.',
                'data' => $user,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse|JsonResponse
    {
        if ($user->id === auth()->id()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
                ], 400);
            }

            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'User deleted successfully.',
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(User $user): RedirectResponse|JsonResponse
    {
        return $this->destroy($user);
    }
}
