<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\ShopGoodsModel;
use Common\Model\Shop_cateModel;
use Common\Model\ShopGoodsListModel;
use Common\Model\Shop_labelModel;
use Common\Model\ShopSpecModel;
/**
 * 创建后台首页控制器
 * 16/08/03
 * boom
 */
class ShopGoodsController extends CommonController {
	public $title = 'JUSLAND商城管理';
	public $db;
	public $label;
	public $category;
	protected $goods_list;
	protected $spec;
	
	/**
	 * 创建构造函数
	 */
	 public function __construct(){
	 	parent::__construct();
		$this->assign('title',$title);	
		$this->db = new ShopGoodsModel;	
		$this->label = new Shop_labelModel;	
		$this->category = new Shop_cateModel;
		$this->goods_list = new ShopGoodsListModel;
		$this->spec = new ShopSpecModel;
	 }

	//商品删除
	 public function goods_del($gid=NULL){
	 	if(is_null($gid)) return FALSE;
	 	
	 	$cid = M('shop_goods')->where(array('gid'=>$gid))->getField('category_cid');
	 	$arr = array(
	 		array('shop_goods','gid'),//商品
	 		array('shop_goods_list','goods_id'),//货品
	 		array('shop_goods_attr','goods_gid'),//商品属性
	 		array('shop_goods_cart','gid'),//购物车
	 		array('shop_goods_details','goods_gid'),//商品详情
	 		array('shop_cc_goods','gid'),//首页推荐
	 		array('shop_goods_links','one|two'),
	 	);
	 	M()->startTrans();//开启事务

	 	foreach($arr as $k => $v){
	 		$re = M($v[0])->where(array($v[1]=>$gid))->save(array('is_del'=>1));

	 		if($re === FALSE){
	 			M()->rollback();
	 			alert('删除失败');die;
	 		};
	 	}
	 	M()->commit();
	 	alert('删除成功',U('show',array('cid'=>$cid)));
	 }

	 //商品关联列表
	 public function links_list($id=NULL){
	 	if(is_null($id)) return FALSE;
	 	$data = $this->db->where(array('gid'=>$id,'is_del'=>0))->find();
	 	if(is_null($data)) return FALSE;
	 	$gids = $this->db->get_ids($id,true);
	 	$data = $this->db->alias('g')->where(array('g.gid'=>array('IN',implode(',',$gids)),'g.is_del'=>0,'c.is_del'=>0))->join('__SHOP_CATEGORY__ c ON g.category_cid = c.cid')->select();
	 	$this->assign('data',$data);
	 	$this->assign('id',$id);
	 	$this->display();
	 }

	 //未关联列表
	 public function not_links($id=NULL){
	 	if(is_null($id)) return FALSE;
	 	$data = $this->db->where(array('gid'=>$id,'is_del'=>0))->find();
	 	if(is_null($data)) return FALSE;
	 	$gids = $this->db->get_ids($id);
	 	$condition = array('g.gid'=>array('NOT IN',implode(',',$gids)),'g.is_del'=>0,'c.is_del'=>0);
	 	$get = I('get.');
	 	if(!empty($get['value'])){
	 		$condition[$get['type']] = array('LIKE','%'.$get['value'].'%');
	 	};
	 	$order = '';
	 	if(!empty($get['order'])){
	 		$order = 'g.addtime '.$get['order'];
	 	};
	 	$data = $this->db->alias('g')->where($condition)->join('__SHOP_CATEGORY__ c ON g.category_cid = c.cid')->order($order)->select();
	 	$this->assign('data',$data);
	 	$this->assign('id',$id);
	 	$this->display();
	 }

