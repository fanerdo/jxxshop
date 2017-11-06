<?php
namespace Admin\Model;

class GoodsModel extends CommonModel {

	//自定义字段
	protected $fields = array('id','goods_name','goods_sn','cate_id','market_price','shop_price','goods_img','goods_thumb','goods_body','is_hot','is_rec','is_new','addtime','isdel','is_sale','type_id','goods_number','cx_price','start','plcount','sale_number','end');

	protected $_validate = array(
		array('goods_name','require','商品名称必须填写',1),
		array('cate_id','checkCategory','分类必须填写',1,'callback'),
		array('market_price','currency','市场价格格式不对'),
		array('shop_price','currency','本店价格格式不对'),
	);


	public function checkCategory($cate_id){

		$cate_id = intval($cate_id);
		if($cate_id > 0){
			return true;
		}
		return false;
	}


	public function _before_insert(&$data)
	{
		if($data['cx_price']>0){
			$data['start'] = strtotime($data['start']);
			$data['end'] = strtotime($data['end']);
		}else{
			$data['cx_price'] = 0.00;
			$data['start'] = 0;
			$data['end'] = 0;

		}
		$data['addtime'] = time();
		//dump($data);die;
		if (!$data['goods_sn']) {
			$data['goods_sn'] = 'JX' . uniqid(); //生成唯一标识
			//dump($data);die;
		} else {
			$row = $this->where("goods_sn = " . $data['goods_sn'])->find();
			if ($row) {
				$this->error = '货号重复！';
				return false;
			}

		}

		$res = $this->uploadImg();
		if($res){
			$data['goods_img'] = $res['goods_img'];
			$data['goods_thumb'] = $res['goods_thumb'];
		}

	}


	public function _after_insert($data){
		$goods_id=$data['id'];
		//接受提交的扩展分类
		$ext_cate_id = I('post.ext_cate_id');
		D('GoodsCate')->insertExtCate($ext_cate_id,$goods_id);

		//属性的入库
		$attr = I('post.attr');
		D('GoodsAttr')->insertAttr($attr,$goods_id);

		//实现商品相册图片上传以及入库
		//1、将商品图片上传释放
		unset($_FILES['goods_img']);
		$upload = new \Think\Upload();
		//商品相册图片批量上传
		$info = $upload->upload();
		foreach ($info as $key => $value) {
			//上传之后的图片地址
			$goods_img = 'Uploads/'.$value['savepath'].$value['savename'];
			//实现缩略图的制作
			$img = new \Think\Image();
			//打开图片
			$img->open($goods_img);
			//制作缩略图
			$goods_thumb = 'Uploads/'.$value['savepath'].'thumb_'.$value['savename'];
			$img->thumb(100,100)->save($goods_thumb);
			$list[]=array(
				'goods_id'=>$goods_id,
				'goods_img'=>$goods_img,
				'goods_thumb'=>$goods_thumb
			);
		}
		if($list){
			M('GoodsImg')->addAll($list);
		}
	}

	//列表及分页
	public function getList($id=1){
		$where = 'isdel='.$id;
		$pageseiz = 5;

		//查找分类下面的所有子分类
		//dump($cate_id);die;
		$cate_id = intval(I('get.cate_id'));

		if($cate_id){
			$cateModel = D('Category');
			$tree = $cateModel->getChildren($cate_id);
			//dump($cate_id);die;
			$tree[] = $cate_id;
			$ch = implode(',',$tree);

			//获取扩展分类商品ID
			$ext_goods_ids = M('GoodsCate')->group('goods_id')->where("cate_id in ($ch)")->select();
			//dump($ext_goods_ids);die;

			if($ext_goods_ids){
				foreach($ext_goods_ids as $v){
					$goods_ids[] = $v['goods_id'];
				}
				$goods_ids = implode(',',$goods_ids);
			}

			if(!$goods_ids){
				$where .= " AND cate_id in ($ch)";
			}else{
				$where .= " AND (cate_id in ($ch) or id in ($goods_ids))";
			}
		}

		//使用推荐搜索
		$intro_type = I('get.intro_type');
		if($intro_type){

			if($intro_type == 'is_new' || $intro_type == 'is_rec' || $intro_type == 'is_hot'){
				$where .= " AND $intro_type = 1";
			}

		}
		//使用上下架搜索

		$is_sale = I('get.is_sale');
		if($is_sale == 1){
			$where .= " AND is_sale = 1";

		}elseif ($is_sale == 2){
			$where .= " AND is_sale = 0";
		}

		//使用关键字搜索
		$keyword = I('get.keyword');
		if($keyword){
			$where .= " AND goods_name like '%$keyword%'";

		}
		//dump($where);die;

		$count = $this->where($where)->count();
		$page = new \Think\Page($count,$pageseiz);
		$show = $page->show();
		$p = intval(I('get.p'));
		if($p>1){
			$num = ($p-1)*$pageseiz;
		}else{
			$num = 0;
		}

		$data = $this->where($where)->page($p,$pageseiz)->select();

		return array('data'=>$data,'pageStr'=>$show,'num'=>$num);

	}

	public function dels($id){
		return $this->where("id=$id")->setField('isdel',0);

	}

	public function findOne($id){

		return $this->find($id);

	}

