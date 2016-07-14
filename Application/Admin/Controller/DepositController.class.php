<?php
namespace Admin\Controller;

use Think\Controller;

use Admin\Model\SysDictModel;

class DepositController extends BaseController
{
	public function _initialize(){
		parent::_initialize();
	}
	// 运营商充值记录
  public function index(){

		$param = I('get.');

		if(in_array($this->login_user['user_role'],array(SysDictModel::USER_ROLE_AGENT,SysDictModel::USER_ROLE_OPERATOR))){
			$param['operator_id'] = $this->uid;
		}

		// 开始时间
		if(!$param['date-range-picker']){
			$param['begin_time'] = date('Y-m-d',strtotime('-1 month'));
			$param['end_time'] = date('Y-m-d');
		}else{
			list($param['begin_time'],$param['end_time']) = explode(' - ',$param['date-range-picker']);
		}

		$this->assign('param',$param);

		$result = D('OperatorOrderInfo')->get_deposit_list($param['operator_id'],$param['begin_time'],$param['end_time'],$param['deposit_sn']);

		if(I('submitbtn') == '导出excel'){
			$file_name = '充值记录信息导出';
			$excel_title = array(
				array('width' => 20,'val' => '流水号'),
				array('width' => 20,'val' => '日期'),
				array('width' => 20,'val' => '平台'),
				array('width' => 20,'val' => '充值金额'),
				array('width' => 20,'val' => '折扣'),
				array('width' => 20,'val' => '获得游戏币'),
				array('width' => 20,'val' => '充值状态'),
				array('width' => 20,'val' => '游戏币总计'),
			);
			$excel_data = array();
			$begin_row = 1;

			if(!empty($result['list'])) export_excel($excel_title,$result['list'],$file_name,$begin_row);
		}

		$this->assign('list',$result['list']);

		$this->assign('page',$result['page']);

    $this->display();

  }

	// 添加充值记录
	public function add(){

		$result = array(
			'status' => true,
			'msg' => '添加记录成功',
			'url' => U('Admin/Deposit/index'),
		);

		if($this->login_user['user_role'] != SysDictModel::USER_ROLE_ADMIN){
			$result['status'] = false;
			$result['msg'] = '您没有权限';
			$this->ajaxReturn($result);
			exit();
		}

		$param = I('post.');

		$operator_id = $param['operator_id'];
		$amount = floatval($param['amount']);
		$discount = intval($param['discount']);
		$gold = floatval($param['gold']);

		if(!$operator_id){
			$result['status'] = false;
			$result['msg'] = '请选择平台';
			$this->ajaxReturn($result);
			exit();
		}
		if($amount <= 0){
			$result['status'] = false;
			$result['msg'] = '请填写大于0的充值金币数';
			$this->ajaxReturn($result);
			exit();
		}
		if($discount == ''){
			$result['status'] = false;
			$result['msg'] = '运营商折扣不能为空';
			$this->ajaxReturn($result);
			exit();
		}
		if($gold <= 0){
			$result['status'] = false;
			$result['msg'] = '请填写大于0的充值金币数';
			$this->ajaxReturn($result);
			exit();
		}

		$amount = round($gold - ($gold * ($discount / 100)),2);

		$return = D('OperatorOrderInfo')->add_deposit($operator_id,$amount,$discount,$gold);

		if($return['status'] === true){
			$this->ajaxReturn($result);
			exit();
		}else{
			$result['status'] = false;
			$result['msg'] = $return['msg'];
			$this->ajaxReturn($result);
			exit();
		}

	}

	// 异步获取运营商折扣信息
	public function get_operator_discount(){
		$result = array(
			'status' => true,
			'msg' => '',
			'discount' => 0,
		);

		$operator_id = I('operator_id',0);

		$discount = D('SysUser')->where('user_role IN (' . SysDictModel::USER_ROLE_OPERATOR .',' . SysDictModel::USER_ROLE_AGENT . ') AND uid = %d',array($operator_id))->getField('discount');

		if($discount === false){
			return array(
				'status' => false,
				'msg' => '参数错误',
				'discount' => 0,
			);
		}
		$result['discount'] = $discount;

		$this->ajaxReturn($result);
	}

	// 玩家转入记录
	public function player_list(){
		$param = I('get.');

		if(in_array($this->login_user['user_role'],array(SysDictModel::USER_ROLE_AGENT,SysDictModel::USER_ROLE_OPERATOR))){
			$param['operator_id'] = $this->uid;
		}

		// 开始时间
		if(!$param['date-range-picker']){
			$param['begin_time'] = date('Y-m-d',strtotime('-1 month'));
			$param['end_time'] = date('Y-m-d');
		}else{
			list($param['begin_time'],$param['end_time']) = explode(' - ',$param['date-range-picker']);
		}
		$this->assign('param',$param);



		if(I('submitbtn') == '导出excel'){
			$result = D('UserOrderInfo')->get_deposit_list($param['operator_id'],$param['begin_time'],$param['end_time'],$param['deposit_sn'],'',true);
			$file_name = '充值记录信息导出';
			$excel_title = array(
				array('width' => 20,'val' => '流水号'),
				array('width' => 20,'val' => '日期'),
				array('width' => 20,'val' => '转入运营商'),
				array('width' => 20,'val' => '运营商订单号'),
				array('width' => 20,'val' => '玩家用户名'),
				array('width' => 20,'val' => '转入金额'),
				array('width' => 20,'val' => '转入获得游戏币数量'),
				array('width' => 20,'val' => '转入进度'),
				array('width' => 20,'val' => '游戏币总计'),
			);
			$excel_data = array();
			$begin_row = 1;

			if(!empty($result['list'])) export_excel($excel_title,$result['list'],$file_name,$begin_row);
		}

		$result = D('UserOrderInfo')->get_deposit_list($param['operator_id'],$param['begin_time'],$param['end_time'],$param['deposit_sn']);
		$this->assign('list',$result['list']);

		$this->assign('page',$result['page']);

    $this->display();
	}
}
