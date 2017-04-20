<?php
namespace Admin\Controller;

use Think\Controller;

class ClientController extends BaseController{

	public $versionType = 'occifial';

	public $clientModel = null;

	public function _initialize(){
		parent::_initialize();
		$this->clientModel = D('ClientVersion');

		if(I('version_type') == 'beta'){
			$this->versionType = 'beta';
			$this->clientModel = D('ClientVersionBeta');
		}elseif(I('version_type') == 'reveal'){
			$this->versionType = 'reveal';
			$this->clientModel = D('ClientVersionReveal');
		}elseif(I('version_type') == 'cf365'){
			$this->versionType = 'cf365';
			$this->clientModel = D('ClientVersionCf365');
		}
		$this->assign('version_type',$this->versionType);
	}
	// app版本管理
	public function version(){
		$result = $this->clientModel->get_list();

		$this->assign('page',$result['page']);
		$this->assign('list',$result['list']);
		$this->display();
	}
	// 添加版本信息
	public function add_version(){
		$result = array(
			'status' => true,
			'msg' => '创建版本成功',
			'url' => U('Admin/Client/version',array('version_type'=>$this->versionType)),
		);
		if(IS_AJAX && IS_POST){
			$args = I('post.');
			$this->clientModel->startTrans();
			if(!$this->clientModel->create()){
				$result['status'] = false;
				$result['msg'] = $this->clientModel->getError();
				$this->ajaxReturn($result);
				exit();
			}

			$args['input_time'] = date('Y-m-d H:i:s');
			$id = $this->clientModel->add($args);
			if(!$id){
				$this->clientModel->rollback();

				$result['status'] = false;
				$result['msg'] = '添加版本失败';
				$this->ajaxReturn($result);
				exit();
			}

			$this->clientModel->commit();
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
		$ClientVersionModel = $this->clientModel;

		$result = $ClientVersionModel->find($version_id);
		if(!$result){
			$page_error = "参数错误,找不到指定的版本信息";
		}

		$client_conf_field = $ClientVersionModel->client_conf_field;

		if(IS_AJAX && IS_POST){
			$return = array(
				'status' => true,
				'msg' => '编辑配置成功',
				'url' => U('Admin/Client/version',array('version_type'=>$this->versionType)),
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
	// 清除version_json缓存
	public function make_version_data(){
		$return = array(
			'status' => true,
			'msg' => '更新json成功',
			'url' => U('Admin/Client/version',array('version_type'=>$versionType)),
		);
		
		$json_data = $this->get_version_json_data();
		S('client_version_data_' . $this->versionType,$json_data);

		$file_name = 'client_version.json';
		$json_data = json_encode($json_data);

		if($this->versionType == 'beta'){
			$re = QiNiuPutContent($file_name,$json_data);
		}elseif($this->versionType == 'reveal' || $this->versionType == 'cf365'){
			$re = OssPutContent($file_name,$json_data,$this->versionType);
		}else{
			$re = OssPutContent($file_name,$json_data);
		}

		if($re){
			$this->ajaxReturn($return);
		}else{
			$return['status'] = false;
			$return['msg'] = '编辑配置信息失败，请重试！';
			$this->ajaxReturn($return);
		}
	}
	// 导出app所需json格式
	public function version_json(){

		$ac = I('ac');

		$json_data = array();

		if($ac == 'test'){
			$json_data = $this->get_version_json_data();
		}else{
			$json_data = S('client_version_data_' . $this->versionType);
			if(!$json_data){
				$json_data = $this->get_version_json_data();
				S('client_version_data_' . $this->versionType,$json_data);
			}
		}


		header('Content-type:text/json');
		return $this->ajaxReturn($json_data);
	}


	private function get_version_json_data(){

		$ClientVersionModel = $this->clientModel;
		$result = $ClientVersionModel->order('id DESC')->find();

		//if(empty($result))$this->error('没有可以导出的版本配置信息');

		$client_conf_field = $ClientVersionModel->client_conf_field;
		
		$version_conf = unserialize($result['conf']);
		foreach($client_conf_field as $key=>$val){
			$version_conf_field_arr[$key] = $val['field_type'] == 'string' ? '' : array();
			if(!isset($version_conf[$key]))$version_conf[$key] = $val['field_type'] == 'string' ? '' : array();
		}
		if(!empty($version_conf['ip'])){
			$version_conf['ip'] = DesEncrypt($version_conf['ip']);
		}
		if(!empty($version_conf['TestPlayRoomIp'])){
			$version_conf['TestPlayRoomIp'] = DesEncrypt($version_conf['TestPlayRoomIp']);
		}
		if(!empty($version_conf['TestPlayGameIp'])){
			$version_conf['TestPlayGameIp'] = DesEncrypt($version_conf['TestPlayGameIp']);
		}
		if(empty($version_conf))$version_conf = $version_conf_field_arr;
			
		return $version_conf;
	}
}