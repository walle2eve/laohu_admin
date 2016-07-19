<?php
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
	function get_game_icon($themd_id,$icon_id){
		if($icon_id == 0){
			return '/Icon_' . $icon_id . '.png'; 
		}else{
			return '/' . $themd_id . '/' . $themd_id . '_' . $icon_id . '.png'; 
		}
		
	}
	// 获取中奖线 图标文件
	function get_win_line_icon($line){
			return '/win_line_icon/' . $line . '.png'; 		
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
