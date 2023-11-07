<?php
namespace App\Modules\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Validator;

class Main extends Controller{

    private $_vars = [];
    private $_cusParams = [];
    private $_viewPath = "Auth/";

    public function __construct(){
        $this->_vars['base_url'] = URL::to('')."/";
        $this->_vars['url'] = URL::to('')."/";
    }

    public function index(){
        if(Request()->session()->has('sessLogin')){
            return $this->_in();
        }else{
            return $this->_out();
        }
    }

    private function _in(){
        return redirect('home');
    }

    private function _out(){
        return view($this->_viewPath.'vMain', $this->_vars);
    }

    public function login(){
        $requestAllData = Request()->all()['data'];
        $validator = Validator::make($requestAllData, [
            'username' => 'required|max:32',
            'password' => 'required|max:16',
        ])->setAttributeNames([
            'username' => 'Username',
            'password' => 'Password'
        ]);

        if ($validator->fails()){
            // $validator->getMessageBag()->add('email', 'Email not found');
            return redirect()->back()->withErrors($validator->errors());
        }else{
            $db = DB::table("tc_user")
                    ->selectRaw('*')
                    ->where("f_username",Request()->data['username'])
                    ->where("f_password",Request()->data['password'])
                    ->where("f_active",1)
                    ->get();
            // dd($db->toArray());
            if(count($db) == 1){
                $result = $db->toArray();
                $row = $result[0];
                Request()->session()->put('sessLogin',true);
                Request()->session()->put('sessUserId',$row->f_user_id);
                Request()->session()->put('sessUsername',$row->f_username);
                // Request()->session()->put('sessUserType',$row->f_user_type);
                // Request()->session()->put('sessUserLink',$row->f_user_link);
                // Request()->session()->put('sessUserCode',$row->f_user_code);
                // Request()->session()->put('sessClientId',$row->f_client_id);
                return redirect('/');
            }else{
                // echo "User not valid.";
                return redirect('/'); 
            }
        }
    }

    public function logout(){
        Request()->session()->flush();
        return redirect('/');
    }
}
