<?php
namespace Admin\Model;
use Think\Model;

class ActivityModel extends Model{

	const STATUS_NONE	= 0;	// 未开始

	const STATUS_BEGIN	= 1;	// 进行中

	const STATUS_DONE	= 2;	// 已完成

	const STATUS_CLOSE	= 3;	// 已关闭

    protected $dbName =	'laohu';

    public function __construct($dbName=''){
        parent::__construct();
        if($dbName != '')$this->dbName = $dbName;
    }

	public function alist($user_role,$param){
		$where = ' 1=1 ';

		$time = time();

		if(isset($param['operator_id']) && intval($param['operator_id']) > 0){
			$where .= " AND a.operator_id = " . intval($param['operator_id']);
		}

		$where .= " AND sro.role_id = " . $user_role;

		$count = $this->alias('a')
            ->join('LEFT JOIN __OPERATOR__ op ON op.id = a.operator_id')
            ->join('LEFT JOIN __SYS_ROLE_OPERATOR__ sro ON sro.operator_id = op.id')
            ->where($where)->count();

		$page = page($count);

		$list = $this->alias('a')
					->field('a.id,a.operator_id,op.name as operator_name,a.begin_time,a.end_time,a.status,a.remark')
					->join('LEFT JOIN __OPERATOR__ op ON op.id = a.operator_id')
                    ->join('LEFT JOIN __SYS_ROLE_OPERATOR__ sro ON sro.operator_id = op.id')
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

	public function get_one($id){
        return $this->alias('act')
            ->field('act.*,op.name as operator_name')
            ->join('Left join __OPERATOR__ op ON op.id = act.operator_id')
            ->where('act.id = %d',array($id))->find();
    }
}
?>