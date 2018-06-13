<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return 'hello&nbsp;benthink!';
    }

    /**
     * 测试
     * api post index.php/index/index/test
     * @return void 啥也不会发生
     */
    public function test(){
        return json_encode(input('post.'));
    }
}
