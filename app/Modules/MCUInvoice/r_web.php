<?php
use Illuminate\Support\Facades\Route;

use App\Modules\MCUInvoice\Main;
use App\Modules\MCUInvoice\B1Main;
use App\Modules\MCUInvoice\B2Main;
use App\Modules\MCUInvoice\B3Main;

$MAIN_URI = "mcu-invoice";
$CLASS = Main::class;
Route::get('/'.$MAIN_URI, [$CLASS, 'index']);

$MAIN_URI = "mcu-invoice/b1";
$CLASS = B1Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::post('/'.$MAIN_URI."/generate", [$CLASS, 'generate']);
Route::delete('/'.$MAIN_URI."/delete/{id?}", [$CLASS, 'delete']);
Route::put('/'.$MAIN_URI."/approve/{id?}", [$CLASS, 'approve']);
Route::get('/'.$MAIN_URI."/confirm/{id?}", [$CLASS, 'confirm']);
Route::put('/'.$MAIN_URI."/confirmSave/{id?}", [$CLASS, 'confirmSave']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);

Route::get('/'.$MAIN_URI."/readCorporateCombo", [$CLASS, 'readCorporateCombo']);
Route::get('/'.$MAIN_URI."/readCorporateClientCombo", [$CLASS, 'readCorporateClientCombo']);
Route::get('/'.$MAIN_URI."/readLaboratoryCombo", [$CLASS, 'readLaboratoryCombo']);
Route::get('/'.$MAIN_URI."/readMCUFormatCombo", [$CLASS, 'readMCUFormatCombo']);
Route::get('/'.$MAIN_URI."/readCorporateEmp", [$CLASS, 'readCorporateEmp']);

$MAIN_URI = "mcu-invoice/b2";
$CLASS = B2Main::class;
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
Route::get('/'.$MAIN_URI."/sendWA/{id?}", [$CLASS, 'sendWA']);
Route::post('/'.$MAIN_URI."/sendWAProcess/{mcu_event_id?}/{id?}", [$CLASS, 'sendWAProcess']);

$MAIN_URI = "mcu-invoice/b3";
$CLASS = B3Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add/{mcu_event_id?}", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create/{mcu_event_id?}", [$CLASS, 'create']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);




$MAIN_URI = "mcu-invoice";
$MAIN_URI1 = "mcu-invoice/b1";
$MAIN_URI2 = "mcu-invoice/b2";
$MAIN_URI3 = "mcu-invoice/b3";
$CLASS = Main::class;
$CLASS1 = B1Main::class;
$CLASS2 = B2Main::class;
$CLASS3 = B3Main::class;

Route::get('/'.$MAIN_URI2."/readFormatPackageCombo", [$CLASS, 'readFormatPackageCombo']);
Route::get('/'.$MAIN_URI3."/readFormatPackageCombo", [$CLASS, 'readFormatPackageCombo']);
Route::get('/'.$MAIN_URI2."/readMCUInvoice", [$CLASS1, 'read']);