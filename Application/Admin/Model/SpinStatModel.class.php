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
}
