<?php
namespace Admin\Controller;

use Think\Controller;

use Admin\Model\SysDictModel;

use Admin\Model\SysLogModel;

class NoticeController extends BaseController
{
	public $versionType = 'occifial';

	//public $clientModel = null;

	public function _initialize(){
		parent::_initialize();

		$operator = 0;

		if(in_array($this->login_user['user_role'],array(SysDictModel::USER_ROLE_AGENT,SysDictModel::USER_ROLE_OPERATOR))){
			$operator = $this->uid;
		}else{
			$operator = I('operator');
		}

		if($operator == -1){
			$this->versionType = 'beta';
		}elseif($operator == -2){
			$this->versionType = 'reveal';
		}elseif($operator == '10025'){
			$this->versionType = 'cf365';
		}elseif($operator == '10027'){
			$this->versionType = 'fafa';
		}
	}
	public function index(){

		$param = I('get.');

		if(in_array($this->login_user['user_role'],array(SysDictModel::USER_ROLE_AGENT,SysDictModel::USER_ROLE_OPERATOR))){
			$param['operator'] = $this->uid;
		}

		$list = D('OperatorNotice')->nlist($param);
		$this->assign('list',$list['list']);
		$this->assign('page',$list['page']);

		$this->assign('param',$param);

		$this->display();

	}
	// 信息保存
	public function save(){

		$result = array(
				'status' 	=> true,
				'msg'		=> '操作成功',
				'url'		=> U('Admin/Notice/index'),
			);

		if(!IS_AJAX || !IS_POST)
			$this->error('页面错误');

		$notice = D('OperatorNotice');

		$data = I('post.');

		if(in_array($this->login_user['user_role'],array(SysDictModel::USER_ROLE_AGENT,SysDictModel::USER_ROLE_OPERATOR))){
			//$data['operator_id'] = $this->uid;
		}

		$notice->startTrans();

		if (!$notice->create()){

			$notice->rollback();
		    $result['status'] = false;
		    $result['msg'] = $notice->getError();
		    $this->ajaxReturn($result);
		    exit();

		}

		$notice->create_time = time();

		$notice->dispose_user = $this->uid;

		//print_r($notice->create_time);exit();

		if($notice->id){
			$res = $notice->save();
		}else{
			$res = $notice->add();
		}

		$notice->commit();

		$this->ajaxReturn($result);
	}

	//设置通知状态
	public function set_status(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST) $this->error('页面错误');

		$id = I('post.key',0);
		$id = intval($id);

		$notice = D('OperatorNotice')->find($id);
		if(empty($notice)){
			$result['status'] = false;
			$result['msg'] = '参数错误，无该信息';
			$this->ajaxReturn($result);
			exit();
		}
		$status = $notice['status'] == 1 ? -1 : 1;
		$return = D('OperatorNotice')->where('id = %d',array($id))->setField('status',$status);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '修改状态失败';
			$this->ajaxReturn($result);
			exit();
		}
		$this->ajaxReturn($result);
	}
	//删除通知
	public function del(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST) $this->error('页面错误');

		$id = I('post.id',0);
		$id = intval($id);

		$notice = D('OperatorNotice')->find($id);
		if(empty($notice)){
			$result['status'] = false;
			$result['msg'] = '参数错误，无该信息';
			$this->ajaxReturn($result);
			exit();
		}

		$return = D('OperatorNotice')->delete($id);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '删除失败';
			$this->ajaxReturn($result);
			exit();
		}

		$this->ajaxReturn($result);
	}

	
	// 清除version_json缓存
	public function make_notice_data(){

		$operator = I('post.operator');
		$return = array(
			'status' => true,
			'msg' => '更新json成功',
			'url' => U('Admin/Notice/index',array('operator'=> $operator)),
		);
		
		$json_data = $this->get_notice_json_data($operator);
		S('notice_data_' . $this->versionType,$json_data);

		$file_name = 'notice.json';
		$json_data = json_encode($json_data);

		if($this->versionType == 'beta'){
			$re = QiNiuPutContent($file_name,$json_data);
		}elseif($this->versionType == 'reveal' || $this->versionType == 'cf365' || $this->versionType == 'fafa'){
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
	public function notice_json(){

		$operator = I('get.operator');

		$json_data = array();

		$json_data = S('notice_data_' . $this->versionType);
		if(!$json_data){
			$json_data = $this->get_notice_json_data($operator);
			S('notice_data_' . $this->versionType,$json_data);
		}

		header('Content-type:text/json');
		return $this->ajaxReturn($json_data);
	}


	private function get_notice_json_data($operator = 0){
		$result = D('OperatorNotice')->field('id,title,content,writer,create_time')->where('operator_id = %d AND status = 1',array($operator))->order('create_time DESC')->select();

		return $result;
	}
}
