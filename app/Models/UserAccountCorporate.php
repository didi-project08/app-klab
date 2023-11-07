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

class UserAccountCorporate extends Model{
    use HasFactory;

    public static function query($vars = [], $params = []){
        $db =
            DB::table("tc_user AS a")
            ->select(DB::raw("a.*, b.f_role_name, c.corporate_id, ca.code AS corporate_code, ca.title AS corporate_name"))
            ->join("tc_role AS b", function($join){
                $join->on("a.f_role_id","=","b.f_role_id");
            })
            ->join("user_corporate AS c", function($join){
                $join->on("a.f_user_id","=","c.f_user_id");
            })
            ->join("corporate AS ca", function($join){
                $join->on("c.corporate_id","=","ca.id");
            })
            ->groupBy("a.f_user_id");
        
        if(isset($vars['delete'])){
            if($vars['delete'] == 0){
                $db = $db->whereRaw("a.f_delete_dt IS NULL");
            }else if($vars['delete'] == 1){
                $db = $db->whereRaw("a.f_delete_dt IS NOT NULL");
            }
        }else{
            $db = $db->whereRaw("a.f_delete_dt IS NULL");
        }
        
        if(isset($vars['f_user_id'])){
            $db = $db->whereRaw("a.f_user_id='".$vars['f_user_id']."'");
        }
        if(isset($vars['f_user_type'])){
            $db = $db->whereRaw("a.f_user_type='".$vars['f_user_type']."'");
        }
        if(isset($vars['userCorporate'])){
            if(count($vars['userCorporate']) > 0){
                $db = $db->where(function($query) use ($vars){
                    foreach ($vars['userCorporate'] as $k => $v) {
                        $query = $query->orWhereRaw("c.corporate_id=".$v->id);
                    }
                });
            }else{
                $db = $db->whereRaw("a.id=-9");
            }   
        }
        if(isset($vars['srcEvt'])){
            $db = $db->where(function($query) use ($vars){
                $query = $query->orWhereRaw("a.f_username LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.f_fullname LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.f_phone_number LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("a.f_email LIKE '%".$vars['srcEvt']."%'");
                $query = $query->orWhereRaw("ca.title LIKE '%".$vars['srcEvt']."%'");
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
            $db = $db->orderBy('a.f_entry_dt', 'desc');
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
                $v->pw = "********";
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
