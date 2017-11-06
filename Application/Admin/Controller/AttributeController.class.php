<?php
namespace Admin\Controller;

use const false;

class AttributeController extends CommonController {


	public function add(){

		if(IS_GET){
			$type = D('type')->select();
			$this->assign('type',$type);
			$this->display();
		}else{
			$m = D('Attribute');
			$data = $m->create();
			//dump($data);
			if(!$data){
				$this->error($m->getError());
			}
			$m->add($data);
			$this->success('写入成功！');

		}
	}

	public function index(){
		$m = D('Attribute');
		$data = $m->listData();
		$this->assign('data',$data);
		$this->display();

	}


	public function edit(){
		if(IS_GET){
			$attr_id = intval(I('get.attr_id'));
			$info = D('Attribute')->findOneById($attr_id);
			$this->assign('info',$info);

			$type = D('Type')->select();
			//dump($type);die;
			$this->assign('type',$type);
			$this->display();
		}else{
			$m = D('Attribute');
			$data = $m->create();
			if(!$data){
				$this->error($m->getError());
			}
			if($data['id']<=0){
				$this->error('参数错误！');
			}

			$m->save($data);
			$this->success('修改成功！',U('index'));

		}

	}

	public function dels(){

		$attr_id = intval(I('get.attr_id'));
		if($attr_id<=0){
			$this->error('参数错误！');
		}
		$res = D('Attribute')->remove($attr_id);

		if($res === false){
			$this->error('删除失败！');

		}
		$this->success('删除成功！');


	}

}