<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validRoles = ['super admin', 'admin', 'viewer', 'aproval', 'approval', 'admin pusat', 'admin wilayah', 'admin cabang', 'operator'];
        if (auth()->user() !== NULL && in_array(auth()->user()->role, $validRoles)) {
            if (auth()->user()->status_active === 'block') {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['success' => false, 'message' => 'Akun ini telah dinonaktifkan (blocked).'], 403);
                }
                return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan.');
            }
            return $next($request);
        }
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.'
            ], 401);
        }
        return redirect()->route('login')->with('error', 'silahkan login terlebih dahulu');
    }
}
