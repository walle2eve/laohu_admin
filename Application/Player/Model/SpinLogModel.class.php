<?php
namespace Player\Model;

use Think\Model\MongoModel;

class SpinLogModel extends MongoModel
{
   protected $connection  =  'DB_TYPE_MONGO_CONFIG';
   protected $dbName      =  'laohu_log';
   protected $tablePrefix  =  '';

   public function _initialize(){
      parent::_initialize();

      ini_set('mongo.long_as_object', 1);
   }

   public function bet_log($operator_id,$begin_time,$end_time,$order_by='win DESC',$account_id=''){
      
      $where =  array();

      // 数据表中存储的是java类型的时间戳，包含毫秒，需要转换  
      $where['createTime'] =  array('between', array($begin_time * 1000, $end_time * 1000 + 999));

      if($operator_id != ''){
         $where['operator_id'] = intval($operator_id);
      }
      if($account_id != ''){
         $where['account_id'] = $account_id;
      }else{
         //$where['account_id'] = array('all','');
      }

      $count = $this->where($where)->count();

      $page = page($count);

      //$order_by = $order_by . ',id DESC';

      $order_by = '_id DESC';

      $list = $this->field("id,log_type,log_time,region_id,server_id,operator_id,theme_id,theme_name,game_sort,account_id,nick_name,user_id,bet,mul,total_bet,win,wheel,is_sactter,reason,param,createTime")->where($where)->order($order_by)->limit($page->firstRow.','.$page->listRows)->select();


      $operators = S('user_roles');

      foreach($list as &$row){
         foreach($row as $key=>$val){
            if(is_object($row[$key])){
               $row[$key] = $val->value;
            }
         }
         // 格式化附加参数
         $json_data = (array)json_decode($row['param']);
         $row['line'] = count($json_data);

         // 矩阵 图标
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

         $line_icons = array();

          if(in_array($row['theme_id'],array('1004'))){
             $line = 9;
          }elseif(in_array($row['theme_id'],array('1005'))){
             $line = 1;
         }elseif(in_array($row['theme_id'],array('1003','1010'))){
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

         $createTime = is_object($row['createTime']) ? (array)$row['createTime'] : $row['createTime'];
         $row['createTime'] = isset($createTime['value']) ? $createTime['value'] : $createTime;

         $operator_id = is_object($row['operator_id']) ? (array)$row['operator_id'] : $row['operator_id'];
      
         $row['user_name'] = isset($operator_id['value']) ? $operator_id['value'] : $operator_id;
         $row['user_name'] = $operators[$row['user_name']];
      }

      return array('list'=>$list,'page'=>$page->show());
   }
}
