<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\DB;

use Closure;

class MW_CheckAllowAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $mId = null){
      $segment_array = self::__getUriSegment();

      if(is_array($segment_array) && count($segment_array) >= 2){
        $module = strtolower(self::__getUriSegment(1));

        if($mId != null){
          $module = $mId;
        }

        $loopOri = count($segment_array);

        if(!isset($segment_array[2])){
          $segment_array[2] = 'index';
        }

        $loop = count($segment_array);
        if($loop > 3){ $loop = 3; }

        // $access = "";
        // for ($i=1; $i < $loop ; $i++) {
        //   if($i > 1){
        //     $access .= "/".$segment_array[$i];
        //   }else{
        //     $access .= $segment_array[$i];
        //   }
        // }

        $access = "/".$segment_array[2];
        $access2 = $access;
        if(isset($segment_array[3])){
          $access2 = $access."/".$segment_array[3];
        }
        

        // dd($access);

        if($loopOri == 2){
          $redirect = "";
        }else{
          $redirect = $segment_array[1];
        }

        $userId = session()->get('sessUserId');

        $qModule =
            DB::table(DB::raw("(SELECT * FROM tc_module WHERE f_active=1) AS a"))
            ->select(DB::raw("a.f_module_id, a.f_module_group_id, a.f_module, a.f_module_name"))
            ->join(DB::raw("(SELECT * FROM tc_module_group WHERE f_active=1) AS b"), function($join){
                $join->on("a.f_module_group_id","=","b.f_module_group_id");
            })
            ->whereRaw("(a.f_module='".$module."' OR a.f_module_id='".$module."')");

        $db =
            DB::table(DB::raw("(SELECT * FROM tc_user WHERE f_active=1 AND f_user_id='".$userId."') AS a"))
            ->select(DB::raw("d.*, CONCAT(d.f_module,f.f_module_access) AS f_method"))
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
            })
            // ->whereRaw("CONCAT(d.f_module,f.f_module_access) = '".$access."'")
            ->whereRaw("(f.f_module_access = '".$access."' OR f.f_module_access = '".$access2."')")
            ;
          $db = $db->get();

          // dd(session()->get('sessUserId'));
          // dd($db->count());

          // if(count($db->toArray()) == 1){
          if($db->count() == 1){
            return $next($request);
          }else{
            // throw new Exception("Access denied", 403);
            // return abort(403, 'Access deniedsss');
            // return "Access module not allowed
            // return redirect('/view403/?redirect='.$redirect);
            return redirect('/view403/?message=Access not allowed.&redirect='.$redirect);
            // return redirect('/'.$redirect);
            // echo "<script>alert('Access module not allowed')</script>";
            // echo "<script>alert('".$access."')</script>";
            // redirect(base_url().$redirect,'refresh');
            // echo "<script>window.location.replace('".base_url().$redirect."')</script>";
            // exit();
          }
      }else{
        // echo "Link acceess not found";
        // return redirect('/');
        return redirect('/view403/?message=Access not allowed.&redirect=');
        // echo "<script>alert('Link acceess not found.')</script>";
        // redirect(base_url(),'refresh');
        // echo "<script>window.location.replace('".base_url()."')</script>";
      }
    }

    private function __getUriSegment($segment = 0){
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
}
