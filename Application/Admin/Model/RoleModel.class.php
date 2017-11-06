<?php
namespace Admin\Model;


class RoleModel extends CommonModel {

	//自定义字段
	protected $fields = array('id','role_name');

	protected $_validate = array(
		array('role_name','require','角色名称必须填写！'),
		array('role_name','','角色名称重复！',1,'unique'),
	);

	public function getlist(){

		$pagesize = 5;
		$count = $this->count();
		$page = new \Think\Page($count,$pagesize);
		$show = $page->show();
		$p = intval(I('get.p'));
		$list = $this->page($p,$pagesize)->select();
		return array('show'=>$show,'list'=>$list);

	}

	public function remove($id){
		return $this->where("id=$id")->delete();
	}
	
}