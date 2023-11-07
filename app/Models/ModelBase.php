<?php
namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\url;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory as Spreadsheet_IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Spreadsheet_Style_Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border as Spreadsheet_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Borders as Spreadsheet_Style_Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill as Spreadsheet_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as Spreadsheet_Style_NumberFormat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Laboratory;
use App\Models\Corporate;
use App\Models\UserLaboratory;
use App\Models\UserCorporate;
use App\Models\UserCorporateClient;
use App\Models\Role;
use App\Models\RoleCorporate;
use App\Models\RoleLaboratory;

class ModelBase extends Model{
    use HasFactory;

    public static function ShowPage($view = null, $vars = []){
    	$vars['userModule'] = self::m_get_user_module();
		$vars['userInfo'] = self::m_get_user_info();

        // dd($vars);

    	echo view($view, $vars);
	}
	public static function userLaboratory($vars = []){
        $userScope = self::m_user_scope();
        if($userScope['info']->f_user_admin == 0){
            $vars['f_user_id'] = $userScope['info']->f_user_id;
            return UserLaboratory::query($vars, ["mode"=>"rowsAll"]);
        }else{
            return Laboratory::query($vars, ["mode"=>"rowsAll"]);
        }
    }
	public static function userCorporate($vars = []){
        $userScope = self::m_user_scope();
        if($userScope['info']->f_user_admin == 0){
            $vars['f_user_id'] = $userScope['info']->f_user_id;
            return UserCorporate::query($vars, ["mode"=>"rowsAll"]);
        }else{
            return Corporate::query($vars, ["mode"=>"rowsAll"]);
        }
    }
	public static function userCorporateClient($vars = []){
        $userScope = self::m_user_scope();
        if($userScope['info']->f_user_admin == 0){
            $vars['f_user_id'] = $userScope['info']->f_user_id;
            return UserCorporateClient::query($vars, ["mode"=>"rowsAll"]);
        }else{
            return CorporateClient::query($vars, ["mode"=>"rowsAll"]);
        }
    }
	public static function role($vars = []){
        return Role::query($vars, ["mode"=>"rowsAll"]);
    }
	public static function roleCorporate($vars = []){
        return RoleCorporate::query($vars, ["mode"=>"rowsAll"]);
    }
	public static function roleLaboratory($vars = []){
        return RoleLaboratory::query($vars, ["mode"=>"rowsAll"]);
    }

    public static function m_show_page($content = null, $vars = []){
    	$vars['content'] = $content;

    	$vars['userModule'] = self::m_get_user_module();
		$vars['userInfo'] = self::m_get_user_info();

		// dd($vars);

    	echo view('app_layout/show', $vars);
        // echo view('layouts.admin.master', $vars);
	}
	
	public static function m_user_scope($vars = []){
		$result['info'] = self::m_get_user_info();
		// $result['branch'] = self::m_get_user_branch();
		$result['roleAction'] = self::m_get_user_role_action($vars);
		// $result['branchConfig'] = self::m_get_user_branch_config();

		// $result['platePattern'] = self::m_get_plate_pattern();
		// $result['branchPlatePattern'] = self::m_get_branch_plate_pattern();
		return $result;
	}

    // public static function m_get_client_info(){
    // 	$clientId = session()->get('sessClientId');
    // 	$result = [];
    // 	$db = DB::table("t_client")
    //                 ->selectRaw('*')
    //                 ->where("f_client_id",$clientId)
    //                 ->get();
    //     if(count($db) == 1){
    //     	$result = $db->toArray();
    //         $row = $result[0];
    //         $result = $row;
    //     }
    //     return $result;
    // }

    public static function m_get_user_info(){
    	$userId = session()->get('sessUserId');
    	$result = [];
    	$db = DB::table("tc_user")
                    ->selectRaw('*')
                    ->where("f_user_id",$userId)
                    ->get();
        if(count($db) == 1){
        	$result = $db->toArray();
            $row = $result[0];
            $result = $row;
		}
		unset($result->f_password);
        return $result;
	}
	
