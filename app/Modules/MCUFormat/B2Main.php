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

use App\Models\MCUFormat;
use App\Models\MCUFormatItem;

class B2Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCUFormat/";
        $this->_vars["_id_"] = "M7";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b2";
        $this->_vars['url'] = URL::to('/')."/mcu-format/";
        $this->_vars['urlb'] = $this->_vars['url']."b2/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        return view($this->_viewPath.'v2Main', $this->_vars);
    }

    public function filter(){
        return view($this->_viewPath.'v2Filter', $this->_vars);
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

    // public function read(){
    //     $result['rows'] = MCUFormatItem::query($this->_vars, ["mode"=>"rows"]);
    //     $result['total'] = MCUFormatItem::query($this->_vars, ["mode"=>"total"]);
    //     echo json_encode($result);
    // }
    public function read(){
        $this->_vars['orderBy'] = ["a.level:asc","a.sort:asc"];
        $result = MCUFormatItem::query($this->_vars, ["mode"=>"rowsAll"]);
        $resultNew = MCUFormatItem::converToTree($result, @$result[0]->level);

        $formatItemList[0] = (object) ["id"=>-1, "level"=>-1, "header"=>1, "header_name"=>"PARENT", "name"=>"<b>-- FORMAT ITEM LIST --<b>"];
        $formatItemList[0]->name_custome = $formatItemList[0]->name;
        $formatItemList[0]->children = $resultNew;

        echo json_encode($formatItemList);
    }
    public function add(Request $request, $mcu_format_id = 0, $id = 0){
        $this->_vars["_idbf_"] = $this->_vars["_idb_"]."f1";
        $qVars['id'] = $mcu_format_id;
        $sel = MCUFormat::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $qVars['id'] = $id;
            $sel2 = MCUFormatItem::query($qVars, ["mode"=>"rowOne"]);
            if(($id == -1 && $this->_vars['header'] == 1) || ($sel2['success'] && $sel2['data']->header == 1)){
                $this->_vars['mode'] = 'add';
                $this->_vars['mcuFormatData'] = $sel['data'];
                $this->_vars['itemParentData'] = @$sel2['data'];
                if($this->_vars['header'] == 1){
                    $formData['main_parent'] = @$sel2['data']->main_parent;
                    $formData['parent'] = @$sel2['data']->id;
                    $formData['level'] = @$sel2['data']->level + 1;
                    $formData['header'] = 1;
                    $formData['sort'] = $this->_vars['sort'];
                    if($id == -1){
                        $formData['main_parent'] = -1;
                        $formData['parent'] = 0;
                        $formData['level'] = 0;
                        $formData['header'] = 1;
                        $formData['sort'] = $this->_vars['sort'];
                    }
                    $this->_vars['formData'] = $formData;
                    $this->_vars['url_save'] = $this->_vars['urlb']."create/".$mcu_format_id."/".$id;
                    return view($this->_viewPath.'v2Form', $this->_vars);
                }else{
                    $formData['main_parent'] = @$sel2['data']->main_parent;
                    $formData['parent'] = @$sel2['data']->id;
                    $formData['level'] = @$sel2['data']->level + 1;
                    $formData['header'] = 0;
                    $formData['sort'] = $this->_vars['sort'];
                    $this->_vars['formData'] = $formData;
                    $this->_vars['url_save'] = $this->_vars['urlb']."createInput/".$mcu_format_id."/".$id;
                    return view($this->_viewPath.'v2FormInput', $this->_vars);
                }
            }else{
                return "Sorry, Please select PARENT.";
            }
        }else{
            return "MCU FORMAT : ".$sel['message'];
        }

    }

    public function create(Request $request, $mcu_format_id = 0, $id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'main_parent' => ['required','integer'],
            'parent' => ['required','integer'],
            'level' => ['required','integer'],
            'header' => ['required','integer'],
            'sort' => ['required','integer'],
            'name' => ['required','string','max:100'],
        ])->setAttributeNames([
            'main_parent' => 'Main Parent',
            'parent' => 'Parent',
            'level' => 'Level',
            'header' => 'Header',
            'sort' => 'Sort',
            'name' => 'Parent Name',
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
                DB::table('mcu_format_item')->insert($insert);

                if($request->all()['main_parent'] == -1){
                    $lastInsertId = DB::getPdo()->lastInsertId();
                    DB::table('mcu_format_item')->where(['id'=>$lastInsertId])->update(['main_parent'=>$lastInsertId]);
                }

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
    public function createInput(Request $request, $mcu_format_id = 0, $id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'main_parent' => ['required','integer'],
            'parent' => ['required','integer'],
            'level' => ['required','integer'],
            'header' => ['required','integer'],
            'sort' => ['required','integer'],
            'code' => ['required','string','max:30'],
            'name' => ['required','string','max:100'],
            'unit' => ['required','string','max:30'],
            'ref_m' => ['required','string','max:100'],
            'ref_f' => ['required','string','max:100'],
        ])->setAttributeNames([
            'main_parent' => 'Main Parent',
            'parent' => 'Parent',
            'level' => 'Level',
            'header' => 'Header',
            'sort' => 'Sort',
            'code' => 'Item Code',
            'name' => 'Item Name',
            'unit' => 'Unit',
            'ref_m' => 'Normal Value (Male)',
            'ref_f' => 'Normal Value (Female)',
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
                DB::table('mcu_format_item')->insert($insert);

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
        $qVars['id'] = $id;
        $sel = MCUFormatItem::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'edit';
            $this->_vars['selData'] = json_encode($sel['data']);
            if($sel['data']->header == 1){
                $this->_vars['url_save'] = $this->_vars['urlb']."update/".$id;
                return view($this->_viewPath.'v2Form', $this->_vars);
            }else{
                $this->_vars['url_save'] = $this->_vars['urlb']."updateInput/".$id;
                return view($this->_viewPath.'v2FormInput', $this->_vars);
            }
        }else{
            return $sel['message'];
        }
    }

    public function update(Request $request, $id = 0){
        $qVars['id'] = $id;
        $sel = MCUFormatItem::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $validator = Facades\Validator::make($request->all(), [
                'main_parent' => ['required','integer'],
                'parent' => ['required','integer'],
                'level' => ['required','integer'],
                'header' => ['required','integer'],
                'sort' => ['required','integer'],
                'name' => ['required','string','max:100'],
            ])->setAttributeNames([
                'main_parent' => 'Main Parent',
                'parent' => 'Parent',
                'level' => 'Level',
                'header' => 'Header',
                'sort' => 'Sort',
                'name' => 'Parent Name',
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
                    DB::table('mcu_format_item')->where($where)->update($update);
    
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
        }else{
            $result['success'] = false;
            $result['message'] = $sel['message'];
        }
        
        echo json_encode($result);
    }
    public function updateInput(Request $request, $id = 0){
        $qVars['id'] = $id;
        $sel = MCUFormatItem::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $validator = Facades\Validator::make($request->all(), [
                'main_parent' => ['required','integer'],
                'parent' => ['required','integer'],
                'level' => ['required','integer'],
                'header' => ['required','integer'],
                'sort' => ['required','integer'],
                'code' => ['required','string','max:30'],
                'name' => ['required','string','max:100'],
                'unit' => ['required','string','max:30'],
                'ref_m' => ['required','string','max:100'],
                'ref_f' => ['required','string','max:100'],
            ])->setAttributeNames([
                'main_parent' => 'Main Parent',
                'parent' => 'Parent',
                'level' => 'Level',
                'header' => 'Header',
                'sort' => 'Sort',
                'code' => 'Item Code',
                'name' => 'Item Name',
                'unit' => 'Unit',
                'ref_m' => 'Normal Value (Male)',
                'ref_f' => 'Normal Value (Female)',
            ]);
            $validator->after(function (Validator $validator) use ($request, $id, $sel) {
                // no addtional validation
                $db = 
                    DB::table(DB::raw("mcu_format_item AS a"))
                    ->whereRaw("a.id != '".$id."'")
                    ->whereRaw("a.mcu_format_id = '".$sel['data']->mcu_format_id."'")
                    ->whereRaw("a.code = '".$request->post('code')."'")
                    ->limit(1);
                $db = $db->get();
                $total = count($db);
                if($total > 0){
                    $validator->errors()->add('code', 'Code already exist, try another one.');
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
                    $update['code'] = strtoupper($update['code']);
                    $update['update_by'] = session()->get('sessUsername');
                    $update['update_at'] = $at;
                    $where = [
                        "id"=>$id,
                    ];
                    DB::table('mcu_format_item')->where($where)->update($update);
    
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
        }else{
            $result['success'] = false;
            $result['message'] = $sel['message'];
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
            DB::table('mcu_format_item')->where($where)->update($update);

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

    public function moveTo(Request $request){
        $validator = Facades\Validator::make($request->all(), [
            'moveTo' => ['required','integer'],
            'beforeId' => ['required','integer'],
            'centerId' => ['required','integer'],
            'afterId' => ['required','integer'],
            'beforeSort' => ['required','integer'],
            'centerSort' => ['required','integer'],
            'afterSort' => ['required','integer']
        ])->setAttributeNames([
            'moveTo' => 'Move To',
            'beforeId' => 'Before Id',
            'centerId' => 'Center Id',
            'afterId' => 'After Id',
            'beforeSort' => 'Before Sort',
            'centerSort' => 'Center Sort',
            'afterSort' => 'After Sort'
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
                $requestAll = $request->all();
                if($requestAll['moveTo'] == 1){
                    DB::table('mcu_format_item')->where(["id"=>$requestAll['centerId']])->update(["sort"=>$requestAll['beforeSort']]);
                    DB::table('mcu_format_item')->where(["id"=>$requestAll['beforeId']])->update(["sort"=>$requestAll['centerSort']]);
                }else{
                    DB::table('mcu_format_item')->where(["id"=>$requestAll['centerId']])->update(["sort"=>$requestAll['afterSort']]);
                    DB::table('mcu_format_item')->where(["id"=>$requestAll['afterId']])->update(["sort"=>$requestAll['centerSort']]);
                }
                DB::commit();
                $result['success'] = true;
                $result['message'] = "Move Success.";
            } catch(\Exception $ex){
                DB::rollback();
                $result['success'] = false;
                $result['message'] = "Move Failed.";
            }
        }
        echo json_encode($result);
    }

    public function duplicateTo(Request $request, $id = 0){
        $this->_vars["_idbf_"] = $this->_vars["_idb_"]."fdt";
        $qVars['id'] = $id;
        $sel = MCUFormat::query($qVars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['url_save'] = $this->_vars['urlb']."duplicateToSave/".$id;
            $this->_vars['mcuFormatData'] = $sel['data'];
            return view($this->_viewPath.'v2FormDuplicateTo', $this->_vars);
        }else{
            return $sel['message'];
        }
    }

    public function duplicateToSave(Request $request, $id = 0){
        if($id != $request->all()['idTo']){
            $nextProcess = false;
            $qVars['id'] = $id;
            $sel = MCUFormat::query($qVars, ["mode"=>"rowOne"]);
            if($sel['success']){
                $qVars['id'] = $request->all()['idTo'];
                $sel2 = MCUFormat::query($qVars, ["mode"=>"rowOne"]);
                if($sel['success']){
                    $nextProcess = true;
                }else{
                    $result['success'] = false;
                    $result['message'] = $sel2['message'];
                }
            }else{
                $result['success'] = false;
                $result['message'] = $sel['message'];
            }
        }else{
            $validator->errors()->add('idTo', 'Please select other MCU Format.');
        }

        if($nextProcess = true){
            $validator = Facades\Validator::make($request->all(), [
                'idTo' => ['required','integer'],
            ])->setAttributeNames([
                'idTo' => 'Other MCU Format',
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
                    $qVars = [];
                    $qVars['mcu_format_id'] = $id;
                    $qVars['orderBy'] = ["a.level:asc","a.sort:asc"];
                    $mcuFormat = MCUFormatItem::query($qVars, ["mode"=>"rowsAll"]);
                    $mcuFormatTree = MCUFormatItem::converToTree($mcuFormat, @$mcuFormat[0]->level);

                    // $result['success'] = false;
                    // $result['message'] = "Update Failed.";
                    // $result['data'] = $mcuFormatTree;
                    
                    if(count($mcuFormatTree) > 0){
                        DB::table("mcu_format_item")->where(["mcu_format_id"=>$request->all()['idTo']])->delete();
                        MCUFormatItem::duplicate($mcuFormatTree, $request->all()['idTo']);

                        DB::commit();
                        $result['success'] = true;
                        $result['message'] = "Duplicate Success.";
                    }else{
                        $result['success'] = false;
                        $result['message'] = "Can't Process, This Format is empty.";
                    }
                } catch(\Exception $ex){
                    throw $ex;
                    DB::rollback();
                    $result['success'] = false;
                    $result['message'] = "Update Failed.";
                }
            }
        }
        echo json_encode($result);
    }
}
