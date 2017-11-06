<?php
namespace Home\Controller;

class ApiController extends CommonController {

    public function login(){

        $username = I('get.username');
        $password = I('get.password');
        //dump($username);die;
        if(!$username || $password){
            $this->ajaxReturn(array('status'=>0 , 'msg' => '参数错误！'));
        }

        $model = D('User');
        $info = $model->where("username='$username'")->find();

        if(!$info){
            $this->ajaxRturn(array('status'=>0,'msg'=>'用户名错误！'));
        }

        if($info['status']!=1){
            $this->ajaxReturn(array('status'=>0,'msg'=>'用户没有激活！'));
        }

    }



}

