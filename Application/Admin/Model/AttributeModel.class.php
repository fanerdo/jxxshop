<?php
namespace Admin\Model;

use function cookie;
use const false;

class AttributeModel extends CommonModel {

	//自定义字段
	protected $fields = array('id','attr_name','type_id','attr_type','attr_input_type','attr_value');

	protected $_validate = array(
		array('attr_name','require','角色名称必须填写！'),
		array('type_id','require','类型必须填写！'),
		array('attr_type','1,2','属性类型只能单选或者唯一！',1,'in'),
		array('attr_input_type','1,2','属性录入方式只能手工或者列表！',1,'in'),

	);

	public function listData(){

		$pagezise = 3;
		$count = $this->count();

		$page = new \Think\Page($count,$pagezise);
		$show = $page->show();
		$p = intval(I('get.p'));
		$list = $this->page($p,$pagezise)->select();

		$type = D('Type')->select();
		foreach($type as $k=>$v){
			$typeinfo[$v['id']] = $v;

		}

		foreach($list as $k=>$v){
			$list[$k]['type_id'] = $typeinfo[$v['type_id']]['type_name'];
		}
		return ['pageStr'=>$show,'list'=>$list];
	}

	public function remove($attr_id){

		return $this->where("id=$attr_id")->delete();
	}





}