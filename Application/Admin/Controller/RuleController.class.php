<?php
namespace Admin\Controller;

class RuleController extends CommonController {

	public function index(){
		$m = D('Rule');
		$list = $m->getCateTree();
		//dump($list);die;
		$this->assign('list',$list);
		$this->display();
	}

	public function add(){

		if(IS_GET){
			$m = D('Rule');
			$Cate = $m->getCateTree();
			$this->assign('cate',$Cate);
			$this->display();
		}else{

			$m = D('Rule');
			$data = $m->create();

			if(!$data){
				$this->error($m->getError());
			}

			$insert = $m->add($data);

			if(!$insert){
				$this->error('数据写入失败');
			}
			$this->success('插入成功！');
		}

	}

	public function del(){

		$id = intval(I('get.id'));
		//echo $id;die;
		if($id<0){
			$this->error('参数不对！');
		}

		$m = D('Rule');
		$res = $m->del($id);
		if($res===false){
			$this->error('删除失败！');
		}
		$this->success('删除成功！');
	}


	public function edit(){

		if(IS_GET){

			$id = intval(I('get.id'));
			$m = D('Rule');
			$res = $m->findOneById($id);
			//dump($res);die;
			$this->assign('res',$res);

			$cate = $m->getCateTree();
			$this->assign('cate',$cate);
			$this->display();
		}else{

			$m = D('Rule');
			$data = $m->create();
			$res = $m->update($data);
			//dump($data);
			if($res === false){
				$this->error($m->getError());

			}
			$this->success('更新成功！');
		}
	}


}