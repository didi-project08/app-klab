<?php
namespace App\Modules\BASE;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Models\ModelBase;

class MY_Controller extends Controller{
    protected $_vars = [];
    protected $_cusParams = [];
    protected $_viewPath = "";
    protected $_payload = [];

    public function __construct(){
        $this->middleware('mw_checklogin');
        // $this->middleware('log')->only('index');
        // $this->middleware('subscribed')->except('store');

        self::__checkModuelAccess();
        self::__checkAjaxRequest();

        $this->_vars['base_url'] = URL::to('/')."/";
        $this->_vars['module_active'] = self::__getModuleActive();
        $this->_vars['SYS_CONNECTION'] = env('SYS_CONNECTION');

		    // $this->_vars['userInfo'] = ModelBase::m_get_user_info();
        // $this->_vars['userModule'] = ModelBase::m_get_user_module();
		    // $this->_vars['userInfo'] = self::m_user_scope();

        $request_params = Request()->all();
        foreach ($request_params as $k => $v) {
            $this->_vars[$k] = $v;
        }
    }

    protected function __getUriSegment($segment = 0){
      $pathInfo = Request()->getPathInfo();
      $exp = explode("/", $pathInfo);
      if($segment == 0){
        $uriSegment = $exp;
      }else{
        if(isset($exp[$segment])){
          $uriSegment = $exp[$segment];
        }else{
          $uriSegment = null;
        }
      }
      return $uriSegment;
    }
    protected function __checkModuelAccess(){
      // dd(explode("/",Request()->getPathInfo()));
      // dd($this->_vars);
      $mId = null;
      if(isset($this->_vars['_mId_'])){
        $mId = $this->_vars['_mId_'];
      }

      $method_list = $this->__getModuleAccess('method_list');
  		$method = strtolower(self::__getUriSegment(2));
  		$method2 = $method."/".strtolower(self::__getUriSegment(3));
  		if($method == '') { $method = 'index'; }
  		if(in_array("/".$method, $method_list) || in_array("/".$method2, $method_list)){
        $this->middleware('mw_checkallowaccess:'.$mId);
      }
    }

    protected function __checkAjaxRequest(){
      $mId = null;
      if(isset($this->_vars['_mId_'])){
        $mId = $this->_vars['_mId_'];
      }

      $method_list = $this->__getModuleAccess('method_list_xmlhttprequest');
  		$method = strtolower(self::__getUriSegment(2));
      $method2 = $method."/".strtolower(self::__getUriSegment(3));
      if($method == '') { $method = 'index'; }
      if(in_array("/".$method, $method_list) || in_array("/".$method2, $method_list)){
        $this->middleware('mw_checkajaxrequest');
      }
    }

    protected function __getModuleAccess($mode = 'base'){
      $module = strtolower(self::__getUriSegment(1));
      if(isset($this->_vars['_mId_'])){
        $module = $this->_vars['_mId_'];
      }
      // if($module == 6){
      //   dd($module);
      // }

      $db =
          DB::table(DB::raw("(SELECT * FROM tc_module WHERE f_active=1) AS a"))
          ->select(DB::raw("b.*, a.f_module"))
          ->join(DB::raw("(SELECT * FROM tc_module_access WHERE f_active=1) AS b"), function($join){
              $join->on("a.f_module_id","=","b.f_module_id");
          })
          ->whereRaw("(a.f_module='".$module."' OR a.f_module_id='".$module."')")
          ->get();
      $db = $db->toArray();

      $result = [];
      if($mode == 'base'){
        $result = $db;
      }else if($mode == 'method_list'){
        foreach ($db as $k => $v) {
          $result[] = $v->f_module_access;
        }
      }else if($mode == 'method_list_xmlhttprequest'){
        foreach ($db as $k => $v) {
          if($v->f_xml_http_request == 1){
            $result[] = $v->f_module_access;
          }
        }
      }
      return $result;
    }

