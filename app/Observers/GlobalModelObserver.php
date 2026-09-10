<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class GlobalModelObserver
{
    public function created(Model $model)
    {
        $this->log('insert', $model);
    }

    public function updated(Model $model)
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);
        if (!empty($changes)) {
            $this->log('update', $model, $changes);
        }
    }

    public function deleted(Model $model)
    {
        $this->log('delete', $model);
    }

    protected function log(string $action, Model $model, array $changes = null)
    {
        if ($model instanceof \App\Models\ActivityLog) return; // skip self-log

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'menu_name'   => $this->getMenuName($model),
            'table_name'  => $model->getTable(),
            'record_id'   => $model->getKey(),
            'changes'     => $changes ?: $model->toArray(),
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'url'         => request()->fullUrl(),
            'route'       => optional(request()->route())->getName(),
            'description' => $this->generateDescription($action, $model),
        ]);
    }

    public function generateDescription($action, $model)
    {
        return "";
    }

    public function getMenuName($model)
    {
        $menu = [
            "oss_new" => "Layanan OSS",
            "bhpnu" => "Layanan BHPNU",
            "coretax" => "Layanan Coretax",
            "informasi" => "Informasi Portal",
            "pdptk" => "Data PDPTK",
            "data_lainnya" => "Data Lainnya",
            "profile_pengurus_cabang" => "Profile Pengurus Cabang",
            "profile_pengurus_wilayah" => "Profile Pengurus Wilayah",
            "satpen" => "Satuan Pendidikan",
            "virtual_npsn" => "Virtual NPSN",
            "users" => "Users",
        ];

        return $menu[$model->getTable()] ?? "";
    }
}
