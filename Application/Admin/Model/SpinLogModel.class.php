<?php
namespace Admin\Model;
use Think\Model;

class SpinLogModel extends Model
{
    protected $connection = 'DB_LAOHU_LOG_CONFIG';
	//protected $trueTableName;
	protected $tablePrefix = '';
	/***
	public function __construct($table_name){
		parent::__construct($table_name);
		$this->trueTableName = $table_name;
	}

	// 获取游戏记录
	public function bet_log($operator_id,$begin_time,$end_time,$order_by='win DESC',$account_id='',$tables=array()){

		$where = ' 1=1 ';
		if($operator_id != ''){
			$where .= " AND operator_id = " . $operator_id . " ";
		}
		// 数据表中存储的是java类型的时间戳，包含毫秒，需要转换
		$where .= ' AND (createTime BETWEEN ' . ($begin_time * 1000) . ' AND ' . ($end_time * 1000 + 999) . ') ';
		if($account_id != ''){
			$where .= " AND account_id LIKE '%" . $account_id . "%' ";
		}

		if(empty($tables)){
			return array('list'=>array(),'page'=>'暂无符合条件的记录');
		}

		//
		$count_sqls = array();
		$sqls = array();

		foreach($tables as $table){
			$count_sqls[] = " SELECT COUNT(*) AS tp_num FROM  " . $table . " WHERE " . $where;
			$sqls[] = " SELECT * FROM  " . $table;
		}

		$count_table = "(". implode(' UNION ALL ',$count_sqls) .")";

		$table = "(". implode(' UNION ALL ',$sqls) .")";

		$count = $this->table($count_table)->alias('t')->sum('tp_num');

		$page = page($count);

		$list = $this->table($table)->alias('t')->where($where)->order($order_by)->limit($page->firstRow.','.$page->listRows)->select();

		// echo $this->getlastsql();exit();

		foreach($list as &$row){
			// 格式化附加参数
			$json_data = (array)json_decode($row['param']);
			$row['line'] = count($json_data);
		}
		return array('list'=>$list,'page'=>$page->show());
	}**/
	// 获取游戏记录
	public function bet_log($operator_id,$begin_time,$end_time,$order_by='win DESC',$account_id=''){
		$where = ' 1=1 ';

		// 数据表中存储的是java类型的时间戳，包含毫秒，需要转换
		$where .= ' AND (createTime BETWEEN ' . ($begin_time * 1000) . ' AND ' . ($end_time * 1000 + 999) . ') ';
		if($operator_id != ''){
			$where .= " AND operator_id = " . $operator_id . " ";
		}else{
			$where .= " AND operator_id <> 0 ";
		}
		if($account_id != ''){
			$where .= " AND account_id = '" . $account_id . "' ";
		}else{
			$where .= " AND account_id <> '' ";
		}

		$count = $this->where($where)->count();

		$page = page($count);

		$order_by = $order_by . ',id DESC';

		$list = $this->alias('t')->field('t.*,suser.user_name')->join('LEFT JOIN laohu.t_sys_user suser ON suser.uid = t.operator_id')->where($where)->order($order_by)->limit($page->firstRow.','.$page->listRows)->select();

		//echo $this->getlastsql();

		foreach($list as &$row){
			// 格式化附加参数
			$json_data = (array)json_decode($row['param']);
			$row['line'] = count($json_data);

			// 矩阵 图标
			$wheel = $row['wheel'];
			$wheel = rtrim($wheel, "]");
			$wheel = ltrim($wheel, "[");
			$wheel = explode(',',$wheel);

			$icons = array();

			// 排列规则
      list($rows,$columns) = explode(',',$row['game_sort']);

			$wheel = trim_array($wheel);
			// 矩阵图标
			foreach($wheel as $key=>$val){
				$t_k = $key%$rows;
				$icon = get_game_icon($row['theme_id'],$val);
				if($rows == 5){
					// 5行3列只显示中间一行
					if(in_array($t_k,array(2))){
						$icons[$t_k][] = $icon;
					}
				}else{
					$icons[$t_k][] = $icon;
				}
			}
      //print_r($icons);exit();
			$row['icons'] = $icons;

			$line_icons = array();

      if(in_array($row['theme_id'],array('1004'))){
        $line = 9;
      }elseif(in_array($row['theme_id'],array('1005'))){
        $line = 1;
      }else{
        $line = 20;
      }

			// 中奖线图标
			if($row['line'] > 0){
				foreach($json_data as $key=>$line_row){
					$t_k = $key%$rows;
					list($win_line,,,,,) = explode(':',$line_row);
					$icon = get_win_line_icon($win_line);
					$line_icons[$t_k][] = $icon;
				}
			}

			$row['win_line_icons'] = $line_icons;
			///print_r($row);exit();
		}
		return array('list'=>$list,'page'=>$page->show());
	}
}
