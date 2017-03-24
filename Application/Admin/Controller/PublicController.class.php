<?php
namespace Admin\Controller;

use Think\Controller;

class PublicController extends BaseController
{
	public function _initialize(){
		parent::_initialize();
	}
    public function login(){
        $this->display();
    }
	// 登录action
	public function dologin(){
		
		$result = array(
			'status'	=>	true,
			'msg'		=>	'登录成功',
		);
		
		if(IS_POST && IS_AJAX){
			$login_name 	= 	I('account','','htmlspecialchars');
			$login_pwd 		=	I('pwd','');
			
			if(trim($login_name) == '' || trim($login_pwd) == ''){
				$result['status'] 	= false;
				$result['msg']		= '登录名和密码不能为空！';
				
				return $this->ajaxReturn($result);
			}
			
			$islogin = D('SysUser')->get_login($login_name);

			if(empty($islogin) || $islogin['status'] != 1){
				$result['status'] 	= false;
				$result['msg']		= '登录名不存在！';
				
				return $this->ajaxReturn($result);
			}
			
			// 验证用户名密码
			
			$pwd = get_pwd($login_pwd,$islogin['salt']);
			
			if($pwd !== $islogin['login_pwd']){
				$result['status'] 	= false;
				$result['msg']		= '密码错误！';
				
				return $this->ajaxReturn($result);
			}
			
			// 保存登录信息
			
			$this->save_login_info($islogin);
			
			$result['url'] = U('Admin/Index/index');
			
			return $this->ajaxReturn($result);
			exit();
		}
		
		exit('page failed');
	}

	// test mongo
	/**
	public function testMongo(){
		$model = D('SyncLog');
		print_r(
			$model->field(array('sync_id'=>true,'_id'=>false))
					->where(
						array(
							'sync_id' => array('lt',9594)
						)
					)
					->order('sync_id DESC')
					->limit('150,100')
					->select()
		);
	}
	**/

	// 保存用户登录信息到session
	private function save_login_info($islogin){
		$this->logout();
		session('uid',$islogin['uid']);
		session('login_user',$islogin);
		
		D('SysUser')->save_login_info($islogin);
	}
	
	public function logout(){
		session('uid',null);
		session('login_user',null);
		return true;
	}
	
	//清除缓存
	public function clear_cache(){
		// 菜单缓存
		$user_roles = D('SysDict')->get_dict(100);
		foreach($user_roles as $row){
			$param_name = 'sys_menu_' . $row['dict_id'];
			S($param_name,null);
		}
		// 平台用户缓存
		S('user_roles',null);
	}

	// download
	public function download_test(){

		$this->display('download2');
	}

	// download
	public function download(){

		$version = D('ClientVersion')->get_last_version();
		$iosDownloadUrl = $version['conf']['IosDownloadUrl'];
		$androidDownloadUrl = $version['conf']['AndroidDownloadUrl'];
		$this->assign('iosDownloadUrl',$iosDownloadUrl);
		$this->assign('androidDownloadUrl',$androidDownloadUrl);
		$this->display('download3');
	}
}