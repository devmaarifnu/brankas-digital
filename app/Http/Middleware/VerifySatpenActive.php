<?php

namespace App\Http\Middleware;

use App\Models\ProfilePengurusCabang;
use App\Models\ProfilePengurusWilayah;
use App\Models\Satpen;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySatpenActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        if (in_array(auth()->user()->role, ["admin wilayah", "admin cabang"])){
            
            if ($request->segment(1) == 'coretax') {
                if (auth()->user()->role == "admin wilayah") {
                    $pwilayah = ProfilePengurusWilayah::where("id_pw", auth()->user()->provId)->first();
                    if (!$pwilayah) {
                        return redirect()->route('coretax.403');
                    }
                }
                elseif (auth()->user()->role == "admin cabang") {
                    $pcabang = ProfilePengurusCabang::where("id_pc", auth()->user()->cabangId)->first();
                    if (!$pcabang) {
                        return redirect()->route('coretax.403');
                    }
                }
            }
            
            return $next($request);
        }
        
        $satpen = Satpen::select(["id_user","status"])
            ->where('id_user', '=', auth()->user()->id_user)
            ->first();
        if ('setujui' != $satpen->status)
        {
            if ($request->segment(1) == 'oss') return redirect()->route('oss.403');
            elseif ($request->segment(1) == 'bhpnu') return redirect()->route('bhpnu.403');
            elseif ($request->segment(1) == 'coretax') return redirect()->route('coretax.403');
            else return redirect()->route('oss.403');
        }

        return $next($request);
    }
}
