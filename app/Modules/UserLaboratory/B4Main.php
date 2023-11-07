<?php
namespace App\Modules\UserLaboratory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
// use Validator;
// use Illuminate\Support\Facades;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades;
use Illuminate\Validation\Validator;

use App\Modules\BASE\MY_Controller;
use App\Models\ModelBase;
use App\Helpers\Main AS MainHelper;

use App\Models\ModuleAccess;
use App\Models\RoleModuleAccess;
use App\Models\UserAccountCorporate;
use App\Models\UserCorporateClient;
use App\Models\RoleAccountLaboratory;

class B4Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "UserLaboratory/";
        $this->_vars["_id_"] = "M7";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b4";
        $this->_vars['url'] = URL::to('/')."/user-laboratory/";
        $this->_vars['urlb'] = $this->_vars['url']."b4/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        return view($this->_viewPath.'v4Main', $this->_vars);
    }

    public function filter(){
        return view($this->_viewPath.'v4Filter', $this->_vars);
    }
    public function read(){
        $this->_vars['f_show_to'] = "#2";
        $result = ModuleAccess::query($this->_vars, ["mode"=>"rowsAll"]);
        $resultNew = ModuleAccess::converToTree($result);

        $resultNew2[0] = ["id"=>-1, "name"=>"<b>-- SELECT ALL --<b>"];
        $resultNew2[0]['children'] = $resultNew;
        $resultNew2[0]['rowsChecked'] = RoleModuleAccess::query($this->_vars, ["mode"=>"rowsAll"]);
        
        $idList = [];
        foreach ($result as $k => $v) {
            $idList[$v->f_module_access_id] = $v->f_module_access_id;
        }
        $resultNew2[0]['idList'] = $idList;

        echo json_encode($resultNew2);
    }
    public function roleModuleAccessUpdate(Request $request, $f_role_id = 0){
        // dd($request->all());
        $qVars['f_role_id'] = $f_role_id;
        $sel = RoleAccountLaboratory::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $validatorList = [
                // 'roleModuleIdList' => ['required'],
                // 'roleModuleAccessIdList' => ['required'],
            ];
            $validator = Facades\Validator::make($request->all(), $validatorList)->setAttributeNames([
                // 'corpModuleIdList' => 'Please Checklist Module Access to proses',
                // 'roleModuleAccessIdList' => 'Please Checklist Module Access to proses',
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
                $roleModuleIdList = [];
                $roleModuleAccessIdList = [];
                if(isset($request->all()['roleModuleIdList'])){
                    $roleModuleIdList = $request->all()['roleModuleIdList'];
                }
                if(isset($request->all()['roleModuleAccessIdList'])){
                    $roleModuleAccessIdList = $request->all()['roleModuleAccessIdList'];
                }
    
                DB::beginTransaction();
                try{
                    $where = [];
                    $where['f_role_id'] = $f_role_id;
                    DB::table('tc_role_module')->where($where)->delete();
                    DB::table('tc_role_module_access')->where($where)->delete();

                    foreach ($roleModuleIdList as $k => $v) {
                        $insert = [];
                        $insert['f_role_id'] = $f_role_id;
                        $insert['f_module_id'] = $v;
                        DB::table('tc_role_module')->insert($insert);
                    }
                    foreach ($roleModuleAccessIdList as $k => $v) {
                        $insert = [];
                        $insert['f_role_id'] = $f_role_id;
                        $insert['f_module_access_id'] = $v;
                        DB::table('tc_role_module_access')->insert($insert);
                    }

                    DB::commit();
                    $result['success'] = true;
                    $result['message'] = "Success.";
                } catch(\Exception $ex){
                    throw $ex;
                    DB::rollback();
                    $result['success'] = false;
                    $result['message'] = "Failed.";
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
