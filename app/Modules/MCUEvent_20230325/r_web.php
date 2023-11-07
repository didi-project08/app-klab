<?php
use Illuminate\Support\Facades\Route;

use App\Modules\MCUEvent\Main;

$MAIN_URI = "mcu-event_20230325";
$CLASS = Main::class;
Route::get('/'.$MAIN_URI, [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create", [$CLASS, 'create']);
Route::get('/'.$MAIN_URI."/edit/{id?}", [$CLASS, 'edit']);
Route::put('/'.$MAIN_URI."/update/{id?}", [$CLASS, 'update']);
Route::delete('/'.$MAIN_URI."/delete/{id?}", [$CLASS, 'delete']);
Route::get('/'.$MAIN_URI."/confirm/{id?}", [$CLASS, 'confirm']);
Route::put('/'.$MAIN_URI."/confirmSave/{id?}", [$CLASS, 'confirmSave']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);



Route::get('/'.$MAIN_URI."/laboratoryCombo", [$CLASS, 'laboratoryCombo']);
Route::get('/'.$MAIN_URI."/laboratoryReferCombo", [$CLASS, 'laboratoryReferCombo']);
Route::get('/'.$MAIN_URI."/corporateCombo", [$CLASS, 'corporateCombo']);
Route::get('/'.$MAIN_URI."/readCorporateEmp", [$CLASS, 'readCorporateEmp']);



Route::get('/'.$MAIN_URI."/b2Index", [$CLASS, 'b2Index']);
Route::get('/'.$MAIN_URI."/b2Read", [$CLASS, 'b2Read']);
Route::get('/'.$MAIN_URI."/b2Add/{mcu_event_id?}", [$CLASS, 'b2Add']);
Route::post('/'.$MAIN_URI."/b2Create/{mcu_event_id?}", [$CLASS, 'b2Create']);
Route::get('/'.$MAIN_URI."/b2Edit/{id?}", [$CLASS, 'b2Edit']);
Route::put('/'.$MAIN_URI."/b2Update/{id?}", [$CLASS, 'b2Update']);
Route::delete('/'.$MAIN_URI."/b2Delete/{id?}", [$CLASS, 'b2Delete']);
Route::get('/'.$MAIN_URI."/b2Present/{id?}", [$CLASS, 'b2Present']);
Route::put('/'.$MAIN_URI."/b2PresentSave/{id?}", [$CLASS, 'b2PresentSave']);
Route::get('/'.$MAIN_URI."/b2Filter", [$CLASS, 'b2Filter']);