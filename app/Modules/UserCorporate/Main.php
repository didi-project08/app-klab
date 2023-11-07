<?php
namespace App\Modules\UserCorporate;

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

class Main extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->_viewPath = "UserCorporate/";
        $this->_vars["_id_"] = "M7";
        $this->_vars['url'] = URL::to('/')."/user-corporate/";
    }
    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        ModelBase::ShowPage($this->_viewPath.'vMain', $this->_vars);
    }
    public function readCorporateCombo(){
        $result = ModelBase::userCorporate($this->_vars);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }
    public function readRoleCombo(){
        $result = ModelBase::roleCorporate($this->_vars);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }
}
