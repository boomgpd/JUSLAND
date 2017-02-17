<?php
namespace Shop\Controller;
use Common\Model\Shop_cartModel;
use Common\Model\Shop_addressModel;
use Common\Model\Shop_orderModel;

//商城前台订单控制器
class OrderController extends CommonController {
	
	private $cart;
    private $address;
    private $order;
	
	public function __construct(){
        parent::__construct();
        $this->cart = new Shop_cartModel;
        $this->address = new Shop_addressModel;
		$this->order = new Shop_orderModel;
	}

    //订单首页
    public function index(){
        if(IS_AJAX){
            if(!isset($_SESSION['member_id'])){
                $this->ajaxReturn(array('type'=>'error','msg'=>'你还没有登录，要登录吗？','url'=>"/Main/login/login.html?backname=http://wp.c-wia.com/index.php/Shop/Goods/cart.html?"));die;
            }else{
                $this->ajaxReturn(array('type'=>'success'));die;
            };
        }
        if(IS_POST){
	        $ids = I('post.ids');
	        $cart = $this->cart->where(array('cart_id'=>array('IN',implode(',',$ids)),'member_id'=>$_SESSION['member_id'],'is_del'=>0))->select();
	        $data = $this->cart->get_data($cart);
        }else if(IS_GET){
        	$post = I('get.');
			$list = M('Shop_goods_list')->where(array('combine'=>$post['sids'],'goods_id'=>$post['gid'],'is_del'=>0))->find();
	        $success = array('type'=>'success','cate'=>0,'num'=>0,'total'=>0);
	        if(is_null($list)){
	        	alert('没有对应规格的货品',U("Goods/details",array('id'=>$post['gid'])));die;
	        };
	        $product = M('Shop_goods')->where(array('gid'=>$post['gid'],'is_del'=>0))->find();
	        if(is_null($product)){
	        	alert('没有对应的商品或商品已下线',U("Goods/details",array('id'=>$post['gid'])));die;
	        };
			$cart['gid'] = $product['gid'];
			$cart['glid'] = $list['glid'];
			$cart['num'] = $post['num'];
	        $data = $this->cart->noce_data($cart);
			$get = $cart;
			$this->assign('get',$get);
			$type_good = 1;
			$this->assign('type_good',$type_good);
        }
        //省份数据
        $province = M('Area')->where(array('pid'=>0))->select();
        $this->assign('province',$province);
        //收货地址数据
        $address = $this->address->get_data($address);
        $this->assign('address',$address);
        $this->assign('data',$data);
        $this->display();
    }

    //添加订单
    public function add(){
        if(!IS_POST) return FALSE;
        $post = I('post.');
		if($post['type_good']){
			$re = $this->order->store_get($post);
		}else{
	        $re = $this->order->store($post);
		}
        if(!$re){
            alert($this->order->getError());die;
        };
        redirect(U('Pay/cart',array('id'=>$re)));
    }

    //获得收货地址数据
    public function get_address(){
        if(!IS_AJAX) return FALSE;
        $error = array('type'=>'error','msg'=>'');
        $success = array('type'=>'success','data'=>'');
        $id = I('post.key');
        $re = $this->address->where(array('id'=>$id,'member_id'=>$_SESSION['member_id'],'is_del'=>0))->find();
        if(is_null($re)){
            $error['msg'] = '数据异常';
            $this->ajaxReturn($error);die;
        };
        //获得收货地址数据
        $data = array();
        $data['province_all'] = M('Area')->field('area_id,aname')->where(array('pid'=>0))->select();//所有城市数据
        $data['city_all'] = M('Area')->field('area_id,aname')->where(array('pid'=>$re['province']))->select();//对应城市的数据
        $data['linkman'] = $re['linkman'];
        $data['phone'] = $re['phone'];
        $data['province'] = $re['province'];
        $data['city'] = $re['city'];
        $data['coding'] = $re['coding'];
        $data['details'] = $re['details'];
        $data['default'] = $re['default'];
        $data['id'] = $re['id'];
        $success['data'] = $data;
        $this->ajaxReturn($success);die;
    }

    //收货地址添加或编辑
    public function change_address(){
        if(!IS_AJAX) return FALSE;
        $error = array('type'=>'error','msg'=>'');
        $success = array('type'=>'success','data'=>'');
        $post = I('post.');
        if($post['action'] == 'add'){
            $re = $this->address->store($post['data']);
        }else if($post['action'] == 'edit'){
            $re = $this->address->edit($post['data']);
        }else{
            $error['msg'] = '非法请求';
            $this->ajaxReturn($error);die;
        };
        if(!$re){
            $error['msg'] = $this->address->getError();
            $this->ajaxReturn($error);
        }else{
            $success['data'] = $re;
            $this->ajaxReturn($success);die;
        }
    }

    /**
     * 创建获取对应省份的市区
     */
    public function step_area(){
        if(!IS_AJAX) return FALSE;
        $pid = I('post.pid');
        if($pid == 0){
            $this->ajaxReturn(FALSE);exit;
        }
        $data = M('area')->where(array('pid'=>$pid))->select();
        if($data){
            $this->ajaxReturn($data);exit;
        }else{
            $this->ajaxReturn(FALSE);exit;
        }   
    }
}