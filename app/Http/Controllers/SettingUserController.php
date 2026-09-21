<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SettingUserController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Hanya Super admin yang memiliki akses ke fitur Manajemen Users.');
        }

        $query = User::query();

        // Pencarian Free-Text
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('username', 'like', "%{$q}%");
                if (Schema::hasColumn('users', 'email')) {
                    $w->orWhere('email', 'like', "%{$q}%");
                }
            });
        }

        // Filter Enumerasi: Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter Enumerasi: Status
        if ($request->filled('status_active')) {
            $query->where('status_active', $request->status_active);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('setting.users', compact('users'))->with('title', 'Manajemen Users');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Hanya Super admin yang dapat menambah user.');
        }

        $rules = [
            'name'          => 'required|string|max:100',
            'username'      => 'required|string|max:50|unique:users,username',
            'password'      => 'required|string|min:6',
            'role'          => 'required|in:super admin,admin,viewer,aproval',
            'status_active' => 'required|in:active,block',
        ];

        if (Schema::hasColumn('users', 'email')) {
            $rules['email'] = 'nullable|email|max:100|unique:users,email';
        }

        $request->validate($rules, [
            'username.unique' => 'Username tersebut sudah terdaftar, gunakan username lain.',
            'email.unique'    => 'Email tersebut sudah terdaftar.',
            'password.min'    => 'Password minimal 6 karakter.',
        ]);

        $userData = [
            'name'          => $request->name,
            'username'      => $request->username,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'status_active' => $request->status_active,
        ];

        if (Schema::hasColumn('users', 'email') && $request->filled('email')) {
            $userData['email'] = $request->email;
        }

        User::create($userData);

        return redirect()->route('setting.users')->with('success', 'User baru (' . $request->name . ') berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Hanya Super admin yang dapat mengubah data user.');
        }

        $user = User::findOrFail($id);

        $rules = [
            'name'          => 'required|string|max:100',
            'username'      => 'required|string|max:50|unique:users,username,' . $user->id_user . ',id_user',
            'role'          => 'required|in:super admin,admin,viewer,aproval',
            'status_active' => 'required|in:active,block',
            'password'      => 'nullable|string|min:6',
        ];

        if (Schema::hasColumn('users', 'email')) {
            $rules['email'] = 'nullable|email|max:100|unique:users,email,' . $user->id_user . ',id_user';
        }

        $request->validate($rules, [
            'username.unique' => 'Username tersebut sudah digunakan oleh user lain.',
            'password.min'    => 'Password baru minimal 6 karakter.',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->role = $request->role;
        $user->status_active = $request->status_active;

        if (Schema::hasColumn('users', 'email')) {
            $user->email = $request->email;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('setting.users')->with('success', 'Data user ' . $user->name . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Hanya Super admin yang dapat menghapus user.');
        }

        $user = User::findOrFail($id);

        // Cegah Super Admin menghapus akunnya sendiri yang sedang aktif
        if ($user->id_user == auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('setting.users')->with('success', 'User ' . $name . ' berhasil dihapus.');
    }

    public function toggle(Request $request, $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Hanya Super admin yang dapat mengubah status pengguna.');
        }

        $user = User::findOrFail($id);
        if ($user->id_user == auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif digunakan.');
        }

        $user->status_active = ($user->status_active == 'active' || $user->status_active == '1') ? 'block' : 'active';
        $user->save();

        return back()->with('success', 'Status user ' . $user->name . ' berhasil diubah.');
    }

    public function updateRole(Request $request, $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Hanya Super admin yang dapat mengubah role pengguna.');
        }

        $request->validate([
            'role' => 'required|in:super admin,admin,viewer,aproval',
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'Role pengguna ' . $user->name . ' berhasil diubah menjadi ' . $request->role . '.');
    }
}

