<?php
namespace Admin\Model;
use Think\Model;

class SpinStatModel extends Model{

  	protected $connection = 'DB_LAOHU_LOG_CONFIG';
	protected $tablePrefix = '';

	public $today_spin_table = '';

	public function __construct(){
		parent::__construct();
		$this->today_spin_table = 'laohu_log.spin_log_' . date('Y_m_d');
	}
	// 获取平台总投注额
	public function get_bet_sum($operator_id){
		$today_bet = M()->table($this->today_spin_table)->where('operator_id = %d',array($operator_id))->sum('total_bet');
		$total_bet = $this->where('operator_id = %d',array($operator_id))->sum('total_bet');
		//echo M()->getlastsql();exit();
		return floatval($today_bet + $total_bet);
	}
	// 获取平台总赢取额
	public function get_win_sum($operator_id){
		$today_win = M()->table($this->today_spin_table)->where('operator_id = %d',array($operator_id))->sum('win');
		$total_win = $this->where('operator_id = %d',array($operator_id))->sum('total_win');
		return floatval($today_win + $total_win);
	}
	// 获取游戏主题统计信息
	public function get_theme_stat($year){

		$result = D('SysUser')->get_operator();

		$list = $this->alias('spin_stat')->field("stat_year,stat_month,theme_id,theme_info.name AS theme_name,operator_id,COUNT(DISTINCT user_id) play_count")->join("LEFT JOIN laohu.t_theme_info AS theme_info ON theme_info.id = spin_stat.theme_id")->where('stat_year = %d',array($year))->group('stat_year,stat_month,operator_id,theme_id')->select();

		return array('operator_info' => $result, 'stat_list' => $list);
	}
	// 获取总投注次数
	//public function get_count_bet($operator_id,$account_id)
}
