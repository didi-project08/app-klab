<?php
namespace App\Modules\Home;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

use App\Modules\BASE\MY_Controller;
use App\Models\ModelBase;
// use App\Models\Model_query;
use App\Helpers\Main AS MainHelper;

class Main extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->_viewPath = "Home/";
        $this->_vars["_id_"] = "1";
        $this->_vars['url'] = URL::to('/')."/home/";
    }

    public function index(){
        $this->_vars['userScope'] = ModelBase::m_user_scope();
        $this->_vars['vartest'] = "Ini Contoh";

        // $content = view($this->_viewPath.'vMain', $this->_vars);
        // $this->_vars['title'] = "ADMIN - Mapping Super";

        // ModelBase::m_show_page($content, $this->_vars);
        ModelBase::ShowPage($this->_viewPath.'vMain', $this->_vars);

        // dd($this->_vars);

        // return view($this->_viewPath.'vMain', $this->_vars);
    }
}
