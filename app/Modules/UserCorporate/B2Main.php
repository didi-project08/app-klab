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

use App\Models\CorporateClient;
use App\Models\UserAccountCorporate;
use App\Models\UserCorporateClient;

class B2Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "UserCorporate/";
        $this->_vars["_id_"] = "M7";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b2";
        $this->_vars['url'] = URL::to('/')."/user-corporate/";
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
        $this->_vars['orderBy'] = ["a.title:asc"];
        $result['rows'] = CorporateClient::query($this->_vars, ["mode"=>"rowsAll"]);
        $this->_vars['orderBy'] = ["b.title:asc"];
        $result['rowsChecked'] = UserCorporateClient::query($this->_vars, ["mode"=>"rowsAll"]);
        echo json_encode($result);
    }
    public function userClientUpdate(Request $request, $f_user_id = 0, $corporate_id = 0){
        $qVars['f_user_id'] = $f_user_id;
        $sel = UserAccountCorporate::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $validatorList = [
                'corpClientIdList' => ['required'],
            ];
            $validator = Facades\Validator::make($request->all(), $validatorList)->setAttributeNames([
                'corpClientIdList' => 'Please Checklist Client to proses',
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
                $corpClientIdList = $request->all()['corpClientIdList'];
                // $corpClientIdList = explode(",",$corpClientIdList);
    
                DB::beginTransaction();
                try{
                    $where = [];
                    $where['f_user_id'] = $f_user_id;
                    DB::table('user_corporate_client')->where($where)->delete();

                    $pSuccess = 0;
                    $pNotAllow = 0;
                    $pExist = 0;
                    foreach ($corpClientIdList as $k => $v) {
                        $insert = [];
                        $insert['f_user_id'] = $f_user_id;
                        $insert['corporate_id'] = $corporate_id;
                        $insert['corporate_client_id'] = $v;
                        DB::table('user_corporate_client')->insert($insert);
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
