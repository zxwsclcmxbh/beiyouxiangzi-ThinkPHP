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

function json($error_code, $msg="", $data=array())
{
    $result = array(
        'error_code'=>$error_code,
        'msg'=>$msg,
        'data'=>$data
    );
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}