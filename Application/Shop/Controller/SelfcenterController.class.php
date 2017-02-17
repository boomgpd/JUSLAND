<?php
namespace Shop\Controller;
use Common\Model\Shop_addressModel;

//商城前台订单控制器
class SelfcenterController extends CommonController{
	private $address;
	
	public function __construct(){
        parent::__construct();
		$this->address = new Shop_addressModel;
		$this->left();
	}
	
	/**
	 * 创建显示全部订单方法
	 */
	public function index(){
		$get = I('get.');
		$condition = array();
		if(isset($get['status'])){
			$condition['status'] = $get['status'];
		}else if(isset($get['is_del'])){
			$condition['is_del'] = 1;
		}
		$limit = 5;//每页显示的数量；
		if($_GET['page']){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}//默认￥page为1
		$order = M('shop_orders')->order('addtime DESC')->where($condition)->where(array('member_id'=>$_SESSION['member_id']))->limit($limit)->page($page)->select();
		$arr = M('shop_orders')->order('addtime DESC')->where($condition)->where(array('member_id'=>$_SESSION['member_id']))->count();
		$num = ceil($arr/$limit);//输出总共有几页，向上取整。
		if($page == 1){
			$page_left = 0;
		}else if($page < 4){
			$page_left = $page - 1;
		}else if($page > 3){
			$page_left = 3;
		}//判断当前页面左边显示几个
		if($num == 1 || $num == 0){
			$page_type = FALSE;
		}else{
			$page_type = TRUE;
		}//判断是否显示分页样式
		$page_right =  $num-$page;
		if($page_right > 3) $page_right = 3;//计算当前页面右边显示几个
		$page_array =array('page_left'=>$page_left,'num'=>$num,'page_right'=>$page_right,'page_type'=>$page_type,'page'=>$page); 
		$data  = $this->getOrders($order);
		$this->assign('data',$data);
		$this->assign('page_array',$page_array);
		$this->display();
	}
	
	/**
	 * 创建获取订单信息方法
	 */
	public function getOrders($data){
		foreach($data as $k=>&$v){
			/*获取订单列表信息*/
			$v['goods_list'] = M('shop_orders_list')->where(array('is_del'=>0,'orders_oid'=>$v['oid']))->select();
			foreach($v['goods_list'] as $kk=>&$vv){
				/*获取对应商品信息*/
				$vv['goods_data'] = M('shop_goods')->field('gid,pic,gname')->where(array('is_del'=>0,'gid'=>$vv['goods_gid']))->find();
//				$vv['goods_status'] = 
//				echo $vv['status'];
				/*订单状态中文名*/
				switch ($v['status']) {
					case 0:
						$vv['status_name'] = '未付款';
						break;
					case 1:
						$vv['status_name'] = '待发货';
						break;
					case 2:
						$vv['status_name'] = '已发货';
						break;
					case 3:
						$vv['status_name'] = '已签收';
						break;
					case 4:
						$vv['status_name'] = '退款中';
						break;
					case 5:
						$vv['status_name'] = '已删除';
				}
				
				
				/*获取订单列表对应的货品*/
				$vv['goods_list_product'] = M('shop_goods_list')->where(array('is_del'=>0,'glid'=>$vv['goods_list_glid']))->find(); 
				$vv['goods_list_product']['combine'] = explode("|", $vv['goods_list_product']['combine']);
				foreach($vv['goods_list_product']['combine'] as $kkk=>&$vvv){
					/*获取货品组合规格名称*/
					$temp = array();
					$temp['gavalue'] = M('shop_goods_attr')->where(array('is_del'=>0,'gaid'=>$vvv))->getField('gavalue');
					$temp['spec_id'] = M('shop_goods_attr')->where(array('is_del'=>0,'gaid'=>$vvv))->getField('spec_id');
					$temp['spec_name'] = M('shop_spec_name')->where(array('is_del'=>0,'s_id'=>$temp['spec_id']))->getField('spec_name');
					unset($temp['spec_id']);
					$vvv = $temp;
				}
			}
		}
//		dump($data);die;
		return $data;
	}

	/**
	 * 创建显示收货地址方法
	 */
	public function order_address_show(){
		if(IS_POST){
			$data = I('post.');
			$data['member_id'] = $_SESSION['member_id'];
			$re = $this->address->ad_store($data);
			if($re){
				alert('添加成功',U('order_address_show'));
			}else{
				alert($this->address->getError());
			}
		}
		$data = M('shop_address')->where(array('is_del'=>0,'member_id'=>$_SESSION['member_id']))->order('addtime DESC')->select();
		foreach($data as $k=>&$v){
			$v['address'] = '';
			$v['address'] .= M('area')->where(array('is_del'=>0,'area_id'=>$v['province'],'pid'=>0,'type'=>1))->getField('aname');	
			$v['address'] .= M('area')->where(array('is_del'=>0,'area_id'=>$v['city'],'type'=>2))->getField('aname');	
			$v['address'] .= $v['details'];
			$v['false_phone'] = substr_replace($v['phone'],'****',3,4); 
		}
		
		$area_one = M('area')->where(array('is_del'=>0,'pid'=>0,'type'=>1))->select();
		$this->assign('area_one',$area_one);
		$this->assign('data',$data);
		$this->display();
	}

