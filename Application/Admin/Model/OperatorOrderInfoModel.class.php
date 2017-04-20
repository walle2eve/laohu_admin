<?php
namespace Admin\Model;

use Think\Model;

class OperatorOrderInfoModel extends Model{

	const DEPOSIT_ORDER_TYPE = 210100;

	const WITHDRAWAL_ORDER_TYPE = 210200;

	// 获取运营商充值记录
	public function get_deposit_list($operator_id = '',$begin_time = '',$end_time = '',$sn = '',$order_by='',$export = false){

		$where = array();

		$where['order_type'] = self::DEPOSIT_ORDER_TYPE;

		if($operator_id != ''){

			$where['operator_id'] = $operator_id;

		}

		if($begin_time && $end_time){
			$begin_time = date('Y-m-d 00:00:00',strtotime($begin_time));
			$end_time = date('Y-m-d 23:59:59',strtotime($end_time));

			$where['create_time'] = array('BETWEEN',array($begin_time,$end_time));
		}

		if($sn != ''){
			$where['sn'] = $sn;
		}


		$order_by = in_array($order_by,array('create_time','amount')) ? $order_by . ' DESC ' : 'id DESC';

		if($export === true){

			$limit = '';
			$page_show = '';

		}else{

			$count = $this->where($where)->count();

			$page = page($count);

			$limit = $page->firstRow.','.$page->listRows;

			$page_show = $page->show();
		}


		$list = $this
				->alias('t')
				->field('t.sn,t.create_time,suser.user_name,t.amount,t.discount,t.gold,t.status,t.total_gold')
				->join('LEFT JOIN t_sys_user suser ON suser.uid = t.operator_id')
				->where($where)
				->order($order_by)
				->limit()
				->select();

		return array('list'=>$list,'page'=> $page_show);

	}

	// add_deposit, 添加运营商充值记录
	public function add_deposit($operator_id,$amount,$discount,$deposit_gold,$discount_money,$remark=''){
		$operator_info = D('SysUser')->where('uid = %d',array($operator_id))->find();
		if(!$operator_info)return array('status'=>false,'msg'=>'运营商信息错误');
		if($operator_info['discount'] != $discount) return array('status'=>false,'msg'=>'运营商折扣信息错误');

		$param['operator_id'] = $operator_id;
		$param['discount'] = $discount;
		$param['amount'] = $amount;
		$param['gold'] = $deposit_gold;
		$param['discount_money'] = $discount_money;
		$param['total_gold'] = $operator_info['gold'] + $deposit_gold;
		$param['order_type'] = self::DEPOSIT_ORDER_TYPE;
		$param['admin_id'] = session('uid');
		$param['remark'] = $remark;
		$param['create_time'] = date('Y-m-d H:i:s');
		$param['create_year'] = date('Y');
		$param['create_month'] = date('m');
		$param['create_day'] = date('d');
		$param['sn'] = get_sn();
		// 添加充值记录
		$this->startTrans();

		$id = $this->add($param);

		if($id > 0){
			$gold_status = M('SysUser')->where('uid = %d',array($operator_id))->setInc('gold',$deposit_gold);
			$deposit_money_status = M('SysUser')->where('uid = %d',array($operator_id))->setInc('deposit_money',$amount);
			$deposit_gold_status = M('SysUser')->where('uid = %d',array($operator_id))->setInc('deposit_gold',$deposit_gold);

			if($gold_status && $deposit_money_status && $deposit_gold_status){
				$this->commit();
				return array('status'=>true);
			}
			$this->rollback();
			return array('status'=>false,'msg'=>'充值记录添加失败，请重试！');
		}
		$this->rollback();

		return array('status'=>false,'msg'=>'服务器响应失败，请重试！');
	}

	// 获取运营商充值统计信息
	public function deposit_stat($stat_type,$year,$month=''){

		$operator_info = array();
		$operator_stat = array();
		$year_stat = array();

		$t_arr = D('SysUser')
					->alias('suser')
					->field('suser.user_name,suser.discount,suser.uid')
					//->join('LEFT JOIN t_operator_order_info ooi ON ooi.operator_id = suser.uid AND ooi.order_type = ' . self::DEPOSIT_ORDER_TYPE .' AND ooi.status = 1')
					->where('suser.status = 1 AND user_role IN (' . SysDictModel::USER_ROLE_OPERATOR .',' . SysDictModel::USER_ROLE_AGENT . ')')
					->select();
		foreach($t_arr as $row){
			$operator_info[$row['uid']] = $row;
		}

		if($stat_type == 'month'){

			$first_day = date('Y-m-01');
			$last_day = date('Y-m-d',strtotime("$first_day +1 month -1 day"));

			$first_day = strtotime($first_day);
			$last_day = strtotime($last_day);


			for($i=$first_day;$i<=$last_day;$i+=86400){
				$operator_stat[$i]['date'] = date('Y-m-d',$i);
				foreach($operator_info as $operator_id=>$row){
					$operator_stat[$i]['stat'][$operator_id] = $this->where("operator_id = %d AND create_year = %d AND create_month = '%s' AND create_day = '%s' AND order_type = %d AND status = 1",array($operator_id,date('Y'),date('m'),date('d',$i),self::DEPOSIT_ORDER_TYPE))->sum('amount');
					//echo M()->getlastsql();
				}

			}
		}elseif($stat_type == 'year'){

			for($i=1;$i<=12;$i++){
				$datetime = $year .'-'.$i.'-1';
				$operator_stat[$i]['date'] = $i . '月';

				foreach($operator_info as $operator_id=>$row){
					$month = date('m',strtotime($datetime));
					$operator_stat[$i]['stat'][$operator_id] = $this->where("operator_id = %d AND create_year = %d AND create_month = '%s' AND order_type = %d AND status = 1",array($operator_id,$year,$month,self::DEPOSIT_ORDER_TYPE))->sum('amount');
					//echo M()->getlastsql();exit();
					if(empty($year_stat[$operator_id]) || !isset($year_stat))
						$year_stat[$operator_id] = $this->where("operator_id = %d AND create_year = %d AND order_type = %d AND status = 1",array($operator_id,$year,self::DEPOSIT_ORDER_TYPE))->sum('amount');
				}
			}

		}

		return array('title'=>$operator_info,'list'=>$operator_stat,'year_stat' => $year_stat);
	}
}
