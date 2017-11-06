<?php
namespace Admin\Controller;
class RoleController extends CommonController {
	public function add(){

		if(IS_GET){
			$this->display();
		}else{
			$m = D('Role');
			$data = $m->create();
			if(!$data){
				$this->error($m->getError());
			}
			$res = $m->add($data);
			$this->success('添加OK！');
		}

	}

	public function index(){
		$m = D('Role');
		$data = $m->getlist();
		$this->assign('data',$data);
		$this->display();

	}

	public function edit(){

		$m = D('Role');

		if(IS_GET){
			$id = intval(I('get.id'));
			$res = $m->findOneById($id);
			$this->assign('res',$res);
			$this->display();
		}else{
			$data = $m->create();
			if(!$data){
				$this->error($m->getError());
			}
			if($data['id']<=1){
				$this->error('参数错误！');
			}

			$m->save($data);
			$this->success('更新成功！');
		}

	}

	public function dels(){
		$m = D('Role');
		$id = intval(I('get.id'));
		if($id<=1){
			$this->error('参数错误！');
		}
		$res = D('role')->remove($id);
		if($res===false){
			$this->error('删除失败');
		}
		$this->success('删除成功！');
	}


	public function disfetch(){

		if(IS_GET){

			$role_id = intval(I('get.role_id'));
			//dump($role_id);die;
			if($role_id<=1){
				$this->error('参数错误！');
			}

			$hasRules = D('RoleRule')->getRules($role_id);
			//dump($hasRules);die;
			foreach($hasRules as $v){
				$hasRulesIds[] = $v['rule_id'];
			}

			//dump($hasRulesIds);die;
			$this->assign('hasRules',$hasRulesIds);

			$m = D(Rule);
			$rule = $m->getCateTree();
			$this->assign('rule',$rule);
			$this->display();

		}else{
			$role_id = intval(I('post.role_id'));
			//dump($role_id);die;
			if($role_id<=1){
				$this->error('参数错误！');
			}
			$rules = I('post.rule');
			//dump($rules);die;
			D('RoleRule')->disfetch($role_id,$rules);
			$user_info = M('AdminRole')->where('role_id='.$role_id)->select();
			foreach ($user_info as $k => $v){
				S('user_'.$v['admin_id'],null);
			}

			$this->success('操所成功！',U('index'));
		}

	}

	public function flushAdmin(){
		$user = M('AdminRole')->where('role_id=1')->select();
		foreach ($user as $k=>$v){
			S('user_'.$v['admin_id'],null);
		}
		echo 'ok';

	}


}