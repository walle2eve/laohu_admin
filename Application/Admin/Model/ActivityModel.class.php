<?php
namespace Admin\Model;
use Think\Model;

class ActivityModel extends Model{

	public function alist($param){
		$where = ' 1=1 ';

		if(isset($param['operator']) && intval($param['operator']) > 0){
			$where .= " AND a.operator_id = " . intval($param['operator']);
		}

		$count = $this->alias('a')->where($where)->count();

		$page = page($count);

		$list = $this->alias('a')
					->field('a.id,a.operator_id,su.user_name as operator_name,a.begin_time,a.end_time,a.status,a.activity_switch,a.remark')
					->join('LEFT JOIN t_sys_user su ON su.uid = a.operator_id')
					->where($where)
					->group('a.id')
					->order('create_time DESC,id Desc')
					->limit($page->firstRow.','.$page->listRows)
					->select();
		return array('list'=>$list,'page'=>$page->show());
	}
}
?>