<?php
use Illuminate\Support\Facades\Route;

use App\Modules\MCUMonitoring\Main;
use App\Modules\MCUMonitoring\B1Main;

$MAIN_URI = "mcu-monitoring";
$CLASS = Main::class;
Route::get('/'.$MAIN_URI, [$CLASS, 'index']);

$MAIN_URI = "mcu-monitoring/b1";
$CLASS = B1Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add/{mcu_event_id?}", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create/{mcu_event_id?}", [$CLASS, 'create']);
Route::get('/'.$MAIN_URI."/edit/{id?}", [$CLASS, 'edit']);
Route::put('/'.$MAIN_URI."/update/{id?}", [$CLASS, 'update']);
Route::delete('/'.$MAIN_URI."/delete/{id?}", [$CLASS, 'delete']);
Route::get('/'.$MAIN_URI."/present/{id?}", [$CLASS, 'present']);
Route::put('/'.$MAIN_URI."/presentSave/{id?}", [$CLASS, 'presentSave']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);
Route::get('/'.$MAIN_URI."/patientExport", [$CLASS, 'patientExport']);
Route::get('/'.$MAIN_URI."/resultExport", [$CLASS, 'resultExport']);
Route::get('/'.$MAIN_URI."/resultImport/{id?}", [$CLASS, 'resultImport']);
Route::post('/'.$MAIN_URI."/resultImportSave/{id?}", [$CLASS, 'resultImportSave']);
Route::get('/'.$MAIN_URI."/move/{id?}", [$CLASS, 'move']);
Route::post('/'.$MAIN_URI."/moveSave/{id?}", [$CLASS, 'moveSave']);

$MAIN_URI = "mcu-monitoring";
$MAIN_URI1 = "mcu-monitoring/b1";
$CLASS = Main::class;
$CLASS1 = B1Main::class;

Route::get('/'.$MAIN_URI1."/readFormatPackageCombo", [$CLASS, 'readFormatPackageCombo']);