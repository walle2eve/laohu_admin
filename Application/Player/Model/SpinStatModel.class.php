<?php
namespace Player\Model;
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
		return floatval($today_bet + $total_bet);

	}
	// 获取平台总赢取额
	public function get_win_sum($operator_id){

		$today_win = M()->table($this->today_spin_table)->where('operator_id = %d',array($operator_id))->sum('win');
		$total_win = $this->where('operator_id = %d',array($operator_id))->sum('total_win');
		return floatval($today_win + $total_win);

	}

	// 获取游戏主题统计信息
	public function get_theme_stat($operator_options,$year,$operator_id=0){

	    $operator_ids = array_keys($operator_options);

        $whereSql = 'stat_year = %d';
		$whereAr = array($year);

		if($operator_id){
		    $whereSql .= ' And operator_id = %d';
		    array_push($whereAr,$operator_id);
        }else{
		    $whereSql .= ' AND operator_id IN (' .implode(',',$operator_ids ). ')';
        }

		$list = $this->alias('spin_stat')
            ->field("stat_year,stat_month,theme_id,theme.name AS theme_name,operator_id,COUNT(DISTINCT user_id) play_count")
            ->join("LEFT JOIN laohu.t_theme AS theme ON theme.id = spin_stat.theme_id")
            ->where($whereSql,$whereAr)
            ->group('stat_year,stat_month,operator_id,theme_id')
            ->select();
		return array('operator_info' => $operator_options, 'stat_list' => $list);
	}

	// 获取总投注次数
	public function get_count_bet($operator_id,$begin_time,$end_time,$account_id = ''){
		$where['operator_id'] = $operator_id;
		$where['stat_date']	=	array('between', array(date('Y-m-d',$begin_time), date('Y-m-d',$end_time)));
		if($account_id != '')
			$where['user_id'] = D('UserInfo')->get_user_id($account_id);
		return $this->where($where)->sum('count_bet');
	}
}
