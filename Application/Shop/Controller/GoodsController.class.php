<?php
namespace Shop\Controller;
use Common\Model\Shop_cateModel;
use Common\Model\Shop_fcateModel;
use Common\Model\ShopGoodsModel;
use Common\Model\Shop_cartModel;
use Common\Model\Activity_labelModel;
header("Content-type: text/html; charset=utf-8"); 
//商城前台商品控制器
class GoodsController extends CommonController {
	
	private $cate;
	private $fcate;
    private $goods;
    private $cart;
	private $label;
	private $not_online_courom =array();
	
	public function __construct(){
		$this->cate = new Shop_cateModel;
		$this->fcate = new Shop_fcateModel;
        $this->goods = new ShopGoodsModel;
        $this->cart = new Shop_cartModel;
		$this->label = new Activity_labelModel;
		parent::__construct();
	}

    //列表页
     public function lists($cid=NULL){
        if(empty($cid)) return FALSE;
        $cate = $this->cate->where(array('cid'=>$cid,'is_del'=>0))->find();
        if(is_null($cate)) return FALSE;
        $level = $this->cate->get_bread($cid);
        $level = count($level);
        if($level != 2) return FALSE;
        $cates = $this->cate->where(array('pid'=>$cate['pid'],'is_del'=>0))->select();//相关分类数据
        //获得筛选条件
        $gids = M('shop_goods')->where(array('category_cid'=>$cid,'is_del'=>0))->getField('gid',TRUE);
		$gids1 = explode('|', $cate['spec_id']);
//		dump($cate);die;
		foreach ($gids1 as $key => $value) {
			$spec[] = M('shop_spec_name')->where(array('s_id'=>$value))->find();
		}
		foreach ($spec as $key => &$value) {
			$value['attr'] = M('Shop_goods_attr')->where(array('goods_gid'=>array('IN',implode(',',$gids)),'spec_id'=>$value['s_id'],'is_del'=>0))->group('gavalue')->select();
		}
//      if(empty($gids)) $gids = array();
//      $spec = M('Shop_goods_attr')->where(array('goods_gid'=>array('IN',implode(',',$gids)),'spec_id'=>array('IN',implode(',',$gids1)),'is_del'=>0))->group('gavalue')->select();//去重条件
//      foreach ($spec as $key => &$value) {
//      	$value['spec_name'] = M('shop_spec_name')->where(array('s_id'=>$value['spec_id']))->getField('spec_name');
//      }
		//      $temp = array();
//		$spec = array();
//      foreach($goods_attr as $k => $v){//合并相同的条件
//          $temp[$v['gavalue']][] = $v;
//      };
//      $spec = array();//筛选条件
//      foreach($temp as $k => $v){//查找到对应的筛选条件名称
//          $name = M('Shop_type_attr')->where(array('gavalue'=>$k,'is_del'=>0))->getField('gavalue');
//          if(!is_null($name)){
//              $spec[] = array('name'=>$name,'value'=>$v);
//          };
//      };
        $this->assign('spec',$spec);//分配筛选条件
        $num = count($spec);//获得筛选的个数
        $param = isset($_GET['param'])?explode('_',$_GET['param']):array_fill(0,$num,0);//判断是否有筛选
        $this->assign('param',$param);
//      dump($param);die;
        $screen = array();//存储被筛选的商品id
        foreach($param as $k => $v){
            if($v != 0){
                $gavalue = M('Shop_goods_attr')->where(array('gaid'=>$v,'is_del'=>0))->getField('gavalue');
                $screen[] = M('Shop_goods_attr')->where(array('gavalue'=>$gavalue,'is_del'=>0))->getField('goods_gid',true);
            };
        };
        if(!empty($screen)){//有筛选条件
            $temp = $screen[0];
            foreach($screen as $k => $v){
                $temp = array_intersect($temp,$v);
            };
            $gids = array_intersect($temp,$gids);
        };
        //商品数据
        $gids = implode(',',$gids);
        
        /**
         * 创建排序筛选方法
         */
        $order = array();
         
        if(!$_GET['click'] && !$_GET['sales_num'] && !$_GET['order_price']){
            $order['click'] = 'click DESC';
            $order['sales_num'] = 'sales_num DESC';
            $order['order_price'] = 'shopprice ASC';
            $order = implode(",", $order);
        }else if($_GET['click']){
            $order = 'click DESC';
        }else if($_GET['sales_num']){
            $order = 'sales_num DESC';
        }else if($_GET['order_price']){
            if($_GET['order_price'] == 0){
                $order = 'shopprice ASC';
            }else{
                $order = 'shopprice DESC';
            }
            
        }
        
        $get = $_GET;

//      条件筛选
        if(IS_POST){
            $post_data = I('get.');
            $post_data = array_merge($post_data,I('post.'));
        }else{
            $post_data = I('get.');
        }
        $condition = array();
        
        if(isset($post_data['goods_name'])){
            $condition['gname|des'] = array('LIKE',"%".$post_data['goods_name']."%");//从商品名称和商品描述里查找
            $get['goods_name'] = $post_data['goods_name'];
        }
        
        if($post_data['small_price'] && !$post_data['big_price']){
            $condition['shopprice'] = array('gt',$post_data['small_price']);//只有最小值
            $get['small_price'] = $post_data['small_price'];
        }else if(!$post_data['small_price'] && $post_data['big_price']){
            $condition['shopprice'] = array('lt',$post_data['big_price']);//只有最大值
            $get['big_price'] = $post_data['big_price'];
        }else if($post_data['small_price'] && $post_data['big_price']){
            $condition['shopprice'] = array('between',array($post_data['small_price'],$post_data['big_price']));//只有最大值
            $get['big_price'] = $post_data['big_price'];
            $get['small_price'] = $post_data['small_price'];
        }
        
        $this->assign('get',$get);
        
//      dump($condition);die;
        /**
         * 分页开始
         */
        $page_number = I('get.page');
        if(!$page_number){
            $page_number = 1;
        }
        $goods_data = $this->goods->where(array('is_del'=>0,'gid'=>array('IN',$gids)))->where($condition)->limit(C('__SHOP_LIST_NUM__'))->page($page_number)->order($order)->select();
        $page_num  = ceil($this->goods->where(array('is_del'=>0,'gid'=>array('IN',$gids)))->where($condition)->limit()->page()->count()/C('__SHOP_LIST_NUM__'));
        if($page_number == 1){
            $page_left = 0;
        }else if($page_number == 2){
            $page_left = 1;
        }else if($page_number == 3){
            $page_left = 2;
        }else if($page_number > 3){
            $page_left = 3;
        }
        
        if($page_num == 1 || $page_num == 0){
            $page_type = FALSE;
        }else{
            $page_type = TRUE;
        }
        
        $page_right = $page_num - $page_number;
        if($page_right > 3) $page_right = 3;
        $page = array('num'=>$page_num,'page_number'=>$page_number,'page_left'=>$page_left,'page_right'=>$page_right,'page_type'=>$page_type);
        $this->assign('page',$page);
//      分页结束
//      dump($page);die;
        $top_cate = $this->cate->where(array('pid'=>0,'is_del'=>0))->select();
        $this->assign('goods_data',$goods_data);
        $this->assign('top_cate',$top_cate);
        $this->assign('cate',$cate);
        $this->assign('cates',$cates);
        $this->display();
    }

