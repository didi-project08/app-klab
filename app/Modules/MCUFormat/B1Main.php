<?php
namespace App\Modules\MCUFormat;

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

use App\Models\Laboratory;
use App\Models\MCUFormat;

class B1Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCUFormat/";
        $this->_vars["_id_"] = "M7";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b1";
        $this->_vars['url'] = URL::to('/')."/mcu-format/";
        $this->_vars['urlb'] = $this->_vars['url']."b1/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        return view($this->_viewPath.'v1Main', $this->_vars);
    }
    public function filter(){
        return view($this->_viewPath.'v1Filter', $this->_vars);
    }
    public function readLaboratoryCombo(){
        $result = Laboratory::query($this->_vars, ["mode"=>"rowsAll"]);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }

    public function read(){
        $result['rows'] = MCUFormat::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = MCUFormat::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function add(){
        $this->_vars['mode'] = 'add';
        $this->_vars['url_save'] = $this->_vars['urlb']."create";
        return view($this->_viewPath.'v1Form', $this->_vars);
    }

    public function create(Request $request){
        $validator = Facades\Validator::make($request->all(), [
            'laboratory_id' => ['required','integer'],
            'title' => ['required','string','max:100'],
        ])->setAttributeNames([
            'laboratory_id' => 'Provider',
            'title' => 'Format Name',
        ]);
        $validator->after(function (Validator $validator) use ($request) {
            // no additional validation
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
                DB::table('mcu_format')->insert($insert);

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
        $sel = MCUFormat::query($this->_vars, ["mode"=>"rowOne"]);
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
            'title' => ['required','string','max:100'],
        ])->setAttributeNames([
            'title' => 'Format Name',
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
                DB::table('mcu_format')->where($where)->update($update);

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
            DB::table('mcu_format')->where($where)->update($update);

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
