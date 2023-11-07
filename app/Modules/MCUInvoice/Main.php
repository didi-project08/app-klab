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
use App\Models\MCUEvent;
use App\Models\MCUEventPatient;
use App\Models\MCUFormatPackage;

class Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCUInvoice/";
        $this->_vars["_id_"] = "M7";
        $this->_vars['url'] = URL::to('/')."/mcu-invoice/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        ModelBase::ShowPage($this->_viewPath.'vMain', $this->_vars);
    }
    public function readFormatPackageCombo(){
        $this->_vars['orderBy'] = ['a.title:asc'];
        $result = MCUFormatPackage::query($this->_vars, ["mode"=>"rowsAll"]);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }




    public function filter(){
        return view($this->_viewPath.'vFilter', $this->_vars);
    }
    public function laboratoryCombo(){
        $userScope = ModelBase::m_user_scope();
        if($userScope['info']->f_user_admin == 0){
            $this->_vars['f_user_id'] = $userScope['info']->f_user_id;
        }
        $result = UserLaboratory::query($this->_vars, ["mode"=>"rowsAll"]);
        echo json_encode($result);
    }
    public function laboratoryReferCombo(){
        $result = LaboratoryRefer::query($this->_vars, ["mode"=>"rowsAll"]);
        echo json_encode($result);
    }
    public function corporateCombo(){
        $result = Corporate::query($this->_vars, ["mode"=>"rowsAll"]);
        echo json_encode($result);
    }
    public function readCorporateEmp(){
        $result['rows'] = CorporateEmp::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = CorporateEmp::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }


    private function userLaboratory(){
        $userScope = ModelBase::m_user_scope();
        $vars = [];
        if($userScope['info']->f_user_admin == 0){
            $vars['f_user_id'] = $userScope['info']->f_user_id;
            $this->_vars['userLaboratory'] = UserLaboratory::query($vars, ["mode"=>"rowsAll"]);
        }else{
            $this->_vars['userLaboratory'] = UserLaboratory::query($vars, ["mode"=>"rowsAll"]);
        }
    }

    public function read(){
        self::userLaboratory();
        $this->_vars['orderBy'] = ['sa.order:asc','a.create_at:desc'];
        $result['rows'] = MCUEvent::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = MCUEvent::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }

    public function add(){
        $this->_vars['mode'] = 'add';
        $this->_vars['url_save'] = $this->_vars['url']."create";
        return view($this->_viewPath.'vForm', $this->_vars);
    }
    public function create(Request $request){
        $validator = Facades\Validator::make($request->all(), [
            'laboratory_id' => ['required','integer'],
            'laboratory_id_refer' => ['required','integer'],
            'title' => ['required','string','max:200'],
            'date_from' => ['required','date_format:d/m/Y'],
            'date_to' => ['required','date_format:d/m/Y'],
        ],[
            'date_from.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
            'date_to.date_format' => "The :attribute does not match the format dd/mm/yyyy."
        ])->setAttributeNames([
            'laboratory_id' => 'Provider',
            'laboratory_id_refer' => 'Refer-To',
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

                if($insert['laboratory_id'] == $insert['laboratory_id_refer']){
                    $insert['status'] = 'sAccepted';
                    $insert['confirm_by'] = session()->get('sessUsername');
                    $insert['confirm_at'] = $at;
                }else{
                    $insert['status'] = 'sProposed';
                }

                DB::table('mcu_event')->insert($insert);

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
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            self::userLaboratory();
            $userLaboratory = $this->_vars['userLaboratory'];
            $allowEdit = false;
            foreach ($userLaboratory as $k => $v) {
                if($v->id == $sel['data']->laboratory_id){
                    $allowEdit = true;
                    break;
                }
            }
            if($allowEdit){
                $this->_vars['mode'] = 'edit';
                $this->_vars['url_save'] = $this->_vars['url']."update/".$id;
                $this->_vars['selData'] = json_encode($sel['data']);
                return view($this->_viewPath.'vForm', $this->_vars);
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
    public function delete(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            self::userLaboratory();
            $userLaboratory = $this->_vars['userLaboratory'];
            $allowDelete = false;
            foreach ($userLaboratory as $k => $v) {
                if($v->id == $sel['data']->laboratory_id){
                    $allowDelete = true;
                    break;
                }
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
                    DB::table('mcu_event')->where($where)->update($update);

                    DB::commit();
                    $result['success'] = true;
                    $result['message'] = "Delete Success.";
                } catch(\Exception $ex){
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
            $result['message'] = "Delete Failed, Event Not Found.";
        }
        echo json_encode($result);
    }
    public function confirm(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            if($sel['data']->status == 'sProposed'){
                self::userLaboratory();
                $userLaboratory = $this->_vars['userLaboratory'];
                $allowConfirm = false;
                foreach ($userLaboratory as $k => $v) {
                    if($v->id == $sel['data']->laboratory_id_refer){
                        $allowConfirm = true;
                        break;
                    }
                }
                if($allowConfirm){
                    $this->_vars['url_save'] = $this->_vars['url']."confirmSave/".$id;
                    $this->_vars['selData'] = json_encode($sel['data']);
                    return view($this->_viewPath.'vFormConfirm', $this->_vars);
                }else{
                    return "Sorry, You are not allow to Confirm.";
                }
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


    public function b2Index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        return view($this->_viewPath.'v2Main', $this->_vars);
    }
    public function b2Filter(){
        return view($this->_viewPath.'v2Filter', $this->_vars);
    }
    public function b2Read(){
        $this->_vars['orderBy'] = ['sa.order:asc','a.create_at:desc'];
        $result['rows'] = MCUEventPatient::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = MCUEventPatient::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function b2Add(Request $request, $mcu_event_id = 0){
        $this->_vars['id'] = $mcu_event_id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'add';
            $this->_vars['url_save'] = $this->_vars['url']."b2Create/".$mcu_event_id;
            $this->_vars['eventData'] = $sel['data'];
            return view($this->_viewPath.'v2Form', $this->_vars);
        }else{
            return $sel['message'];
        }
    }
    public function b2Create(Request $request, $mcu_event_id = 0){
        $this->_vars['id'] = $mcu_event_id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $validatorList = [
                'type' => ['required','integer'],
                'corporate_emp_id' => ['nullable','integer'],
                'emp_no' => ['nullable','string','max:20'],
                'area' => ['nullable','string','max:50'],
                'division' => ['nullable','string','max:50'],
                'position' => ['nullable','string','max:50'],
                'nik' => ['nullable','string','max:20'],
                'name' => ['required','string','max:100'],
                'gender' => ['required',Rule::in(['M','F'])],
                'dob' => ['required','date_format:d/m/Y'],
                'phone' => ['nullable','string','max:30'],
                'address' => ['nullable','string','max:200'],
                'schedule_date' => ['required','date_format:d/m/Y'],
                'actual_date' => ['required','date_format:d/m/Y']
            ];
            if($request->all()['type'] == 1){
                $validatorList['corporate_id'] = ['required','integer'];
            }
            $validator = Facades\Validator::make($request->all(), $validatorList,[
                'type.integer' => "Please choose a valid :attribute",
                'corporate_id.integer' => "Please choose a valid :attribute",
                'corporate_emp_id.integer' => "Please choose a valid :attribute or make it empty.",
                'gender.in' => "Please choose a valid :attribute",
                'dob.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
                'schedule_date.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
                'actual_date.date_format' => "The :attribute does not match the format dd/mm/yyyy."
            ])->setAttributeNames([
                'type' => 'Patient Type',
                'corporate_id' => 'Corporate',
                'corporate_emp_id' => 'Corporate Employee',
                'emp_no' => 'Emp. Number',
                'area' => 'Job Area',
                'division' => 'Job Division',
                'position' => 'Job Position',
                'nik' => 'ID (NIK / Passport)',
                'name' => 'Fullname',
                'dob' => 'Date of Birth',
                'gender' => 'Gender',
                'phone' => 'Phone Number',
                'address' => 'Address',
                'schedule_date' => 'Schedule Date',
                'actual_date' => 'Actual Date',
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
                    $insert['dob'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['dob'])));
                    $insert['schedule_date'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['schedule_date'])));
                    $insert['actual_date'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['actual_date'])));
                    $insert['mcu_event_id'] = $mcu_event_id;
                    $insert['status'] = "sScheduled";
                    $insert['status_by'] = session()->get('sessUsername');
                    $insert['status_at'] = $at;
                    $insert['status_note'] = "New Patient";
                    $insert['regnumprov'] = MCUEventPatient::genRegNumProv($insert['mcu_event_id']);
                    $insert['regnumref'] = MCUEventPatient::genRegNumRef($insert['mcu_event_id']);
                    DB::table('mcu_event_patient')->insert($insert);
                    $lastInsertId = DB::getPdo()->lastInsertId();

                    $insert = [];
                    $insert['mcu_event_patient_id'] = $lastInsertId;
                    $insert['status'] = "sScheduled";
                    $insert['status_by'] = session()->get('sessUsername');
                    $insert['status_at'] = $at;
                    $insert['status_note'] = "New Patient";
                    DB::table('mcu_event_patient_status')->insert($insert);

                    DB::commit();
                    $result['success'] = true;
                    $result['message'] = "Create Success.";
                } catch(\Exception $ex){
                    throw $ex;
                    DB::rollback();
                    $result['success'] = false;
                    $result['message'] = "Create Failed.";
                }
            }
        }else{
            DB::rollback();
            $result['success'] = false;
            $result['message'] = $sel['message'];
        }
        echo json_encode($result);
    }
    public function b2Edit(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUEventPatient::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['id'] = $sel['data']->mcu_event_id;
            $sel2 = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
            if($sel2['success']){
                $this->_vars['mode'] = 'edit';
                $this->_vars['url_save'] = $this->_vars['url']."b2Update/".$id;
                $this->_vars['selData'] = json_encode($sel['data']);
                $this->_vars['eventData'] = $sel2['data'];
                return view($this->_viewPath.'v2Form', $this->_vars);
            }else{
                return "Event : ".$sel['message'];
            }
        }else{
            return "Event Patient : ".$sel['message'];
        }
    }
    public function b2Update(Request $request, $id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'emp_no' => ['nullable','string','max:20'],
            'area' => ['nullable','string','max:50'],
            'division' => ['nullable','string','max:50'],
            'position' => ['nullable','string','max:50'],
            'nik' => ['nullable','string','max:20'],
            'name' => ['required','string','max:100'],
            'gender' => ['required','string','max:1'],
            'dob' => ['required','date_format:d/m/Y'],
            'phone' => ['nullable','string','max:30'],
            'address' => ['nullable','string','max:200'],
            'schedule_date' => ['required','date_format:d/m/Y'],
            'actual_date' => ['required','date_format:d/m/Y']
        ],[
            'dob.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
            'schedule_date.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
            'actual_date.date_format' => "The :attribute does not match the format dd/mm/yyyy."
        ])->setAttributeNames([
            'emp_no' => 'Emp. Number',
            'area' => 'Job Area',
            'division' => 'Job Division',
            'position' => 'Job Position',
            'nik' => 'ID (NIK / Passport)',
            'name' => 'Fullname',
            'dob' => 'Date of Birth',
            'gender' => 'Gender',
            'phone' => 'Phone Number',
            'address' => 'Address',
            'schedule_date' => 'Schedule Date',
            'actual_date' => 'Actual Date'
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
                $update['dob'] = date("Y-m-d", strtotime(str_replace("/","-",$update['dob'])));
                $update['schedule_date'] = date("Y-m-d", strtotime(str_replace("/","-",$update['schedule_date'])));
                $update['actual_date'] = date("Y-m-d", strtotime(str_replace("/","-",$update['actual_date'])));
                $where = [
                    "id"=>$id,
                ];
                DB::table('mcu_event_patient')->where($where)->update($update);

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
    public function b2Present(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUEventPatient::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            if($sel['data']->status == 'sScheduled'){
                $this->_vars['id'] = $sel['data']->mcu_event_id;
                $sel2 = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
                if($sel2['success']){
                    $this->_vars['mode'] = 'present';
                    $this->_vars['url_save'] = $this->_vars['url']."b2PresentSave/".$id;
                    $this->_vars['selData'] = json_encode($sel['data']);
                    $this->_vars['eventData'] = $sel2['data'];
                    return view($this->_viewPath.'v2Form', $this->_vars);
                }else{
                    return "Event : ".$sel['message'];
                }
            }else{
                return "Event Patient : This data already Presented.";
            }
        }else{
            return "Event Patient : ".$sel['message'];
        }
    }
    public function b2PresentSave(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUEventPatient::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $validator = Facades\Validator::make($request->all(), [
                'emp_no' => ['nullable','string','max:20'],
                'area' => ['nullable','string','max:50'],
                'division' => ['nullable','string','max:50'],
                'position' => ['nullable','string','max:50'],
                'nik' => ['nullable','string','max:20'],
                'name' => ['required','string','max:100'],
                'gender' => ['required','string','max:1'],
                'dob' => ['required','date_format:d/m/Y'],
                'phone' => ['nullable','string','max:30'],
                'address' => ['nullable','string','max:200'],
                'schedule_date' => ['required','date_format:d/m/Y'],
                'actual_date' => ['required','date_format:d/m/Y']
            ],[
                'dob.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
                'schedule_date.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
                'actual_date.date_format' => "The :attribute does not match the format dd/mm/yyyy."
            ])->setAttributeNames([
                'emp_no' => 'Emp. Number',
                'area' => 'Job Area',
                'division' => 'Job Division',
                'position' => 'Job Position',
                'nik' => 'ID (NIK / Passport)',
                'name' => 'Fullname',
                'dob' => 'Date of Birth',
                'gender' => 'Gender',
                'phone' => 'Phone Number',
                'address' => 'Address',
                'schedule_date' => 'Schedule Date',
                'actual_date' => 'Actual Date'
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
                    $update['dob'] = date("Y-m-d", strtotime(str_replace("/","-",$update['dob'])));
                    $update['schedule_date'] = date("Y-m-d", strtotime(str_replace("/","-",$update['schedule_date'])));
                    $update['actual_date'] = date("Y-m-d", strtotime(str_replace("/","-",$update['actual_date'])));
                    $update['status'] = "sPresented";
                    $update['status_by'] = session()->get('sessUsername');
                    $update['status_at'] = $at;
                    $update['status_note'] = "Presented";
                    $update['presentat'] = $at;
                    $update['presentnum'] = MCUEventPatient::genPresentNum($sel['data']->mcu_event_id);
                    $where = [
                        "id"=>$id,
                    ];
                    DB::table('mcu_event_patient')->where($where)->update($update);
    
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
        }else{
            $result['success'] = false;
            $result['message'] = "Event Not Found.";
        }

        

        echo json_encode($result);
    }
}
