<?php
/**
 * Created by PhpStorm.
 * User: panda
 * Date: 2017/8/7
 * Time: 11:35
 */

namespace Admin\Controller;


class SysController extends BaseController
{
    public function _initialize(){
        parent::_initialize();
    }

    // 权限组
    public function role(){
        $param = I('get.');
        $list = D('SysRole')->get_list($param);
        $this->assign('list',$list['list']);
        $this->assign('page',$list['page']);

        $this->assign('param',$param);
        $this->display();
    }
    // 删除权限组信息
    public function del_role(){

        $return = array(
            'status' => true,
            'msg' => '操作成功',
            'url' => U('Admin/Sys/role'),
        );

        $id = I('id');
        $role = D('SysRole')->find($id);

        if(empty($role)){
            $return['status'] = false;
            $return['msg'] = '参数错误！';
            $this->ajaxReturn($return);
            exit();
        }

        D('SysRole')->startTrans();

        D('SysRoleFunc')->where('role_id = %d',array($id))->delete();
        D('SysRoleOperator')->where('role_id = %d',array($id))->delete();
        // 删掉相应的权限组后，原组别下会员状态不可用，需重新设置
        $re = D('SysUser')->where('user_role = %d',array($id))->setField('status',-1);

        if($re === false){

            D('SysRole')->rollback();
            $return['status'] = false;
            $return['msg'] = '操作失败，请稍后重试！';
            $this->ajaxReturn($return);
            exit();

        }

        D('SysRole')->delete($id);

        D('SysRole')->commit();

        A('Public')->clear_cache();

        $this->ajaxReturn($return);

    }
    // 编辑权限组信息
    public function edit_role(){

        $role = array();
        $id = I('id');

        $page_error = '';

        if($id){

            $role = D('SysRole')->find($id);

            if(empty($role)){
                $page_error = '权限参数错误';
            }

            $role_funcs = D('SysRoleFunc')->where('role_id = %d',array($id))->getField('func_id',true);
            $this->assign('role_funcs',$role_funcs);

            $role_operators = D('SysRoleOperator')->where('role_id = %d',array($id))->getField('operator_id',true);
            $this->assign('role_operators',$role_operators);

        }

        if(IS_AJAX && IS_POST){
            $return = array(
                'status' => true,
                'msg' => '操作成功',
                'url' => U('Admin/Sys/role'),
            );

            if($page_error <> ''){
                $return['status'] = false;
                $return['msg'] = $page_error;
                $this->ajaxReturn($return);
                exit();
            }

            // 处理参数
            $args = I('post.');

            $funcs = $args['extends']['funcs'];
            $operators = $args['extends']['operators'];
            unset($args['extends']);
            unset($args['id']);

            $args['input_time'] = date('Y-m-d H:i:s');

            D('SysRole')->startTrans();



            if($id)  D('SysRole')->where('id = %d',array($id))->save($args);
            else $id = D('SysRole')->add($args);


            if($id === false){

                $return['status'] = false;
                $return['msg'] = '操作失败，请重试！';
                $this->ajaxReturn($return);

            }else{

                foreach($funcs as $val){
                    $args_func[] = array(
                        'role_id' => $id,
                        'func_id' => $val
                    );
                }

                $return_func = D('SysRoleFunc')->add_all($id,$args_func);

                foreach($operators as $val){
                    $args_operator[] = array(
                        'role_id' => $id,
                        'operator_id' => $val
                    );
                }

                $return_operaotr = D('SysRoleOperator')->add_all($id,$args_operator);

                if(!$return_func || !$return_operaotr){

                    D('SysRole')->rollback();

                    $return['status'] = false;
                    $return['msg'] = $page_error;
                    $this->ajaxReturn($return);
                    exit();
                }

                D('SysRole')->commit();

                A('Public')->clear_cache();

                $this->ajaxReturn($return);
            }

            exit();
        }

        $this->assign('result',$role);

        $func_list = D('SysFunc')->get_all();
        $this->assign('func_list',$func_list);

        $operator_list = D('Operator')->get_all();
        $this->assign('operator_list',$operator_list);

        $this->assign('page_error',$page_error);

        $this->display();
    }

