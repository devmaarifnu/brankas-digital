<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class ReferensiKemdikbud {

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
            $response = Http::withOptions(['verify' => false])->get(config('services.referensi_kemdikbud.url') . '/' . $npsn);

            if ($response->successful() || $response->serverError()) {
                $html = $response->body();

                // Create a new instance of Symfony's DomCrawler
                $crawler = new Crawler($html);

                // Extract the data you want using DOM traversal methods
                $tableRows = $crawler->filter('table tr');
                $dataList = [];

                if ($tableRows->count() > 1) {
                    $tableRows->each(function (Crawler $row) use (&$dataList) {
                        // Extract the data from each table row
                        $rowData = $row->filter('td');
                        //Verify colomn count not single
                        if ($rowData->count() > 1) {
                            $keystring = trim(strtolower($rowData->eq(1)->text()));
                            $keystring = preg_replace('/[^a-zA-Z\s]+/', "", $keystring);
                            //Replace whitespace to underscore
                            $arrKey = str_replace(" ", "_", $keystring);
                            //Verify key in not whitespace
                            if ($arrKey) {
                                $dataList[$arrKey] = trim($rowData->eq(3)->text());
                            }
                        }
                    });
                }

                if (count($dataList) > 0) return $this->set(true, $dataList);

                Log::warning("ReferensiKemdikbud: NPSN {$npsn} tidak ditemukan (dataList kosong)");
                return $this->set(false, "NPSN {$npsn} tidak ditemukan di Referensi Kemdikbud");

            } else {
                Log::error("ReferensiKemdikbud: HTTP {$response->status()} untuk NPSN {$npsn}");
                return $this->set(false, "Gagal mengakses halaman Referensi Kemdikbud untuk NPSN {$npsn}: HTTP {$response->status()}");
            }
        } catch (\Exception $err) {
            Log::error("ReferensiKemdikbud: Exception NPSN {$npsn} - {$err->getMessage()}", ['exception' => $err]);
            return $this->set(false, "Koneksi ke Referensi Kemdikbud gagal saat mengecek NPSN {$npsn}");

        }
    }

}
