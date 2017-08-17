<?php
/**
 * Created by PhpStorm.
 * User: panda
 * Date: 2017/8/7
 * Time: 12:20
 */

namespace Admin\Model;
use Think\Model;

class SysRoleModel extends Model
{
    protected $fields = array(
        'id',
        'role_name',
        'status',
        'input_time'
    );

    protected $_validate = array(
        array('role_name','require','名称必填'),
        array('role_name','','权限组名称已经存在',0,'unique',1),
    );

    public function get_all(){
        return $this->where('status = 1')->select();
    }

    public function get_list($param = array()){

        $where = ' 1=1 ';

        if(isset($param['keyword']) && trim($param['keyword']) != ''){
            $where .= " AND (role_name LIKE '%".$param['keyword']."%') ";
        }

        $count = $this->where($where)->count();

        $page = page($count);

        $list = $this
            ->where($where)
            ->order('id asc')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        return array('list'=>$list,'page'=>$page->show());
    }
}