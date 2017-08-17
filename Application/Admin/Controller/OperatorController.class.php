<?php
namespace Admin\Controller;

// use Think\Controller;

// use Admin\Model\SysUserModel;

class OperatorController extends BaseController
{
	public function _initialize(){
		parent::_initialize();
	}
  	public function manage(){
		$param = I('get.');
		$list = D('Operator')->get_list($param);

		$this->assign('list',$list['list']);
		$this->assign('page',$list['page']);

		//运营商类别 , 字典值130
		$user_roles = D('SysDict')->get_dict(130);

		$this->assign('user_roles',$user_roles);
		$this->display();
    }

    public function edit(){
    	$page_error = '';
    	$id = I('id');
    	$operator = D('Operator')->find($id);
		if(empty($operator)){
			$page_error = '提交的参数错误';
		}

		if(IS_AJAX && IS_POST){
			$return = array(
				'status' => true,
				'msg' => '编辑配置成功',
				'url' => U('Admin/Operator/manage'),
			);

			if($page_error <> ''){
				$return['status'] = false;
				$return['msg'] = $page_error;
				$this->ajaxReturn($return);
				exit();
			}

			// 处理参数
			$args = I('post.');

			$re = D('Operator')->where('id = %d',array($id))->save($args);

			if($re === false){

				$return['status'] = false;
				$return['msg'] = '编辑配置信息失败，请重试！';
				$this->ajaxReturn($return);

			}else{

                A('Public')->clear_cache();
				$this->ajaxReturn($return);
			}


			exit();
		}
		$this->assign('result',$operator);
		$this->display();
    }

	public function add(){
		$result = array(
			'status' => true,
			'msg' => '添加成功',
			'url' => U('Admin/Operator/manage'),
		);
		///
		if(IS_AJAX && IS_POST){
			$args = I('post.');
			D('Operator')->startTrans();
			if(!D('Operator')->create()){
				$result['status'] = false;
				$result['msg'] = D('Operator')->getError();
				$this->ajaxReturn($result);
				exit();
			}

			$args['input_time'] = time();

			$id = D('Operator')->add($args);
			if(!$id){
				D('Operator')->rollback();

				$result['status'] = false;
				$result['msg'] = '添加运营商失败';
				$this->ajaxReturn($result);
				exit();
			}

			// 生成access_key
			$access_key = to_guid_string($id);
			$access_key = implode('#',array($id,$access_key));

			$save_status = D('Operator')->where('id = %d',array($id))->setField('access_key',$access_key);

			if(!$save_status){
				D('Operator')->rollback();

				$result['status'] = false;
				$result['msg'] = '生成key失败，请重试！';
				$this->ajaxReturn($result);
				exit();
			}


			D('Operator')->commit();

            A('Public')->clear_cache();
			$this->ajaxReturn($result);
			exit();
		}
		die('error page');
	}

	//设置运营商状态
	public function set_status(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST)die('error page');

		$id = I('post.key',0);
		$uid = intval($id);

		$operator = D('Operator')->find($id);

		if(empty($operator)){
			$result['status'] = false;
			$result['msg'] = '运营商参数错误，无法修改状态，请刷新后重试';
			$this->ajaxReturn($result);
			exit();
		}

		$status = $operator['status'] == 1 ? -1 : 1;
		$return = D('Operator')->where('id = %d',array($id))->setField('status',$status);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '修改状态失败';
			$this->ajaxReturn($result);
			exit();
		}

		A('Public')->clear_cache();
		$this->ajaxReturn($result);
	}

}
