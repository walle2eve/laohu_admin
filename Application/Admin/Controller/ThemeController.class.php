<?php
namespace Admin\Controller;

use Think\Controller;

use Admin\Model\SysDictModel;

class ThemeController extends BaseController
{
	public $versionType = 'occifial';

	public $themeModel = null;

	public function _initialize(){
		parent::_initialize();
		$this->themeModel = D('ThemeInfo');

		if(I('version_type') == 'beta'){
			$this->versionType = 'beta';
			$this->themeModel = D('ThemeInfoBeta');
		}elseif(I('version_type') == 'reveal'){
			$this->versionType = 'reveal';
			$this->themeModel = D('ThemeInfoReveal');
		}elseif(I('version_type') == 'cf365'){
			$this->versionType = 'cf365';
			$this->themeModel = D('ThemeInfoCf365');
		}
		$this->assign('version_type',$this->versionType);
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

		$result = $this->themeModel->get_list($param);

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

		$theme = $this->themeModel->find($id);
		if(empty($theme)){
			$result['status'] = false;
			$result['msg'] = '参数错误，无法修改状态，请刷新后重试';
			$this->ajaxReturn($result);
			exit();
		}
		$data['status'] = $theme['status'] == 1 ? -1 : 1;
		$data['input_time'] = date('Y-m-d H:i:s');
		$return = $this->themeModel->where('id = %d',array($id))->save($data);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '操作成功！';
			$this->ajaxReturn($result);
			exit();
		}
		//A('Public')->clear_cache();
		$this->ajaxReturn($result);
	}
	//设置排序
	public function set_sort(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST)die('error page');

		$id = I('post.key',0);
		$id = intval($id);

		$theme = $this->themeModel->find($id);
		if(empty($theme)){
			$result['status'] = false;
			$result['msg'] = '参数错误，请刷新后重试';
			$this->ajaxReturn($result);
			exit();
		}

		$data['sort'] = I('post.sort');
		$data['input_time'] = date('Y-m-d H:i:s');
		$return = $this->themeModel->where('id = %d',array($id))->save($data);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '操作失败，请重试';
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
			'url' => U('Admin/Theme/edit',array('version_type'=>$this->versionType)),
		);
		
		if(IS_AJAX && IS_POST){
			$args = I('post.');
			$this->themeModel->startTrans();
			
			if(!$this->themeModel->create()){
				$result['status'] = false;
				$result['msg'] = $this->themeModel->getError();
				$this->ajaxReturn($result);
				exit();
			}
			$args['status'] = 1;
			$args['input_time'] = date('Y-m-d H:i:s');
			$id = $this->themeModel->add($args);
			if(!$id){
				$this->themeModel->rollback();

				$result['status'] = false;
				$result['msg'] = '添加游戏主题失败';
				$this->ajaxReturn($result);
				exit();
			}

		//	A('Public')->clear_cache();
			$this->themeModel->commit();
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
		//$ThemeInfoModel = D('ThemeInfo');

		$result = $this->themeModel->find($theme_id);
		if(!$result){
			$page_error = "参数错误,找不到指定的主题信息";
		}

		$theme_conf_field = $this->themeModel->theme_conf_field;

		if(IS_AJAX && IS_POST){
			$return = array(
				'status' => true,
				'msg' => '编辑配置成功',
				'url' => U('Admin/Theme/edit',array('version_type'=>$this->versionType)),
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
			$re = $this->themeModel->where('id = %d',array($theme_id))->save($data);

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

	// 清除theme_conf_json缓存
	public function make_theme_conf_data(){
		$return = array(
			'status' => true,
			'msg' => '更新json成功',
			'url' => U('Admin/Theme/edit',array('version_type'=>$this->versionType)),
		);
		$json_data = $this->get_theme_json_data();

		S('theme_conf_data_' . $this->$versionType,$json_data);

		// 上传至oss
		$file_name = 'client_theme.json';
		$json_data = json_encode($json_data);

		//$re = StoragePutContent($file_name,$json_data);

		if($this->versionType == 'beta'){
			$re = QiNiuPutContent($file_name,$json_data);
		}elseif($this->versionType == 'reveal' || $this->versionType == 'cf365'){
			$re = OssPutContent($file_name,$json_data,$this->versionType);
		}else{
			$re = OssPutContent($file_name,$json_data);
		}

		if($re){
			$this->ajaxReturn($return);
		}else{
			$return['status'] = false;
			$return['msg'] = '配置信息上传失败，请重试！';
			$this->ajaxReturn($return);
		}
	}

	// 导出app所需json格式
	public function theme_json(){

		$ac = I('ac');

		$json_data = array();

		if($ac == 'test'){
			$json_data = $this->get_theme_json_data();
		}else{
			$json_data = S('theme_conf_data_' . $this->$versionType);
			if(!$json_data){
				$json_data = $this->get_theme_json_data();
				S('theme_conf_data_' . $this->$versionType,$json_data);
			}
		}

		header('Content-type:text/json');
		return $this->ajaxReturn($json_data);
	}

	private function get_theme_json_data(){

		$list = $this->themeModel->where('status = 1')->order('sort ASC')->select();
		//if(empty($list))$this->error('没有可以导出的主题配置信息');
		$theme_conf_field = $this->themeModel->theme_conf_field;

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

		return $json_data;
	}
}