    //搜索列表页
    public function search($cid=NULL){
        $q = I('get.q');
        $condition = empty($q)?array('is_del'=>0):array('is_del'=>0,'gname|des'=>array('LIKE','%'.$q.'%'));
        $gids = $this->goods->where($condition)->getField('gid',true);//获得商品主键

        //条件筛选
        if(IS_POST){
            $post_data = I('get.');
            $post_data = array_merge($post_data,I('post.'));
        }else{
            $post_data = I('get.');
        }
        $condition = array();
        
        if(isset($post_data['goods_name'])){
            $condition['gname|des'] = array('LIKE',"%".$post_data['goods_name']."%");//从商品名称和商品描述里查找
            $get['goods_name'] = $post_data['goods_name'];
        }
        
        if($post_data['small_price'] && !$post_data['big_price']){
            $condition['shopprice'] = array('gt',$post_data['small_price']);//只有最小值
            $get['small_price'] = $post_data['small_price'];
        }else if(!$post_data['small_price'] && $post_data['big_price']){
            $condition['shopprice'] = array('lt',$post_data['big_price']);//只有最大值
            $get['big_price'] = $post_data['big_price'];
        }else if($post_data['small_price'] && $post_data['big_price']){
            $condition['shopprice'] = array('between',array($post_data['small_price'],$post_data['big_price']));//只有最大值
            $get['big_price'] = $post_data['big_price'];
            $get['small_price'] = $post_data['small_price'];
        }
        
        $this->assign('get',$get);
        
        /**
         * 分页开始
         */
        $page_number = I('get.page');
        if(!$page_number){
            $page_number = 1;
        }
        $goods_data = $this->goods->where(array('is_del'=>0,'gid'=>array('IN',$gids)))->where($condition)->limit(C('__SHOP_LIST_NUM__'))->page($page_number)->order($order)->select();
        $page_num  = ceil($this->goods->where(array('is_del'=>0,'gid'=>array('IN',$gids)))->where($condition)->limit()->page()->count()/C('__SHOP_LIST_NUM__'));
        if($page_number == 1){
            $page_left = 0;
        }else if($page_number == 2){
            $page_left = 1;
        }else if($page_number == 3){
            $page_left = 2;
        }else if($page_number > 3){
            $page_left = 3;
        }
        
        if($page_num == 1 || $page_num == 0){
            $page_type = FALSE;
        }else{
            $page_type = TRUE;
        }
        
        $page_right = $page_num - $page_number;
        if($page_right > 3) $page_right = 3;
        $page = array('num'=>$page_num,'page_number'=>$page_number,'page_left'=>$page_left,'page_right'=>$page_right,'page_type'=>$page_type);
        $this->assign('page',$page);

        $cids = array();
        foreach($goods_data as $k => $v){//获得所有分类id
            $cids[] = $v['category_cid'];
        };
        $cids = array_unique($cids);//去重
        $cates = $this->cate->field('cname,cid')->where(array('cid'=>array('IN',implode(',',$cids)),'is_del'=>0))->select();
        $top_cate = $this->cate->where(array('pid'=>0,'is_del'=>0))->select();//顶级分类
        $this->assign('goods_data',$goods_data);
        $this->assign('cates',$cates);
        $this->assign('top_cate',$top_cate);
        $this->display();
    }
    

