<?php

namespace App\Http\Controllers;

use App\Models\NPYP;
use App\Models\NPYPSatpen;
use App\Models\Satpen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NPYPUserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $npypSatpen = NPYPSatpen::with(['npyp'])
            ->whereHas('satpen', function ($query) use ($user) {
                $query->where('id_user', $user->id_user);
            })
            ->first();

        return view('npyp.index', compact('npypSatpen'));
    }

}