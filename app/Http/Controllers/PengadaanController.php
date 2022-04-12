<?php

namespace App\Http\Controllers;

use App\Models\Pengadaan;
use App\Models\Prk;
use App\Models\Skki;
use App\Models\PengadaanJasa;
use App\Models\PengadaanMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Helpers\ResponseHelper;

class PengadaanController extends Controller
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

    protected function _saveJasasAndMaterialBasedPrkSkki($pengadaan_id, $prk_skkis) {
        if($pengadaan_id) {
            // delete jasa & material
            $pengadaan_jasas_existing = PengadaanJasa::where('pengadaan_id', $pengadaan_id)->get();
            foreach ($pengadaan_jasas_existing as $jasa_existing) {
                $jasa_existing->delete();
            }

            $pengadaan_materials_existing = PengadaanMaterial::where('pengadaan_id', $pengadaan_id)->get();
            foreach ($pengadaan_materials_existing as $material_existing) {
                $material_existing->delete();
            }
        }

        $nomor_prk_skkis = json_decode($prk_skkis);
        $nomor_wbs_jasas = [];
        $nomor_wbs_materials = [];
        if(count($nomor_prk_skkis)) {
            foreach ($nomor_prk_skkis as $nomor_prk_skki) {
                $skki = Skki::with(['jasas', 'materials'])->where('nomor_prk_skki', $nomor_prk_skki)->first();

                array_push($nomor_wbs_jasas, $skki->nomor_wbs_jasa);
                array_push($nomor_wbs_materials, $skki->nomor_wbs_material);

                $pengadaan_jasas = [];
                foreach ($skki->jasas as $jasa) {
                    array_push($pengadaan_jasas, [
                        'nama_jasa' => $jasa->nama_jasa,
                        'harga' => $jasa->harga,
                        'pengadaan_id' => $pengadaan_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                $pengadaan_materials = [];
                foreach ($skki->materials as $material) {
                    array_push($pengadaan_materials, [
                        'kode_normalisasi' => $material->kode_normalisasi,
                        'nama_material' => $material->nama_material,
                        'jumlah' => $material->jumlah,
                        'harga' => $material->harga,
                        'satuan' => $material->satuan,
                        'pengadaan_id' => $pengadaan_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                $pengadaan_jasa = PengadaanJasa::insert($pengadaan_jasas);
                $pengadaan_materials = PengadaanMaterial::insert($pengadaan_materials);
            }
            $pengadaan = Pengadaan::find($pengadaan_id);
            $pengadaan->update([
                'nomor_wbs_jasas' => json_encode($nomor_wbs_jasas),
                'nomor_wbs_materials' => json_encode($nomor_wbs_materials),
            ]);
        }

        return true;
    }

    protected function _saveJasasBasedWbsJasa($pengadaan_id, $nomor_wbs_jasas) {
        if($pengadaan_id) {
            // delete jasa & material
            $pengadaan_jasas_existing = PengadaanJasa::where('pengadaan_id', $pengadaan_id)->get();
            foreach ($pengadaan_jasas_existing as $jasa_existing) {
                $jasa_existing->delete();
            }
        }

        $nomor_wbs_jasas = json_decode($nomor_wbs_jasas);

        if(count($nomor_wbs_jasas)) {
            foreach ($nomor_wbs_jasas as $nomor_wbs_jasa) {
                $skki = Skki::with('jasas')->where('nomor_wbs_jasa', $nomor_wbs_jasa)->first();

                $pengadaan_jasas = [];
                foreach ($skki->jasas as $jasa) {
                    array_push($pengadaan_jasas, [
                        'nama_jasa' => $jasa->nama_jasa,
                        'harga' => $jasa->harga,
                        'pengadaan_id' => $pengadaan_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                $pengadaan_jasa = PengadaanJasa::insert($pengadaan_jasas);
            }
        }

        return true;
    }

    protected function _saveMaterialsBasedWbsMaterial($pengadaan_id, $nomor_wbs_materials) {
        if($pengadaan_id) {
            // delete  material
            $pengadaan_materials_existing = PengadaanMaterial::where('pengadaan_id', $pengadaan_id)->get();
            foreach ($pengadaan_materials_existing as $material_existing) {
                $material_existing->delete();
            }
        }

        $nomor_wbs_materials = json_decode($nomor_wbs_materials);

        if(count($nomor_wbs_materials)) {
            foreach ($nomor_wbs_materials as $nomor_wbs_material) {
                $skki = Skki::with('materials')->where('nomor_wbs_material', $nomor_wbs_material)->first();

                $pengadaan_materials = [];
                foreach ($skki->materials as $material) {
                    array_push($pengadaan_materials, [
                        'kode_normalisasi' => $material->kode_normalisasi,
                        'nama_material' => $material->nama_material,
                        'jumlah' => $material->jumlah,
                        'harga' => $material->harga,
                        'satuan' => $material->satuan,
                        'pengadaan_id' => $pengadaan_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                $pengadaan_materials = PengadaanMaterial::insert($pengadaan_materials);
            }
        }

        return true;
    }

    public function index(Request $request) {
        $pengadaans = Pengadaan::with(['jasas', 'materials'])->get();
        return $this->response->success($pengadaans);
    }

    public function create(Request $request) {
        $data = [];
        if($request->nomor_prk_skkis && $request->nomor_prk_skkis !== 'null') {
            $data['nomor_prk_skkis'] = $request->nomor_prk_skkis;
        }
        if($request->nodin && $request->nodin !== 'null') {
            $data['nodin'] = $request->nodin;
        }
        if($request->tanggal_nodin && $request->tanggal_nodin !== 'null') {
            $data['tanggal_nodin'] = $request->tanggal_nodin;
        }
        if($request->nomor_pr && $request->nomor_pr !== 'null') {
            $data['nomor_pr'] = $request->nomor_pr;
        }
        if($request->nama_project && $request->nama_project !== 'null') {
            $data['nama_project'] = $request->nama_project;
        }
        if($request->tanggal_awal && $request->tanggal_awal !== 'null') {
            $data['tanggal_awal'] = $request->tanggal_awal;
        }
        if($request->tanggal_akhir && $request->tanggal_akhir !== 'null') {
            $data['tanggal_akhir'] = $request->tanggal_akhir;
        }
        if($request->status && $request->status !== 'null') {
            $data['status'] = $request->status;
        }
        if($request->nomor_wbs_jasas && $request->nomor_wbs_jasas !== 'null') {
            $data['nomor_wbs_jasas'] = $request->nomor_wbs_jasas;
        }
        if($request->nomor_wbs_materials && $request->nomor_wbs_materials !== 'null') {
            $data['nomor_wbs_materials'] = $request->nomor_wbs_materials;
        }

        $pengadaan = Pengadaan::create($data);

        if(!$pengadaan) {
            $this->response->bad_request();
        }

        // save jasa & material
        $this->_saveJasasAndMaterial($pengadaan->id, $request->nomor_prk_skkis);
        
        return $this->response->created($pengadaan);
    }

    public function show($skki_id) {
        $skki = Skki::with(['jasas'])->find($skki_id);

        if(!$skki) {
            return $this->response->not_found();
        }
        
        return $this->response->success($skki);
    }

    public function update($pengadaan_id, Request $request) {
        $pengadaan = Pengadaan::find($pengadaan_id);

        $data = [];
        if($request->nomor_prk_skkis && $request->nomor_prk_skkis !== 'null') {
            $data['nomor_prk_skkis'] = $request->nomor_prk_skkis;
        }
        if($request->nodin && $request->nodin !== 'null') {
            $data['nodin'] = $request->nodin;
        }
        if($request->tanggal_nodin && $request->tanggal_nodin !== 'null') {
            $data['tanggal_nodin'] = $request->tanggal_nodin;
        }
        if($request->nomor_pr && $request->nomor_pr !== 'null') {
            $data['nomor_pr'] = $request->nomor_pr;
        }
        if($request->nama_project && $request->nama_project !== 'null') {
            $data['nama_project'] = $request->nama_project;
        }
        if($request->status && $request->status !== 'null') {
            $data['status'] = $request->status;
        }
        if($request->nomor_wbs_jasas && $request->nomor_wbs_jasas !== 'null') {
            $data['nomor_wbs_jasas'] = $request->nomor_wbs_jasas;
        }
        if($request->nomor_wbs_materials && $request->nomor_wbs_materials !== 'null') {
            $data['nomor_wbs_materials'] = $request->nomor_wbs_materials;
        }

        if(!$pengadaan) {
            return $this->response->not_found();
        }

        $old_pengadaan = $pengadaan->toArray();
        $update = tap($pengadaan)->update($data);

        if($update) {
            // save jasa & material karena perubahan prk_skkis
            if($old_pengadaan['nomor_prk_skkis'] !== $data['nomor_prk_skkis']) {
                $this->_saveJasasAndMaterialBasedPrkSkki($pengadaan_id, $update->nomor_prk_skkis);
            }

            // save jasa karena perubahan wbs
            if($old_pengadaan['nomor_wbs_jasas'] !== $data['nomor_wbs_jasas']) {
                $this->_saveJasasBasedWbsJasa($pengadaan_id, $update->nomor_wbs_jasas);
            }

            // save  material karena perubahan wbs
            if($old_pengadaan['nomor_wbs_materials'] !== $data['nomor_wbs_materials']) {
                $this->_saveMaterialsBasedWbsMaterial($pengadaan_id, $update->nomor_wbs_materials);
            }

            return $this->response->created($update);
        }


        return $this->response->bad_request();
    }

    public function delete($pengadaan_id) {
        $pengadaan = Pengadaan::find($pengadaan_id);

        if(!$pengadaan) {
            return $this->response->not_found();
        }

        $delete = $pengadaan->delete();

        if($delete) {
            $jasas = PengadaanJasa::where('pengadaan_id', $pengadaan_id)->get();
            foreach ($jasas as $jasa) {
                $jasa->delete();
            }
            
            $materials = PengadaanMaterial::where('pengadaan_id', $pengadaan_id)->get();
            foreach ($materials as $material) {
                $material->delete();
            }
            return $this->response->success();
        }

        return $this->response->bad_request();
    }
}
