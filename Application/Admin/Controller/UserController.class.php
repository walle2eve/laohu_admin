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
	//设置vip等级
	public function set_vip_level(){
		$result = array(
			'status' => false,
			'msg' => '设置VIP等级失败!'
		);
		if(IS_AJAX && IS_POST){
			$user_id = I('post.id',0);
			$vip_level = I('post.vip_level',0);

			if(!in_array($vip_level,array(1,2,3,4))){
				$result['msg'] = '您选择的vip等级错误!!';
				$this->ajaxReturn($result);
				exit();
			}

			$user_info = D('UserInfo')->where('user_id  = %d',array($user_id))->find();

			if(empty($user_info)){
				$result['msg'] = '找不到该玩家信息!';
				$this->ajaxReturn($result);
				exit();
			}

			if($user_info['vip_level'] == $vip_level){
				$result['msg'] = '玩家VIP等级无变化!';
				$this->ajaxReturn($result);
				exit();
			}
			$return = D('UserInfo')->where('user_id = %d',array($user_id))->setField('vip_level',intval($vip_level));

			if($return === false){
			}else{
				$result = array(
					'status' => true,
					'msg' => '设置VIP等级成功!'
				);
			}
		}
		$this->ajaxReturn($result);
	}
}
