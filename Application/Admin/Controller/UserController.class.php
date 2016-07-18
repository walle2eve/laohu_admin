<?php
namespace Admin\Controller;

use Think\Controller;

use Admin\Model\SysDictModel;

class UserController extends BaseController
{
	public function _initialize(){
		parent::_initialize();
	}
	// 游戏记录
    public function bet_log()
    {
		$param = I('get.');

		if(in_array($this->login_user['user_role'],array(SysDictModel::USER_ROLE_AGENT,SysDictModel::USER_ROLE_OPERATOR))){
			$param['operator_id'] = $this->uid;
		}

		// 测试开始时间
		$param['min_date'] = date('Y-m-d',strtotime('-30 day'));//C('PLAYER_SPIN_BEGIN_DATE');
		$param['max_date'] = date('Y-m-d');

		if(!$param['date_range_picker']){
			$param['begin_time'] = $param['min_date'];
			$param['end_time'] = date('Y-m-d');
		}else{

			list($param['begin_time'],$param['end_time']) = explode(' - ',$param['date_range_picker']);

			if(!strtotime($param['end_time']))list($param['begin_time'],$param['end_time']) = explode('+-+',$param['date_range_picker']);

		}
		
		if(!$param['begin_time'] || strtotime($param['begin_time']) < strtotime("-30 day")){
			$param['begin_time'] = strtotime(date('Y-m-d 00:00:00',strtotime("-30 day")));
			$param['end_time'] = time();
		}else{
			$param['begin_time'] = strtotime(date('Y-m-d 00:00:00',strtotime($param['begin_time'])));
			$param['end_time'] = strtotime($param['end_time']) ? (strtotime(date('Y-m-d 00:00:00',strtotime($param['end_time'])))) : strtotime(date('Y-m-d 00:00:00'));
		}

		// 排序
		$orderbys = array('win','bet','total_bet');

		$param['order_by'] = !in_array($param['order_by'],$orderbys) ? 'win' : $param['order_by'];

		$this->assign('param',$param);

		$param['order_by'] = $param['order_by'] . ' DESC ';

		// 调取数据
		/**
		$table_name_prefix = 'spin_log_';

		$table_name = $table_name_prefix . date('Y_m_d',$param['begin_time']);

		$model = new \Admin\Model\SpinLogModel($table_name);

		for($i = $param['begin_time']; $i <= $param['end_time']; $i+=86400){
			$table_name = $table_name_prefix . date('Y_m_d',$i);
			$tables[] = $table_name;
		}

		$result = $model->bet_log($param['operator_id'],$param['begin_time'],$param['end_time'],$param['order_by'],$param['account_id'],$tables);
		//print_r($result);
		**/

		$result = D('SpinLog')->bet_log($param['operator_id'],$param['begin_time'],$param['end_time'],$param['order_by'],$param['account_id'],$tables);

		$this->assign('list',$result['list']);
		$this->assign('page',$result['page']);
        $this->display();
    }
	// 用户资金记录
	public function money(){
		$param = I('get.');

		if(in_array($this->login_user['user_role'],array(SysDictModel::USER_ROLE_AGENT,SysDictModel::USER_ROLE_OPERATOR))){
			$param['operator_id'] = $this->uid;
		}

		$result = D('UserInfo')->get_userinfo_list($param['operator_id'],$param['account_id'],$param['order_by']);

		$this->assign('param',$param);
		$this->assign('list',$result['list']);
		$this->assign('page',$result['page']);
		$this->display();
	}
}
