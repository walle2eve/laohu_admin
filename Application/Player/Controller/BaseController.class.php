<?php
namespace Player\Controller;

use Think\Controller;

use Admin\Model\SysDictModel;

class BaseController extends Controller
{
	protected $uid;
	protected $login_user;

	public function _initialize(){
		$this->uid 			=  	session('uid');
		$this->login_user 	= 	session('login_user');

		// 判断是否已登录
		if((!$this->uid || !$this->login_user) && !in_array(ACTION_NAME,array('login','dologin'))){
		//	$this->display('Public/login');
		//	die();
		}
	}
}
