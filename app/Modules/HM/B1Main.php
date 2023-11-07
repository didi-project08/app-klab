<?php
namespace App\Modules\HM;

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
use App\Models\MCUOnsiteEvent;
use App\Models\MCUOnsiteEventPatient;
use App\Models\MCUFormat;
use App\Models\HMDevice;
use App\Models\HMSample;

class B1Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "HM/";
        $this->_vars["_id_"] = "M2";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b1";
        $this->_vars['url'] = URL::to('/')."/hm/";
        $this->_vars['urlb'] = $this->_vars['url']."b1/";
    }

    public function sample(Request $request, $id = null){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        $this->_vars['id'] = $id;
        $this->_vars['device'] = HMDevice::query($this->_vars, ["mode"=>"rowsAll"]);
        unset($this->_vars['id']);
        $this->_vars['device_id'] = $id;
        ModelBase::ShowPage($this->_viewPath.'v1Main', $this->_vars);
    }
    public function read(Request $request, $device_id = null){
        $userScope = ModelBase::m_user_scope();
        // if($userScope['info']->f_user_type == 2){
        //     $this->_vars['delete'] = -1;
        //     $this->_vars['userLaboratory'] = ModelBase::userLaboratory($this->_vars);
        // }else if($userScope['info']->f_user_type == 3){
        //     $this->_vars['delete'] = -1;
        //     $this->_vars['userCorporate'] = ModelBase::userCorporate($this->_vars);
        //     $this->_vars['userCorporateClient'] = ModelBase::userCorporateClient($this->_vars);
        // }
        // $this->_vars['delete'] = 0;
        // $this->_vars['orderBy'] = ['sa.order:asc','ST:asc','a.create_at:desc'];
        // $this->_vars['countPatient'] = true;
        // if($userScope['info']->f_user_type == 2){
        //     $this->_vars['statusList'] = ['sAccepted'];
        // }
        $this->_vars['device_id'] = $device_id;
        $result['rows'] = HMSample::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = HMSample::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
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

    
    public function add(){
        $userScope = ModelBase::m_user_scope();
        $this->_vars['userScope'] = $userScope;
        $this->_vars['mode'] = 'add';
        $this->_vars['url_save'] = $this->_vars['urlb']."create";
        return view($this->_viewPath.'v1Form', $this->_vars);
    }
    public function create(Request $request){
        $validator = Facades\Validator::make($request->all(), [
            'title' => ['required','string','max:200'],
            'date_from' => ['required','date_format:d/m/Y'],
            'date_to' => ['required','date_format:d/m/Y'],
        ],[
            'date_from.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
            'date_to.date_format' => "The :attribute does not match the format dd/mm/yyyy."
        ])->setAttributeNames([
            'title' => 'Event Title',
            'date_from' => 'Event From',
            'date_to' => 'Event To',
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
                $insert = $request->all(); 
                unset($insert['_token']);
                $insert['create_by'] = session()->get('sessUsername');
                $insert['create_at'] = $at;
                $insert['date_from'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['date_from'])));
                $insert['date_to'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['date_to'])));
                $insert['status'] = 'sProposed';
                $userScope = ModelBase::m_user_scope();
                // if($insert['owner'] == 2){
                //     $insert['status'] = 'sAccepted';
                //     $insert['confirm_by'] = session()->get('sessUsername');
                //     $insert['confirm_at'] = $at;
                // }else{
                //     $insert['status'] = 'sProposed';
                // }
                $insert['status'] = 'sProposed';
                DB::table('mcu_onsite_event')->insert($insert);

                DB::commit();
                $result['success'] = true;
                $result['message'] = "Create Success.";
            } catch(\Exception $ex){
                // throw $ex;
                DB::rollback();
                $result['success'] = false;
                $result['message'] = "Create Failed.";
            }
        }
        echo json_encode($result);
    }
    public function edit(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUOnsiteEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $userScope = ModelBase::m_user_scope();
            $allowEdit = false;
            // if($userScope['info']->f_user_type == $sel['data']->owner){
            //     $allowEdit = true;
            // }
            // if($userScope['info']->f_user_admin == 1){
            //     $allowEdit = true;
            // }
            $allowEdit = true;
            if($allowEdit){
                $this->_vars['userScope'] = $userScope;
                $this->_vars['mode'] = 'edit';
                $this->_vars['url_save'] = $this->_vars['urlb']."update/".$id;
                $this->_vars['selData'] = json_encode($sel['data']);
                return view($this->_viewPath.'v1Form', $this->_vars);
            }else{
                return "Sorry, You are not allow to Edit.";
            }
        }else{
            return $sel['message'];
        }
    }
    public function update(Request $request, $id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'title' => ['required','string','max:200'],
            'date_from' => ['required','date_format:d/m/Y'],
            'date_to' => ['required','date_format:d/m/Y'],
        ],[
            'date_from.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
            'date_to.date_format' => "The :attribute does not match the format dd/mm/yyyy."
        ])->setAttributeNames([
            'title' => 'Event Title',
            'date_from' => 'Event From',
            'date_to' => 'Event To',
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
                $update['update_by'] = session()->get('sessUsername');
                $update['update_at'] = $at;
                $update['date_from'] = date("Y-m-d", strtotime(str_replace("/","-",$update['date_from'])));
                $update['date_to'] = date("Y-m-d", strtotime(str_replace("/","-",$update['date_to'])));
                $where = [
                    "id"=>$id,
                ];
                DB::table('mcu_onsite_event')->where($where)->update($update);

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
    public function delete(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $this->_vars['countPatient'] = true;
        $sel = MCUOnsiteEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $eventData = $sel['data'];
            if($eventData->patient_total == $eventData->patient_scheduled){
                $userScope = ModelBase::m_user_scope();
                $allowDelete = false;
                if($userScope['info']->f_user_type == $sel['data']->owner){
                    $allowDelete = true;
                }
                if($userScope['info']->f_user_admin == 1){
                    $allowDelete = true;
                }
                if($allowDelete){
                    $at = date("Y-m-d H:i:s");
                    DB::beginTransaction();
                    try{
                        $update = [
                            "delete_by"=>session()->get('sessUsername'),
                            "delete_at"=>$at,
                        ];
                        $where = [
                            "id"=>$id,
                        ];
                        DB::table('mcu_onsite_event')->where($where)->update($update);

                        DB::table('mcu_onlsite_event_patient')
                        ->whereRaw("mcu_event_id=".$id." AND delete_at IS NULL")
                        ->update($update);
    
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
                    $result['message'] = "Sorry, You are not allow to Delete.";
                }
            }else{
                $result['success'] = false;
                $result['message'] = "Delete Failed, Some Patient on MCU Proces.";
            }
        }else{
            $result['success'] = false;
            $result['message'] = "Delete Failed, Event Not Found.";
        }
        echo json_encode($result);
    }
    public function confirm(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUOnsiteEvent::query($this->_vars, ["mode"=>"rowOne"]);
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
                DB::table('mcu_onsite_event')->where($where)->update($update);

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
