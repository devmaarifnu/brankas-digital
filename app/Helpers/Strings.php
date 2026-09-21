<?php

namespace App\Helpers;

class Strings
{

    public static function removeFirstWord($word, $count = 0)
    {

        $wordclear = explode(" ", preg_replace('/[^a-zA-Z\s]+/', "", trim(strtolower($word))));
        for ($i = 1; $i < $count; $i++) {
            array_shift($wordclear);
        }
        if (($key = array_search("kab", $wordclear)) !== false) {
            $wordclear[$key] = "kabupaten";
        }
        if (in_array("prov", $wordclear)) {
            array_shift($wordclear);
        }
        $newword = implode(" ", $wordclear);

        return $newword;
    }

    public static function getFileName($path)
    {
        $fullName = basename($path);
        $parts = explode('~', $fullName);
        return end($parts);
    }

    public static function replaceMapFile($mapfile)
    {
        $mapfile = str_replace("_", " ", $mapfile);
        if (str_contains($mapfile, "pc")) {
            $mapfile = "pengurus cabang";
        } elseif (str_contains($mapfile, "pw")) {
            $mapfile = "pengurus wilayah";
        }
        return $mapfile;
    }
}
