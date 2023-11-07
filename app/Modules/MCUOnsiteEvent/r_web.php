<?php
use Illuminate\Support\Facades\Route;

use App\Modules\MCUOnsiteEvent\Main;
use App\Modules\MCUOnsiteEvent\B1Main;
use App\Modules\MCUOnsiteEvent\B2Main;
use App\Modules\MCUOnsiteEvent\B3Main;

// use Storage;
use Illuminate\Http\Request;


Route::get('download', function(Request $request){
    $path = $request->get('path');
    $dir = $request->get('d');
    $clear = $request->get('c');
    if($clear == 1){
        $list = Storage::disk('local')->files($dir);
        foreach ($list as $k => $v) {
            if($v != ".gitignore"){
                $now = time();
                $fileCreated = Storage::disk('local')->lastModified($v);
                $gap = 60*60*24;

                if($now - $fileCreated >= $gap){
                    Storage::disk('local')->delete($v);
                }
            }
        }
    }
    if(Storage::disk('local')->exists($path)){
        return Storage::disk('local')->download($path);
    }else{
        return "Files not found";
    }
});

$MAIN_URI = "mcu-onsite-event";
$CLASS = Main::class;
Route::get('/'.$MAIN_URI, [$CLASS, 'index']);

$MAIN_URI = "mcu-onsite-event/b1";
$CLASS = B1Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create", [$CLASS, 'create']);
Route::get('/'.$MAIN_URI."/edit/{id?}", [$CLASS, 'edit']);
Route::put('/'.$MAIN_URI."/update/{id?}", [$CLASS, 'update']);
Route::delete('/'.$MAIN_URI."/delete/{id?}", [$CLASS, 'delete']);
Route::get('/'.$MAIN_URI."/confirm/{id?}", [$CLASS, 'confirm']);
Route::put('/'.$MAIN_URI."/confirmSave/{id?}", [$CLASS, 'confirmSave']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);

Route::get('/'.$MAIN_URI."/readCorporateCombo", [$CLASS, 'readCorporateCombo']);
Route::get('/'.$MAIN_URI."/readCorporateClientCombo", [$CLASS, 'readCorporateClientCombo']);
Route::get('/'.$MAIN_URI."/readLaboratoryCombo", [$CLASS, 'readLaboratoryCombo']);
Route::get('/'.$MAIN_URI."/readMCUFormatCombo", [$CLASS, 'readMCUFormatCombo']);
Route::get('/'.$MAIN_URI."/readCorporateEmp", [$CLASS, 'readCorporateEmp']);

$MAIN_URI = "mcu-onsite-event/b2";
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
Route::get('/'.$MAIN_URI."/label/{id?}", [$CLASS, 'label']);
Route::get('/'.$MAIN_URI."/updateResult/{id?}", [$CLASS, 'updateResult']);
Route::put('/'.$MAIN_URI."/updateResultSave/{id?}", [$CLASS, 'updateResultSave']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);
Route::get('/'.$MAIN_URI."/patientExport", [$CLASS, 'patientExport']);
Route::get('/'.$MAIN_URI."/resultExport", [$CLASS, 'resultExport']);
Route::get('/'.$MAIN_URI."/resultImport/{id?}", [$CLASS, 'resultImport']);
Route::post('/'.$MAIN_URI."/resultImportSave/{id?}", [$CLASS, 'resultImportSave']);
Route::get('/'.$MAIN_URI."/move/{id?}", [$CLASS, 'move']);
Route::post('/'.$MAIN_URI."/moveSave/{id?}", [$CLASS, 'moveSave']);
Route::get('/'.$MAIN_URI."/sendWA/{id?}", [$CLASS, 'sendWA']);
Route::post('/'.$MAIN_URI."/sendWAProcess/{mcu_event_id?}/{id?}", [$CLASS, 'sendWAProcess']);
Route::post('/'.$MAIN_URI."/printCover", [$CLASS, 'printCover']);

$MAIN_URI = "mcu-onsite-event/b3";
$CLASS = B3Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add/{mcu_event_id?}", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create/{mcu_event_id?}", [$CLASS, 'create']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);




$MAIN_URI = "mcu-onsite-event";
$MAIN_URI1 = "mcu-onsite-event/b1";
$MAIN_URI2 = "mcu-onsite-event/b2";
$MAIN_URI3 = "mcu-onsite-event/b3";
$CLASS = Main::class;
$CLASS1 = B1Main::class;
$CLASS2 = B2Main::class;
$CLASS3 = B3Main::class;

Route::get('/'.$MAIN_URI2."/readFormatPackageCombo", [$CLASS, 'readFormatPackageCombo']);
Route::get('/'.$MAIN_URI3."/readFormatPackageCombo", [$CLASS, 'readFormatPackageCombo']);
Route::get('/'.$MAIN_URI2."/readMCUEvent", [$CLASS1, 'read']);