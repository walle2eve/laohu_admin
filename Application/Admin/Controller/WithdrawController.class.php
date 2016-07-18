<?php
namespace Admin\Controller;

use Think\Controller;

use Admin\Model\SysDictModel;

class WithdrawController extends BaseController
{
	public function _initialize(){
		parent::_initialize();
	}
    public function index(){

		$param = I('get.');

		if(in_array($this->login_user['user_role'],array(SysDictModel::USER_ROLE_AGENT,SysDictModel::USER_ROLE_OPERATOR))){
			$param['operator_id'] = $this->uid;
		}

		// 开始时间
		if(!$param['date_range_picker']){
			$param['begin_time'] = date('Y-m-d',strtotime('-1 Month'));
			$param['end_time'] = date('Y-m-d');
		}else{

			list($param['begin_time'],$param['end_time']) = explode(' - ',$param['date_range_picker']);

			if(!strtotime($param['end_time']))list($param['begin_time'],$param['end_time']) = explode('+-+',$param['date_range_picker']);

		}

		$this->assign('param',$param);



		if(I('submitbtn') == '导出excel'){
			$result = D('UserOrderInfo')->get_withdrawal_list($param['operator_id'],$param['begin_time'],$param['end_time'],$param['keyword'],'',true);
			$file_name = '提现记录信息导出';
			$excel_title = array(
				array('width' => 20,'val' => '流水号'),
				array('width' => 20,'val' => '运营商订单号'),
				array('width' => 20,'val' => '日期'),
				array('width' => 20,'val' => '提现平台'),
				array('width' => 20,'val' => '用户名'),
				array('width' => 20,'val' => '提现游戏币数量'),
				array('width' => 20,'val' => '提现进度'),
				array('width' => 20,'val' => '提现反充游戏币'),
				array('width' => 20,'val' => '游戏币总计'),
			);
			$excel_data = array();
			$begin_row = 1;

			if(!empty($result['list'])) export_excel($excel_title,$result['list'],$file_name,$begin_row);
		}
		$result = D('UserOrderInfo')->get_withdrawal_list($param['operator_id'],$param['begin_time'],$param['end_time'],$param['keyword']);

		$this->assign('list',$result['list']);

		$this->assign('page',$result['page']);

        $this->display();

    }
}
