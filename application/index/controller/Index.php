<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return 'hello&nbsp;benthink!';
    }

    public function test(){
        return json_encode(input('post.'));
    }
}
