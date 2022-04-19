<?php

namespace App\Http\Controllers;

use App\Models\Kontrak;
use App\Models\Pengadaan;
use App\Models\Prk;
use App\Models\Skki;
use App\Models\KontrakJasa;
use App\Models\KontrakMaterial;
use App\Models\PengadaanJasa;
use App\Models\PengadaanMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Helpers\AntiNull;
use App\Http\Helpers\ResponseHelper;

class PengadaanController extends Controller
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
            $update_pengadaan = [
                'nomor_wbs_jasas' => json_encode($nomor_wbs_jasas),
                'nomor_wbs_materials' => json_encode($nomor_wbs_materials),
            ];
            $pengadaan->update($update_pengadaan);

            return $update_pengadaan;
        }

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
        $data = $this->antinull->request($request->all());

        $pengadaan = Pengadaan::create($data);

        if(!$pengadaan) {
            $this->response->bad_request();
        }

        // save jasa & material
        if(isset($data['nomor_prk_skkis'])) {
            $updated_pengadaan = $this->_saveJasasAndMaterialBasedPrkSkki($pengadaan->id, $request->nomor_prk_skkis);

            if($updated_pengadaan) {
                $pengadaan['nomor_wbs_jasas'] = $updated_pengadaan['nomor_wbs_jasas'];
                $pengadaan['nomor_wbs_materials'] = $updated_pengadaan['nomor_wbs_materials'];
            }
        }
        
        return $this->response->created($pengadaan);
    }

    public function show($pengadaan_id) {
        $pengadaan = Pengadaan::with(['jasas', 'materials'])->find($pengadaan_id);

        if(!$pengadaan) {
            return $this->response->not_found();
        }
        
        return $this->response->success($pengadaan);
    }

    public function update($pengadaan_id, Request $request) {
        $start = microtime(true);
        $pengadaan = Pengadaan::find($pengadaan_id);

        $data = $this->antinull->request($request->all());

        if(!$pengadaan) {
            return $this->response->not_found();
        }

        $old_pengadaan = $pengadaan->toArray();
        $update = tap($pengadaan)->update($data);

        if($update) {
            // save jasa & material karena perubahan prk_skkis
            if($old_pengadaan['nomor_prk_skkis'] !== $data['nomor_prk_skkis']) {
                $updated_pengadaan = $this->_saveJasasAndMaterialBasedPrkSkki($pengadaan_id, $update->nomor_prk_skkis);
                if($updated_pengadaan) {
                    $pengadaan['nomor_wbs_jasas'] = $updated_pengadaan['nomor_wbs_jasas'];
                    $pengadaan['nomor_wbs_materials'] = $updated_pengadaan['nomor_wbs_materials'];
                }
            }

            // save jasa karena perubahan wbs
            if($old_pengadaan['nomor_wbs_jasas'] !== $data['nomor_wbs_jasas']) {
                $this->_saveJasasBasedWbsJasa($pengadaan_id, $update->nomor_wbs_jasas);
            }

            // save  material karena perubahan wbs
            if($old_pengadaan['nomor_wbs_materials'] !== $data['nomor_wbs_materials']) {
                $this->_saveMaterialsBasedWbsMaterial($pengadaan_id, $update->nomor_wbs_materials);
            }

            // if update status = proses. delete kontrak
            if(isset($data['status'])) {
                if($data['status'] == 'proses') {
                    $kontraks = Kontrak::where('pengadaan_id', $pengadaan->id)->get();
                    foreach ($kontraks as $kontrak) {
                        $kontrak_jasas = KontrakJasa::where('kontrak_id', $kontrak->id)->get();
                        foreach ($kontrak_jasas as $kontrak_jasa) {
                            $kontrak_jasa->delete();
                        }
                        $kontrak_materials = KontrakMaterial::where('kontrak_id', $kontrak->id)->get();
                        foreach ($kontrak_materials as $kontrak_material) {
                            $kontrak_material->delete();
                        }
                        $kontrak->delete();
                    }
                }
            }

            $wbs_jasas_option = [];
            $wbs_materials_option = [];
            foreach (json_decode($data['nomor_prk_skkis']) as $prk_skki) {
                $prk_skkis_result = Skki::where('nomor_prk_skki', $prk_skki)->get();
                foreach ($prk_skkis_result as $prk_skki_result) {
                    array_push($wbs_jasas_option, $prk_skki_result->nomor_wbs_jasa);
                    array_push($wbs_materials_option, $prk_skki_result->nomor_wbs_material);
                }
            }

            $pengadaan_updated = Pengadaan::with(['materials', 'jasas'])->find($pengadaan_id)->toArray();
            $pengadaan_updated['wbs_jasas_option'] = $wbs_jasas_option;
            $pengadaan_updated['wbs_materials_option'] = $wbs_materials_option;
            $pengadaan_updated['timing'] = microtime(true) - $start;
            return $this->response->created($pengadaan_updated);
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
