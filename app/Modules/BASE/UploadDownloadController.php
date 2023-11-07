<?php

namespace App\Modules\BASE;

use Storage;

use Illuminate\Http\Request;
use Symfony\Polyfill\Intl\Idn\Idn;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\url;
use Illuminate\Support\Facades\Response as Download;

use App\Modules\BASE\MY_Controller;
use App\Models\ModelBase;
use App\Helpers\Main AS MainHelper;

class UploadDownloadController extends MY_Controller
{
    public function __construct(Request $request){
        parent::__construct();
        $this->_viewPath = "BASE/";
        $this->_vars["_id_"] = "M00";
        $this->_vars['url'] = URL::to('/')."/base/";
    }

    public function download(Request $request){
        $path = $request->get('path');
        $dir = $request->get('d');
        $clear = $request->get('c');
        if($clear == 1){
            MainHelper::deleteFileMoreOneDay($request, $dir);
        }
        if(Storage::disk('local')->exists($path)){
            return Storage::disk('local')->download($path);
        }else{
            return "Files not found";
        }
    }
    public function preview(Request $request){
        $path = $request->get('path');
        $dir = $request->get('d');
        $clear = $request->get('c');
        if($clear == 1){
            MainHelper::deleteFileMoreOneDay($request, $dir);
        }
        if(Storage::disk('local')->exists($path)){
            $mimeType = Storage::disk('local')->mimeType($path);
            header("Content-type:".$mimeType);
            header("Content-Disposition:inline;filename='$path");
            echo Storage::disk('local')->get($path);
        }else{
            return "Files not found";
        }
    }
    // public function s3Upload(Request $request){
    //     // Helper::deleteFileMoreOneDay($request,'import');

    //     $checkPermission = Helper::checkPermission($request);
    //     if ($checkPermission['success'] === false) {
    //         return response()->json($checkPermission);
    //     }

    //     $checkPermissionApi = Helper::checkPermissionApi($request);
    //     if ($checkPermissionApi['success'] === false) {
    //         return response()->json($checkPermissionApi);
    //     }
    //     // dd($checkPermissionApi);

    //     $this->validate($request, [
    //         'file' => 'required|file|max:7000', // max 7MB
    //         'd' => 'required'
    //     ]);
    //     $path = Storage::disk('s3')->putFile(
    //         $request->post('d'),
    //         $request->file('file')
    //     );

    //     if($path){
    //         return response()->json([
    //             "success"=>true,
    //             "status_code"=>201,
    //             "status_name"=>"SUCCESS",
    //             "filepath"=>$path
    //         ]);
    //     }else{
    //         return response()->json([
    //             "success"=>false,
    //             "status_code"=>400,
    //             "status_name"=>"UPLOAD FAILED",
    //             "messages"=>"Request data to Database failed."
    //         ]);
    //     }
    // }
    // public function s3Preview(Request $request){
    //     $path = $request->get('path');
    //     $dir = $request->get('d');
    //     $clear = $request->get('c');
    //     if($clear == 1){
    //         Helper::deleteFileMoreOneDay($request, $dir);
    //     }
    //     if(Storage::disk('local')->exists($path)){
    //         $mimeType = Storage::disk('local')->mimeType($path);
    //         header("Content-type:".$mimeType);
    //         header("Content-Disposition:inline;filename='$path");
    //         echo Storage::disk('local')->get($path);
    //     }else{
    //         return "Files not found";
    //     }
    // }
    // public function s3Download(Request $request){
    //     $checkPermission = Helper::checkPermission($request);
    //     if ($checkPermission['success'] === false) {
    //         return response()->json($checkPermission);
    //     }

    //     $checkPermissionApi = Helper::checkPermissionApi($request);
    //     if ($checkPermissionApi['success'] === false) {
    //         return response()->json($checkPermissionApi);
    //     }

    //     $path = $request->get('path');
    //     $mode = $request->get('mode');
    //     if(Storage::disk('s3')->exists($path)){

    //         if($mode == "PREVIEW"){
    //             $fileContent = Storage::disk('s3')->get($path);
    //             $pathArray = explode("/",$path);
    //             $lastKey = array_key_last($pathArray);
    //             $localPath = "temp/".$pathArray[$lastKey];
    //             $copyToLocal = Storage::disk('local')->put($localPath, $fileContent);
    //             if($copyToLocal){
    //                 $urlDownload = "/api/download?path=".$localPath."&d=temp&c=1";
    //                 $urlPreview = "/api/preview?path=".$localPath."&d=temp&c=1";
    //                 return [
    //                     "success"=>true,
    //                     "status_code"=>201,
    //                     "status_name"=>"SUCCESS",
    //                     // "file_path"=>$zipFile,
    //                     "file_download"=>$urlDownload,
    //                     "file_preview"=>$urlPreview
    //                 ];
    //             }else{
    //                 return response()->json([
    //                     "success"=>false,
    //                     "status_code"=>400,
    //                     "status_name"=>"DOWNLOAD FAILED",
    //                     "messages"=>"Request data to Database failed."
    //                 ]);
    //             }
    //         }else{
    //             $temporaryUrl = Storage::disk('s3')->temporaryUrl(
    //                 $path,
    //                 now()->addHour(),
    //                 ['ResponseContentDisposition' => 'attachment']
    //             );

    //             if($temporaryUrl){
    //                 return response()->json([
    //                     "success"=>true,
    //                     "status_code"=>201,
    //                     "status_name"=>"SUCCESS",
    //                     "file_download"=>$temporaryUrl
    //                 ]);
    //             }else{
    //                 return response()->json([
    //                     "success"=>false,
    //                     "status_code"=>400,
    //                     "status_name"=>"DOWNLOAD FAILED",
    //                     "messages"=>"Request data to Database failed."
    //                 ]);
    //             }
    //         }
    //     }else{
    //         return response()->json([
    //             "success"=>false,
    //             "status_code"=>400,
    //             "status_name"=>"DOWNLOAD FAILED",
    //             "messages"=>"File not found."
    //         ]);
    //     }
    // }
    // public function s3Delete(Request $request){
    //     $checkPermission = Helper::checkPermission($request);
    //     if ($checkPermission['success'] === false) {
    //         return response()->json($checkPermission);
    //     }

    //     $checkPermissionApi = Helper::checkPermissionApi($request);
    //     if ($checkPermissionApi['success'] === false) {
    //         return response()->json($checkPermissionApi);
    //     }

    //     $path = $request->get('path');
    //     $delete = Storage::disk('s3')->delete($path);

    //     if($delete){
    //         return response()->json([
    //             "success"=>true,
    //             "status_code"=>201,
    //             "status_name"=>"SUCCESS"
    //         ]);
    //     }else{
    //         return response()->json([
    //             "success"=>false,
    //             "status_code"=>400,
    //             "status_name"=>"DELETE FAILED",
    //             "messages"=>"Request data to Database failed."
    //         ]);
    //     }
    // }
}
