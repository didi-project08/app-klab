<?php
namespace App\Modules\MCUEvent;

use Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
// use Validator;
// use Illuminate\Support\Facades;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory as Spreadsheet_IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Spreadsheet_Style_Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border as Spreadsheet_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Borders as Spreadsheet_Style_Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill as Spreadsheet_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as Spreadsheet_Style_NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType as Spreadsheet_Cell_DataType;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as Spreadsheet_Cell_Date;

use App\Modules\BASE\MY_Controller;
use App\Models\ModelBase;
use App\Helpers\Main AS MainHelper;

// use App\Models\Laboratory;
// use App\Models\LaboratoryRefer;
// use App\Models\UserLaboratory;
// use App\Models\Corporate;
// use App\Models\CorporateEmp;
use App\Models\MCUEvent;
use App\Models\MCUEventPatient;
use App\Models\MCUFormatItem;

class B2Main extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCUEvent/";
        $this->_vars["_id_"] = "M2";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b2";
        $this->_vars['url'] = URL::to('/')."/mcu-event/";
        $this->_vars['urlb'] = $this->_vars['url']."b2/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        return view($this->_viewPath.'v2Main', $this->_vars);
    }
    public function filter(){
        return view($this->_viewPath.'v2Filter', $this->_vars);
    }
    public function read(){
        $this->_vars['orderBy'] = ['sa.order:asc','a.create_at:desc'];
        $result['rows'] = MCUEventPatient::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = MCUEventPatient::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function add(Request $request, $mcu_event_id = 0){
        $this->_vars['id'] = $mcu_event_id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'add';
            $this->_vars['url_save'] = $this->_vars['urlb']."create/".$mcu_event_id;
            $this->_vars['eventData'] = $sel['data'];
            return view($this->_viewPath.'v2Form', $this->_vars);
        }else{
            return $sel['message'];
        }
    }
    public function create(Request $request, $mcu_event_id = 0){
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
    public function edit(Request $request, $id = 0){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        $this->_vars['id'] = $id;
        $sel = MCUEventPatient::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['id'] = $sel['data']->mcu_event_id;
            $sel2 = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
            if($sel2['success']){
                $this->_vars['mode'] = 'edit';
                $this->_vars['url_save'] = $this->_vars['urlb']."update/".$id;
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
    public function update(Request $request, $id = 0){
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
            'actual_date' => ['required','date_format:d/m/Y'],
            'mcu_format_package_id' => ['required','integer']
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
            'actual_date' => 'Actual Date',
            'mcu_format_package_id' => 'Package'
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

                $qVars['id'] = $id;
                $sel = MCUEventPatient::query($qVars, ["mode"=>"rowOne"]);
                // dd($sel);
                if($sel['success']){
                    $v = $sel['data'];
                    $update2["emp_no"] = $update['emp_no'];
                    $update2["area"] = $update['area'];
                    $update2["division"] = $update['division'];
                    $update2["position"] = $update['position'];
                    $update2["nik"] = $update['nik'];
                    $update2["name"] = $update['name'];
                    $update2["gender"] = $update['gender'];
                    $update2["dob"] = $update['dob'];
                    $update2["phone"] = $update['phone'];
                    $update2["address"] = $update['address'];
                    $update2['update_by'] = session()->get('sessUsername');
                    $update2['update_at'] = $at;
                    $where2 = [
                        "id"=>$v->corporate_emp_id,
                    ];
                    DB::table('corporate_emp')->where($where2)->update($update2);
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
    public function present(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUEventPatient::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            if($sel['data']->status == 'sScheduled'){
                $this->_vars['id'] = $sel['data']->mcu_event_id;
                $sel2 = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
                if($sel2['success']){
                    $this->_vars['mode'] = 'present';
                    $this->_vars['url_save'] = $this->_vars['urlb']."presentSave/".$id;
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
    public function presentSave(Request $request, $id = 0){
        $qVars['id'] = $id;
        $sel = MCUEventPatient::query($qVars, ["mode"=>"rowOne"]);
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
                'actual_date' => ['required','date_format:d/m/Y'],
                'mcu_format_package_id' => ['required','integer']
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
                'actual_date' => 'Actual Date',
                'mcu_format_package_id' => 'Package'
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

                    if($update['status'] == "sPresented"){
                        // $update['status'] = "sPresented";
                        // $update['status_by'] = session()->get('sessUsername');
                        // $update['status_at'] = $at;
                        // $update['status_note'] = "Presented";
                        $update['presentat'] = $at;
                        $update['presentnum'] = MCUEventPatient::genPresentNum($sel['data']->mcu_event_id);
                    }
                    $where = [
                        "id"=>$id,
                    ];
                    DB::table('mcu_event_patient')->where($where)->update($update);

                    $insert = [];
                    $insert['mcu_event_patient_id'] = $id;
                    // $insert['status'] = "sPresented";
                    $insert['status'] = $update['status'];
                    $insert['status_by'] = session()->get('sessUsername');
                    $insert['status_at'] = $at;
                    // $insert['status_note'] = "Presented";
                    $insert['status_note'] = $update['status_note'];
                    DB::table('mcu_event_patient_status')->insert($insert);

                    $qVars['id'] = $id;
                    $sel = MCUEventPatient::query($qVars, ["mode"=>"rowOne"]);
                    // dd($sel);
                    if($sel['success']){
                        $v = $sel['data'];
                        $update2["emp_no"] = $update['emp_no'];
                        $update2["area"] = $update['area'];
                        $update2["division"] = $update['division'];
                        $update2["position"] = $update['position'];
                        $update2["nik"] = $update['nik'];
                        $update2["name"] = $update['name'];
                        $update2["gender"] = $update['gender'];
                        $update2["dob"] = $update['dob'];
                        $update2["phone"] = $update['phone'];
                        $update2["address"] = $update['address'];
                        $update2['update_by'] = session()->get('sessUsername');
                        $update2['update_at'] = $at;
                        $where2 = [
                            "id"=>$v->corporate_emp_id,
                        ];
                        DB::table('corporate_emp')->where($where2)->update($update2);
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
        }else{
            $result['success'] = false;
            $result['message'] = "Patient Not Found.";
        }
        echo json_encode($result);
    }
    public function updateResult(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUEventPatient::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            if($sel['data']->status == 'sPresented' || $sel['data']->status == 'sFinished'){
                $this->_vars['id'] = $sel['data']->mcu_event_id;
                $sel2 = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
                if($sel2['success']){
                    $this->_vars['mode'] = 'updateResult';
                    $this->_vars['url_save'] = $this->_vars['urlb']."updateResultSave/".$id;
                    $this->_vars['selData'] = json_encode($sel['data']);
                    $this->_vars['eventData'] = $sel2['data'];
                    return view($this->_viewPath.'v2Form', $this->_vars);
                }else{
                    return "Event : ".$sel['message'];
                }
            }else{
                return "Event Patient : Can't Update Result.";
            }
        }else{
            return "Event Patient : ".$sel['message'];
        }
    }
    public function updateResultSave(Request $request, $id = 0){
        $qVars['id'] = $id;
        $sel = MCUEventPatient::query($qVars, ["mode"=>"rowOne"]);
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
                'actual_date' => ['required','date_format:d/m/Y'],
                'mcu_format_package_id' => ['required','integer'],
                'status' => ['required','string'],
                'fit_date' => ['required','date_format:d/m/Y'],
                'fit_cate' => ['required','string','max:50'],
                'fit_note' => ['required','string']
            ],[
                'dob.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
                'schedule_date.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
                'actual_date.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
                'fit_date.date_format' => "The :attribute does not match the format dd/mm/yyyy."
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
                'actual_date' => 'Actual Date',
                'mcu_format_package_id' => 'Package',
                'status' => 'Status',
                'fit_date' => 'Fit Date',
                'fit_cate' => 'Fit Category',
                'fit_note' => 'Fit Note'
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
                    $update['fit_date'] = date("Y-m-d", strtotime(str_replace("/","-",$update['fit_date'])));

                    $update['status_by'] = session()->get('sessUsername');
                    $update['status_at'] = $at;
                    if($update['status'] == "sPresented"){
                        $update['status_note'] = "Presented";
                    }
                    if($update['status'] == "sFinished"){
                        $update['status_note'] = "Finished";
                    }

                    $where = [
                        "id"=>$id,
                    ];
                    DB::table('mcu_event_patient')->where($where)->update($update);

                    $insert = [];
                    $insert['mcu_event_patient_id'] = $id;
                    $insert['status'] = $update['status'];
                    $insert['status_by'] = session()->get('sessUsername');
                    $insert['status_at'] = $at;
                    $insert['status_note'] = $update['status_note'];
                    DB::table('mcu_event_patient_status')->insert($insert);

                    $qVars['id'] = $id;
                    $sel = MCUEventPatient::query($qVars, ["mode"=>"rowOne"]);
                    // dd($sel);
                    if($sel['success']){
                        $v = $sel['data'];
                        $update2["emp_no"] = $update['emp_no'];
                        $update2["area"] = $update['area'];
                        $update2["division"] = $update['division'];
                        $update2["position"] = $update['position'];
                        $update2["nik"] = $update['nik'];
                        $update2["name"] = $update['name'];
                        $update2["gender"] = $update['gender'];
                        $update2["dob"] = $update['dob'];
                        $update2["phone"] = $update['phone'];
                        $update2["address"] = $update['address'];
                        $update2['update_by'] = session()->get('sessUsername');
                        $update2['update_at'] = $at;
                        $where2 = [
                            "id"=>$v->corporate_emp_id,
                        ];
                        DB::table('corporate_emp')->where($where2)->update($update2);
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
        }else{
            $result['success'] = false;
            $result['message'] = "Patient Not Found.";
        }
        echo json_encode($result);
    }
    public function delete(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCUEventPatient::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $v = $sel['data'];
            if($v->status == 'sScheduled'){
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
                    DB::table('mcu_event_patient')->where($where)->update($update);
                    
                    $where = [];
                    $where = [
                        "corporate_emp_id"=>$v->corporate_emp_id,
                    ];
                    DB::table('mcu_event_corporate_emp')->where($where)->delete();
    
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
                $result['message'] = "Can't delete this data.";
            }
        }else{
            $result['success'] = false;
            $result['message'] = "Delete Failed, Event Not Found.";
        }
        echo json_encode($result);
    }
    public function patientExport(Request $request){
        $qVars['id'] = $this->_vars['mcu_event_id'];
        $event = MCUEvent::query($qVars, ["mode"=>"rowOne"]);
        if($event['success']){
            $eventData = $event['data'];

            $this->_vars['orderBy'] = ['sa.order:asc','a.create_at:desc'];
            $contentList = MCUEventPatient::query($this->_vars, ["mode"=>"rowsAll"]);
            $fieldList = MCUEventPatient::excelField();

            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0);
            $sheetIndex = -1;

            $sheetIndex++;
            $createSheet = new Worksheet($spreadsheet, 'data');
            $spreadsheet->addSheet($createSheet, $sheetIndex);
            $spreadsheet->setActiveSheetIndex($sheetIndex);

            $c = $spreadsheet->getActiveSheet();

            $xr = 1; $xc = 0; $xc++;
            $c->setCellValue(MainHelper::getCol($xc).$xr, 'MCU Patient');

            $lastRowCol = MainHelper::excelHeader($c, ['xr'=>3, 'xc'=>1, 'fieldList'=>$fieldList]);
            $lastRowCol = MainHelper::excelContent($c, ['xr'=>4, 'xc'=>1, 'fieldList'=>$fieldList, 'contentList'=>$contentList]);

            $fileName = "MCUPatient_".uniqid().".xlsx";
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$fileName.'"');
            header('Cache-Control: max-age=0');

            $writer = Spreadsheet_IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }else{
            return "MCU Project not found.";
        }
    }
    public function resultExport(Request $request){
        $qVars['id'] = $this->_vars['mcu_event_id'];
        $event = MCUEvent::query($qVars, ["mode"=>"rowOne"]);
        if($event['success']){
            $eventData = $event['data'];
            // dd($eventData);

            $this->_vars['orderBy'] = ['sa.order:asc','a.create_at:desc'];
            $contentList = MCUEventPatient::query($this->_vars, ["mode"=>"rowsAll"]);
            $fieldList = MCUEventPatient::excelField();
            // dd($fieldList);
            
            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0);
            $sheetIndex = -1;

            $sheetIndex++;
            $createSheet = new Worksheet($spreadsheet, 'data');
            $spreadsheet->addSheet($createSheet, $sheetIndex);
            $spreadsheet->setActiveSheetIndex($sheetIndex);

            $c = $spreadsheet->getActiveSheet();

            $xr = 1; $xc = 0; $xc++;
            $c->setCellValue(MainHelper::getCol($xc).$xr, 'MCU Patient Result');

            $excelParams = ['xr'=>3, 'xc'=>1, 'fieldList'=>$fieldList];
            $lastRowCol = MainHelper::excelHeader($c, $excelParams);
            $excelParams = ['xr'=>4, 'xc'=>1, 'fieldList'=>$fieldList, 'contentList'=>$contentList];
            $lastRowCol = MainHelper::excelContent($c, $excelParams);

            if($this->_vars['resultCate'] == -1){
                $formatItemField = [];
                $row['field'] = "fit_cate";
                $row['title'] = "FIT STATUS";
                $row['width'] = 160;
                $row['dataType'] = "STRING";
                array_push($formatItemField, $row);
                $row['field'] = "fit_note";
                $row['title'] = "FIT NOTE";
                $row['width'] = 160;
                $row['dataType'] = "STRING";
                array_push($formatItemField, $row);
            }else{
                $qVars = [];
                $qVars['mcu_format_id'] = $eventData->mcu_format_id;
                $qVars['resultCate'] = $this->_vars['resultCate'];
                $formatItem = MCUFormatItem::query($qVars, ["mode"=>"rowsAll"]);
                $formatItem = MCUFormatItem::converToTree($formatItem);
                $formatItemField = MCUFormatItem::converToExcelField($formatItem);
                // dd($formatItemField);
            }
            $excelParams = ['xr'=>3, 'xc'=>$lastRowCol['xc']+1, 'fieldList'=>$formatItemField];
            MainHelper::excelHeader($c, $excelParams);

            $formatItemFieldID = [];
            foreach ($formatItemField as $k => $v) {
                $v['title'] = $v['field'];
                array_push($formatItemFieldID, $v);
            }
            $excelParams = ['xr'=>2, 'xc'=>$lastRowCol['xc']+1, 'fieldList'=>$formatItemFieldID];
            MainHelper::excelHeader($c, $excelParams);


            $fileName = "MCUPatientResult_".uniqid().".xlsx";
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$fileName.'"');
            header('Cache-Control: max-age=0');

            $writer = Spreadsheet_IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }else{
            return "MCU Project not found.";
        }
    }
    public function resultImport(Request $request, $mcu_event_id = 0){
        $this->_vars['id'] = $mcu_event_id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'add';
            $this->_vars['url_save'] = $this->_vars['urlb']."resultImportSave/".$mcu_event_id;
            $this->_vars['eventData'] = $sel['data'];
            return view($this->_viewPath.'v2FormImportResult', $this->_vars);
        }else{
            return $sel['message'];
        }
    }
    public function resultImportSave(Request $request, $mcu_event_id = 0){
        $this->_vars['id'] = $mcu_event_id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $validator = Facades\Validator::make($request->all(), [
                'file' => ['required','file']
            ])->setAttributeNames([
                'file' => 'File',
            ]);
            $validator->after(function (Validator $validator) use ($request) {
                $file = $request->file('file');
                $mimeType = $file->getMimeType();
                $size = $file->getSize();
                $size = $size / 1024 / 1024;
                
                if($mimeType != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                    $validator->errors()->add('file', 'Please choose .xlsx file');
                }
                if($size > 2){
                    $validator->errors()->add('file', 'Max size 2MB');
                }
            });
            if ($validator->fails()){
                $result['success'] = false;
                $result['message'] = "Form data is not valid.";
                $result['form_error'] = true;
                $result['form_error_array'] = $validator->errors();
            }else{
                $path = Storage::disk('local')->putFile(
                    'excelImport',
                    $request->file('file')
                );
                // dd($path);

                $exist = Storage::disk('local')->exists($path);
                if($exist){
                    $at = date("Y-m-d H:i:s");

                    DB::beginTransaction();
                    try{
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                        $spreadsheet = $reader->setReadDataOnly(true)->load(storage_path("app/".$path));
                        $sheetData = $spreadsheet->getSheet(0)->toArray();
                        // dd($sheetData);

                        foreach ($sheetData as $k => $v) {
                            if($k >= 3){
                                $update = [];
                                $update['fit_cate'] = $v[16];
                                $update['fit_note'] = $v[17];
                                $update['fit_by'] = session()->get('sessUsername');
                                $update['fit_at'] = $at;
                                if($v[15] == "YES"){
                                    $update['status'] = "sFinished";
                                    $update['status_by'] = session()->get('sessUsername');
                                    $update['status_at'] = $at;
                                    $update['status_note'] = "Import Result";
                                }
                                $where = [];
                                $where['id'] = $v[0];
                                DB::table("mcu_event_patient")->where($where)->update($update);

                                if($v[15] == "YES"){
                                    $insert = [];
                                    $insert['mcu_event_patient_id'] = $v[0];
                                    $insert['status'] = "sFinished";
                                    $insert['status_by'] = session()->get('sessUsername');
                                    $insert['status_at'] = $at;
                                    $insert['status_note'] = "Import Result.";
                                    DB::table('mcu_event_patient_status')->insert($insert);
                                }
                            }
                        }
                        
                        DB::commit();
                        $result['success'] = true;
                        $result['message'] = "Import Result Success.";
                    } catch(\Exception $ex){
                        throw $ex;
                        DB::rollback();
                        $result['success'] = false;
                        $result['message'] = "Import Result Failed.";
                    }

                    
                }else{
                    $validator->errors()->add('file', 'Upload file not succee, please try again.');
                    $result['success'] = false;
                    $result['message'] = "Form data is not valid.";
                    $result['form_error'] = true;
                    $result['form_error_array'] = $validator->errors();
                }
            }
        }else{
            $result['success'] = false;
            $result['message'] = "Event Not Found.";
        }
        echo json_encode($result);
    }
    public function move(Request $request, $mcu_event_id = 0){
        $this->_vars['_idbf_'] = $this->_vars['_idb_']."FMove";
        $this->_vars['id'] = $mcu_event_id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'add';
            $this->_vars['url_save'] = $this->_vars['urlb']."moveSave/".$mcu_event_id;
            $this->_vars['eventData'] = $sel['data'];
            return view($this->_viewPath.'v2FormMove', $this->_vars);
        }else{
            return $sel['message'];
        }
    }
    public function moveSave(Request $request, $mcu_event_id = 0){
        $this->_vars['id'] = $mcu_event_id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $eventData = $sel['data'];

            $validatorList = [
                'otherMCUEventId' => ['required','integer'],
                'patientIdList' => ['required','string'],
                'mcu_format_package_id' => ['required','integer']
            ];
            $validator = Facades\Validator::make($request->all(), $validatorList)->setAttributeNames([
                'otherMCUEventId' => "Other MCU Project",
                'patientIdList' => 'Patient List',
                'mcu_format_package_id' => 'Package',
            ]);
            $validator->after(function (Validator $validator) use ($mcu_event_id, $request) {
                if($mcu_event_id == $request->all()['otherMCUEventId']){
                    $validator->errors()->add('otherMCUEventId', 'Please select other MCU Project.');
                }
            });
            if ($validator->fails()){
                $result['success'] = false;
                $result['message'] = "Form data is not valid.";
                $result['form_error'] = true;
                $result['form_error_array'] = $validator->errors();
            }else{
                $this->_vars['id'] = $request->all()['otherMCUEventId'];
                $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
                if($sel['success']){
                    $otherEventData = $sel['data'];
                    $at = date("Y-m-d H:i:s");

                    $patientIdList = $request->all()['patientIdList'];
                    $patientIdList = explode(",",$patientIdList);
                    $qVars['patientIdList'] = $patientIdList;
                    $qVars['status'] = "sScheduled";
                    $patientData = MCUEventPatient::query($qVars, ["mode"=>"rowsAll"]);

                    // dd($patientData);
        
                    DB::beginTransaction();
                    try{
                        $pSuccess = 0;
                        $pNotAllow = 0;
                        $pExist = 0;
                        foreach ($patientData as $k => $v) {
                            $update = [];
                            $update['mcu_event_id'] = $request->all()['otherMCUEventId'];
                            $update['mcu_format_package_id'] = $request->all()['mcu_format_package_id'];
                            $where = [];
                            $where['id'] = $v->id;
                            DB::table('mcu_event_patient')->where($where)->update($update);
                            
                            $update = [];
                            $update['mcu_event_id'] = $request->all()['otherMCUEventId'];
                            $where = [];
                            $where['mcu_event_patient_id'] = $v->id;
                            DB::table('mcu_event_corporate_emp')->where($where)->update($update);
                            
                            $pSuccess++;
                        }
                        DB::commit();
                        $result['success'] = true;
                        $result['message'] = "Success : ".$pSuccess;
                    } catch(\Exception $ex){
                        throw $ex;
                        DB::rollback();
                        $result['success'] = false;
                        $result['message'] = "Move Failed.";
                    }
                }else{
                    DB::rollback();
                    $result['success'] = false;
                    $result['message'] = "Other MCU Project : ".$sel['message'];
                }
            }
        }else{
            DB::rollback();
            $result['success'] = false;
            $result['message'] = $sel['message'];
        }
        echo json_encode($result);
    }
    public function sendWA(Request $request, $mcu_event_id = 0){
        $this->_vars['_idbf_'] = $this->_vars['_idb_']."FWA";
        $qVars['id'] = $mcu_event_id;
        $sel = MCUEvent::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'add';
            $this->_vars['url_save'] = $this->_vars['urlb']."sendWAProcess/".$mcu_event_id."/";
            $this->_vars['eventData'] = $sel['data'];
            return view($this->_viewPath.'v2SendWA', $this->_vars);
        }else{
            return $sel['message'];
        }
    }
    public function sendWAProcess(Request $request, $mcu_event_id = 0, $id = 0){
        $qVars['id'] = $mcu_event_id;
        $event = MCUEvent::query($qVars, ["mode"=>"rowOne"]);
        if($event['success']){
            $qVars['id'] = $id;
            $patient = MCUEventPatient::query($qVars, ["mode"=>"rowOne"]);
            if($patient['success']){
                $result = self::sendWAProcess2($event['data'], $patient['data']);
            }else{
                $result = "PATIENT : ".$patient;
            }
        }else{
            $result = "EVENT : ".$event;
        }
        echo json_encode($result);
    }
    private function sendWAProcess2($event, $patient){
        $token = 'jKQ9j8TtzMuCsjbABlsLCAFJIklrtjsL7V6v2zk7IgJvyhdzRv';
        $phone = $patient->phone;
        $message = "";
        $message .= "INFORMASI PELAKSANAAN MCU" . PHP_EOL . PHP_EOL;
        $message .= "KTP / Passport : " . $patient->nik . PHP_EOL;
        $message .= "Name : " . $patient->name . PHP_EOL;
        $message .= "Gender : " . $patient->gender . PHP_EOL;
        $message .= "DOB : " . $patient->dob_dmY_slash . PHP_EOL . PHP_EOL;

        $message .= "Corporate : " . $event->corporate_name . PHP_EOL;
        $message .= "Client : " . $event->corporate_client_name . PHP_EOL;
        $message .= "MCU Location : " . $event->laboratory_name . PHP_EOL;
        $message .= "MCU Package : " . $patient->mcu_format_package_name . PHP_EOL;
        $message .= "MCU Date : " . $patient->schedule_date_dmY_slash . PHP_EOL . PHP_EOL;
        
        $message .= "apabila ada kendala atau pertanyaan silahkan hubungi nomor di bawah ini" . PHP_EOL . PHP_EOL;
        $message .= $event->client_pic_name . " : " . $event->client_pic_phone;
        try{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://nusagateway.com/api/send-message.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'token' => $token,
                    'phone' => $phone,
                    'message' => $message
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            $response = json_decode($response,true);
            // dd($response);

            if(@$response['result'] == 'true'){
                DB::commit();
                $result['success'] = true;
                $result['message'] = $response['message'];
            }else if(@$response['result'] == 'false'){
                DB::commit();
                $result['success'] = false;
                $result['message'] = $response['message'];
            }else{
                DB::commit();
                $result['success'] = false;
                $result['message'] = "Number not valid";
            }

            if($result['success']){
                $update = [];
                $update['wa_by'] = session()->get('sessUsername');
                $update['wa_at'] = date("Y-m-d H:i:s");
                $update['wa_status'] = 1;
                $update['wa_status_message'] = $result['message'];
                $where = [
                    "id"=>$patient->id,
                ];
                DB::table('mcu_event_patient')->where($where)->update($update);

                $insert = [];
                $insert['token'] = $token;
                $insert['phone'] = $phone;
                $insert['message'] = $message;
                $insert['status'] = 1;
                $insert['status_message'] = $result['message'];
                $insert['create_by'] = session()->get('sessUsername');
                $insert['create_at'] = date("Y-m-d H:i:s");
                DB::table('wa_history')->insert($insert);
            }else{
                $update = [];
                $update['wa_by'] = null;
                $update['wa_at'] = null;
                $update['wa_status'] = 0;
                $update['wa_status_message'] = $result['message'];
                $where = [
                    "id"=>$patient->id,
                ];
                DB::table('mcu_event_patient')->where($where)->update($update);

                $insert = [];
                $insert['token'] = $token;
                $insert['phone'] = $phone;
                $insert['message'] = $message;
                $insert['status'] = 0;
                $insert['status_message'] = $result['message'];
                $insert['create_by'] = session()->get('sessUsername');
                $insert['create_at'] = date("Y-m-d H:i:s");
                DB::table('wa_history')->insert($insert);
            }
            
        } catch(\Exception $ex){
            throw $ex;
            DB::rollback();
            $result['success'] = false;
            $result['message'] = "Send WA Failed.";
        }
        return $result;
    }
}
