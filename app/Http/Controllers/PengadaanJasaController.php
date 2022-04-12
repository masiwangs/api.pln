<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Models\PengadaanJasa;
use Illuminate\Http\Request;

class PengadaanJasaController extends Controller {
    
    protected $response;

    public function __construct() {
        $this->response = new ResponseHelper;
    }

    public function index($pengadaan_id) {
        $jasas = PengadaanJasa::where('pengadaan_id', $pengadaan_id)->get();
        return $this->response->success($jasas);
    }

    public function delete($skki_id, $jasa_id) {
        $skki_jasa = SkkiJasa::find($jasa_id);
        
        if($skki_jasa->delete()) {
            return $this->response->success();
        }

        return $this->response->not_found();
    }
}