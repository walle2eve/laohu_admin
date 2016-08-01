<?php
namespace Admin\Model;

use Think\Model;

class ThemePlayedStatModel extends Model{

    protected $connection = 'DB_LAOHU_LOG_CONFIG';
	//protected $trueTableName;
	protected $tablePrefix = '';

	public function stat(){
		
		$operator_info = array();
		$case_sql = '';

		$result = D('SysUser')->get_operator();

		foreach($result as $key=>$row){
			$case_sql .= "SUM(CASE WHEN operator_id = ". $row['uid'] ." THEN play_count ELSE 0 END) AS `" . $row['login_name'] . "`,";
			$operator_info[$row['login_name']] = $row;
		}

		$list = $this->field('theme_id,theme_name,' . $case_sql . ' 0 AS tmp_field')->where()->group('theme_id')->select();

		return array('operator_info' => $result, 'stat_list' => $list);
	}
}