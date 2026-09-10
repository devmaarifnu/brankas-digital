<?php
namespace App\Helpers;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Logo\Logo;
use Illuminate\Support\Str;
use Mockery\Exception;

class GenerateQr {

    public static function make(string $content, $imagePath) {
        try {
            // Generate QR code image using endroid/qr-code library
            $writer = new PngWriter();
            // Create QR code
            $qrCode = QrCode::create($content)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->setSize(130)
                ->setMargin(10)
                ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));

            // Create generic logo
//            $logo = Logo::create(public_path('assets/images/logos/rounded-logo.png'))
//                ->setResizeToWidth(60)
//                ->setPunchoutBackground(true);

//            $result = $writer->write($qrCode, $logo);
            $result = $writer->write($qrCode);
            $result->saveToFile($imagePath);

            return true;
        } catch (Exception $e) {
            return false;
        }

    }

    public static function encodeQr() {
        return route('verify', str_replace("-", "", Str::uuid()));
    }


}
