<?php
namespace Home\Model;
class CartModel extends CommonModel {

	protected $fields = array('id','user_id','goods_id','goods_count','goods_attr_ids');
    public function addCart($goods_id,$goods_count,$attr){

		sort($attr);
		$goods_attr_ids = $attr?implode(',',$attr) :'';

		$res = $this->checkGoodsNumber($goods_id,$goods_count,$goods_attr_ids);
		if(!$res){
			$this->error = "库存不足！";
			return false;

		}

		$user_id = session('user_id');

		if($user_id){
			$data = array(
				'user_id' => $user_id,
				'goods_id'=> $goods_id,
				'goods_attr_ids'=> $goods_attr_ids

			);
			$info = $this->where($data)->find();
			if($info){
				$this->where($data)->setField('goods_count',$goods_count+$info['goods_count']);
			}else{
				$data['goods_count'] = $goods_count;
				$this->add($data);

			}
		}else{

			$cart = unserialize(cookie('cart'));
			$key = $goods_id .'-'.$goods_attr_ids;
			if(array_key_exists($key,$cart)){
				$cart[$key] += $goods_count;

			}else{
				$cart[$key] = $goods_count;

			}
		cookie('cart',serialize($cart));

		}
		return true;

    }

    public function checkGoodsNumber($goods_id,$goods_count,$goods_attr_ids){

    	$goods = D('Admin/Goods')->where("id=$goods_id")->find();

    	if($goods['goods_number']<$goods_count){
			return false;

	    }

	    if($goods_attr_ids){
			$where = "goods_id=$goods_id and goods_attr_ids = '$goods_attr_ids'";
			$number = M('GoodsNumber')->where($where)->find();
			if(!$number || $number['goods_number']<$goods_count){
				return false;
			}

	    }
	    return true;
    }

	public function cookie2db(){

    	$cart = unserialize(cookie('cart'));
    	//dump($cart);die;
    	$user_id = session('user_id');
    	if(!$user_id){
			return false;
	    }

	    foreach($cart as $key => $value){
			$tmp = explode('-',$key);
			$map = array(
				'user_id'=>$user_id,
				'goods_id'=>$tmp[0],
				'goods_attr_ids'=>$tmp[1]
			);
		    $info = $this->where($map)->find();
		    //dump($info)die;
		    if($info){
				$this->where($map)->setField('goods_count',$value+$info['goods_count']);
		    }else{
				$map['goods_count'] = $value;
				$this->add($map);
		    }

	    }

		cookie('cart',null);
	}

	public function getlist(){

		$user_id = session('user_id');
		if($user_id){
			$data = $this->where('user_id='.$user_id)->select();

		}else{
			$cart = unserialize(cookie('cart'));
			foreach($cart as $key=>$value){
				$tmp = explode('-',$key);
				$data[] = array(
					'goods_id'=>$tmp[0],
					'goods_attr_ids'=>$tmp[1],
					'goods_count'=>$value,
				);
			}

		}

		$goodsModel = D('Admin/Goods');
		foreach($data as $key=>$value){

			$goods = $goodsModel->where('id='.$value['goods_id'])->find();
			if($goods['cx_price']>0 && $goods['star']<time() && $goods['end']>time()){
				$goods['cx_price'] = $goods['shop_price'];

			}
			$data[$key]['goods'] = $goods;

			if($value['goods_attr_ids']){
				$attr = M('GoodsAttr')->alias('a')->join('left join jx_attribute b on a.attr_id = b.id')->field('a.attr_values,b.attr_name')->where('a.id in ('.$value['goods_attr_ids'].')')->select();
            $data[$key]['attr'] = $attr;
			}
		}
		//dump($attr);die;
		return $data;
	}

	public function getTotle($data){
		$count = $price = 0;

		foreach($data as $key => $value){
			$count += $value['goods_count'];
			$price += $value['goods_count']*$value['goods']['shop_price'];
		}
		return array('count'=>$count,'price'=>$price);


	}

	public function dels($goods_id,$goods_attr_ids){
		$goods_attr_ids = $goods_attr_ids ? $goods_attr_ids : '';

		$user_id = session('user_id');
		if($user_id){
			$where = "user_id=$user_id and goods_id = $goods_id and goods_attr_ids = '$goods_attr_ids'";
			$this->where($where)->delete();
		}else{
			$cart = unserialize(cookie('cart'));
			$key = $goods_id.'-'.$goods_attr_ids;
			unset($cart[$key]);
			cookie('cart',serialize($cart));

		}

	}

	public function updateCount($goods_id,$goods_count,$goods_attr_ids){
        if($goods_count<=0){
            return false;
        }

        $goods_attr_ids = $goods_attr_ids ? $goods_attr_ids : '';

        $user_id = session('user_id');
        if($user_id){
            $where = " user_id = $user_id and goods_id = $goods_id and goods_attr_ids = '$goods_attr_ids'";
            $this->where($where)->setField('goods_count',$goods_count);
        }else{
            $cart = unserialize(cookie('cart'));

        }

    }

}