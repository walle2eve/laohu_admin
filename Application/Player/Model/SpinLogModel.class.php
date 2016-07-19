<?php
namespace Player\Model;
use Think\Model;

class SpinLogModel extends Model
{
     protected $connection = 'DB_LAOHU_LOG_CONFIG';
	   //protected $trueTableName;
	   protected $tablePrefix = '';
     // 获取游戏记录
     public function bet_log($operator_id,$begin_time,$end_time,$order_by='win DESC',$account_id=''){
       $where = ' 1=1 ';
       if($operator_id != ''){
         $where .= " AND operator_id = " . $operator_id . " ";
       }
       // 数据表中存储的是java类型的时间戳，包含毫秒，需要转换
       $where .= ' AND (createTime BETWEEN ' . ($begin_time * 1000) . ' AND ' . ($end_time * 1000 + 999) . ') ';
       if($account_id != ''){
         $where .= " AND account_id LIKE '%" . $account_id . "%' ";
       }

       $count = $this->where($where)->count();

       $page = page($count);

       $order_by = $order_by . ',createTime DESC';

       $list = $this->alias('t')->field('t.*,suser.user_name')->join('LEFT JOIN laohu.t_sys_user suser ON suser.uid = t.operator_id')->where($where)->order($order_by)->limit($page->firstRow.','.$page->listRows)->select();

       //echo $this->getlastsql();

       foreach($list as &$row){
         // 格式化附加参数
         $json_data = (array)json_decode($row['param']);
         $row['line'] = count($json_data);
         ///echo $row['param'];
         ///echo "<br />";
         ///print_r($json_data);exit();
         // 矩阵 图标
         $wheel = $row['wheel'];
         $wheel = rtrim($wheel, "]");
         $wheel = ltrim($wheel, "[");
         $wheel = explode(',',$wheel);

         $icons = array();

         // 排列规则
         if($row['theme_id'] == 1005){
           $rows = 5;
         }else{
           $rows = 3;
         }

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
         $row['icons'] = $icons;

         $line_icons = array();
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