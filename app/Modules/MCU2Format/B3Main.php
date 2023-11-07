<?php
namespace App\Modules\MCU2Format;

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

use App\Models\MCU2FormatPackage;

class B3Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCU2Format/";
        $this->_vars["_id_"] = "M30";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b3";
        $this->_vars['url'] = URL::to('/')."/mcu2-format/";
        $this->_vars['urlb'] = $this->_vars['url']."b3/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        return view($this->_viewPath.'v3Main', $this->_vars);
    }

    public function filter(){
        return view($this->_viewPath.'v3Filter', $this->_vars);
    }
    public function laboratoryCombo(){
        $userScope = ModelBase::m_user_scope();
        if($userScope['info']->f_user_admin == 0){
            $this->_vars['f_user_id'] = $userScope['info']->f_user_id;
        }
        $result = UserLaboratory::query($this->_vars, ["mode"=>"rowsAll"]);
        // if(count($result) > 0){
        //     $result[0]->selected = true;
        // }
        echo json_encode($result);
    }

    public function read(){
        $this->_vars['orderBy'] = ["a.title:asc"];
        $result['rows'] = MCU2FormatPackage::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = MCU2FormatPackage::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function add(Request $request, $mcu_format_id = 0){
        $this->_vars["_idbf_"] = $this->_vars["_idb_"]."f1";
        $this->_vars['mode'] = 'add';
        $this->_vars['url_save'] = $this->_vars['urlb']."create/".$mcu_format_id;
        return view($this->_viewPath.'v3Form', $this->_vars);
    }

    public function create(Request $request, $mcu_format_id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'code' => ['required','string','max:20'],
            'title' => ['required','string','max:100'],
            'price_lab' => ['nullable','numeric'],
            'price_corporate' => ['nullable','numeric'],
        ])->setAttributeNames([
            'code' => 'Code',
            'title' => 'Package Name',
            'price_lab' => 'Price Provider',
            'price_corporate' => 'Price Corporate',
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
                $insert['mcu_format_id'] = $mcu_format_id;
                DB::table('mcu2_format_package')->insert($insert);

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
        $this->_vars["_idbf_"] = $this->_vars["_idb_"]."f1";
        $this->_vars['id'] = $id;
        $sel = MCU2FormatPackage::query($this->_vars, ["mode"=>"rowOne"]);
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
            'code' => ['required','string','max:20'],
            'title' => ['required','string','max:100'],
            'price_lab' => ['nullable','numeric'],
            'price_corporate' => ['nullable','numeric'],
        ])->setAttributeNames([
            'code' => 'Code',
            'title' => 'Package Name',
            'price_lab' => 'Price Provider',
            'price_corporate' => 'Price Corporate',
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
                DB::table('mcu2_format_package')->where($where)->update($update);

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
            DB::table('mcu2_format_package')->where($where)->update($update);

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
