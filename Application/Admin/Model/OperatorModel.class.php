<?php
/**
 * Created by PhpStorm.
 * User: panda
 * Date: 2017/7/26
 * Time: 15:05
 */

namespace Admin\Model;
use Think\Model;

class OperatorModel extends Model
{
    public function getall(){
        return $this->select();
    }
}