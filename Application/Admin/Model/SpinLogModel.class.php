<?php
namespace Admin\Model;
//use Think\Model;
use Think\Model\MongoModel;

//class SpinLogModel extends Model
class SpinLogModel extends MongoModel
{
    //protected $connection = 'DB_LAOHU_LOG_CONFIG';
	//protected $trueTableName;
	//protected $tablePrefix = '';

    protected $connection 	= 	'DB_TYPE_MONGO_CONFIG';
    protected $dbName		=	'laohu_log';
	protected $tablePrefix 	= 	'';

	public function _initialize(){
		parent::_initialize();

		ini_set('mongo.long_as_object', 1);
	}

	// 获取游戏记录
	/**
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
	  }elseif(in_array($row['theme_id'],array('1003'))){
        $line = 50;
      }else{
        $line = 20;
      }

			// 中奖线图标
			if($row['line'] > 0){
				foreach($json_data as $key=>$line_row){
					$t_k = $key%$rows;
					list($win_line,,,,,) = explode(':',$line_row);
					$icon = get_win_line_icon($win_line,$line);
					$line_icons[$t_k][] = $icon;
				}
			}

			$row['win_line_icons'] = $line_icons;
			///print_r($row);exit();
		}
		return array('list'=>$list,'page'=>$page->show());
	}
	**/
	public function bet_log($operator_id,$begin_time,$end_time,$order_by='win DESC',$account_id=''){
		
		$where =  array();

		// 数据表中存储的是java类型的时间戳，包含毫秒，需要转换	
		$betweentime  = array($begin_time * 1000, $end_time * 1000 + 999);
		$where['createTime']	=	array('between', $betweentime);

		if($operator_id != ''){
			$where['operator_id'] = intval($operator_id);
		}
		if($account_id != ''){
			$where['account_id'] = $account_id;
		}else{
			//$where['account_id'] = array('all','');
		}

		//$count = $this->where($where)->count();
		$count = D('SpinStat')->get_count_bet($operator_id,$begin_time,$end_time,$account_id);
		
		if(date('Y-m-d',$end_time) == date('Y-m-d')){
			$stime = strtotime(date('Y-m-d'));
			$etime = $stime+86399;
			$today_where = $where;
			$today_where['createTime'] = array('between', array($stime * 1000, $etime * 1000 + 999));
			//$today_where['log_time'] = array('between', array($stime * 1000, $etime * 1000 + 999));
			$count_today = $this->where($today_where)->count();
			//echo $count_today;
			$count = $count + $count_today;
		}

		$page = page($count);

		$order_by = $order_by . ',_id DESC';

		$list = $this->field("id,log_type,log_time,region_id,server_id,operator_id,theme_id,theme_name,game_sort,account_id,nick_name,user_id,bet,mul,total_bet,win,wheel,is_sactter,reason,param,createTime")->where($where)->order($order_by)->limit($page->firstRow.','.$page->listRows)->select();

		
		$where['createTime'] = array('$gte'=>$betweentime[0],'$lte'=>$betweentime[1]);
		$sum_args = array(
				array('$match' => $where),
				array('$group' => array(
					"_id" => NULL,
					"total_bet"  => array('$sum' => '$total_bet'),
					"total_win"  => array('$sum' => '$win')
					))
			);
		$total_bet_win = $this->aggregate($sum_args);

		if(!empty($total_bet_win['result']) && isset($total_bet_win['result'])){
			$total_bet = round($total_bet_win['result'][0]['total_bet'],2);
			$total_win = round($total_bet_win['result'][0]['total_win'],2);
		}else{
			$total_bet = 0;
			$total_win = 0;
		}

		//echo $this->getlastsql();
		//print_r($total_bet_win);
		
		$operators = S('user_roles');

		foreach($list as &$row){
			foreach($row as $key=>$val){
				if(is_object($row[$key])){
					$row[$key] = $val->value;
				}
			}

			$createTime = is_object($row['createTime']) ? (array)$row['createTime'] : $row['createTime'];
			$row['createTime'] = isset($createTime['value']) ? $createTime['value'] : $createTime;

			$operator_id = is_object($row['operator_id']) ? (array)$row['operator_id'] : $row['operator_id'];
			
			$row['user_name'] = isset($operator_id['value']) ? $operator_id['value'] : $operator_id;
			$row['user_name'] = $operators[$row['user_name']];

			// 格式化附加参数
			$json_data = (array)json_decode($row['param']);
			$row['line'] = count($json_data);
			// 
			if(!in_array($row['reason'],array(3,4,6))){
				continue;
			}

			// 矩阵 图标
			$wheel = $row['wheel'];
			$wheel = str_replace("[", "", $wheel);
			$wheel = str_replace("]", "", $wheel);
			$wheel = str_replace("{", "", $wheel);
			$wheel = str_replace("}", "", $wheel);
			$wheel = explode(',',$wheel);

			$icons = array();

			// 排列规则
      		list($rows,$columns) = explode(',',$row['game_sort']);

      		$icons_count = intval($rows * $columns);

			$wheel = trim_array($wheel);

			$row['wheel_count'] = intval(count($wheel)/$icons_count);
			if($row['wheel_count'] > 1){
				$wheel = array_chunk($wheel,$icons_count);
			}else{
				$wheel[] = $wheel;
			}

			// 矩阵图标
			foreach($wheel as $k=>$arow){
				foreach($arow as $key=>$val){
					$t_k = $key%$rows;
					$icon = get_game_icon($row['theme_id'],$val);
					if($rows == 5){
						// 5行3列只显示中间一行
						if(in_array($t_k,array(2))){
							$icons[$k][$t_k][] = $icon;
						}
					}else{
						$icons[$k][$t_k][] = $icon;
					}
				}
			}

			$row['icons'] = $icons;
			//print_r($row['icons']);exit();
			$line_icons = array();

		    if(in_array($row['theme_id'],array('1004'))){
		       $line = 9;
		    }elseif(in_array($row['theme_id'],array('1005'))){
		       $line = 1;
			}elseif(in_array($row['theme_id'],array('1003','1010'))){
		       $line = 50;
		    }elseif(in_array($row['theme_id'],array('1012'))){
		       $line = 25;
		    }else{
		       $line = 20;
		    }

		    //大闹天宫不显示中奖线
		    if($row['theme_id'] == 1011) $row['line'] = 0;

			// 中奖线图标
			if($row['line'] > 0){
				foreach($json_data as $key=>$line_row){
					$t_k = $key%$rows;
					list($win_line,,,,,) = explode(':',$line_row);
					$icon = get_win_line_icon($win_line,$line);
					$line_icons[$t_k][] = $icon;
				}
			}

			$row['win_line_icons'] = $line_icons;

		}
		return array('list'=>$list,'page'=>$page->show(),'total_bet' => $total_bet, 'total_win' => $total_win);
	}
}
