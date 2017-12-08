<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * TP5模型对象转数组
 * @param  object  $obj  结果对象集
 * @return array
 */
function toArray($obj) {
	$return = [];
	foreach ($obj as $key) {
		$temp = json_decode($key);
		$new = [];
		foreach ($temp as $keyy => $valuee) {
			$new[$keyy] = $valuee;
		}
		array_push($return, $new);
	}
	return $return;
}

/**
 * 生成Json信息		如果需要停止运行就手动加die();
 * @param  integer $code 状态吗
 * @param  string $msg  提示信息
 * @param  array  $data 附加数组
 * @return void
 */
function makeReturnJson($code, $msg, $data = [],$sys=false) {
	$return = [
		'code' => $code,
		'msg' => $msg,
		'data' => $data,
	];

	if($sys==false){
		return json_encode($return, JSON_UNESCAPED_UNICODE);
	}else{
		return json($return);
	}
}

/**
 * 生成Layui的智能表格的Json
 * 先获取$_GET['page'] 和 $_GET['limit'] ,然后进行page分页
 * @param  array $data  分页后的数组
 * @param  integer $count 全部数组
 * @return void
 */
function makeLayuiTable($data,$count){
	$return =[
		'code'=>0,
		'msg'=>'获取成功',
		'count'=>count($count),
		'data'=>$data,
	];
	return json($return);
}

/**
 * 获取当前登录用户的UID
 * @return integer
 */
function getUserUid() {
	$userToken = cookie('usertoken');
	// if(empty($usertoken)){
	// 	if(empty($_SERVER['HTTP_USERTOKEN'])){
	// 		return false;
	// 	}else{
	// 		$userToken = $_SERVER['HTTP_USERTOKEN'];
	// 	}
	// }
	$token = base64_decode($userToken);
	$json = json_decode($token,true);
	return $json['uid'];
}

/**
 * 检查用户是否登录
 * @return Boolean 成功返回用户信息,否则返回false
 */
function checkUserLogin() {
	$userToken = cookie('usertoken');
	// if(empty($usertoken)){
	// 	if(empty($_SERVER['HTTP_USERTOKEN'])){
	// 		return false;
	// 	}else{
	// 		$userToken = $_SERVER['HTTP_USERTOKEN'];
	// 	}
	// }
	$token = base64_decode($userToken);
	if (empty($token)) {
		return false;
	}
	$json = json_decode($token,true);
	if (empty($json)) {
		cookie('usertoken', null);
		return false;
	}
	$uid = $json['uid'];
	$userData = db('user')->where('uid', $uid)->find();
	if (!$userData) {
		return false;
	} else {
		if ($userData['password'] != $json['password']) {
			cookie('usertoken', null);
			return false;
		} else {
			return $userData;
		}
	}

}

/**
 * 记住密码
 * @param  integer $uid        用户的UID
 * @param  string $enpassword 加密后的密码
 * @return string	usertoken
 */
function remeberUser($uid,$enpassword){
	$data = [
		'uid'=>$uid,
		'password'=>$enpassword
	];
	$json = json_enocde($data);
	$base = base64_enocde($json);
	cookie('usertoken',$base,3600*24*7);
	return $base;
}

/**
 * 文章内容进行转义
 * @param  string $contents 原内容
 * @return string
 */
function encodeContents($contents){
	$contents = htmlentities($contents);
	return $contents;
}

/**
 * 对文章内容进行反转义
 * @param  string $contents 已转义的内容
 * @return string
 */
function decodeContents($contents){
	$contents = html_entity_decode($contents);
	return $contents;
}


function page($array,$page,$count,$order=0){
	$countpage = 0;
	$page=(empty($page))?'1':$page;
	$start=($page-1)*$count;
	if($order==1){
		$array=array_reverse($array);
	}
	$totals=count($array);
	$countpage=ceil($totals/$count); #计算总页面数
	$pagedata=array();
	$pagedata=array_slice($array,$start,$count);
	return $pagedata;
}
