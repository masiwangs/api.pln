<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Models\Pengadaan;
use App\Models\Skki;
use Illuminate\Http\Request;

class PengadaanPrkSkkiController extends Controller {
    
    protected $response;

    public function __construct() {
        $this->response = new ResponseHelper;
    }

    public function index($pengadaan_id) {
        $pengadaan = Pengadaan::find($pengadaan_id);
        $nomor_prk_skkis = json_decode($pengadaan->nomor_prk_skkis);
        $skkis = Skki::whereIn('nomor_prk_skki', $nomor_prk_skkis)->get();
        return $this->response->success($skkis);
    }
}