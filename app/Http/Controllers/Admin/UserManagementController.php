<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    /**
     * Menampilkan daftar semua user.
     */
    public function index(Request $request)
    {
        // Tentukan kolom yang boleh diurutkan
        $sortableColumns = ['name', 'email', 'role', 'created_at'];
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');

        // Validasi kolom sort dan arah sort
        if (!in_array($sortBy, $sortableColumns)) {
            $sortBy = 'created_at';
        }
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Ambil keyword search (jika ada)
        $search = $request->query('search');

        // Query user dengan optional search
        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Sorting dan paginasi
        $users = $query->orderBy($sortBy, $sortDirection)->paginate(10);

        // Simpan query agar pagination & sort tetap berfungsi saat search
        $users->appends($request->query());

        return view('admin.users.index', compact('users', 'sortBy', 'sortDirection'));
    }

    /**
     * Menampilkan form tambah user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class, 'ends_with:@school.com,@admin.com'],
            'role' => ['required', 'string', Rule::in(['admin', 'user'])],
            'password' => ['required', 'string', 'min:1', 'confirmed'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit user.
     */
    public function edit(User $user)
    {
        // Aturan baru: Akun primordial tidak bisa diedit
        if ($user->email === 'superadmin@admin.com') { // <-- GANTI DENGAN EMAIL PRIMORDIAL-MU
            return redirect()->route('admin.users.index')->with('error', 'Akun Super Admin tidak bisa diedit.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Meng-update data user di database.
     */
    public function update(Request $request, User $user)
    {
        // Aturan baru: Akun primordial tidak bisa diedit
        if ($user->email === 'superadmin@admin.com') {
            return redirect()->route('admin.users.index')->with('error', 'Akun Super Admin tidak bisa diedit.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'user'])],
            'password' => ['nullable', 'string', 'min:1', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        // Aturan 1: Admin tidak bisa menghapus akunnya sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        // Aturan 2: Siapapun tidak bisa menghapus akun "primordial"
        if ($user->email === 'superadmin@admin.com') { // <-- GANTI DENGAN EMAIL PRIMORDIAL-MU
            return back()->with('error', 'Akun Super Admin tidak bisa dihapus.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
