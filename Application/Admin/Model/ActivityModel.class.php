<?php
namespace Admin\Model;
use Think\Model;

class ActivityModel extends Model{

	const STATUS_NONE	= 0;	// 未开始

	const STATUS_BEGIN	= 1;	// 进行中

	const STATUS_DONE	= 2;	// 已完成

	const STATUS_CLOSE	= 3;	// 已关闭

	public function alist($param){
		$where = ' 1=1 ';

		$time = time();

		if(isset($param['operator']) && intval($param['operator']) > 0){
			$where .= " AND a.operator_id = " . intval($param['operator']);
		}

		$count = $this->alias('a')->where($where)->count();

		$page = page($count);

		$list = $this->alias('a')
					->field('a.id,a.operator_id,su.user_name as operator_name,a.begin_time,a.end_time,a.status,a.remark')
					->join('LEFT JOIN t_sys_user su ON su.uid = a.operator_id')
					->where($where)
					->group('a.id')
					->order('create_time DESC,id Desc')
					->limit($page->firstRow.','.$page->listRows)
					->select();
		foreach($list as &$row){
			$show_status = 0;
			if($row['begin_time'] > $time){ // 活动未开始
				$show_status = self::STATUS_NONE;
			}elseif($row['begin_time'] <= $time && $row['end_time'] >= $time){ 
				$show_status = $row['status'] == 1 ? self::STATUS_BEGIN : self::STATUS_NONE;
			}elseif($row['end_time'] < $time){
				$show_status = $row['status'] == 1 ? self::STATUS_DONE : self::STATUS_CLOSE;
			}
			$row['show_status'] = $show_status;
			$row['show_status_content'] = L('activity_status')[$show_status];
		}
		return array('list'=>$list,'page'=>$page->show());
	}
}
?>