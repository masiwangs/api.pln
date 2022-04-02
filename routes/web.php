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

    $router->get('/prks', 'PrkController@index');
    $router->post('/prks', 'PrkController@create');
    $router->get('/prks/{prk_id}', 'PrkController@show');
    $router->post('/prks/{prk_id}', 'PrkController@update');
    $router->delete('/prks/{prk_id}', 'PrkController@delete');

    $router->get('/prks/{prk_id}/jasas', 'PrkJasaController@index');
    $router->post('/prks/{prk_id}/jasas', 'PrkJasaController@create');
    $router->delete('/prks/{prk_id}/jasas/{jasa_id}', 'PrkJasaController@delete');

    $router->get('/materials', 'MaterialController@index');
    $router->post('/materials', 'MaterialController@create');
    $router->post('/materials/{material_id}', 'MaterialController@update');
    $router->delete('/materials/{material_id}', 'MaterialController@delete');
});