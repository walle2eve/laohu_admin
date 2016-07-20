<?php
namespace Admin\Model;
use Think\Model;

class UserInfoModel extends Model{

	// 平台用户数,取状态有效的用户
	public function get_player_nums($operator_id){
		return $this->where('status = 1 AND operator_id = %d',array($operator_id))->count();
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
			->field('su.user_name,ui.account_id,ui.gold,
			SUM(CASE uoi.order_type WHEN 210100 THEN uoi.amount ELSE 0  END) deposit,
			((SELECT SUM(total_bet) FROM laohu_log.spin_stat WHERE user_id = ui.user_id) + (SELECT SUM(total_bet) FROM laohu_log.`' . $today_table . '` WHERE user_id = ui.user_id)) bet,
			((SELECT SUM(total_win) FROM laohu_log.spin_stat WHERE user_id = ui.user_id) + (SELECT SUM(win) FROM laohu_log.`' . $today_table . '` WHERE user_id = ui.user_id)) win,
			SUM(case uoi.order_type WHEN 210200 THEN uoi.amount ELSE 0  END) withdraw')
			->join('LEFT JOIN t_sys_user su ON su.uid = ui.operator_id')
			->join('LEFT JOIN t_user_order_info uoi ON uoi.player_id = ui.user_id And uoi.`status` = 1')
			//->join('LEFT JOIN laohu_log.spin_stat ss ON ss.user_id = ui.user_id')
			->where($where)
			->group('ui.user_id')
			->order($orderby)
			->limit($page->firstRow.','.$page->listRows)
			->select();
			//echo $this->getlastsql();
		return array(
			'list' => $list,
			'page' => $page->show(),
		);
	}
}
