<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {

	public function __construct()
	{
		parent::__construct();
		$cate = D('Admin/Category')->getCateTree();
		$this->assign('cate',$cate);
	}

    public function checkLogin(){
	    $user_id = session('user_id');
	    if(!$user_id){
            $this->error('请先登陆',U('User/login'));
        }

    }
}