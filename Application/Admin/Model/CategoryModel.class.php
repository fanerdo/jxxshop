<?php
namespace Admin\Model;

class CategoryModel extends CommonModel {

	//自定义字段
	protected $fields = array('id','cname','parent_id','isrec');

	protected $_validate = array(
		array('cname','require','分类信息必须填写！'),
	);

	//获取分类下的所有子分类
	public function getChildren($id){

		$data = $this->select();
        $list = $this->getTree($data,$id,1,false);
		foreach($list as $value){
			$tree[] = $value['id'];
		}
		//dump($list);die;
		return $tree;
	}

	public function getCateTree($id=0){

		$data = $this->select();

		$list = $this->getTree($data,$id);

		return $list;
	}

	public function getTree($data,$id=0,$lev=1,$iscache=true){
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

		$tree = $this->getCateTree($data['id']);
		//dump($tree);die;
		$tree[] = array('id' => $data['id']);
		//dump($data);die;
		foreach ($tree as $v){

			if($data['parent_id']==$v['id']){
				$this->error = '不能设置分类为子分类';
				return false;
			}

		}
		dump($data);die;
		return $this->save($data);
	}

	public function getFloor(){

		$data = $this->where("parent_id=0")->select();
		foreach($data as $k=>$v){
			$data[$k]['son'] = $this->where('parent_id='.$v['id'])->select();
			$data[$k]['recson'] =$this->where('isrec = 1 and parent_id='.$v['id'])->select();

			foreach($data[$k]['recson'] as $key=>$value){
				$data[$k]['recson'][$key]['goods'] = $this->getGoodsByCateId($value['id']);

			}
		}
		//dump($data);die;
		return $data;
	}

	public function getGoodsByCateId($cate_id,$limit=8){

		$children = $this->getChildren($cate_id);
		$children[] = $cate_id;
		$children = implode(',',$children);
		$goods = D('Goods')->where("is_sale=1 and cate_id in ($children)")->limit($limit)->select();
		return $goods;
	}
	
}