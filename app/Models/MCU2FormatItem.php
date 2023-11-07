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

class MCU2FormatItem extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("mcu2_format_item AS a");
        
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
        if(isset($vars['mcu_format_id'])){
            $db = $db->whereRaw("a.mcu_format_id='".$vars['mcu_format_id']."'");
        }
        if(isset($vars['resultCate'])){
            $db = $db->whereRaw("a.main_parent='".$vars['resultCate']."'");
        }
        if(isset($vars['srcEvt'])){
            $db = $db->where(function($query) use ($vars){
                $query = $query->orWhereRaw("a.name LIKE '%".$vars['srcEvt']."%'");
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
    public static function converToTree($data, $parent = 0){
        $resultNew = [];
        foreach ($data as $k => $v) {
            if($v->parent == $parent){
                $v->iconCls = "icon-save-custome";
                if($v->level == 0 && $v->header == 1){
                    $v->name_custome = "<b>".$v->name."</b>";
                }else{
                    $v->name_custome = $v->name;
                }
                if($v->header == 1){
                    $v->header_name = "PARENT";
                    $v->children = self::converToTree($data, $v->id);
                }else{
                    $v->header_name = "INPUT";
                }
                array_push($resultNew, $v);
            }
        }
        return $resultNew;
    }
    public static function converToExcelField($data = [], $preTitle = ""){
        $result = [];
        foreach ($data as $k => $v) {
            if(isset($v->children)){
                if($preTitle == ""){
                    $preTitleNew = $v->level.":".$v->name;
                }else{
                    $preTitleNew = $preTitle."\n".$v->level.":".$v->name;
                }
                $result2 = self::converToExcelField($v->children, $preTitleNew);
                foreach ($result2 as $k2 => $v2) {
                    array_push($result, $v2);
                }
            }else{
                if($preTitle == ""){
                    $title = $v->level.":".$v->name;
                }else{
                    $title = $preTitle."\n".$v->level.":".$v->name;
                }
                $row['field'] = $v->code;
                $row['title'] = $title;
                $row['align'] = "LEFT";
                $row['width'] = 160;
                $row['dataType'] = "STRING";
                array_push($result, $row);
            }
        }
        return $result;
    }

    public static function duplicate($mcuFormatTree, $mcu_format_id = 0, $main_parent = 0, $parent = 0){
        $at = date("Y-m-d H:i:s");
        foreach ($mcuFormatTree as $k => $v) {
            $insert = (array) $v;
            unset($insert['id']);
            unset($insert['children']);
            unset($insert['iconCls']);
            unset($insert['header_name']);
            unset($insert['name_custome']);
            unset($insert['update_by']);
            unset($insert['update_at']);
            unset($insert['delete_at']);
            unset($insert['delete_at']);
            $insert['create_by'] = session()->get('sessUsername');
            $insert['create_at'] = $at;
            $insert['mcu_format_id'] = $mcu_format_id;
            $insert['main_parent'] = $main_parent;
            $insert['parent'] = $parent;
            DB::table("mcu2_format_item")->insert($insert);
            $lastInsertId = DB::getPdo()->lastInsertId();
            if($insert['parent'] == 0){
                DB::table("mcu2_format_item")->where(["id"=>$lastInsertId])->update(['main_parent'=>$lastInsertId]);
            }
            if(isset($v->children)){
                $lastData = DB::table("mcu2_format_item")->where(["id"=>$lastInsertId])->limit(1)->get();
                self::duplicate($v->children, $mcu_format_id, $lastData[0]->main_parent, $lastData[0]->id);
            }
        }
    }
}
