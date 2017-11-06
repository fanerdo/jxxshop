<?php
/**
 * Created by PhpStorm.
 * User: d_z12
 * Date: 2017/10/13 0013
 * Time: 下午 10:09
 */

function myU($name,$value){

    if($name=='sort'){
        $sort = $value;
        $price = I('price');
    }elseif ($name=='price'){
        $price = $value;
        $sort = I('sort');

    }elseif ($name=='attr'){
        if(!$attr){
            $attr = $value;

        }else{
           // $attr = I('get.attr');
            $attr = explode(',',$attr);
            $attr[] = $value;
            $attr = array_unique($attr);
            $attr = implode(',',$attr);
        }

    }

    return U('Category/index').'?id='.I('get.id').'&sort='.$sort.'&price='.$price.'&attr='.$attr;

}