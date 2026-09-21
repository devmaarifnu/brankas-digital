<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class DapoMaarifNU {

    private bool $status;
    private $result;

    private function set(bool $status, $result) {
        $this->status = $status;
        $this->result = $result;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getResult() {
        return $this->result;
    }

    public function clone($npsn)
    {
        try {
            $token = config('services.dapo.token');
            $url = str_replace('http://', 'https://', config('services.dapo.url')) . '/' . $npsn;
            Log::info("DapoMaarifNU: Requesting {$url}");

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . trim($token),
                    'Accept: application/json',
                ],
            ]);
            $body = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($statusCode >= 200 && $statusCode < 300) {
                $jsonResponse = json_decode($body, true);
                return $this->set(true, $jsonResponse);
            } elseif ($statusCode == 400) {
                Log::warning("DapoMaarifNU: NPSN {$npsn} tidak ditemukan (HTTP 400)");
                return $this->set(false, "NPSN {$npsn} tidak ditemukan di Dapo Maarif NU");
            } else {
                Log::error("DapoMaarifNU: HTTP {$statusCode} untuk NPSN {$npsn}", ['body' => $body]);
                return $this->set(false, "Gagal mengakses API Dapo Maarif NU untuk NPSN {$npsn}: HTTP {$statusCode}");
            }
        } catch (\Exception $err) {
            Log::error("DapoMaarifNU: Exception NPSN {$npsn} - {$err->getMessage()}", ['exception' => $err]);
            return $this->set(false, "Koneksi ke Dapo Maarif NU gagal saat mengecek NPSN {$npsn}");

        }
    }

}
