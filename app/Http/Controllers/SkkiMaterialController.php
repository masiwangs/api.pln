<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Models\SkkiMaterial;
use Illuminate\Http\Request;

class SkkiMaterialController extends Controller {
    
    protected $response;

    public function __construct() {
        $this->response = new ResponseHelper;
    }

    public function index($skki_id) {
        $materials = SkkiMaterial::where('skki_id', $skki_id)->get();
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