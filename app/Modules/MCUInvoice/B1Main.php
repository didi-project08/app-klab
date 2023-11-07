<?php
namespace App\Modules\MCUInvoice;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
// use Validator;
// use Illuminate\Support\Facades;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

use App\Modules\BASE\MY_Controller;
use App\Models\ModelBase;
use App\Helpers\Main AS MainHelper;

use App\Models\Laboratory;
use App\Models\LaboratoryRefer;
use App\Models\UserLaboratory;
use App\Models\Corporate;
use App\Models\CorporateEmp;
use App\Models\MCUInvoice;
use App\Models\MCUInvoicePatient;
use App\Models\MCUFormat;

class B1Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCUInvoice/";
        $this->_vars["_id_"] = "M7";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b1";
        $this->_vars['url'] = URL::to('/')."/mcu-invoice/";
        $this->_vars['urlb'] = $this->_vars['url']."b1/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        return view($this->_viewPath.'v1Main', $this->_vars);
    }
    public function filter(){
        return view($this->_viewPath.'v1Filter', $this->_vars);
    }
    public function readCorporateCombo(){
        $result = ModelBase::userCorporate($this->_vars);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }
    public function readCorporateClientCombo(){
        $result = ModelBase::userCorporateClient($this->_vars);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }
    public function readLaboratoryCombo(){
        $result = Laboratory::query($this->_vars, ["mode"=>"rowsAll"]);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }
    public function readMCUFormatCombo(){
        $this->_vars['orderBy'] = ['a.create_at:asc'];
        $result = MCUFormat::query($this->_vars, ["mode"=>"rowsAll"]);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }
    public function readCorporateEmp(){
        $result['rows'] = CorporateEmp::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = CorporateEmp::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }

    public function read(){
        $userScope = ModelBase::m_user_scope();
        $this->_vars['delete'] = -1;
        $this->_vars['userCorporate'] = ModelBase::userCorporate($this->_vars);
        $this->_vars['userCorporateClient'] = ModelBase::userCorporateClient($this->_vars);

        $this->_vars['delete'] = 0;
        $this->_vars['orderBy'] = ['sa.order:asc','a.create_at:desc'];
        // $this->_vars['countPatient'] = true;

        $result['rows'] = MCUInvoice::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = MCUInvoice::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function generate(){
        $db =
            DB::table("mcu_event AS a")
            ->select(DB::raw("b.presentat, a.corporate_id, a.corporate_client_id, YEAR(b.presentat) AS invoice_year, WEEK(b.presentat, 1) AS invoice_week, COUNT(1) AS patient_total, GROUP_CONCAT(b.id) AS patient_id_list, GROUP_CONCAT(b.mcu_event_id) AS event_id_list"))
            // ->addSelect(DB::raw("WEEK('2023-04-23', 1) AS week1, WEEK('2023-04-24', 1) AS week2"))
            ->join("mcu_event_patient AS b", function($join){
                $join->on("a.id","=","b.mcu_event_id");
            })
            ->whereRaw("a.delete_at IS NULL")
            ->whereRaw("b.delete_at IS NULL")
            ->whereRaw("(b.status = 'sPresented' OR b.status = 'sFinished')")
            ->whereRaw("(b.mcu_invoice_id = 0 OR b.mcu_invoice_id = '' OR b.mcu_invoice_id IS NULL)")
            ->whereRaw("WEEK(b.presentat, 1) < WEEK(CURDATE(), 1)")
            ->groupByRaw("a.corporate_id")
            ->groupByRaw("a.corporate_client_id")
            ->groupByRaw("YEAR(b.presentat)")
            ->groupByRaw("WEEK(b.presentat, 1)");
        $db = $db->get();
        // dd($db);
        $total = $db->count();
        if($total > 0){
            $at = date("Y-m-d H:i:s");
            foreach ($db as $k => $v) {
                $db = 
                    DB::table(DB::raw("mcu_invoice AS a"))
                    ->whereRaw("a.corporate_id='".$v->corporate_id."'")
                    ->whereRaw("a.corporate_client_id='".$v->corporate_client_id."'")
                    ->whereRaw("a.year='".$v->invoice_year."'")
                    ->whereRaw("a.week='".$v->invoice_week."'")
                    ->whereRaw("a.delete_at IS NULL")
                    ->limit(1);
                $db = $db->get();
                $total = count($db);
                if($total > 0){
                    $lastInsertId = $db[0]->id;
                }else{
                    $date_invoice = (new \DateTime())->setISODate($v->invoice_year, $v->invoice_week, 8)->format('Y-m-d');
                    $date_from = (new \DateTime())->setISODate($v->invoice_year, $v->invoice_week)->format('Y-m-d');
                    $date_to = (new \DateTime())->setISODate($v->invoice_year, $v->invoice_week, 7)->format('Y-m-d');

                    $insert = [
                        "corporate_id"=>$v->corporate_id,
                        "corporate_client_id"=>$v->corporate_client_id,
                        "year"=>$v->invoice_year,
                        "week"=>$v->invoice_week,
                        "date"=>$date_invoice,
                        "date_from"=>$date_from,
                        "date_to"=>$date_to,
                        "create_by"=>session()->get('sessUsername'),
                        "create_at"=>$at,
                        "status"=>'sCreated',
                        "status_by"=>session()->get('sessUsername'),
                        "status_at"=>$at
                    ];
                    DB::table('mcu_invoice')->insert($insert);
                    $lastInsertId = DB::getPdo()->lastInsertId();
                }
    
                $patient_id_list_array = explode(",", $v->patient_id_list);
                $event_id_list_array = explode(",", $v->event_id_list);
                foreach ($patient_id_list_array as $k2 => $v2) {
                    $insert = [
                        "mcu_invoice_id"=>$lastInsertId,
                        "mcu_event_id"=>$event_id_list_array[$k2],
                        "mcu_event_patient_id"=>$v2,
                        "create_by"=>session()->get('sessUsername'),
                        "create_at"=>$at
                    ];
                    DB::table('mcu_invoice_patient')->insert($insert);
    
                    $where = ["id"=>$v2];
                    $update = ["mcu_invoice_id"=>$lastInsertId];
                    DB::table('mcu_event_patient')->where($where)->update($update);
                }
            }
            $result['success'] = true;
            $result['message'] = "Generate Success.";
        }else{
            $result['success'] = false;
            $result['message'] = "Sorry, no data ready to generate.";
        }
        echo json_encode($result);
    }
    // public function add(){
    //     $userScope = ModelBase::m_user_scope();
    //     $this->_vars['userScope'] = $userScope;
    //     $this->_vars['mode'] = 'add';
    //     $this->_vars['url_save'] = $this->_vars['urlb']."create";
    //     return view($this->_viewPath.'v1Form', $this->_vars);
    // }
    // public function create(Request $request){
    //     $validator = Facades\Validator::make($request->all(), [
    //         'owner' => ['required','integer'],
    //         'corporate_id' => ['required','integer'],
    //         'corporate_client_id' => ['required','integer'],
    //         'laboratory_id' => ['required','integer'],
    //         'mcu_format_id' => ['required','integer'],
    //         'title' => ['required','string','max:200'],
    //         'date_from' => ['required','date_format:d/m/Y'],
    //         'date_to' => ['required','date_format:d/m/Y'],
    //     ],[
    //         'date_from.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
    //         'date_to.date_format' => "The :attribute does not match the format dd/mm/yyyy."
    //     ])->setAttributeNames([
    //         'owner' => 'Owner Data',
    //         'corporate_id' => 'Corporate',
    //         'corporate_client_id' => 'Client',
    //         'laboratory_id' => 'Provider',
    //         'mcu_format_id' => 'MCU Format',
    //         'title' => 'Event Title',
    //         'date_from' => 'Event From',
    //         'date_to' => 'Event To',
    //     ]);
    //     $validator->after(function (Validator $validator) use ($request) {
    //         // no addtional validation
    //     });

    //     if ($validator->fails()){
    //         $result['success'] = false;
    //         $result['message'] = "Form data is not valid.";
    //         $result['form_error'] = true;
    //         $result['form_error_array'] = $validator->errors();
    //     }else{
    //         $at = date("Y-m-d H:i:s");

    //         DB::beginTransaction();
    //         try{
    //             $insert = $request->all();
    //             unset($insert['_token']);
    //             $insert['create_by'] = session()->get('sessUsername');
    //             $insert['create_at'] = $at;
    //             $insert['date_from'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['date_from'])));
    //             $insert['date_to'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['date_to'])));
    //             $insert['status'] = 'sProposed';
    //             $userScope = ModelBase::m_user_scope();
    //             if($insert['owner'] == 2){
    //                 $insert['status'] = 'sAccepted';
    //                 $insert['confirm_by'] = session()->get('sessUsername');
    //                 $insert['confirm_at'] = $at;
    //             }else{
    //                 $insert['status'] = 'sProposed';
    //             }
    //             DB::table('mcu_event')->insert($insert);

    //             DB::commit();
    //             $result['success'] = true;
    //             $result['message'] = "Create Success.";
    //         } catch(\Exception $ex){
    //             // throw $ex;
    //             DB::rollback();
    //             $result['success'] = false;
    //             $result['message'] = "Create Failed.";
    //         }
    //     }
    //     echo json_encode($result);
    // }
    // public function edit(Request $request, $id = 0){
    //     $this->_vars['id'] = $id;
    //     $sel = MCUInvoice::query($this->_vars, ["mode"=>"rowOne"]);
    //     if($sel['success']){
    //         $userScope = ModelBase::m_user_scope();
    //         $allowEdit = false;
    //         if($userScope['info']->f_user_type == $sel['data']->owner){
    //             $allowEdit = true;
    //         }
    //         if($userScope['info']->f_user_admin == 1){
    //             $allowEdit = true;
    //         }
    //         if($allowEdit){
    //             $this->_vars['userScope'] = $userScope;
    //             $this->_vars['mode'] = 'edit';
    //             $this->_vars['url_save'] = $this->_vars['urlb']."update/".$id;
    //             $this->_vars['selData'] = json_encode($sel['data']);
    //             return view($this->_viewPath.'v1Form', $this->_vars);
    //         }else{
    //             return "Sorry, You are not allow to Edit.";
    //         }
    //     }else{
    //         return $sel['message'];
    //     }
    // }
    // public function update(Request $request, $id = 0){
    //     $validator = Facades\Validator::make($request->all(), [
    //         'title' => ['required','string','max:200'],
    //         'date_from' => ['required','date_format:d/m/Y'],
    //         'date_to' => ['required','date_format:d/m/Y'],
    //     ],[
    //         'date_from.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
    //         'date_to.date_format' => "The :attribute does not match the format dd/mm/yyyy."
    //     ])->setAttributeNames([
    //         'title' => 'Event Title',
    //         'date_from' => 'Event From',
    //         'date_to' => 'Event To',
    //     ]);
    //     $validator->after(function (Validator $validator) use ($request) {
    //         // no addtional validation
    //     });

    //     if ($validator->fails()){
    //         $result['success'] = false;
    //         $result['message'] = "Form data is not valid.";
    //         $result['form_error'] = true;
    //         $result['form_error_array'] = $validator->errors();
    //     }else{
    //         $at = date("Y-m-d H:i:s");
    //         DB::beginTransaction();
    //         try{
    //             $update = $request->all();
    //             unset($update['_token']);
    //             unset($update['_method']);
    //             $update['update_by'] = session()->get('sessUsername');
    //             $update['update_at'] = $at;
    //             $update['date_from'] = date("Y-m-d", strtotime(str_replace("/","-",$update['date_from'])));
    //             $update['date_to'] = date("Y-m-d", strtotime(str_replace("/","-",$update['date_to'])));
    //             $where = [
    //                 "id"=>$id,
    //             ];
    //             DB::table('mcu_event')->where($where)->update($update);

    //             DB::commit();
    //             $result['success'] = true;
    //             $result['message'] = "Update Success.";
    //         } catch(\Exception $ex){
    //             // throw $ex;
    //             DB::rollback();
    //             $result['success'] = false;
    //             $result['message'] = "Update Failed.";
    //         }
    //     }
    //     echo json_encode($result);
    // }
    public function delete(Request $request, $id = 0){
        $at = date("Y-m-d H:i:s");

        $qVars['id'] = $id;
        $sel = MCUInvoice::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $invoiceData = $sel['data'];
            if($invoiceData->status == "sCreated"){
                $qVars = [];
                $qVars['mcu_invoice_id'] = $id;
                $invoicePatient = MCUInvoicePatient::query($qVars, ["mode"=>"rowsAll"]);

                DB::beginTransaction();
                try{
                    $update = [
                        "delete_by"=>session()->get('sessUsername'),
                        "delete_at"=>$at,
                    ];
                    $where = [
                        "id"=>$id,
                    ];
                    DB::table('mcu_invoice')->where($where)->update($update);
                    DB::table('mcu_invoice_patient')->where($where)->update($update);

                    foreach ($invoicePatient as $k => $v) {
                        $update = [
                            "mcu_invoice_id"=>0,
                        ];
                        $where = [
                            "id"=>$v->id,
                        ];
                        DB::table('mcu_event_patient')->where($where)->update($update);
                    }
                    DB::commit();
                    $result['success'] = true;
                    $result['message'] = "Delete Success.";
                } catch(\Exception $ex){
                    dd($ex);
                    DB::rollback();
                    $result['success'] = false;
                    $result['message'] = "Delete Failed.";
                }
            }else{
                $result['success'] = false;
                $result['message'] = "Delete Failed, Invoice has been approved.";
            }
        }else{
            $result['success'] = false;
            $result['message'] = "Delete Failed, Event Not Found.";
        }
        echo json_encode($result);
    }
    public function approve(Request $request, $id = 0){
        $at = date("Y-m-d H:i:s");

        $qVars['id'] = $id;
        $sel = MCUInvoice::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            DB::beginTransaction();
            try{
                $update = [
                    "status"=>"sApproved",
                    "status_by"=>session()->get('sessUsername'),
                    "status_at"=>$at,
                    "approved_by"=>session()->get('sessUsername'),
                    "approved_at"=>$at,
                ];
                $where = [
                    "id"=>$id,
                ];
                DB::table('mcu_invoice')->where($where)->update($update);

                DB::commit();
                $result['success'] = true;
                $result['message'] = "Delete Success.";
            } catch(\Exception $ex){
                dd($ex);
                DB::rollback();
                $result['success'] = false;
                $result['message'] = "Delete Failed.";
            }
        }else{
            $result['success'] = false;
            $result['message'] = "Delete Failed, Event Not Found.";
        }
        echo json_encode($result);
    }
    public function confirm(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUInvoice::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            if($sel['data']->status == 'sProposed'){
                $this->_vars['url_save'] = $this->_vars['urlb']."confirmSave/".$id;
                $this->_vars['selData'] = json_encode($sel['data']);
                return view($this->_viewPath.'v1FormConfirm', $this->_vars);
            }else{
                return "Sorry, This data already Confirmed.";
            }
        }else{
            return $sel['message'];
        }
    }
    public function confirmSave(Request $request, $id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'status' => ['required','string'],
            'confirm_note' => ['required','string','max:200']
        ])->setAttributeNames([
            'status' => 'Status',
            'confirm_note' => 'Note'
        ]);
        $validator->after(function (Validator $validator) use ($request) {
            // no addtional validation
        });

        if ($validator->fails()){
            $result['success'] = false;
            $result['message'] = "Form data is not valid.";
            $result['form_error'] = true;
            $result['form_error_array'] = $validator->errors();
        }else{
            $at = date("Y-m-d H:i:s");

            DB::beginTransaction();
            try{
                $update = $request->all();
                unset($update['_token']);
                unset($update['_method']);
                $update['confirm_by'] = session()->get('sessUsername');
                $update['confirm_at'] = $at;
                $where = [
                    "id"=>$id,
                ];
                DB::table('mcu_event')->where($where)->update($update);

                DB::commit();
                $result['success'] = true;
                $result['message'] = "Update Success.";
            } catch(\Exception $ex){
                // throw $ex;
                DB::rollback();
                $result['success'] = false;
                $result['message'] = "Update Failed.";
            }
        }
        echo json_encode($result);
    }
}
