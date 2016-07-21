<?php
namespace Admin\Model;
use Think\Model;

class SysLogModel extends Model {

	const ADMIN_DO_LOG			= 110100;	// 后台操作
	const API_DO_LOG			= 110200;	// api操作

	const PLAYER_LOGIN			= 120110;	// 玩家登录
	const PLAYER_REGISTER		= 120120;	// 玩家注册
	const PLAYER_UPDATE_PWD	= 120130;	// 修改玩家密码
	const GET_PLAYER_INFO		= 120140;	// 获取玩家信息

	const FROZEN_PLAYER			= 120150;	// 冻结玩家账号
	const GET_PLAYER_SPIN_LOG	= 120160;	// 获取玩家投注记录

	const PLAYER_DEPOSIT		= 120170;	// 玩家充值
	const PLAYER_WITHDRAW		= 120180;	// 玩家取现

	const GET_PLAYER_ORDER_INFO 		= 120190;
	const SET_VIP_LEVEL 		= 120200;

	// 添加日志
	public function add_log($do_type,$content,$log_type,$operator_id=null,$player_id=null,$remark=''){

		$data['do_type'] = $do_type;
		$data['content'] = $content;
		$data['log_type'] = $log_type;
		$data['create_time'] = time();

		$data['operator_id'] = $operator_id;
		$data['player_id'] = $player_id;
		$data['remark'] = $remark;

		return $this->add($data);
	}

}
