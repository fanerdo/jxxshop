<?php
namespace Admin\Controller;

class AdminController extends CommonController {


	public function add(){

		if(IS_GET){
			$role = D('Role')->select();
			$this->assign('role',$role);
			$this->display();
		}else{
			$m = D('Admin');
			$data = $m->create();
			if(!$data){
				$this->error($m->getError());
			}

			$m->add($data);
			$this->success('添加成功！');

		}
	}


	public function index(){
		$m = D('Admin');
		$data = $m->listDate();
		$this->assign('data',$data);
		$this->display();
	}


	public function dels(){

		$id = intval(I('get.id'));
		if($id<=0){
			$this->error('参数错误！');
		}

		$res = D('Admin')->remove($id);
		if(!$res){
			$this->error('删除失败！');
		}

		$this->success('删除成功！');

	}

	public function edit(){
		$m = D('Admin');
		if(IS_GET){
			$id = intval(I('get.id'));
			$info = $m->findOne($id);
			//dump($info);die;
			$role = D('Role')->select();
			$this->assign('info',$info);
			$this->assign('role',$role);
			$this->display();
		}else{
			$data = $m->create();
			//dump($data);die;
			if(!$data){
				$this->error($m->getError());
			}

			if($data['id']<=1){
				$this->error('参数错误！');
			}

			$m->update($data);

			$this->success('更新成功！',U('index'));

		}
	}


}