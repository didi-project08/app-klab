<?php
use Illuminate\Support\Facades\Route;

use App\Modules\UserLaboratory\Main;
use App\Modules\UserLaboratory\B1Main;
use App\Modules\UserLaboratory\B3Main;
use App\Modules\UserLaboratory\B4Main;

$MAIN_URI = "user-laboratory";
$CLASS = Main::class;
Route::get('/'.$MAIN_URI, [$CLASS, 'index']);

$MAIN_URI = "user-laboratory/b1";
$CLASS = B1Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create", [$CLASS, 'create']);
Route::get('/'.$MAIN_URI."/edit/{id?}", [$CLASS, 'edit']);
Route::put('/'.$MAIN_URI."/update/{id?}", [$CLASS, 'update']);
Route::delete('/'.$MAIN_URI."/delete/{id?}", [$CLASS, 'delete']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);

$MAIN_URI = "user-laboratory/b3";
$CLASS = B3Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add/{mcu_format_id?}", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create/{mcu_format_id?}", [$CLASS, 'create']);
Route::get('/'.$MAIN_URI."/edit/{id?}", [$CLASS, 'edit']);
Route::put('/'.$MAIN_URI."/update/{id?}", [$CLASS, 'update']);
Route::delete('/'.$MAIN_URI."/delete/{id?}", [$CLASS, 'delete']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);

$MAIN_URI = "user-laboratory/b4";
$CLASS = B4Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::post('/'.$MAIN_URI."/roleModuleAccessUpdate/{f_role_id?}", [$CLASS, 'roleModuleAccessUpdate']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);


$MAIN_URI = "user-laboratory";
$MAIN_URI1 = "user-laboratory/b1";
$MAIN_URI3 = "user-laboratory/b3";
$MAIN_URI4 = "user-laboratory/b4";
$CLASS = Main::class;
$CLASS1 = B1Main::class;
$CLASS3 = B3Main::class;
$CLASS4 = B4Main::class;

Route::get('/'.$MAIN_URI1."/readLaboratoryCombo", [$CLASS, 'readLaboratoryCombo']);
Route::get('/'.$MAIN_URI1."/readRoleCombo", [$CLASS, 'readRoleCombo']);

Route::get('/'.$MAIN_URI3."/readLaboratoryCombo", [$CLASS, 'readLaboratoryCombo']);