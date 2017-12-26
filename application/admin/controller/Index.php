<?php
namespace app\admin\controller;

use think\Controller;

use app\api\common\Base;

class Index extends Controller{

	public function index(){
		return $this->fetch();
	}

	public function welcome(){
		return $this->fetch();
	}

	public function _empty($name){
		return $this->fetch($name);
	}

}
