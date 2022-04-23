<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'v1'], function() use($router) {
    $router->get('/projects', 'ProjectController@index');
    $router->post('/projects', 'ProjectController@create');
    $router->post('/projects/{project_id}', 'ProjectController@update');
    $router->delete('/projects/{project_id}', 'ProjectController@delete');

    // prk
    $router->get('/prks', 'PrkController@index');
    $router->post('/prks', 'PrkController@create');
    $router->get('/prks/{prk_id}', 'PrkController@show');
    $router->post('/prks/{prk_id}', 'PrkController@update');
    $router->delete('/prks/{prk_id}', 'PrkController@delete');

    $router->get('/prks/{prk_id}/jasas', 'PrkJasaController@index');
    $router->post('/prks/{prk_id}/jasas', 'PrkJasaController@create');
    $router->delete('/prks/{prk_id}/jasas/{jasa_id}', 'PrkJasaController@delete');

    $router->get('/prks/{prk_id}/materials', 'PrkMaterialController@index');
    $router->post('/prks/{prk_id}/materials', 'PrkMaterialController@create');
    $router->delete('/prks/{prk_id}/materials/{material_id}', 'PrkMaterialController@delete');

    // skki
    $router->get('/skkis', 'SkkiController@index');
    $router->post('/skkis', 'SkkiController@create');
    $router->get('/skkis/{skki_id}', 'SkkiController@show');
    $router->post('/skkis/{skki_id}', 'SkkiController@update');
    $router->delete('/skkis/{skki_id}', 'SkkiController@delete');
    
    $router->get('/skkis/{skki_id}/jasas', 'SkkiJasaController@index');
    $router->delete('/skkis/{skki_id}/jasas/{jasa_id}', 'SkkiJasaController@delete');
    
    $router->get('/skkis/{skki_id}/materials', 'SkkiMaterialController@index');
    $router->delete('/skkis/{skki_id}/materials/{material_id}', 'SkkiMaterialController@delete');

    // pengadaan
    $router->get('/pengadaans', 'PengadaanController@index');
    $router->post('/pengadaans', 'PengadaanController@create');
    $router->get('/pengadaans/{pengadaan_id}', 'PengadaanController@show');
    $router->post('/pengadaans/{pengadaan_id}', 'PengadaanController@update');
    $router->delete('/pengadaans/{pengadaan_id}', 'PengadaanController@delete');
    
    $router->get('/pengadaans/{pengadaan_id}/prk_skkis', 'PengadaanPrkSkkiController@index');

    $router->get('/pengadaans/{pengadaan_id}/jasas', 'PengadaanJasaController@index');
    
    $router->get('/pengadaans/{pengadaan_id}/materials', 'PengadaanMaterialController@index');

    // kontrak
    $router->get('/kontraks', 'KontrakController@index');
    $router->post('/kontraks', 'KontrakController@create');
    $router->get('/kontraks/{kontrak_id}', 'KontrakController@show');
    $router->post('/kontraks/{kontrak_id}', 'KontrakController@update');
    $router->delete('/kontraks/{kontrak_id}', 'KontrakController@delete');
    
    // pelaksanaan
    $router->get('/pelaksanaans', 'PelaksanaanController@index');
    $router->get('/pelaksanaans/{kontrak_id}', 'PelaksanaanController@show');
    $router->post('/pelaksanaans/{kontrak_id}', 'PelaksanaanController@update');

    $router->post('/pelaksanaans/{kontrak_id}/transaction/material', 'PelaksanaanController@materialTransaction');
    $router->post('/pelaksanaans/{kontrak_id}/transaction/jasa', 'PelaksanaanController@jasaTransaction');
    $router->delete('/pelaksanaans/{kontrak_id}/transaction/jasa/{jasa_id}', 'PelaksanaanController@deleteJasaTransaction');

    // pembayaran
    $router->get('/pembayarans', 'PembayaranController@index');
    $router->get('/pembayarans/{kontrak_id}', 'PembayaranController@show');
    $router->post('/pembayarans/{kontrak_id}', 'PembayaranController@create');
    $router->delete('/pembayarans/{kontrak_id}', 'PembayaranController@deleteByKontrak');

    $router->get('/materials', 'MaterialController@index');
    $router->post('/materials', 'MaterialController@create');
    $router->post('/materials/{material_id}', 'MaterialController@update');
    $router->delete('/materials/{material_id}', 'MaterialController@delete');

    $router->get('/stats/biaya', 'StatsController@biaya');
});