	 //添加关联方法
	 public function links_add(){
	 	if(!IS_AJAX || !IS_POST) return FALSE;
	 	$post = I('post.');
//	 	$re = M('shop_goods_links')->where(array('one|two'=>$post['one'],'one|two'=>$post['two'],'is_del'=>0))->find();
//	 	if(!is_null($re)){
//	 		$this->ajaxReturn(array('type'=>0,'msg'=>'这两件商品已关联'));die;
//	 	};
	 	$post['addtime'] = time();
	 	$re = M('shop_goods_links')->add($post);
	 	if(!$re){
	 		$this->ajaxReturn(array('type'=>0,'msg'=>'数据异常'));die;
	 	};
	 	$this->ajaxReturn(array('type'=>1,'msg'=>'关联成功'));die;
	 }

	 //删除关联方法
	 public function links_del(){
	 	if(!IS_AJAX || !IS_POST) return FALSE;
	 	$post = I('post.');
	 	$re = M('shop_goods_links')->where(array('one|two'=>$post['one'],'one|two'=>$post['two'],'is_del'=>0))->save(array('is_del'=>1));
	 	if(!$re){
	 		$this->ajaxReturn(array('type'=>0,'msg'=>'删除失败'));die;
	 	};
	 	$this->ajaxReturn(array('type'=>1,'msg'=>'删除成功'));die;
	 }
	 
	/**
	 * 创建列出商品分类方法
	 */
	 public function list_cate_show(){
	 	$category = M('shop_category')->where(array('pid'=>0,'is_del'=>0))->select();
		$this->assign('category',$category);
		$this->display();
	 }
	 
	 /**
	  * 创建查看商品方法
	  */
	public function show(){
		$cid = I('get.cid');
		$get = I('get.');
		$condition = array();
		
		if($get['value'] && $get['type'] == 'gname'){
			$condition['a.gname'] = array('like','%'.$get['value'].'%');
		}else if($get['value'] && $get['type'] == 'gnumber'){
			$condition['a.gnumber'] = array('like','%'.$get['value'].'%');
		}else if($get['value'] && $get['type'] == 'cagegory'){
			$condition['b.cname'] = array('like','%'.$get['value'].'%');
		}
		
		if($get['order']){
			$order = $get['order'];
		}else{
			$order = 'DESC';
		}
		
		$this->assign('cid',$cid);
		$cids = implode(',', $this->category->get_son($cid));
		$data = $this->db->alias('a')->join('__SHOP_CATEGORY__ b ON a.category_cid = b.cid')->field('a.gid,a.gname,a.gnumber,a.marketprice,a.shopprice,a.category_cid,a.addtime,b.cname,a.url')->where($condition)->where(array('a.is_del'=>0,'a.category_cid'=>array('in',$cids)))->order('a.addtime '.$order)->select();
		foreach($data as $k=>&$v){
			$v['addtime'] = date('Y年m月d日H点',$v['addtime']);
		}
		$this->assign('data',$data);
//		dump($data);die;
		$this->display();
	}
	
	/**
	 * 创建货品显示方法
	 */
	public function show_list(){
		$gid = I('get.goods_id');
		$this->assign('gid',$gid);
		$cid = $this->db->where(array('gid'=>$gid,'is_del'=>0))->getField('category_cid');
		$this->assign('cid',$cid);
		$data = M('shop_goods_list')->where(array('is_del'=>0,'goods_id'=>$gid))->select();
		foreach($data as $k=>&$v){
			$v['combine'] = explode("|", $v['combine']);
			foreach($v['combine'] as $kk=>$vv){
				$v['combine'][$kk] = M('shop_goods_attr')->where(array('gaid'=>$vv))->getField('gavalue');
			}
		}
		$this->assign('data',$data);
		$goodss = $this->db->where(array('gid'=>$gid))->getField('spec_id');
		foreach (explode('|', $goodss) as $key => $value) {
			$spec[] = M('shop_spec_name')->where(array('s_id'=>$value))->find();
		}
		$this->assign('spec',$spec);
		$this->display();
	}
	
