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

class CorporateEmp extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("corporate_emp AS a")
            ->select(DB::raw("a.*, 
            b.title AS corporate_name, b.code AS corporate_code,
            ca.mcu_event_id, ca.schedule_date, ca.actual_date, ca.status, ca.status_at, ca.status_note,
            IF(ca.mcu_event_id IS NULL, 1, IF(ca.status = 'sCanceled' OR ca.status = 'sFinished', 1, 0)) AS allowRegistMCU,
            IF(ca.mcu_event_id IS NULL, SYSDATE(), IF(ca.status = 'sFinished', DATE_ADD(ca.status_at, INTERVAL 11 MONTH), IF(ca.status = 'sCanceled', ca.status_at, ''))) AS next_mcu_date"))
            ->join("corporate AS b", function($join){
                $join->on("a.corporate_id","=","b.id");
            })
            ->leftJoin("mcu_event_corporate_emp AS c", function($join){
                $join->on("a.id","=","c.corporate_emp_id");
            })->leftJoin("mcu_event_patient AS ca", function($join){
                $join->on("c.mcu_event_patient_id","=","ca.id");
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
        if(isset($vars['corporate_id'])){
            $db = $db->whereRaw("a.corporate_id='".$vars['corporate_id']."'");
        }
        if(isset($vars['q'])){
            $vars['srcEvt'] = $vars['q'];
        }
        if(isset($vars['srcEvt'])){
            $db = $db->where(function($query) use ($vars){
                $query = $query->orWhereRaw("b.code LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("b.title LIKE '%".$vars['srcEvt']."%'");
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
        if(isset($vars['corpEmpIdList'])){
            if(count($vars['corpEmpIdList']) > 0){
                $db = $db->whereIn('a.id', $vars['corpEmpIdList']);
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
                        if($field == 'allowRegistMCU'){
                            $db = $db->orderByRaw("IF(ca.mcu_event_id IS NULL, 1, IF(ca.status = 'sCanceled' OR ca.status = 'sFinished', 1, 0)) ".$order);
                        }if($field == 'nextMCU'){
                            $db = $db->orderByRaw("IF(ca.status = 'sFinished' OR ca.status = 'sCanceled' OR ca.status IS NULL, 0, 1) ".$order);
                            $db = $db->orderByRaw("IF(ca.mcu_event_id IS NULL, SYSDATE(), IF(ca.status = 'sFinished', DATE_ADD(ca.status_at, INTERVAL 11 MONTH), IF(ca.status = 'sCanceled', ca.status_at, ''))) ".$order);
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
                $v->dob_dmY_slash = $v->dob;
                if(strtotime($v->dob)){
                    $v->dob_dmY_slash = date("d/m/Y", strtotime($v->dob));
                }
                
                $v->eventnum = "EV" . str_pad($v->mcu_event_id, 4, '0', STR_PAD_LEFT);
                if($v->mcu_event_id == null){
                    $v->eventnum = "";
                }
                // $v->allowRegistMCU = 0;
                // if($v->mcu_event_id == null){
                //     $v->allowRegistMCU = 1;
                // }else{
                //     if($v->status == 'sCanceled' || $v->status == 'sFinished'){
                //         $v->allowRegistMCU = 1;
                //     }
                // }
                $v->allowRegistMCUColor = 'red';
                if($v->allowRegistMCU == 1){
                    $v->allowRegistMCUColor = 'blue';
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

    public static function excelField($fieldOrder = []){
        $fieldList = [];
        $row = [];
        $row['field'] = "id";
        $row['title'] = "ID";
        $row['width'] = 70;
        $row['align'] = "CENTER";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "corporate_code";
        $row['title'] = "Corp. Code";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "corporate_name";
        $row['title'] = "Corp. Name";
        $row['width'] = 200;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
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
        $row['style'] = [
            "fill" => [
                "color" => array('rgb' => 'FF0000')
            ]
        ];
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "gender";
        $row['title'] = "Gender (M/F)";
        $row['width'] = 60;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $row['style'] = [
            "fill" => [
                "color" => array('rgb' => 'FF0000')
            ]
        ];
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "dob";
        $row['title'] = "DOB (dd/mm/yyyy)";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "DATE";
        $row['formatCell'] = "dd/mm/yyyy";
        $row['phpFormula'] = "";
        $row['style'] = [
            "fill" => [
                "color" => array('rgb' => 'FF0000')
            ]
        ];
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "phone";
        $row['title'] = "Phone (WA)";
        $row['width'] = 140;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['style'] = [
            "fill" => [
                "color" => array('rgb' => 'FF0000')
            ]
        ];
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

        if(count($fieldOrder) > 0){
            $fieldListNew = [];
            foreach ($fieldOrder as $k => $v) {
                
            }
        }else{
            return $fieldList;
        }
    }
}