	public static function m_get_user_branch(){
		$userId = session()->get('sessUserId');
    	$result = [];
    	$db = DB::table(DB::raw("(SELECT * FROM tc_user_branch WHERE f_delete=0) AS a"))
					->select(DB::raw("b.*, a.f_id"))
					->leftJoin(DB::raw("(SELECT * FROM t_branch WHERE f_delete=0) AS b"), function($join){
						$join->on("a.f_branch_id","=","b.f_branch_id");
					})
                    ->where("a.f_user_id",$userId)
                    ->get();
		$result = $db->toArray();
        return $result;
	}

	public static function m_get_user_branch_config(){
		$userId = session()->get('sessUserId');
    	$result = [];
    	$db = DB::table(DB::raw("(SELECT * FROM tc_user_branch) AS a"))
					->select(DB::raw("b.*"))
					->leftJoin(DB::raw("(SELECT * FROM tc_branch_config WHERE f_delete=0) AS b"), function($join){
						$join->on("a.f_branch_id","=","b.f_branch_id");
					})
                    ->where("a.f_user_id",$userId)
                    ->get();

		foreach ($db as $k => $v) {
			$result[$v->f_branch_id][$v->f_config_code] = $v->f_config_value;
		}
		
        return $result;
	}

	public static function m_get_plate_pattern(){
		// $userId = session()->get('sessUserId');
    	$result = [];
    	$db = DB::table(DB::raw("(SELECT * FROM t_plate_pattern WHERE f_delete=0) AS a"))
					->select(DB::raw("a.*"))
                    ->get();
		$result = $db->toArray();
        return $result;
	}

	public static function m_get_branch_plate_pattern(){
		$result = [];
    	$db = 
			DB::table(DB::raw("(SELECT * FROM t_branch_plate_pattern WHERE f_delete=0) AS a"))
			->select(DB::raw("b.*, a.f_branch_id"))
			->join(DB::raw("(SELECT * FROM t_plate_pattern WHERE f_delete=0) AS b"), function($join){
                $join->on("a.f_plate_pattern_id","=","b.f_plate_pattern_id");
            })
			->orderBy("a.f_branch_id","ASC")
			->orderBy("a.f_plate_pattern_id","ASC")
			->get();
		
		$result = [];
		foreach ($db as $k => $v) {
			$result[$v->f_branch_id][] = $v;
		}

		// $result = $db->toArray();
        return $result;
	}

	public static function m_get_user_role_action($vars = []){
		$userId = session()->get('sessUserId');

		$pathInfo = Request()->getPathInfo();
		$exp = explode("/", $pathInfo);
		$module = null;
		if(isset($exp[1])){
			$module = $exp[1];
		}

		if(isset($vars['_mId_'])){
	        $module = $vars['_mId_'];
	      }

		$qModule =
            DB::table(DB::raw("(SELECT * FROM tc_module WHERE f_active=1) AS a"))
            ->select(DB::raw("a.f_module_id, a.f_module_group_id, a.f_module, a.f_module_name"))
            ->join(DB::raw("(SELECT * FROM tc_module_group WHERE f_active=1) AS b"), function($join){
                $join->on("a.f_module_group_id","=","b.f_module_group_id");
			})
			->whereRaw("(a.f_module='".$module."' OR a.f_module_id='".$module."')");

		$db =
            DB::table(DB::raw("(SELECT * FROM tc_user WHERE f_active=1 AND f_user_id='".$userId."') AS a"))
            ->select(DB::raw("f.*"))
            ->join(DB::raw("(SELECT * FROM tc_role WHERE f_delete=0) AS b"), function($join){
                $join->on("a.f_role_id","=","b.f_role_id");
            })
            ->join(DB::raw("(SELECT * FROM tc_role_module) AS c"), function($join){
                $join->on("a.f_role_id","=","c.f_role_id");
            })
            ->join(DB::raw("({$qModule->toSql()}) AS d"), function($join){
                $join->on("c.f_module_id","=","d.f_module_id");
            })
            ->join(DB::raw("(SELECT * FROM tc_role_module_access) AS e"), function($join){
                $join->on("a.f_role_id","=","e.f_role_id");
            })
            ->join(DB::raw("(SELECT * FROM tc_module_access WHERE f_active=1) AS f"), function($join){
                $join->on("d.f_module_id","=","f.f_module_id")
                ->on("e.f_module_access_id","=","f.f_module_access_id");
            });
          	$db = $db->get();

		$result = [];
		foreach ($db as $k => $v) {
			$result[$v->f_access_code]['f_access_name'] = $v->f_access_name;
			$result[$v->f_access_code]['f_desc'] = $v->f_desc;
		}
		
		return $result;
	}

