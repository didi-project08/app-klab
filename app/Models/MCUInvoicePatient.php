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

class MCUInvoicePatient extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("mcu_invoice_patient AS z")
            ->select(DB::raw("a.*, 
            b.title AS mcu_event_title,
            ba.code AS corporate_name, ba.title AS corporate_name,
            bb.title AS client_name,
            bc.code AS laboratory_code, bb.title AS laboratory_name,
            bd.title AS mcu_format_name,
            c.title AS mcu_format_package_name,
            sa.name AS status_name, sa.color AS status_color"))
            ->join("mcu_event_patient AS a", function($join){
                $join->on("z.mcu_event_patient_id","=","a.id");
            })
            ->join("mcu_event AS b", function($join){
                $join->on("a.mcu_event_id","=","b.id");
            })
            ->leftJoin("corporate AS ba", function($join){
                $join->on("b.corporate_id","=","ba.id");
            })
            ->leftJoin("corporate_client AS bb", function($join){
                $join->on("b.corporate_client_id","=","bb.id");
            })
            ->leftJoin("laboratory AS bc", function($join){
                $join->on("b.laboratory_id","=","bc.id");
            })
            ->leftJoin("mcu_format AS bd", function($join){
                $join->on("b.mcu_format_id","=","bd.id");
            })
            ->leftJoin("mcu_format_package AS c", function($join){
                $join->on("a.mcu_format_package_id","=","c.id");
            })
            ->leftJoin(DB::raw("(SELECT * FROM status_all WHERE tbl='mcu_event_patient') AS sa"), function($join){
                $join->on("a.status","=","sa.status");
            });
        
        if(isset($vars['delete'])){
            if($vars['delete'] == 0){
                $db = $db->whereRaw("z.delete_at IS NULL");
            }else if($vars['delete'] == 1){
                $db = $db->whereRaw("z.delete_at IS NOT NULL");
            }
        }else{
            $db = $db->whereRaw("z.delete_at IS NULL");
        }
        if(isset($vars['id'])){
            $db = $db->whereRaw("z.id='".$vars['id']."'");
        }
        if(isset($vars['mcu_invoice_id'])){
            $db = $db->whereRaw("z.mcu_invoice_id='".$vars['mcu_invoice_id']."'");
        }
        if(isset($vars['mcu_event_id'])){
            $db = $db->whereRaw("a.mcu_event_id='".$vars['mcu_event_id']."'");
        }
        if(isset($vars['corporate_emp_id'])){
            $db = $db->whereRaw("a.corporate_emp_id='".$vars['corporate_emp_id']."'");
        }
        if(isset($vars['patientIdList'])){
            if(count($vars['patientIdList']) > 0){
                $db = $db->whereIn('a.id', $vars['patientIdList']);
            }else{
                $db = $db->whereRaw("a.id=-9");
            }
        }
        if(isset($vars['status'])){
            $db = $db->whereRaw("a.status='".$vars['status']."'");
        }
        if(isset($vars['srcEvt'])){
            $db = $db->where(function($query) use ($vars){
                $query = $query->orWhereRaw("a.emp_no LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.nik LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.name LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.gender LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.dob LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.phone LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.address LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.area LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.division LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.position LIKE '%".$vars['srcEvt']."%'");
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
                $v->eventnum = "EV" . str_pad($v->mcu_event_id, 4, '0', STR_PAD_LEFT);

                $v->type_name = $v->type;
                if($v->type == 0){
                    $v->type_name = "PERSONAL";
                }else if($v->type == 1){
                    $v->type_name = "CORPORATE";
                }
                $v->dob_dmY_slash = $v->dob;
                if(strtotime($v->dob)){
                    $v->dob_dmY_slash = date("d/m/Y", strtotime($v->dob));
                }
                $v->schedule_date_dmY_slash = $v->schedule_date;
                if(strtotime($v->schedule_date)){
                    $v->schedule_date_dmY_slash = date("d/m/Y", strtotime($v->schedule_date));
                }
                $v->actual_date_dmY_slash = $v->actual_date;
                if(strtotime($v->actual_date)){
                    $v->actual_date_dmY_slash = date("d/m/Y", strtotime($v->actual_date));
                }
                $v->regnumprovFull = $v->regnumprov;
                if(is_numeric($v->regnumprov)){
                    $v->regnumprovFull = $v->laboratory_code . "-" . date("ymd", strtotime($v->create_at)) . str_pad($v->regnumprov, 4, '0', STR_PAD_LEFT);
                }
                $v->presentnumFull = $v->presentnum;
                if(is_numeric($v->presentnum)){
                    $v->presentnumFull = 'EV' . str_pad($v->mcu_event_id, 4, '0', STR_PAD_LEFT) . "-" . date("ymd", strtotime($v->presentat)) . str_pad($v->presentnum, 4, '0', STR_PAD_LEFT);
                }

                $v->price_lab_mod = number_format($v->price_lab,2,',','.');
                $v->price_corporate_mod = number_format($v->price_corporate,2,',','.');
                
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
        $row['field'] = "schedule_date";
        $row['title'] = "Schedule";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "DATE";
        $row['formatCell'] = "dd/mm/yyyy";
        $row['phpFormula'] = "";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "actual_date";
        $row['title'] = "Actual / Present";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "DATE";
        $row['formatCell'] = "dd/mm/yyyy";
        $row['phpFormula'] = "";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "presentnumFull";
        $row['title'] = "Present Number";
        $row['width'] = 160;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "mcu_format_package_name";
        $row['title'] = "MCU Package";
        $row['width'] = 120;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "emp_no";
        $row['title'] = "Emp. Number";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "nik";
        $row['title'] = "ID (NIK / Passpoer)";
        $row['width'] = 130;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "name";
        $row['title'] = "Fullname";
        $row['width'] = 200;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "gender";
        $row['title'] = "Gender";
        $row['width'] = 60;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "dob";
        $row['title'] = "DOB";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "DATE";
        $row['formatCell'] = "dd/mm/yyyy";
        $row['phpFormula'] = "";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "phone";
        $row['title'] = "Phone";
        $row['width'] = 140;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "address";
        $row['title'] = "Address";
        $row['width'] = 200;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['wrap'] = true;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "area";
        $row['title'] = "Area";
        $row['width'] = 140;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "division";
        $row['title'] = "Division";
        $row['width'] = 140;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "position";
        $row['title'] = "Position";
        $row['width'] = 140;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "status_name";
        $row['title'] = "Status";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "status_by";
        $row['title'] = "Status By";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "status_at";
        $row['title'] = "Status At";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "DATE";
        $row['formatCell'] = "dd/mm/yyyy hh:mm:ss";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "status_note";
        $row['title'] = "Status Note";
        $row['width'] = 200;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['wrap'] = true;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "fit_cate";
        $row['title'] = "Fit Status";
        $row['width'] = 160;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "fit_note";
        $row['title'] = "Fit Note";
        $row['width'] = 300;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['wrap'] = true;
        $fieldList[$row['field']] = $row;

        if(count($fieldOrder) > 0){
            $fieldListNew = [];
            foreach ($fieldOrder as $k => $v) {
                
            }
        }else{
            return $fieldList;
        }
    }

    // public static function genRegNumProv($mcu_event_id){
    //     $num = 0;
    //     $db =
    //         DB::table("mcu_event AS a")
    //         ->select(DB::raw("a.laboratory_id"))
    //         ->whereRaw("a.id=".$mcu_event_id);
    //     $db = $db->get();
    //     $laboratory_id = $db[0]->laboratory_id;
    //     $db =
    //         DB::table("mcu_event AS a")
    //         ->select(DB::raw("b.regnumprov"))
    //         ->join("mcu_event_patient AS b", function($join){
    //             $join->on("a.id","=","b.mcu_event_id");
    //         })
    //         ->whereRaw("a.laboratory_id=".$laboratory_id)
    //         ->whereRaw("b.create_at>='".date("Y-m-d")." 00:00:00'")
    //         ->whereRaw("b.create_at<='".date("Y-m-d")." 23:59:59'")
    //         ->orderBy("b.regnumprov","desc")
    //         ->limit(1);
    //     $db = $db->get();
    //     $total = count($db);
    //     if($total > 0){
    //         if(is_numeric($db[0]->regnumprov)){
    //             $num = $db[0]->regnumprov * 1;
    //         }
    //     }
    //     $num = $num + 1;
    //     return $num;
    // }
    // public static function genRegNumRef($mcu_event_id){
    //     $num = 0;
    //     $db =
    //         DB::table("mcu_event AS a")
    //         ->select(DB::raw("a.laboratory_id_refer"))
    //         ->whereRaw("a.id=".$mcu_event_id);
    //     $db = $db->get();
    //     $laboratory_id_refer = $db[0]->laboratory_id_refer;
    //     $db =
    //         DB::table("mcu_event AS a")
    //         ->select(DB::raw("b.regnumref"))
    //         ->join("mcu_event_patient AS b", function($join){
    //             $join->on("a.id","=","b.mcu_event_id");
    //         })
    //         ->whereRaw("a.laboratory_id_refer=".$laboratory_id_refer)
    //         ->whereRaw("b.create_at>='".date("Y-m-d")." 00:00:00'")
    //         ->whereRaw("b.create_at<='".date("Y-m-d")." 23:59:59'")
    //         ->orderBy("b.regnumref","desc")
    //         ->limit(1);
    //     $db = $db->get();
    //     $total = count($db);
    //     if($total > 0){
    //         if(is_numeric($db[0]->regnumref)){
    //             $num = $db[0]->regnumref * 1;
    //         }
    //     }
    //     $num = $num + 1;
    //     return $num;
    // }
    // public static function genPresentNum($mcu_event_id){
    //     $num = 0;
    //     $db =
    //         DB::table("mcu_event_patient AS a")
    //         ->select(DB::raw("a.presentnum"))
    //         ->whereRaw("a.mcu_event_id=".$mcu_event_id)
    //         ->whereRaw("a.presentat>='".date("Y-m-d")." 00:00:00'")
    //         ->whereRaw("a.presentat<='".date("Y-m-d")." 23:59:59'")
    //         ->orderBy("a.presentnum","desc")
    //         ->limit(1);
    //     $db = $db->get();
    //     $total = count($db);
    //     if($total > 0){
    //         if(is_numeric($db[0]->presentnum)){
    //             $num = $db[0]->presentnum * 1;
    //         }
    //     }
    //     $num = $num + 1;
    //     return $num;
    // }
}
