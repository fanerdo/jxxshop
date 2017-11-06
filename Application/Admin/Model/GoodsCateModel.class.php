<?php
namespace Admin\Model;

class GoodsCateModel extends CommonModel {
	public function insertExtCate($ext_cate_id,$goods_id){

		$ext_cate_id = array_unique($ext_cate_id);

		foreach($ext_cate_id as $v){
			if($v!=0){
				$list[] = array('goods_id'=>$goods_id,'cate_id'=>$v);
			}
		}

		$this->addAll($list);
	}

}

