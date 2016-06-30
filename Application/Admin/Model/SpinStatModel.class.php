<?php 
namespace Admin\Model;
use Think\Model;

class SpinStatModel extends Model{
	
    protected $connection = 'DB_LAOHU_LOG_CONFIG';
	protected $tablePrefix = ''; 
	
	// 获取平台总投注额
	public function get_bet_sum($operator_id){
		return $this->where('operator_id = %d',array($operator_id))->sum('total_bet');
	}
	// 获取平台总赢取额
	public function get_win_sum($operator_id){
		return $this->where('operator_id = %d',array($operator_id))->sum('total_win');
	}
}