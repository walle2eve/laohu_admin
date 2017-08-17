<?php
namespace Admin\Controller;

use Admin\Model\SysLogModel;

use Admin\Model\ActivityModel;

class ActivityController extends BaseController
{
	private $filename 	= '';
	
	private $fileext  	= '';

	private $uperror	= '';

	protected $activityModel;

	public function _initialize(){
	    parent::_initialize();
	    $this->activityModel = new ActivityModel();
    }

  	public function index(){
		$param = I('get.');

		$list = $this->activityModel->alist($this->login_user['user_role'],$param);
		$this->assign('list',$list['list']);
		$this->assign('page',$list['page']);

		$this->assign('param',$param);

		$this->display();
    }
    // 上传并解析csv文件

    /**
     *
     */
    public function uploadcsv(){
    	$result = array(
    			'status' 	=> true,
    			'msg'		=> '导入用户成功',
    			'data'		=> null,	
    		);

    	if(!IS_AJAX || !IS_POST){
    		$result['status'] = false;
    		$result['msg']	= '页面来源错误！';
    		$this->ajaxReturn($result);
    		exit();
    	}

    	$operator = I('get.operator_id');

    	if(!$operator){
    		$result['status'] = false;
    		$result['msg']	= '请选择平台！';
    		$this->ajaxReturn($result);
    		exit();
    	}

    	$_file = $_FILES['uploadfile'];

    	if(!$_file){
    		$result['status'] = false;
    		$result['msg']	= '文件不能为空';
    		$this->ajaxReturn($result);
    		exit();
    	}

    	$upresult = $this->_uploadcsv($_file);
    	if(!$upresult){
    		$result['status'] = false;
    		$result['msg']	= $this->uperror;
    		$this->ajaxReturn($result);
    		exit();
    	}
    	//echo $this->filename;exit();
		if (!file_exists($this->filename)) {
    		$result['status'] = false;
    		$result['msg']	= '文件上传错误，找不到路径！';
    		$this->ajaxReturn($result);
    		exit();
		}
		//----------------------------------------------------------------------------------

		Vendor('PHPExcel');
		//根据不同类型分别操作
		if( $this->fileext =='xlsx'|| $this->fileext =='xls' ){
			$objPHPExcel = \PHPExcel_IOFactory::load($this->filename);
		}else if( $this->fileext == 'csv' ){
			$objReader = \PHPExcel_IOFactory::createReader('CSV')
			  ->setDelimiter(',')
			  ->setInputEncoding('GBK') //不设置将导致中文列内容返回boolean(false)或乱码
			  ->setEnclosure('"')
			  ->setLineEnding("\r\n")
			  ->setSheetIndex(0);
			$objPHPExcel = $objReader->load($this->filename);
		}

		$cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

		if(!$objPHPExcel){
    		$result['status'] = false;
    		$result['msg']	= '上传的文件有错误，无法读取信息！';
    		$this->ajaxReturn($result);
    		exit();
		}

		//选择标签页
		$sheet = $objPHPExcel->getSheet(0);
		//获取行数与列数,注意列数需要转换
		$highestRowNum = $sheet->getHighestRow();
		//$highestColumn = $sheet->getHighestColumn();
		//$highestColumnNum = \PHPExcel_Cell::columnIndexFromString($highestColumn);

		if($highestRowNum > 1000){
    		$result['status'] = false;
    		$result['msg']	= '导入的用户数量请不要大于1000！';
    		$this->ajaxReturn($result);
    		exit();
		}

		//开始取出数据并存入数组，只取第一列
		$checkedArr = array();
		$ignoreArr = array();

		for($i=1;$i<=$highestRowNum;$i++){ 

			$cellName = \PHPExcel_Cell::stringFromColumnIndex(0).$i;
			$cellVal = $sheet->getCell($cellName)->getValue();
			if(trim($cellVal) == '') break; // 如果有空行跳出循环，默认为已结束！

			$userid = D('UserInfo')->get_user_id_by_operator($cellVal,$operator);

			if(!$userid){
				$ignoreArr[] = $cellVal;
				continue;
			}

			$checkedArr['userids'][] = $userid;
			$checkedArr['accounts'][] = strtoupper($cellVal);
		}

		if(!$checkedArr){
			$result['status'] 	= false;
			$result['msg']		= '上传的用户数据验证错误,请核对后上传！';
    		$this->ajaxReturn($result);
    		exit();
		} 
		// 返回数据
		$result['status'] 	= true;
		$result['msg']		= sprintf('共上传%s条记录，其中验证通过<font color="blue">%s</font>条，验证失败<font color="red">%s</font>条！',$highestRowNum, count($checkedArr['accounts']), count($ignoreArr));
		$result['data'] = array('checkedIds' => implode(',',$checkedArr['userids']), 'checkedAccounts' => implode(',',$checkedArr['accounts']), 'ignoreArr' => $ignoreArr);
    	$this->ajaxReturn($result);
    }

