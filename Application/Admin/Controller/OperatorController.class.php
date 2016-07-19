<?php
namespace Admin\Controller;

use Think\Controller;

// use Admin\Model\SysUserModel;

class OperatorController extends BaseController
{
	public function _initialize(){
		parent::_initialize();
	}
    public function manage(){
		$param = I('get.');
		$list = D('SysUser')->get_list($param);
		$this->assign('list',$list['list']);
		$this->assign('page',$list['page']);

		//用户类别 , 字典值100
		$user_roles = D('SysDict')->get_dict(100);
		//echo D('SysDict')->getlastsql();
		$this->assign('user_roles',$user_roles);
		$this->display();
    }
	public function add_user(){
		$result = array(
			'status' => true,
			'msg' => '创建用户成功',
			'url' => U('Admin/Operator/manage'),
		);
		///
		if(IS_AJAX && IS_POST){
			$args = I('post.');
			D('SysUser')->startTrans();
			if(!D('SysUser')->create()){
				$result['status'] = false;
				$result['msg'] = D('SysUser')->getError();
				$this->ajaxReturn($result);
				exit();
			}
			// 添加用户
			$args['salt'] = get_rand_char();
			$args['login_pwd'] = get_pwd($pwd,$salt);
			$args['input_time'] = time();

			$uid = D('SysUser')->add($args);
			if(!$uid){
				D('SysUser')->rollback();

				$result['status'] = false;
				$result['msg'] = '添加用户失败';
				$this->ajaxReturn($result);
				exit();
			}

			// 生成access_key
			$access_key = to_guid_string($uid);
			$access_key = implode('#',array($uid,$access_key));
			$save_status = D('SysUser')->where('uid = %d',array($uid))->setField('access_key',$access_key);
			if(!$save_status){
				D('SysUser')->rollback();

				$result['status'] = false;
				$result['msg'] = '生成key失败，请重试！';
				$this->ajaxReturn($result);
				exit();
			}

			A('Public')->clear_cache();
			D('SysUser')->commit();
			$this->ajaxReturn($result);
			exit();
		}
		die('error page');
	}
	//重置用户密码
	public function reset_user_pwd(){
		$result = array(
			'status' => true,
			'msg' => '重置密码成功'
		);
		$uid = I('post.id');
		$uid = intval($uid);
		$repwd = I('post.pwd');

		if(!IS_AJAX || !IS_POST)die('error page');
		$user = D('SysUser')->find($uid);
		if(!$user){
			$result['status'] = false;
			$result['msg'] = '用户参数错误，找不到该用户，请刷新后重试';
			$this->ajaxReturn($result);
			exit();
		}
		$pwd = get_pwd($repwd,$user['salt']);
		$return = D('SysUser')->where('uid = %d',array($uid))->setField('login_pwd',$pwd);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '重置用户密码失败，请重试';
			$this->ajaxReturn($result);
			exit();
		}
		$this->ajaxReturn($result);
	}
	//设置用户状态
	public function set_status(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST)die('error page');

		$uid = I('post.key',0);
		$uid = intval($uid);
		if($uid == 10001){
			// 系统管理员，不允许修改\
			$result['status'] = false;
			$result['msg'] = '不允许修改系统内置用户的状态';
			$this->ajaxReturn($result);
			exit();
		}
		$user = D('SysUser')->find($uid);
		if(empty($user)){
			$result['status'] = false;
			$result['msg'] = '用户参数错误，无法修改状态，请刷新后重试';
			$this->ajaxReturn($result);
			exit();
		}
		$status = $user['status'] == 1 ? -1 : 1;
		$return = D('SysUser')->where('uid = %d',array($uid))->setField('status',$status);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '修改状态失败';
			$this->ajaxReturn($result);
			exit();
		}
		$this->ajaxReturn($result);
	}
	//删除用户
	public function del_user(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST)die('error page');

		$uid = I('post.id',0);
		$uid = intval($uid);
		if($uid == 10001){
			// 系统管理员，不允许删除
			$result['status'] = false;
			$result['msg'] = '不允许删除系统内置用户';
			$this->ajaxReturn($result);
			exit();
		}
		$user = D('SysUser')->find($uid);
		if(empty($user)){
			$result['status'] = false;
			$result['msg'] = '用户参数错误，无法修改状态，请刷新后重试';
			$this->ajaxReturn($result);
			exit();
		}

		$return = D('SysUser')->delete($uid);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '删除失败';
			$this->ajaxReturn($result);
			exit();
		}
		$this->ajaxReturn($result);
	}
}
