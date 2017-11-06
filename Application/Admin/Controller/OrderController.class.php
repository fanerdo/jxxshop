<?php
namespace Admin\Controller;

class OrderController extends CommonController {


	public function index(){

        $data = M('Order')->select();
        $this->assign('data',$data);
        //dump($data);die;
        $this->display();
    }

    public function send()
    {

        if(IS_GET){

            $order_id = I('get.order_id');
            $info = M('Order')->alias('a')->field('a.*,b.username')->join('left join jx_user b on a.user_id = b.id')->where('a.id='.$order_id)->find();
            $this->assign('info',$info);
            $this->display();
        }else{

            $order_id = I('get.order_id');

            $data = array(
                'no' => I('post.no'),
                'order_status'=> 2
            );

            $info = M('Order')->where('id='.$order_id)->save($data);
            $this->success('发货成功！');
        }


    }

}