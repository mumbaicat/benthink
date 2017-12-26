<?php
namespace app\api\common;

use think\Controller;

class Base extends Controller{

    public function __construct($passAction=[]){
        parent::__construct();
        $userData = checkUserLogin();
        if(!$passAction){
            // 白名单验证
            if(!in_array($this->request->action(),$passAction)){
                if(!$userData){
                    return makeReturnJson(500,'尚未登录');
                }
            }
        }else{
            if(!$this->$userData){
                return makeReturnJson(500,'尚未登录');
            }
        }

        // 这里可以做权限的认证

        return $userData;
    }

}
