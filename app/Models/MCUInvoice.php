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

class MCUInvoice extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("mcu_invoice AS a")
            ->select(DB::raw("a.*,
                        b.code AS corporate_code, b.title AS corporate_name,
                        c.title AS corporate_client_name,
                        sa.name AS status_name, sa.color AS status_color"))
            ->leftJoin("corporate AS b", function($join){
                $join->on("a.corporate_id","=","b.id");
            })
            ->leftJoin("corporate_client AS c", function($join){
                $join->on("a.corporate_client_id","=","c.id");
            })
            ->leftJoin(DB::raw("(SELECT * FROM status_all WHERE tbl='mcu_invoice') AS sa"), function($join){
                $join->on("a.status","=","sa.status");
            });

        $vars['countPatient'] = true;
        if(isset($vars['countPatient']) && $vars['countPatient']){
            $qPatient =
                DB::table("mcu_invoice_patient AS a")
                ->select(DB::raw("a.mcu_invoice_id, COUNT(1) AS patient_total, SUM(b.price_lab) AS price_lab_total, SUM(b.price_corporate) AS price_corporate_total"))
                ->leftJoin("mcu_event_patient AS b", function($join){
                    $join->on("a.mcu_event_patient_id","=","b.id");
                })
                ->whereRaw("a.delete_at IS NULL")
                ->groupBy("a.mcu_invoice_id");
            $db = 
                $db->addSelect(DB::raw("f.patient_total, f.price_lab_total, f.price_corporate_total"))
                ->leftJoin(DB::raw("(".$qPatient->toSQL().") AS f"), function($join){
                    $join->on("a.id","=","f.mcu_invoice_id");
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
                $query = $query->orWhereRaw("c.title LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("b.title LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.title LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.date LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("DATE_FORMAT(a.date, '%d/%m/%Y') LIKE '%".$vars['srcEvt']."%'");
            });
        }

        if(isset($vars['userCorporate'])){
            if(count($vars['userCorporate']) > 0){
                $db = $db->where(function($query) use ($vars){
                    foreach ($vars['userCorporate'] as $k => $v) {
                        $query = $query->orWhereRaw("a.corporate_id=".$v->id);
                    }
                });
            }else{
                $db = $db->whereRaw("a.id=-9");
            }   
        }
        if(isset($vars['userCorporateClient'])){
            if(count($vars['userCorporateClient']) > 0){
                $db = $db->where(function($query) use ($vars){
                    foreach ($vars['userCorporateClient'] as $k => $v) {
                        $query = $query->orWhereRaw("a.corporate_client_id=".$v->id);
                    }
                });
            }else{
                $db = $db->whereRaw("a.id=-9");
            }   
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
                $v->date_dmY_slash = $v->date;
                if(strtotime($v->date)){
                    $v->date_dmY_slash = date("d/m/Y", strtotime($v->date));
                }
                $v->date_from_dmY_slash = $v->date_from;
                if(strtotime($v->date_from)){
                    $v->date_from_dmY_slash = date("d/m/Y", strtotime($v->date_from));
                }
                $v->date_to_dmY_slash = $v->date_to;
                if(strtotime($v->date_to)){
                    $v->date_to_dmY_slash = date("d/m/Y", strtotime($v->date_to));
                }
                $v->trans_date = $v->date_from_dmY_slash . ' - ' . $v->date_to_dmY_slash;

                $v->corporateClientName = $v->corporate_name."</br>".$v->corporate_client_name;

                $v->patient_total_mod = number_format($v->patient_total,0,',','.');
                $v->price_lab_total_mod = number_format($v->price_lab_total,2,',','.');
                $v->price_corporate_total_mod = number_format($v->price_corporate_total,2,',','.');

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
