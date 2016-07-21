<?php
namespace Admin\Controller;

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
			$this->display('Public/login');
			die();
		}

		// <!--{ 目前只支持到3级菜单 }-->
		$this->assign('menu_list',$this->menu_list());
		$this->assign('user_roles',$this->user_roles());
		$this->assign('login_user',$this->login_user);
	}
	/**
	 * @comment 获取登录用户菜单
	 * @return array
	 */
	private function menu_list(){
		$param_name = 'sys_menu_' . $this->login_user['user_role'];
		$menu_list = S($param_name);
		if(!$menu_list){
			$menu_list = get_user_func($this->login_user['user_role']);
			S($param_name,$menu_list);
		}
		return $menu_list;
	}

	private function user_roles(){
		$user_roles = S('user_roles');

		if(!$user_roles){

			$where = 'user_role IN (' . SysDictModel::USER_ROLE_AGENT . ',' . SysDictModel::USER_ROLE_OPERATOR . ')';
			$user_roles = D('SysUser')->field('uid,user_name')->where($where)->select();

			foreach($user_roles as $row){
				$arr[$row['uid']] = $row['user_name'];
			}
			$user_roles = $arr;
			S('user_roles',$user_roles);
		}
		return $user_roles;
	}
}
