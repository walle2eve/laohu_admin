<?php
/**
 * Created by PhpStorm.
 * User: panda
 * Date: 2017/8/8
 * Time: 10:20
 */

namespace Admin\Model;
use Think\Model;

class SysRoleOperatorModel extends Model
{
    public function add_all($role_id,$data=array()){
        $this->where('role_id = %d',array($role_id))->delete();
        return $this->addAll($data);
    }
    public function get_all(){
        return $this->alias('sro')
            ->field('op.name,op.logogram,op.type,op.discount,op.access_key,op.encry_type,op.status,op.gold,op.deposit_money,op.deposit_gold,op.storage_type,op.storage_bucket,op.storage_access_id,op.storage_access_key,op.storage_endpoint,op.input_time,op.is_diy,op.diy_db_name,sro.operator_id,sro.role_id,ro.role_name')
            ->join('LEFT JOIN __SYS_ROLE__ ro ON sro.role_id = ro.id')
            ->join('LEFT JOIN __OPERATOR__ op ON sro.operator_id = op.id')
            ->where('ro.status = 1 AND op.status = 1')
            ->select();
    }
}