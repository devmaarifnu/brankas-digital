<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Setting;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Settings extends Controller
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function get($lookup)
    {
        $settings = Setting::pluck('value', 'lookup')->all();
        if (array_key_exists($lookup, $settings)) {
            return $settings[$lookup];
        }
        return null;
    }

    public function pageSetting()
    {
        $settings = Setting::orderBy('id')->get()->toArray();
        $tapel = TahunPelajaran::orderBy('id', 'desc')->get();
        return view('admin.users.setting', compact('settings', 'tapel'));
    }

    public function saveSetting(Request $request)
    {

        $request->validate([
            'template_piagam' => 'file|mimes:docx|max:2024',
            'template_sk' => 'file|mimes:docx|max:2024'
        ]);

        foreach (Setting::pluck('value', 'lookup')->all() as $key => $setting) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $filename = str_replace("#", "", $file->getClientOriginalName());
                $fileStorage = storage_path('app/templates/' . $setting);
                if (file_exists($fileStorage)) {
                    unlink($fileStorage);
                }
                $file->storeAs('templates', $filename);
                Setting::where("lookup", '=', $key)->update([
                    "value" => $filename,
                ]);
            } elseif ($request->input($key) != null && $request->input($key) != $setting) {
                Setting::where("lookup", '=', $key)->update([
                    "value" => $request->input($key)
                ]);
            }
        }
        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
    }

    public function viewLogActivity(Request $request)
    {
        $paginatePerPage = 20;
        $logs = ActivityLog::with(["user", "satpen"])
            ->orderBy('id', 'desc')->paginate($paginatePerPage);

        if ($request->has('date_start') && $request->has('date_end')) {
            $logs = ActivityLog::with(["user", "satpen"])
                ->whereBetween('created_at', [$request->date_start, $request->date_end])
                ->orderBy('id', 'desc')->paginate($paginatePerPage);
        }

        return view('admin.users.log_activity', compact('logs'));
    }
}