    // 后台管理员
    public function user(){
        $param = I('get.');
        $list = D('SysUser')->get_list($param);
        $this->assign('list',$list['list']);
        $this->assign('page',$list['page']);

        $this->display();
    }
    // 编辑管理员信息
    public function edit_user(){
        $page_error = '';

        $id = I('id');
        if($id){
            $user = D('SysUser')->find($id);
            if(empty($user)){
                $page_error = '用户参数错误';
            }
        }

        if(IS_AJAX && IS_POST){
            $return = array(
                'status' => true,
                'msg' => '编辑成功',
                'url' => U('Admin/Sys/user'),
            );

            if($page_error <> ''){
                $return['status'] = false;
                $return['msg'] = $page_error;
                $this->ajaxReturn($return);
                exit();
            }

            if(!$id)$this->_add_user();

            // 处理参数
            $args = I('post.');

            if($id)
                $re = D('SysUser')->where('uid = %d',array($id))->save($args);
            else
                $re = D('SysUser')->add($args);

            if($re === false){
                $return['status'] = false;
                $return['msg'] = '编辑失败，请重试！';
                $this->ajaxReturn($return);
            }else{
                $this->ajaxReturn($return);
            }

            exit();
        }
        $this->assign('result',$user);

        // 权限组
        $admin_roles = D('SysRole')->get_all();
        $this->assign('admin_roles',$admin_roles);
        $this->assign('page_error',$page_error);
        $this->display();
    }
    // 添加管理员
    private function _add_user(){
        $result = array(
            'status' => true,
            'msg' => '添加管理员成功',
            'url' => U('Admin/Sys/user'),
        );
        if(IS_AJAX && IS_POST){
            $args = I('post.');
            if(isset($args['uid'])) unset($args['uid']);
            D('SysUser')->startTrans();
            if(!D('SysUser')->create()){
                $result['status'] = false;
                $result['msg'] = D('SysUser')->getError();
                $this->ajaxReturn($result);
                exit();
            }
            // 添加用户
            $args['salt'] = get_rand_char();
            $args['login_pwd'] = get_pwd($args['login_pwd'],$args['salt']);
            $args['input_time'] = time();

            $uid = D('SysUser')->add($args);

            if(!$uid){
                D('SysUser')->rollback();

                $result['status'] = false;
                $result['msg'] = '添加管理员失败';
                $this->ajaxReturn($result);
                exit();
            }

            A('Public')->clear_cache();
            D('SysUser')->commit();
            $this->ajaxReturn($result);
            exit();
        }
    }

    //重置用户密码
    public function reset_user_pwd(){
        $result = array(
            'status' => true,
            'msg' => '重置密码成功'
        );
        $uid = I('post.id');
        $uid = intval($uid);
        $repwd = I('post.pwd');

        if(!IS_AJAX || !IS_POST)die('error page');
        $user = D('SysUser')->find($uid);

        if(!$user){
            $result['status'] = false;
            $result['msg'] = '用户参数错误，找不到该用户，请刷新后重试';
            $this->ajaxReturn($result);
            exit();
        }
        $pwd = get_pwd($repwd,$user['salt']);
        $return = D('SysUser')->where('uid = %d',array($uid))->setField('login_pwd',$pwd);

        if($return === false){
            $result['status'] = false;
            $result['msg'] = '重置用户密码失败，请重试';
            $this->ajaxReturn($result);
            exit();
        }
        $this->ajaxReturn($result);
    }

    //设置状态
    public function set_status(){
        $result = array(
            'status' => true,
        );

        if(!IS_AJAX || !IS_POST)die('error page');

        $type = I('post.type','');
        $key = I('post.key',0);
        $field = I('post.field','status');

        switch($type){
            case 'role':
                $model = D('SysRole');
                $pri_field = 'id';
                break;
            case 'user':
                $model = D('SysUser');
                $pri_field = 'uid';
                break;
            default:
                $result['status'] = false;
                $result['msg'] = '类型错误！';
                $this->ajaxReturn($result);
                exit();
        }

        $id = intval($key);

        if($id == 10001 && $type == 'user'){
            // 系统管理员，不允许修改\
            $result['status'] = false;
            $result['msg'] = '不允许修改系统内置用户的状态';
            $this->ajaxReturn($result);
            exit();
        }
        if($id == 1 && $type == 'role'){
            // 超级管理员，不允许修改\
            $result['status'] = false;
            $result['msg'] = '不允许修改系统内置权限组的状态';
            $this->ajaxReturn($result);
            exit();
        }
        $data = $model->find($id);


        if(empty($data)){
            $result['status'] = false;
            $result['msg'] = '参数错误，无法修改状态，请刷新后重试';
            $this->ajaxReturn($result);
            exit();
        }

        $status = $data['status'] == 1 ? -1 : 1;

        $return = $model->where($pri_field . ' = %d',array($id))->setField($field,$status);

        if($return === false){
            $result['status'] = false;
            $result['msg'] = '修改状态失败';
            $this->ajaxReturn($result);
            exit();
        }
        A('Public')->clear_cache();
        $this->ajaxReturn($result);
    }
    // 删除管理员
    public function del_user(){

        $return = array(
            'status' => true,
            'msg' => '操作成功',
            'url' => U('Admin/Sys/user'),
        );

        $id = I('id');
        $user = D('SysUser')->find($id);

        if(empty($user)){
            $return['status'] = false;
            $return['msg'] = '参数错误！';
            $this->ajaxReturn($return);
            exit();
        }

        $re = D('SysUser')->delete($id);

        if($re === false){
            $return['status'] = false;
            $return['msg'] = '操作失败，请稍后重试！';
            $this->ajaxReturn($return);
            exit();

        }

        $this->ajaxReturn($return);

    }
}