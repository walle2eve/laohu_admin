<?php 
namespace Admin\Model;
use Think\Model;

class SysDictModel extends Model {
	// 平台用户
	//const USER_ROLE_OPERATOR = 100210;
	// 代理用户
	//const USER_ROLE_AGENT	= 100220;
	// 管理员用户
	//const USER_ROLE_ADMIN	= 100110;
	
	public function get_dict($dict_type){
		return $this->where("dict_type = %d",array($dict_type))->select();
	}
}