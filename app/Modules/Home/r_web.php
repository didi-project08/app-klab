<?php
use Illuminate\Support\Facades\Route;

use App\Modules\Home\Main;

$MAIN_URI = "home";
$CLASS = Main::class;
Route::get('/'.$MAIN_URI, [$CLASS, 'index']);