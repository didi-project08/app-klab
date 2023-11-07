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

class ModuleAccess extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("tc_module AS a")
            ->select(DB::raw("b.*, a.f_module_name"))
            ->join("tc_module_access AS b", function($join){
                $join->on("a.f_module_id","=","b.f_module_id");
            });
        
        if(isset($vars['active'])){
            $db = $db->whereRaw("a.f_active = ".$vars['active']);
            $db = $db->whereRaw("b.f_active = ".$vars['active']);
        }else{
            $db = $db->whereRaw("a.f_active=1");
            $db = $db->whereRaw("b.f_active=1");
        }

        if(isset($vars['f_module_id'])){
            $db = $db->whereRaw("a.f_module_id='".$vars['f_module_id']."'");
        }
        if(isset($vars['f_module_access_id'])){
            $db = $db->whereRaw("b.f_module_access_id='".$vars['f_module_access_id']."'");
        }
        if(isset($vars['f_show_to'])){
            $db = $db->whereRaw("b.f_show_to LIKE '%".$vars['f_show_to']."%'");
        }
        if(isset($vars['srcEvt'])){
            $db = $db->where(function($query) use ($vars){
                $query = $query->orWhereRaw("a.f_access_name LIKE '%".$vars['srcEvt']."%'");
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
            $db = $db->orderBy('a.f_sort', 'asc');
            $db = $db->orderBy('b.f_sort', 'asc');
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
    public static function converToTree($data){
        $resultNew = [];
        foreach ($data as $k => $v) {
            $v = (array) $v;

            if($v['f_module_access'] == "/index"){
                $vParent['id'] = $v['f_module_id'] - ($v['f_module_id'] + $v['f_module_id'] + 1000);
                $vParent['name'] = $v['f_module_name'];
                $vParent['f_module_access'] = "";
                $vParent['f_xml_http_request'] = "";
                $resultNew[$v['f_module_id']] = $vParent;

                $v['id'] = $v['f_module_access_id'];
                $v['name'] = ($v['f_access_name'] == "" || $v['f_access_name'] == null) ? $v['f_module_access'] : $v['f_access_name'];
                $resultNew[$v['f_module_id']]["children"][] = $v;
            }else{
                $v['id'] = $v['f_module_access_id'];
                $v['name'] = ($v['f_access_name'] == "" || $v['f_access_name'] == null) ? $v['f_module_access'] : $v['f_access_name'];
                $resultNew[$v['f_module_id']]["children"][] = $v;
            }
        }
        $resultNew2 = [];
        foreach ($resultNew as $k => $v) {
            $resultNew2[] = $v;
        }
        return $resultNew2;
    }
}
