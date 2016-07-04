<?php
namespace Admin\Model;
use Think\Model;
class UserOrderInfoModel extends Model
{
	const DEPOSIT_ORDER_TYPE = 210100;
	const WITHDRAWAL_ORDER_TYPE = 210200;

	// 获取运营商所属玩家充值金额
	public function get_deposit_sum($operator_id){
		$where['operator_id'] = $operator_id;
		$where['status'] = 1;
		$where['order_type'] = self::DEPOSIT_ORDER_TYPE;
		return $this->where($where)->sum('amount');
	}
	// 获取运营商所属玩家取现金额
	public function get_withdraw_sum($operator_id){
		$where['operator_id'] = $operator_id;
		$where['status'] = 1;
		$where['order_type'] = self::WITHDRAWAL_ORDER_TYPE;
		return $this->where($where)->sum('amount');
	}

	// 获取提现记录
	public function get_withdrawal_list($operator_id = '',$begin_time = '',$end_time = '',$keyword = '',$order_by='',$export = false){

		//$where = array();

		$where = 'uoi.order_type = ' . self::WITHDRAWAL_ORDER_TYPE;

		if($operator_id != ''){

			$where .= ' AND uoi.operator_id = ' . $operator_id;

		}

		if($begin_time && $end_time){
			$begin_time = date('Y-m-d 00:00:00',strtotime($begin_time));
			$end_time = date('Y-m-d 23:59:59',strtotime($end_time));

			$where .= " AND uoi.create_time BETWEEN '" . $begin_time ."' AND '" . $end_time . "' ";
		}

		if($keyword != ''){
			$where .=  " AND (uoi.sn LIKE '%". $keyword ."%' OR ui.account_id LIKE '%" . $keyword . "%') ";
		}

		$order_by = in_array($order_by,array('create_time','amount')) ? $order_by . ' DESC ' : 'create_time DESC';

		if($export === true){

			$limit = '';
			$page_show = '';

		}else{

			$count = $this->alias('uoi')
					->join('LEFT JOIN t_sys_user suser ON suser.uid = uoi.operator_id')
					->join('LEFT JOIN t_user_info ui ON ui.user_id = uoi.player_id')->where($where)->count();

			$page = page($count);

			$limit = $page->firstRow.','.$page->listRows;

			$page_show = $page->show();
		}


		$list = $this
				->alias('uoi')
				->field('uoi.sn,uoi.create_time,suser.user_name,ui.account_id,uoi.amount,uoi.status,"",""')
				->join('LEFT JOIN t_sys_user suser ON suser.uid = uoi.operator_id')
				->join('LEFT JOIN t_user_info ui ON ui.user_id = uoi.player_id')
				->where($where)
				->order($order_by)
				->limit($limit)
				->select();
		return array('list'=>$list,'page'=>$page_show);

	}
	// 获取转入记录
	public function get_deposit_list($operator_id = '',$begin_time = '',$end_time = '',$keyword = '',$order_by='',$export = false){

		//$where = array();

		$where = 'uoi.order_type = ' . self::DEPOSIT_ORDER_TYPE;

		if($operator_id != ''){

			$where .= ' AND uoi.operator_id = ' . $operator_id;

		}

		if($begin_time && $end_time){
			$begin_time = date('Y-m-d 00:00:00',strtotime($begin_time));
			$end_time = date('Y-m-d 23:59:59',strtotime($end_time));

			$where .= " AND uoi.create_time BETWEEN '" . $begin_time ."' AND '" . $end_time . "' ";
		}

		if($keyword != ''){
			$where .=  " AND (uoi.sn LIKE '%". $keyword ."%' OR ui.account_id LIKE '%" . $keyword . "%') ";
		}

		$order_by = in_array($order_by,array('create_time','amount')) ? $order_by . ' DESC ' : 'create_time DESC';

		if($export === true){

			$limit = '';
			$page_show = '';

		}else{

			$count = $this->alias('uoi')
					->join('LEFT JOIN t_sys_user suser ON suser.uid = uoi.operator_id')
					->join('LEFT JOIN t_user_info ui ON ui.user_id = uoi.player_id')->where($where)->count();

			$page = page($count);

			$limit = $page->firstRow.','.$page->listRows;

			$page_show = $page->show();
		}
		$list = $this
				->alias('uoi')
				->field('uoi.sn,uoi.create_time,suser.user_name,ui.account_id,uoi.amount,uoi.amount as gold,uoi.status,"",""')
				->join('LEFT JOIN t_sys_user suser ON suser.uid = uoi.operator_id')
				->join('LEFT JOIN t_user_info ui ON ui.user_id = uoi.player_id')
				->where($where)
				->order($order_by)
				->limit($limit)
				->select();
		return array('list'=>$list,'page'=>$page_show);

	}
}
