<?php
namespace Admin\Controller;

// use Think\Controller;

class ClientController extends BaseController{

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

        $this->resultUrl = U('Admin/Client/version',array('version_type'=>$this->versionType));

        $this->assign('version_type',$this->versionType);
    }
	// app版本管理
	public function version(){
        $param['operator_id'] = $this->operator_id;
		$result = D('ClientOperator')->get_list($this->login_user['user_role'],$param);;

		$this->assign('page',$result['page']);
		$this->assign('list',$result['list']);

		$this->assign('param',$param);
		$this->display();
	}
	// 添加版本信息
	public function add_version(){

		$result = array(
			'status' => true,
			'msg' => '创建版本成功',
			'url' => U('Admin/Client/version',array('version_type'=>$this->versionType)),
		);

		if(IS_AJAX && IS_POST){
			$args = I('post.');
			M()->startTrans();
			if(!D('ClientOperator')->create()){
				$result['status'] = false;
				$result['msg'] = D('ClientOperator')->getError();
				$this->ajaxReturn($result);
				exit();
			}

			$args['input_time'] = date('Y-m-d H:i:s');
			$id = D('ClientOperator')->add($args);
			if(!$id){
				M()->rollback();

				$result['status'] = false;
				$result['msg'] = '添加版本失败';
				$this->ajaxReturn($result);
				exit();
			}

			M()->commit();
			$this->ajaxReturn($result);
			exit();
		}
		die('error page');
	}
	// 编辑版本配置信息
	public function edit_version_conf(){

		$page_error = '';
		$version_id = I('version_id');
		$version_id = intval($version_id);

		$ClientVersionModel = D('ClientOperator');

		$result = $ClientVersionModel->find($version_id);
		if(!$result){
			$page_error = "参数错误,找不到指定的版本信息";
		}

		$client_conf_field = $ClientVersionModel->client_conf_field;

		if(IS_AJAX && IS_POST){
			$return = array(
				'status' => true,
				'msg' => '编辑配置成功',
				'url' => U('Admin/Client/version',array('version_type'=>$this->versionType)),
			);
			
			if($page_error <> ''){
				$return['status'] = false;
				$return['msg'] = $page_error;
				$this->ajaxReturn($return);
				exit();
			}

			// 处理参数
			$args = I('post.');
			
			$data['version_no'] = trim($args['ClientVersion']);
			unset($args['version_id']);
			$data['conf'] = serialize($args);
			$data['input_time'] = date('Y-m-d H:i:s');
			$re = $ClientVersionModel->where('id = %d',array($version_id))->save($data);

			if($re){
				$this->ajaxReturn($return);
			}else{
				$return['status'] = false;
				$return['msg'] = '编辑配置信息失败，请重试！';
				$this->ajaxReturn($return);
			}
			
			exit();
		}

		$result['conf'] = unserialize($result['conf']);

		$this->assign('page_error',$page_error);
		$this->assign('result',$result);
		$this->assign('client_conf_field',$client_conf_field);
		$this->display();
	}
    // 生成json推送至oss或七牛
    public function make_json(){

        $file_name = 'client_version_test.json';

        $json_data = $this->get_client_json_data();
        parent::make_json($file_name,$json_data);
    }
    // 输出到页面
    public function show_json(){

        $json_data = $this->get_client_json_data();
        parent::show_json($json_data);
    }


	private function get_client_json_data(){

        $param['operator_id'] = $this->operator_id;
		$result = D('ClientOperator')->get_last_version($this->login_user['user_role'],$param);

		$client_conf_field = D('ClientOperator')->client_conf_field;
		
		$version_conf = unserialize($result['conf']);

		foreach($client_conf_field as $key=>$val){
			$version_conf_field_arr[$key] = $val['field_type'] == 'string' ? '' : array();
			if(!isset($version_conf[$key]))$version_conf[$key] = $val['field_type'] == 'string' ? '' : array();
		}

		if(!empty($version_conf['ip'])){
			$version_conf['ip'] = DesEncrypt($version_conf['ip']);
		}
		if(!empty($version_conf['TestPlayRoomIp'])){
			$version_conf['TestPlayRoomIp'] = DesEncrypt($version_conf['TestPlayRoomIp']);
		}
		if(!empty($version_conf['TestPlayGameIp'])){
			$version_conf['TestPlayGameIp'] = DesEncrypt($version_conf['TestPlayGameIp']);
		}
		if(empty($version_conf))$version_conf = $version_conf_field_arr;
			
		return $version_conf;
	}
}