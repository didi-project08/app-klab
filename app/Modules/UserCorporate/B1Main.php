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

use App\Models\UserAccountCorporate;

class B1Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "UserCorporate/";
        $this->_vars["_id_"] = "M7";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b1";
        $this->_vars['url'] = URL::to('/')."/user-corporate/";
        $this->_vars['urlb'] = $this->_vars['url']."b1/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        return view($this->_viewPath.'v1Main', $this->_vars);
    }

    public function filter(){
        return view($this->_viewPath.'v1Filter', $this->_vars);
    }
    public function read(){
        $qVars['delete'] = -1;
        $this->_vars['userCorporate'] = ModelBase::userCorporate($qVars);
        $result['rows'] = UserAccountCorporate::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = UserAccountCorporate::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function add(){
        $this->_vars['mode'] = 'add';
        $this->_vars['url_save'] = $this->_vars['urlb']."create";
        return view($this->_viewPath.'v1Form', $this->_vars);
    }

    public function create(Request $request){
        $validator = Facades\Validator::make($request->all(), [
            'corporate_id' => ['required','integer'],
            'f_role_id' => ['required','integer'],
            'f_username' => ['required','string','max:64'],
            'f_password' => ['required','string','max:64'],
        ])->setAttributeNames([
            'corporate_id' => 'Corporate',
            'f_role_id' => 'Role',
            'f_username' => 'Username',
            'f_password' => 'Password',
        ]);
        $validator->after(function (Validator $validator) use ($request) {
            $db = 
                DB::table(DB::raw("tc_user AS a"))
                ->whereRaw("a.f_username='".$request->post('f_username')."'")
                ->limit(1);
            $db = $db->get();
            $total = count($db);
            if($total > 0){
                $validator->errors()->add('f_username', 'Username already exist, try another one.');
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
                $insert = [];
                $insert['f_user_type'] = 3;
                $insert['f_active'] = 1;
                $insert['f_username'] = $request->all()['f_username'];
                $insert['f_password'] = $request->all()['f_password'];
                $insert['f_role_id'] = $request->all()['f_role_id'];
                $insert['f_fullname'] = $request->all()['f_username'];
                $insert['f_entry_by'] = session()->get('sessUsername');
                $insert['f_entry_dt'] = $at;
                DB::table('tc_user')->insert($insert);
                $lastInsertId = DB::getPdo()->lastInsertId();

                $insert = [];
                $insert['f_user_id'] = $lastInsertId;
                $insert['corporate_id'] = $request->all()['corporate_id'];
                DB::table('user_corporate')->insert($insert);

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
        $this->_vars['f_user_id'] = $id;
        $sel = UserAccountCorporate::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
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
            'f_role_id' => ['required','integer'],
            'f_password' => ['required','string','max:64'],
        ])->setAttributeNames([
            'f_role_id' => 'Role',
            'f_password' => 'Password',
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
                $update['f_password'] = $request->all()['f_password'];
                $update['f_role_id'] = $request->all()['f_role_id'];
                $update['f_modify_by'] = session()->get('sessUsername');
                $update['f_modify_dt'] = $at;
                $where = [
                    "f_user_id"=>$id,
                ];
                DB::table('tc_user')->where($where)->update($update);

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
                "f_user_id"=>$id,
            ];
            DB::table('tc_user')->where($where)->update($update);

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
