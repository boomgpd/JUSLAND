<?php
namespace Common\Model;
use Think\Model;

//购物车模型
class Shop_cartModel extends Model{
	
	protected $tableName = 'shop_goods_cart';

	private $son = 0;
	
	protected $_validate = array(

	);

	//添加购物车
	public function store($data){
		if(isset($_SESSION['member_id'])){//登录添加
			$cart = $this->where(array('member_id'=>$_SESSION['member_id'],'is_del'=>0))->select();
	        $key = NULL;
	        foreach($cart as $k => $v){
	            if($v['gid'] == $data['gid'] && $v['glid'] == $data['glid']){
	                $key = $v['cart_id'];
	                break;
	            };
	        };
	        if(is_null($key)){
	            $data['member_id'] = $_SESSION['member_id'];
	            $data['addtime'] = time();
	            $this->add($data);
	        }else{
	            $one = $this->where(array('cart_id'=>$key,'is_del'=>0))->find();
	            $one['num'] = $one['num'] + $data['num'];
	            $one['addtime'] = time();
	            $this->save($one);
	        };
	        $cart = $this->where(array('member_id'=>$_SESSION['member_id'],'is_del'=>0))->select();
		}else{//未登录添加
			if(isset($_SESSION['cart'])){
            $cart = $_SESSION['cart'];
            $key = NULL;
            foreach($cart as $k => $v){
                if($v['gid'] == $data['gid'] && $v['glid'] == $data['glid']){
                    $key = $k;
                    break;
                };
            };
            if(is_null($key)){
                $cart[] = $data;
            }else{
                $cart[$key]['num'] = $cart[$key]['num'] + $data['num'];
            };
            $_SESSION['cart'] = $cart;
	        }else{
	            $_SESSION['cart'][] = $data;
	        };
	        $cart = $_SESSION['cart'];
		};
		return $cart;
	}


	//获得购物车数据
	public function get_data($cart){
		$data = array();
		foreach($cart as $k => $v){
            $temp = M('Shop_goods sg')->where(array('sg.gid'=>$v['gid'],'sg.is_del'=>0,'sgl.glid'=>$v['glid'],'sgl.is_del'=>0))->join('__SHOP_GOODS_LIST__ sgl ON sg.gid = sgl.goods_id')->find();//查询商品和货品数据
            if(!is_null($temp)){
                $spec = explode('|',$temp['combine']);//查询规格名称
                $specs = '';
                foreach($spec as $kk => $vv){
                    $gavalue = M('Shop_goods_attr')->where(array('gaid'=>$vv,'is_del'=>0))->getField('gavalue');
                    if(!is_null($gavalue)){
                        $specs .= '|'.$gavalue;
                    };
                };
                $temp['specs'] = ltrim($specs,'|');
                $temp['num'] = $v['num'];
                $temp['total'] = $temp['num'] * $temp['price'];
                if(isset($_SESSION['member_id'])){
                    $temp['cart_id'] = $v['cart_id'];
                }else{
                    $temp['cart_id'] = $k;
                };
                $data['goods'][] = $temp;
            };
        };
        $data['total'] = 0;
        foreach($data['goods'] as $k => $v){
            $data['total'] = $data['total'] + $v['total'];
        };
        return $data;
	}
	/**
	 * 立即购买获取数据
	 */
	public function noce_data($cart){
		$data = array();
        $temp = M('Shop_goods sg')->where(array('sg.gid'=>$cart['gid'],'sg.is_del'=>0,'sgl.glid'=>$cart['glid'],'sgl.is_del'=>0))->join('__SHOP_GOODS_LIST__ sgl ON sg.gid = sgl.goods_id')->find();//查询商品和货品数据
        if(!is_null($temp)){
            $spec = explode('|',$temp['combine']);//查询规格名称
            $specs = '';
            foreach($spec as $kk => $vv){
                $gavalue = M('Shop_goods_attr')->where(array('gaid'=>$vv,'is_del'=>0))->getField('gavalue');
                if(!is_null($gavalue)){
                    $specs .= '|'.$gavalue;
                };
            };
            $temp['specs'] = ltrim($specs,'|');
            $temp['num'] = $cart['num'];
            $temp['total'] = $temp['num'] * $temp['price'];
            $data['goods'][] = $temp;
        };
        $data['total'] = 0;
        foreach($data['goods'] as $k => $v){
            $data['total'] = $data['total'] + $v['total'];
        };
        return $data;
	}

}
