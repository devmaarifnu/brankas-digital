<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Settings;
use App\Models\Others;
use App\Models\PDPTK;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyOperator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role == 'operator') {
            if (auth()->user()->status_active == 'block') {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['success' => false, 'message' => 'Akun operator ini telah dinonaktifkan (blocked).'], 403);
                }
                return redirect()->route('login')->with('error', 'this account has blocked');
            }

            $satpen = $request->route('satpen');
            if ($satpen != null) {
                if ($satpen->id_user !== auth()->user()->id_user) {
                    if ($request->expectsJson() || $request->is('api/*')) {
                        return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk Satpen ini.'], 403);
                    }
                    return redirect()->back()->with('error', 'you cannot access this page');
                }
            }

            // must fill pdptk data
            if ($request->routeIs('pdptk') || $request->routeIs('pdptk.*') || $request->is('api/*')) {
                return $next($request);
            }

            $pdptk = PDPTK::where('tapel', '=', Settings::get('current_tapel'))
                ->whereHas('satpen', function ($query) {
                    $query->where('id_user', '=', auth()->user()->id_user);
                })->first();

            if ($pdptk == null || $pdptk?->status_sinkron == 0) {
                return redirect()->route('pdptk');
            }

            // must fill other data
            if ($request->routeIs('other') || $request->routeIs('other.*')) {
                return $next($request);
            }

            $other = Others::whereHas('satpen', function ($query) {
                $query->where('id_user', '=', auth()->user()->id_user);
            })->first();

            if ($other == null || $other?->status_sinkron == 0) {
                return redirect()->route('other');
            }

            return $next($request);
        }
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => false, 'message' => 'User tidak memiliki hak akses Operator.'], 403);
        }
        return redirect()->route('login')->with('error', 'user tidak memiliki privilages');
    }
}
