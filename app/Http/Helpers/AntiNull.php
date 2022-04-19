<?php

namespace App\Http\Helpers;

class AntiNull
{
    public function request($data) {
        $clean_data = [];
        foreach ($data as $k => $d) {
            if($d !== null && $d !== 'null') {
                $clean_data = array_merge($clean_data, [$k => $d]);
            }
        }
        return $clean_data;
    }
}