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
use App\Models\MCU2FormatPackageItem;
use App\Models\MCU2FormatItem;

class B4Main extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCU2Format/";
        $this->_vars["_id_"] = "M30";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b4";
        $this->_vars['url'] = URL::to('/')."/mcu2-format/";
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
        $this->_vars['orderBy'] = ["a.level:asc","a.sort:asc"];
        $result = MCU2FormatItem::query($this->_vars, ["mode"=>"rowsAll"]);
        $resultNew = MCU2FormatItem::converToTree($result, @$result[0]->level);

        $resultNew2[0] = ["id"=>-1, "level"=>-1, "header"=>1, "header_name"=>"PARENT", "name"=>"<b>-- FORMAT ITEM LIST --<b>"];
        $resultNew2[0]['name_custome'] = $resultNew2[0]['name'];
        $resultNew2[0]['children'] = $resultNew;

        $this->_vars['orderBy'] = ["b.level:asc","b.sort:asc"];
        $resultNew2[0]['rowsChecked'] = MCU2FormatPackageItem::query($this->_vars, ["mode"=>"rowsAll"]);

        echo json_encode($resultNew2);
    }
    public function packageItemUpdate(Request $request, $mcu_format_package_id = 0){
        // dd($request->all());
        $qVars['id'] = $mcu_format_package_id;
        $sel = MCU2FormatPackage::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $validatorList = [
                'packageItemIdList' => ['required']
            ];
            $validator = Facades\Validator::make($request->all(), $validatorList)->setAttributeNames([
                'packageItemIdList' => 'Please Checklist Package Item first.'
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
                $packageItemIdList = $request->all()['packageItemIdList'];
    
                DB::beginTransaction();
                try{
                    $where = [];
                    $where['mcu_format_package_id'] = $mcu_format_package_id;
                    DB::table('mcu2_format_package_item')->where($where)->delete();

                    foreach ($packageItemIdList as $k => $v) {
                        $insert = [];
                        $insert['mcu_format_package_id'] = $mcu_format_package_id;
                        $insert['mcu_format_item_id'] = $v;
                        DB::table('mcu2_format_package_item')->insert($insert);
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
