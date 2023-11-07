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

class Image extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("image AS a");

        
        if(isset($vars['code'])){
            $db = $db->whereRaw("a.code='".$vars['code']."'");
        }
        
        if(isset($vars['q'])){
            $vars['srcEvt'] = $vars['q'];
        }
        if(isset($vars['srcEvt'])){
            $db = $db->where(function($query) use ($vars){
                $query = $query->orWhereRaw("a.code LIKE '%".$vars['srcEvt']."%'");
            });
        }

        if(isset($vars['orderBy'])){
            if(count($vars['orderBy']) > 0){
                foreach ($vars['orderBy'] as $k => $v) {
                    if($e = explode(":",$v)){
                        $field = $e[0]; 
                        $order = $e[1];
                        $db = $db->orderBy($field, $order);
                    }
                }
            }
        }else{
            $db = $db->orderBy('a.create_at', 'desc');
        }
        
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
        $row['field'] = "code";
        $row['title'] = "Corporate Name";
        $row['width'] = 300;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
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
        $row['title'] = "Title";
        $row['width'] = 300;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
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
