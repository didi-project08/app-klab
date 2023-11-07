<?php
namespace App\Helpers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Encryption\DecryptException;


class Encrypt_mod {
    
    public static function payment_method($payment_type=0){
        $result = DB::table('t_payment_method')->where("f_payment_method_id",$payment_type)->where("f_delete",0)->get()->toArray();

        return $result;
    }

    public static function encrypt_general($mode = "", $data = ""){
        $result = DB::table('t_encrypt_key')->where("f_key_code","GENERAL")->where("f_delete",0)->get()->toArray();
        
        if($mode == "encrypt"){

            $encrypter = new \Illuminate\Encryption\Encrypter($result[0]->f_private_key, $result[0]->f_cipher);

            $return_result = $encrypter->encryptString($data);

        }else if($mode == "decrypt"){
            try {
                $encrypter = new \Illuminate\Encryption\Encrypter($result[0]->f_public_key, $result[0]->f_cipher);

                $return_result = $encrypter->decryptString($data);
            } catch (DecryptException $e) {
                $return_result = 'notValid';
            }
        }
        
        // echo "<pre>";print_r($return_result);exit();
        return $return_result;
    }

    public static function encrypt_transaction_pcr($mode = "", $data = ""){
        $result = DB::table('t_encrypt_key')->where("f_key_code","TRANSACTION_PCR")->where("f_delete",0)->get()->toArray();
        
        if($mode == "encrypt"){

            $encrypter = new \Illuminate\Encryption\Encrypter($result[0]->f_private_key, $result[0]->f_cipher);

            $return_result = $encrypter->encryptString($data);

        }else if($mode == "decrypt"){
            try {
                $encrypter = new \Illuminate\Encryption\Encrypter($result[0]->f_public_key, $result[0]->f_cipher);

                $return_result = $encrypter->decryptString($data);
            } catch (DecryptException $e) {
                $return_result = 'notValid';
            }
        }
        
        return $return_result;
    }



}