    protected function __getModuleActive(){
      $result = [
        "mg"=>0,
        "m"=>0
      ];

      $module = strtolower(self::__getUriSegment(1));

      $db = 
        DB::table("tc_module","a")
        ->select(DB::raw("a.*"))
        ->whereRaw("lower(f_module) = '".strtolower($module)."'");
      $db = $db->get();
      $db = $db->toArray();
      if(count($db) == 1){
        $r = $db[0];
        $result = [
          "mg"=>$r->f_module_group_id,
          "m"=>$r->f_module_id,
        ];
      }

      return $result;
    }

    // protected function __checkAllowAccess($segment_array = []){
    //   if(is_array($segment_array) && count($segment_array) >= 2){
    //     $module = strtolower(self::__getUriSegment(1));
    //
    //     $loopOri = count($segment_array);
    //
    //     if(!isset($segment_array[2])){
		// 			$segment_array[2] = 'index';
		// 		}
    //
    //     $loop = count($segment_array);
    //     if($loop > 3){ $loop = 3; }
    //
    //     $access = "";
		// 		for ($i=1; $i < $loop ; $i++) {
		// 			if($i > 1){
		// 				$access .= "/".$segment_array[$i];
		// 			}else{
		// 				$access .= $segment_array[$i];
		// 			}
		// 		}
    //
    //     if($loopOri == 2){
		// 			$redirect = "";
		// 		}else{
		// 			$redirect = $segment_array[1];
		// 		}
    //
    //     $userId = session()->get('sessUserId');
    //
    //     $qModule =
    //         DB::table(DB::raw("(SELECT * FROM tc_module WHERE f_active=1) AS a"))
    //         ->select(DB::raw("a.f_module_id, a.f_module_group_id, a.f_module, a.f_module_name"))
    //         ->join(DB::raw("(SELECT * FROM tc_module_group WHERE f_active=1) AS b"), function($join){
    //             $join->on("a.f_module_group_id","=","b.f_module_group_id");
    //         });
    //
    //     $db =
    //         DB::table(DB::raw("(SELECT * FROM tc_user WHERE f_active=1 AND f_user_id='".$userId."') AS a"))
    //         ->select(DB::raw("d.*, CONCAT(d.f_module,f.f_module_access) AS f_method"))
    //         ->join(DB::raw("(SELECT * FROM tc_role WHERE f_delete=0) AS b"), function($join){
    //             $join->on("a.f_role_id","=","b.f_role_id");
    //         })
    //         ->join(DB::raw("(SELECT * FROM tc_role_module) AS c"), function($join){
    //             $join->on("a.f_role_id","=","c.f_role_id");
    //         })
    //         ->join(DB::raw("({$qModule->toSql()}) AS d"), function($join){
    //             $join->on("c.f_module_id","=","d.f_module_id");
    //         })
    //         ->join(DB::raw("(SELECT * FROM tc_role_module_access) AS e"), function($join){
    //             $join->on("a.f_role_id","=","b.f_role_id");
    //         })
    //         ->join(DB::raw("(SELECT * FROM tc_module_access WHERE f_active=1) AS f"), function($join){
    //             $join->on("d.f_module_id","=","f.f_module_id")
    //             ->on("e.f_module_access_id","=","f.f_module_access_id");
    //         })
    //         ->whereRaw("CONCAT(d.f_module,f.f_module_access) = '".$access."'")
    //         ;
    //       $db = $db->get();
    //
    //       // dd(session()->get('sessUserId'));
    //       // dd($db->toSql());
    //
    //       if(count($db->toArray()) == 1){
  	// 				// no action
  	// 			}else{
  	// 				echo "Access module not allowed";
  	// 				// echo "<script>alert('Access module not allowed')</script>";
  	// 				// echo "<script>alert('".$access."')</script>";
  	// 				// redirect(base_url().$redirect,'refresh');
  	// 				// echo "<script>window.location.replace('".base_url().$redirect."')</script>";
  	// 				// exit();
  	// 			}
    //   }else{
    //     echo "Link acceess not found";
		// 		// echo "<script>alert('Link acceess not found.')</script>";
    // 		// redirect(base_url(),'refresh');
    // 		// echo "<script>window.location.replace('".base_url()."')</script>";
    //   }
    // }


}
