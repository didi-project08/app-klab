<?php
namespace App\Modules\HM;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
// use Validator;
// use Illuminate\Support\Facades;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

use App\Models\ModelBase;
use App\Helpers\Main AS MainHelper;

use App\Models\Laboratory;
use App\Models\LaboratoryRefer;
use App\Models\UserLaboratory;
use App\Models\Corporate;
use App\Models\CorporateEmp;
use App\Models\MCUOnsiteEvent;
use App\Models\MCUOnsiteEventPatient;
use App\Models\MCUFormat;
use App\Models\HMDevice;
use App\Models\HMSample;

class API1Main extends Controller{

    public function __construct(){
        $this->_viewPath = "HM/";
        $this->_vars["_id_"] = "M2";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."api1";
        $this->_vars['url'] = URL::to('/')."/hm/";
        $this->_vars['urlb'] = $this->_vars['url']."api1/";
    }

    public function store(Request $request){
        // dd($request->all());
        $validator = Facades\Validator::make($request->all(), [
            'device_id' => ['required','uuid'],
            'data' => ['required','array'],
        ],[
            'date_from.date_format' => "The :attribute does not match the format dd/mm/yyyy.",
            'date_to.date_format' => "The :attribute does not match the format dd/mm/yyyy."
        ])->setAttributeNames([
            'device_id' => 'Device ID',
            'data' => 'Data'
        ]);
        $validator->after(function (Validator $validator) use ($request) {
            // no addtional validation
        });

        if ($validator->fails()){
            $result['success'] = false;
            $result['status_code'] = 200;
            $result['message'] = "Form data is not valid.";
            $result['form_error'] = true;
            $result['form_error_array'] = $validator->errors();
        }else{
            DB::beginTransaction();
            try{
                $device_id = $request->all()['device_id'];
                $dataSample = $request->all()['data'];
                foreach ($dataSample as $k => $v) {
                    $at = date("Y-m-d H:i:s");
                    $insert = $v;
                    $insert['device_id'] = $device_id;
                    $insert['id'] = MainHelper::uuid();
                    $insert['create_by'] = $device_id;
                    $insert['create_at'] = $at;
                    DB::table('hm_sample')->insert($insert);
                }

                DB::table('hm_device')->where(['id'=>$device_id])->update(['last_online'=>date("Y-m-d H:i:s")]);

                DB::commit();
                $result['success'] = true;
                $result['status_code'] = 200;
                $result['message'] = "Success";
            } catch(\Exception $ex){
                // throw $ex;
                DB::rollback();
                $result['success'] = false;
                $result['status_code'] = $ex->getCode();
                $result['message'] = "Failed.";
            }
        }

        return response()->json($result, $result['status_code']);
    }

}
