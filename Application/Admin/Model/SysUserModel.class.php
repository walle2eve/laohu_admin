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
								'discount',
								'access_key',
								'salt',
								'status',
								'last_login_time',
								'last_login_ip',
								'login_count'
							);

    protected $pk     = 'uid';

	protected $_validate = array(
		array('user_role','require','用户类别必须'),
		array('login_name','','登录名称已经存在',0,'unique',1), // 在新增的时候验证login_name字段是否唯一
		array('login_pwd','require','登录密码必须'),
		array('user_name','require','平台名称必须'),
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

		$count = $this->alias('su')->join('LEFT JOIN __SYS_DICT__ sd ON sd.dict_id = su.user_role')->where($where)->count();

		$page = page($count);

		$list = $this->alias('su')
					->join('LEFT JOIN __SYS_DICT__ sd ON sd.dict_id = su.user_role')
					->where($where)
					->order('uid DESC')
					->limit($page->firstRow.','.$page->listRows)
					->select();
		return array('list'=>$list,'page'=>$page->show());
	}

	//获取运营商信息
	public function get_operator(){

		// 根据当前登录用户类别判断显示
		$login_user = session('login_user');

		$where = ' status = 1 ';

		switch($login_user['user_role']){
			case SysDictModel::USER_ROLE_ADMIN :
				$where .= ' AND user_role IN (' . SysDictModel::USER_ROLE_OPERATOR .',' . SysDictModel::USER_ROLE_AGENT . ') ';
			break;
			case SysDictModel::USER_ROLE_OPERATOR :
			case SysDictModel::USER_ROLE_AGENT :
				$where .= ' AND uid = ' . $login_user['uid'];
			break;
			default:
				return array();
			break;
		}
		///   echo $where;
		return $this->where($where)->select();
	}

	// 获取用户消费统计信息
	public function get_user_bet_stat(){
		$operator_info = $this->get_operator();
		foreach($operator_info as &$row){
			// 用户数
			$row['player_count'] = D('UserInfo')->get_player_nums($row['uid']);
			// 用户存入
			$row['player_deposit'] = D('UserOrderInfo')->get_deposit_sum($row['uid']);
			// 用户取现
			$row['player_withdraw'] = D('UserOrderInfo')->get_withdraw_sum($row['uid']);
			// 用户投注额
			$row['player_bet'] = D('SpinStat')->get_bet_sum($row['uid']);
			// 用户赢取额
			$row['player_win'] = D('SpinStat')->get_win_sum($row['uid']);
		}

		return $operator_info;
	}
}
