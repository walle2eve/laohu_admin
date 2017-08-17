<?php
/**
 * Created by PhpStorm.
 * User: panda
 * Date: 2017/8/7
 * Time: 15:44
 */

namespace Admin\Model;
use Think\Model;

class SysRoleFuncModel extends Model
{
    public function add_all($role_id,$data=array()){
        $this->where('role_id = %d',array($role_id))->delete();
        return $this->addAll($data);
    }

    public function get_all(){
        return $this->alias('srf')
            ->field('func.func_name,func.module,func.controller,func.action,srf.func_id,ro.role_name,srf.role_id')
            ->join('LEFT JOIN __SYS_ROLE__ ro ON srf.role_id = ro.id')
            ->join('LEFT JOIN __SYS_FUNC__ func ON srf.func_id = func.func_id')
            ->where('ro.status = 1 AND func.is_del = 0')
            ->select();
    }
}