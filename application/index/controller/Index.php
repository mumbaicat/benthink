<?php
namespace app\index\controller;

use mumbaicat\apidoc\ApiDoc;

class Index
{
    public function index()
    {
        $doc = new ApiDoc('../application');
        $doc->make();
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
