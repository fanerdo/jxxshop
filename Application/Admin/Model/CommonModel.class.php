<?php
namespace Admin\Model;

use Think\Model;

class CommonModel extends Model {
/*
	public function __construct()
	{
		parent::__construct();
	}
*/

	public function findOneById($id){

		return $this->where("id=$id")->find();

	}
}