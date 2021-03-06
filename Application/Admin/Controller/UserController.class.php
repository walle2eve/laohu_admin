<?php
namespace Admin\Controller;

use Think\Controller;

use Admin\Model\SysDictModel;

use Admin\Model\SysLogModel;

class UserController extends BaseController
{
	public function _initialize(){
		parent::_initialize();
	}
	// 游戏记录
	public function bet_log()
    {
		$param = I('get.');

		// 测试开始时间
		$param['min_date'] = date('Y-m-d H:i:s',strtotime('-30 day'));
		$param['max_date'] = date('Y-m-d 23:59:59');

		if(!$param['date_range_picker'] && !$param['begin_time']){
			$param['begin_time'] = date('Y-m-d H:i:s',strtotime('-1 day'));
			$param['end_time'] = $param['max_date'];
		}

		if(!$param['begin_time'] || $param['begin_time'] < strtotime("-30 day")){
			$param['begin_time'] = strtotime(date('Y-m-d 00:00:00',strtotime("-1 day")));
			$param['end_time'] = time();
		}else{
			$param['end_time'] = $param['end_time'] ? $param['end_time'] : strtotime(date('Y-m-d 23:59:59'));
		}

		// 排序
		$orderbys = array('win','bet','total_bet','createTime');

		$param['order_by'] = !in_array($param['order_by'],$orderbys) ? 'win' : $param['order_by'];

		$this->assign('param',$param);

		$param['order_by'] = $param['order_by'] . ' DESC ';
		
		if($param['operator_id']) 
			$result = D('SpinLog')->bet_log($param['operator_id'],$param['begin_time'],$param['end_time'],$param['order_by'],$param['account_id']);
		else
			$result = array('list'=>array(),'page'=> '');

		

		$this->assign('list',$result['list']);
		$this->assign('page',$result['page']);
		$this->assign('total_bet',$result['total_bet']);
		$this->assign('total_win',$result['total_win']);
        $this->display();
    }
	// 用户资金记录
	public function money(){
		$param = I('get.');

		$result = D('UserInfo')->get_userinfo_list($this->login_user['user_role'],$param['operator_id'],$param['account_id'],$param['order_by']);

		$this->assign('param',$param);
		$this->assign('list',$result['list']);
		$this->assign('page',$result['page']);
		$this->display();
	}
	//设置vip等级
	public function set_vip_level(){
		$result = array(
			'status' => false,
			'msg' => '设置VIP等级失败!'
		);
		if(IS_AJAX && IS_POST){
			$user_id = I('post.id',0);
			$vip_level = I('post.vip_level',0);

			if(!in_array($vip_level,array(1,2,3,4,5))){
				$result['msg'] = '您选择的vip等级错误!!';
				$this->ajaxReturn($result);
				exit();
			}

			$user_info = D('UserInfo')->where('user_id  = %d',array($user_id))->find();

			if(empty($user_info)){
				$result['msg'] = '找不到该玩家信息!';
				$this->ajaxReturn($result);
				exit();
			}

			if($user_info['vip_level'] == $vip_level){
				$result['msg'] = '玩家VIP等级无变化!';
				$this->ajaxReturn($result);
				exit();
			}

            $return  = D('UserInfo')->set_vip_level($user_info,$user_id,$vip_level);

			if($return === false){
			}else{

				$result = array(
					'status' => true,
					'msg' => '设置VIP等级成功!'
				);

				$content = get_log_content(SysLogModel::SET_VIP_LEVEL,array('vip_level'=>$vip_level));
				$log_result = D('SysLog')->add_log(SysLogModel::ADMIN_DO_LOG,$content,SysLogModel::SET_VIP_LEVEL,$user_info['operator_id'],$user_info['user_id'],'');

			}
		}
		$this->ajaxReturn($result);
	}
	// 玩家登入记录
	public function login_log(){
		$param = I('get.');

		$result = D('Operator')->get_user_login_stat($this->operator_options,$param['start_date'],$param['start_date']);

		if(I('submitbtn') == '导出excel'){
			$file_name = '玩家登入记录导出';
			$excel_title = array(
				array('width' => 20,'val' => '运营商'),
				array('width' => 20,'val' => '累计登录用户数'),
				array('width' => 20,'val' => '次日'),
				array('width' => 20,'val' => '3日'),
				array('width' => 20,'val' => '7日'),
				array('width' => 20,'val' => '15日'),
				array('width' => 20,'val' => '30日'),
				array('width' => 20,'val' => '3个月'),
			);
			$excel_data = array();
			$begin_row = 1;

			if(!empty($result)) export_excel($excel_title,$result,$file_name,$begin_row);
		}

		$this->assign('param',$param);
		$this->assign('list',$result);
		$this->display();
	}
}
