<?php
namespace Home\Controller;

class GoodsController extends CommonController {
    public function index(){
    	$goods_id = intval(I('get.goods_id'));

    	if($goods_id<=0){
			$this->redirect('Index/index');
	    }

	    $goodsModel = D('Admin/Goods');
		$goods = $goodsModel->where('is_sale=1 and id='.$goods_id)->find();

		if(!$goods_id){
			$this->redirect('Index/index');
		}

		$goods['goods_body'] = htmlspecialchars_decode($goods['goods_body']);

		$attr = M('GoodsAttr')->alias('a')->field('a.*,b.attr_name,b.attr_type')->join("left join jx_attribute b on a.attr_id = b.id")->where('a.goods_id='.$goods_id)->select();

	    foreach ($attr as $k => $v){
			if($v['attr_type']==1){
				$unique[] = $v;

			}else{
				$sigle[$v['attr_id']][] = $v;
			}

	    }

	    if(!$goods){
			$this->redirect('Index/index');
	    }

	    if($goods['cx_price']>0 && $goods['start']<time() && $goods['end']>time()){
			$goods['shop_price'] = $goods['cx_price'];

	    }

	    $pic = M('GoodsImg')->where('goods_id='.$goods_id)->select();
	    $this->assign('pic',$pic);
		//dump($goods);die;
        $this->assign('unique',$unique);
        $this->assign('sigle',$sigle);
        $this->assign('goods',$goods);

        $commentModel = D('Comment');
        $comment = $commentModel->getlist($goods_id);
        $this->assign('comment',$comment);
        //dump($comment);die;
        $buyer = M('Impression')->where('goods_id='.$goods_id)->order('count desc')->limit(8)->select();
        $this->assign('buyer',$buyer);
		$this->display();

    }

    public function comment(){

        $this->checkLogin();

        $model = D('Comment');
        $data = $model->create();
        if(!$data){
            $this->error('参数错误！');
        }

        $model->add($data);
        $this->success('写入成功！');
    }

    public function good(){

        $comment_id = I('post.comment_id');
        $model = D('Comment');
        $info = $model->where('id='.$comment_id)->find();
        if(!$info){
            $this->ajaxReturn(array('status'=>1,'msg'=>'error'));
        }

        $model->where("id=$comment_id")->setField('good_number',$info['good_number']+1);

        //dump($model->getLastSql());die;

        $this->ajaxReturn(array('status'=>1,'msg'=>'ok','good_number'=>$info['good_number']+1));
    }



}