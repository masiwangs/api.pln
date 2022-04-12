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

class KontrakController extends Controller
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
        $pengadaans = Pengadaan::with(['jasas', 'materials'])->get();
        return $this->response->success($pengadaans);
    }

    public function create(Request $request) {
        if(!$request->pengadaan_id && $request->pengadaan_id == 'null') {
            return $this->response->bad_request();
        }
        
        $data = [];
        if($request->nomor_kontrak && $request->nomor_kontrak !== 'null') {
            $data['nomor_kontrak'] = $request->nomor_kontrak;
        }
        if($request->tanggal_kontrak && $request->tanggal_kontrak !== 'null') {
            $data['tanggal_kontrak'] = $request->tanggal_kontrak;
        }
        if($request->tanggal_awal && $request->tanggal_awal !== 'null') {
            $data['tanggal_awal'] = $request->tanggal_awal;
        }
        if($request->tanggal_akhir && $request->tanggal_akhir !== 'null') {
            $data['tanggal_akhir'] = $request->tanggal_akhir;
        }
        if($request->pelaksana && $request->pelaksana !== 'null') {
            $data['pelaksana'] = $request->pelaksana;
        }
        if($request->direksi_pekerjaan && $request->direksi_pekerjaan !== 'null') {
            $data['direksi_pekerjaan'] = $request->direksi_pekerjaan;
        }

        $pengadaan = Pengadaan::create($data);

        if(!$pengadaan) {
            return $this->response->bad_request();
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
