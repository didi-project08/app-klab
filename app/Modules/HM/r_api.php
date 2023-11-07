<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\HM\API1Main;

// $MAIN_URI = "fks";
// Route::post('/'.$MAIN_URI."/push", function(Request $request){
//     $array = [
//         "d01"=>"Default 1",
//         "d02"=>"Default 2",
//     ];
//     $response = $array + $request->all();
//     return response()->json($response);
// });

$MAIN_URI = "hm/api1";
$CLASS = API1Main::class;
Route::post('/'.$MAIN_URI."/store", [$CLASS, 'store']);
