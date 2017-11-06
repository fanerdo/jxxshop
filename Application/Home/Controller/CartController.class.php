<?php
namespace Home\Controller;

class CartController extends CommonController {
	public function addCart(){
	   //dump(I('post.'));die;
		$goods_id = intval(I('post.goods_id'));
		$goods_count = intval(I('post.goods_count'));
		$attr = I('post.attr');
		$model = D('Cart');
		$res = $model->addCart($goods_id,$goods_count,$attr);
		if(!$res){
			$this->error($model->getError());
		}
		$this->success('写入成功！');
   }

   public function index(){

		$model = D('Cart');
		$data = $model->getlist();
		$this->assign('data',$data);
        //dump($data);//die;
		$total = $model->getTotle($data);
		//dump($total);die;
		$this->assign('total',$total);
		$this->display();

   }

   public function dels(){
   	    $goods_id = intval(I('get.goods_id'));
	    $goods_attr_ids = I('get.goods_attr_ids');
	    D('Cart')->dels( $goods_id, $goods_attr_ids);
		$this->success('删除成功！');
   }

   public function updateCount(){

       $goods_id = intval(I('post.goods_id'));
       $goods_count = intval(I('post.goods_count'));
       $goods_attr_ids = intval(I('post.goods_attr_ids'));

       D('Cart')->updateCount($goods_id,$goods_count,$goods_attr_ids);
   }
}