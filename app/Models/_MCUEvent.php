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

class MCUEvent extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("mcu_event AS a")
            ->select(DB::raw("a.*, b.title AS laboratory_name, c.title AS refer_name, sa.name AS status_name, sa.color AS status_color"))
            ->leftJoin("laboratory AS b", function($join){
                $join->on("a.laboratory_id","=","b.id");
            })
            ->leftJoin("laboratory AS c", function($join){
                $join->on("a.laboratory_id_refer","=","c.id");
            })
            ->leftJoin(DB::raw("(SELECT * FROM status_all WHERE tbl='mcu_event') AS sa"), function($join){
                $join->on("a.status","=","sa.status");
            });
        
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
                $query = $query->orWhereRaw("b.title LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.title LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.date_from LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.date_to LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("DATE_FORMAT(a.date_from, '%d/%m/%Y') LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("DATE_FORMAT(a.date_to, '%d/%m/%Y') LIKE '%".$vars['srcEvt']."%'");
            });
        }

        if(isset($vars['userLaboratory'])){
            if(count($vars['userLaboratory']) > 0){
                $db = $db->where(function($query) use ($vars){
                    foreach ($vars['userLaboratory'] as $k => $v) {
                        $query = $query->orWhereRaw("a.laboratory_id=".$v->id);
                        $query = $query->orWhereRaw("a.laboratory_id_refer=".$v->id);
                    }
                });
            }else{
                $db = $db->whereRaw("a.id=-9");
            }   
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
                $v->eventnum = "EV" . str_pad($v->id, 4, '0', STR_PAD_LEFT);
                $v->date_from_dmY_slash = $v->date_from;
                if(strtotime($v->date_from)){
                    $v->date_from_dmY_slash = date("d/m/Y", strtotime($v->date_from));
                }
                $v->date_to_dmY_slash = $v->date_to;
                if(strtotime($v->date_to)){
                    $v->date_to_dmY_slash = date("d/m/Y", strtotime($v->date_to));
                }
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
}
