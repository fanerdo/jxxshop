<?php
namespace Home\Model;
class OrderModel extends CommonModel {

	protected $fields = array('id','user_id','addtime','total_price','pay_status','name','address','tel');

	public function order(){
        $cateModel = D('Cart');
        $data = $cateModel->getlist();
        if(!$data){
            $this->error = '购物车没有商品！';
            return false;
        }

        foreach ($data as $key => $value){
            $status = $cateModel->checkGoodsNumber($value['goods_id'],$value['goods_count'],$value['goods_attr_ids']);

            if(!$status){
                $this->error = '库存量不够！';
                return false;
            }
        }

        $total = $cateModel->getTotle($data);
        $order=array(
            'user_id'=>session('user_id'),
            'addtime'=>time(),
            'total_price'=>$total['price'],
            'name'=>I('post.name'),
            'address'=>I('post.address'),
            'tel'=>I('post.tel'),
        );
        //dump($order);die;
        $order_id = $this->add($order);

        foreach ($data as $key => $value){
            $goods_order[] = array(
                'order_id' => $order_id,
                'goods_id' => $value['goods_id'],
                'goods_attr_ids' => $value['goods_attr_ids'],
                'price' => $value['goods']['shop_price'],
                'goods_count' =>$value['goods_count']

            );
        }

        M('OrderGoods')->addAll($goods_order);
       //dump($data);die;
        foreach ($data as $key=>$value){
            M('Goods')->where('id='.$value['goods_id'])->setDec('goods_number',$value['goods_count']);
            M('Goods')->where('id='.$value['goods_id'])->setInc('sale_number',$value['goods_count']);

            if($value['goods_attr_ids']){
                $where = "goods_id=".$value['goods_id']." and goods_attr_ids = "."'".$value['goods_attr_ids']."'";
                M('GoodsNumber')->where($where)->setDec('goods_number',$value['goods_count']);
                //string(147) "INSERT INTO `jx_order` (`user_id`,`addtime`,`total_price`,`name`,`address`,`tel`) VALUES ('1','1507817074','1598','张三','湖北武汉','111111')"
            }
        }

        $order['total_price'] = $total['price'];//价格
        //dump($this->getLastSql());die;

        $user_id = session('user_id');
        $cateModel->where('user_id='.$user_id)->delete();
        $order['id'] = $order_id;


        return $order;
    }



}