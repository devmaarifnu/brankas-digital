<?php

namespace App\Http\Controllers;

use App\Helpers\MailService;
use App\Models\PasswordReset;
use App\Models\Satpen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function showForgetPasswordForm()
    {
        return view('auth.forgetPassword');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitForgetPasswordForm(Request $request)
    {
        $request->validate([
            'no_registrasi' => 'required|exists:satpen',
        ]);
        $satpen = Satpen::where('no_registrasi', $request->no_registrasi)->first();
        $token = Str::random(64);

        PasswordReset::create([
            'email' => $satpen->email,
            'token' => $token,
        ]);

        $link_reset = url('auth/reset', $token);
        MailService::send([
            "to" => $satpen->email,
            "subject" => "Reset Password",
            "recipient" => "Operator Sekolah",
            "content" => "<p>Anda dapat mengatur ulang kata sandi dari tautan di bawah ini:</p>
                            <a href='$link_reset' class='cta-button'>Reset Password</a>"
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => sprintf("Link reset password telah dikirimkan ke email %s.", $satpen->email)
            ]);
        }

        return back()->with('success', sprintf("Link reset password telah dikirimkan pada email %s !", $satpen->email));
    }

    /**
     * Write code on Method
     *
     * @return response()
     */

    public function showResetPasswordForm($token) {
        $tokenReset = PasswordReset::where(['token'=> $token])->first();
        if (!$tokenReset) {
            return redirect()->route('forgot')->with('error', 'Link reset tidak valid');
        }
        return view('auth.forgetPasswordLink', ['token' => $token]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:satpen',
            'new_password' => 'required|string',
            'password_confirm' => 'required|same:new_password'
        ]);

        $updatePassword = PasswordReset::where([
                'email' => $request->email,
                'token' => $request->token
            ])->first();

        if(!$updatePassword){
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token reset password tidak valid atau telah kadaluarsa.'
                ], 400);
            }
            return back()->with('error', 'Invalid token!');
        }

        $satpen = Satpen::where('email', $request->email)->first();
        User::where('id_user', '=', $satpen->id_user)
            ->update(['password' => Hash::make($request->new_password)]);

        PasswordReset::where(['email'=> $request->email])->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diganti. Silakan login dengan password baru.'
            ]);
        }

        return redirect()->route('login')->with('success', 'Password anda telah diganti!');
    }
}