    public function save(){

    	$result = array(
			'status' => true,
			'msg' => '创建活动成功',
			'url' => U('Admin/Activity/index')
		);

    	$actiData = array();

    	$postdata = I('post.');  		

    	if(!isset($postdata['operator_id']) || $postdata['operator_id'] == false){
    		$result['status'] 	= false;
    		$result['msg']		= '请选择活动';
    		$this->ajaxReturn($result);
			exit();
    	}
    	// ---------------------------------------------
    	$id = $postdata['activityid'];


    	$actiData['operator_id'] = $postdata['operator_id'];

    	$actiData['activity_type'] = !isset($postdata['activity_type']) || !($postdata['activity_type']) ? 1 : $postdata['activity_type'];

    	$actiData['user_type'] = $postdata['user_type'];

    	if(!isset($postdata['activity_date_picker']) || $postdata['activity_date_picker'] == false){
    		$result['status'] 	= false;
    		$result['msg']		= '请选择活动起止时间';
    		$this->ajaxReturn($result);
			exit();
    	}

    	list($actiData['begin_time'],$actiData['end_time']) = explode(' - ',$postdata['activity_date_picker']);

    	$actiData['begin_time'] = strtotime($actiData['begin_time']);
    	$actiData['end_time']	= strtotime($actiData['end_time']);

    	if(!$actiData['begin_time'] || !$actiData['end_time']){
    		$result['status'] 	= false;
    		$result['msg']		= '活动起止时间格式有误';
    		$this->ajaxReturn($result);
			exit();
    	}

    	if($actiData['begin_time'] >= $actiData['end_time']){
    		$result['status'] 	= false;
    		$result['msg']		= '活动结束时间必须大于开始时间';
    		$this->ajaxReturn($result);
			exit();
    	}

    	$actiData['remark']	= $postdata['remark'];

    	// 默认活动状态未开始
    	//$actiData['status'] = 0;
    	$actiData['status'] = intval($postdata['status']);
    	//$actiData['activity_switch'] = intval($postdata['status']);

    	// age status
    	$actiData['age_status'] = intval($postdata['age_status']);

    	if($actiData['age_status']){
    		$actiData['min_age'] = intval($postdata['min_age']);
    		$actiData['max_age'] = intval($postdata['max_age']);

    		if($actiData['min_age'] > $actiData['max_age']){
	    		$result['status'] 	= false;
	    		$result['msg']		= '年龄区间错误';
	    		$this->ajaxReturn($result);
				exit();
    		}
    	}

    	// vip status
    	$actiData['vip_status'] = intval($postdata['vip_status']);

    	if($actiData['vip_status']){
    		$actiData['min_vip_level'] = intval($postdata['min_vip_level']);
    		$actiData['max_vip_level'] = intval($postdata['max_vip_level']);

    		if($actiData['min_vip_level'] > $actiData['max_vip_level']){
	    		$result['status'] 	= false;
	    		$result['msg']		= 'vip等级区间错误';
	    		$this->ajaxReturn($result);
				exit();
    		}
    	}

    	// deposit status
    	$actiData['deposit_status'] = intval($postdata['deposit_status']);

    	if($actiData['deposit_status']){
    		$actiData['min_deposit_coins'] = floatval($postdata['min_deposit_coins']);
    		$actiData['max_deposit_coins'] = floatval($postdata['max_deposit_coins']);

    		if($actiData['min_deposit_coins'] > $actiData['max_deposit_coins']){
	    		$result['status'] 	= false;
	    		$result['msg']		= '转入金币区间错误';
	    		$this->ajaxReturn($result);
				exit();
    		}
    		// deposit time status 
    		$actiData['deposit_time_status'] = intval($postdata['deposit_time_status']);

	    	if($postdata['deposit_date_picker']  && $actiData['deposit_time_status'] == 1){
		    	if(!isset($postdata['deposit_date_picker']) || $postdata['deposit_date_picker'] == false){
		    		$result['status'] 	= false;
		    		$result['msg']		= '请选择累计转入起止时间';
		    		$this->ajaxReturn($result);
					exit();
		    	}

		    	list($actiData['min_deposit_time'],$actiData['max_deposit_time']) = explode(' - ',$postdata['deposit_date_picker']);

		    	$actiData['min_deposit_time'] = strtotime($actiData['min_deposit_time']);
		    	$actiData['max_deposit_time']	= strtotime($actiData['max_deposit_time']);

		    	if(!$actiData['min_deposit_time'] || !$actiData['max_deposit_time']){
		    		$result['status'] 	= false;
		    		$result['msg']		= '累计转入起止时间格式有误';
		    		$this->ajaxReturn($result);
					exit();
		    	}

		    	if($actiData['min_deposit_time'] >= $actiData['max_deposit_time']){
		    		$result['status'] 	= false;
		    		$result['msg']		= '累计转入结束时间必须大于开始时间';
		    		$this->ajaxReturn($result);
					exit();
		    	}
	    	}
    	}

    	// bet status
    	$actiData['bet_status'] = intval($postdata['bet_status']);

    	if($actiData['bet_status']){

    		$actiData['min_bet_coins'] = floatval($postdata['min_bet_coins']);
    		$actiData['max_bet_coins'] = floatval($postdata['max_bet_coins']);

    		if($actiData['min_bet_coins'] > $actiData['max_bet_coins']){
	    		$result['status'] 	= false;
	    		$result['msg']		= '投注金币区间错误';
	    		$this->ajaxReturn($result);
				exit();
    		}
    		// bet time status 
    		$actiData['bet_time_status'] = intval($postdata['bet_time_status']);

	    	if($postdata['bet_date_picker'] && $actiData['bet_time_status'] == 1){
		    	if(!isset($postdata['bet_date_picker']) || $postdata['bet_date_picker'] == false){
		    		$result['status'] 	= false;
		    		$result['msg']		= '请选择累计投注起止时间';
		    		$this->ajaxReturn($result);
					exit();
		    	}

		    	list($actiData['min_bet_time'],$actiData['max_bet_time']) = explode(' - ',$postdata['bet_date_picker']);

		    	$actiData['min_bet_time'] = strtotime($actiData['min_bet_time']);
		    	$actiData['max_bet_time']	= strtotime($actiData['max_bet_time']);

		    	if(!$actiData['min_bet_time'] || !$actiData['max_bet_time']){
		    		$result['status'] 	= false;
		    		$result['msg']		= '累计投注起止时间格式有误';
		    		$this->ajaxReturn($result);
					exit();
		    	}

		    	if($actiData['min_bet_time'] >= $actiData['max_bet_time']){
		    		$result['status'] 	= false;
		    		$result['msg']		= '累计投注结束时间必须大于开始时间';
		    		$this->ajaxReturn($result);
					exit();
		    	}
	    	}
    	}

    	$actiData['create_time'] = time();
    	// theme param
    	$gameinfo = array();

    	$gameinfo['theme_id'] = $postdata['gameid'];

    	$gameinfo['theme_name'] = $postdata['gamename'];

    	$gameinfo['lines'] = intval($postdata['game_lines']);

    	$gameinfo['rounds'] = intval($postdata['game_rounds']);

    	$gameinfo['mul'] = floatval($postdata['game_mul']);

    	$gameinfo['bet'] = floatval($postdata['game_bet']);

    	$gameinfo['total_bet'] = floatval($postdata['totalbet']);

    	//-------------------------------------------------------

    	$actiData['theme_id'] = $postdata['gameid'];

    	$actiData['theme_param'] = json_encode($gameinfo);

    	//$actiData['free_info'] = "恭喜你,获得%s免费旋转次数%d次,TOTAL BET为%s，请立即前往领取";

    	$actiData['free_info'] = "恭喜你,获得{0}免费旋转次数{1}次,TOTAL BET为{2}，请立即前往领取";

    	//$actiData['free_info'] = sprintf($actiData['free_info'],$gameinfo['theme_name'],$gameinfo['rounds'],$gameinfo['total_bet']);

    	$actiData['dispose_user'] = $this->uid;

    	// accounts
    	if($actiData['user_type'] > 2){
    		$actiData['accounts'] = $postdata['accounts'];
    	}


    	// add 
		M()->startTrans();

		if($id) {

			$activity = $this->activityModel->find($id);
			if(empty($activity)){
				$result['status'] = false;
				$result['msg'] = '参数错误，无该信息';
				$this->ajaxReturn($result);
				exit();
			}
			if($activity['end_time'] < time()){
				$result['status'] = false;
				$result['msg'] = '活动已过期，不能编辑';
				$this->ajaxReturn($result);
				exit();
			}

			$activity_id = $this->activityModel->where('id = %d',array($id))->save($actiData);
			D('ActivityUser')->where('activity_id = %d',array($id))->delete();
			$result['msg'] = '修改活动成功';
		}
		else {
		    $activity_id = $this->activityModel->add($actiData);
        }

		if(!$activity_id){
			M()->rollback();
    		$result['status'] 	= false;
    		$result['msg']		= '记录存储失败，请稍后重试！';
    		$this->ajaxReturn($result);
			exit();
		}

        if($this->operatorDB != ''){
            $this->activityModel = new ActivityModel($this->operatorDB);

            if($id) {
                $this->activityModel->delete($id);
                $actiData['id'] = $id;
            }else{
                $actiData['id'] = $activity_id;
            }

            $this->activityModel->add($actiData);
        }

		// activity user
		if($actiData['user_type'] > 2 && !empty($postdata['userids'])){
			$actiUser = array();

			foreach(explode(',',$postdata['userids']) as $val){
				$row = array();
				$row['activity_id'] = $activity_id;
				$row['user_id'] = $val;
				$row['status'] = 1;
				$actiUser[] = $row;
			}

			$actiUserRes = D('ActivityUser')->addAll($actiUser);

			if(!$actiUserRes){
				M()->rollback();
	    		$result['status'] 	= false;
	    		$result['msg']		= '用户信息添加失败，请稍后重试！';
	    		$this->ajaxReturn($result);
				exit();
			}	
		}

		M()->commit();

    	$this->ajaxReturn($result);
    }
	//删除通知
	public function del(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST) $this->error('页面错误');