    //详情页
    public function details($id=NULL){
        if(empty($id)){
            alert('页面丢失');die;
        };
        $data = M('Shop_goods sg')->where(array('sg.gid'=>$id,'sg.is_del'=>0,'sgd.is_del'=>0))->join(' __SHOP_GOODS_DETAILS__ sgd ON sg.gid = sgd.goods_gid')->find();
        if(is_null($data)){
            alert('页面丢失');die;
        };
        M('Shop_goods')->where(array('gid'=>$id,'is_del'=>0))->setInc('click');
        $data['medium'] = explode('|',$data['medium']);
        $data['big'] = explode('|',$data['big']);
        $data['details'] = htmlspecialchars_decode($data['details']);
        //获得规格
        $goods_attr = M('Shop_goods')->where(array('gid'=>$data['gid'],'is_del'=>0))->getfield('spec_id');
		$temp = array();
        foreach(explode('|',$goods_attr) as $k => $v){
        	$temp[] = M('shop_spec_name')->where(array('s_id'=>$v))->find();
        };
		$goods_list = M('shop_goods_list')->where(array('is_del'=>0,'goods_id'=>$id))->getField('combine',TRUE);
		$goods_list = implode("|", $goods_list);
		$goods_list = explode("|", $goods_list);
		$goods_list = array_unique($goods_list);
		foreach ($goods_list as $k => $v) {
			$spec[] = M('shop_goods_attr')->where(array('gaid'=>$v))->find();
		}
		$specs = array();
		foreach ($temp as $key => $value) {
			$attr = array();
			$attr = $value;
			foreach ($spec as $k => $v) {
				if($value['s_id'] == $v['spec_id']){
					$attr['attr'][] = $v;
				}
			}
			$specs[] = $attr;
		}
        $specss = '';
        foreach($specs as $k => $v){
            $specss .= '、'.$v['spec_name'];
        };
        $gids = $this->goods->get_ids($id,true);
        $linked = $this->goods->where(array('gid'=>array('IN',implode(',',$gids)),'is_del'=>0))->select();
        $this->assign('linked',$linked);
        $specss = ltrim($specss,'、');
        $this->assign('specss',$specss);
        $this->assign('specs',$specs);
        $this->assign('data',$data);

        $label = $this->label->where(array('is_del'=>0))->select();
        $arr = array();$att = array();
        foreach ($label as $key => $value) {
            if($value['type'] == 1 ){
                $arr[] = $value;
            }else if($value['type'] == 2){
                $att[] = $value;
            }
        }
        $temp = array($arr,$att);
        $this->assign('temp',$temp);
        $this->display();
    }

