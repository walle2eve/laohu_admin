<?php 
namespace Admin\Model;
use Think\Model;

class SysFuncModel extends Model
{
	public function get_user_func($user_role,$up_id=0){
	    $whereSql = 'func.up_id = %d AND func.is_del = 0';
	    $whereAr = array($up_id);

	    if($up_id > 0){
	        $whereSql .= '  AND rf.role_id = %d ';
	        array_push($whereAr,$user_role);
        }

		return D('SysFunc')->alias('func')
						->field('func.*')
						->join('LEFT JOIN __SYS_ROLE_FUNC__ rf ON rf.func_id = func.func_id')
                        ->join('LEFT JOIN __SYS_ROLE__ sr ON sr.id = rf.func_id')
						->where($whereSql,$whereAr)
						->order('order_num,func_id')
						->select();
	}

	public function get_all($up_id = 0){
	    $func_all = S('func_all');

	    if(!$func_all){
            $menu = $this->where('up_id = %d AND is_del = 0',array($up_id))->order('order_num ASC,func_id asc')->select();
            foreach($menu as &$row){
                if($menu['is_last'] == 0)
                    $row['son_list'] = $this->get_all($row['func_id']);
            }
        }
	    $func_all = $menu;

        return $func_all;
    }
}