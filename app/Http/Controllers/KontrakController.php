<?php

namespace App\Http\Controllers;

use App\Models\Kontrak;
use App\Models\Pengadaan;
use App\Models\Prk;
use App\Models\Skki;
use App\Models\KontrakJasa;
use App\Models\KontrakMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Http\Helpers\ResponseHelper;
use App\Http\Helpers\AntiNull;

class KontrakController extends Controller
{
    protected $response, $antinull;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->response = new ResponseHelper;
        $this->antinull = new AntiNull;
    }

    public function _saveJasasAndMaterial($kontrak_id, $pengadaan_id) {
        $pengadaan = Pengadaan::with(['jasas', 'materials'])->find($pengadaan_id);
        $jasas = [];
        $materials = [];
        foreach ($pengadaan->jasas as $jasa) {
            $jasa = [
                'nama_jasa' => $jasa->nama_jasa,
                'harga' => $jasa->harga,
                'kontrak_id' => $kontrak_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            array_push($jasas, $jasa);
        }
        foreach ($pengadaan->materials as $material) {
            $material = [
                'kode_normalisasi' => $material->kode_normalisasi,
                'nama_material' => $material->nama_material,
                'harga' => $material->harga,
                'jumlah' => $material->jumlah,
                'satuan' => $material->satuan,
                'kontrak_id' => $kontrak_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            array_push($materials, $material);
        }
        KontrakJasa::insert($jasas);
        KontrakMaterial::insert($materials);
    }

    public function index(Request $request) {
        $kontraks = Kontrak::with(['jasas', 'materials'])->get();
        return $this->response->success($kontraks);
    }

    public function create(Request $request) {
        if(!$request->pengadaan_id || $request->pengadaan_id == 'null') {
            return $this->response->bad_request();
        }
        
        $data = $this->antinull->request($request->all());

        $pengadaan = Pengadaan::find($data['pengadaan_id']);
        $pengadaan->update([
            'status' => 'terkontrak'
        ]);

        $kontrak = Kontrak::create($data);

        if(!$kontrak) {
            return $this->response->bad_request();
        }

        $this->_saveJasasAndMaterial($kontrak->id, $data['pengadaan_id']);

        return $this->response->created($kontrak);
    }

    public function show($kontrak_id) {
        $kontrak = Kontrak::with(['jasas', 'materials'])->find($kontrak_id);

        if(!$kontrak) {
            return $this->response->not_found();
        }
        
        return $this->response->success($kontrak);
    }

    public function update($kontrak_id, Request $request) {
        $kontrak = Kontrak::find($kontrak_id);

        $data = $this->antinull->request($request->all());

        if(!$kontrak) {
            return $this->response->not_found();
        }

        $update = tap($kontrak)->update($data);

        if($update) {
            return $this->response->created($update);
        }

        return $this->response->bad_request();
    }

    // public function delete($pengadaan_id) {
    //     $pengadaan = Pengadaan::find($pengadaan_id);

    //     if(!$pengadaan) {
    //         return $this->response->not_found();
    //     }

    //     $delete = $pengadaan->delete();

    //     if($delete) {
    //         $jasas = PengadaanJasa::where('pengadaan_id', $pengadaan_id)->get();
    //         foreach ($jasas as $jasa) {
    //             $jasa->delete();
    //         }
            
    //         $materials = PengadaanMaterial::where('pengadaan_id', $pengadaan_id)->get();
    //         foreach ($materials as $material) {
    //             $material->delete();
    //         }
    //         return $this->response->success();
    //     }

    //     return $this->response->bad_request();
    // }
}
