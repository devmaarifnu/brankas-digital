<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PDPTK;

class DashboardApiController extends Controller
{
    /**
     * Get PTK count data for dashboard charts
     */
    public function getPTKCount()
    {
        try {
            $specificFilter = request()->specificFilter;
            Log::info("DashboardAPI: Getting PTK");

            // Query count PTK berdasarkan cabang - menggunakan field sesuai model PDPTK dan Satpen
            $ptkData = PDPTK::join('satpen as s', 'pdptk.id_satpen', '=', 's.id_satpen')
                ->whereHas('satpen', function($q) use ($specificFilter) {
                    $q->where($specificFilter);
                })
                ->whereIn('s.status', ['setujui', 'expired', 'perpanjangan'])
                ->select(
                    DB::raw('SUM(pdptk.guru_lk) as guru_lk'),
                    DB::raw('SUM(pdptk.guru_pr) as guru_pr'),
                    DB::raw('SUM(pdptk.tendik_lk) as tendik_lk'),
                    DB::raw('SUM(pdptk.tendik_pr) as tendik_pr'),
                    DB::raw('SUM(pdptk.jml_guru + pdptk.jml_tendik) as total_ptk')
                )
                ->first();

            $result = [
                'guru_lk' => (int) ($ptkData->guru_lk ?? 0),
                'guru_pr' => (int) ($ptkData->guru_pr ?? 0),
                'tendik_lk' => (int) ($ptkData->tendik_lk ?? 0),
                'tendik_pr' => (int) ($ptkData->tendik_pr ?? 0),
                'total_ptk' => (int) ($ptkData->total_ptk ?? 0),
            ];

            Log::info("DashboardAPI: PTK count result", $result);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error("DashboardAPI: Error getting PTK count", [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get Peserta Didik count data for dashboard charts
     */
    public function getPDCount()
    {
        try {
            $specificFilter = request()->specificFilter;
            Log::info("DashboardAPI: Getting PD count");

            // Query count Peserta Didik berdasarkan cabang - menggunakan field sesuai model PDPTK dan Satpen
            $pdData =  PDPTK::join('satpen as s', 'pdptk.id_satpen', '=', 's.id_satpen')
                ->whereHas('satpen', function($q) use ($specificFilter) {
                    $q->where($specificFilter);
                })
                ->whereIn('s.status', ['setujui', 'expired', 'perpanjangan'])
                ->select(
                    DB::raw('SUM(pdptk.pd_lk) as pd_lk'),
                    DB::raw('SUM(pdptk.pd_pr) as pd_pr'),
                    DB::raw('SUM(pdptk.pd_lk + pdptk.pd_pr) as total_peserta_didik')
                )
                ->first();

            $result = [
                'pd_lk' => (int) ($pdData->pd_lk ?? 0),
                'pd_pr' => (int) ($pdData->pd_pr ?? 0),
                'total_pd' => (int) ($pdData->total_peserta_didik ?? 0),
            ];

            Log::info("DashboardAPI: PD count result", $result);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error("DashboardAPI: Error getting PD count", [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}