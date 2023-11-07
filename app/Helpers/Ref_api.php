<?php
namespace App\Helpers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class Ref_api {
    public static function api_link($api_code = ""){
        $result = DB::table('t_ref_api')->where("f_api_code",$api_code)->where("f_delete",0)->get();
        return $result;
    }
}