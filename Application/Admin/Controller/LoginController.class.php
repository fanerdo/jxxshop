<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {

	public function login(){

		if(IS_GET){
			$this->display();
		}else{
			$captcha = I('post.captcha');
			//dump($captcha);die;
			$ver = new \Think\Verify();
			$res = $ver->check($captcha);
			if(!$res){
				$this->error('验证码错误！');
			}

			$username = I('post.username');
			$password = I('post.password');
			$m = D('Admin');
			$res = $m->Login($username,$password);
			if(!$res){
				$this->error($m->getError());
			}

			$this->success('登陆成功！',U('Index/index'));
		}

	}

	public function verify(){

		$config = array('length'=>3,'codeSet'=>'123');
		$ver = new \Think\Verify($config);
		$ver->entry();
	}

}