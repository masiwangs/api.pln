<?php

namespace App\Http\Controllers;

use App\Models\Prk;
use App\Models\Skki;
use App\Models\SkkiJasa;
use App\Models\SkkiMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Helpers\AntiNullHelper;
use App\Http\Helpers\ResponseHelper;

class SkkiController extends Controller
{
    protected $antinull, $response;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->antinull = new AntiNullHelper;
        $this->response = new ResponseHelper;
    }

    protected function _saveJasasAndMaterial($skki_id, $prks) {
        if($skki_id) {
            // delete jasa & material
            $skki_jasas_existing = SkkiJasa::where('skki_id', $skki_id)->get();
            foreach ($skki_jasas_existing as $jasa_existing) {
                $jasa_existing->delete();
            }

            $skki_materials_existing = SkkiMaterial::where('skki_id', $skki_id)->get();
            foreach ($skki_materials_existing as $material_existing) {
                $material_existing->delete();
            }
        }

        $nomor_prks = json_decode($prks);
        if(count($nomor_prks)) {
            foreach ($nomor_prks as $nomor_prk) {
                $prk = Prk::with(['jasas', 'materials'])->where('nomor_prk', $nomor_prk)->first();
                $skki_jasas = [];
                foreach ($prk->jasas as $jasa) {
                    array_push($skki_jasas, [
                        'nama_jasa' => $jasa->nama_jasa,
                        'harga' => $jasa->harga,
                        'skki_id' => $skki_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                $skki_materials = [];
                foreach ($prk->materials as $material) {
                    array_push($skki_materials, [
                        'kode_normalisasi' => $material->kode_normalisasi,
                        'nama_material' => $material->nama_material,
                        'jumlah' => $material->jumlah,
                        'harga' => $material->harga,
                        'satuan' => $material->satuan,
                        'skki_id' => $skki_id,
                        'prk_id' => $prk->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                $skki_jasa = SkkiJasa::insert($skki_jasas);
                $skki_materials = SkkiMaterial::insert($skki_materials);
            }
        }

        return true;
    }

    public function index(Request $request) {
        $skkis = Skki::with(['jasas', 'materials'])->get();
        return $this->response->success($skkis);
    }

    public function create(Request $request) {
        $data = $this->antinull->request($request->all());

        $skki = Skki::create($data);

        if(!$skki) {
            $this->response->bad_request();
        }

        // save jasa & material
        if($request->prks) {
            $this->_saveJasasAndMaterial($skki->id, $request->prks);
        }
        
        return $this->response->created($skki);
    }

    public function show($skki_id) {
        $skki = Skki::with(['jasas', 'materials'])->find($skki_id);

        if(!$skki) {
            return $this->response->not_found();
        }
        
        return $this->response->success($skki);
    }

    public function update($skki_id, Request $request) {
        $skki = Skki::find($skki_id);
        
        if(!$skki) {
            return $this->response->not_found();
        }

        $data = $this->antinull->request($request->all());

        $update = tap($skki)->update($data);

        if($update) {
            // save jasa & material
            $this->_saveJasasAndMaterial($skki_id, $update->prks);
            return $this->response->created($update);
        }


        return $this->response->bad_request();
    }

    public function delete($skki_id) {
        $skki = Skki::find($skki_id);

        if(!$skki) {
            return $this->response->not_found();
        }

        $delete = $skki->delete();

        if($delete) {
            $jasas = SkkiJasa::where('skki_id', $skki_id)->get();
            foreach ($jasas as $jasa) {
                $jasa->delete();
            }
            
            $materials = SkkiMaterial::where('skki_id', $skki_id)->get();
            foreach ($materials as $material) {
                $material->delete();
            }
            return $this->response->success();
        }

        return $this->response->bad_request();
    }
}
