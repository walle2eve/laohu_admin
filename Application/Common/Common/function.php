<?php
	function StoragePutContent($object,$content){
		$type = C('STORAGE_TYPE');
		$func = $type . 'PutContent';
		if(!function_exists($func)){
			$func = 'OssPutContent';
		}
		return $func($object,$content);
	}
	/**
	 * oss
	 * $object oss端文件名称
	 * $content 要上传的字符串
	 */
	function OssPutContent($object,$content,$version="occifial"){
		$oss_conf = C($version);
		$OSS_ACCESS_ID = $oss_conf['OSS_ACCESS_ID'];
		$OSS_ACCESS_KEY = $oss_conf['OSS_ACCESS_KEY'];
		$OSS_ENDPOINT = $oss_conf['OSS_ENDPOINT'];
		$OSS_BUCKET	=	$oss_conf['OSS_BUCKET'];
		//print_r($bucket);exit();
	    Vendor('OSS.autoload');

	    $ossClient = new \OSS\OssClient(
	        $OSS_ACCESS_ID, $OSS_ACCESS_KEY, $OSS_ENDPOINT);
	    try{
	        $result = $ossClient->putObject($OSS_BUCKET, $object, $content);
	        
	    } catch(OssException $e) {
	        //printf(__FUNCTION__ . ": FAILED\n");
	        //printf($e->getMessage() . "\n");
	        return false;
	    }

	   	return true;
	}
	/***
	 * qiniu 云存储
	 * 
	 */
	function QiNiuPutContent($targetFilePath,$content,$bucket=""){
	  Vendor('QiNiu.autoload');

	  $accessKey = C('QINIU_ACCESS_KEY');
	  $secretKey = C('QINIU_SECRET_KEY');
	  $auth = new \Qiniu\Auth($accessKey, $secretKey);

	  $bucket = C('QINIU_BUCKET');
	  /**
	  $policy = array(
	      'callbackUrl' => 'http://your.domain.com/callback.php',
	      'callbackBody' => 'filename=$(fname)&filesize=$(fsize)'
	  );
	  **/
	  //$uptoken = $auth->uploadToken($bucket, null, 3600, $policy);
	  $uptoken = $auth->uploadToken($bucket, $targetFilePath, 3600, array('insertOnly' => 1));

	  $bukMgr = new \Qiniu\Storage\BucketManager($auth);

	  $ret = $bukMgr->delete($bucket, $targetFilePath);

	  $uploadMgr = new \Qiniu\Storage\UploadManager();

	  list($ret, $err) = $uploadMgr->put($uptoken, $targetFilePath, $content, null, 'application/json');
	  //echo "\n====> putFile result: \n";
	  //var_dump($ret);exit();

	  if ($err !== null) {
	  //	var_dump($err);
	      return false;
	  } else {
	  //    var_dump($ret);
	  	 return true;
	  }

	}
	/*
	    TripleDES加密
	*/
	function DesEncrypt($data)
	{    
	    //Pad for PKCS7
	    $blockSize = mcrypt_get_block_size('tripledes', 'ecb');
	    $len = strlen($data);
	    $pad = $blockSize - ($len % $blockSize);
	    $data .= str_repeat(chr($pad), $pad);

	    $key = C('DES_KEY');
	    $key = md5($key,TRUE);
	    $key .= substr($key,0,8); //comment this if you use 168 bits long key

	    //Encrypt data
	    $encData = mcrypt_encrypt('tripledes', $key, $data, 'ecb'); 
	    return base64_encode($encData);
	}

	 /*
	    TripleDES解密
	 */
	 function DesDecrypt($data)
	 {
	    $key = C('DES_KEY');
	    $key = md5($key, TRUE);
	    $key .= substr($key, 0, 8);

	    //Decrypt data
	    $fromBase64Str = base64_decode($data);
	    $decData = mcrypt_decrypt('tripledes', $key, $fromBase64Str, 'ecb');
	    return $decData;
	 }
	/**
	 * @function bootstrap 风格分页
	 * @param $count		总记录数
	 * @param $up_id 		上级菜单ID
	 * @param $level		菜单层级 1,2,3
	 * @return array
	 */
	function page($count,$row=0,$rollPage=5){
		if(!$row)$row = C('LIST_ROWS');
		$Page = new \Think\Page($count,$row,array(),$rollPage);
		$Page->setConfig('theme',"<ul class='pagination'><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></li><li>%FIRST%</li><li>%UP_PAGE%</li>%LINK_PAGE%<li>%DOWN_PAGE%</li><li>%END%</li></ul>");
		return $Page;// 分页显示输出
	}
	// 随机数
	function get_rand_char($length=6){
		$str = null;
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol)-1;

		for($i=0;$i<$length;$i++){
			$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}

		return $str;
	}
	// login_pwd 加密方式
	function get_pwd($pwd,$salt){
		return md5(md5($pwd) . $salt);
	}

	/**
	*  订单号
	*/
	function get_sn(){
		$year_code = array('A','B','C','D','E','F','G','H','I','J');
		$order_sn = $year_code[intval(date('Y'))-2016].
		strtoupper(dechex(date('m'))).date('d').
		substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
		return $order_sn;
	}
	// 获取错误信息
	function get_err_msg($err_code){
		$param = 'API_ERR_MSG_' . $err_code;
		return L($param);
	}
	// 获取日志内容
	function get_log_content($log_code,$replace_arr = array()){
		$param = 'LOG_CONTENT_' . $log_code;
		if(!empty($replace_arr))
			return L($param,$replace_arr);
		else
			return L($param);
	}

	// menu level
	function get_en_nums($num){
		$en_nums = array(
			1=>'first','second','third','fourth','fifth','sixth','seventh','ninth','tenth',
		);
		return $en_nums[$num];
	}

	/**
	 * @function get_user_func
	 * @param $user_role	登录用户角色
	 * @param $up_id 		上级菜单ID
	 * @param $level		菜单层级 1,2,3
	 * @return array
	 */
    function get_user_func($user_role,$up_id=0,$level=0){

		$level = $level + 1;

		$menu_list = D('SysFunc')->get_user_func($user_role,$up_id);
		foreach($menu_list as $key=>$row){
			$menu_list[$key]['level'] = get_en_nums($level);
			if($data = get_user_func($user_role,$row['func_id'],$level)){
				$menu_list[$key]['son']['level'] = get_en_nums($level);
				$menu_list[$key]['son'] = $data;
			}
		}
		return $menu_list;
	}
	// 获取游戏图标文件
	function get_game_icon($theme_id,$icon_id){
		if($icon_id == 0 && $theme_id != 1012){
			return '/Icon_' . $icon_id . '.png';
		}else{
			//return '/' . $theme_id . '/' . $theme_id . '_' . $icon_id . '.png';
			return '/' . $theme_id . '/Icon_' . $icon_id . '.png';
		}

	}
	// 获取中奖线 图标文件
	function get_win_line_icon($win_line,$line=20){
			$icon_name = str_pad($line,2,'0',STR_PAD_LEFT) . '-' . ($win_line == 0 ? ($line == 9 ? 'beilv' : 'free') : str_pad($win_line,2,'0',STR_PAD_LEFT));
			return '/win_line_icon/' . $line . '/' . $icon_name . '.png';
	}
	// 去除数组中的某一个值
	function array_remove_value(&$arr, $var){
		foreach ($arr as $key => $value) {
			if (is_array($value)) {
				array_remove_value($arr[$key], $var);
			} else {
				$value = trim($value);
				if ($value == $var) {
					unset($arr[$key]);
				} else {
					$arr[$key] = $value;
				}
			}
		}
	}
	// 删除数组元素的空格
	function trim_array($Input){
		if (!is_array($Input))
			return trim($Input);
		return array_map('trim_array', $Input);
	}

	/**
	 * 导出excel
	 */
	function export_excel($title = array(),$content = array(),$file_name = '导出Excel',$begin_row = 1,$objPHPExcel = false, $return = false){

		if(!$objPHPExcel){
			Vendor('PHPExcel');
			$objPHPExcel = new \PHPExcel();


			//缓存
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
		}

		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle($file_name);


		// 输出表头
		if(is_array($title) && !empty($title)){
			foreach($title as $key=>$row){
				$column = \PHPExcel_Cell::stringFromColumnIndex($key);
				$objActSheet->getColumnDimension($column)->setWidth($row['width']);
				$objActSheet->setCellValueExplicit( $column . $begin_row, $row['val'],  PHPExcel_Cell_DataType::TYPE_STRING);
			}
			$begin_row = $begin_row + 1;
		}


		// 输出内容
		if(is_array($content) && !empty($content)){
			foreach($content as $key=>$row){
				$key2 = 0;
				foreach($row as $k2=>$val){
					if($k2 === 'status'){
						$val = $val == 1 ? '完成' : ($val == -1 ? '失败' : '进行中');
					}
					$column = \PHPExcel_Cell::stringFromColumnIndex($key2);
					$objActSheet->setCellValueExplicit( $column . $begin_row, $val,  PHPExcel_Cell_DataType::TYPE_STRING);
					$key2 ++;
				}
				$begin_row ++;
			}
		}

		/*****************************************************************************/
		if($return  ===  false){

			$ua = $_SERVER["HTTP_USER_AGENT"];

			$encoded_filename = urlencode($file_name);
			$encoded_filename = str_replace("+", "%20", $encoded_filename);

			header('Content-Type: application/octet-stream');
			header('Content-Type: application/vnd.ms-excel');
			header('Cache-Control: max-age=0');

			if(preg_match("/MSIE/", $ua) || preg_match("/Trident\/7.0/", $ua)){
				header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xlsx"');
			} else if (preg_match("/Firefox/", $ua)) {
				header('Content-Disposition: attachment; filename*="utf8\'\'' . $file_name . '.xlsx"');
			} else {
				header('Content-Disposition: attachment; filename="' . $file_name . '.xlsx"');
			}

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit;
		}
		return $objPHPExcel;
	}
