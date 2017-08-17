<?php
namespace Admin\Controller;

use Think\Controller;

class BaseController extends Controller
{
	protected $uid;
	protected $login_user;
	protected $operator_options;
    // 运营商分库
	protected $operatorDB;
    //
    protected $versionType = 'occifial';
    protected $operator_id;
    protected $resultUrl;


	protected $not_login_action = array(
			'login',
			'verifyCode',
			'dologin',
			'theme_json',
			'version_json',
			'download',
			'download_test',
		);

	public function _initialize(){

		$this->uid 			=  	session('uid');
		$this->login_user 	= 	session('login_user');

		// 判断是否已登录
		if((!$this->uid || !$this->login_user) && !in_array(ACTION_NAME,$this->not_login_action)){
			$this->display('Public/login');
			die();
		}

        $this->assign('login_user',$this->login_user);

        // <!--{ 目前只支持到3级菜单 }-->
        $this->assign('menu_list',$this->_menu_list());
        // 运营商信息
        $sys_role_operators = $this->_sys_role_operators();
        $this->operator_options = $sys_role_operators['roles'][$this->login_user['user_role']]['operators'];
        $this->assign('operator_options',$this->operator_options);

        // 登陆用户允许访问的action
        $this_url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        $_sys_role_functions = $this->_sys_role_functions();

        foreach($_sys_role_functions['functions'] as $item){
            $all_functions[] = $item['module'] . '/' . $item['controller'] . '/' . $item['action'];
        }

        $role_allow_functions = $_sys_role_functions['roles'][$this->login_user['user_role']]['functions'];
        if(in_array($this_url,$all_functions) && !in_array($this_url,$role_allow_functions)){
            if(IS_AJAX){
                $result['status'] = false;
                $result['msg'] = '您没有权限操作当前页面';
                $this->ajaxReturn($result);
                exit();
            }else{
                $this->error('您没有权限操作当前页面');
            }
        }


        // 验证是否有操作当前平台的权限
        // begin
        $this->operator_id = I('operator_id',0);

        $operator_ids = array_keys($this->operator_options);

        if(!in_array($this->operator_id,$operator_ids) && isset($_REQUEST['operator_id']) && $_REQUEST['operator_id'] > 0){
            if(IS_AJAX){
                $result['status'] = false;
                $result['msg'] = '您没有权限操作该平台';
                $this->ajaxReturn($result);
                exit();
            }else{
                $this->error('您没有权限操作该平台');
            }
        }

        $this->assign('operator_id',$this->operator_id);
        // end

        // operator 分库分表
        $this->operatorDB = '';
        $operator_info = $this->operator_options[$this->operator_id];
        if($operator_info){
            if($operator_info['is_diy'] == 1){
                $this->operatorDB = $operator_info['diy_db_name'];
            }else{
                $this->operatorDB = 'public_laohu';
            }
        }

	}
	/**
	 * @comment 获取登录用户菜单
	 * @return array
	 */
	private function _menu_list(){
		$param_name = 'sys_menu_' . $this->login_user['user_role'];
		$menu_list = S($param_name);
		if(!$menu_list){
			$menu_list = get_user_func($this->login_user['user_role']);
			S($param_name,$menu_list);
		}
		return $menu_list;
	}
    // 用户组可以操作的平台
    protected function _sys_role_operators(){

        $sys_role_operators = S('operators');

        if(!$sys_role_operators){
            $data = D('SysRoleOperator')->get_all();

            foreach($data as $row){
                $sys_role_operators['operators'][$row['operator_id']] = $row;
                $sys_role_operators['roles'][$row['role_id']]['operators'][$row['operator_id']] = $row;
                $sys_role_operators['roles'][$row['role_id']]['role_name'] = $row['role_name'];
            }
            S('operators',$sys_role_operators);
        }

        return $sys_role_operators;
    }
    // 用户组可以操作的action
    protected function _sys_role_functions(){
        $sys_role_functions = S('functions');

        if(!$sys_role_functions){
            $data = D('SysRoleFunc')->get_all();

            foreach($data as $row){
                $sys_role_functions['functions'][$row['func_id']] = $row;
                $sys_role_functions['roles'][$row['role_id']]['functions'][$row['func_id']] = $row['module'] . '/' . $row['controller'] . '/' . $row['action'];
                $sys_role_functions['roles'][$row['role_id']]['role_name'] = $row['role_name'];
            }

            S('functions',$sys_role_functions);
        }

        return $sys_role_functions;
    }

    // 生成json
    protected function make_json($file_name,$json_data){

        $return = array(
            'status' => true,
            'msg' => '更新json成功',
            'url' => $this->resultUrl,
        );

        $json_data = json_encode($json_data);

        if(in_array($this->versionType,array('beta','reveal'))){
            $storage_conf = C($this->versionType);
        }else{
            $operator_info = $this->operator_options[$this->operator_id];
            $storage_conf['storage_type'] = $operator_info['storage_type'];
            $storage_conf['storage_bucket'] = $operator_info['storage_bucket'];
            $storage_conf['storage_access_id'] = $operator_info['storage_access_id'];
            $storage_conf['storage_access_key'] = $operator_info['storage_access_key'];
            $storage_conf['storage_endpoint'] = $operator_info['storage_endpoint'];
        }

        // 配置信息为空，则提示错误
        if(!$storage_conf['storage_type']
            || !$storage_conf['storage_bucket']
            || !$storage_conf['storage_access_id']
            || !$storage_conf['storage_access_key']){

            $return['status'] = false;
            $return['msg'] = '请先完善平台json存储配置信息！';
            $this->ajaxReturn($return);
        }

        $re = storagePutContent($storage_conf,$json_data,$file_name);

        if($re){
            $this->ajaxReturn($return);
        }else{
            $return['status'] = false;
            $return['msg'] = '更新json失败，请重试！';
            $this->ajaxReturn($return);
        }
    }

    // 输出到网页
    protected function show_json($json_data){
        header('Content-type:text/json');
        return $this->ajaxReturn($json_data);
    }
}
