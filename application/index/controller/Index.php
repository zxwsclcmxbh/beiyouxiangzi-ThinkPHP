<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return "hello world";
    }

    public function home()
    {
        $res1 = json(1,"返回成功");
        return;
    }
}
