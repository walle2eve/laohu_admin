<?php
namespace Admin\Model;
use Think\Model;

class ThemeInfoModel extends Model{
    protected $fields = array(
								'id',
								'name',
								'status',
								'theme_info',
								'sort',
								'input_time',
							);

    protected $pk     = 'id';

	protected $_validate = array(
		array('id','require','游戏主题ID必须'),
		array('id','','游戏主题ID已经存在',0,'unique',1),
		array('name','require','游戏主题名称必须'),
	);

	public $theme_conf_field = array(
		'themeid' => array('field_type'=>'string'),
		'themeName' => array('field_type'=>'string'),
		'atlasName' => array('field_type'=>'string'),
		'atlasName2' => array('field_type'=>'string'),
		'atlasName3' => array('field_type'=>'string'),
		'assetName' => array('field_type'=>'string'),
		'ContainerName' => array('field_type'=>'string'),
		'iconName' => array('field_type'=>'string'),
		'Logo' => array('field_type'=>'string'),
		'backGround' => array('field_type'=>'string'),
		'lineNum' => array('field_type'=>'string'),
		'num' => array('field_type'=>'string'),
		'model' => array('field_type'=>'string'),
		'MiniLogo' => array('field_type'=>'string'),
		'BackImage' => array('field_type'=>'string'),
		'LoadingInfo' => array('field_type'=>'string'),
		'LoadingScale' => array('field_type'=>'string'),
		'folder'=> array('field_type'=>'string'),
		//'backMusic' => array('field_type'=>'string'),
		//'spinMusic' => array('field_type'=>'string'),
		//'stopMusic' => array('field_type'=>'string'),
		'scale' => array('field_type'=>'string'),
		'width' => array('field_type'=>'string'),
		'height' => array('field_type' => 'string'),
		'IP'	=> array('field_type' => 'string'),
		'port' => array('field_type'=>'string'),
		'versionCode' => array('field_type' => 'string'),
		'sceneName' => array('field_type' => 'string'),
		'AnimationList' => array('field_type'=>'list','field_infos'=>array(
			'SpriteName',
			'id',
			'AnimationName',
			'ImageCount',
			'framesPerSecond',
			'width',
			'height',
			'hasZero',
			)),
		'SoundList' => array('field_type' => 'list','field_infos' => array(
			'key',
			'value',
			)),
		'file' => array('field_type' => 'list', 'field_infos' => array(
			'FileName',
			'Version',
			'DateTime',
			)),
		);

	public function get_list($param = array(),$status = 0){

		$where = ' 1=1 ';
		
		if($status <> 0) $where .= " AND status =  " . intval($status);

		if(isset($param['keyword']) && trim($param['keyword']) != ''){
			$where .= " AND (name LIKE '%".$param['keyword']."%') ";
		}

		$count = $this->where($where)->count();

		$page = page($count);

		$list = $this->where($where)
					->order('sort ASC')
					->limit($page->firstRow.','.$page->listRows)
					->select();
		return array('list'=>$list,'page'=>$page->show());
	}
}