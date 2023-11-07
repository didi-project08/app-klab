<?php
namespace App\Modules\Image;

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

use App\Models\ProviderType;

class Main extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->_viewPath = "Image/";
        $this->_vars["_id_"] = "M33";
        $this->_vars['url'] = URL::to('/')."/image/";
    }
    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        ModelBase::ShowPage($this->_viewPath.'vMain', $this->_vars);
    }

    public function readProviderTypeCombo(){
        $this->_vars['orderBy'] = ['a.title:asc'];
        $result = ProviderType::query($this->_vars, ["mode"=>"rowsAll"]);
        if(isset($this->_vars['selectFirst']) && $this->_vars['selectFirst'] == 1){
            if(count($result) > 0){
                $result[0]->selected = true;
            }
        }
        echo json_encode($result);
    }
}
