<?php 
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Validate;

class User extends Controller{

	// 登陆
	public function login(){
		// userRemeber($uid,$encryptPassword);
	}

	// 注册
	public function register(){
		// userRemeber($uid,$encryptPassword);
	}

	// 注销
	public function logout(){
		cookie('usertoken',null);
	}

	// 密码加密
	public function encryptPassword($password) {
		$password = md5(sha1($password));
		$one = $password[0];
		$two = $password[1];
		$password = sha1($one.$password.$one);
		$one = $password[0];
		$two = $password[1];
		return $one.$two.$password.$two;
	}

}