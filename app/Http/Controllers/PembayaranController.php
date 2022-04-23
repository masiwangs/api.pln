<?php

namespace App\Http\Controllers;

use App\Http\Helpers\{ AntiNullHelper, ResponseHelper };
use App\Models\{ 
    Kontrak, 
    KontrakJasa,
    KontrakMaterial, 
    PelaksanaanJasaTransaction,
    PelaksanaanMaterialTransaction,
    Pembayaran
};
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    protected $antinull, $response;

    public function __construct()
    {
        $this->antinull = new AntinullHelper;
        $this->response = new ResponseHelper;
    }

    public function index(){
        $kontraks = Kontrak::with(['jasa_transactions', 'material_transactions', 'pembayarans'])->get();
        return $this->response->success($kontraks);
    }

    public function show($kontrak_id) {
        $kontrak = Kontrak::with([
            'jasa_transactions',
            'material_transactions',
            'pembayarans'
        ])->find($kontrak_id);

        if(!$kontrak) {
            return $this->response->not_found();
        }

        return $this->response->success($kontrak);
    }

    public function create($kontrak_id, Request $request) {
        $data = $this->antinull->request($request->all());

        $pembayaran = Pembayaran::create($data);

        return $this->response->success($pembayaran);
    }

    public function deleteByKontrak($kontrak_id) {
        $pembayarans = Pembayaran::where('kontrak_id', $kontrak_id)->get();

        foreach ($pembayarans as $pembayaran) {
            $pembayaran->delete();
        }

        if(!$pembayaran) {
            $this->response->not_found();
        }

        return $this->response->success();
    }
}
