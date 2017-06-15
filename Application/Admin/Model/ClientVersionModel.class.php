<?php
namespace Admin\Model;
use Think\Model;
class ClientVersionModel extends Model{
    protected $pk     = 'id';

	protected $_validate = array(
		array('version_no','require','版本号必须'),
		array('version_no','','版本号已经存在',0,'unique',1),
	);

	public $client_conf_field = array(
		'ClientVersion' => array('field_type'=>'string'),
		'isMust' => array('field_type'=>'string'),
		'ip' => array('field_type'=>'string'),
		'ClientUrl' => array('field_type'=>'string'),
		'Debuger' => array('field_type'=>'string'),
		'IosClientVersion' => array('field_type'=>'string'),
		'IosClientUrl' => array('field_type'=>'string'),
		'TestPlayRoomIp' => array('field_type'=>'string'),
		'TestPlayGameIp' => array('field_type'=>'string'),
		'IosDownloadUrl' => array('field_type'=>'string'),
		'AndroidDownloadUrl' => array('field_type'=>'string'),
		'fileSize' => array('field_type'=>'string'),
		'updateInfo' => array('field_type'=>'textarea'),
		'fileList' => array('field_type' => 'list','field_infos' => array(
			'FileName',
			'Version',
			'DateTime',
			'Info'
			)),
	);

	public function get_list(){

		$where = ' 1=1 ';
		
		//if($status <> 0) $where .= " AND status =  " . intval($status);

		//if(isset($param['keyword']) && trim($param['keyword']) != ''){
		//	$where .= " AND (name LIKE '%".$param['keyword']."%') ";
		//}

		$count = $this->where($where)->count();

		$page = page($count);

		$list = $this->where($where)
					->order('id DESC')
					->limit($page->firstRow.','.$page->listRows)
					->select();
		return array('list'=>$list,'page'=>$page->show());
	}

	public function get_last_version(){

		$where = ' 1=1 ';

		$version_info = $this->where($where)
					->order('id DESC')
					->find();
		$version_info['conf'] = unserialize($version_info['conf']);
		return $version_info;
	}
}
?>