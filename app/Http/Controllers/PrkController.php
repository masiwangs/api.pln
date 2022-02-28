<?php

namespace App\Http\Controllers;

use App\Models\Prk;
use App\Models\PrkJasa;
use App\Models\PrkMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Helpers\Response;

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
        $this->response = new Response;
    }

    //
    public function index(Request $request) {
        $prks = Prk::with('materials', 'jasas');
        if($request->has('basket')) {
            $prks = $prks->where('basket', $request->basket);
        }
        $prks = $prks->get();

        $data = [];
        foreach ($prks as $prk) {
            $temp = $prk;

            $temp['sum_materials'] = 0;
            foreach ($prk->materials as $material) {
                $temp['sum_materials'] += $material->jumlah * $material->harga;
            }

            $temp['sum_jasas'] = 0;
            foreach ($prk->jasas as $jasa) {
                $temp['sum_jasas'] += $jasa->harga;
            }

            array_push($data, $temp);
        }

        return $this->response->success($data);
    }

    public function create(Request $request) {
        $prk = Prk::create($request->only('nama_project', 'no_prk', 'project_id', 'basket', 'created_by'));

        if($prk) {
            if($request->has('materials')) {
                $materials = [];

                foreach ($request->materials as $material) {
                    array_push($materials, [
                        'kode_normalisasi' => $material['kode_normalisasi'],
                        'nama_material' => $material['nama_material'],
                        'jumlah' => $material['jumlah'],
                        'harga' => $material['harga'],
                        'prk_id' => $prk->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }

                $prk_materials = PrkMaterial::insert($materials);
                
                $prk['materials'] = $materials;
            }

            if($request->has('jasas')) {
                $jasas = [];

                foreach ($request->jasas as $jasa) {
                    array_push($jasas, [
                        'nama_jasa' => $jasa['nama_jasa'],
                        'harga' => $jasa['harga'],
                        'prk_id' => $prk->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                $prk_jasas = PrkJasa::insert($jasas);
                
                $prk['jasas'] = $jasas;
            }

            return $this->response->created($prk);
        }

        return $this->response->bad_request();
    }

    public function show($prk_id) {
        $prk = Prk::find($prk_id);

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

        $update = $prk->update($request->all());

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
            return $this->response->success();
        }

        return $this->response->bad_request();
    }
}
