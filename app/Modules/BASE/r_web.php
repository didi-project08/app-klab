<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Modules\BASE\UploadDownloadController;

Route::get('/base/download', [UploadDownloadController::class, 'download']);
Route::get('/base/preview', [UploadDownloadController::class, 'preview']);
