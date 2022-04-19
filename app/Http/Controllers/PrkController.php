<?php

namespace App\Http\Controllers;

use App\Models\Prk;
use App\Models\PrkJasa;
use App\Models\PrkMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Helpers\ResponseHelper;

class PrkController extends Controller
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

    //
    public function index(Request $request) {
        $prks = Prk::with(['jasas', 'materials'])->get();
        return $this->response->success($prks);
    }

    public function create(Request $request) {
        $prk = Prk::create($request->only(
            'nama_project', 
            'nomor_prk', 
            'lot_number',
            'prioritas',
            'project_id', 
            'basket'
        ));

        if(!$prk) {
            $this->response->bad_request();
        }

        return $this->response->created($prk);
    }

    public function show($prk_id) {
        $prk = Prk::with(['jasas', 'materials'])->find($prk_id);

        if(!$prk) {
            return $this->response->not_found();
        }
        
        return $this->response->success($prk);
    }

    public function update($prk_id, Request $request) {
        $prk = Prk::find($prk_id);
        
        if(!$prk) {
            return $this->response->not_found();
        }

        $update = tap($prk)->update($request->all());

        if($update) {
            return $this->response->created($update);
        }

        return $this->response->bad_request();
    }

    public function delete($prk_id) {
        $prk = Prk::find($prk_id);

        if(!$prk) {
            return $this->response->not_found();
        }

        $delete = $prk->delete();

        if($delete) {
            $jasas = PrkJasa::where('prk_id', $prk_id)->get();
            foreach ($jasas as $jasa) {
                $jasa->delete();
            }
            
            $materials = PrkMaterial::where('prk_id', $prk_id)->get();
            foreach ($materials as $material) {
                $material->delete();
            }
            return $this->response->success();
        }

        return $this->response->bad_request();
    }
}