	public static function m_get_module_action(){

	}

    public static function m_get_user_module(){
      $userId = session()->get('sessUserId');

    	$qRole = DB::table(DB::raw("(SELECT * FROM tc_user WHERE f_active=1) AS a"))
    				->select(DB::raw("a.f_role_id"))
    				->join(DB::raw("(SELECT * FROM tc_role WHERE f_delete=0) AS b"), function($join){
    					$join->on("a.f_role_id","=","b.f_role_id");
    				})
    				->whereRaw("a.f_user_id='".$userId."'");

    	$qModule = DB::table(DB::raw("(SELECT * FROM tc_module WHERE f_active=1) AS a"))
    				->select(DB::raw("a.*, b.f_module_menu_id, b.f_module_group_name, b.f_icon AS f_icon_mg, b.f_sort AS f_sort_mg, a.f_sort AS f_sort_m"))
    				->join(DB::raw("(SELECT * FROM tc_module_group WHERE f_active=1) AS b"), function($join){
    					$join->on("a.f_module_group_id","=","b.f_module_group_id");
    				});

    	$db = DB::table(DB::raw("({$qRole->toSql()}) as a"))
    				->select(DB::raw("c.*"))
    				->join(DB::raw("tc_role_module AS b"), function($join){
    					$join->on("a.f_role_id","=","b.f_role_id");
    				})
    				->join(DB::raw("({$qModule->toSql()}) as c"), function($join){
    					$join->on("b.f_module_id","=","c.f_module_id");
    				})
    				->orderBy("c.f_sort_mg")
    				->orderBy("c.f_sort_m")
    				->get()
    				;
    	$db = $db->toArray();
    	// dd($db);

  		$module = [];
  		foreach ($db as $k => $v) {
  			// $module[$v->f_module_group_id]['f_module_menu_id'] = $v->f_module_menu_id;
  			// $module[$v->f_module_group_id]['f_module_group_id'] = $v->f_module_group_id;
  			// $module[$v->f_module_group_id]['f_module_group_name'] = $v->f_module_group_name;
  			// $module[$v->f_module_group_id]['f_module_group_icon'] = $v->f_icon_mg;
  			$module[$v->f_module_id] = (array)$v;
  		}

  		// $result['success'] = true;
  		// $result['message'] = "";
  		// $result['data'] = $module;

  		return $module;
    }

  //   public function m_get_user_module(){
  //   	$userId = 1;
		// $dbRole = $this->db->select("a.level AS f_role_id")
		// 			->from("(SELECT * FROM tb_user WHERE status=1 AND f_delete=0) AS a")
		// 			->join("(SELECT * FROM tb_user_group WHERE f_delete=0) AS b","a.level=b.id","INNER")
		// 			->where("a.id", $userId);
		// $qRole = $this->db->get_compiled_select();

		// $this->db->select("a.*, b.f_module_menu_id, b.f_module_group_name, b.f_icon AS f_icon_mg, b.f_sort AS f_sort_mg, a.f_sort AS f_sort_m")
		// 			->from("(SELECT * FROM t_module WHERE f_active=1) AS a")
		// 			->join("(SELECT * FROM t_module_group WHERE f_active=1) AS b","a.f_module_group_id=b.f_module_group_id","INNER")
		// 			->order_by("b.f_sort")
		// 			->order_by("a.f_sort");
		// $qModule = $this->db->get_compiled_select();

