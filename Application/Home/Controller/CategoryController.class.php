<?php
namespace Home\Controller;

class CategoryController extends CommonController {

    public function index(){

        $model = D('Admin/Goods');
        $data = $model->getList2();
        //dump($data);die;
        $this->assign('data',$data);
        $this->display();

    }

}