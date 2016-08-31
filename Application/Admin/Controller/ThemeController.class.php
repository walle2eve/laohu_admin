<?php
namespace Admin\Controller;

use Think\Controller;

use Admin\Model\SysDictModel;

class ThemeController extends BaseController
{
	public function _initialize(){
		parent::_initialize();
	}
	public function stat(){

		$stat_year = date("Y");

		$result = D('SpinStat')->get_theme_stat($stat_year);
		if(!$result)$this->error('运营商信息错误！');

		$theme_stat = array();
		$theme_info = array();

		foreach($result['stat_list'] as $key=>$row){
			$row['stat_month'] = intval($row['stat_month']);
			$theme_stat[$row['stat_month']][$row['theme_id']][$row['operator_id']] = $row;
			$theme_info[$row['theme_id']] = array('theme_id'=> $row['theme_id'], "theme_name" => $row['theme_name']);
		}

		$this->assign('operator_info',$result['operator_info']);
		$this->assign('theme_info',$theme_info);
		$this->assign('theme_stat',$theme_stat);
		$this->display();
	}
	public function edit(){

		$param = I('get.');

		$result = D('ThemeInfo')->get_list($param);

		$this->assign('page',$result['page']);
		$this->assign('list',$result['list']);
		$this->assign('param',$param);
		$this->display();
	}
	//设置主题状态
	public function set_status(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST)die('error page');

		$id = I('post.key',0);
		$id = intval($id);

		$theme = D('ThemeInfo')->find($id);
		if(empty($theme)){
			$result['status'] = false;
			$result['msg'] = '参数错误，无法修改状态，请刷新后重试';
			$this->ajaxReturn($result);
			exit();
		}
		$data['status'] = $theme['status'] == 1 ? -1 : 1;
		$data['input_time'] = date('Y-m-d H:i:s');
		$return = D('ThemeInfo')->where('id = %d',array($id))->save($data);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '修改状态失败';
			$this->ajaxReturn($result);
			exit();
		}
		//A('Public')->clear_cache();
		$this->ajaxReturn($result);
	}
	// 添加游戏主题信息
	public function add_theme(){
		$result = array(
			'status' => true,
			'msg' => '创建游戏主题成功',
			'url' => U('Admin/Theme/edit'),
		);
		if(IS_AJAX && IS_POST){
			$args = I('post.');
			D('ThemeInfo')->startTrans();
			if(!D('ThemeInfo')->create()){
				$result['status'] = false;
				$result['msg'] = D('ThemeInfo')->getError();
				$this->ajaxReturn($result);
				exit();
			}
			$args['status'] = 1;
			$args['input_time'] = date('Y-m-d H:i:s');
			$id = D('ThemeInfo')->add($args);
			if(!$id){
				D('ThemeInfo')->rollback();

				$result['status'] = false;
				$result['msg'] = '添加游戏主题失败';
				$this->ajaxReturn($result);
				exit();
			}

		//	A('Public')->clear_cache();
			D('ThemeInfo')->commit();
			$this->ajaxReturn($result);
			exit();
		}
		die('error page');
	}
	// 编辑游戏主题配置信息
	public function edit_conf(){
		$page_error = '';
		$theme_id = I('themeid');
		$theme_id = intval($theme_id);
		$ThemeInfoModel = D('ThemeInfo');

		$result = $ThemeInfoModel->find($theme_id);
		if(!$result){
			$page_error = "参数错误,找不到指定的主题信息";
		}

		$theme_conf_field = $ThemeInfoModel->theme_conf_field;

		if(IS_AJAX && IS_POST){
			$return = array(
				'status' => true,
				'msg' => '编辑配置成功',
				'url' => U('Admin/Theme/edit'),
			);
			if($page_error <> ''){
				$return['status'] = false;
				$return['msg'] = $page_error;
				$this->ajaxReturn($return);
				exit();
			}
			// 处理参数
			$args = I('post.');
			unset($args['version_id']);
			$data['name'] = trim($args['themeName']);

			$data['theme_info'] = serialize($args);
			$data['input_time'] = date('Y-m-d H:i:s');
			$re = $ThemeInfoModel->where('id = %d',array($theme_id))->save($data);

			if($re){
				$this->ajaxReturn($return);
			}else{
				$return['status'] = false;
				$return['msg'] = '编辑配置信息失败，请重试！';
				$this->ajaxReturn($return);
			}
			
			exit();
		}

		$result['theme_info'] = unserialize($result['theme_info']);

		$this->assign('page_error',$page_error);
		$this->assign('result',$result);
		$this->assign('theme_conf_field',$theme_conf_field);
		$this->display();
	}
	// 导出app所需json格式
	public function export_json(){
		$ThemeInfoModel = D('ThemeInfo');
		$list = $ThemeInfoModel->where('status = 1')->select();

		if(empty($list))$this->error('没有可以导出的主题配置信息');

		$theme_conf_field = $ThemeInfoModel->theme_conf_field;

		$json_data = array();

		foreach($list as $row){
			$theme_info = unserialize($row['theme_info']);
			foreach($theme_conf_field as $key=>$val){
				$theme_conf_field_arr[$key] = $val['field_type'] == 'string' ? '' : array();
				if(!isset($theme_info[$key]))$theme_info[$key] = $val['field_type'] == 'string' ? '' : array();
			}
			if(!empty($theme_info['IP'])){
				$theme_info['IP'] = DesEncrypt($theme_info['IP']);
			}
			if(empty($theme_info))$theme_info = $theme_conf_field_arr;
			$json_data[$row['id']] = $theme_info;
		}
		header('Content-type:text/json');
		return $this->ajaxReturn($json_data);
	}
}