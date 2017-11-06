<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
		//加载前台热销，推荐，新品
		$this->assign('is_show',1);
		$goodsModel = D('Admin/Goods');
		$hot = $goodsModel->getRecGoods('is_hot');
	    $this->assign('hot',$hot);
	    $goodsModel = D('Admin/Goods');
	    $rec = $goodsModel->getRecGoods('is_rec');
	    $this->assign('rec',$rec);
	    $goodsModel = D('Admin/Goods');
	    $new = $goodsModel->getRecGoods('is_new');
	    $this->assign('new',$new);
		//显示疯狂抢购产品
	    $crazy = $goodsModel->getCrazyGoods();
		$this->assign('crazy',$crazy);

		$floor = D('Admin/Category')->getFloor();
	    $this->assign('floor',$floor);
 		$this->display();
    }
}