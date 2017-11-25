<?php 
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Validate;

class User extends Controller{

	protected $userData;
	protected $uid;

	public function __construct(){
		parent::__construct();
		$passAction = ['nbnb'];
		// if(!in_array($this->request->action(),$passAction)){
		// 	// 需要登录
		// }
		$this->$userData =  checkUserLogin();
		if(!$this->$userData){
			return makeReturnJson(500,'尚未登录');
		}
		$this->$uid = $this->$userData['uid'];
	}

	public function index(){
		return 'hello';
	}

}