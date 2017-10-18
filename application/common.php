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
 * 生成返回Json
 * @param  integer $code 提示码
 * @param  string $msg  提示信息
 * @param  array  $data 附加数据
 * @return json
 */
function makeReturnJson($code, $msg, $data = []) {
	$return = [
		'code' => $code,
		'msg' => $msg,
		'data' => $data,
	];
	return json_encode($return, JSON_UNESCAPED_UNICODE);
}

/**
 * 获取当前用户UID
 * @return integer
 */
function userUid() {
	$token = cookie('usertoken');
	$json = base64_decode($token);
	$data = json_decode($json,true);
	$uid = $data['uid'];
	return $uid;
}

/**
 * 记住用户信息
 * @param  integer $uid      用户UID
 * @param  string $password 加密后的用户密码
 * @return void
 */
function userRemeber($uid, $password) {
	$data = [
		'uid' => $uid,
		'password' => $password,
	];
	$json = json_encode($data);
	$token = base64_encode($json);
	cookie('usertoken', $token,(24*3600)*7);
}

/**
 * 检测用户真实性 (__construct用这个就可以了)
 * @return Boolean
 */
function userCheck() {
	if (!userCheckLogin()) {
		return false;
	}
	$token = cookie('usertoken');
	$json = base64_decode($token);
	$data = json_decode($json,true);

	$uid = $data['uid']; // 这里要过滤
	$encryptPassword = $data['password']; // 这里要过滤
	$userData = db('user')->where(['uid' => $uid])->find();
	if (!$userData) {
		cookie('usertoken', null);
		return false;
	}
	if ($userData['password'] != $encryptPassword) {
		cookie('usertoken', null);
		return false;
	}
	return true;
}

/**
 * 检测是否登录
 * @return Boolean
 */
function userCheckLogin() {
	// $_SERVER['HTTP_USERTOKEN']
	$token = cookie('usertoken');
	if (!$token) {
		return false;
	}
	$json = base64_decode($token);
	$data = json_decode($json,true);
	if (empty($data['uid']) or empty($data['password'])) {
		cookie('usertoken', null);
		return false;
	}
	return true;
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