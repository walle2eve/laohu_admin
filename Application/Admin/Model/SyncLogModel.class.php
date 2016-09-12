<?php
namespace Admin\Model;
use Think\Model\MongoModel;

class SyncLogModel extends MongoModel
{
    protected $connection 	= 	'DB_TYPE_MONGO_CONFIG';
    protected $dbName		=	'laohu_log';
	protected $tablePrefix 	= 	'';

	public function testMongo(){

	}
}