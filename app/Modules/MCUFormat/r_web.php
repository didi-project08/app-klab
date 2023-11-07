<?php
use Illuminate\Support\Facades\Route;

use App\Modules\MCUFormat\Main;
use App\Modules\MCUFormat\B1Main;
use App\Modules\MCUFormat\B2Main;
use App\Modules\MCUFormat\B3Main;
use App\Modules\MCUFormat\B4Main;

$MAIN_URI = "mcu-format";
$CLASS = Main::class;
Route::get('/'.$MAIN_URI, [$CLASS, 'index']);

$MAIN_URI = "mcu-format/b1";
$CLASS = B1Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create", [$CLASS, 'create']);
Route::get('/'.$MAIN_URI."/edit/{id?}", [$CLASS, 'edit']);
Route::put('/'.$MAIN_URI."/update/{id?}", [$CLASS, 'update']);
Route::delete('/'.$MAIN_URI."/delete/{id?}", [$CLASS, 'delete']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);
Route::get('/'.$MAIN_URI."/readLaboratoryCombo", [$CLASS, 'readLaboratoryCombo']);

$MAIN_URI = "mcu-format/b2";
$CLASS = B2Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add/{mcu_format_id?}/{id?}", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create/{mcu_format_id?}/{id?}", [$CLASS, 'create']);
Route::post('/'.$MAIN_URI."/createInput/{mcu_format_id?}/{id?}", [$CLASS, 'createInput']);
Route::get('/'.$MAIN_URI."/edit/{id?}", [$CLASS, 'edit']);
Route::put('/'.$MAIN_URI."/update/{id?}", [$CLASS, 'update']);
Route::put('/'.$MAIN_URI."/updateInput/{id?}", [$CLASS, 'updateInput']);
Route::delete('/'.$MAIN_URI."/delete/{id?}", [$CLASS, 'delete']);
Route::put('/'.$MAIN_URI."/moveTo", [$CLASS, 'moveTo']);
Route::get('/'.$MAIN_URI."/duplicateTo/{id?}", [$CLASS, 'duplicateTo']);
Route::post('/'.$MAIN_URI."/duplicateToSave/{id?}", [$CLASS, 'duplicateToSave']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);

$MAIN_URI = "mcu-format/b3";
$CLASS = B3Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add/{mcu_format_id?}", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create/{mcu_format_id?}", [$CLASS, 'create']);
Route::get('/'.$MAIN_URI."/edit/{id?}", [$CLASS, 'edit']);
Route::put('/'.$MAIN_URI."/update/{id?}", [$CLASS, 'update']);
Route::delete('/'.$MAIN_URI."/delete/{id?}", [$CLASS, 'delete']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);

$MAIN_URI = "mcu-format/b4";
$CLASS = B4Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::post('/'.$MAIN_URI."/packageItemUpdate/{mcu_format_package_id?}", [$CLASS, 'packageItemUpdate']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);


$MAIN_URI = "mcu-format";
$MAIN_URI1 = "mcu-format/b1";
$MAIN_URI2 = "mcu-format/b2";
$MAIN_URI3 = "mcu-format/b3";
$MAIN_URI4 = "mcu-format/b3";
$CLASS = Main::class;
$CLASS1 = B1Main::class;
$CLASS2 = B2Main::class;
$CLASS3 = B3Main::class;
$CLASS4 = B4Main::class;

Route::get('/'.$MAIN_URI2."/readMCUFormat", [$CLASS1, 'read']);