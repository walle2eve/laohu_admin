<?php 
namespace Admin\Model;
use Think\Model;

class LoginLogModel extends Model
{
    protected $connection = 'DB_LAOHU_LOG_CONFIG';
	protected $trueTableName;
		
	public function __construct($tableName){
		parent::__construct($tableName);
		
		$this->trueTableName = $tableName;
		print_r($this->trueTableName);
	}
}