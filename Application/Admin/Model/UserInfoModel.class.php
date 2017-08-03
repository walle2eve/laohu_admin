<?php
namespace Admin\Model;
use Think\Model;

class UserInfoModel extends Model{
    protected $dbName =	'laohu';

    public function set_vip_level($user_info,$user_id,$vip_level){
        $operator_id = $user_info['operator_id'];
        $operator_info = D('Operator')->find($operator_id);
        if($operator_info['is_diy'] == 1){
            $this->dbName = $operator_info['diy_db_name'];
        }
        $this->where('user_id = %d',array($user_id))->setField('vip_level',intval($vip_level));
    }
	// 平台用户数,取状态有效的用户
	public function get_player_nums($operator_id){
		return $this->where('status = 1 AND operator_id = %d',array($operator_id))->count();
	}
	// 平台玩家总剩余金币数
	public function get_player_balance($operator_id){
		return $this->where('operator_id = %d',array($operator_id))->sum('gold');
	}
	// 用户信息列表
	public function get_userinfo_list($operator_id = 0,$account_id = '',$orderby = 'deposit'){

		$where['ui.status'] = 1;

		if($operator_id > 0)$where['ui.operator_id'] = $operator_id;

		if($account_id != '')$where['ui.account_id'] = array('LIKE',$account_id);

		$count = $this->alias('ui')->where($where)->count();

		$page = page($count);

		$orderby = in_array($orderby,array('deposit','withdraw','bet','win','gold')) ? $orderby . '  DESC ' : ' deposit DESC ';

		$today_table = 'spin_log_' . date('Y_m_d');

		$list = $this->alias('ui')
			->field('su.user_name,ui.account_id,ui.gold,ui.vip_level,ui.user_id,ui.create_time,
			SUM(CASE uoi.order_type WHEN 210100 THEN uoi.amount ELSE 0  END) deposit,
			((SELECT IFNULL(SUM(total_bet),0) FROM laohu_log.spin_stat WHERE user_id = ui.user_id) + (SELECT IFNULL(SUM(total_bet),0) FROM laohu_log.`' . $today_table . '` WHERE user_id = ui.user_id)) bet,
			((SELECT IFNULL(SUM(total_win),0) FROM laohu_log.spin_stat WHERE user_id = ui.user_id) + (SELECT IFNULL(SUM(win),0) FROM laohu_log.`' . $today_table . '` WHERE user_id = ui.user_id)) win,
			SUM(case uoi.order_type WHEN 210200 THEN uoi.amount ELSE 0  END) withdraw')
			->join('LEFT JOIN t_sys_user su ON su.uid = ui.operator_id')
			->join('LEFT JOIN t_user_order_info uoi ON uoi.player_id = ui.user_id And uoi.`status` = 1')
			->where($where)
			->group('ui.user_id')
			->order($orderby . ', create_time DESC')
			->limit($page->firstRow.','.$page->listRows)
			->select();

		return array(
			'list' => $list,
			'page' => $page->show(),
		);
	}

	// 
	public function get_user_id($account_id){
		return $this->where('account_id = "%s"',array($account_id))->getField('user_id');
	}

	public function get_user_id_by_operator($account_id,$operator_id){
		return $this->where('operator_id = %d AND account_id = "%s" AND status = 1',array($operator_id,$account_id))->getField('user_id');
	}
}
