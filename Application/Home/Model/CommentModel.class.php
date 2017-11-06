<?php
namespace Home\Model;
class CommentModel extends CommonModel {

	protected $fields = array('id','user_id','goods_id','addtime','content','star','good_number');

    public function _before_insert(&$data)
    {
        $data['addtime'] = time();
        $data['user_id'] = session('user_id');

    }

    public function _after_insert($data)
    {
        $old = I('post.old');
        foreach($old as $key => $value){
            M('Impression')->where('id='.$value)->setInc('count');
        }

        $name = I('post.name');
        $name = explode(',',$name);

        $name = array_unique($name);
        foreach ($name as $key=>$value){
            if(!$value){
                continue;
            }
            $where = array('goods_id'=>$data['goods_id'],'name'=>$value);
            $model = M('Impression');
            $res = $model->where($where)->find();
            if($res){
                $model->where($where)->setInc('count');
            }else{
                $where['count'] = 1;
                $model ->add($where);

            }
        }

        M('Goods')->where('id='.$data['goods_id'])->setInc('plcount');
    }

    public function getlist($goods_id){

        $p = I('get.p');
        $pagesize = 5;
        $count = $this->where('goods_id='.$goods_id)->count();
        $page = new \Think\Page($count,$pagesize);
        $page->setConfig('is_anchor',true);

        $show = $page->show();

        $list = $this->alias('a')->field('a.*,b.username')->join('left join jx_user b on a.user_id = b.id')->where("goods_id=$goods_id")->page($p,$pagesize)->select();

        return array('list'=>$list,'page'=>$show);
    }


}