		// $this->db->select("c.*")
		// 			->from("(".$qRole.") AS a")
		// 			->join("t_role_module AS b","a.f_role_id=b.f_role_id","INNER")
		// 			->join("(".$qModule.") AS c","b.f_module_id=c.f_module_id","INNER")
		// 			->order_by("c.f_sort_mg")
		// 			->order_by("c.f_sort_m")
		// 			->group_by("b.f_module_id");
		// $db = $this->db->get();

		// $module = [];
		// foreach ($db->result_array() as $k => $v) {
		// 	$module[$v['f_module_group_id']]['f_module_menu_id'] = $v['f_module_menu_id'];
		// 	$module[$v['f_module_group_id']]['f_module_group_id'] = $v['f_module_group_id'];
		// 	$module[$v['f_module_group_id']]['f_module_group_name'] = $v['f_module_group_name'];
		// 	$module[$v['f_module_group_id']]['f_module_group_icon'] = $v['f_icon_mg'];
		// 	$module[$v['f_module_group_id']]['module'][$v['f_module_id']] = $v;
		// }

		// $result['success'] = true;
		// $result['message'] = "";
		// $result['data'] = $module;

		// return $result;
  //   }


	public static function m_document_download($filepath, $newFilename = null){
		if(file_exists($filepath)){
			$pathinfo = pathinfo($filepath);
			$mime_content_type = mime_content_type($filepath);
			$headers = ['Content-Type: '.$mime_content_type];

			// return response()->download($filepath);
			return response()->download($filepath, $newFilename, $headers);
		}else{
			return "File not found.";
		}
	}

	public static function m_spreadsheet_style(){

		$result = [

		"Header" => array (
					"alignment" => array (
						"horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_CENTER,
						"vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
					),
					"font" => array (
						"bold" => true,
						"color" => array (
							"rgb" => "001e68"
						),
						"size" => 16,
					),
					"fill" => array(
						"fillType" => Spreadsheet_Style_Fill::FILL_SOLID,
						// "color" => array('rgb' => '808080')
						"color" => array('rgb' => 'ffffff')
					),
					"alignment" => array (
						"horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_LEFT,
						"vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
					)
				),

		"stHeader" => array (
					"alignment" => array (
						"horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_CENTER,
						"vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
					),
					"font" => array (
						"bold" => true,
						"color" => array (
							"rgb" => "ffffff"
						),
						"size" => 9,
					),
					"fill" => array(
						"fillType" => Spreadsheet_Style_Fill::FILL_SOLID,
						// "color" => array('rgb' => '808080')
						"color" => array('rgb' => '001e68')
					),
			'borders' => array(
			'allBorders' => array(
				'borderStyle' => Spreadsheet_Style_Border::BORDER_THIN,
						"color" => array('rgb' => '787878')
			)
			)
				),

				"stFooter" => array (
					"alignment" => array (
						"horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_CENTER,
						"vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
					),
					"font" => array (
						"bold" => true,
						"color" => array (
							// "rgb" => "000000"
							"rgb" => "ffffff"
						),
						"size" => 9,
					),
					"fill" => array(
						"type" => Spreadsheet_Style_Fill::FILL_SOLID,
						// "color" => array('rgb' => 'eaeaea')
						"color" => array('rgb' => '001e68')
					)
				),

				"stSize9" => array (
					"font" => array (
						"size" => 9,
					)
				),

				"stWrap" => array (
					"alignment" => array (
						"wrapText" => true
					)
				),

				"stCenter" => array (
					"alignment" => array (
						"horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_CENTER,
						"vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
					)
				),

				"stRight" => array (
					"alignment" => array (
						"horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_RIGHT,
						"vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
					)
				),

				"stLeft" => array (
					"alignment" => array (
						"horizontal" => Spreadsheet_Style_Alignment::HORIZONTAL_LEFT,
						"vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
					)
				),

				"stMiddle" => array (
					"alignment" => array (
						"vertical" => Spreadsheet_Style_Alignment::VERTICAL_CENTER
					)
				),

				"stBold" => array (
					"font" => array (
						"bold" => true
					)
				),

				"stBorder" => array(
				'borders' => array(
					'allBorders' => array(
					'borderStyle' => Spreadsheet_Style_Border::BORDER_THIN,
					)
				)
				)

		];

		return $result;
	}
}
