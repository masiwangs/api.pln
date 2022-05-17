<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Models\{
    Prk,
    Skki,
    Pengadaan,
    Kontrak,
    Pembayaran
};
use Illuminate\Http\Request;

class StatsController extends Controller 
{
    protected $response;
    public function __construct()
    {
        $this->response = new ResponseHelper;
    }

    public function biaya(Request $request) {
        $prks = Prk::query();
        $skkis = Skki::query();
        $pengadaans = Pengadaan::query();
        $kontraks = Kontrak::query();
        $pembayarans = Pembayaran::query();

        if($request->basket) {
            if(in_array($request->basket, [1, 2, 3])) {
                $prks = $prks->where('basket', $request->basket);
                $skkis = $skkis->where('basket', $request->basket);
                $pengadaans = $pengadaans->where('basket', $request->basket);
                $kontraks = $kontraks->where('basket', $request->basket);
                $pembayarans = $pembayarans->where('basket', $request->basket);
            }
        }

        $prks = $prks->get('id');
        $skkis = $skkis->get('id');
        $pengadaans = $pengadaans->get('id');
        $kontraks = $kontraks->get('id');
        $pembayarans = $pembayarans->get();

        $nominal = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        foreach ($prks as $prk) {
            foreach ($prk->jasas as $prk_jasa) {
                $nominal[0] += $prk_jasa->harga;
            }
            foreach ($prk->materials as $prk_material) {
                $nominal[1] += $prk_material->harga * $prk_material->jumlah;
            }
        }

        foreach ($skkis as $skki) {
            foreach ($skki->jasas as $skki_jasa) {
                $nominal[2] += $skki_jasa->harga;
            }
            foreach ($skki->materials as $skki_material) {
                $nominal[3] += $skki_material->harga * $skki_material->jumlah;
            }
        }
        foreach ($pengadaans as $pengadaan) {
            foreach ($pengadaan->jasas as $pengadaan_jasa) {
                $nominal[4] += $pengadaan_jasa->harga;
            }
            foreach ($pengadaan->materials as $pengadaan_material) {
                $nominal[5] += $pengadaan_material->harga * $pengadaan_material->jumlah;
            }
        }
        foreach ($kontraks as $kontrak) {
            foreach ($kontrak->jasas as $kontrak_jasa) {
                $nominal[6] += $kontrak_jasa->harga;
            }
            foreach ($kontrak->materials as $kontrak_material) {
                $nominal[7] += $kontrak_material->harga * $kontrak_material->jumlah;
            }
            foreach ($kontrak->jasa_transactions as $kontrak_jasa) {
                $nominal[8] += $kontrak_jasa->harga;
            }
            foreach ($kontrak->material_transactions as $kontrak_material) {
                $nominal[9] += $kontrak_material->harga * $kontrak_material->jumlah;
            }
        }
        foreach ($pembayarans as $pembayaran) {
            $nominal[10] += $pembayaran->nominal;
        }

        return $this->response->success($nominal);
    }
}