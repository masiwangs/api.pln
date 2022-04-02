<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Models\PrkJasa;
use Illuminate\Http\Request;

class PrkJasaController extends Controller {

    protected $response;

    public function __construct() {
        $this->response = new ResponseHelper;
    }

    public function index($prk_id) {
        $jasas = PrkJasa::where('prk_id', $prk_id)->get();
        return $this->response->success($jasas);
    }

    public function create($prk_id, Request $request) {
        $data = [
            'nama_jasa' => $request->nama_jasa,
            'harga' => $request->harga,
            'prk_id' => $prk_id
        ];

        $jasa = PrkJasa::create($data);

        if(!$jasa) {
            return $this->response->bad_request();
        }

        return $this->response->success($jasa);
    }

    public function delete($prk_id, $jasa_id, Request $request) {
        $jasa = PrkJasa::find($jasa_id);
        if($jasa->delete()) {
            return $this->response->success();
        }
        return $this->response->not_found();
    }
}