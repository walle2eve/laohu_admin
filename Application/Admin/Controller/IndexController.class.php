<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends BaseController
{
	public function _initialize(){
		parent::_initialize();
	}
    public function index(){
		//运营商信息
		$operator_info = D('SysUser')->get_user_bet_stat();
		
		$this->assign('operator_info',$operator_info);
		
        $this->display();
    }
	// 运营商充值统计
	public function stat(){
		
		$year = I('year','');
		
		$stat_type = I('type','month');
		
		$stat_type = in_array($stat_type,array('month','year')) ? $stat_type : "month";

		switch($stat_type){
			case 'month':
				$year = date('Y');
				$month = date('month');
			break;
			case 'year':
				$year = $year == '' ? date('Y') : $year;
				$month = '';
			break;
			default:
			break;
		}

		$result = D('OperatorOrderInfo')->deposit_stat($stat_type,$year,$month);
		
		if(I('submitbtn') == '导出excel'){

			$file_name = '充值统计' . ($stat_type == 'month' ? '-按月统计' : '-按年统计');
			
			$excel_title 	= array();
			$excel_data		= array();
			$begin_row = 1;
			$objPHPExcel = false;
			
			if($stat_type == 'month'){
				
				$title_first = '日期(DATE)';
			
			}elseif($stat_type == 'year'){
				
				$excel_title[] = array('width'=> 20, 'val' => '年份'); 
				foreach($result['title'] as $row){
					$excel_title[] =  array('width'=> 20, 'val' => $row['user_name'] . '(' . $row['discount'] . '% off)');
				}
				$excel_title[] =  array('width'=> 20, 'val' => '总计');
				
				// 年统计
				$key = 0;
				
				$excel_data[$key][] = $year;
				$total_deposit = 0;
				
				foreach($result['year_stat'] as $val){
					$total_deposit += $val;
					$excel_data[$key][] = number_format(floatval($val),2);
				}
				$excel_data[$key][] = number_format($total_deposit,2);
				
				//-----------------------------------------------------
				$title_first = '日期(DATE)';
				
				$objPHPExcel  = export_excel($excel_title,$excel_data,$file_name,$begin_row,$objPHPExcel,true);
				
				$begin_row = 4;
			}
			///// export_excel 
			$excel_title 	= array();
			$excel_data		= array();
			/*表头 begin*/
			$excel_title[] = array('width'=> 20, 'val' => $title_first); 
			foreach($result['title'] as $row){
				$excel_title[] =  array('width'=> 20, 'val' => $row['user_name'] . '(' . $row['discount'] . '% off)');
			}
			$excel_title[] =  array('width'=> 20, 'val' => '总计');
			/*表头 end*/
			
			$key = 0;
			foreach($result['list'] as $row){
				$excel_data[$key][] = $row['date'];
				$total_deposit = 0;
				foreach($row['stat'] as $val){
					$total_deposit += $val;
					$excel_data[$key][] = number_format(floatval($val),2);
				}
				$excel_data[$key][] = number_format($total_deposit,2);
					
				$key ++;
			}
			
			export_excel($excel_title,$excel_data,$file_name,$begin_row,$objPHPExcel,false);
		}
		
		$this->assign('title',$result['title']);
		$this->assign('list',$result['list']);
		$this->assign('year_stat',$result['year_stat']);
		
		$this->assign('stat_type',$stat_type);
		$this->assign('year',$year);
		
		$this->display();
	}
}