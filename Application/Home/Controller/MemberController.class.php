<?php
namespace Home\Controller;

class MemberController extends CommonController {

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }


    public function order(){

        $user_id = session('user_id');
        $data = D('Order')->where('user_id='.$user_id)->select();
        $this->assign('data',$data);
        $this->display();

    }

    public function express()
    {
        $order_id = I('get.order_id');
        $info = M('Order')->where('id='.$order_id)->find();
        //dump($info);die;
        if(!$info || $info['order_status']!=2){
            $this->error('参数错误！');

        }

        $res = $this->kd($info['no']);
        if(!$res){
            $this->error('快递查询失败！');
        }


        $res = json_decode($res,true);
        //dump($res['data']);die;
        $this->assign('data',$res['data']);
        $this->display();

    }


    public function kd($kdh)
    {

        if ($kdh) {

            //$kdh = '62253595669';

            $text = file_get_contents('http://www.kuaidi100.com/autonumber/autoComNum?text=' . $kdh);
            //var_dump($text);

            $res = json_decode($text, true);

            //var_dump($res);

            $type = $res['auto'][0]['comCode'];
            //var_dump($type);

            $kd = file_get_contents("http://www.kuaidi100.com/query?type=" . $type . "&postid=" . $kdh . "&id=1&valicode=&temp=0.34396813391111913");

            //$kd = json_decode($kd);
            return $kd;

        } else {

            return false;
        }
    }

}