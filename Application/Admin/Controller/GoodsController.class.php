<?php
namespace Admin\Controller;

class GoodsController extends CommonController
{

	public function index()
	{
		$cate = D('Category')->getCateTree();
		$this->assign('cate', $cate);

		$m = D('Goods');
		$data = $m->getList();
		$this->assign('data', $data);
		$this->display();
	}

	public function add()
	{

		if (IS_GET) {
			$type = D('Type')->select();
			//dump($type);die;
			$this->assign('type', $type);
			$m = D('Category');
			$cate = $m->getCateTree();
			//dump($cate);die;

			$this->assign('cate', $cate);
			$this->display();
			die();
		}

		$m = D('Goods');
		$data = $m->create();
		//dump($data);die;
		if (!$data) {
			$this->error($m->getError());
		}

		$row = $m->add($data);

		if (!$row) {
			$this->error($m->getError());
		}

		$this->success('添加成功！');
	}

	//伪删除

	public function dels()
	{
		$id = intval(I('get.id'));
		if ($id <= 0) {
			$this->error('参数错误！');
		}

		$m = D('Goods');
		$res = $m->dels($id);
		if ($res === false) {
			$this->error('删除失败！');
		}
		$this->success('删除成功！');
	}

	//编辑
	public function edit()
	{

		if (IS_GET) {
			$id = intval(I('get.id'));
			$model = D('Goods');
			$data = $model->findOne($id);
			//dump($data);die;

			$mo = D('Category');
			$cate = $mo->getCateTree();

			$ext = M('GoodsCate')->where("goods_id=$id")->select();
			//dump($cate);die;

			if (!$ext) {
				$ext = array(
					array('msg' => 'nodata'),
				);
			}
			$data['goods_body'] = htmlspecialchars_decode($data['goods_body']);
			$this->assign('ext', $ext); //商品属于的分类信息
			$this->assign('data', $data);//商品信息
			$this->assign('cate', $cate);//获取分类列表
			$type = D('Type')->select();
			$this->assign('type', $type);//获取类型
			$goodsAttrModel = M('GoodsAttr');
			$attr = $goodsAttrModel->alias('a')->field('a.*,b.attr_name,b.attr_type,b.attr_input_type,b.attr_value')->join('left join jx_attribute b on a.attr_id=b.id')->where('a.goods_id=' . $id)->select();

			foreach ($attr as $key => $value) {
				if ($value['attr_input_type'] == 2) {
					$attr[$key]['attr_value'] = explode(',', $value['attr_value']);
				}
			}

			foreach ($attr as $key => $value) {
				$attr_list[$value['attr_id']][] = $value;
			}
			//dump($attr_list);
			$this->assign('attr', $attr_list);

			//对应的相册图片信息
			//dump($goods_id);die;
			$goods_img_list = M('GoodsImg')->where('goods_id=' . $id)->select();
			$this->assign('goods_img_list', $goods_img_list);

			$this->display();
		} else {

			$m = D('Goods');
			$data = $m->create();
			//dump($data);die;
			$data['id'] = intval(I('get.id'));
			if (!$data) {
				$this->error($m->getError());
			}

			$res = $m->update($data);
			if ($res === false) {
				$this->error($m->getError());
			}

			$this->success('更新成功！');
		}
	}

	public function trash()
	{

		$cate = D('Category')->getCateTree();
		$this->assign('cate', $cate);

		$m = D('Goods');
		$data = $m->getList(0);
		$this->assign('data', $data);
		$this->display();

	}

	public function recover()
	{

		$id = intval(I('get.id'));

		$m = M('Goods')->where("id=$id")->setField('isdel', 1);
		if ($m === false) {
			$this->error('还原失败！');
		}
		$this->success('还原OK！', U('index'));
	}

	public function cdel()
	{

		$id = intval(I('get.id'));

		$m = M('Goods')->where("id=$id")->delete();
		if ($m === false) {
			$this->error('删除失败！');
		}
		$this->success('删除彻底OK！', U('trash'));
	}


	public function showAttr()
	{
		$type_id = intval(I('post.type_id'));
		if ($type_id <= 0) {
			echo '没有数据';
			exit;
		}
		$data = D('Attribute')->where('type_id=' . $type_id)->select();
		foreach ($data as $key => $value) {
			if ($value['attr_input_type'] == 2) {
				//是一个列表选择 因此需要处理默认值
				$data[$key]['attr_value'] = explode(',', $value['attr_value']);
			}
		}
		$this->assign('data', $data);
		$this->display();
	}

	//实现相册中图片的删除功能
	public function delImg()
	{
		$img_id = intval(I('post.img_id'));
		if ($img_id <= 0) {
			$this->ajaxReturn(array('status' => 0, 'msg' => '参数错误'));
		}
		//先将图片删除掉
		$model = D('GoodsImg');
		$info = $model->where('id=' . $img_id)->find();
		if (!$info) {
			$this->ajaxReturn(array('status' => 0, 'msg' => '参数错误'));
		}
		unlink($info['goods_img']);
		unlink($info['goods_thumb']);
		//将图片对应的数据库中的信息删除
		$model->where('id=' . $img_id)->delete();
		$this->ajaxReturn(array('status' => 1, 'msg' => 'ok'));
	}

	public function setNumber()
	{

		if (IS_GET) {
			$goods_id = intval(I('get.goods_id'));
			$GoodsAttrModel = D('GoodsAttr');
			$attr = $GoodsAttrModel->getSigleAtte($goods_id);
			//dump($attr);die;
			if(!$attr){
				$info = D('Goods')->where('id='.$goods_id)->find();
				$this->assign('info',$info);
				$this->display('nosigle');die;
			}

			$info = M('GoodsNumber')->where('goods_id='.$goods_id)->select();

			if(!$info){
				$info = array('goods_number'=>0);

			}

			$this->assign('attr', $attr);
			$this->display();
		} else {
			//dump(I('post.'));die;
			$attr = I('post.attr');
			$goods_number = I('post.goods_number');
			$goods_id = I('post.goods_id');

			foreach ($goods_number as $key => $value) {
				$tmp = array();
				foreach ($attr as $k => $v) {
					$tmp[] = $v[$key];
				}
				sort($tmp);
				//dump($tmp);die;
				$goods_attr_ids = implode(',', $tmp);

				if(in_array($goods_attr_ids,$has)){
					unset($goods_number['$key']);
					continue;
				}
				$has[] = $goods_attr_ids;
				$list[] = array(
					'goods_id' => $goods_id,
					'goods_number' => $value,
					'goods_attr_ids' => $goods_attr_ids
				);

			}
			//dump($list);die;

			M('GoodsNumber')->where('goods_id='.$goods_id)->delete();
			M('GoodsNumber')->addAll($list);

			$goods_count = array_sum($goods_number);
			D('Goods')->where('id='.$goods_id)->setField('goods_number',$goods_count);
			$this->success('添加成功！');

		}
	}
}