    //异步获得货品数据
    public function get_data(){
        if(!IS_AJAX) return FALSE;
        $post = I('post.');
        $data = M('Shop_goods_list')->where(array('combine'=>$post['sids'],'goods_id'=>$post['gid'],'is_del'=>0))->find();
        $re = array();
        if(is_null($data)){
            $re['type'] = 'error';
            $re['msg'] = '没有对应规格的货品';
        }else{
            $re['type'] = 'success';
            $re['market'] = $data['market'];
            $re['price'] = $data['price'];
            $re['total'] = sprintf("%01.2f",$data['price'] * $post['num']);
        };
        $this->ajaxReturn($re);
    }

    //异步添加购物车数据
    public function add_cart(){
        if(!IS_AJAX) return FALSE;
        $post = I('post.');
        $list = M('Shop_goods_list')->where(array('combine'=>$post['sids'],'goods_id'=>$post['gid'],'is_del'=>0))->find();
        $error = array('type'=>'error','msg'=>'');
        $success = array('type'=>'success','cate'=>0,'num'=>0,'total'=>0);
        if(is_null($list)){
            $error['msg'] = '没有对应规格的货品';
            $this->ajaxReturn($error);die;
        };
        $product = M('Shop_goods')->where(array('gid'=>$post['gid'],'is_del'=>0))->find();
        if(is_null($product)){
            $error['msg'] = '没有对应的商品或商品已下线';
            $this->ajaxReturn($error);die;
        };
        $data['gid'] = $post['gid'];//商品id
        $data['glid'] = $list['glid'];//货品id
        $data['addtime'] = time();//添加时间
        $data['num'] = $post['num'];//数量
        //添加购物车
        $cart = $this->cart->store($data); 
        $success['cate'] = count($cart);
        foreach($cart as $k => $v){
            $success['num'] = $success['num'] + $v['num'];
            $price = M('Shop_goods_list')->where(array('glid'=>$v['glid'],'is_del'=>0))->getField('price');
            if(!is_null($price)){
                $success['total'] = $success['total'] + ($price * $v['num']);
            };
        };
        $this->ajaxReturn($success);die;
    }
	//立即购买
	public function noce(){
		$post = I('get.');
		$list = M('Shop_goods_list')->where(array('combine'=>$post['sids'],'goods_id'=>$post['gid'],'is_del'=>0))->find();
        $error = array('type'=>'error','msg'=>'');
        $success = array('type'=>'success','cate'=>0,'num'=>0,'total'=>0);
        if(is_null($list)){
            $error['msg'] = '没有对应规格的货品';
            $this->ajaxReturn($error);die;
        };
        $product = M('Shop_goods')->where(array('gid'=>$post['gid'],'is_del'=>0))->find();
        if(is_null($product)){
            $error['msg'] = '没有对应的商品或商品已下线';
            $this->ajaxReturn($error);die;
        };
	}
    //购物车页面
    public function cart(){
        if(isset($_SESSION['member_id'])){
        	if(isset($_SESSION['cart'])){
        		$new_cart =  $_SESSION['cart'];
        		foreach($new_cart as $k=>$v){
        			$re = $this->cart->store($v);
        		}
				unset($_SESSION['cart']);
        	}
            $cart = $this->cart->where(array('member_id'=>$_SESSION['member_id'],'is_del'=>0))->select();
        }else{
            $cart = $_SESSION['cart'];
        };
        // dump($cart);die;
        $data = $this->cart->get_data($cart);
        $this->assign('data',$data);
        $this->display();
    }