	/**
	 * 创建添加货品方法
	 */
	public function goods_list_add(){
		if(!IS_POST) return FALSE;
		$data = I('post.');
		$re = $this->goods_list->store($data);
		if(!$re){
			alert($this->goods_list->getError());
		}else{
			alert('添加成功',U('admin/shopGoods/show_list',array('goods_id'=>$data['goods_id'])));
		}
	}

/**
	 * 创建添加货品方法
	 */
	public function goods_list_edit(){
		$glid = I('get.glid');
		$goods_id = I('get.goods_id');
		if(IS_POST){
			$data = I('post.');
			$re = $this->goods_list->edit($data);
			if(!$re && $re === FALSE){
				alert($this->goods_list->getError());
			}else{
				alert('编辑成功',U('admin/shopGoods/show_list',array('goods_id'=>$goods_id)));
			}
		}
		
		$good_list = $this->goods_list->where(array('glid'=>$glid,'is_del'=>0))->getField('combine');
		foreach (explode('|', $good_list) as $key => $value) {
			$spec[] = M('shop_goods_attr')->where(array('gaid'=>$value))->find();
		}
		foreach ($spec as $key => &$value) {
			$value['spec_id'] = M('shop_spec_name')->field('spec_name,s_id')->where(array('s_id'=>$value['spec_id']))->find();
		}
		$this->assign('spec',$spec);
		$this->assign('gid',$gid);
		
		$data = $this->goods_list->where(array('is_del'=>0,'glid'=>$glid))->find();
		$data['combine'] = explode("|", $data['combine']);
		$this->assign('data',$data);
		if(!$data) alert('该商品不存在，或已删除');
		$this->display();
	}
	
	/**
	 * 创建删除方法
	 */
	public function goods_list_del(){
		$glid = I('get.glid');
		$goods_id = I('get.goods_id');
		$re = $this->goods_list->save(array('glid'=>$glid,'is_del'=>1));
		if(!$re){
			alert($this->goods_list->getError());
		}else{
			alert('删除成功',U('admin/shopGoods/show_list',array('goods_id'=>$goods_id)));
		}
	} 
	
	
	/**
	 * 创建列出分类方法
	 */
	 public function list_cate_add(){
	 	$category = M('shop_category')->where(array('pid'=>0,'is_del'=>0))->select();
		$this->assign('category',$category);
//		dump($category);die;
		$this->display();
	 }
	 
	
	/**
	 * 创建商品添加方法
	 */
	 public function add(){
	 	$cid = I('get.cid');
	 	if(IS_POST){
	 		$data = I('post.');
			$re = $this->db->store($data);
			if(!$re){
				alert($this->db->getError());
			}else{
				alert('添加成功',U('admin/shopGoods/add',array('cid'=>$cid)));
			}
	 	}
	 
		$category = M('shop_category')->where(array('is_del'=>0,'pid'=>$cid))->select();
		foreach($category as $k=>&$v){
			$v['sonData'] = M('shop_category')->where(array('is_del'=>0,'pid'=>$v['cid']))->select();
		}
		$label = $this->label->where(array('category_id'=>$cid,'is_del'=>0))->select();
		$this->assign('label',$label);
		$this->assign('category',$category);
		$this->display();
	 }
		/**
		 * 编辑添加新规格的方法
		 */
	 public function addspec(){
	 	if(IS_AJAX){
	 		$data = I('post.gid');
			$re = $this->db->field('category_cid,spec_id')->where(array('gid'=>$data))->find();
			$res = M('shop_category')->where(array('cid'=>$re['category_cid']))->getField('spec_id');
			$re = explode('|', $re['spec_id']);
			$res = explode('|', $res);
			foreach($res as $k=>$v){
				foreach ($re as $key => $value) {
					if($v == $value){
						unset($res[$k]);
					}
				}
			}
			foreach ($res as $key => $value) {
				$info[] =  M('shop_spec_name')->where(array('s_id'=>$value))->find();
			}
			$this->ajaxReturn($info);exit;
	 	}
	 }
	 /**
	  * 创建编辑商品方法
	  */
	public function goods_edit(){
		$g_id =I('get.gid');
		$cid = $this->db->where(array('is_del'=>0,'gid'=>$g_id))->getField('category_cid');
		$cid = M('shop_category')->where(array('cid'=>$cid))->getField('pid');
		$cid = M('shop_category')->where(array('cid'=>$cid))->getField('pid');
		if(IS_POST){
			$data = I('post.');
			$re = $this->db->edit($data);
			if(!$re && $re===FALSE){
				alert($this->db->getError());
			}else{	
				alert('编辑成功',U('admin/shopGoods/show',array('cid'=>$cid)));
			}
		}
		
		$data['goods'] = $this->db->where(array('is_del'=>0,'gid'=>$g_id))->find();
		$data['goods']['url'] = explode("|", $data['goods']['url']);
		$data['details'] = M('shop_goods_details')->where(array('goods_gid'=>$g_id,'is_del'=>0))->find();
		$data['details']['medium'] = explode("|", $data['details']['medium']);
		$data['details']['big'] = explode("|", $data['details']['big']);
		$data['details']['details'] = htmlspecialchars_decode($data['details']['details']);
		$att = explode("|", $data['goods']['spec_id']);
		foreach ($att as $key => &$value) {
			$value = M('shop_spec_name')->where(array('s_id'=>$value))->find();
			$data['att'][] = $value;
		}
		$this->assign('data',$data);
		$this->assign('data',$data);
		$cid = M('shop_category')->where(array('is_del'=>0,'cid'=>$data['goods']['category_cid']))->find();
		foreach (explode('|',$cid['spec_id']) as $key => $value) {
			$spec[] = M('shop_spec_name')->where(array('s_id'=>$value))->find();
		}
		$this->assign('spec',$spec);
		$cid = M('shop_category')->where(array('is_del'=>0,'cid'=>$cid['pid']))->getField('pid');
		$category = M('shop_category')->where(array('is_del'=>0,'pid'=>$cid))->select();
		foreach($category as $k=>&$v){
			$v['sonData'] = M('shop_category')->where(array('is_del'=>0,'pid'=>$v['cid']))->select();
		}
		$cate = new \Common\Model\Shop_cateModel;
		$cids = $cate->get_bread($data['goods']['category_cid']);
		$top_cid = M('shop_category')->where(array('cid'=>$cids[0],'is_del'=>0))->getField('pid');
		$label = $this->label->where(array('category_id'=>$top_cid,'is_del'=>0))->select();
		$labels = M('shop_goods_label')->where(array('gid'=>$g_id,'is_del'=>0))->getField('lid',true);
		
		$this->assign('labels',$labels);
		$this->assign('label',$label);

		$this->assign('category',$category);
		
		$this->display();
	}
	 
