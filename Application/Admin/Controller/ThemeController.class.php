<?php
namespace Admin\Controller;

use Admin\Model\SysDictModel;

class ThemeController extends BaseController
{

	protected $versionType = 'occifial';
	protected $operator_id;
	protected $resultUrl;

	public function _initialize(){
		parent::_initialize();

		$this->operator_id = I('operator_id',0);

		if(I('version_type') == 'beta'){
			$this->versionType = 'beta';
			$this->operator_id = '-1';
		}elseif(I('version_type') == 'reveal'){
			$this->versionType = 'reveal';
			$this->operator_id = '-2';
		}

        $this->resultUrl = U('Admin/Theme/manage',array('version_type'=>$this->versionType));

		$this->assign('version_type',$this->versionType);
	}

    // 主题管理
    public function manage(){

        $param = I();
        $param['operator_id'] = $this->operator_id;

        $result = D('ThemeOperator')->get_list($this->login_user['user_role'],$param);

        $this->assign('page',$result['page']);
        $this->assign('list',$result['list']);
        $this->assign('param',$param);

        $this->display();
    }

    // 编辑游戏主题配置信息
    public function edit_conf(){

        $page_error = '';
        $id = I('id');
        $id = intval($id);

        $result = D('ThemeOperator')->find_one($id);

        if(!$result){
            $page_error = "参数错误,找不到指定的主题信息";
        }

        $theme_conf_field = D('ThemeOperator')->theme_conf_field;

        if(IS_AJAX && IS_POST){

            $return = array(
                'status' => true,
                'msg' => '编辑配置成功',
                'url' => U('Admin/Theme/manage',array('version_type'=>$this->versionType)),
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
            //$data['name'] = trim($args['themeName']);
            $data['info'] = serialize($args);
            $data['input_time'] = date('Y-m-d H:i:s');
            $re = D('ThemeOperator')->where('id = %d',array($id))->save($data);

            if($re){
                $this->ajaxReturn($return);
            }else{
                $return['status'] = false;
                $return['msg'] = '编辑配置信息失败，请重试！';
                $this->ajaxReturn($return);
            }

            exit();
        }

        $result['theme_info'] = unserialize($result['info']);

        $this->assign('page_error',$page_error);
        $this->assign('result',$result);
        $this->assign('theme_conf_field',$theme_conf_field);
        $this->display();
    }

	// 主题统计
	public function stat(){

		$param = I('get.');

		if(!isset($param['stat_year']) || !$param['stat_year'])
            $stat_year = date("Y");
		else $stat_year = $param['stat_year'];

		$operator_id = $param['operator_id'];

		$result = D('SpinStat')->get_theme_stat($this->operator_options,$stat_year,$operator_id);

		if(!$result)$this->error('运营商信息错误！');

		$theme_stat = array();
		$theme_info = array();



		foreach($result['stat_list'] as $key=>$row){
			$row['stat_month'] = intval($row['stat_month']);
			$theme_stat[$row['operator_id']][$row['theme_id']][$row['stat_month']] = $row;
			$theme_info[$row['theme_id']] = array('theme_id'=> $row['theme_id'], "theme_name" => $row['theme_name']);
		}

		$this->assign('operator_info',$result['operator_info']);
		$this->assign('theme_info',$theme_info);
		$this->assign('theme_stat',$theme_stat);

		$this->assign('param',$param);

		$this->display();
	}

	//设置主题状态
	public function set_status(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST) die('error page');

		$id = I('post.key',0);

		$id = intval($id);

		$field = I('post.field','');

		$field = in_array($field,array('status','is_activity')) ? $field : 'status';

		$theme = D('ThemeOperator')->find_one($id);

		if(empty($theme)){
			$result['status'] = false;
			$result['msg'] = '参数错误，无法修改状态，请刷新后重试';
			$this->ajaxReturn($result);
			exit();
		}

		$data[$field] = $theme[$field] == 1 ? -1 : 1;

		$data['input_time'] = date('Y-m-d H:i:s');

		$return = D('ThemeOperator')->where('id = %d',array($id))->save($data);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '操作失败！';
			$this->ajaxReturn($result);
			exit();
		}
		A('Public')->clear_cache();
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

