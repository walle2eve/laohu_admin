<?php
/**
 * Created by PhpStorm.
 * User: panda
 * Date: 2017/8/10
 * Time: 14:11
 */

namespace Admin\Model;
use Think\Model;

class ThemeModel extends Model
{
    protected $fields = array(
        'id',
        'name',
        'input_time',
    );

    protected $_validate = array(
        array('id','require','游戏主题ID必须'),
        array('id','','游戏主题ID已经存在',0,'unique',1),
        array('name','require','游戏主题名称必须'),
    );
}