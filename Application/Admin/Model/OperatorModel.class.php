<?php
/**
 * Created by PhpStorm.
 * User: panda
 * Date: 2017/7/26
 * Time: 15:05
 */

namespace Admin\Model;
use Think\Model;

class OperatorModel extends Model
{
    protected $fields = array(
    'id','name', 'logogram', 'remark', 'type', 'discount', 'encry_type', 'access_key',
    'status', 'gold', 'deposit_money', 'deposit_gold', 'storage_type',
        'storage_bucket', 'storage_access_id', 'storage_access_key', 'storage_endpoint', 'input_time',
        'is_diy', 'diy_db_name'
);

    protected $pk     = 'id';

    protected $_validate = array(
        array('type','require','运营商类别必选'),
        array('logogram','','运营商简称不能重复',0,'unique',1), // 在新增的时候验证logogram字段是否唯一
        array('name','require','运营商名称必填'),
    );

    public function get_all(){
        return $this->select();
    }

    // 获取运营商信息列表
    public function get_list($param = array()){
        $where = ' 1=1 ';
        if(isset($param['keyword']) && trim($param['keyword']) != ''){
            $where .= " AND (op.name LIKE '%".$param['keyword']."%' ) ";
        }

        $count = $this->alias('op')->join('LEFT JOIN __SYS_DICT__ sd ON sd.dict_id = su.type')->where($where)->count();

        $page = page($count);

        $list = $this->alias('op')
            ->join('LEFT JOIN __SYS_DICT__ sd ON sd.dict_id = op.type')
            ->where($where)
            ->order('id DESC')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        return array('list'=>$list,'page'=>$page->show());
    }

    // 获取用户消费统计信息
    public function get_user_bet_stat($operator_info = array()){

        foreach($operator_info as &$row){
            // 用户数
            $row['player_count'] = D('UserInfo')->get_player_nums($row['operator_id']);
            // 用户存入
            $row['player_deposit'] = D('UserOrderInfo')->get_deposit_sum($row['operator_id']);
            // 用户取现
            $row['player_withdraw'] = D('UserOrderInfo')->get_withdraw_sum($row['operator_id']);
            // 用户投注额
            $row['player_bet'] = D('SpinStat')->get_bet_sum($row['operator_id']);
            // 用户赢取额
            $row['player_win'] = D('SpinStat')->get_win_sum($row['operator_id']);
            // 平台玩家总剩余金币数
            $row['player_balance'] = D('UserInfo')->get_player_balance($row['operator_id']);
        }

        return $operator_info;
    }

    // 获取玩家登录统计信息
    public function get_user_login_stat($operator_info = array(),$start_date = ''){

        $data = array();

        foreach($operator_info as $key=>$row){
            // 玩家连续登录天数信息
            $player_login_count = D('LoginLogStat')->get_login_stat_by_operator($row['operator_id'],$start_date);

            $data[$key]['user_name'] = $row['name'];
            $data[$key] = array_merge($data[$key],$player_login_count);
            //$data[$key]['login_count'] = $player_login_count['login_count'];
        }

        return $data;
    }
}