<?php
namespace Admin\Controller;

use Think\Controller;

class ClientController extends BaseController{
	// app版本管理
	public function version(){
		$result = D('ClientVersion')->get_list();

		$this->assign('page',$result['page']);
		$this->assign('list',$result['list']);
		$this->display();
	}
	// 添加版本信息
	public function add_version(){
		$result = array(
			'status' => true,
			'msg' => '创建版本成功',
			'url' => U('Admin/Client/version'),
		);
		if(IS_AJAX && IS_POST){
			$args = I('post.');
			D('ClientVersion')->startTrans();
			if(!D('ClientVersion')->create()){
				$result['status'] = false;
				$result['msg'] = D('ClientVersion')->getError();
				$this->ajaxReturn($result);
				exit();
			}

			$args['input_time'] = date('Y-m-d H:i:s');
			$id = D('ClientVersion')->add($args);
			if(!$id){
				D('ClientVersion')->rollback();

				$result['status'] = false;
				$result['msg'] = '添加版本失败';
				$this->ajaxReturn($result);
				exit();
			}

			D('ClientVersion')->commit();
			$this->ajaxReturn($result);
			exit();
		}
		die('error page');
	}
	// 编辑版本配置信息
	public function edit_version_conf(){

		$page_error = '';
		$version_id = I('version_id');
		$version_id = intval($version_id);
		$ClientVersionModel = D('ClientVersion');

		$result = $ClientVersionModel->find($version_id);
		if(!$result){
			$page_error = "参数错误,找不到指定的版本信息";
		}

		$client_conf_field = $ClientVersionModel->client_conf_field;

		if(IS_AJAX && IS_POST){
			$return = array(
				'status' => true,
				'msg' => '编辑配置成功',
				'url' => U('Admin/Client/version'),
			);
			if($page_error <> ''){
				$return['status'] = false;
				$return['msg'] = $page_error;
				$this->ajaxReturn($return);
				exit();
			}

			// 处理参数
			$args = I('post.');
			
			$data['version_no'] = trim($args['ClientVersion']);
			unset($args['version_id']);
			$data['conf'] = serialize($args);
			$data['input_time'] = date('Y-m-d H:i:s');
			$re = $ClientVersionModel->where('id = %d',array($version_id))->save($data);

			if($re){
				$this->ajaxReturn($return);
			}else{
				$return['status'] = false;
				$return['msg'] = '编辑配置信息失败，请重试！';
				$this->ajaxReturn($return);
			}
			
			exit();
		}

		$result['conf'] = unserialize($result['conf']);

		$this->assign('page_error',$page_error);
		$this->assign('result',$result);
		$this->assign('client_conf_field',$client_conf_field);
		$this->display();
	}
	// 导出app所需json格式
	public function version_json(){
		$ClientVersionModel = D('ClientVersion');
		$result = $ClientVersionModel->order('id DESC')->find();

		if(empty($result))$this->error('没有可以导出的版本配置信息');

		$client_conf_field = $ClientVersionModel->client_conf_field;

		$json_data = array();

		
		$version_conf = unserialize($result['conf']);
		foreach($client_conf_field as $key=>$val){
			$version_conf_field_arr[$key] = $val['field_type'] == 'string' ? '' : array();
			if(!isset($version_conf[$key]))$version_conf[$key] = $val['field_type'] == 'string' ? '' : array();
		}
		if(!empty($version_conf['ip'])){
			$version_conf['ip'] = DesEncrypt($version_conf['ip']);
		}
		if(empty($version_conf))$version_conf = $version_conf_field_arr;
		$json_data = $version_conf;


		header('Content-type:text/json');
		return $this->ajaxReturn($json_data);
	}
}