		$id = I('post.activityid',0);
		$id = intval($id);

		$activity = $this->activityModel->find($id);
		if(empty($activity)){
			$result['status'] = false;
			$result['msg'] = '参数错误，无该信息';
			$this->ajaxReturn($result);
			exit();
		}
		if($activity['begin_time'] > time() && $activity['end_time'] < time()){
			$result['status'] = false;
			$result['msg'] = '活动正在进行中，不能删除';
			$this->ajaxReturn($result);
			exit();
		}
		D('ActivityUser')->where('activity_id = %d',array($id))->delete();
		$return = $this->activityModel->delete($id);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '删除失败';
			$this->ajaxReturn($result);
			exit();
		}

		if($this->operatorDB != ''){
		    $this->activityModel = new ActivityModel($this->operatorDB);
            $return = $this->activityModel->delete($id);
        }

		$this->ajaxReturn($result);
	}
	//设置活动状态
	public function set_status(){
		$result = array(
			'status' => true,
		);

		if(!IS_AJAX || !IS_POST) $this->error('页面错误');

		$id = I('post.key',0);
		$id = intval($id);

		$activity = $this->activityModel->find($id);
		if(empty($activity)){
			$result['status'] = false;
			$result['msg'] = '参数错误，无该信息';
			$this->ajaxReturn($result);
			exit();
		}
		$status = $activity['status'] == 1 ? 0 : 1;
		$return = $this->activityModel->where('id = %d',array($id))->setField('status',$status);

		if($return === false){
			$result['status'] = false;
			$result['msg'] = '修改状态失败';
			$this->ajaxReturn($result);
			exit();
		}

        if($this->operatorDB != ''){
            $activity = $this->activityModel->find($id);

            $this->activityModel = new ActivityModel($this->operatorDB);
            $this->activityModel->delete($id);
            $this->activityModel->add($activity);
        }

		$this->ajaxReturn($result);
	}
	// 根据选择的平台获取对应的主题列表
    public function get_theme_list_by_operator(){
        $result = array(
            'status' => true,
            'msg' => '',
            'data' => 0,
            'url' => U('Admin/Activity/index'),
        );

        if(!IS_AJAX || !IS_POST){
            $result['status'] = false;
            $result['msg']	= '页面来源错误！';
            $this->ajaxReturn($result);
            exit();
        }

        $operator_id = I('post.operator_id',0);

        if(!$operator_id){
            $result['status'] = false;
            $result['msg']	= '请选择平台！';
            $this->ajaxReturn($result);
            exit();
        }

        $game_list = D('ThemeOperator')->get_list_by_operator($operator_id,1);

        $result['data'] = $game_list;
        $this->ajaxReturn($result);
    }
    // 异步获取主题信息
    public function get_theme_param(){
		$result = array(
			'status' => true,
			'msg' => '',
			'data' => 0,
			'url' => U('Admin/Activity/index'),
		);

    	if(!IS_AJAX || !IS_POST){
    		$result['status'] = false;
    		$result['msg']	= '页面来源错误！';
    		$this->ajaxReturn($result);
    		exit();
    	}

		$theme_id = I('post.id',0);

		$theme_info = D('ThemeOperator')->get_theme($this->operator_id,$theme_id);

		if(empty($theme_info)){
			$result = array(
				'status' => false,
				'msg' => '参数错误,没有找到游戏主题信息',
			);
    		$this->ajaxReturn($result);
    		exit();
		}

		$theme_info = unserialize($theme_info['info']);

		$returnArr = array();

		$returnArr['themeid'] = $theme_info['themeid'];

		$returnArr['themeName'] = $theme_info['themeName'];

		$returnArr['linesBet'] = $theme_info['linesBet'];

		$returnArr['totalBetFormula'] = $theme_info['totalBet'];

		//-------------------------------------------------------------------------
		$returnArr['mul'] = explode(',',$theme_info['mul']);

		$returnArr['bet'] = explode(',',$theme_info['bet']);

		if($theme_info['lineNumChange'] == "true"){

			$returnArr['lineNum'] = range($theme_info['lineNum'],1,1);

		}else{

			$returnArr['lineNum'] = array($theme_info['lineNum']);

		}

		//$returnArr['lineNum'] = json_encode($returnArr['lineNum']);
		//-------------------------------------------------------------------------
		$result['data'] = $returnArr;

		$this->ajaxReturn($result);
    }
    // 异步获取活动信息
    public function get_info(){
		$result = array(
			'status' => true,
			'msg' => '',
			'data' => 0,
			'url' => U('Admin/Activity/index'),
		);

    	if(!IS_AJAX || !IS_POST){
    		$result['status'] = false;
    		$result['msg']	= '页面来源错误！';
    		$this->ajaxReturn($result);
    		exit();
    	}

		$id = I('post.id',0);

		$activity_info = $this->_get_info($id);

		if(empty($activity_info)){
			$result = array(
				'status' => false,
				'msg' => '参数错误,没有找到活动信息',
			);
    		$this->ajaxReturn($result);
    		exit();
		}

		if($activity_info['begin_time'] < time() && $activity_info['end_time'] > time() && $activity_info['status'] == 1){
			$result = array(
				'status' => false,
				'msg' => '活动正在进行中，不允许修改！',
			);
    		$this->ajaxReturn($result);
    		exit();
		}

		if($activity_info['status'] == 2){
			$result = array(
				'status' => false,
				'msg' => '活动已关闭，不允许修改！',
			);
    		$this->ajaxReturn($result);
    		exit();
		}

		$result['data'] = $activity_info;

		$this->ajaxReturn($result);
    }
    //
    public function detail(){
		
		$id = I('get.id');

		$activity_info = $this->_get_info($id);

		$this->assign('info',$activity_info);

		$this->display();
    }
    //
    private function _get_info($id){
        $data = $this->activityModel->get_one($id);
    	return $data;
    }
    // 获取玩家信息
    public function get_user_by_name(){
		$result = array(
			'status' => true,
			'msg' => '',
			'data' => 0,
		);

    	if(!IS_AJAX || !IS_POST){
    		$result['status'] = false;
    		$result['msg']	= '页面来源错误！';
    		$this->ajaxReturn($result);
    		exit();
    	}
    	
		$operator_id = I('post.operator_id',0);
		$account_id = I('post.account','');

    	if(!$operator_id){
    		$result['status'] = false;
    		$result['msg']	= '请选择平台！';
    		$this->ajaxReturn($result);
    		exit();
    	}

		$user_id = D('UserInfo')->get_user_id_by_operator($account_id,$operator_id);

		if(empty($user_id)){
			$result = array(
				'status' => false,
				'msg' => '用户名错误，没找到该用户',
			);
			$this->ajaxReturn($result);
			exit();
		}
		$result['data'] = array('checkedIds' => $user_id, 'checkedAccounts' => strtoupper($account_id));

		$result['msg'] = '用户' . $account_id . '查找成功！';

		$this->ajaxReturn($result);
    }

    // 处理上传文件
    private function _uploadcsv($file){
	    $upload = new \Think\Upload();
	    $upload->maxSize   =     3145728;
	    $upload->exts      =     array('csv','xls','xlsx');
	    $upload->rootPath  =     C('UPLOAD_ROOT_PATH');
		$upload->autoSub = true;
		$upload->subName = array('date','Ym');
	    // 上传单个文件 
	    $info   =   $upload->uploadOne($file);
	    if(!$info) {
	        $this->uperror = $upload->getError();
	        return false;
	    }else{
	        $this->filename = $upload->rootPath . $info['savepath'].$info['savename'];
	        $this->fileext = $info['ext'];
	        return true;
	    }
    }
}