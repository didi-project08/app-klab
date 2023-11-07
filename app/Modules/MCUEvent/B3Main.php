<?php
namespace App\Modules\MCUEvent;

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

class B3Main extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCUEvent/";
        $this->_vars["_id_"] = "M2";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b3";
        $this->_vars['url'] = URL::to('/')."/mcu-event/";
        $this->_vars['urlb'] = $this->_vars['url']."b3/";
    }
    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        return view($this->_viewPath.'v3Main', $this->_vars);
    }
    public function filter(){
        return view($this->_viewPath.'v3Filter', $this->_vars);
    }
    public function read(){
        $this->_vars['userCorporate'] = ModelBase::userCorporate();
        $this->_vars['orderBy'] = ['allowRegistMCU:desc'];
        $result['rows'] = CorporateEmp::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = CorporateEmp::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function add(Request $request, $mcu_event_id = 0){
        $this->_vars['id'] = $mcu_event_id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'add';
            $this->_vars['url_save'] = $this->_vars['urlb']."create/".$mcu_event_id;
            $this->_vars['eventData'] = $sel['data'];
            return view($this->_viewPath.'v3Form', $this->_vars);
        }else{
            return $sel['message'];
        }
    }
    public function create(Request $request, $mcu_event_id = 0){
        $this->_vars['id'] = $mcu_event_id;
        $sel = MCUEvent::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $validatorList = [
                'corpEmpIdList' => ['required','string'],
                'mcu_format_package_id' => ['required','integer'],
                'schedule_date' => ['required','date_format:d/m/Y'],
                'actual_date' => ['required','date_format:d/m/Y']
            ];
            $validator = Facades\Validator::make($request->all(), $validatorList,[
                'schedule_date.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
                'actual_date.date_format' => "The :attribute does not match the format dd/mm/yyyy."
            ])->setAttributeNames([
                'corpEmpIdList' => 'Please Checklist Employee to proses',
                'mcu_format_package_id' => 'Package',
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
                $eventData = $sel['data'];

                $corpEmpIdList = $request->all()['corpEmpIdList'];
                $corpEmpIdList = explode(",",$corpEmpIdList);
                $qVars['userCorporate'] = ModelBase::userCorporate();
                $qVars['corpEmpIdList'] = $corpEmpIdList;
                $corpEmpData = CorporateEmp::query($qVars, ["mode"=>"rowsAll"]);

                $mcu_format_package_id = $request->all()['mcu_format_package_id'];
                $qVars['id'] = $mcu_format_package_id;
                $packageData = MCUFormatPackage::query($qVars, ["mode"=>"rowOne"]);

                if($packageData['success']){
                    DB::beginTransaction();
                    try{
                        $pSuccess = 0;
                        $pNotAllow = 0;
                        $pExist = 0;
                        foreach ($corpEmpData as $k => $v) {
                            if($v->allowRegistMCU == 1){
                                $qVars = [];
                                $qVars["mcu_event_id"] = $mcu_event_id;
                                $qVars["corporate_emp_id"] = $v->id;
                                $selPatient = MCUEventPatient::query($qVars, ["mode"=>"rowOne"]);
                                if($selPatient['success'] == false){
                                    $insert = [];
                                    $insert["mcu_event_id"] = $mcu_event_id;
                                    $insert["type"] = 1;
                                    $insert["corporate_emp_id"] = $v->id;
                                    $insert["emp_no"] = $v->emp_no;
                                    $insert["area"] = $v->area;
                                    $insert["division"] = $v->division;
                                    $insert["position"] = $v->position;
                                    $insert["nik"] = $v->nik;
                                    $insert["name"] = $v->name;
                                    $insert["gender"] = $v->gender;
                                    $insert["dob"] = $v->dob;
                                    $insert["phone"] = $v->phone;
                                    $insert["address"] = $v->address;
                                    // $insert["mcu_format_package_id"] = 0;
                                    $insert['mcu_format_package_id'] = $request->all()['mcu_format_package_id'];
                                    $insert['price_lab'] = $packageData['data']->price_lab;
                                    $insert['price_corporate'] = $packageData['data']->price_corporate;
                                    $insert["schedule_date"] = date("Y-m-d", strtotime(str_replace("/","-",$request->all()['schedule_date'])));
                                    $insert["actual_date"] = date("Y-m-d", strtotime(str_replace("/","-",$request->all()['actual_date'])));
                                    $insert['create_by'] = session()->get('sessUsername');
                                    $insert['create_at'] = $at;
                                    $insert['status'] = "sScheduled";
                                    $insert['status_by'] = session()->get('sessUsername');
                                    $insert['status_at'] = $at;
                                    $insert['status_note'] = "New Patient From Corporate Employee";
                                    DB::table('mcu_event_patient')->insert($insert);
                                    $lastInsertId = DB::getPdo()->lastInsertId();
        
                                    $insert = [];
                                    $insert['mcu_event_patient_id'] = $lastInsertId;
                                    $insert['status'] = "sScheduled";
                                    $insert['status_by'] = session()->get('sessUsername');
                                    $insert['status_at'] = $at;
                                    $insert['status_note'] = "New Patient From Corporate Employee";
                                    DB::table('mcu_event_patient_status')->insert($insert);
        
                                    $where = [];
                                    $where['corporate_emp_id'] = $v->id;
                                    DB::table('mcu_event_corporate_emp')->where($where)->delete();
        
                                    $insert = [];
                                    $insert['corporate_emp_id'] = $v->id;
                                    $insert['mcu_event_id'] = $mcu_event_id;
                                    $insert['mcu_event_patient_id'] = $lastInsertId;
                                    DB::table('mcu_event_corporate_emp')->insert($insert);
    
                                    $pSuccess++;
                                }else{
                                    $pExist++;
                                }
                            }else{
                                $pNotAllow++;
                            }
                        }
                        DB::commit();
                        $result['success'] = true;
                        $result['message'] = "Success : ".$pSuccess.", Exist : ".$pExist.", Not-Allowed : ".$pNotAllow;
                    } catch(\Exception $ex){
                        throw $ex;
                        DB::rollback();
                        $result['success'] = false;
                        $result['message'] = "Create Failed.";
                    }
                }else{

                }
            }
        }else{
            DB::rollback();
            $result['success'] = false;
            $result['message'] = $sel['message'];
        }
        echo json_encode($result);
    }
}
