<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Models\PengadaanMaterial;
use Illuminate\Http\Request;

class PengadaanMaterialController extends Controller {
    
    protected $response;

    public function __construct() {
        $this->response = new ResponseHelper;
    }

    public function index($pengadaan_id) {
        $materials = PengadaanMaterial::where('pengadaan_id', $pengadaan_id)->get();
        return $this->response->success($materials);
    }

    public function delete($skki_id, $material_id) {
        $skki_material = SkkiMaterial::find($material_id);
        
        if($skki_material->delete()) {
            return $this->response->success();
        }

        return $this->response->not_found();
    }
}