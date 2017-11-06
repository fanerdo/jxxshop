<?php
namespace Admin\Model;

class RuleModel extends CommonModel {

	//自定义字段
	protected $fields = array('id','rule_name','module_name','controller_name','action_name','parent_id','is_show');

	protected $_validate = array(
		array('rule_name','require','权限名称必须填写！'),
		array('module_name','require','模块名称必须填写！'),
		array('controller_name','require','控制器名称必须填写！'),
		array('action_name','require','方法名称必须填写！'),
	);

	//获取分类下的所有子分类
	public function getChildren($id){

		$data = $this->select();
		$this->getTree($data,$id,1,false);
		foreach($list as $value){
			$tree[] = $value['id'];
		}
		return $tree;
	}

	public function getCateTree($id=0){

		$data = $this->select();

		$list = $this->getTree($data,$id);

		return $list;
	}

	public function getTree($data,$id,$lev=1,$iscache=true){
		static $list = array();
		if(!$iscache){

			$list = array();
		}


		foreach ($data as $v) {

			if($v['parent_id']==$id){

				$v['lev'] = $lev;
				$list[] = $v;
				$this->getTree($data,$v['id'],$lev+1);

			}
		}
		return $list;
	}

	public function del($id){

		$res = $this->where("parent_id=".$id)->find();
		if($res){
			return false;
		}

		return $this->where('id='.$id)->delete();
	}


	public function update($data){
		//dump($data);die;
		$tree = $this->getCateTree($data['id']);
		//dump($tree);die;
		$tree[] = array('id' => $data['id']);
		//dump($tree);die;
		foreach ($tree as $v){

			if($data['parent_id']==$v['id']){
				$this->error = '不能设置分类为子分类';
				return false;
			}

		}
		return $this->save($data);
	}


	
}