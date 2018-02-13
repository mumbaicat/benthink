<?php
namespace app\api\common;

use think\Controller;

class Base extends Controller{


    public function __construct($passAction=[]){
        parent::__construct();
        $userData = check_user_login();
        if(count($passAction)!=0){
            // 白名单验证
            if(!in_array($this->request->action(),$passAction)){
                if(!$userData){
                    return make_return_json(500,'尚未登录1');
                }
            }
        }else{
            if(!$userData){
                return make_return_json(500,'尚未登录2');
            }
        }

        // 这里可以做权限的认证

        return $userData;
    }

}
