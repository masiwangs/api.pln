<?php

namespace App\Http\Controllers;

use App\Http\Helpers\{ AntiNullHelper, ResponseHelper };
use App\Models\{ 
    Kontrak, 
    KontrakJasa,
    KontrakMaterial, 
    PelaksanaanJasaTransaction,
    PelaksanaanMaterialTransaction
};
use Illuminate\Http\Request;

class PelaksanaanController extends Controller
{
    protected $antinull, $response;

    public function __construct()
    {
        $this->antinull = new AntinullHelper;
        $this->response = new ResponseHelper;
    }

    public function index(){
        $kontraks = Kontrak::get();
        
        return $this->response->success($kontraks);
    }

    public function show($kontrak_id) {
        $kontrak = Kontrak::with([
            'jasas',
            'jasa_transactions',
            'materials',
            'material_transactions'
        ])->find($kontrak_id);

        if(!$kontrak) {
            return $this->response->not_found();
        }

        return $this->response->success($kontrak);
    }

    public function materialTransaction(Request $request) {
        $data = $this->antinull->request($request->all());
        $material_stock = KontrakMaterial::where('kode_normalisasi', $data['kode_normalisasi'])->get();

        if($data['transaction'] == 'out') {
            $stock = 0;
            foreach ($material_stock as $material) {
                $stock += $material->stock;
            }
    
            $stock_added = $data['jumlah'];
            if($stock < $stock_added) {
                return $this->response->bad_request();
            }
    
            $material_transaction = PelaksanaanMaterialTransaction::create($data);
    
            foreach ($material_stock as $material) {
                if($stock_added > 0) {
                    if($material->stock > 0) {
                        // jika stock lebih dari 0
                        $updated_stock = 0;
                        if($material->stock >= $stock_added) {
                            // jika stock lebih dari stock yang dibutuhkan
                            $updated_stock = $stock_added;
                            $material->update(['stock' => $material->stock - $stock_added]);
                        } else {
                            // jika stock kurang dari stock yang dibutuhkan
                            $updated_stock = $material->stock;
                            $material->update(['stock' => 0]);
                        }
        
                        $stock_added -= $updated_stock;
                    }
                }
            }
        } else {
            $jumlah = 0;
            $stock = 0;
            $material_jumlah = KontrakMaterial::where('kode_normalisasi', $data['kode_normalisasi'])->get();

            foreach ($material_jumlah as $material) {
                $jumlah += $material->jumlah;
                $stock += $material->stock;
            }

            $stock_returned = $data['jumlah'];
            if($data['jumlah'] > $jumlah - $stock) {
                return $this->response->bad_request();
            }

            $material_transaction = PelaksanaanMaterialTransaction::create($data);

            foreach ($material_jumlah as $material) {
                if($stock_returned > 0) {
                    $updated_stock = 0;
                    if($material->stock < $material->jumlah) {
                        $updated_stock = 0;
                        if($material->jumlah - $material->stock > $stock_returned) {
                            $updated_stock = $stock_returned;
                            $material->update(['stock' => $material->stock + $stock_returned]);
                        } else {
                            $updated_stock = $material->jumlah - $material->stock;
                            $material->update(['stock' => $material->jumlah]);
                        }
                        $stock_returned -= $updated_stock;
                    }
                }
            }

        }

        return $this->response->success($material_transaction);
    }

    public function jasaTransaction(Request $request) {
        $data = $this->antinull->request($request->all());

        $rab_jasa = KontrakJasa::where('kontrak_id', $data['kontrak_id'])->get();

        $total_jasa = 0;
        foreach ($rab_jasa as $rab) {
            $total_jasa += $rab->harga;
        }

        $transaksi_jasa = PelaksanaanJasaTransaction::where('kontrak_id', $data['kontrak_id'])->get();

        $realisasi_jasa = 0;
        foreach ($transaksi_jasa as $transaksi) {
            $realisasi_jasa += $transaksi->harga;
        }

        if($data['harga'] > $total_jasa - $realisasi_jasa) {
            return $this->response->bad_request('Saldo tidak mencukupi');
        }

        $transaksi_jasa_baru = PelaksanaanJasaTransaction::create($data);

        return $this->response->success($transaksi_jasa_baru);
    }

    public function deleteJasaTransaction($jasa_id) {
        $jasa = PelaksanaanJasaTransaction::find($jasa_id);

        if(!$jasa) {
            return $this->response->not_found();
        }

        $jasa->delete();

        return $this->response->success();
    }
}