	//实现商品信息修改
	public function update($data)
	{

		if($data['cx_price']>0){
			$data['start'] = strtotime($data['start']);
			$data['end'] = strtotime($data['end']);

		}else{
			$data['price'] = 0.00;
			$data['start'] = 0;
			$data['end'] = 0;


		}

		$goods_id = $data['id'];
		$goods_sn =$data['goods_sn'];
		if(!$goods_sn){

			$data['goods_sn'] = 'JX'.uniqid();
		}else{
			//用户有提交货号 检查货号是否重复 并且需要将自己以前的货号排除在外
			$res = $this->where("goods_sn = '$goods_sn' AND id != $goods_id")->find();
			if($res){
				$this->error = '货号错误';
				return false;
			}
		}

		$extCateModel= D('GoodsCate');
		$extCateModel->where("goods_id = $goods_id")->delete();
		//将最新的扩展分类写入数据
		$ext_cate_id = I('post.ext_cate_id');
		$extCateModel->insertExtCate($ext_cate_id,$goods_id);

		//图片上传
		$res = $this->uploadImg();
		if($res){
			$data['goods_img'] = $res['goods_img'];
			$data['goods_thumb'] =  $res['goods_thumb'];
		}

		//属性修改
		$goodsAttrModel = D('GoodsAttr');
		$goodsAttrModel->where('goods_id='.$goods_id)->delete();
		$attr = I('post.attr');
		$goodsAttrModel->insertAttr($attr,$goods_id);

		//实现图片批量上传
		unset($_FILES['goods_img']);
		$upload = new \Think\Upload();
		$info = $upload->upload();
		//dump($info);die;
		foreach($info as $key => $value){
			$goods_img = 'Uploads/'.$value['savepath'].$value['savename'];
			$img = new \Think\Image();
			$img->open($goods_img);
			$goods_thumb = 'Uploads/'.$value['savepath'].'thumb_'.$value['savename'];
			$img->thumb(100,100)->save($goods_thumb);
			$list[] = array(
				'goods_id' => $goods_id,
				'goods_img' => $goods_img,
				'goods_thumb' => $goods_thumb
			);
		}

		if($list){
			M('GoodsImg')->addAll($list);
		}
		return $this->save($data);
	}

	public function uploadImg(){

		if(!isset($_FILES['goods_img']) || $_FILES['goods_img']['error']!=0) return false;

		$up = new \Think\Upload();
		$res = $up->uploadOne($_FILES['goods_img']);
		if(!$res){
			$this->error = $up->getError();
		}
		$goods_img = 'Uploads/'.$res['savepath'].$res['savename'];

		$img = new \Think\Image();
		$img->open($goods_img);
		$goods_thumb = 'Uploads/'.$res['savepath'].'thumb_'.$res['savename'];
		$img->thumb(450,450)->save($goods_thumb);
		$data['goods_img'] = $goods_img;
		$data['goods_thumb'] = $goods_thumb;
		return array('goods_img'=>$goods_img,'goods_thumb'=>$goods_thumb);

	}

	public function getRecGoods($type){

		return $this->where("is_sale=1 and $type = 1")->limit(5)->select();

	}

	public function getCrazyGoods(){
		$where = "is_sale = 1 and cx_price > 0 and start<".time()." and end >".time();
		return $this->where($where)->limit(5)->select();

	}


	public function getList2(){

	    $cate_id = I('get.id');

        $children = D('Admin/Category')->getChildren($cate_id);

	    $children[] = $cate_id;

	    $children = implode(',',$children);
	    $where = "is_sale=1 and cate_id in ($children)";

	    //价格区间
        $goods_info = $this->field('max(shop_price) max_price ,min(shop_price) min_price ,count(id) goods_count,group_concat(id) goods_ids')->where($where)->find();
       // dump($goods_info);die;
        if($goods_info['goods_count']>1){
            $cha = $goods_info['max_price']-$goods_info['min_price'];
            if($cha<100){
                $sec = 1;
            }elseif($cha<500){
                $sec = 2;
            }elseif($cha<1000){
                $sec = 3;
            }elseif($cha<5000){
                $sec = 4;
            }elseif($cha<10000){
                $sec = 5;
            }else{
                $sec = 6;
            }

            $price = array();
            $first = ceil($goods_info['min_price']);
            $zl = ceil($cha/$sec);
            for($i=0;$i<$sec;$i++){
                $price[] = $first .'-'.($first+$zl);
                $first += $zl;
            }
        }

        //接收价格筛选
        if(I('get.price')){
            $tmp = explode('-',I('get.price'));
            $where .= ' and shop_price >'.$tmp[0].' and shop_price <'.$tmp[1];
        }


        if($goods_info['goods_ids']){
            $attr = M('GoodsAttr')->alias('a')->field('distinct a.attr_id,a.attr_values,b.attr_name')->join('left join jx_attribute b on a.attr_id = b.id')->where('a.goods_id in ('.$goods_info["goods_ids"].')')->select();

            foreach ($attr as $key=>$value){
                $attrwhere[$value['attr_id']][] = $value;

            }
        }

        //实现属性筛选

        if(I('get.attr')){

            $attrParms = explode(',',I('get.attr'));
            //dump($attrParms);die;
            $goods = M('GoodsAttr')->field('group_concat(goods_id) as goods_ids')->where(array('attr_values'=>array('in',$attrParms)))->find();

            if($goods['goods_ids']){
                $where .= " and id in ({$goods['goods_ids']})";

            }
        }

       //dump($price);die;
	    $p = I('get.p');
	    $pagesize = 8;

	    $count = $this->where($where)->count();
	    $page = new \Think\Page($count,$pagesize);
	    $show = $page->show();

	    $sort = I('get.sort')?I('get.sort'):'sale_number';

	    $list = $this->where($where)->page($p,$pagesize)->order($sort.' desc')->select();
        //dump($this->getLastSql());die;
	    return array('list'=>$list,'page'=>$show,'price'=>$price,'attrwhere'=>$attrwhere);
    }


}
