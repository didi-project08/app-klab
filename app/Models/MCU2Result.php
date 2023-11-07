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

class MCU2Result extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("mcu2_result AS a");
        
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
        if(isset($vars['mcu2_event_id'])){
            $db = $db->whereRaw("a.mcu2_event_id='".$vars['mcu2_event_id']."'");
        }
        
        if(isset($vars['mcu2_patient_id'])){
            $db = $db->whereRaw("a.mcu2_patient_id='".$vars['mcu2_patient_id']."'");
        }
        if(isset($vars['patientIdList'])){
            if(count($vars['patientIdList']) > 0){
                $db = $db->whereIn('a.mcu2_patient_id', $vars['patientIdList']);
            }else{
                $db = $db->whereRaw("a.mcu2_patient_id=-9");
            }
        }

        if(isset($vars['unique'])){
            $db = $db->whereRaw("a.unique='".$vars['unique']."'");
        }
        if(isset($vars['uniqueList'])){
            if(count($vars['uniqueList']) > 0){
                $db = $db->whereIn('a.unique', $vars['uniqueList']);
            }else{
                $db = $db->whereRaw("a.unique=-9");
            }
        }

        if(isset($vars['ktp'])){
            $db = $db->whereRaw("a.ktp='".$vars['ktp']."'");
        }
        if(isset($vars['ktpList'])){
            if(count($vars['ktpList']) > 0){
                $db = $db->whereIn('a.ktp', $vars['ktpList']);
            }else{
                $db = $db->whereRaw("a.ktp=-9");
            }
        }

        if(isset($vars['emp_no'])){
            $db = $db->whereRaw("a.emp_no='".$vars['emp_no']."'");
        }
        if(isset($vars['empNoList'])){
            if(count($vars['empNoList']) > 0){
                $db = $db->whereIn('a.emp_no', $vars['empNoList']);
            }else{
                $db = $db->whereRaw("a.emp_no=-9");
            }
        }

        if(isset($vars['q'])){
            $vars['srcEvt'] = $vars['q'];
        }
        if(isset($vars['srcEvt'])){
            $db = $db->where(function($query) use ($vars){
                $query = $query->orWhereRaw("a.unique LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.ktp LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.emp_no LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.mcu_item_code LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.mcu_item_name LIKE '%".$vars['srcEvt']."%'");
            });
        }

        // if(isset($vars['orderBy'])){
        //     if(count($vars['orderBy']) > 0){
        //         foreach ($vars['orderBy'] as $k => $v) {
        //             if($e = explode(":",$v)){
        //                 $field = $e[0]; 
        //                 $order = $e[1];
        //                 $db = $db->orderBy($field, $order);
        //             }
        //         }
        //     }
        // }else{
        //     $db = $db->orderBy('a.create_at', 'desc');
        // }
        
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
        $row['field'] = "mcu2_event_id";
        $row['title'] = "Event ID";
        $row['width'] = 70;
        $row['align'] = "CENTER";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "mcu2_patient_id";
        $row['title'] = "Patient ID";
        $row['width'] = 70;
        $row['align'] = "CENTER";
        $fieldList[$row['field']] = $row;

        $row = [];
        $row['field'] = "unique";
        $row['title'] = "Unique Code";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "ktp";
        $row['title'] = "ID (KTP / Password)";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "emp_no";
        $row['title'] = "Employee Number";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;

        $row = [];
        $row['field'] = "mcu_item_id";
        $row['title'] = "Item ID";
        $row['width'] = 70;
        $row['align'] = "CENTER";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "mcu_item_code";
        $row['title'] = "Item Code";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "mcu_item_name";
        $row['title'] = "Item Name";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "value";
        $row['title'] = "Result Value";
        $row['width'] = 300;
        $row['align'] = "LEFT";
        $row['dataType'] = "STRING";
        $row['colSpan'] = 1;
        $fieldList[$row['field']] = $row;

        $row = [];
        $row['field'] = "status_name";
        $row['title'] = "Status";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "created_by";
        $row['title'] = "created By";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "STRING";
        $fieldList[$row['field']] = $row;
        $row = [];
        $row['field'] = "created_at";
        $row['title'] = "Created At";
        $row['width'] = 100;
        $row['align'] = "CENTER";
        $row['dataType'] = "DATE";
        $row['formatCell'] = "dd/mm/yyyy hh:mm:ss";
        $fieldList[$row['field']] = $row;

        if(count($fieldOrder) > 0){
            $fieldListNew = [];
            foreach ($fieldOrder as $k => $v) {
                
            }
        }else{
            return $fieldList;
        }
    }

    public static function converToCodeValue($resultData = []){
        $codeValue = [];
        foreach ($resultData as $k => $v) {
            $codeValue[$v->mcu_item_code] = $v->value;
        }
        return $codeValue;
    }
}