		$theme = D('ThemeOperator')->find_one($id);

		if(empty($theme)){
			$result['status'] = false;
			$result['msg'] = '参数错误，请刷新后重试';
			$this->ajaxReturn($result);
			exit();
		}

		$data['sort'] = I('post.sort');
		$data['input_time'] = date('Y-m-d H:i:s');
		$return = D('ThemeOperator')->where('id = %d',array($id))->save($data);

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
			'url' => U('Admin/Theme/manage',array('version_type'=>$this->versionType)),
		);
		
		if(IS_AJAX && IS_POST){
			$args = I('post.');
			M()->startTrans();
			
			$operator_id = $args['operator_id'];

			$theme_id = $args['id'];
			$theme_name = $args['name'];

			$base_theme = D('Theme')->find($theme_id);

			if($base_theme){
			    if($base_theme['name'] != $theme_name){
			        $theme_edit = array('name' => $theme_name, 'input_time' => date('Y-m-d'));
			        $res = D('Theme')->where('id = %d',array($theme_id))->save($theme_edit);
                }
            }else{
			    $theme_add = array('id' => $theme_id,'name' => $theme_name, 'input_time' => date('Y-m-d'));
			    $res = D('Theme')->add($theme_add);
            }

			if($res === false){
                M()->rollback();

				$result['status'] = false;
				$result['msg'] = '操作游戏主题失败';
				$this->ajaxReturn($result);
				exit();
			}
			// 运营商的主题
            $operator_theme = D('ThemeOperator')->get_theme($operator_id,$theme_id);

            if(!empty($operator_theme)){

                M()->rollback();

                $result['status'] = false;
                $result['msg'] = '当前平台已添加当前主题。如需修改，请在列表处编辑配置！';
                $this->ajaxReturn($result);
                exit();
            }

            $operator_theme_add = array(
                'theme_id' => $theme_id,
                'operator_id' => $operator_id,
                'input_time' => date('Y-m-d')
            );

            $res = D('ThemeOperator')->add($operator_theme_add);

            if(!$res){
                M()->rollback();

                $result['status'] = false;
                $result['msg'] = '平台主题添加失败，请稍后重试！';
                $this->ajaxReturn($result);
                exit();
            }

            M()->commit();
			$this->ajaxReturn($result);
			exit();
		}
		die('error page');
	}
    // 生成json推送至oss或七牛
	public function make_json(){

        $file_name = 'client_theme_test.json';


        $json_data = $this->get_theme_json_data();
        parent::make_json($file_name,$json_data);
    }
    // 输出到页面
	public function show_json(){

	    $json_data = $this->get_theme_json_data();
	    parent::show_json($json_data);
   }

	private function get_theme_json_data(){

		$list = D('ThemeOperator')->where('status = 1 AND operator_id = %d',array($this->operator_id))->order('sort ASC')->select();

		$theme_conf_field = D('ThemeOperator')->theme_conf_field;

		$json_data = array();

		foreach($list as $row){
			$theme_info = unserialize($row['info']);
			foreach($theme_conf_field as $key=>$val){
				$theme_conf_field_arr[$key] = $val['field_type'] == 'string' ? '' : array();
				if(!isset($theme_info[$key]))$theme_info[$key] = $val['field_type'] == 'string' ? '' : array();
			}
			if(!empty($theme_info['IP'])){
				$theme_info['IP'] = DesEncrypt($theme_info['IP']);
			}
			if(empty($theme_info))$theme_info = $theme_conf_field_arr;
			$json_data[$row['theme_id']] = $theme_info;
		}

		return $json_data;
	}
}