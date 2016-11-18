<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class My_Controller
 * 基础Controller，其中定义了很多常量和最基础的调用方法
*/
class My_Controller extends CI_Controller
{
    public $code = 0;
    public $message = "";
    public $data ="";

    /**
     * 构造函数，定义一些变量
     */
    function __construct()
    {
        parent::__construct();
        header('Content-type:text/html; charset=utf-8');
    }
    
    /**
     * json统一返回格式
     */
    public function sendJson()
    {
        $reInfo['code'] = $this->code;
        $reInfo['message'] = $this->message;
        $reInfo['data'] = $this->data;
        echo json_encode($reInfo);
        return ;
    }
    
}


