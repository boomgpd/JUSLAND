<?php
namespace Admin\Controller;
use Admin\Model\EmsModel;
use Common\Model\Shop_orderModel;
use Admin\Model\Shop_taobao_orderModel;
header('Content-Type: text/html; charset=utf-8');
//订单控制器
class ShopOrderController extends CommonController {
	
    private $ems;
    private $order;
	private $taobao_order;
	
	public function __construct(){
		parent::__construct();
        $this->ems = new EmsModel;
        $this->order = new Shop_orderModel;
		$this->taobao_order = new Shop_taobao_orderModel;
	}

    //订单首页
    public function index(){
        $condition = array('is_del'=>0,'tb_status'=>$_GET['order_status']);//条件
        if(IS_POST){//搜索
            $keyword = I('post.keyword');
            if(!empty($keyword)){
                $condition['number|consignee|address'] = array('LIKE','%'.$keyword.'%');
            };
        };
        $data = $this->order->where($condition)->order('addtime ASC')->select();//订单数据
        $this->assign('data',$data);
        $this->display();
    }

    //查看订单详情
    public function info($id=NULL){
        if(is_null($id)) return FALSE;
        $order = $this->order->where(array('oid'=>$id,'is_del'=>0))->find();//查询是否有该订单信息
        if(is_null($order)) return FALSE;
        $data = M('shop_orders_list ol')->where(array('ol.orders_oid'=>$id))->join('__SHOP_GOODS__ sg ON ol.goods_gid = sg.gid')->join('__SHOP_GOODS_LIST__ sgl ON ol.goods_list_glid = sgl.glid')->select();
        
        //拼接规格
        foreach($data as $k => &$v){
            $combine = implode(',',explode('|',$v['combine']));
            $v['spec'] = M('shop_goods_attr')->where(array('gaid'=>array('IN',$combine)))->getField('gavalue');
			if($v['tb_id'] != 0){
				$tb_order = $this->taobao_order->where(array('tb_order_id'=>$v['tb_id']))->find();
				$v = array_merge($tb_order,$v);
			}
            $data[$k] = $v;
        };
        $this->assign('data',$data);
        $this->assign('order',$order);
        $this->display();
    }
	
	/**
	 * 创建添加淘宝订单号和邮费方法
	 */
	public function add_taobao(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.');
		$re = $this->taobao_order->store($data);  
		$arr = array();
		if(!$re){
			$arr['type'] = 'false';
			$arr['content'] = $this->taobao_order->getError();
		}else{
			$arr['type'] = 'true';
			$arr['content'] = '添加成功';
		}
		$this->ajaxReturn($arr);exit;
	}
	
	/**
	 * 创建添加快递单号方法
	 */
	public function ems_info($id=NULL){
		if(is_null($id)) return FALSE;
        $order = $this->order->where(array('oid'=>$id,'is_del'=>0))->find();//查询是否有该订单信息
        $this->assign('order',$order);
        if(is_null($order)) return FALSE;
        $data = M('shop_taobao_order a')->join('__SHOP_ORDERS_LIST__  b ON a.tb_order_id = b.tb_id')->group('tb_order_id')->where(array('b.orders_oid'=> $id))->select();
        $this->assign('data',$data);
        //拼接规格
		$ems = M('shop_ems_type')->where(array('is_del'=>0))->select();
		$this->assign('ems',$ems);
        $this->display();
	}
	/**
	 * 创建淘宝订单编辑方法
	 */
	public function tb_ems_add(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.');
		$re = $this->taobao_order->ems_add($data);
		$arr = array();
		if(!$re){
			$arr['type'] = 'false';
			$arr['content'] = $this->taobao_order->getError();
		}else{
			$arr['type'] = 'true';
			$arr['content'] = '添加成功';
			$arr['ems_type'] = M('shop_ems_type')->where(array('is_del'=>0,'id'=>$data['ems_type_id']))->getField('title');
		}
		$this->ajaxReturn($arr);	
	}
	
