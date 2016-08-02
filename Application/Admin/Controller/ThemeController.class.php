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
}