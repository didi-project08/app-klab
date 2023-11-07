<?php
use Illuminate\Support\Facades\Route;

use App\Modules\Auth\Main;

Route::get('/view403', function(){
  // return 'Access denied';
  return $_GET['message'];
});


$MAIN_URI = "";
$CLASS = Main::class;
Route::get('/', [$CLASS, 'index']);
Route::post('/login', [$CLASS, 'login']);
Route::get('/logout', [$CLASS, 'logout']);