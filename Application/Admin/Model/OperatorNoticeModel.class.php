<?php
namespace Admin\Model;

use Think\Model;

class OperatorNoticeModel extends Model{
	protected $_validate = array(
	     array('title','require','标题必须填写'), 
	     array('operator_id','require','请选择平台'),
	     array('content','require','请填写通知内容'),  
	     //array('content','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
	     //array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
	     //array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
	     //array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
    );
	public function nlist($user_role,$param){
		$where = ' 1=1 ';

		if(isset($param['operator_id']) && abs($param['operator_id']) > 0){
			$where .= " AND no.operator_id = " . intval($param['operator_id']);
		}

		if($param['operator_id'] < 0){
        }else{
            $where .= " AND sro.role_id = " . $user_role;
        }

		$count = $this->alias('no')
            ->join('LEFT JOIN __OPERATOR__ op ON op.id = no.operator_id')
            ->join('LEFT JOIN __SYS_ROLE_OPERATOR__ sro ON sro.operator_id = op.id')
            ->where($where)->count();

		$page = page($count);

		$list = $this->alias('no')
					->field('no.*,op.name As operator_name,(SELECT user_name FROM t_sys_user WHERE uid = no.dispose_user) AS dispose_name')
					->join('LEFT JOIN __OPERATOR__ op ON op.id = no.operator_id')
                    ->join('LEFT JOIN __SYS_ROLE_OPERATOR__ sro ON sro.operator_id = op.id')
					->where($where)
					->order('create_time DESC')
					->limit($page->firstRow.','.$page->listRows)
					->select();

		return array('list'=>$list,'page'=>$page->show());
	}
}