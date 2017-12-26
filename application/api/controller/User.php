<?php
namespace app\api\controller;

use think\Db;
use think\Validate;

use app\api\common\Base;

class User extends Base{

	protected $userData;
	protected $uid;

	public function __construct(){
		$this->userData = parent::__construct();
		$this->uid = $this->userData['uid'];
		// $passAction = ['nbnb'];
		// // if(!in_array($this->request->action(),$passAction)){
		// // 	// 需要登录
		// // }
		// $this->$userData =  checkUserLogin();
		// if(!$this->$userData){
		// 	return makeReturnJson(500,'尚未登录');
		// }
		// $this->$uid = $this->$userData['uid'];
	}

	public function index(){
		return 'hello api moudel';
	}

}
