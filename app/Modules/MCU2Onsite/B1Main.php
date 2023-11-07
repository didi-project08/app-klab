<?php
namespace App\Modules\MCU2Onsite;

use Storage;
use Yaza\LaravelGoogleDriveStorage\Gdrive;

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

// use App\Models\Laboratory;
// use App\Models\LaboratoryRefer;
// use App\Models\UserLaboratory;
// use App\Models\Corporate;
// use App\Models\CorporateEmp;

use App\Models\MCU2Event;
use App\Models\MCU2Format;

class B1Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCU2Onsite/";
        $this->_vars["_id_"] = "M31";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b1";
        $this->_vars['url'] = URL::to('/')."/mcu2-onsite/";
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
    // public function readLaboratoryCombo(){
    //     $result = Laboratory::query($this->_vars, ["mode"=>"rowsAll"]);
    //     if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
    //         if(count($result) > 0){
    //             $result[0]->selected = true;
    //         }
    //     }
    //     echo json_encode($result);
    // }
    public function readMCUFormatCombo(){
        $this->_vars['orderBy'] = ['a.create_at:asc'];
        $result = MCU2Format::query($this->_vars, ["mode"=>"rowsAll"]);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }
    // public function readCorporateEmp(){
    //     $result['rows'] = CorporateEmp::query($this->_vars, ["mode"=>"rows"]);
    //     $result['total'] = CorporateEmp::query($this->_vars, ["mode"=>"total"]);
    //     echo json_encode($result);
    // }

    public function read(){
        $userScope = ModelBase::m_user_scope();
        // if($userScope['info']->f_user_type == 2){
        //     $this->_vars['delete'] = -1;
        //     $this->_vars['userLaboratory'] = ModelBase::userLaboratory($this->_vars);
        // }else if($userScope['info']->f_user_type == 3){
        //     $this->_vars['delete'] = -1;
        //     $this->_vars['userCorporate'] = ModelBase::userCorporate($this->_vars);
        //     $this->_vars['userCorporateClient'] = ModelBase::userCorporateClient($this->_vars);
        // }
        $this->_vars['delete'] = 0;
        $this->_vars['orderBy'] = ['sa.order:asc','ST:asc','a.create_at:desc'];
        $this->_vars['countPatient'] = true;
        // if($userScope['info']->f_user_type == 2){
        //     $this->_vars['statusList'] = ['sAccepted'];
        // }
        $result['rows'] = MCU2Event::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = MCU2Event::query($this->_vars, ["mode"=>"total"]);
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
            'client_id' => ['required','integer'],
            'mcu2_format_id' => ['required','integer'],
            'title' => ['required','string','max:100'],
            'desc' => ['nullable','string','max:200'],
            'date_from' => ['required','date_format:d/m/Y'],
            'date_to' => ['required','date_format:d/m/Y'],
        ],[
            'date_from.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
            'date_to.date_format' => "The :attribute does not match the format dd/mm/yyyy."
        ])->setAttributeNames([
            'client_id' => 'Client',
            'mcu2_format_id' => 'MCU Format',
            'title' => 'Event Title',
            'desc' => 'Description',
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
                $insert['uuid'] = MainHelper::uuid();
                $insert['date_from'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['date_from'])));
                $insert['date_to'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['date_to'])));
                DB::table('mcu2_event')->insert($insert);
                
                $insertId = DB::getPdo()->lastInsertId();
                if(env('SYS_CONNECTION') == 'ONLINE'){
                    Storage::disk('google')->makeDirectory($insertId);
                }else{
                    Storage::disk('local')->makeDirectory($insertId);
                }

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
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $userScope = ModelBase::m_user_scope();
            $this->_vars['userScope'] = $userScope;
            $this->_vars['mode'] = 'edit';
            $this->_vars['url_save'] = $this->_vars['urlb']."update/".$id;
            $this->_vars['selData'] = json_encode($sel['data']);
            return view($this->_viewPath.'v1Form', $this->_vars);
        }else{
            return $sel['message'];
        }
    }
    public function update(Request $request, $id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'title' => ['required','string','max:100'],
            'desc' => ['nullable','string','max:200'],
            'date_from' => ['required','date_format:d/m/Y'],
            'date_to' => ['required','date_format:d/m/Y'],
        ],[
            'date_from.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
            'date_to.date_format' => "The :attribute does not match the format dd/mm/yyyy."
        ])->setAttributeNames([
            'title' => 'Event Title',
            'desc' => 'Description',
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
                DB::table('mcu2_event')->where($where)->update($update);

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
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $eventData = $sel['data'];
            if($eventData->patient_total == $eventData->patient_scheduled){
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
                    DB::table('mcu2_event')->where($where)->update($update);

                    DB::table('mcu2_patient')
                    ->whereRaw("mcu2_event_id=".$id." AND delete_at IS NULL")
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
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
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
