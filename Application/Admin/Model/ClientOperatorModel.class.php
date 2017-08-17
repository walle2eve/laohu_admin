<?php
namespace Admin\Model;
use Think\Model;

class ClientOperatorModel extends Model{
    protected $pk     = 'id';

	protected $_validate = array(
        array('operator_id','require','请选择平台'),
		array('version_no','require','版本号必须'),
		//array('version_no','','版本号已经存在',0,'unique',1),
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
	// 列表
    public function get_list($user_role,$param = array()){

        $where = ' 1=1 ';

        if($param['operator_id'] >= 0){
            $where .= ' AND sro.role_id = ' . $user_role;
        }

        if(isset($param['operator_id']) && abs($param['operator_id']) > 0)
            $where .= ' AND client.operator_id = ' . intval($param['operator_id']);

        $count = $this->alias('client')
            ->join("LEFT JOIN __SYS_ROLE_OPERATOR__ sro ON sro.operator_id = client.operator_id")
            ->where($where)->count();

        $page = page($count);

        $list = $this->alias('client')
            ->field('client.*')
            ->join("LEFT JOIN __SYS_ROLE_OPERATOR__ sro ON sro.operator_id = client.operator_id")
            ->where($where)
            ->order('operator_id,id ASC')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();

        return array('list'=>$list,'page'=>$page->show());

    }

	public function get_last_version($user_role,$param=array()){
		$where = ' 1=1 ';

        if($param['operator_id'] >= 0){
            $where .= ' AND sro.role_id = ' . $user_role;
        }

        if(isset($param['operator_id']) && abs($param['operator_id']) > 0)
            $where .= ' AND client.operator_id = ' . intval($param['operator_id']);

		$version_info = $this->alias('client')
                    ->join("LEFT JOIN __SYS_ROLE_OPERATOR__ sro ON sro.operator_id = client.operator_id")
                    ->where($where)
					->order('client.id DESC')
					->find();

		//$version_info['conf'] = unserialize($version_info['conf']);
		return $version_info;
	}
}
?>