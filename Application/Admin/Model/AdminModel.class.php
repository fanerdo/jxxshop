<?php
namespace Admin\Model;

class AdminModel extends CommonModel {

	//自定义字段
	protected $fields = array('id','username','password');

	protected $_validate = array(
		array('username','require','角色名称必须填写！'),
		array('username','','角色名称重复！',1,'unique'),
		array('password','require','密码必须填写！'),
	);


	protected $_auto = array(

		array('password','md5',3,'function')
	);

	protected function _after_insert($data)
	{
		$admin_role = array(
			'admin_id' => $data['id'],
			'role_id' => I('post.role_id')

		);

		M('AdminRole')->add($admin_role);
	}

	public function listDate(){
		$pagesize = 5;
		$count = $this->count();
		$page = new \Think\Page($count,$pagesize);
		$show = $page->show();
		$p = intval(I('get.p'));
		$list = $this->page($p,$pagesize)->alias('a')->field('a.username,c.*,b.role_name')->join('left join jx_admin_role c on a.id = c.admin_id ')->join('left join jx_role b on c.role_id = b.id')->select();
		//dump($list);die;
		return array('show'=>$show,'list'=>$list);

	}

	public function remove($id){

		$this->startTrans();
		$userStatus = $this->where("id=$id")->delete();

		if(!$userStatus){
			$this->rollback();
			return false;
		}

		$roleStatus = M('AdminRole')->where("admin_id=$id")->delete();
		if(!$roleStatus){
			$this->rollback();
			return false;
		}

		$this->commit();
		return true;
	}

	public function findOne($id){

		return $this->alias('a')->field('a.*,b.role_id')->join('left join jx_admin_role b on a.id = b.admin_id ')->where("a.id=$id")->select();

	}

	public function update($data){

		$id = intval(I('post.role_id'));

		$this->save($data);

		M('AdminRole')->where("admin_id=".$data['id'])->save(array('role_id'=>$id));
	}

	public function Login($username,$password){
		//dump($username);die;
		$user = $this->where("username = '$username'")->find();
		if(!$user){
			$this->error = "用户名不存在！";
			return false;
		}

		if($user['password'] != md5($password)){
			$this->error='密码错误！';
			return false;
		}
		cookie('admin',$user);
		return true;
	}



}