<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends My_Controller {
    function __construct()
    {
        parent::__construct();
    
        $this->load->model('User_Model');
    }
    
	/**
	 * 首页 登录页面
	 */
	public function index()
	{
		$this->load->view('site/index');
	}

	public function login()
	{
	   if(!isset($_POST['username']) || !isset($_POST['password'])){
	       $this->code = -1;
	       $this->message = "参数不正确";
	       $this->sendJson();
	       return;
	   }
	   $username = $_POST['username'];
	   $password = $_POST['password'];
	   $userObj = $this->User_Model->login($username,$password);
	   if($userObj == "")
	   {
	       $this->code = -1;
	       $this->message = "账号或密码错误";
	       $this->sendJson();
	   }else{
	       $_SESSION['chatRoom']['id'] = $userObj['id'];
	       $_SESSION['chatRoom']['nickname'] = $userObj['nickname'];
	       $this->code = 1;
	       $this->sendJson();
	   }
	   return ;
	}
	
	//注册页面渲染
	public function regist()
	{
	    $this->load->view('site/regist');
	}
	
	//注册
	public function ajaxRegist()
	{
	    if(!isset($_POST['username']) || !isset($_POST['password'])){
	        $this->code = -1;
	        $this->message = "参数不正确";
	        $this->sendJson();
	        return;
	    }
	    $userObj = $this->User_Model->regist($_POST);
	    if($userObj == "")
	    {
	        $this->code = -1;
	        $this->message = "注册失败";
	        $this->sendJson();
	    }else{
	        $_SESSION['chatRoom']['id'] = $userObj['id'];
	        $_SESSION['chatRoom']['nickname'] = $userObj['nickname'];
	        $this->code = 1;
	        $this->sendJson();
	    }
	    return ;
	}
	
	//渲染聊天室首页
	public function chatRoom()
	{
	    $this->checkLogin();
	    $this->load->view('chat/room');
	}
	

}
