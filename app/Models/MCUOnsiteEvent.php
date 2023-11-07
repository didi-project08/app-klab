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

class MCUOnsiteEvent extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("mcu_onsite_event AS a")
            ->select(DB::raw("a.*,
                        sa.name AS status_name, sa.color AS status_color"))
            ->leftJoin(DB::raw("(SELECT * FROM status_all WHERE tbl='mcu_event') AS sa"), function($join){
                $join->on("a.status","=","sa.status");
            });

        // $vars['countPatient'] = true;
        if(isset($vars['countPatient']) && $vars['countPatient']){
            $qPatient =
                DB::table("mcu_onsite_event_patient AS a")
                ->select(DB::raw("a.mcu_event_id,
                        COUNT(1) AS patient_total,
                        SUM(IF(a.status = 'sScheduled', 1, 0)) AS patient_scheduled,
                        SUM(IF(a.status = 'sPresented', 1, 0)) AS patient_presented,
                        SUM(IF(a.status = 'sCanceled', 1, 0)) AS patient_canceled,
                        SUM(IF(a.status = 'sFinished', 1, 0)) AS patient_finished"))
                ->whereRaw("a.delete_at IS NULL")
                ->groupBy("a.mcu_event_id");
            $db = 
                $db->addSelect(DB::raw("f.patient_total, f.patient_scheduled, f.patient_presented, f.patient_canceled, f.patient_finished"))
                ->leftJoin(DB::raw("(".$qPatient->toSQL().") AS f"), function($join){
                    $join->on("a.id","=","f.mcu_event_id");
                });
        }

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
        if(isset($vars['corporate_id'])){
            $db = $db->whereRaw("a.corporate_id='".$vars['corporate_id']."'");
        }
        if(isset($vars['srcEvt'])){
            $db = $db->where(function($query) use ($vars){
                $query = $query->orWhereRaw("a.title LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.date_from LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.date_to LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("DATE_FORMAT(a.date_from, '%d/%m/%Y') LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("DATE_FORMAT(a.date_to, '%d/%m/%Y') LIKE '%".$vars['srcEvt']."%'");
            });
        }
        if(isset($vars['statusList'])){
            if(count($vars['statusList']) > 0){
                $db = $db->where(function($query) use ($vars){
                    foreach ($vars['statusList'] as $k => $v) {
                        $query = $query->orWhereRaw("a.status='".$v."'");
                    }
                });
            }else{
                $db = $db->whereRaw("a.status=-9");
            }   
        }
        if(isset($vars['orderBy'])){
            if(count($vars['orderBy']) > 0){
                foreach ($vars['orderBy'] as $k => $v) {
                    if($e = explode(":",$v)){
                        $field = $e[0]; 
                        $order = $e[1];

                        if($field == 'ST'){
                            if(isset($vars['countPatient']) && $vars['countPatient']){
                                $db = $db->orderByRaw("IF(f.patient_total = 0, 0, IF(f.patient_total = (f.patient_canceled + f.patient_finished),1,0)) ".$order);
                            }
                        }else{
                            $db = $db->orderBy($field, $order);
                        }
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
                $v->eventFinish = "A";
                $v->eventFinishColor = "red";
                if(isset($v->patient_total)){
                    if($v->patient_total > 0){
                        if($v->patient_total == ($v->patient_canceled + $v->patient_finished)){
                            $v->eventFinish = "F";
                            $v->eventFinishColor = "blue";
                        }
                    }
                }
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