	 /**
	  * 创建获取对应规格和属性的异步方法
	  */
	public function get_attr(){
		if(!IS_AJAX) return FALSE;
		$category_id = I('get.category_id');
		$type_id = M('shop_category')->where(array('is_del'=>0,'cid'=>$category_id))->getField('type_id');
		$spec = M('shop_type_attr')->where(array('is_del'=>0,'type_id'=>$type_id,'class'=>2))->select();
		foreach($spec as $k=>&$v){
			$v['tavalue'] = explode("|", $v['tavalue']);
		}
		$spec_id = M('shop_category')->where(array('is_del'=>0,'cid'=>$category_id))->getField('spec_id');
		$attr = $this->spec->getData(array('s_id'=>$spec_id));
		$arr =array();
		$arr['attr'] = $this->attr_html($attr);
		$arr['spec'] = $this->spec_html($spec);
		$this->ajaxReturn($arr);exit;
	}
	
	
	public function attr_html($attr){
		$this->assign('attr',$attr);
		$html = $this->fetch('attr_html');
		return $html;
	}
	
	public function spec_html($spec){
		$this->assign('spec',$spec);
		$html = $this->fetch('spec_html');
		return $html;
	}
	
	public function welcome(){
		$this->display();
    }
	
	/**
	 * 创建规格查看方法
	 */
	public function spec_img_show(){
		$gid = I('get.gid');
		$spec = $this->db->where(array('is_del'=>0,'gid'=>$gid))->getField('spec_id');
		foreach (explode("|", $spec) as $key => $value) {
			$spec_name[] = M('shop_spec_name')->where(array('s_id'=>$value))->find();
		}
		foreach ($spec_name as $key => &$value) {
			$value['attr'] = M('shop_goods_attr')->where(array('goods_gid'=>$gid,'spec_id'=>$value['s_id'],'is_del'=>0))->getField('gavalue',TRUE);
			$value['attr'] = implode(",", $value['attr']);
		}
		$this->assign('spec_name',$spec_name);
		$this->assign('gid',$gid);
//		$showData = array();
//		foreach($type_attr_taid as $k=>$v){
//			$temp = array();
//			$temp['name'] = M('shop_type_attr')->where(array('taid'=>$v))->getField('taname');
//			foreach($data as $kk=>$vv){
//				if($v == $vv['type_attr_taid']){
//					$temp['tavalue'][] = $vv;
//				}
//			}
//			$showData[] = $temp;
//		}
//		$this->assign('showData',$showData);
//		dump($showData);die;
		$this->display();
	}
	