    //订单编辑方法
    public function edit($id=NULL){
        if(is_null($id)) return FALSE;
        $data = M('shop_orders_list')->where(array('olid'=>$id,'status'=>1))->find();//订单列表数据
        $order = $this->order->where(array('oid'=>$data['orders_oid'],'is_del'=>0))->find();//获得对应的订单
        if(is_null($data) || is_null($order)) return FALSE;
        if(IS_POST){
            $post = I('post.');
            $arr = array('tb_number'=>$post['tb_number']);
            if(!empty($post['ems_id'])){
                $arr['ems_type_id'] = $post['ems_id'];
                $arr['ems_number'] = $post['ems_number'];
            };
            $re = M('shop_orders_list')->where(array('olid'=>$id))->save($arr);
            if(!$re){
                alert('保存失败');die;
            };
            alert('保存成功',U('info',array('id'=>$order['oid'])));die;
        };
        $ems = M('shop_ems_type')->where(array('is_del'=>0))->select();//获得快递数据
        $this->assign('ems',$ems);
        $this->assign('data',$data);
        $this->display();
    }

    //跳转路径方法
    public function go($id=NULL){
        if(is_null($id)) return FALSE;
        $order_list = M('shop_orders_list')->where(array('olid'=>$id))->find();//获得订单列表信息
        $order = $this->order->where(array('oid'=>$order_list['orders_oid'],'is_del'=>0))->find();//获得订单信息
        if(is_null($order)) return FALSE;
        $goods = M('shop_goods')->where(array('gid'=>$order_list['goods_gid']))->find();//获得商品数据
        if(is_null($goods)) return FALSE;
        $data = explode('|',$goods['url']);//切割跳转路径
        $this->assign('data',$data);
        $this->assign('order',$order);
        $this->display();
    }

    //发货
    public function deliver(){
        if(!IS_AJAX || !IS_POST) return FALSE;
        $post = I('post.');
        $re = M('shop_orders_list')->where(array('ems_number'=>$post['val'],'status'=>1))->save(array('status'=>2));
        if(!$re){
            $this->ajaxReturn(array('type'=>0));die;
        }else{
            $this->ajaxReturn(array('type'=>1));die;
        };
    }

    //切换订单状态
    public function cut(){
        if(!IS_AJAX || !IS_POST) return FALSE;
        $id = I('post.id'); 
        $order = $this->order->where(array('oid'=>$id,'is_del'=>0))->find();//查询订单信息
        if(is_null($order) || $order['status'] == 0 || $order['status'] == 4 || empty($order['tb_number'])){//没有该数据或还未付款或已完成
            $this->ajaxReturn(array('type'=>0));
        };
        $order['status'] = ''.($order['status']+1);
        $re = $this->order->save($order);
        if(!$re){
            $this->ajaxReturn(array('type'=>0));die;
        }else{
            $this->ajaxReturn(array('type'=>1));die;
        };
    }

    //快递类型首页
    public function ems_index(){
        $data = $this->ems->where(array('is_del'=>0))->select();
        $this->assign('data',$data);
        $this->display();
    }

    //添加快递类型
    public function ems_add(){
        if(IS_POST){
            $post = I('post.');
            $re = $this->ems->store($post);
            if(!$re){
                alert($this->ems->getError());die;
            };
            alert('添加成功',U('ems_index'));die;
        };
        $this->display();
    }

    //添加快递类型
    public function ems_edit($id=NULL){
        if(empty($id)) return FALSE;
        if(IS_POST){
            $post = I('post.');
            $re = $this->ems->edit($post);
            if(!$re){
                alert($this->ems->getError());die;
            };
            alert('编辑成功',U('ems_index'));die;
        };
        $data = $this->ems->where(array('id'=>$id,'is_del'=>0))->find();
        $this->assign('data',$data);
        $this->display();
    }

    //删除快递类型
    public function ems_del($id=NULL){
        if(empty($id)) return FALSE;
        $this->ems->where(array('id'=>$id))->save(array('is_del'=>0));
        alert('删除成功',U('ems_index'));
    }
	
}