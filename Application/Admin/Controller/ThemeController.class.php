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
		$result = D('ThemePlayedStat')->stat();
		if(!$result)$this->error('运营商信息错误！');


		$this->assign('operator_info',$result['operator_info']);
		$this->assign('list',$result['stat_list']);
		$this->display();
	}
}