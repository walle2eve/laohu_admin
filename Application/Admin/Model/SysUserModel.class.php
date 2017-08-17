<?php
namespace Admin\Model;
use Think\Model;

class SysUserModel extends Model
{
    protected $fields = array(
								'uid',
								'user_name',
								'user_role',
								'login_name',
								'login_pwd',
								'input_time',
								'input_name',
								'input_phone',
								'input_email',
								'salt',
								'status',
								'last_login_time',
								'last_login_ip',
								'login_count'
							);

    protected $pk     = 'uid';

	protected $_validate = array(
		array('user_role','require','权限组必须'),
		array('login_name','','登录名称已经存在',0,'unique',1), // 在新增的时候验证login_name字段是否唯一
		array('login_pwd','require','登录密码必须'),
		//array('user_name','require','平台名称必须'),
	);

	public function get_login($login_name){
		return $this->where("login_name = '%s'",array($login_name))->find();
	}

	// 保存登录信息
	public function save_login_info($user){
		$client_ip = get_client_ip();
		$data = array(
			'last_login_time' => time(),
			'last_login_ip' => $client_ip,
			'login_count' => intval($user['login_count']) + 1,
		);
		$this->where('uid = %d',array($user['uid']))->save($data);
	}

	// 获取用户列表
	public function get_list($param = array()){
		$where = ' 1=1 ';
		if(isset($param['keyword']) && trim($param['keyword']) != ''){
			$where .= " AND (su.user_name LIKE '%".$param['keyword']."%' OR su.login_name LIKE '%".$param['keyword']."%') ";
		}

		$count = $this->alias('su')->where($where)->count();

		$page = page($count);

		$list = $this->alias('su')
                    ->field('su.*,sr.role_name')
					->join('LEFT JOIN __SYS_ROLE__ sr ON sr.id = su.user_role')
					->where($where)
					->order('su.uid DESC')
					->limit($page->firstRow.','.$page->listRows)
					->select();
		return array('list'=>$list,'page'=>$page->show());
	}

}
