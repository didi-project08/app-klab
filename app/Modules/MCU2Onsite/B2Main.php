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
use \Mpdf\Mpdf AS PDF;
use \Milon\Barcode\DNS1D;
use \Milon\Barcode\DNS2D;

use App\Modules\BASE\MY_Controller;
use App\Models\ModelBase;
use App\Helpers\Main AS MainHelper;

// use App\Models\Laboratory;
// use App\Models\LaboratoryRefer;
// use App\Models\UserLaboratory;
// use App\Models\Corporate;
// use App\Models\CorporateEmp;
use App\Models\MCU2Event;
use App\Models\MCU2Patient;
use App\Models\MCU2FormatItem;
use App\Models\MCU2FormatPackageItem;
use App\Models\MCU2Result;
use App\Models\MCU2FormatTeam;
Use App\Models\MCU2FormatRefItem;
Use App\Models\MCU2FormatConclusion;
Use App\Models\MCU2FormatFitstatus;

class B2Main extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCU2Onsite/";
        $this->_vars["_id_"] = "M31";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b2";
        $this->_vars['url'] = URL::to('/')."/mcu2-onsite/";
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
        // $this->_vars['orderBy'] = ['sa.order:asc','a.create_at:desc'];
        // $this->_vars['orderBy'] = ['sa.order:asc','a.schedule_date:asc','a.id:asc'];
        // $this->_vars['orderBy'] = ['sa.order:asc','a.schedule_date:asc','a.name:asc'];
        // $this->_vars['orderBy'] = ['a.schedule_date:asc','a.reg_num:asc','a.id:asc'];
        $this->_vars['orderBy'] = ['a.schedule_date:asc','a.reg_num:asc','a.id:asc'];
        $result['rows'] = MCU2Patient::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = MCU2Patient::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function add(Request $request, $mcu_event_id = 0){
        $this->_vars['id'] = $mcu_event_id;
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
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
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $validatorList = [
                'unique' => ['required','string','max:50'],
                'name' => ['required','string','max:100'],
                'gender' => ['required',Rule::in(['M','F'])],
                'dob' => ['required','date_format:d/m/Y'],
                'schedule_date' => ['required','date_format:d/m/Y'],
                'mcu2_format_package_code' => ['required','string','max:20'],
                'ktp' => ['nullable','string','max:30'],
                'phone' => ['nullable','string','max:30'],
                'email' => ['nullable','string','max:100'],
                'address' => ['nullable','string','max:200'],
                'emp_no' => ['nullable','string','max:30'],
                'emp_area' => ['nullable','string','max:50'],
                'emp_div' => ['nullable','string','max:50'],
                'emp_pos' => ['nullable','string','max:50']
            ];
            $validator = Facades\Validator::make($request->all(), $validatorList,[
                'gender.in' => "Please choose a valid :attribute",
                'dob.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
                'schedule_date.date_format' => "The :attribute does not match the format dd/mm/yyyy."
            ])->setAttributeNames([
                'unique' => "Unique Code",
                'name' => 'Fullname',
                'dob' => 'Date of Birth',
                'gender' => 'Gender',
                'schedule_date' => "Schedule Date",
                'mcu2_format_package_code' => "MCU Package",
                'ktp' => 'ID (NIK / Passport)',
                'phone' => 'Phone Number',
                'email' => 'Email',
                'address' => 'Address',
                'emp_no' => 'Emp. Number',
                'emp_area' => 'Job Area',
                'emp_div' => 'Job Division',
                'emp_pos' => 'Job Position'
            ]);

            $GLOBALS['patient_photo_upload']['status'] = 0;
            $GLOBALS['patient_photo_upload']['path'] = "";
            $GLOBALS['patient_photo_upload']['data'] = "";
            $validator->after(function (Validator $validator) use ($request, $mcu_event_id) {
                $db = 
                    DB::table(DB::raw("mcu2_patient AS a"))
                    ->whereRaw("a.mcu2_event_id='".$mcu_event_id."'")
                    ->whereRaw("a.unique='".$request->post('unique')."'")
                    ->whereRaw("a.delete_at IS NULL")
                    ->limit(1);
                $db = $db->get();
                $total = count($db);
                if($total > 0){
                    $validator->errors()->add('unique', 'UNIQUE CODE already exist, try another one.');
                }

                $patient_photo = $request->post('patient_photo');
                $patient_photo_new = $request->post('patient_photo_new');
                if($patient_photo_new == 1 && $patient_photo != null && $patient_photo != ""){
                    if (preg_match('/^data:image\/(\w+);base64,/', $patient_photo, $type)) {
                        $patient_photo = substr($patient_photo, strpos($patient_photo, ',') + 1);
                        $type = strtolower($type[1]); // jpg, png, gif
                    
                        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                            throw new \Exception('invalid image type');
                        }
                        $patient_photo = str_replace( ' ', '+', $patient_photo );
                        $patient_photo = base64_decode($patient_photo);
                    
                        if ($patient_photo === false) {
                            $validator->errors()->add('patient_photo', 'Failed to conver, please try again or left empty.');
                        }else{
                            $GLOBALS['patient_photo_upload']['status'] = 1;
                            $GLOBALS['patient_photo_upload']['path'] = "{$mcu_event_id}/PATIENT_PHOTO/##id##.{$type}";
                            $GLOBALS['patient_photo_upload']['data'] = $patient_photo;
                        }
                    } else {
                        $validator->errors()->add('patient_photo', 'Photo daata not valid, please try again or left empty.');
                    }
                }

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
                    unset($insert['patient_photo']);
                    unset($insert['patient_photo_new']);
                    $insert['create_by'] = session()->get('sessUsername');
                    $insert['create_at'] = $at;
                    $insert['uuid'] = MainHelper::uuid();
                    $insert['dob'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['dob'])));
                    $insert['schedule_date'] = date("Y-m-d", strtotime(str_replace("/","-",$insert['schedule_date'])));
                    $insert['mcu2_event_id'] = $mcu_event_id;
                    $insert['reg_num'] = MCU2Patient::genReg_Num($insert['mcu2_event_id']);
                    $insert['reg_by'] = session()->get('sessUsername');
                    $insert['reg_at'] = $at;
                    $insert['status'] = "sScheduled";
                    $insert['status_by'] = session()->get('sessUsername');
                    $insert['status_at'] = $at;
                    $insert['status_note'] = "New Patient";
                    // $insert['regnumprov'] = MCU2Patient::genRegNumProv($insert['mcu_event_id']);
                    // $insert['regnumref'] = MCU2Patient::genRegNumRef($insert['mcu_event_id']);
                    unset($insert['forcePresentNum']);
                    DB::table('mcu2_patient')->insert($insert);
                    $lastInsertId = DB::getPdo()->lastInsertId();

                    if($GLOBALS['patient_photo_upload']['status']){
                        $GLOBALS['patient_photo_upload']['path'] = str_replace("##id##", $lastInsertId, $GLOBALS['patient_photo_upload']['path']);

                        DB::table('mcu2_patient')->where(["id"=>$lastInsertId])->update(["patient_photo"=>$GLOBALS['patient_photo_upload']['path']]);

                        if(env('SYS_CONNECTION') == 'ONLINE'){
                            Storage::disk('google')->put($GLOBALS['patient_photo_upload']['path'], $GLOBALS['patient_photo_upload']['data']);
                        }else{
                            Storage::disk('local')->put($GLOBALS['patient_photo_upload']['path'], $GLOBALS['patient_photo_upload']['data']);
                        }
                    }

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
        $sel = MCU2Patient::query($this->_vars, ["mode"=>"rowOne","withPatientPhoto"=>1]);
        if($sel['success']){
            $this->_vars['id'] = $sel['data']->mcu2_event_id;
            $sel2 = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
            if($sel2['success']){
                $this->_vars['mode'] = 'edit';
                $this->_vars['url_save'] = $this->_vars['urlb']."update/".$sel2['data']->id."/".$id;
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
    public function update(Request $request, $mcu_event_id, $id = 0){
        // dd($request->all());
        $requestAll = $request->all();

        $validatorList = [
            'unique' => ['required','string','max:50'],
            'name' => ['required','string','max:100'],
            'gender' => ['required',Rule::in(['M','F'])],
            'dob' => ['required','date_format:d/m/Y'],
            'schedule_date' => ['required','date_format:d/m/Y'],
            'mcu2_format_package_code' => ['required','string','max:20'],
            'ktp' => ['nullable','string','max:30'],
            'phone' => ['nullable','string','max:30'],
            'email' => ['nullable','string','max:100'],
            'address' => ['nullable','string','max:200'],
            'emp_no' => ['nullable','string','max:30'],
            'emp_area' => ['nullable','string','max:50'],
            'emp_div' => ['nullable','string','max:50'],
            'emp_pos' => ['nullable','string','max:50']
        ];
        $validator = Facades\Validator::make($request->all(), $validatorList,[
            'gender.in' => "Please choose a valid :attribute",
            'dob.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
            'schedule_date.date_format' => "The :attribute does not match the format dd/mm/yyyy."
        ])->setAttributeNames([
            'unique' => "Unique Code",
            'name' => 'Fullname',
            'dob' => 'Date of Birth',
            'gender' => 'Gender',
            'schedule_date' => "Schedule Date",
            'mcu2_format_package_code' => "MCU Package",
            'ktp' => 'ID (NIK / Passport)',
            'phone' => 'Phone Number',
            'email' => 'Email',
            'address' => 'Address',
            'emp_no' => 'Emp. Number',
            'emp_area' => 'Job Area',
            'emp_div' => 'Job Division',
            'emp_pos' => 'Job Position'
        ]);

        $GLOBALS['patient_photo_upload']['status'] = 0;
        $GLOBALS['patient_photo_upload']['path'] = "";
        $GLOBALS['patient_photo_upload']['data'] = "";
        $validator->after(function (Validator $validator) use ($request, $mcu_event_id, $id) {
            $db = 
                DB::table(DB::raw("mcu2_patient AS a"))
                ->whereRaw("a.id != '".$id."'")
                ->whereRaw("a.mcu2_event_id='".$mcu_event_id."'")
                ->whereRaw("a.unique='".$request->post('unique')."'")
                ->whereRaw("a.delete_at IS NULL")
                ->limit(1);
            $db = $db->get();
            $total = count($db);
            if($total > 0){
                $validator->errors()->add('unique', 'UNIQUE CODE already exist, try another one.');
            }

            $patient_photo = $request->post('patient_photo');
            $patient_photo_new = $request->post('patient_photo_new');
            if($patient_photo_new == 1 && $patient_photo != null && $patient_photo != ""){
                if (preg_match('/^data:image\/(\w+);base64,/', $patient_photo, $type)) {
                    $patient_photo = substr($patient_photo, strpos($patient_photo, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif
                
                    if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                        throw new \Exception('invalid image type');
                    }
                    $patient_photo = str_replace( ' ', '+', $patient_photo );
                    $patient_photo = base64_decode($patient_photo);
                
                    if ($patient_photo === false) {
                        $validator->errors()->add('patient_photo', 'Failed to conver, please try again or left empty.');
                    }else{
                        $GLOBALS['patient_photo_upload']['status'] = 1;
                        $GLOBALS['patient_photo_upload']['path'] = "{$mcu_event_id}/PATIENT_PHOTO/{$id}.{$type}";
                        $GLOBALS['patient_photo_upload']['data'] = $patient_photo;
                    }
                } else {
                    $validator->errors()->add('patient_photo', 'Photo daata not valid, please try again or left empty.');
                }
            } 
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

                unset($update['patient_photo_new']);
                if($GLOBALS['patient_photo_upload']['status']){
                    $update['patient_photo'] = $GLOBALS['patient_photo_upload']['path'];
                }else{
                    unset($update['patient_photo']);
                }

                $update['update_by'] = session()->get('sessUsername');
                $update['update_at'] = $at;
                $update['dob'] = date("Y-m-d", strtotime(str_replace("/","-",$update['dob'])));
                $update['schedule_date'] = date("Y-m-d", strtotime(str_replace("/","-",$update['schedule_date'])));
                unset($update['forcePresentNum']);
                $where = [
                    "id"=>$id,
                ];
                DB::table('mcu2_patient')->where($where)->update($update);

                if($GLOBALS['patient_photo_upload']['status']){
                    if(env('SYS_CONNECTION') == 'ONLINE'){
                        Storage::disk('google')->put($GLOBALS['patient_photo_upload']['path'], $GLOBALS['patient_photo_upload']['data']);
                    }else{
                        Storage::disk('local')->put($GLOBALS['patient_photo_upload']['path'], $GLOBALS['patient_photo_upload']['data']);
                    }
                }
                
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
        echo json_encode($result);
    }
    public function present(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCU2Patient::query($this->_vars, ["mode"=>"rowOne","withPatientPhoto"=>1]);
        if($sel['success']){
            if($sel['data']->status != 'sPresented'){
                $this->_vars['id'] = $sel['data']->mcu2_event_id;
                $sel2 = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
                if($sel2['success']){
                    $this->_vars['mode'] = 'present';
                    $this->_vars['url_save'] = $this->_vars['urlb']."presentSave/".$sel2['data']->id."/".$id;
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
    public function updateStatus(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCU2Patient::query($this->_vars, ["mode"=>"rowOne","withPatientPhoto"=>1]);
        if($sel['success']){
            $this->_vars['id'] = $sel['data']->mcu2_event_id;
            $sel2 = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
            if($sel2['success']){
                $this->_vars['mode'] = 'updateStatus';
                $this->_vars['url_save'] = $this->_vars['urlb']."presentSave/".$sel2['data']->id."/".$id;
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
    public function presentSave(Request $request, $mcu_event_id = 0, $id = 0){
        $qVars['id'] = $id;
        $sel = MCU2Patient::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){

            $validatorList = [
                'unique' => ['required','string','max:50'],
                'name' => ['required','string','max:100'],
                'gender' => ['required',Rule::in(['M','F'])],
                'dob' => ['required','date_format:d/m/Y'],
                'schedule_date' => ['required','date_format:d/m/Y'],
                'mcu2_format_package_code' => ['required','string','max:20'],
                'ktp' => ['nullable','string','max:30'],
                'phone' => ['nullable','string','max:30'],
                'email' => ['nullable','string','max:100'],
                'address' => ['nullable','string','max:200'],
                'emp_no' => ['nullable','string','max:30'],
                'emp_area' => ['nullable','string','max:50'],
                'emp_div' => ['nullable','string','max:50'],
                'emp_pos' => ['nullable','string','max:50'],
                'forcePresentNum' => ['nullable','integer'],
                'status' => ['required','string','max:64'],
                'status_note' => ['nullable','string','max:200'],
            ];
            $validator = Facades\Validator::make($request->all(), $validatorList,[
                'gender.in' => "Please choose a valid :attribute",
                'dob.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
                'schedule_date.date_format' => "The :attribute does not match the format dd/mm/yyyy."
            ])->setAttributeNames([
                'unique' => "Unique Code",
                'name' => 'Fullname',
                'dob' => 'Date of Birth',
                'gender' => 'Gender',
                'schedule_date' => "Schedule Date",
                'mcu2_format_package_code' => "MCU Package",
                'ktp' => 'ID (NIK / Passport)',
                'phone' => 'Phone Number',
                'email' => 'Email',
                'address' => 'Address',
                'emp_no' => 'Emp. Number',
                'emp_area' => 'Job Area',
                'emp_div' => 'Job Division',
                'emp_pos' => 'Job Position',
                'forcePresentNum' => 'Force Present Number',
                'status' => 'Status',
                'status_note' => 'Status Note'
            ]);

            $GLOBALS['patient_photo_upload']['status'] = 0;
            $GLOBALS['patient_photo_upload']['path'] = "";
            $GLOBALS['patient_photo_upload']['data'] = "";
            $validator->after(function (Validator $validator) use ($request, $mcu_event_id, $id) {
                $db = 
                    DB::table(DB::raw("mcu2_patient AS a"))
                    ->whereRaw("a.id != '".$id."'")
                    ->whereRaw("a.mcu2_event_id='".$mcu_event_id."'")
                    ->whereRaw("a.unique='".$request->post('unique')."'")
                    ->whereRaw("a.delete_at IS NULL")
                    ->limit(1);
                $db = $db->get();
                $total = count($db);
                if($total > 0){
                    $validator->errors()->add('unique', 'UNIQUE CODE already exist, try another one.');
                }

                $patient_photo = $request->post('patient_photo');
                $patient_photo_new = $request->post('patient_photo_new');
                if($patient_photo_new == 1 && $patient_photo != null && $patient_photo != ""){
                    if (preg_match('/^data:image\/(\w+);base64,/', $patient_photo, $type)) {
                        $patient_photo = substr($patient_photo, strpos($patient_photo, ',') + 1);
                        $type = strtolower($type[1]); // jpg, png, gif
                    
                        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                            throw new \Exception('invalid image type');
                        }
                        $patient_photo = str_replace( ' ', '+', $patient_photo );
                        $patient_photo = base64_decode($patient_photo);
                    
                        if ($patient_photo === false) {
                            $validator->errors()->add('patient_photo', 'Failed to conver, please try again or left empty.');
                        }else{
                            $GLOBALS['patient_photo_upload']['status'] = 1;
                            $GLOBALS['patient_photo_upload']['path'] = "{$mcu_event_id}/PATIENT_PHOTO/{$id}.{$type}";
                            $GLOBALS['patient_photo_upload']['data'] = $patient_photo;
                        }
                    } else {
                        $validator->errors()->add('patient_photo', 'Photo daata not valid, please try again or left empty.');
                    }
                }
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

                    unset($update['patient_photo_new']);
                    if($GLOBALS['patient_photo_upload']['status']){
                        $update['patient_photo'] = $GLOBALS['patient_photo_upload']['path'];
                    }else{
                        unset($update['patient_photo']);
                    }

                    $update['update_by'] = session()->get('sessUsername');
                    $update['update_at'] = $at;
                    $update['dob'] = date("Y-m-d", strtotime(str_replace("/","-",$update['dob'])));
                    $update['schedule_date'] = date("Y-m-d", strtotime(str_replace("/","-",$update['schedule_date'])));

                    if($update['status'] == "sPresented"){
                        $update['present_by'] = session()->get('sessUsername');
                        $update['present_at'] = $at;
                        if($sel['data']->present_num == null || $sel['data']->present_num == ''){
                            $update['present_num'] = MCU2Patient::genPresent_Num($sel['data']->mcu2_event_id);
                        }
                        if(is_numeric($update['forcePresentNum'])){
                            $update['present_num'] = $update['forcePresentNum'];
                        }
                    }
                    
                    unset($update['forcePresentNum']);
                    $where = [
                        "id"=>$id,
                    ];
                    DB::table('mcu2_patient')->where($where)->update($update);

                    if($GLOBALS['patient_photo_upload']['status']){
                        if(env('SYS_CONNECTION') == 'ONLINE'){
                            Storage::disk('google')->put($GLOBALS['patient_photo_upload']['path'], $GLOBALS['patient_photo_upload']['data']);
                        }else{
                            Storage::disk('local')->put($GLOBALS['patient_photo_upload']['path'], $GLOBALS['patient_photo_upload']['data']);
                        }
                    }

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
            $result['success'] = false;
            $result['message'] = "Patient Not Found.";
        }
        echo json_encode($result);
    }

    public function label(Request $request){
        // dd("sa");
        $requestAll = $request->all();
        $qVars['id'] = $requestAll['confEventId'];
        $event = MCU2Event::query($qVars, ["mode"=>"rowOne"]);
        if($event['success']){
            $eventData = $event['data'];

            $qVars = [];
            $qVars['mcu2_event_id'] = $requestAll['confEventId'];
            $qVars['idList'] = explode(",", $requestAll['patientIdList']);
            $qVars['orderBy'] = ['a.schedule_date:asc','a.reg_num:asc','a.id:asc'];
            $patientList = MCU2Patient::query($qVars, ["mode"=>"rowsAll"]);

            if(count($patientList) > 0){
                $confField = "id";
                $confSize = "110x52-2";
                $confCopy = 1;
                if(isset($this->_vars['confField'])){
                    $confField = $this->_vars['confField'];
                }
                if(isset($this->_vars['confSize'])){
                    $confSize = $this->_vars['confSize'];
                }
                if(isset($this->_vars['confCopy'])){
                    $confCopy = $this->_vars['confCopy'];
                }

                if($confSize == '110x52-2'){
                    $mpdfConfig = [
                        'mode' => '',
                        'format' => [110, 25],
                        // 'format' => "A4",
                        'orientation' => 'P',
                        'default_font_size' => 0,
                        'default_font' => 'frutiger',
                        'margin_left' => 3,
                        'margin_right' => 3,
                        'margin_top' => 0,
                        'margin_bottom' => 0,
                        'margin_header' => 0,
                        'margin_footer' => 0,
                    ];
                    $mpdf = new PDF($mpdfConfig);
                    $mpdf->allow_charset_conversion=true;
                    $mpdf->charset_in='UTF-8';
                    $stylesheet = MainHelper::mpdfMyStylesheet_1();
                    $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
                }else{
                    return "Size need to define.";
                }

                try {
                    foreach ($patientList as $kP => $vP) {
                        for ($i=0; $i < $confCopy; $i++) { 
                            $mpdf->AddPage();
                            $qVars = [];
                            $qVars['confField'] = $confField;
                            $qVars['confSize'] = $confSize;
                            $qVars['confCopy'] = $confCopy;
                            $qVars['data'] = $vP;

                            $bc = new DNS2D();
                            if($vP->$confField != null && $vP->$confField != ""){
                                // $qVars['barcode'] = $bc->getBarcodeSVG($vP->$confField, 'C128',3,50,'black', false);
                                $qVars['barcode'] = $bc->getBarcodeSVG($vP->$confField, 'QRCODE');
                                $qVars['barcode'] = str_replace('<?xml version="1.0" standalone="no"?>','',$qVars['barcode']);
                                // $qVars['barcode'] = '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($vP->$confField, 'C128') . '" alt="barcode"   />';
                            }else{
                                $qVars['barcode'] = "";
                            }

                            // return $qVars['barcode'];
                            
                            $content = view($this->_viewPath.'v2Label', $qVars);
                            $mpdf->WriteHTML($content); 
                        }
                    }

                    $pdfName = "MCU_LABEL_".DATE("Y-m-d_His").".pdf";
                    header('Content-type: application/pdf');
                    header('Content-Disposition: inline; filename="'.$pdfName.'"');
                    header('Content-Transfer-Encoding: binary');
                    header('Accept-Ranges: bytes');
                    $mpdf->Output($pdfName,"D");
    

                    $result['success'] = false;
                    $result['status_code'] = 200;
                    $result['message'] = "Print Label Success.";
                } catch (\Exception $ex) {
                    throw $ex;
                    $result['success'] = false;
                    $result['status_code'] = $ex->getCode();
                    $result['message'] = "Print Label Failed.";
                }
            }else{
                $result['success'] = false;
                $result['status_code'] = 400;
                $result['message'] = "Data Patient no found, please try again.";
            }
        }else{
            $result['success'] = false;
            $result['status_code'] = 400;
            $result['message'] = "MCU Event not found.";
        }
        echo $result['message'];







        // $qVars['id'] = $id;
        // $sel = MCU2Patient::query($qVars, ["mode"=>"rowOne"]);
        // if($sel['success']){
        //     $confField = "id";
        //     $confSize = "110x52-2";
        //     $confCopy = 1;
        //     if(isset($this->_vars['confField'])){
        //         $confField = $this->_vars['confField'];
        //     }
        //     if(isset($this->_vars['confSize'])){
        //         $confSize = $this->_vars['confSize'];
        //     }
        //     if(isset($this->_vars['confCopy'])){
        //         $confCopy = $this->_vars['confCopy'];
        //     }

        //     if($confSize == '110x52-2'){
        //         $mpdfConfig = [
        //             'mode' => '',
        //             'format' => [110, 25],
        //             // 'format' => "A4",
        //             'orientation' => 'P',
        //             'default_font_size' => 0,
        //             'default_font' => 'frutiger',
        //             'margin_left' => 3,
        //             'margin_right' => 3,
        //             'margin_top' => 3,
        //             'margin_bottom' => 0,
        //             'margin_header' => 0,
        //             'margin_footer' => 0,
        //         ];
        //         $mpdf = new PDF($mpdfConfig);
        //         $mpdf->allow_charset_conversion=true;
        //         $mpdf->charset_in='UTF-8';
        //         $stylesheet = MainHelper::mpdfMyStylesheet_1();
        //         $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);

        //         for ($i=0; $i < $confCopy; $i++) { 
        //             $mpdf->AddPage();
        //             $qVars = [];
        //             $qVars['confSize'] = $confSize;
        //             $qVars['data'] = $sel['data'];
        //             $content = view($this->_viewPath.'v2Label', $qVars);
        //             $mpdf->WriteHTML($content); 
        //         }

        //         $pdfName = "MCU_LABEL_".DATE("Y-m-d_His").".pdf";
        //         header('Content-type: application/pdf');
        //         header('Content-Disposition: inline; filename="'.$pdfName.'"');
        //         header('Content-Transfer-Encoding: binary');
        //         header('Accept-Ranges: bytes');
        //         $mpdf->Output($pdfName,"D");

        //     }else{
        //         return "Size need to define.";
        //     }
        // }else{
        //     return $sel['message'];
        // }
    }
    public function printCover(Request $request){
        $qVars['idList'] = $this->_vars['idList'];
        $sel = MCU2Patient::query($qVars, ["mode"=>"rowsAll"]);
        // dd($sel);
        if(count($sel) > 0){
            $mpdfConfig = [
                'mode' => '',
                'format' => "A4",
                'orientation' => 'P',
                'default_font_size' => 0,
                'default_font' => 'frutiger',
                'margin_header'=>'0',
                'margin_top'=>'65',
                'margin_bottom'=>'30',
                'margin_left'=>'0',
                'margin_right'=>'0',
                'margin_footer'=>'0',
            ];
            $mpdf = new PDF($mpdfConfig);
            $mpdf->allow_charset_conversion=true;
            $mpdf->charset_in='UTF-8';
            $stylesheet = MainHelper::mpdfMyStylesheet_1();
            $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);

            foreach ($sel as $k => $v) {
                $mpdf->AddPage();
                $qVars = [];
                $v->corpName = @$request->get('corpName');

                if($v->patient_photo == null || $v->patient_photo == ""){
                    $path = 'klablogo.png';
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = Storage::disk('local')->get($path);
                    $v->patient_photo = $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    // dd($v->patient_photo);
                } 

                $qVars['data'] = $v;
                $content = view($this->_viewPath.'v2Cover', $qVars);
                $mpdf->WriteHTML($content); 
            }

            $pdfName = "Cover_".DATE("Y-m-d_His").".pdf";
            $path = "temp/".$pdfName;

            ob_start();
            $mpdf->Output("./","I");
            $content = ob_get_contents();
            ob_end_clean();

            $save = Storage::disk('local')->put($path, $content);
            if($save){
                $urlDownload = "/download?path=".$path."&d=temp&c=1";
                $result['success'] = true;
                $result['message'] = "Create PDF Success.";
                $result['urlDownload'] = $urlDownload;
            }else{
                $result['success'] = false;
                $result['message'] = "Create PDF Feiled.";
            }
            
        }else{
            $result['success'] = false;
            $result['message'] = "No data to be print.";
        }
        echo json_encode($result);
    }
    public function updateResult(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCU2Patient::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            // if($sel['data']->status == 'sPresented' || $sel['data']->status == 'sFinished'){
            if($sel['data']->status != 'sFinished'){
                $this->_vars['id'] = $sel['data']->mcu2_event_id;
                $sel2 = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
                if($sel2['success']){
                    $qVars = [];
                    $qVars['mcu_format_id'] = $sel2['data']->mcu2_format_id;
                    $qVars['resultCate'] = $this->_vars['resultCate'];
                    $qVars['orderBy'] = ["a.sort:asc"];
                    $formatItem = MCU2FormatItem::query($qVars, ["mode"=>"rowsAll"]);
                    $formatItemTree = MCU2FormatItem::converToTree($formatItem);
                    // dd($formatItem);

                    $qVars = [];
                    $qVars['mcu2_patient_id'] = $id;
                    $resultData = MCU2Result::query($qVars, ["mode"=>"rowsAll"]);
                    $resultData = MCU2Result::converToCodeValue($resultData);
                    // dd($resultData);

                    $qVars = [];
                    $qVars['mcu_format_id'] = $sel2['data']->mcu2_format_id;
                    $formatTeam = MCU2FormatTeam::query($qVars, ["mode"=>"rowsAll"]);
                    // dd($formatTeam);
                    // dd(json_encode($formatTeam));

                    $this->_vars['mode'] = 'updateResult';
                    $this->_vars['url_save'] = $this->_vars['urlb']."updateResultSave/".$id."?resultCate=".$this->_vars['resultCate'];
                    $this->_vars['patientData'] = json_encode($sel['data']);
                    $this->_vars['formatTeam'] = json_encode($formatTeam);
                    $this->_vars['eventData'] = $sel2['data'];
                    $this->_vars['formatItem'] = $formatItem;
                    $this->_vars['formatItemTree'] = $formatItemTree;
                    $this->_vars['resultData'] = $resultData;
                    return view($this->_viewPath.'v2FormResult', $this->_vars);
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
        // dd($request->all());
        $qVars['id'] = $id;
        $sel = MCU2Patient::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $patientData = $sel['data'];

            $validator = Facades\Validator::make($request->all(), [
                'iiData' => ['required','array'],
                'iiData.*.id' => ['required','integer'],
                'iiData.*.code' => ['required','string'],
                'iiData.*.name' => ['required','string'],
                'iiData.*.input_type' => ['required','string'],
                'iiData.*.result' => ['nullable'],
            ],[
                
            ])->setAttributeNames([
                'iiData' => 'Item Data',
                'iiData.*.id' => 'Item ID',
                'iiData.*.code' => 'Item Code',
                'iiData.*.name' => 'Item Name',
                'iiData.*.input_type' => 'Input Type',
                'iiData.*.result' => 'Item Result'
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
                // dd($request->all());
                $at = date("Y-m-d H:i:s");
    
                DB::beginTransaction();
                try{
                    $requestAll = $request->all();
                    // unset($update['_token']);
                    // unset($update['_method']);

                    // DB::table('mcu2_result_history')
                    // ->where(["mcu2_patient_id"=>$patientData->id, "active"=>1])
                    // ->update(["active"=>0, "delete_by"=>session()->get('sessUsername'), "delete_at"=>$at]);

                    foreach ($requestAll['iiData'] as $k => $v) {
                        $i = [];
                        $i['mcu2_event_id'] = $patientData->mcu2_event_id;
                        $i['mcu2_patient_id'] = $patientData->id;
                        $i['unique'] = $patientData->unique;
                        $i['ktp'] = $patientData->ktp;
                        $i['emp_no'] = $patientData->emp_no;
                        $i['mcu_item_id'] = $v['id'];
                        $i['mcu_item_code'] = $v['code'];
                        $i['mcu_item_name'] = $v['name'];
                        $i['value'] = $v['result'];
                        $i['create_by'] = session()->get('sessUsername');
                        $i['create_at'] = $at;
                        
                        if($v['input_type'] == "IMAGE"){
                            if($v['result'] != null && $v['result'] != ""){
                                $file = $request->file('iiData')[$k]['result'];
                                $mimeType = $file->getMimeType();
                                $size = $file->getSize();
                                $size = $size / 1024 / 1024;
                                $fileData = file_get_contents($file);
                                $filePath = $patientData->mcu2_event_id."/".$v['input_image_folder']."/".$patientData->id.".";
                                if($size <= 2){
                                    if($mimeType == "image/jpg" || $mimeType == "image/jpeg" || $mimeType == "image/png"){
                                        $ext = explode("/",$mimeType);
                                        $ext = $ext[1];
                                        $filePath = $filePath.$ext;
                                        if(env('SYS_CONNECTION') == 'ONLINE'){
                                            Storage::disk('google')->put($filePath, $fileData);
                                        }else{
                                            Storage::disk('local')->put($filePath, $fileData);
                                        }
                                        $i['value'] = $filePath;
                                    }else{
                                        unset($i['value']);
                                        DB::rollback();
                                        $result['success'] = false;
                                        $result['message'] = "Failed, Please choose .jpg / .jpeg / .png image";
                                    }
                                }else{
                                    unset($i['value']);
                                    DB::rollback();
                                    $result['success'] = false;
                                    $result['message'] = "Failed, File Max size 2MB.";
                                }
                            }else{
                                unset($i['value']);
                            }
                        }
                        
                        $w = [];
                        $w['mcu2_patient_id'] = $patientData->id;
                        $w['mcu_item_code'] = $v['code'];

                        DB::table('mcu2_result')->where($w)->delete();
                        DB::table('mcu2_result')->insert($i);

                        $w['active'] = 1;
                        DB::table('mcu2_result_history')->where($w)->update(["active"=>0, "delete_by"=>session()->get('sessUsername'), "delete_at"=>$at]);
                        $i['active'] = 1;
                        DB::table('mcu2_result_history')->insert($i);
                    }

                    // $update['update_by'] = session()->get('sessUsername');
                    // $update['update_at'] = $at;
                    // $update['dob'] = date("Y-m-d", strtotime(str_replace("/","-",$update['dob'])));
                    // $update['schedule_date'] = date("Y-m-d", strtotime(str_replace("/","-",$update['schedule_date'])));
                    // $update['actual_date'] = date("Y-m-d", strtotime(str_replace("/","-",$update['actual_date'])));
                    // $update['fit_date'] = date("Y-m-d", strtotime(str_replace("/","-",$update['fit_date'])));

                    // $update['status_by'] = session()->get('sessUsername');
                    // $update['status_at'] = $at;
                    // if($update['status'] == "sPresented"){
                    //     $update['status_note'] = "Presented";
                    // }
                    // if($update['status'] == "sFinished"){
                    //     $update['status_note'] = "Finished";
                    // }

                    // $where = [
                    //     "id"=>$id,
                    // ];
                    // DB::table('mcu_onsite_event_patient')->where($where)->update($update);

                    // $insert = [];
                    // $insert['mcu_event_patient_id'] = $id;
                    // $insert['status'] = $update['status'];
                    // $insert['status_by'] = session()->get('sessUsername');
                    // $insert['status_at'] = $at;
                    // $insert['status_note'] = $update['status_note'];
                    // DB::table('mcu_event_patient_status')->insert($insert);

                    // $qVars['id'] = $id;
                    // $sel = MCU2Patient::query($qVars, ["mode"=>"rowOne"]);
                    // // dd($sel);
                    // if($sel['success']){
                    //     $v = $sel['data'];
                    //     $update2["emp_no"] = $update['emp_no'];
                    //     $update2["area"] = $update['area'];
                    //     $update2["division"] = $update['division'];
                    //     $update2["position"] = $update['position'];
                    //     $update2["nik"] = $update['nik'];
                    //     $update2["name"] = $update['name'];
                    //     $update2["gender"] = $update['gender'];
                    //     $update2["dob"] = $update['dob'];
                    //     $update2["phone"] = $update['phone'];
                    //     $update2["address"] = $update['address'];
                    //     $update2['update_by'] = session()->get('sessUsername');
                    //     $update2['update_at'] = $at;
                    //     $where2 = [
                    //         "id"=>$v->corporate_emp_id,
                    //     ];
                    //     DB::table('corporate_emp')->where($where2)->update($update2);
                    // }
    
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
            $result['success'] = false;
            $result['message'] = "Patient Not Found.";
        }
        echo json_encode($result);
    }
    public function delete(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCU2Patient::query($this->_vars, ["mode"=>"rowOne"]);
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
                    DB::table('mcu2_patient')->where($where)->update($update);
                    
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
    public function patientImport(Request $request, $id){
        $this->_vars['id'] = $id;
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'add';
            $this->_vars['url_save'] = $this->_vars['urlb']."patientImportSave/".$id;
            $this->_vars['eventData'] = $sel['data'];
            return view($this->_viewPath.'v2FormImport', $this->_vars);
        }else{
            return $sel['message'];
        }
    }
    public function patientImportSave(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
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

                        $itemCodeList = $sheetData[1];
                        foreach ($sheetData as $k => $v) {
                            if($k >= 3){

                                if($v[0] != "" && $v[0] != null){

                                    $i = [
                                        "mcu2_event_id"=>$id,
                                        "unique"=>$v[0],
                                        "schedule_date"=>Spreadsheet_Cell_Date::excelToDateTimeObject($v[1])->format('Y-m-d'),
                                        "mcu2_format_package_code"=>$v[2],
                                        "mcu2_format_ref_code"=>$v[3],
                                        "name"=>$v[4],
                                        "gender"=>$v[5],
                                        "dob"=>Spreadsheet_Cell_Date::excelToDateTimeObject($v[6])->format('Y-m-d'),
                                        "ktp"=>$v[7],
                                        "phone"=>$v[8],
                                        "email"=>$v[9],
                                        "address"=>$v[10],
                                        "emp_no"=>$v[11],
                                        "emp_area"=>$v[12],
                                        "emp_div"=>$v[13],
                                        "emp_pos"=>$v[14],
                                        "status"=>"sScheduled",
                                        "status_at"=>$at,
                                        "status_by"=>session()->get('sessUsername'),
                                        "create_at"=>$at,
                                        "create_by"=>session()->get('sessUsername')
                                    ];

                                    DB::table('mcu2_patient')->insert($i);
                                }   

                                

                                // DB::table('mcu2_result_history')
                                // ->where(["mcu2_patient_id"=>$v[0], "active"=>1])
                                // ->update(["active"=>0, "delete_by"=>session()->get('sessUsername'), "delete_at"=>$at]);

                                // foreach ($itemCodeList as $kCode => $vCode) {
                                //     $itemCode = $vCode;
                                //     $itemValue = $v[$kCode];
                                //     if($itemCode != "" && $itemCode != null){
                                //         $i = [];
                                //         $i['mcu2_event_id'] = $id;
                                //         $i['mcu2_patient_id'] = $v[0];
                                //         // $i['unique'] = "";
                                //         // $i['ktp'] = "";
                                //         // $i['emp_no'] = "";
                                //         // $i['mcu_item_id'] = $v['id'];
                                //         $i['mcu_item_code'] = $itemCode;
                                //         // $i['mcu_item_name'] = $v['name'];
                                //         $i['value'] = $itemValue;
                                //         $i['create_by'] = session()->get('sessUsername');
                                //         $i['create_at'] = $at;
                                        
                                //         $w['mcu2_patient_id'] = $v[0];
                                //         $w['mcu_item_code'] = $itemCode;

                                //         DB::table('mcu2_result')->where($w)->delete();
                                //         DB::table('mcu2_result')->insert($i);
                                //         $i['active'] = 1;
                                //         DB::table('mcu2_result_history')->insert($i);
                                //     }
                                // }

                                // if($v[15] == "YES"){
                                //     $insert = [];
                                //     $insert['mcu_event_patient_id'] = $v[0];
                                //     $insert['status'] = "sFinished";
                                //     $insert['status_by'] = session()->get('sessUsername');
                                //     $insert['status_at'] = $at;
                                //     $insert['status_note'] = "Import Result.";
                                //     DB::table('mcu_event_patient_status')->insert($insert);
                                // }
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
    public function patientImportTemplate(Request $request){
        $fieldList = MCU2Patient::excelFieldImportTemplate();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $sheetIndex = -1;

        $sheetIndex++;
        $createSheet = new Worksheet($spreadsheet, 'data');
        $spreadsheet->addSheet($createSheet, $sheetIndex);
        $spreadsheet->setActiveSheetIndex($sheetIndex);

        $c = $spreadsheet->getActiveSheet();

        $xr = 1; $xc = 0; $xc++;
        $c->setCellValue(MainHelper::getCol($xc).$xr, 'MCU Patient Template Import');

        // ------------------------------------------------------------------------------------ HEADER
        $excelParams = ['xr'=>3, 'xc'=>1, 'fieldList'=>$fieldList];
        $lastRowCol = MainHelper::excelHeader($c, $excelParams);
        // ------------------------------------------------------------------------------------ HEADER //

        $fileName = "MCUPatientImportTemplate_".uniqid().".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer = Spreadsheet_IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    public function patientExport(Request $request){
        $qVars['id'] = $this->_vars['mcu_event_id'];
        // $qVars['orderBy'] = ['sa.order:asc','ST:asc','a.create_at:desc'];
        $event = MCU2Event::query($qVars, ["mode"=>"rowOne"]);
        if($event['success']){
            $eventData = $event['data'];

            // $this->_vars['orderBy'] = ['sa.order:asc','a.create_at:desc'];
            // $this->_vars['orderBy'] = ['a.schedule_date:asc','a.reg_num:asc','a.id:asc'];
            $this->_vars['orderBy'] = ['a.id:asc'];
            $contentList = MCU2Patient::query($this->_vars, ["mode"=>"rowsAll"]);
            $fieldList = MCU2Patient::excelField();
            // dd($contentList);

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
    private function generateConclusion($eventData = [], $mcuResult = []){
        $conclusion = "";
        $suggestion = "";
        $fitstatus = "";

        $GLOBALS['ITEM_VALUE'] = $mcuResult;

        $qVars['mcu_format_id'] = @$eventData['mcu2_format_id'];
        $qVars['orderBy'] = ['a.sort:asc'];
        $formatConclusion = MCU2FormatConclusion::query($qVars, ["mode"=>"rowsAll"]);
        $formatConclusion_show = [];
        foreach ($formatConclusion as $k => $v) {
            eval($v->formula);
            if(@$e_value == 1){
                if(isset($e_title) && $e_title != null && $e_title != ""){
                    $v->title = $e_title;
                }
                if(isset($e_suggestion) && $e_suggestion != null && $e_suggestion != ""){
                    $v->suggestion = $e_suggestion;
                }
                $formatConclusion_show[$v->id] = $v;

                if($v->remove_id != null && $v->remove_id != ""){
                    $removeIdArray = explode(",",$v->remove_id);
                    foreach ($removeIdArray as $kRID => $vRID) {
                        if(isset($formatConclusion_show[$vRID])){
                            unset($formatConclusion_show[$vRID]);
                        }
                    }
                }
            }
        }
        // dd($formatConclusion_show);
        foreach ($formatConclusion_show as $k => $v) {
            $conclusion .= $v->title;
            $conclusion .= "\r\n";

            $suggestion .= "<b>".$v->title."</b>";
            $suggestion .= "\r\n";
            if($v->suggestion != null && $v->suggestion != ""){
                $suggestion .= $v->suggestion;
                $suggestion .= "\r\n";
                $suggestion .= "\r\n";
            }else{
                $suggestion .= "\r\n";
            }
        }
        // dd($conclusion, $suggestion);

        $qVars['mcu_format_id'] = @$eventData['mcu2_format_id'];
        $qVars['orderBy'] = ['a.id:asc'];
        $formatFitstatus = MCU2FormatFitstatus::query($qVars, ["mode"=>"rowsAll"]);
        $formatFitstatus_show = [];
        foreach ($formatFitstatus as $k => $v) {
            eval($v->formula);
            if(@$e_value == 1){
                $formatFitstatus_show[$v->id] = $v;

                if($v->remove_id != null && $v->remove_id != ""){
                    $removeIdArray = explode(",",$v->remove_id);
                    foreach ($removeIdArray as $kRID => $vRID) {
                        if(isset($formatFitstatus_show[$vRID])){
                            unset($formatFitstatus_show[$vRID]);
                        }
                    }
                }
            }
        }
        // dd($formatFitstatus_show);
        foreach ($formatFitstatus_show as $k => $v) {
            $fitstatus = $v->title;
        }
        // dd($fitstatus);

        $insert_mcuResult['SYS___CONCLUSION'] = $conclusion;
        $insert_mcuResult['SYS___SUGGESTION'] = $suggestion;
        $insert_mcuResult['SYS___FITSTATUS'] = $fitstatus;

        DB::beginTransaction();
        try {
            $at = date("Y-m-d H:i:s");
            foreach ($insert_mcuResult as $k => $v) {
                $i = [];
                $i['mcu2_event_id'] = $mcuResult['mcu2_event_id'];
                $i['mcu2_patient_id'] = $mcuResult['id'];
                $i['unique'] = $mcuResult['unique'];
                $i['ktp'] = $mcuResult['ktp'];
                $i['emp_no'] = $mcuResult['emp_no'];
                $i['mcu_item_id'] = 0;
                $i['mcu_item_code'] = $k;
                $i['mcu_item_name'] = $k;
                $i['value'] = $v;
                $i['create_by'] = session()->get('sessUsername');
                $i['create_at'] = $at;
                
                $w = [];
                $w['mcu2_patient_id'] = $mcuResult['id'];
                $w['mcu_item_code'] = $k;
    
                DB::table('mcu2_result')->where($w)->delete();
                DB::table('mcu2_result')->insert($i);

                $w['active'] = 1;
                DB::table('mcu2_result_history')->where($w)->update(["active"=>0, "delete_by"=>session()->get('sessUsername'), "delete_at"=>$at]);
                $i['active'] = 1;
                DB::table('mcu2_result_history')->insert($i);
            }
            DB::table("mcu2_patient")->where(["id"=>$mcuResult['id']])->update(["generate_conclusion"=>1, "generate_conclusion_at"=>$at]);
            
            DB::commit();
            $result['success'] = true;
            $result['message'] = "Generate Conclusion Failed.";
            $result['data'] = $insert_mcuResult;
        } catch(\Exception $ex){
            // throw $ex;
            DB::rollback();
            $result['success'] = false;
            $result['message'] = "Generate Conclusion Failed.";
        }
        return $result;

    }
    public function conclusionGenerate(Request $request){
        $requestAll = $request->all();
        $qVars['id'] = $this->_vars['mcu_event_id'];
        $event = MCU2Event::query($qVars, ["mode"=>"rowOne"]);
        if($event['success']){
            $eventData = $event['data'];
            
            $qVars = [];
            $qVars['mcu2_event_id'] = $requestAll['mcu_event_id'];
            $qVars['idList'] = explode(",", $requestAll['patientIdList']);
            $qVars['orderBy'] = ['a.schedule_date:asc','a.reg_num:asc','a.id:asc'];
            $patientList = MCU2Patient::query($qVars, ["mode"=>"rowsAll", "withMCUResult"=>true]);

            if(count($patientList) > 0){
                try {
                    foreach ($patientList as $kP => $vP) {
                        $conclusion = self::generateConclusion((array)$eventData, (array)$vP);
                        if($conclusion['success']){
                            foreach ($conclusion['data'] as $kC => $vC) {
                                $vP->$kC = $vC;
                            }
                        }
                    }
                    $result['success'] = false;
                    $result['status_code'] = 200;
                    $result['message'] = "Generate Conclusion Success.";
                } catch (\Exception $ex) {
                    // throw $ex;
                    $result['success'] = false;
                    $result['status_code'] = $ex->getCode();
                    $result['message'] = "Generate Conclusion Failed.";
                }
            }else{
                $result['success'] = false;
                $result['status_code'] = 400;
                $result['message'] = "MCU Patient not found.";
            }
        }else{
            $result['success'] = false;
            $result['status_code'] = 400;
            $result['message'] = "MCU Event not found.";
        }
        echo $result['message'];
    }
    public function resultVerify(Request $request){
        $requestAll = $request->all();
        $qVars['id'] = $this->_vars['mcu_event_id'];
        $event = MCU2Event::query($qVars, ["mode"=>"rowOne"]);
        if($event['success']){
            $eventData = $event['data'];

            $qVars = [];
            $qVars['mcu2_event_id'] = $requestAll['mcu_event_id'];
            // $qVars['idList'] = explode(",", $requestAll['patientIdList']);
            $qVars['idList'] = json_decode($requestAll['patientIdList'],true);
            // $qVars['orderBy'] = ['a.schedule_date:asc','a.reg_num:asc','a.id:asc'];
            $qVars['status'] = "sFinished";
            $patientList = MCU2Patient::query($qVars, ["mode"=>"rowsAll"]);

            if(count($patientList) > 0){
                $at = date("Y-m-d H:i:s");
                DB::beginTransaction();
                try {
                    $total = 0;
                    foreach ($patientList as $kP => $vP) {
                        if($vP->verify_by == "" || $vP->verify_by == null){
                            $where = [];
                            $where = ['id'=>$vP->id];
                            $update = [];
                            $update = [
                                "verify_by"=>session()->get('sessUsername'),
                                "verify_at"=>$at
                            ];
                            DB::table('mcu2_patient')->where($where)->update($update);
                            $total++;
                        }
                    }

                    if($total > 0){
                        DB::commit();
                        $result['success'] = true;
                        $result['status_code'] = 200;
                        $result['message'] = "Verify ".$total." Data Success";
                    }else{
                        DB::rollback();
                        $result['success'] = false;
                        $result['status_code'] = 400;
                        $result['message'] = "Sorry, All data had been Verified before.";
                    }
                } catch (\Exception $ex) {
                    // throw $ex;
                    DB::rollback();
                    $result['success'] = false;
                    $result['status_code'] = $ex->getCode();
                    $result['message'] = "Failed.";
                }
            }else{
                $result['success'] = false;
                $result['status_code'] = 400;
                $result['message'] = "No Finish data found.";
            }
        }else{
            $result['success'] = false;
            $result['status_code'] = 400;
            $result['message'] = "MCU Event not found.";
        }
        echo json_encode($result);
    }
    public function resultPrint(Request $request){
        $requestAll = $request->all();
        $qVars['id'] = $this->_vars['mcu_event_id'];
        $event = MCU2Event::query($qVars, ["mode"=>"rowOne"]);
        if($event['success']){
            $eventData = $event['data'];
            // dd(explode(",", $requestAll['patientIdList']));

            // $this->_vars['orderBy'] = ['sa.order:asc','a.create_at:desc'];
            $qVars = [];
            $qVars['mcu2_event_id'] = $requestAll['mcu_event_id'];
            $qVars['idList'] = explode(",", $requestAll['patientIdList']);
            $qVars['orderBy'] = ['a.schedule_date:asc','a.reg_num:asc','a.id:asc'];
            $patientList = MCU2Patient::query($qVars, ["mode"=>"rowsAll", "withMCUResult"=>true]);
            // $fieldList = MCU2Patient::excelField();
            // dd($contentList);

            $qVars = [];
            $qVars['mcu_format_id'] = $eventData->mcu2_format_id;
            if($this->_vars['resultCate'] > -1){
                $qVars['resultCate'] = $this->_vars['resultCate'];
            }
            $qVars['orderBy'] = ["a.sort:asc"];
            $formatItem = MCU2FormatItem::query($qVars, ["mode"=>"rowsAll"]);
            $formatItem = MCU2FormatItem::converToTree($formatItem);
            // $formatItemField = MCU2FormatItem::converToExcelField($formatItem);
            // dd($formatItem);

            $qVars = [];
            $qVars['mcu_format_id'] = $eventData->mcu2_format_id;
            $formatRef = MCU2FormatRefItem::query($qVars, ["mode"=>"rowsAll"]);
            $formatRef2 = [];
            foreach ($formatRef as $k => $v) {
                $formatRef2[$v->mcu2_format_ref_code][$v->item_code] = $v;
            }
            // dd($formatRef2);

            $qVars = [];
            $qVars['mcu_format_id'] = $eventData->mcu2_format_id;
            $formatTeam = MCU2FormatTeam::query($qVars, ["mode"=>"rowsAll"]);
            $formatTeam2 = [];
            foreach ($formatTeam as $k => $v) {
                $formatTeam2[$v->id] = $v;
            }
            // dd($formatTeam2);

            if(count($patientList) > 0){
                try {
                    $files = [];

                    foreach ($patientList as $kP => $vP) {
                        // dd($vP);

                        if($vP->generate_conclusion == 0){
                            $conclusion = self::generateConclusion((array)$eventData, (array)$vP);
                            if($conclusion['success']){
                                foreach ($conclusion['data'] as $kC => $vC) {
                                    $vP->$kC = $vC;
                                }
                            }
                        }
        
                        $PDFPageIndex = -1;
                        if($this->_vars['resultCate'] == -1 || $this->_vars['resultCate'] == 0){
                            $PDFPageIndex++;
                            $PDFTemplate = $this->_viewPath."v2mcuESCover";
                            echo view($PDFTemplate, ['SEGMENT'=>'MPDF_CONFIG']);
                            if($PDFPageIndex == 0){
                                $mpdf = new PDF($GLOBALS['MPDF_CONFIG']);
                            }else{
                                $mpdf->AddPageByArray($GLOBALS['MPDF_CONFIG_PAGE']);
                            }
                            $viewData['mcuEvent'] = $eventData;
                            $viewData['mcuPatient'] = $vP;
                            $mpdf->WriteHTML(view($PDFTemplate, $viewData));
                        }
        
                        foreach ($formatItem as $kFI => $vFI) {
                            $PDFPageIndex++;
                            if($vFI->view_es != "" && $vFI->view_es != ""){
                                $PDFTemplate = $this->_viewPath.$vFI->view_es;
                                echo view($PDFTemplate, ['SEGMENT'=>'MPDF_CONFIG']);
                                if($PDFPageIndex == 0){
                                    $mpdf = new PDF($GLOBALS['MPDF_CONFIG']);
                                }else{
                                    $mpdf->AddPageByArray($GLOBALS['MPDF_CONFIG_PAGE']);
                                }
                                $viewData['mcuEvent'] = $eventData;
                                $viewData['mcuPatient'] = $vP;
                                $viewData['mcuItem'] = $vFI;
                                $viewData['mcuRef'] = @$formatRef2[@$vP->mcu2_format_ref_code];
                                $viewData['mcuTeam'] = $formatTeam2;

                                // dd(file_get_contents("https://lh3.googleusercontent.com/u/1/drive-viewer/AK7aPaAldiDgpBSSxi64a_vRUeXWj6VY0L3q-s7xuAUh9F8TPHzQzULt8hPzqs9ZUBUsV6Ba4GO49Kp4xZ2TLIcOQOh0WKUBjw=w1920-h955"));

                                // dd($viewData);
                                $mpdf->WriteHTML(view($PDFTemplate, $viewData));
                            }
                        }
        
                        $fileName = $vP->name."_".MainHelper::uuid().".pdf";
                        $path = "temp/".$fileName;
                        
                        ob_start();
                        $mpdf->Output("./","I");
                        $content = ob_get_contents();
                        ob_end_clean();
        
                        $save = Storage::disk('local')->put($path, $content);
                        if($save){
                            $files[] = $path;
                        }
                    }

                    $createFiles = self::createFiles($request, $files);
                    $result = $createFiles;
                    if($result['success']){
                        return redirect($result['file_download']);
                    }
                } catch (\Exception $ex) {
                    throw $ex;
                    $result['success'] = false;
                    $result['status_code'] = $ex->getCode();
                    $result['message'] = "Failed.";
                }
            }else{
                $result['success'] = false;
                $result['status_code'] = 400;
                $result['message'] = "MCU Patient not found.";
            }
        }else{
            // return "MCU Event not found.";
            $result['success'] = false;
            $result['status_code'] = 400;
            $result['message'] = "MCU Event not found.";
        }
        echo $result['message'];
    }
    public function createFiles($request, $files){
        if(count($files) > 0){
            if(count($files) == 1){
                // $urlDownload = url::to('/api/download')."?path=".$files[0]."&d=temp&c=1";
                $urlDownload = "/base/download?path=".$files[0]."&d=temp&c=1";
                $urlPreview = "/base/preview?path=".$files[0]."&d=temp&c=1";
                return [
                    "success"=>true,
                    "status_code"=>201,
                    "status_name"=>"SUCCESS",
                    // "file_path"=>$files[0],
                    "file_download"=>$urlDownload,
                    "file_preview"=>$urlPreview
                ];
            }else{
                $zip = new \ZipArchive();
                $zipName = "mcuES_ZIP_".MainHelper::uuid().".zip";
                $zipFile = "temp/".$zipName;
                $zip->open(storage_path("app/".$zipFile), $zip::CREATE);

                foreach ($files as $k => $v) {
                    $zip->addFile(storage_path("app/".$v), basename($v));
                }
                $zip->close();
                foreach ($files as $k => $v) {
                    Storage::disk('local')->delete($v);
                }

                // $urlDownload = url::to('/api/download')."?path=".$zipFile."&d=temp&c=1";
                $urlDownload = "/base/download?path=".$zipFile."&d=temp&c=1";
                $urlPreview = "/base/preview?path=".$zipFile."&d=temp&c=1";
                return [
                    "success"=>true,
                    "status_code"=>201,
                    "status_name"=>"SUCCESS",
                    // "file_path"=>$zipFile,
                    "file_download"=>$urlDownload,
                    "file_preview"=>$urlPreview
                ];
            }
        }else{
            return [
                "success"=>false,
                "status_code"=>400,
                "status_name"=>"BAD_REQUEST",
                "messages"=>"Not Data to be print"
            ];            
        }
    }
    public function resultExport(Request $request){
        $qVars['id'] = $this->_vars['mcu_event_id'];
        $event = MCU2Event::query($qVars, ["mode"=>"rowOne"]);
        if($event['success']){
            $eventData = $event['data'];
            // dd($eventData);

            // $this->_vars['orderBy'] = ['sa.order:asc','a.create_at:desc'];
            $this->_vars['orderBy'] = ['a.schedule_date:asc','a.reg_num:asc','a.id:asc'];
            $contentList = MCU2Patient::query($this->_vars, ["mode"=>"rowsAll", "withMCUResult"=>true]);
            $fieldList = MCU2Patient::excelField();
            // dd($contentList);
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

            // ------------------------------------------------------------------------------------ HEADER
            $excelParams = ['xr'=>3, 'xc'=>1, 'fieldList'=>$fieldList];
            $lastRowCol = MainHelper::excelHeader($c, $excelParams);
            // ------------------------------------------------------------------------------------ HEADER //
            // $excelParams = ['xr'=>4, 'xc'=>1, 'fieldList'=>$fieldList, 'contentList'=>$contentList];
            // $lastRowCol = MainHelper::excelContent($c, $excelParams);

            // if($this->_vars['resultCate'] == -1){
            //     $formatItemField = [];
            //     $row['field'] = "fit_cate";
            //     $row['title'] = "FIT STATUS";
            //     $row['width'] = 160;
            //     $row['dataType'] = "STRING";
            //     array_push($formatItemField, $row);
            //     $row['field'] = "fit_note";
            //     $row['title'] = "FIT NOTE";
            //     $row['width'] = 160;
            //     $row['dataType'] = "STRING";
            //     array_push($formatItemField, $row);
            // }else{
            //     $qVars = [];
            //     $qVars['mcu_format_id'] = $eventData->mcu2_format_id;
            //     $qVars['resultCate'] = $this->_vars['resultCate'];
            //     $formatItem = MCU2FormatItem::query($qVars, ["mode"=>"rowsAll"]);
            //     $formatItem = MCU2FormatItem::converToTree($formatItem);
            //     $formatItemField = MCU2FormatItem::converToExcelField($formatItem);
            //     // dd($formatItemField);
            // }

            // ------------------------------------------------------------------------------------ HEADER ITEM
            $qVars = [];
            $qVars['mcu_format_id'] = $eventData->mcu2_format_id;
            $qVars['resultCate'] = $this->_vars['resultCate'];
            $qVars['orderBy'] = ["a.sort:asc"];
            $formatItem = MCU2FormatItem::query($qVars, ["mode"=>"rowsAll"]);
            $formatItem = MCU2FormatItem::converToTree($formatItem);
            $formatItemField = MCU2FormatItem::converToExcelField($formatItem);
            $formatItemField[] = [
                "field"=>"SYS___".$qVars['resultCate']."_EXAMINER_ID",
                "title"=>"PEMERIKSA (ID TEAM)",
                "align"=>"CENTER",
                "width"=>200,
                "dataType"=>"STRING"
            ];

            $excelParams = ['xr'=>3, 'xc'=>$lastRowCol['xc']+1, 'fieldList'=>$formatItemField];
            MainHelper::excelHeader($c, $excelParams);
            // ------------------------------------------------------------------------------------ HEADER ITEM //
            // ------------------------------------------------------------------------------------ HEADER ITEM CODE
            $formatItemFieldCODE = [];
            foreach ($formatItemField as $k => $v) {
                $v['title'] = $v['field'];
                unset($v['align']);
                array_push($formatItemFieldCODE, $v);
            }
            $excelParams = ['xr'=>2, 'xc'=>$lastRowCol['xc']+1, 'fieldList'=>$formatItemFieldCODE];
            MainHelper::excelHeader($c, $excelParams);
            // ------------------------------------------------------------------------------------ HEADER ITEM CODE //

            // dd(array_merge($fieldList, $formatItemFieldCODE), $contentList);
            // ------------------------------------------------------------------------------------ CONTENT & CONTENT RESULT
            $excelParams = ['xr'=>4, 'xc'=>1, 'fieldList'=>array_merge($fieldList, $formatItemFieldCODE), 'contentList'=>$contentList];
            $lastRowCol = MainHelper::excelContent($c, $excelParams);
            // ------------------------------------------------------------------------------------ CONTENT & CONTENT RESULT//

            $fileName = "MCUPatientResult_".uniqid().".xlsx";
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$fileName.'"');
            header('Cache-Control: max-age=0');

            $writer = Spreadsheet_IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }else{
            return "MCU Event not found.";
        }
    }
    public function resultImport(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'add';
            $this->_vars['url_save'] = $this->_vars['urlb']."resultImportSave/".$id;
            $this->_vars['eventData'] = $sel['data'];
            return view($this->_viewPath.'v2FormImportResult', $this->_vars);
        }else{
            return $sel['message'];
        }
    }
    public function resultImportSave(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
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

                        

                        $itemCodeList = $sheetData[1];
                        foreach ($sheetData as $k => $v) {
                            if($k >= 3){
                                DB::table('mcu2_result_history')
                                ->where(["mcu2_patient_id"=>$v[0], "active"=>1])
                                ->update(["active"=>0, "delete_by"=>session()->get('sessUsername'), "delete_at"=>$at]);

                                foreach ($itemCodeList as $kCode => $vCode) {
                                    $itemCode = $vCode;
                                    $itemValue = $v[$kCode];
                                    if($itemCode != "" && $itemCode != null){
                                        $i = [];
                                        $i['mcu2_event_id'] = $id;
                                        $i['mcu2_patient_id'] = $v[0];
                                        // $i['unique'] = "";
                                        // $i['ktp'] = "";
                                        // $i['emp_no'] = "";
                                        // $i['mcu_item_id'] = $v['id'];
                                        $i['mcu_item_code'] = $itemCode;
                                        // $i['mcu_item_name'] = $v['name'];
                                        $i['value'] = $itemValue;
                                        $i['create_by'] = session()->get('sessUsername');
                                        $i['create_at'] = $at;
                                        
                                        $w['mcu2_patient_id'] = $v[0];
                                        $w['mcu_item_code'] = $itemCode;

                                        DB::table('mcu2_result')->where($w)->delete();
                                        DB::table('mcu2_result')->insert($i);
                                        $i['active'] = 1;
                                        DB::table('mcu2_result_history')->insert($i);
                                    }
                                }

                                // if($v[15] == "YES"){
                                //     $insert = [];
                                //     $insert['mcu_event_patient_id'] = $v[0];
                                //     $insert['status'] = "sFinished";
                                //     $insert['status_by'] = session()->get('sessUsername');
                                //     $insert['status_at'] = $at;
                                //     $insert['status_note'] = "Import Result.";
                                //     DB::table('mcu_event_patient_status')->insert($insert);
                                // }
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
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
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
        $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
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
                $sel = MCU2Event::query($this->_vars, ["mode"=>"rowOne"]);
                if($sel['success']){
                    $otherEventData = $sel['data'];
                    $at = date("Y-m-d H:i:s");

                    $patientIdList = $request->all()['patientIdList'];
                    $patientIdList = explode(",",$patientIdList);
                    $qVars['patientIdList'] = $patientIdList;
                    $qVars['status'] = "sScheduled";
                    $patientData = MCU2Patient::query($qVars, ["mode"=>"rowsAll"]);

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
                            DB::table('mcu_onsite_event_patient')->where($where)->update($update);
                            
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
        $sel = MCU2Event::query($qVars, ["mode"=>"rowOne"]);
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
        $event = MCU2Event::query($qVars, ["mode"=>"rowOne"]);
        if($event['success']){
            $qVars['id'] = $id;
            $patient = MCU2Patient::query($qVars, ["mode"=>"rowOne"]);
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
                DB::table('mcu_onsite_event_patient')->where($where)->update($update);

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
                DB::table('mcu_onsite_event_patient')->where($where)->update($update);

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