    //购物车变化
    public function change_cart(){
        if(!IS_AJAX) return FALSE;
        $error = array('type'=>'error','msg'=>'数据异常');
        $post = I('post.');
        if(isset($post['num']) && $post['num'] < 1){//非法操作
            $this->ajaxReturn($error);die;
        };
        $ids = explode('|',$post['ids']);
        //选中和删除动作
        if($post['action'] == 'click'){
            $data['type'] = 'success';
            $data['total_all'] = $this->get_total($ids);
            $this->ajaxReturn($data);die;
        }else if($post['action'] == 'del'){
            $data['type'] = 'success';
            $ids = array_unique($ids);
            if(isset($_SESSION['member_id'])){
                $this->cart->where(array('cart_id'=>$ids[0]))->save(array('is_del'=>1));
            }else{
                unset($_SESSION['cart'][$ids[0]]);
            };
            if($post['bool'] == 'true'){
                unset($ids[0]);
                $data['total_all'] = $this->get_total($ids);
            };
            $this->ajaxReturn($data);die;
        };
        $ids = array_unique($ids);
        if(isset($_SESSION['member_id'])){
            $re = $this->cart->where(array('cart_id'=>$ids[0],'is_del'=>0))->save(array('num'=>$post['num']));
        }else{
            $_SESSION['cart'][$ids[0]]['num'] = $post['num']; 
            $re = true;
        };
        if($re === FALSE){
            $this->ajaxReturn($error);die;
        };
        //返回数据
        if(isset($_SESSION['member_id'])){
            $cart = $this->cart->where(array('cart_id'=>$ids[0],'is_del'=>0))->find();
        }else{
            $cart = $_SESSION['cart'][$ids[0]];
        };
        $price = M('Shop_goods_list')->where(array('glid'=>$cart['glid'],'is_del'=>0))->getField('price');
        if(is_null($price)){
            $this->ajaxReturn($error);die;
        };
        $data['total'] = $price * $cart['num'];
        if($post['bool'] == 'true'){//判断是否是选中状态
            $data['total_all'] = $this->get_total($ids);
        };
        $data['type'] = 'success';
        $this->ajaxReturn($data);die;
    }


    //获得总价
    private function get_total($ids){
        $total_all = 0;
        foreach($ids as $k => $v){
            if(isset($_SESSION['member_id'])){
                $temp = $this->cart->where(array('cart_id'=>$v,'is_del'=>0))->find();
            }else{
                $temp = $_SESSION['cart'][$v];
            };
            $price = M('Shop_goods_list')->where(array('glid'=>$temp['glid'],'is_del'=>0))->getField('price');
            if(!is_null($price)){
                $total_all = $total_all + ($price * $temp['num']);
            };
        };
        return $total_all;
    }
	/**
	 * 婚博士在线聊天
	 */
	public function cose(){
		$admin = M('merchant_temp')->where(array('member_id'=>$_SESSION['member_id']))->find();
		if(!$admin){
			$admin['admin_id'] = 0;
		}
		$data = M('custom_type')->where(array('is_del'=>0))->select();
		$this->assign('data',$data);
		$this->assign('admin',$admin);
		$this->display();
	}
	/**
	 * 查找客服的方法
	 * $temp:先查看用户临时表中是否有该客户存在，不存在往下走，存在发送消息，
	 * $admin：查询匹配的客服，遍历$admin查询那些客服是空闲的，如都有用户往下走 存入$num；
	 * $num:求出最小值，谁最小将此用户分配给谁
	 * $sort:假如客服和客户的交谈量一样，那么就是用admin表中的sort字段来排序；
	 */
	public function check_cose(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.');
		//匹配客户开始
		$temp = M('merchant_temp')->where(array('member_id'=>$_SESSION['member_id']))->find();
		if(!$temp){
			$where['is_del'] = 0;
			$where['is_custom'] = 1;
			$where['cus_type_id'] = array('like','%'.$data['custom_type'].'%');
			$admin = M('admin')->order('sort asc')->where($where)->select();
			$num = array();
			foreach ($admin as $key => $value){
				$info = M('merchant_temp')->where(array('admin_id'=>$value['admin_id']))->select();
				$num = M('merchant_temp')->where(array('admin_id'=>$value['admin_id']))->count();
				$num = $num;
				if(!$info){
					$cousom = $value['admin_id'];
					break;
				}	
			}
			if(!$cousom){
				$pos = array_search(min($num), $num);
				foreach ($admin as $key => $value){
					$info = M('merchant_temp')->where(array('admin_id'=>$value['admin_id']))->count();
					if($info == $num[$pos]){
						$cousom = $value['admin_id'];
						break;
					}	
				}
			}
			//匹配客户结束
			$re = M('merchant')->where(array('member_id'=>$_SESSION['member_id']))->find();
			$content['content'] = $re['m_name'];
			$content['member_id'] = $_SESSION['member_id'];
			$content = json_encode($content);
			$bool = sendMsg('',$cousom,'',$content,1); 
			if($bool == 'ok'){
				$this->ajaxReturn(array('type'=>1,'content' =>'为您匹配成功，请等待客服为您解答问题','admin_id'=>$cousom));exit;
	//			$this->not_online_courom[] = $cousom;
	//			$this->again($data);
			}else{
				$this->ajaxReturn(array('type'=>0,'content' =>'客服不在线，您可询问其他事项','admin_id'=>0));exit;
			}
		}else{
			$temp_type = M('custom_type')->where(array('custom_type_id'=>$data['custom_type']))->getField('cus_name');
			$content['cose_conent'] = '该客户想询问关于'.$temp_type.'的问题';
			$content['member_id'] = $temp['member_id'];
			$content = json_encode($content);
			$me_to_uid = C('SYS_NAME').'_merchant_'.$temp['member_id'].C('SYS_NAME').'_custom_'.$temp['admin_id'];
			sendMsg('',$data['admin_id'],$me_to_uid,$content,'');
			$this->ajaxReturn(array('type'=>1,'content' =>'为您匹配成功，请等待客服为您解答问题','admin_id'=>$temp['admin_id']));exit;
		}
	}
	/**
	 * 再次匹配
	 */
	/*public function again($data){  
		$temp = M('merchant_temp')->where(array('member_id'=>$_SESSION['member_id']))->find();
		if(!$temp){
			if(!is_null($this->not_online_courom)){
				$where['admin_id'] = array('not in',implode(',', $this->not_online_courom));
			}
			$where['is_del'] = 0;
			$where['is_custom'] = 1;
			$where['cus_type_id'] = array('like','%'.$data['custom_type'].'%');
			$admin = M('admin')->order('sort asc')->where($where)->select();
			$num = array();
			foreach ($admin as $key => $value){
				$info = M('merchant_temp')->where(array('admin_id'=>$value['admin_id']))->select();
				$num = M('merchant_temp')->where(array('admin_id'=>$value['admin_id']))->count();
				$num = $num;
				if(!$info){
					$cousom = $value['admin_id'];
					break;
				}	
			}
			if(!$cousom){
				$pos = array_search(min($num), $num);
				foreach ($admin as $key => $value){
					$info = M('merchant_temp')->where(array('admin_id'=>$value['admin_id']))->count();
					if($info == $num[$pos]){
						$cousom = $value['admin_id'];
						break;
					}	
				}
			}
		}else{
			$cousom = $temp['admin_id'];
		}
		//匹配客户结束
		$re = M('merchant')->where(array('member_id'=>$_SESSION['member_id']))->find();
		$content['content'] = $re['m_name'];
		$content['member_id'] = $_SESSION['member_id'];
		$content = json_encode($content);
		$bool = sendMsg('',$cousom,'',$content,1); 
		if($bool != 'ok'){
			$not_online_courom[] = $cousom;
			$this->again($data);
		}
	}*/

