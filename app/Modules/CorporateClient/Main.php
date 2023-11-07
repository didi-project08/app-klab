<?php
namespace App\Modules\CorporateClient;

use Storage;

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
use App\Models\UserLaboratory;

class Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "CorporateClient/";
        $this->_vars["_id_"] = "M4";
        $this->_vars['url'] = URL::to('/')."/corporate-client/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        ModelBase::ShowPage($this->_viewPath.'vMain', $this->_vars);
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
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }

    public function read(){
        $this->_vars['userCorporate'] = ModelBase::UserCorporate();
        $result['rows'] = CorporateClient::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = CorporateClient::query($this->_vars, ["mode"=>"total"]);
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
            'code' => ['required','string','max:5'],
            'title' => ['required','string','max:100'],
            'prov' => ['nullable','string','max:50'],
            'city' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
        ])->setAttributeNames([
            'laboratory_id' => 'Provider',
            'code' => 'Corporate Code',
            'title' => 'Corporate Name',
            'prov' => 'Province',
            'city' => 'City',
            'address' => 'Address',
        ]);
        $validator->after(function (Validator $validator) use ($request) {
            $db = 
                DB::table(DB::raw("corporate AS a"))
                ->whereRaw("a.laboratory_id='".$request->post('laboratory_id')."'")
                ->whereRaw("a.code='".$request->post('code')."'")
                ->limit(1);
            $db = $db->get();
            $total = count($db);
            if($total > 0){
                $validator->errors()->add('code', 'Corporate Code already exist, try another one.');
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
                $insert['create_by'] = session()->get('sessUsername');
                $insert['create_at'] = $at;
                $insert['code'] = strtoupper($request->post('code'));
                DB::table('corporate')->insert($insert);

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
        $sel = CorporateClient::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'edit';
            $this->_vars['url_save'] = $this->_vars['url']."update/".$id;
            $this->_vars['selData'] = json_encode($sel['data']);
            return view($this->_viewPath.'vForm', $this->_vars);
        }else{
            return $sel['message'];
        }
    }

    public function update(Request $request, $id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'title' => ['required','string','max:100'],
            'prov' => ['nullable','string','max:50'],
            'city' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
        ])->setAttributeNames([
            'title' => 'Corporate Name',
            'prov' => 'Province',
            'city' => 'City',
            'address' => 'Address',
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
                $where = [
                    "id"=>$id,
                ];
                DB::table('corporate')->where($where)->update($update);

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
            DB::table('corporate')->where($where)->update($update);

            DB::commit();
            $result['success'] = true;
            $result['message'] = "Delete Success.";
        } catch(\Exception $ex){
            DB::rollback();
            $result['success'] = false;
            $result['message'] = "Delete Failed.";
        }
        echo json_encode($result);
    }
}
