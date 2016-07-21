<?php
namespace Player\Controller;

use Think\Controller;

use Admin\Model\SysDictModel;


class UserController extends BaseController
{
	public function _initialize(){
		parent::_initialize();
		header("Content-type: text/html; charset=utf-8");
	}
	// 游戏记录
    public function bet_log()
    {
		$param = I('get.');

		//if(in_array($this->login_user['user_role'],array(SysDictModel::USER_ROLE_AGENT,SysDictModel::USER_ROLE_OPERATOR))){
		//	$param['operator_id'] = $this->uid;
		//}
		$param['operator_id'] = '10017';

		if(!$param['account_id'] || !$param['access_key'])exit('登录信息错误!');
		// 验证玩家登陆信息
		$player_info = D('UserInfo')->field('account_id,uniquekey,status')->where("account_id = '%s'",array($param['account_id']))->find();

		if(!$player_info)exit('找不到指定的玩家信息');

		if(strtoupper($param['access_key']) != strtoupper(md5($param['account_id'] . $player_info['uniquekey']))) exit('access_key错误,无法确认玩家信息!');

		if($player_info['status'] != 1) exit('玩家账号已冻结!');

		// 测试开始时间
		$param['min_date'] = date('Y-m-d',strtotime('-30 day'));//C('PLAYER_SPIN_BEGIN_DATE');
		$param['max_date'] = date('Y-m-d');

		if(!$param['date_range_picker']){
			$param['begin_time'] = $param['min_date'];
			$param['end_time'] = $param['max_date'];
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
		$orderbys = array('win','bet','total_bet','log_time');

		$param['order_by'] = !in_array($param['order_by'],$orderbys) ? 'log_time' : $param['order_by'];

		$this->assign('param',$param);

		$param['order_by'] = $param['order_by'] . ' DESC ';


		// 调取数据
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