	/**
	 * 获取客服信息
	 */
	public function custom(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.msg');
		$re = M('admin')->where(array('admin_id'=>$data['admin_id']))->find();
		$this->ajaxReturn($re);exit;
	}
	/**
	 * 用户发送消息的方法
	 */
	public function send(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.');
		$data['is_cus_is_mer'] = 2;
		$data['member_id'] = $_SESSION['member_id'];
		$re = M('cose')->add($data);
		if($re){
			$content['cose_conent'] = $data['cose_conent'];
			$content['member_id'] = $data['member_id'];
			$content = json_encode($content);
			$me_to_uid = C('SYS_NAME').'_merchant_'.$_SESSION['member_id'].C('SYS_NAME').'_custom_'.$data['admin_id'];
			sendMsg('',$data['admin_id'],$me_to_uid,$content,'');
			$re = M('merchant')->where(array('member_id'=>$_SESSION['member_id']))->find();
			$this->ajaxReturn($re);exit;
		}
	}
	/**
	 * 清除temp表
	 */
	public function clean(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.');
		$re = M('merchant_temp')->where(array('member_id'=>$data['merchant']))->delete();
		if($re){
			$content['cose_conent'] = "当前这位客户已关闭网页 ";
			$content['member_id'] = $data['merchant'];
			$content = json_encode($content);
			$me_to_uid = C('SYS_NAME').'_merchant_'.$_SESSION['member_id'].C('SYS_NAME').'_custom_'.$data['admin_id'];
			sendMsg('',$data['admin_id'],$me_to_uid,$content,'');
			$this->ajaxReturn($re);exit;
		}
	}

}