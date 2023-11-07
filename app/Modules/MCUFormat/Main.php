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

use App\Models\Corporate;
use App\Models\UserLaboratory;

class Main extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->_viewPath = "MCUFormat/";
        $this->_vars["_id_"] = "M7";
        $this->_vars['url'] = URL::to('/')."/mcu-format/";
    }
    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        ModelBase::ShowPage($this->_viewPath.'vMain', $this->_vars);
    }
}
