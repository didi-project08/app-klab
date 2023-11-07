<?php
namespace App\Modules\Corporate;

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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory as Spreadsheet_IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Spreadsheet_Style_Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border as Spreadsheet_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Borders as Spreadsheet_Style_Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill as Spreadsheet_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as Spreadsheet_Style_NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType as Spreadsheet_Cell_DataType;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as Spreadsheet_Cell_Date;

use App\Modules\BASE\MY_Controller;
use App\Models\ModelBase;
use App\Helpers\Main AS MainHelper;

use App\Models\Corporate;

class Main extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->_viewPath = "Corporate/";
        $this->_vars["_id_"] = "M4";
        $this->_vars['url'] = URL::to('/')."/corporate/";
    }
    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        ModelBase::ShowPage($this->_viewPath.'vMain', $this->_vars);
    }
    public function filter(){
        return view($this->_viewPath.'vFilter', $this->_vars);
    }
    public function read(){
        $this->_vars['userCorporate'] = ModelBase::userCorporate();
        $result['rows'] = Corporate::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = Corporate::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function add(){
        $this->_vars['mode'] = 'add';
        $this->_vars['url_save'] = $this->_vars['url']."create";
        return view($this->_viewPath.'vForm', $this->_vars);
    }
    public function create(Request $request){
        $validator = Facades\Validator::make($request->all(), [
            'code' => ['required','string','max:5'],
            'title' => ['required','string','max:100'],
            'prov' => ['nullable','string','max:50'],
            'city' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'pic_name' => ['required','string','max:50'],
            'pic_phone' => ['required','string','max:30']
        ])->setAttributeNames([
            'code' => 'Coporate Code',
            'title' => 'Coporate Name',
            'prov' => 'Province',
            'city' => 'City',
            'address' => 'Address',
            'pic_name' => 'PIC Name',
            'pic_phone' => 'PIC Phone (WA)'
        ]);
        $validator->after(function (Validator $validator) use ($request) {
            $db = 
                DB::table(DB::raw("corporate AS a"))
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
        $sel = Corporate::query($this->_vars, ["mode"=>"rowOne"]);
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
            'pic_name' => ['required','string','max:50'],
            'pic_phone' => ['required','string','max:30']
        ])->setAttributeNames([
            'title' => 'Corporate Name',
            'prov' => 'Province',
            'city' => 'City',
            'address' => 'Address',
            'pic_name' => 'PIC Name',
            'pic_phone' => 'PIC Phone (WA)'
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
    public function export(Request $request){
        $contentList = Corporate::query($this->_vars, ["mode"=>"rowsAll"]);
        $fieldList = Corporate::excelField();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $sheetIndex = -1;

        $sheetIndex++;
        $createSheet = new Worksheet($spreadsheet, 'data');
        $spreadsheet->addSheet($createSheet, $sheetIndex);
        $spreadsheet->setActiveSheetIndex($sheetIndex);

        $c = $spreadsheet->getActiveSheet();

        $xr = 1; $xc = 0; $xc++;
        $c->setCellValue(MainHelper::getCol($xc).$xr, 'Corporate');

        $lastRowCol = MainHelper::excelHeader($c, ['xr'=>3, 'xc'=>1, 'fieldList'=>$fieldList]);
        $lastRowCol = MainHelper::excelContent($c, ['xr'=>4, 'xc'=>1, 'fieldList'=>$fieldList, 'contentList'=>$contentList]);

        $fileName = "Corporate_".uniqid().".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer = Spreadsheet_IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    public function import(){
        $this->_vars['url_save'] = $this->_vars['url']."importSave";
        return view($this->_viewPath.'vFormImport', $this->_vars);
    }
    public function importTemplate(){
        $fieldList = Corporate::importTemplateField();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $sheetIndex = -1;

        $sheetIndex++;
        $createSheet = new Worksheet($spreadsheet, 'data');
        $spreadsheet->addSheet($createSheet, $sheetIndex);
        $spreadsheet->setActiveSheetIndex($sheetIndex);

        $c = $spreadsheet->getActiveSheet();

        $xr = 1; $xc = 0;
        
        $xc++;
        $c->setCellValue(MainHelper::getCol($xc).$xr, "Template ID");
        $c->getStyle(MainHelper::getCol($xc).$xr)->applyFromArray(["font"=>["size"=>9]]);
        $xc++;
        $c->setCellValue(MainHelper::getCol($xc).$xr, md5(env('SYS_IMPORT_TEMPLATE_CODE')));
        $c->getStyle(MainHelper::getCol($xc).$xr)->applyFromArray(["font"=>["size"=>9]]);

        $xr++; $xc = 0;
        $xc++;
        $c->setCellValue(MainHelper::getCol($xc).$xr, "Note");
        $c->getStyle(MainHelper::getCol($xc).$xr)->applyFromArray(["font"=>["size"=>9]]);
        $xc++;
        $c->setCellValue(MainHelper::getCol($xc).$xr, "Red Column Is Required.");
        $c->getStyle(MainHelper::getCol($xc).$xr)->applyFromArray(["font"=>["size"=>9]]);

        $lastRowCol = MainHelper::excelHeader($c, ['xr'=>3, 'xc'=>1, 'fieldList'=>$fieldList]);

        $fileName = "CorporateImportTempate.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer = Spreadsheet_IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    public function importSave(Request $request, $mcu_event_id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'file' => ['required','file']
        ])->setAttributeNames([
            'file' => 'File',
        ]);
        $validator->after(function (Validator $validator) use ($request) {
            $file = $request->file('file');
            $mimeType = $file->getMimeType();
            $size = $file->getSize();
            $size = $size / 1024 / 1024;
            
            if($mimeType != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                $validator->errors()->add('file', 'Please choose .xlsx file');
            }
            if($size > 2){
                $validator->errors()->add('file', 'Max size 2MB');
            }
        });
        if ($validator->fails()){
            $result['success'] = false;
            $result['message'] = "Form data is not valid.";
            $result['form_error'] = true;
            $result['form_error_array'] = $validator->errors();
        }else{
            $path = Storage::disk('local')->putFile(
                'excelImport',
                $request->file('file')
            );
            // dd($path);

            $exist = Storage::disk('local')->exists($path);
            if($exist){
                $at = date("Y-m-d H:i:s");

                DB::beginTransaction();
                try{
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $spreadsheet = $reader->setReadDataOnly(true)->load(storage_path("app/".$path));
                    $sheetData = $spreadsheet->getSheet(0)->toArray();

                    if(isset($sheetData[0][1]) && $sheetData[0][1] == md5(env('SYS_IMPORT_TEMPLATE_CODE'))){
                        // dd($sheetData);

                        $excelRowValidation = [];
                        foreach ($sheetData as $k => $v) {
                            if($k >= 3){
                                $insert = [];
                                $insert['code'] = $v[0];
                                $insert['title'] = $v[1];
                                $insert['prov'] = $v[2];
                                $insert['city'] = $v[3];
                                $insert['address'] = $v[4];
                                $insert['pic_name'] = $v[5];
                                $insert['pic_phone'] = $v[6];

                                $validator = Facades\Validator::make($insert, [
                                    'code' => ['required','string','max:5'],
                                    'title' => ['required','string','max:100'],
                                    'prov' => ['nullable','string','max:50'],
                                    'city' => ['nullable','string','max:50'],
                                    'address' => ['nullable','string','max:255'],
                                    'pic_name' => ['required','string','max:50'],
                                    'pic_phone' => ['required','string','max:30']
                                ])->setAttributeNames([
                                    'code' => 'Coporate Code',
                                    'title' => 'Coporate Name',
                                    'prov' => 'Province',
                                    'city' => 'City',
                                    'address' => 'Address',
                                    'pic_name' => 'PIC Name',
                                    'pic_phone' => 'PIC Phone (WA)'
                                ]);
                                $validator->after(function (Validator $validator) use ($insert) {
                                    $db = 
                                        DB::table(DB::raw("corporate AS a"))
                                        ->whereRaw("a.code='".$insert['code']."'")
                                        ->limit(1);
                                    $db = $db->get();
                                    $total = count($db);
                                    if($total > 0){
                                        $validator->errors()->add('code', 'Corporate Code already exist, try another one.');
                                    }
                                });
                                if ($validator->fails()){
                                    $excelRowValidation[] = [
                                        'excelRow' => $k+1,
                                        'excelValidation' => $validator->errors()
                                    ];
                                }else{
                                    $insert['create_by'] = session()->get('sessUsername');
                                    $insert['create_at'] = $at;
                                    DB::table('corporate')->insert($insert);
                                }
                            }
                        }
                        

                        $cancelIfNotValid = 1;
                        if($cancelIfNotValid == 1){
                            if(count($excelRowValidation) > 0){
                                $result['success'] = false;
                                $result['message'] = "Excel value is not valid.";
                                $result['excel_error'] = true;
                                $result['excel_error_array'] = $excelRowValidation;
                            }else{
                                DB::commit();
                                $result['success'] = true;
                                $result['message'] = "Import Success.";
                                $result['excel_error'] = false;
                            }
                        }else{
                            if(count($excelRowValidation) > 0){
                                DB::commit();
                                $result['success'] = true;
                                $result['message'] = "Import Success With Error.";
                                $result['excel_error'] = true;
                                $result['excel_error_array'] = $excelRowValidation;
                            }else{
                                DB::commit();
                                $result['success'] = true;
                                $result['message'] = "Import Success.";
                                $result['excel_error'] = false;
                            }
                        }
                    }else{
                        $validator->errors()->add('file', 'Template is not valid.');
                        $result['success'] = false;
                        $result['message'] = "Form data is not valid.";
                        $result['form_error'] = true;
                        $result['form_error_array'] = $validator->errors();
                    }
                } catch(\Exception $ex){
                    throw $ex;
                    DB::rollback();
                    $result['success'] = false;
                    $result['message'] = "Import Failed.";
                }

                Storage::disk('local')->delete($path);

            }else{
                $validator->errors()->add('file', 'Upload file not succee, please try again.');
                $result['success'] = false;
                $result['message'] = "Form data is not valid.";
                $result['form_error'] = true;
                $result['form_error_array'] = $validator->errors();
            }
        }

        echo json_encode($result);
    }
}
