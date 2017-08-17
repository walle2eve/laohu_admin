<?php
namespace Admin\Controller;

use Admin\Model\SysLogModel;

class NoticeController extends BaseController
{
    protected $versionType = 'occifial';
    protected $operator_id;
    protected $resultUrl;

    public function _initialize(){
        parent::_initialize();

        $this->operator_id = I('operator_id',0);

        if(I('version_type') == 'beta' || $this->operator_id == '-1'){
            $this->versionType = 'beta';
            $this->operator_id = '-1';
        }elseif(I('version_type') == 'reveal' || $this->operator_id == '-2'){
            $this->versionType = 'reveal';
            $this->operator_id = '-2';
        }


        $this->assign('version_type',$this->versionType);
    }

	public function index(){

		$param = I();
		$param['operator_id'] = $this->operator_id;

		$list = D('OperatorNotice')->nlist($this->login_user['user_role'],$param);

		$this->assign('list',$list['list']);
		$this->assign('page',$list['page']);

		$this->assign('param',$param);

		$this->display();

	}
	// 信息保存
	public function save(){

		$result = array(
				'status' 	=> true,
				'msg'		=> '操作成功',
				'url'		=> U('Admin/Notice/index'),
			);

		if(!IS_AJAX || !IS_POST)
			$this->error('页面错误');

		$notice = D('OperatorNotice');

		$data = I('post.');

		$notice->startTrans();

		if (!$notice->create()){

			$notice->rollback();
		    $result['status'] = false;
		    $result['msg'] = $notice->getError();
		    $this->ajaxReturn($result);
		    exit();

		}

		$notice->create_time = time();

		$notice->dispose_user = $this->uid;


		if($notice->id){
			$res = $notice->save();
		}else{
			$res = $notice->add();
		}

		$notice->commit();

		$this->ajaxReturn($result);
	}

	//设置通知状态
	public function set_status(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST) $this->error('页面错误');

		$id = I('post.key',0);
		$id = intval($id);

		$notice = D('OperatorNotice')->find($id);

		if(empty($notice)){
			$result['status'] = false;
			$result['msg'] = '参数错误，无该信息';
			$this->ajaxReturn($result);
			exit();
		}
		$status = $notice['status'] == 1 ? -1 : 1;
		$return = D('OperatorNotice')->where('id = %d',array($id))->setField('status',$status);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '修改状态失败';
			$this->ajaxReturn($result);
			exit();
		}
		$this->ajaxReturn($result);
	}
	//删除通知
	public function del(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST) $this->error('页面错误');

		$id = I('post.id',0);
		$id = intval($id);

		$notice = D('OperatorNotice')->find($id);
		if(empty($notice)){
			$result['status'] = false;
			$result['msg'] = '参数错误，无该信息';
			$this->ajaxReturn($result);
			exit();
		}

		$return = D('OperatorNotice')->delete($id);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '删除失败';
			$this->ajaxReturn($result);
			exit();
		}

		$this->ajaxReturn($result);
	}

    // 生成json推送至oss或七牛
    public function make_json(){

        $file_name = 'notice_test.json';


        $json_data = $this->get_notice_json_data();
        parent::make_json($file_name,$json_data);
    }
    // 输出到页面
    public function show_json(){

        $json_data = $this->get_notice_json_data();
        parent::show_json($json_data);
    }

	private function get_notice_json_data(){
		$result = D('OperatorNotice')->field('id,title,content,writer,create_time')->where('operator_id = %d AND status = 1',array($this->operator_id))->order('create_time DESC')->select();
		return $result;
	}
}
