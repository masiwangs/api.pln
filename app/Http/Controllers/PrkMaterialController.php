<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Models\PrkMaterial;
use Illuminate\Http\Request;

class PrkMaterialController extends Controller {

    protected $response;

    public function __construct() {
        $this->response = new ResponseHelper;
    }

    public function index($prk_id) {
        $materials = PrkMaterial::where('prk_id', $prk_id)->get();
        return $this->response->success($materials);
    }

    public function create($prk_id, Request $request) {
        $data = [
            'kode_normalisasi' => $request->kode_normalisasi,
            'nama_material' => $request->nama_material,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
            'satuan' => $request->satuan,
            'prk_id' => $prk_id
        ];

        $material = PrkMaterial::create($data);

        if(!$material) {
            return $this->response->bad_request();
        }

        return $this->response->success($material);
    }

    public function delete($prk_id, $material_id, Request $request) {
        $material = PrkMaterial::find($material_id);
        if($material->delete()) {
            return $this->response->success();
        }
        return $this->response->not_found();
    }
}