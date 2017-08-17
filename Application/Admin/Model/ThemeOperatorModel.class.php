<?php
/**
 * Created by PhpStorm.
 * User: panda
 * Date: 2017/8/10
 * Time: 14:13
 */

namespace Admin\Model;
use Think\Model;

class ThemeOperatorModel extends Model
{

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

        'lineNumChange' => array('field_type'=>'string'),
        'lineNum' => array('field_type'=>'string'),
        'linesBet' => array('field_type' => 'string'),
        'mul' => array('field_type'=>'string'),
        'bet' => array('field_type'=>'string'),
        'totalBet' => array('field_type' => 'string'),

        'num' => array('field_type'=>'string'),
        'model' => array('field_type'=>'string'),
        'MiniLogo' => array('field_type'=>'string'),
        'BackImage' => array('field_type'=>'string'),
        'LoadingInfo' => array('field_type'=>'string'),
        'LoadingScale' => array('field_type'=>'string'),
        'folder'=> array('field_type'=>'string'),
        'scale' => array('field_type'=>'string'),
        'width' => array('field_type'=>'string'),
        'height' => array('field_type' => 'string'),
        'IP'	=> array('field_type' => 'string'),
        'port' => array('field_type'=>'string'),
        'versionCode' => array('field_type' => 'string'),
        'sceneName' => array('field_type' => 'string'),
        'IsUseCommonHeaderImg' => array('field_type' => 'string'),
        'RTP' => array('field_type' => 'string'),
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

    // 主题管理活动列表
    public function get_list($user_role,$param = array()){

        $where = ' 1=1 ';

        if(isset($param['keyword']) && trim($param['keyword']) != ''){
            $where .= " AND (t.name LIKE '%".$param['keyword']."%') ";
        }

        if($param['operator_id'] >= 0){
            $where .= ' AND sro.role_id = ' . $user_role;
        }

        if(isset($param['operator_id']) && abs($param['operator_id']) > 0)
            $where .= ' AND tho.operator_id = ' . intval($param['operator_id']);

        $count = $this->alias('tho')
            ->field('t.name as theme_name,tho.theme_id,tho.operator_id,tho.status,tho.is_activity,tho.sort,tho.input_time')
            ->join("LEFT JOIN __THEME__ t ON t.id = tho.theme_id")
            ->join("LEFT JOIN __SYS_ROLE_OPERATOR__ sro ON sro.operator_id = tho.operator_id")->where($where)->count();

        $page = page($count);

        $list = $this->alias('tho')
            ->field('tho.id,t.name as theme_name,tho.theme_id,tho.operator_id,tho.status,tho.is_activity,tho.sort,tho.input_time')
            ->join("LEFT JOIN __THEME__ t ON t.id = tho.theme_id")
            ->join("LEFT JOIN __SYS_ROLE_OPERATOR__ sro ON sro.operator_id = tho.operator_id")
            ->where($where)
            ->order('operator_id,sort ASC')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();

        return array('list'=>$list,'page'=>$page->show());

    }

    // 添加活动处的主题列表
    public function get_list_by_operator($operator_id,$is_activity=0){

        $where = " tho.status = 1 AND tho.is_activity = " . intval($is_activity) . " AND t.id > 0 AND tho.operator_id = " . $operator_id;

        $list = $this->alias('tho')
            ->field('t.name as theme_name,tho.theme_id')
            ->join("LEFT JOIN __THEME__ t ON t.id = tho.theme_id")
            ->where($where)
            ->order('sort ASC')
            ->select();

        return $list;
    }

    // 根据id获取详细信息
    public function find_one($id){
        $where = " tho.id =  " . $id;

        return $this->alias('tho')
            ->field('t.name as theme_name,tho.*')
            ->join("LEFT JOIN __THEME__ t ON t.id = tho.theme_id")
            ->where($where)
            ->order('sort ASC')
            ->find();
    }

    // 根据operator_id和theme_id查找
    public function get_theme($operator_id,$theme_id){

        $where = " tho.operator_id =  " . $operator_id . " AND tho.theme_id = " . $theme_id;

        return $this->alias('tho')
            ->field('tho.*')
            ->where($where)
            ->order('sort ASC')
            ->find();
    }
}