	/*创建修改默认收货地址方法*/
	public function change_defalut(){
		if(!IS_AJAX) return FALSE;
		$re = $this->address->where(array('member_id'=>$_SESSION['member_id']))->setField(array('default'=>0));
		$condition = array(
			'id'	=>	I('get.id'),
			'member_id'=>	$_SESSION['member_id']	
		);
		$re = $this->address->where($condition)->save(array('default'=>1));
		$this->ajaxReturn($re);exit;
	}
	
	/*获取单个收货地址方法*/
	public function getOneAddress(){
		if(!IS_AJAX) return FALSE;
		$data = $this->address->where(array('id'=>I('post.id'),'member_id'=>$_SESSION['member_id'],'is_del'=>0))->find();
		$area_one = M('area')->where(array('is_del'=>0,'pid'=>0,'type'=>1))->select();
		$area_two = M('area')->where(array('is_del'=>0,'pid'=>$data['province'],'type'=>2))->select();
		$arr = array(
			'address'	=>	$data,
			'area_one'	=>	$area_one,
			'area_two'	=>	$area_two,
		);
		$this->ajaxReturn($arr);exit;
	}
	
	/*创建编辑收货地址方法*/
	public function address_edit(){
		if(!IS_POST) return FALSE;
		$data = I('post.');
		$re = $this->address->ad_edit($data);
		if(!$re){
			alert('编辑失败');
		}else{
			alert('编辑成功',U('order_address_show'));
		}
	} 
	
	/*创建删除对应收货地址方法*/
	public function address_del(){
		if(!IS_AJAX) return FALSE;
		$condition = array('id'=>I('get.id'));
		$re = $this->address->where($condition)->save(array('is_del'=>1));
		$this->ajaxReturn($re);exit;
	}
	
	/**
	 * 创建单个订单闲情查看方法
	 */
	public function details(){
		$get = I('get.');
		$order = M('shop_orders')->where(array('member_id'=>$_SESSION['member_id'],'oid'=>$get['oid']))->find();
		$order = array($order);
		$data = current($this->getOrders($order));
		$temp = array();
		foreach($data['goods_list'] as $k=>$v){
			if($v['olid'] == $get['olid']){
				$temp = $v;
				break;
			}
		}
		$data['goods_list'] = $temp;
		$data['goods_list']['tb'] =  M('shop_taobao_order')->where(array('is_del'=>0,'tb_order_id'=>$data['goods_list']['tb_id']))->find();
		$data['goods_list']['tb']['ems_type'] = M('shop_ems_type')->where(array('id'=>$data['goods_list']['tb']['ems_type_id']))->find();
		$data['goods_list']['ems_msg'] = json_decode(getOrderTracesByJson($data['goods_list']['tb']['ems_type']['name'],$data['goods_list']['tb']['ems_number']),TRUE);
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 手动确认收货的方法
	 */
	public function receipt(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.');
		M()->startTrans();
		$orders_list = M('shop_orders_list')->where(array('olid'=>$data['olid']))->save(array('status'=>3));
		if(!$orders_list){
			M()->rollback();
			$eroor =array(
					'type'=>'404',
					'content'=>'收货失败'
				);
			$this->ajaxReturn($eroor);exit;
		}
		$is_orders_list = M('shop_orders_list')->where(array('olid'=>array('not in',$data['olid']),'status'=>array('in','1,2'),'orders_oid'=>$data['oid']))->select();
		$temp = '';
		if(!$is_orders_list){
			$temp = 1;
			$orders = M('shop_orders')->where(array('oid'=>$data['oid']))->save(array('status'=>3,'tb_status'=>3));
			if(!$orders_list){
				M()->rollback();
				$eroor =array(
					'type'=>'404',
					'content'=>'处理订单失败'
				);
				$this->ajaxReturn($eroor);exit;
			}
		}
		M()->commit();
		$success =array(
			'type'=>'100',
			'content'=>'收货成功'
		);
		if($temp){
			$success['type'] = '101';
		}
		$this->ajaxReturn($success);exit;
		
	}
	public function left(){
		$left_data = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
		$this->assign('left_data',$left_data);
//		dump($data);die;
	}
	
}
	