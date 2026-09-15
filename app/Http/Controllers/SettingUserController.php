<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class SettingUserController extends Controller {
    public function index() {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Hanya Super admin yang memiliki akses penuh ke fitur Manajemen Users.');
        }
        $users = User::orderBy('created_at','desc')->get();
        return view('setting.users', compact('users'))->with('title', 'Manajemen Users');
    }
    public function toggle(Request $request, $id) {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('handover.index')->with('error', 'Akses ditolak: Hanya Super admin yang dapat mengubah status pengguna.');
        }
        $user = User::findOrFail($id);
        $user->status_active = $user->status_active == 'active' ? 'block' : 'active';
        $user->save();
        return back()->with('success', 'Status user berhasil diubah.');
    }

    public function updateRole(Request $request, $id) {
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
