<?php
namespace Admin\Model;

class RoleRuleModel extends CommonModel {

	//自定义字段
	protected $fields = array('id','role_id','rule_id');

	public function disfetch($role_id,$rules){

		$this->where("role_id=$role_id")->delete();

		foreach ($rules as $v){

			$list[] = array(
				'role_id' => $role_id,
				'rule_id' => $v
			);

		}
		$this->addAll($list);
	}

	public function getRules($role_id){

		return $this->where("role_id=$role_id")->select();

	}

	
}