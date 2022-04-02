<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use App\Http\Helpers\ResponseHelper;

class MaterialController extends Controller
{
    protected $response;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->response = new ResponseHelper;
    }

    public function index(Request $request) {
        $materials = Material::get();
        return $this->response->success($materials);
    }

    public function create(Request $request) {
        $material = Material::create($request->all());

        if(!$material) {
            $this->response->bad_request();
        }

        return $this->response->created($material);
    }

    // public function show($prk_id) {
    //     $prk = Prk::find($prk_id);

    //     if(!$prk) {
    //         return $this->response->not_found();
    //     }
        
    //     return $this->response->success($prk);
    // }

    public function update($material_id, Request $request) {
        $material = Material::find($material_id);
        
        if(!$material) {
            return $this->response->not_found();
        }

        $update = tap($material)->update($request->all());

        if($update) {
            return $this->response->created($update);
        }

        return $this->response->bad_request();
    }

    public function delete($material_id) {
        $material = Material::find($material_id);

        if(!$material) {
            return $this->response->not_found();
        }

        $delete = $material->delete();

        if($delete) {
            return $this->response->success();
        }

        return $this->response->bad_request();
    }
}
