<?php
namespace App\Modules\MCU2Format;

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

use App\Models\MCU2FormatTeam;

class B5Main extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCU2Format/";
        $this->_vars["_id_"] = "M30";
        $this->_vars["_idb_"] = $this->_vars["_id_"]."b5";
        $this->_vars['url'] = URL::to('/')."/mcu2-format/";
        $this->_vars['urlb'] = $this->_vars['url']."b5/";
    }
    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        ModelBase::ShowPage($this->_viewPath.'v5Main', $this->_vars);
    }
    public function filter(){
        return view($this->_viewPath.'v5Filter', $this->_vars);
    }
    public function read(){
        // $this->_vars['orderBy'] = ["code :asc"];
        $result['rows'] = MCU2FormatTeam::query($this->_vars, ["mode"=>"rows"]);
        $result['total'] = MCU2FormatTeam::query($this->_vars, ["mode"=>"total"]);
        echo json_encode($result);
    }
    public function add(){
        $this->_vars['mode'] = 'add';
        $this->_vars['url_save'] = $this->_vars['urlb']."create";
        return view($this->_viewPath.'v5Form', $this->_vars);
    }
    public function create(Request $request){
        $validator = Facades\Validator::make($request->all(), [
            'name' => ['required','string','max:100'],
            'str_number' => ['nullable','string','max:100'],
            'sip_number' => ['nullable','string','max:100'],
            'sign' => ['required','file'],
        ])->setAttributeNames([
            'name' => 'Fullname',
            'str_number' => 'STR Number',
            'sip_number' => 'SIP Number',
            'sign' => 'Sign',
        ]);
        $validator->after(function (Validator $validator) use ($request) {
            $file = $request->file('sign');
            $mimeType = $file->getMimeType();
            $size = $file->getSize();
            $size = $size / 1024 / 1024;
            if($mimeType != "image/jpg" && $mimeType != "image/jpeg" && $mimeType != "image/png"){
                $validator->errors()->add('sign', 'Please choose .jpg / .jpeg / .png image');
            }
            if($size > 2){
                $validator->errors()->add('sign', 'Max size 2MB');
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
                $requestAll = $request->all();
                $insert['name'] = $requestAll['name'];
                $insert['str_number'] = $requestAll['str_number'];
                $insert['sip_number'] = $requestAll['sip_number'];
                $insert['create_by'] = session()->get('sessUsername');
                $insert['create_at'] = $at;
                DB::table('mcu2_format_team')->insert($insert);
                $lastInsertId = DB::getPdo()->lastInsertId();

                $file = $request->file('sign');
                $mimeType = $file->getMimeType();
                $fileData = file_get_contents($file);
                $filePath = "TEAM_SIGN/".$lastInsertId.".".$mimeType;

                DB::table('mcu2_format_team')->where(["id"=>$lastInsertId])->update(["sign"=>$filePath]);

                if(env('SYS_CONNECTION') == 'ONLINE'){
                    Storage::disk('google')->put($filePath, $fileData);
                }else{
                    Storage::disk('local')->put($filePath, $fileData);
                }

                DB::commit();
                $result['success'] = true;
                $result['message'] = "Add Success.";
            } catch(\Exception $ex){
                // throw $ex;
                DB::rollback();
                $result['success'] = false;
                $result['message'] = "Add Failed.";
            }
        }
        echo json_encode($result);
    }
    public function showImage(Request $request, $code = 0){
        $this->_vars['code'] = $code;
        $sel = MCU2FormatTeam::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'showImage';
            $this->_vars['url_save'] = $this->_vars['urlb']."showImageSave/".$code;
            // $this->_vars['sel'] = $sel['data'];
            $this->_vars['selData'] = json_encode($sel['data']);
            // dd($this->_vars);
            return view($this->_viewPath.'v5Form', $this->_vars);
        }else{
            return $sel['message'];
        }
    }

    public function edit(Request $request, $id = 0){
        $this->_vars['id'] = $id;
        $sel = MCU2FormatTeam::query($this->_vars, ["mode"=>"rowOne"]);
        if($sel['success']){
            $this->_vars['mode'] = 'edit';
            $this->_vars['url_save'] = $this->_vars['urlb']."update/".$id;
            $this->_vars['selData'] = json_encode($sel['data']);
            return view($this->_viewPath.'v5Form', $this->_vars);
        }else{
            return $sel['message'];
        }
    }
    public function update(Request $request, $id = 0){
        $validator = Facades\Validator::make($request->all(), [
            'title' => ['required','string','max:100'],
        ])->setAttributeNames([
            'title' => 'Corporate Name',
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
                DB::table('image')->where($where)->update($update);

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
    public function delete(Request $request, $code = 0){
        $at = date("Y-m-d H:i:s");
        DB::beginTransaction();
        try{
            $where = [
                "code"=>$code,
            ];
            DB::table('image')->where($where)->delete();

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
        $contentList = MCU2FormatTeam::query($this->_vars, ["mode"=>"rowsAll"]);
        $fieldList = MCU2FormatTeam::excelField();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $sheetIndex = -1;

        $sheetIndex++;
        $createSheet = new Worksheet($spreadsheet, 'data');
        $spreadsheet->addSheet($createSheet, $sheetIndex);
        $spreadsheet->setActiveSheetIndex($sheetIndex);

        $c = $spreadsheet->getActiveSheet();

        $xr = 1; $xc = 0; $xc++;
        $c->setCellValue(MainHelper::getCol($xc).$xr, 'Provider Type');

        $lastRowCol = MainHelper::excelHeader($c, ['xr'=>3, 'xc'=>1, 'fieldList'=>$fieldList]);
        $lastRowCol = MainHelper::excelContent($c, ['xr'=>4, 'xc'=>1, 'fieldList'=>$fieldList, 'contentList'=>$contentList]);

        $fileName = "ProviderType_".uniqid().".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer = Spreadsheet_IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    public function import(){
        $this->_vars['url_save'] = $this->_vars['urlb']."importSave";
        return view($this->_viewPath.'v5FormImport', $this->_vars);
    }
    public function importTemplate(){
        $fieldList = MCU2FormatTeam::importTemplateField();

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

        $fileName = "ProviderTypeImportTempate.xlsx";
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
                                $insert['title'] = $v[0];

                                $validator = Facades\Validator::make($insert, [
                                    'title' => ['required','string','max:100'],
                                ])->setAttributeNames([
                                    'title' => 'Coporate Name',
                                ]);
                                $validator->after(function (Validator $validator) use ($insert) {
           
                                });
                                if ($validator->fails()){
                                    $excelRowValidation[] = [
                                        'excelRow' => $k+1,
                                        'excelValidation' => $validator->errors()
                                    ];
                                }else{
                                    $insert['create_by'] = session()->get('sessUsername');
                                    $insert['create_at'] = $at;
                                    DB::table('image')->insert($insert);
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