	/**
	 * 创建添加规格图片方法
	 */
	public function spec_img_add(){
		$get = I('get.');
		$data = M('shop_goods_list')->where(array('goods_id'=>$get['gid'],'is_del'=>0))->getField('combine',TRUE);
		$data = explode('|',implode("|", $data));
		foreach ($data as $key => &$value) {
			$value = M('shop_goods_attr')->where(array('spec_id'=>$get['s_id'],'gaid'=>$value,'is_del'=>0))->find();
		}
		$this->assign('data',$data);
		if(IS_POST){
			$newData = $this->upload();
			if(count($newData) != count($data)){
				alert('请确认所有规格值均已勾选图片');die;
			}
			foreach($data as $k=>$v){
				$re = M('shop_goods_attr')->where(array('gaid'=>$v['gaid']))->save(array('ga_img'=>$newData[$k]['ga_img'],'ga_img_small'=>$newData[$k]['ga_img_small']));
				if(!$re){
					alert('添加失败',U('spec_img_add',array('gid'=>$get['gid'],'taid'=>$get['taid'])));die;
				} 
			}
			alert('添加成功',U('spec_img_add',array('gid'=>$get['gid'],'s_id'=>$get['s_id'])));
		}
		$this->display();
	}
	
	/**
	 * 创建编辑规格图片方法
	 */
	public function spec_img_edit(){
		$gaid = I('get.');
		$data =  M('shop_goods_attr')->where(array('gaid'=>$gaid['gaid']))->find();
		if(IS_POST){
			$newData = I('post.');
			$re = M('shop_goods_attr')->where(array('gaid'=>$newData['gaid']))->save($newData);
			if(!$re && $re === FALSE){
				alert('编辑失败');
			}else{
				alert('编辑成功',U('spec_img_add',array('gid'=>$data['goods_gid'],'s_id'=>$gaid['s_id'])));
			}
		}
		
		$this->assign('data',$data);
		$this->display();
	}
	 
	
	/**
	 * 创建图片上传并缩略方法方法
	 */
	 public function upload(){
	 	$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = 'shop_spec/';// 设置附件上传目录
			$info = $upload->upload();
			$re = $this->thumb($info);
			return $re;
	 }
	 
	 /**
	  * 创建缩略方法
	  */
	public function thumb($info){
		$arr = array();
		foreach($info as $key => $value){
			$image = new \Think\Image();
		 	$image->open(C('__THUMB__').$value['savepath'].$value['savename']);
			$dir = C('__THUMB__').'shop_spec_thumb/';
			is_dir($dir) || mkdir($dir,0777);
			$date = date('Y-m-d').'/';
			$dir .= $date;
			is_dir($dir) || mkdir($dir,0777);
			$image->thumb(150,150,6)->save($dir.$value['savename']);
			$temp = array(
					'ga_img'=>$value['savepath'].$value['savename'],
					'ga_img_small'=>'shop_spec_thumb/'.$date.$value['savename'],
			);
			$arr[] = $temp;
		}
		return $arr;
	}
	 
}