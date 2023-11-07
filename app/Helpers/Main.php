<?php
namespace App\Helpers;

use Storage;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory as Spreadsheet_IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Spreadsheet_Style_Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border as Spreadsheet_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Borders as Spreadsheet_Style_Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill as Spreadsheet_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as Spreadsheet_Style_NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType as Spreadsheet_Cell_DataType;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as Spreadsheet_Cell_Date;
use Carbon\Carbon;

use App\Models\Image;

class Main {
    public static function isValidDate($date, $format = 'Y-m-d'){
        if($date == null || $date == ''){
            return false;
        }
        $d = Carbon::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    public static function isValidDatetime($datetime, $format = 'Y-m-d H:i:s'){
        if($datetime == null || $datetime == ''){
            return false;
        }
        $d = Carbon::createFromFormat($format, $datetime);
        return $d && $d->format($format) == $datetime;
    }
    public static function calculateAge($dateFrom, $dateTo){
        $dateFrom = strtotime($dateFrom);
        $dateTo = strtotime($dateTo);
        $age = ($dateTo - $dateFrom) / 60 / 60 / 24 / 365.5;
        return $age;
    }
    public static function uuid(){
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ));
    }
    public static function getCol($colNum = 0){
        $colNum = $colNum * 1;

        $col = "";
        $colArr = array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $colMax = 26;
        
        if($colNum > 26){
            $colLoop = 0;
            for ($i=0; $i<$colNum ; $i=$i+$colMax) { 
                if(($colNum - $i) > $colMax){
                    $colLoop++;
                }
            }
            $colDiv = $colNum - ($colMax * $colLoop);
            if($colDiv == 0){
                $colDiv = 1;
            }
            $col = $colArr[($colLoop*1)].$colArr[($colDiv*1)];
            // $col = $colLoop."-".$colDiv;
        }else{
            $col = $colArr[($colNum*1)];
        }

        return $col;
    }
    public static function getMonth($monthNum = null, $format = 'M'){
        if($monthNum == null || $monthNum < 1 || !is_numeric($monthNum)){
            $monthName = "#NAN";
        }else{
            if($format == 'romawi'){
                $romawiList = ['','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
                $monthName = $romawiList[$monthNum];
            }else if($format == 'indo_full'){
                $indoList = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $monthName = $indoList[$monthNum];
            }else if($format == 'indo'){
                $indoList = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                $monthName = $indoList[$monthNum];
            }else{
                $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                $monthName = $dateObj->format($format);
            }	
        }
        return $monthName;
    }
    public static function mpdfMyStylesheet(){
        $stylesheet = "
            .row{ width: 100% }
            .row .col-1{ float: left; width: 8.3% }
            .row .col-2{ float: left; width: 16.6% }
            .row .col-3{ float: left; width: 24.9% }
            .row .col-4{ float: left; width: 33.3% }
            .row .col-5{ float: left; width: 41.6% }
            .row .col-6{ float: left; width: 49.9% }
            .row .col-7{ float: left; width: 58.3% }
            .row .col-8{ float: left; width: 66.6% }
            .row .col-9{ float: left; width: 74.9% }
            .row .col-10{ float: left; width: 83.3% }
            .row .col-11{ float: left; width: 91.6% }
            .row .col-12{ float: left; width: 100% }
            .text-left {text-align: left}
            .text-center {text-align: center}
            .text-right {text-align: right}
            .text-white {color:white}
            .font-italic {font-style: italic}
            .font-bold {font-weight: bold}
        ";

        $stylesheet = "
            .row{ width: 100% }
            .text-left {text-align: left}
            .text-center {text-align: center}
            .text-right {text-align: right}
            .text-white {color:white}
            .font-italic {font-style: italic}
            .font-bold {font-weight: bold}
        ";

        for ($i=0; $i <= 100; $i++) {
            $stylesheet .= ".row .colp-".$i."{ float: left; width: ".$i."% }";
        }

        for ($i=0; $i <= 100; $i++) { 
            $stylesheet .= ".p-".$i."px {padding: ".$i."px} ";
            $stylesheet .= ".pt-".$i."px {padding-top: ".$i."px} ";
            $stylesheet .= ".pr-".$i."px {padding-right: ".$i."px} ";
            $stylesheet .= ".pb-".$i."px {padding-bottom: ".$i."px} ";
            $stylesheet .= ".pl-".$i."px {padding-left: ".$i."px} ";
        }
        for ($i=0; $i <= 100; $i++) { 
            $stylesheet .= ".m-".$i."px {margin: ".$i."px} ";
            $stylesheet .= ".mt-".$i."px {margin-top: ".$i."px} ";
            $stylesheet .= ".mr-".$i."px {margin-right: ".$i."px} ";
            $stylesheet .= ".mb-".$i."px {margin-bottom: ".$i."px} ";
            $stylesheet .= ".ml-".$i."px {margin-left: ".$i."px} ";
        }
        for ($i=1; $i <= 100; $i++) { 
            $stylesheet .= ".font-".$i."pt {font-size: ".$i."pt} ";
        }
        for ($i=0; $i <= 10; $i++) { 
            $stylesheet .= ".lh-".$i." {line-height: ".$i."} ";
            for ($j=1; $j <= 9 ; $j++) { 
                $k = $i + ($j/10);
                $stylesheet .= ".lh-".$i."-".$j." {line-height: ".$k."} ";
            }
        }

        return $stylesheet;
    }

    public static function mpdfMyStylesheet_1(){
        
        $stylesheet = "
            .row{ width: 100% }
            .row .col-1{ float: left; width: 8.3% }
            .row .col-2{ float: left; width: 16.6% }
            .row .col-3{ float: left; width: 24.9% }
            .row .col-4{ float: left; width: 33.3% }
            .row .col-5{ float: left; width: 41.6% }
            .row .col-6{ float: left; width: 49.9% }
            .row .col-7{ float: left; width: 58.3% }
            .row .col-8{ float: left; width: 66.6% }
            .row .col-9{ float: left; width: 74.9% }
            .row .col-10{ float: left; width: 83.3% }
            .row .col-11{ float: left; width: 91.6% }
            .row .col-12{ float: left; width: 100% }
            .text-left {text-align: left}
            .text-center {text-align: center}
            .text-right {text-align: right}
            .text-white {color:white}
            .font-italic {font-style: italic}
            .font-bold {font-weight: bold}
        ";

        $stylesheet .= "
            .row{ width: 100% }
            .text-left {text-align: left}
            .text-center {text-align: center}
            .text-right {text-align: right}
            .text-white {color:white}
            .font-italic {font-style: italic}
            .font-bold {font-weight: bold}
        ";

        // $stylesheet .= "
        
        //     fieldset.subsa {
        //         color: #000;
        //         border: 1px groove #737373 !important;
        //         border-radius: 5px;
        //         padding: 0 1.4em 1.4em 1.4em !important;
        //         margin: 0 0 1.5em 0 !important;
        //         -webkit-box-shadow: 0px 0px 0px 0px #000;
        //         box-shadow: 0px 0px 0px 0px #000;
        //     }
            
        //    .subsa {
        //        display: block;
        //         padding-inline-start: 2px;
        //         padding-inline-end: 2px;
        //         border-width: initial;
        //         border-style: none;
        //         border-color: initial;
        //         border-image: initial;
        //         font-size: 1.2em !important;
        //         font-weight: bold !important;
        //         text-align: left !important;
        //         width: auto;
        //         padding: 5px 10px;
        //         border-bottom: none;
        //         border-radius: 5px;
        //         background: #737373;
        //         color: #FFF;
        //     }
        // ";

        for ($i=0; $i <= 100; $i++) {
            $stylesheet .= ".row .colp-".$i."{ float: left; width: ".$i."% }";
        }

        for ($i=0; $i <= 100; $i++) { 
            $stylesheet .= ".p-".$i."px {padding: ".$i."px} ";
            $stylesheet .= ".pt-".$i."px {padding-top: ".$i."px} ";
            $stylesheet .= ".pr-".$i."px {padding-right: ".$i."px} ";
            $stylesheet .= ".pb-".$i."px {padding-bottom: ".$i."px} ";
            $stylesheet .= ".pl-".$i."px {padding-left: ".$i."px} ";
        }
        for ($i=0; $i <= 100; $i++) { 
            $stylesheet .= ".m-".$i."px {margin: ".$i."px} ";
            $stylesheet .= ".mt-".$i."px {margin-top: ".$i."px} ";
            $stylesheet .= ".mr-".$i."px {margin-right: ".$i."px} ";
            $stylesheet .= ".mb-".$i."px {margin-bottom: ".$i."px} ";
            $stylesheet .= ".ml-".$i."px {margin-left: ".$i."px} ";
        }
        for ($i=1; $i <= 100; $i++) { 
            $stylesheet .= ".font-".$i."pt {font-size: ".$i."pt} ";
        }
        for ($i=0; $i <= 10; $i++) { 
            $stylesheet .= ".lh-".$i." {line-height: ".$i."} ";
            for ($j=1; $j <= 9 ; $j++) { 
                $k = $i + ($j/10);
                $stylesheet .= ".lh-".$i."-".$j." {line-height: ".$k."} ";
            }
        }

        return $stylesheet;
    }

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

    public static function validasi_nar($nik=0, $f_dob="", $gender=""){
        $vars['userScope'] = Model_base::m_user_scope();

        $f_provinsi_code = substr($nik, 0, 2);
        $f_kota_code = substr($nik, 0, 4);
        $f_kecamatan_code = substr($nik, 0, 6);
        $dd = substr($nik, 6, 2);
        $mm = substr($nik, 8, 2);
        $yy = substr($nik, 10, 2);

        if($dd * 1 > 40){
            $f_gender_nik = "F";
            $dd = ($dd * 1) - 40;
            if($dd < 10){
                $dd = "0".$dd;
            }
        }else{
            $f_gender_nik = "M";
        }

        if($yy * 1 >= 50){
            $yyyy = "19".$yy;
        }else{
            $yyyy = "20".$yy;
        }
        $f_dob_nik = $yyyy."-".$mm."-".$dd;

        // =========================== //
        $validasi['success'] = true;
        $validasi['message'] = [];
        if(strlen($nik)==16){
            if($gender!= $f_gender_nik){
                $validasi['success'] = false;
                $validasi['message']['f_gender'] = "Jenis kelamin tidak sesuai dengan nomor NIK!";
            }

            if($f_dob_nik != $f_dob){
                $validasi['success'] = false;
                $validasi['message']['f_dob_ddmmyyyy'] = "Tanggal lahir tidak sesuai dengan nomor NIK!";
            }

            $customParams['f_branch_id'] = $vars['userScope']['branch'][0]->f_branch_id;
            $user_nar = M_branch::m_grid01_read($customParams,"getOne");


            if($validasi['success']){
                $client = new GuzzleHttp_Client();
                $jar = new GuzzleHttp_CookieJar;

                $url_login = "https://allrecord-tc19.kemkes.go.id/Index.rpd";
                $url_check_nik = "https://allrecord-tc19.kemkes.go.id/Pasien/nik?nik=".$nik;

                $form_params = [
                    "user"=>$user_nar['data']->f_username,
                    "log_in"=>"true",
                    "pass"=>$user_nar['data']->f_password,
                ];
                $response = $client->request('POST', $url_login, [
                    'form_params' => $form_params,
                    'cookies'=>$jar,
                ]);
                $response = $client->request('GET', $url_check_nik, [
                    'cookies'=>$jar,
                ]);
                $statusCode = $response->getStatusCode();
                $content = $response->getBody()->getContents();

                $result = json_decode($content,true);
                if($result['result']==false){
                    $validasi['message']['cek_nik_nar'] = "NIK tidak valid!";
                }
                // dd($content);
            }
        }

        return $validasi['message'];
    }

    public static function spreadsheetStyle(){

		$result = [

			"Header" => array (
                "alignment" => array (
                    "horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_CENTER,
                    "vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
                ),
                "font" => array (
                    "bold" => true,
                    "color" => array (
                        "rgb" => "001e68"
                    ),
                    "size" => 16,
                ),
                // "fill" => array(
                //     "fillType" => Spreadsheet_Style_Fill::FILL_SOLID,
                //     // "color" => array('rgb' => '808080')
                //     "color" => array('rgb' => 'ffffff')
                // ),
                "alignment" => array (
                    "horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_LEFT,
                    "vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
                )
            ),

			"stHeader" => array (
                "alignment" => array (
                    "horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_CENTER,
                    "vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
                ),
                "font" => array (
                    "bold" => true,
                    "color" => array (
                        "rgb" => "ffffff"
                    ),
                    "size" => 9,
                ),
                "fill" => array(
                    "fillType" => Spreadsheet_Style_Fill::FILL_SOLID,
                    // "color" => array('rgb' => '808080')
                    "color" => array('rgb' => '001e68')
                ),
				'borders' => array(
					'allBorders' => array(
						'borderStyle' => Spreadsheet_Style_Border::BORDER_THIN,
                    	"color" => array('rgb' => '787878')
					)
				)
            ),

            "stFooter" => array (
                "alignment" => array (
                    "horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_CENTER,
                    "vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
                ),
                "font" => array (
                    "bold" => true,
                    "color" => array (
                        // "rgb" => "000000"
                        "rgb" => "ffffff"
                    ),
                    "size" => 9,
                ),
                "fill" => array(
                    "type" => Spreadsheet_Style_Fill::FILL_SOLID,
                    // "color" => array('rgb' => 'eaeaea')
                    "color" => array('rgb' => '001e68')
                )
            ),

            "stSize9" => array (
                "font" => array (
                    "size" => 9,
                )
            ),

            "stSize11Bold" => array (
                "font" => array (
                    "size" => 11,
                    "bold" => true
                )
            ),

            "stWrap" => array (
                "alignment" => array (
                    "wrapText" => true
                )
            ),

            "stCenter" => array (
                "alignment" => array (
                    "horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_CENTER,
                    "vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
                )
            ),

            "stRight" => array (
                "alignment" => array (
                    "horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_RIGHT,
                    "vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
                )
            ),

            "stLeft" => array (
                "alignment" => array (
                    "horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_LEFT,
                    "vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
                )
            ),

            "stMiddle" => array (
                "alignment" => array (
                    "vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
                )
            ),

            "stBold" => array (
                "font" => array (
                    "bold" => true
                )
            ),

            "stBorder" => array(
              'borders' => array(
                'allBorders' => array(
                	'borderStyle' => Spreadsheet_Style_Border::BORDER_THIN,
                )
              )
            )

		];

		return $result;
	}
    public static function excelHeader($c, $params){
        $xrInit = (isset($params['xr'])) ? $params['xr'] : 1;
        $xcInit = (isset($params['xc'])) ? $params['xc'] : 1;
        $fieldList = $params['fieldList'];

        $style = self::spreadsheetStyle();
        
        $xr = $xrInit;
        $xc = $xcInit; $xc--;
        $xrTo = $xr;
        $xcTo = $xc+1;

        $xcSpan = 1;
        foreach ($fieldList as $k => $v) {
            $xc = $xc + $xcSpan;
            $xcSpan = 1;

            $xrTo = $xr;
            $xcTo = $xc;
            if(isset($v['rowSpan']) || isset($v['colSpan'])){
                if(isset($v['rowSpan']) && is_numeric($v['rowSpan'])){
                    $xrTo = $xrTo + $v['rowSpan'] - 1;
                }
                if(isset($v['colSpan']) && is_numeric($v['colSpan'])){
                    $xcTo = $xcTo + $v['colSpan'] - 1;
                    $xcSpan = $v['colSpan'];
                }
                $c->mergeCells(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo));
            }
            $c->setCellValue(self::getCol($xc).($xr), $v['title']);
            $c->getStyle(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo))->applyFromArray($style['stBorder']);
            $c->getStyle(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo))->applyFromArray($style['stHeader']);
            $c->getStyle(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo))->applyFromArray($style['stWrap']);
            if(isset($v['style'])){
                $c->getStyle(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo))->applyFromArray($v['style']);
            }
            if(isset($v['width'])){
                $cWidth = $v['width'] / 8;
                $c->getColumnDimension(self::getCol($xc))->setWidth($cWidth);
            }
            
            $align = "stCenter";
            if(isset($v['align'])){
                if($v['align'] == 'LEFT'){
                    $align = "stLeft";
                }else if($v['align'] == 'RIGHT'){
                    $align = "stRight";
                }
            }
            $c->getStyle(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo))->applyFromArray($style[$align]);

            if(isset($v['headerStyle'])){
                $c->getStyle(self::getCol($xc).$xr.":".self::getCol($xcTo).$xrTo)->applyFromArray($v['headerStyle']);
            }

        }
        return ["xr"=>$xrTo, "xc"=>$xcTo];
    }
    public static function excelContent($c, $params){
        $xrInit = (isset($params['xr'])) ? $params['xr'] : 1;
        $xcInit = (isset($params['xc'])) ? $params['xc'] : 1;
        $fieldList = $params['fieldList'];
        $contentList = $params['contentList'];

        $style = self::spreadsheetStyle();

        $xr = $xrInit; $xr--;
        $xc = $xcInit; $xcInit--;
        $xrTo = $xr+1;
        $xcTo = $xc+1;
        foreach ($contentList as $kD => $vD) {
            $vD = (array) $vD;

            $xr++;
            $xc = $xcInit;
            $xcF = $xc+1;

            $xrTo = $xr;
            $xcTo = $xc+1;

            $rowVal = [];

            $xcSpan = 1;
            foreach ($fieldList as $k => $v) {
                $xc = $xc + $xcSpan;
                $xcSpan = 1;

                $xrTo = $xr;
                $xcTo = $xc;
                if(isset($v['rowSpan']) || isset($v['colSpan'])){
                    if(isset($v['rowSpan']) && is_numeric($v['rowSpan'])){
                        $xrTo = $xrTo + $v['rowSpan'] - 1;
                    }
                    if(isset($v['colSpan']) && is_numeric($v['colSpan'])){
                        $xcTo = $xcTo + $v['colSpan'] - 1;
                        $xcSpan = $v['colSpan'];
                    }
                    $c->mergeCells(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo));
                }
                
                $c->getStyle(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo))->applyFromArray($style['stBorder']);
                $c->getStyle(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo))->applyFromArray($style['stSize9']);
                if(isset($v['wrap']) && $v['wrap']){
                    $c->getStyle(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo))->applyFromArray($style['stWrap']);
                }
                if(isset($v['style'])){
                    $c->getStyle(self::getCol($xc).($xr).":".self::getCol($xcTo).($xrTo))->applyFromArray($v['style']);
                }
                if(isset($v['field'])){
                    // $fieldArray = explode(".",$v['field']);
                    // dd($fieldArray);
                    
                    // $dataSource = $vD;
                    // foreach ($fieldArray as $kFA => $vFA) {
                    //     if(isset($dataSource[$vFA])){
                    //         $dataSource = $dataSource[$vFA];
                    //     }else{
                    //         $dataSource[$vFA] = [];
                    //         $dataSource = $dataSource[$vFA];
                    //     }
                    // }
                    // $fieldValue = $dataSource;
                    // // dd($dataSource);
                    
                    // if(!is_array($fieldValue)){
                    //     $fieldValue = $fieldValue;
                    // }else{
                    //     $fieldValue = "";
                    // }
                    // $rowVal[$v['field']] = $fieldValue;

                    $fieldValue = @$vD[$v['field']];
                    $rowVal[$v['field']] = $fieldValue;

                    if(isset($v['phpFormula'])){
                        $val = $fieldValue;
                        eval($v['phpFormula']);
                        $fieldValue = $val;
                    }

                    if(isset($v['dataType'])){
                        if($v['dataType'] == "STRING"){
                            $c->setCellValueExplicit(self::getCol($xc).$xr, $fieldValue, Spreadsheet_Cell_DataType::TYPE_STRING);
                        }else if($v['dataType'] == "DATE"){
                            if(strtotime($fieldValue)){
                                $isDate = false;
                                if(self::isValidDate(date('Y-m-d\TH:i:s.vp', strtotime($fieldValue)), 'Y-m-d\TH:i:s.vp') || self::isValidDate(date('Y-m-d H:i:s', strtotime($fieldValue)), 'Y-m-d H:i:s') || self::isValidDate(date('Y-m-d', strtotime($fieldValue)), 'Y-m-d')){
                                    $isDate = true;
                                }
                                $t = 0;
                                if($isDate){
                                    if(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fieldValue) == 1){
                                        $fieldValue = Carbon::parse($fieldValue)->addMinutes($t*-1);
                                        $fieldValue = date("Y-m-d", strtotime($fieldValue));
                                    }else{
                                        $fieldValue = Carbon::parse($fieldValue)->addMinutes($t*-1);
                                    }
                                    $c->setCellValue(self::getCol($xc).$xr, Spreadsheet_Cell_Date::PHPToExcel($fieldValue));
                                }else{
                                    $c->setCellValueExplicit(self::getCol($xc).$xr, $fieldValue, Spreadsheet_Cell_DataType::TYPE_STRING);
                                }
                                // if($isDate){
                                //     $c->setCellValue(self::getCol($xc).$xr, Spreadsheet_Cell_Date::PHPToExcel(strtotime($fieldValue)));
                                // }else{
                                //     $c->setCellValueExplicit(self::getCol($xc).$xr, $fieldValue, Spreadsheet_Cell_DataType::TYPE_STRING);
                                // }
                            }else{
                                $c->setCellValueExplicit(self::getCol($xc).$xr, $fieldValue, Spreadsheet_Cell_DataType::TYPE_STRING);
                            }
                        }else{
                            $c->setCellValue(self::getCol($xc).$xr, $fieldValue);
                        }
                    }else{
                        $c->setCellValue(self::getCol($xc).$xr, $fieldValue);
                    }
                    
                    if(isset($v['formatCell'])){
                        $c->getStyle(self::getCol($xc).$xr.":".self::getCol($xcTo).$xrTo)->getNumberFormat()->setFormatCode($v['formatCell']);
                    }
                    
                    $align = "stLeft";
                    if(isset($v['align'])){
                        if($v['align'] == 'CENTER'){
                            $align = "stCenter";
                        }else if($v['align'] == 'RIGHT'){
                            $align = "stRight";
                        }
                    }
                    $c->getStyle(self::getCol($xc).$xr.":".self::getCol($xcTo).$xrTo)->applyFromArray($style[$align]);

                    if(isset($v['contentStyle'])){
                        $c->getStyle(self::getCol($xc).$xr.":".self::getCol($xcTo).$xrTo)->applyFromArray($v['contentStyle']);
                    }
                }
            }
        }
        return ["xr"=>$xrTo, "xc"=>$xcTo];
    }
    
    public static function deleteFileMoreOneDay(Request $request, $path = ""){
        $list = Storage::disk('local')->files($path);
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
    public static function image($appImage = ""){
        $sel = Image::query(["code"=>$appImage], ["mode"=>"rowOne"]);
        if($sel['success']){
            return $sel['data']->data;
        }
        return "";
    }
}