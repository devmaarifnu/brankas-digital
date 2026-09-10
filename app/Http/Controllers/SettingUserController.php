<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class SettingUserController extends Controller {
    public function index() {
        $users = User::orderBy('created_at','desc')->get();
        return view('setting.users', compact('users'))->with('title', 'Manajemen Users');
    }
    public function toggle(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->status_active = !$user->status_active;
        $user->save();
        return back()->with('success', 'Status user berhasil diubah.');
    }
}
