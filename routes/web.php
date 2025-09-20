<?php

use App\Core\Router;
use App\Core\Request;
use App\Core\Session;

Session::start();

$router = new Router();

$router->get('/', function () {
    redirect('/dashboard');
});

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout', ['auth']);

$router->get('/dashboard', 'DashboardController@index', ['auth']);

$router->get('/surat-masuk', 'SuratMasukController@index', ['auth']);
$router->get('/surat-masuk/create', 'SuratMasukController@create', ['auth']);
$router->post('/surat-masuk', 'SuratMasukController@store', ['auth']);
$router->get('/surat-masuk/{id}', 'SuratMasukController@show', ['auth']);
$router->get('/surat-masuk/{id}/edit', 'SuratMasukController@edit', ['auth']);
$router->post('/surat-masuk/{id}/update', 'SuratMasukController@update', ['auth']);
$router->post('/surat-masuk/{id}/delete', 'SuratMasukController@destroy', ['auth']);
$router->get('/surat-masuk/{id}/download', 'SuratMasukController@download', ['auth']);

$router->get('/surat-keluar', 'SuratKeluarController@index', ['auth']);
$router->get('/surat-keluar/create', 'SuratKeluarController@create', ['auth']);
$router->post('/surat-keluar', 'SuratKeluarController@store', ['auth']);
$router->get('/surat-keluar/{id}', 'SuratKeluarController@show', ['auth']);
$router->get('/surat-keluar/{id}/edit', 'SuratKeluarController@edit', ['auth']);
$router->post('/surat-keluar/{id}/update', 'SuratKeluarController@update', ['auth']);
$router->post('/surat-keluar/{id}/delete', 'SuratKeluarController@destroy', ['auth']);
$router->get('/surat-keluar/{id}/download', 'SuratKeluarController@download', ['auth']);

$router->get('/kategori', 'KategoriController@index', ['auth', 'role:admin']);
$router->get('/kategori/create', 'KategoriController@create', ['auth', 'role:admin']);
$router->post('/kategori', 'KategoriController@store', ['auth', 'role:admin']);
$router->get('/kategori/{id}/edit', 'KategoriController@edit', ['auth', 'role:admin']);
$router->post('/kategori/{id}/update', 'KategoriController@update', ['auth', 'role:admin']);
$router->post('/kategori/{id}/delete', 'KategoriController@destroy', ['auth', 'role:admin']);

$router->get('/laporan', 'LaporanController@index', ['auth']);

$router->get('/users', 'UserController@index', ['auth', 'role:admin']);
$router->get('/users/create', 'UserController@create', ['auth', 'role:admin']);
$router->post('/users', 'UserController@store', ['auth', 'role:admin']);
$router->get('/users/{id}/edit', 'UserController@edit', ['auth', 'role:admin']);
$router->post('/users/{id}/update', 'UserController@update', ['auth', 'role:admin']);
$router->post('/users/{id}/reset', 'UserController@resetPassword', ['auth', 'role:admin']);
$router->post('/users/{id}/delete', 'UserController@destroy', ['auth', 'role:admin']);

$router->get('/settings', 'SettingController@index', ['auth', 'role:admin']);
$router->post('/settings/identity', 'SettingController@updateIdentity', ['auth', 'role:admin']);
$router->get('/settings/backup', 'SettingController@backup', ['auth', 'role:admin']);
$router->post('/settings/restore', 'SettingController@restore', ['auth', 'role:admin']);

$request = new Request();
$router->dispatch($request);
