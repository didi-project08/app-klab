<?php
namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\url;
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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("laboratory AS a");
        
        if(isset($vars['delete'])){
            if($vars['delete'] == 0){
                $db = $db->whereRaw("a.delete_at IS NULL");
            }else if($vars['delete'] == 1){
                $db = $db->whereRaw("a.delete_at IS NOT NULL");
            }
        }else{
            $db = $db->whereRaw("a.delete_at IS NULL");
        }
        if(isset($vars['id'])){
            $db = $db->whereRaw("a.id='".$vars['id']."'");
        }
        if(isset($vars['srcEvt'])){
            $db = $db->where(function($query) use ($vars){
                $query = $query->orWhereRaw("a.code LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.title LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.prov LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.city LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.address LIKE '%".$vars['srcEvt']."%'");
            });
        }

        // $db = $db->orderBy('update_at', 'desc');
        $db = $db->orderBy('create_at', 'desc');
        
        if($params['mode'] == 'total'){
            $db = $db->get();
            $result = count($db);
        }else{
            if($params['mode'] == 'rows'){
                if (isset($_GET['page']) && is_numeric($_GET['page']) && isset($_GET['rows']) && is_numeric($_GET['rows'])) {
                    $page = $_GET['page'];
                    $rows = $_GET['rows'];
                    $offset = ($page - 1) * $rows;

                    $db = $db->skip($offset)->take($rows);
                    $db = $db->get();
                } else {
                    $db = $db->skip(0)->take(0);
                    $db = $db->get();
                }
            }else if($params['mode'] == 'rowsAll'){
                $db = $db->get();
            }else if($params['mode'] == 'rowOne'){
                $db = $db->get();
                if($db->count() == 0){
                    $getOneResult['success'] = false;
                    $getOneResult['message'] = "Data not found.";
                    return $getOneResult;
                }else if($db->count() > 1){
                    $getOneResult['success'] = false;
                    $getOneResult['message'] = "Double data found, please contact the data administrator.";
                    return $getOneResult;
                }else{
                    $getOneResult['success'] = true;
                    $getOneResult['message'] = "";
                }
            }

            $result = [];
            foreach ($db as $k => $v) {
                array_push($result, $v);
            }
        }

        if($params['mode'] == 'rowOne'){
            $getOneResult['data'] = $result[0];
            return $getOneResult;
        }else{
            return $result;
        }
    }

    public static function excelField($fieldOrder = []){
        $fieldList = [];
        $row = [];
        $row['field'] = "id";
        $row['title'] = "ID";
        $row['width'] = 70;
        $row['align'] = "CENTER";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "code";
        $row['title'] = "Code";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "title";
        $row['title'] = "Provider Name";
        $row['width'] = 200;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "prov";
        $row['title'] = "Provice";
        $row['width'] = 160;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "city";
        $row['title'] = "City";
        $row['width'] = 160;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "address";
        $row['title'] = "Address";
        $row['width'] = 300;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "pic_name";
        $row['title'] = "PIC Name";
        $row['width'] = 100;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "pic_phone";
        $row['title'] = "PIC Phone";
        $row['width'] = 100;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;

        if(count($fieldOrder) > 0){
            $fieldListNew = [];
            foreach ($fieldOrder as $k => $v) {
                
            }
        }else{
            return $fieldList;
        }
    }

    public static function importTemplateField($fieldOrder = []){
        $fieldList = [];

        $row = [];
        $row['field'] = "code";
        $row['title'] = "Code";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['style'] = [
            "fill" => [
                "color" => array('rgb' => 'FF0000')
            ]
        ];
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "title";
        $row['title'] = "Provider Name";
        $row['width'] = 200;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $row['style'] = [
            "fill" => [
                "color" => array('rgb' => 'FF0000')
            ]
        ];
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "prov";
        $row['title'] = "Province";
        $row['width'] = 160;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "city";
        $row['title'] = "City";
        $row['width'] = 160;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "address";
        $row['title'] = "Address";
        $row['width'] = 300;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "pic_name";
        $row['title'] = "PIC Name";
        $row['width'] = 100;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['style'] = [
            "fill" => [
                "color" => array('rgb' => 'FF0000')
            ]
        ];
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "pic_phone";
        $row['title'] = "PIC Phone";
        $row['width'] = 100;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['style'] = [
            "fill" => [
                "color" => array('rgb' => 'FF0000')
            ]
        ];
        $fieldList[$row['field']] = $row;

        if(count($fieldOrder) > 0){
            $fieldListNew = [];
            foreach ($fieldOrder as $k => $v) {
                
            }
        }else{
            return $fieldList;
        }
    }
}