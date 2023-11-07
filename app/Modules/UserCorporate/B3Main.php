<?php
namespace App\Modules\UserCorporate;

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

use App\Models\RoleAccountCorporate;

class B3Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "UserCorporate/";
        $this->_vars["_id_"] = "M7";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b3";
        $this->_vars['url'] = URL::to('/')."/user-corporate/";
        $this->_vars['urlb'] = $this->_vars['url']."b3/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        return view($this->_viewPath.'v3Main', $this->_vars);
    }
    public function filter(){
        return view($this->_viewPath.'v3Filter', $this->_vars);
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
    public function readRoleCombo(){
        $result = ModelBase::roleCorporate($this->_vars);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }

    public function read(){
        // $qVars['delete'] = -1;
        $this->_vars['userCorporate'] = ModelBase::userCorporate($qVars = []);
        $result['rows'] = RoleAccountCorporate::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = RoleAccountCorporate::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function add(){
        $this->_vars['mode'] = 'add';
        $this->_vars['url_save'] = $this->_vars['urlb']."create";
        return view($this->_viewPath.'v3Form', $this->_vars);
    }

    public function create(Request $request){
        $validator = Facades\Validator::make($request->all(), [
            'corporate_id' => ['required','integer'],
            'f_role_name' => ['required','string','max:50'],
        ])->setAttributeNames([
            'corporate_id' => 'Corporate',
            'f_role_name' => 'Role Name',
        ]);
        $validator->after(function (Validator $validator) use ($request) {
            // no action
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
                $insert = [];
                $insert['f_role_type'] = 3;
                $insert['f_role_name'] = $request->all()['f_role_name'];
                $insert['f_entry_by'] = session()->get('sessUsername');
                $insert['f_entry_dt'] = $at;
                DB::table('tc_role')->insert($insert);
                $lastInsertId = DB::getPdo()->lastInsertId();

                $insert = [];
                $insert['f_role_id'] = $lastInsertId;
                $insert['corporate_id'] = $request->all()['corporate_id'];
                DB::table('role_corporate')->insert($insert);

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

    public function edit(Request $request, $id = 0){
        $this->_vars['f_role_id'] = $id;
        $sel = RoleAccountCorporate::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'edit';
            $this->_vars['url_save'] = $this->_vars['urlb']."update/".$id;
            $this->_vars['selData'] = json_encode($sel['data']);
            return view($this->_viewPath.'v3Form', $this->_vars);
        }else{
            return $sel['message'];
        }
    }

    public function update(Request $request, $id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'f_role_name' => ['required','string','max:50'],
        ])->setAttributeNames([
            'f_role_id' => 'Role Name',
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
                $update['f_role_name'] = $request->all()['f_role_name'];
                $update['f_modify_by'] = session()->get('sessUsername');
                $update['f_modify_dt'] = $at;
                $where = [
                    "f_role_id"=>$id,
                ];
                DB::table('tc_role')->where($where)->update($update);

                DB::commit();
                $result['success'] = true;
                $result['message'] = "Update Success.";
            } catch(\Exception $ex){
                throw $ex;
                DB::rollback();
                $result['success'] = false;
                $result['message'] = "Update Failed.";
            }
        }
        echo json_encode($result);
    }

    public function delete(Request $request, $id = 0){
        $at = date("Y-m-d H:i:s");
        DB::beginTransaction();
        try{
            $update = [
                "f_delete_by"=>session()->get('sessUsername'),
                "f_delete_dt"=>$at,
            ];
            $where = [
                "f_role_id"=>$id,
            ];
            DB::table('tc_role')->where($where)->update($update);

            DB::commit();
            $result['success'] = true;
            $result['message'] = "Delete Success.";
        } catch(\Exception $ex){
            dd($ex);
            DB::rollback();
            $result['success'] = false;
            $result['message'] = "Delete Failed.";
        }
        echo json_encode($result);
    }
}
