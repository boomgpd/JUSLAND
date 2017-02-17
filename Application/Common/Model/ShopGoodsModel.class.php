<?php namespace Common\Model;
use Think\Model;

/**
 * 商品模型
 */
class ShopGoodsModel extends Model{
	protected $tableName="shop_goods";
	protected $seek = 'abcefghijklmnopqrstuvwxyz';
	protected $_validate = array(
	    array('gname','require','商品名称必须填写！'), //默认情况下用正则进行验证
	    array('gnumber','require','商品货号必须填写！'), 
	    array('unit','require','商品单位必须填写！'),
//	    array('url','require','商品链接必须填写！'), 
	    array('des','require','商品描述必须填写！'),
	    array('marketprice','require','市场价格必须填写！'), 
	    array('shopprice','require','会员价格必须填写！'), 
	    array('pic','require','商品列表图必须填写！'),  
	    array('click','require','商品点击次数必须填写！'), 
	    array('addtime','require','添加时间必须填写！'), 
	    array('admin_id','require','操作员主键必须填写！'), 
	    array('type_id','require','类型主键必须填写！'), 
	    array('category_cid','require','分类名称必须填写！'), 
   );
	/**
	 * 创建添加方法
	 */	
	public function store($data){
		if(empty($data['label']) && empty($data['spec_id'])){
			$this->error = '标签和规格必须选，至少分别选一个';return FALSE;
		};
		$goods_data = $this->goodsData($data);
		M()->startTrans();//开启事务
		if(!$this->create($goods_data)) return FALSE;
		$goods_gid = $this->add($this->data);
		if(!$goods_gid){
			$this->error = '添加失败';
			return FALSE;
		}
		$arr = array('gid'=>$goods_gid);
		foreach($data['label'] as $k => $v){
			$arr['lid'] = $v;
			$re = M('shop_goods_label')->add($arr);
			if($re === FALSE){
				$this->error = '添加失败';return FALSE;
			};
		};

//		dump($goods_gid);die;

//		商品详情添加开始
		$data['goods_gid'] = $goods_gid;
		$goods_detail_data = $this->goodsDetails($data);
		$goods_detail = new ShopDetailsGoodsModel;
		$detail_re = $goods_detail->store($goods_detail_data);
		if(!$detail_re){
			$this->error = $goods_detail->getError();
			M()->rollback();//失败后回滚
			return FALSE;
		}
//		商品详情添加结束

//		商品类型属性添加
//		$goods_attr_data = $this->goodsAttr($data); 
//		$goods_attr = new ShopAttrGoodsModel;
//		$attr_re = $goods_attr->store($goods_attr_data);
//		if(!$attr_re){
//			$this->error = $goods_attr->getError();
//			M()->rollback();//失败后回滚
//			return FALSE;
//		}	
//		商品类型属性添加结束
		M()->commit();//提交事务
		return TRUE;
	}
	
	/**
	 * 创建编辑方法
	 */
	public function edit($data){
		if(empty($data['label']) && empty($data['spec_id'])){
			$this->error = '标签和规格必须选，至少分别选一个';return FALSE;
		};
		$goods_data = $this->goodsData($data);
		$goods_data['gid'] = $data['gid'];
		unset($goods_data['gnumber']);
		M()->startTrans();//开启事务
		if(!$this->create($goods_data)) return FALSE;
		// dump($this->data);die;
		$goods_gid = $this->save($this->data);
		if($goods_gid === FALSE){
			$this->error = '编辑失败';
			return FALSE;
		};

		M('shop_goods_label')->where(array('gid'=>$data['gid'],'is_del'=>0))->setField(array('is_del'=>1));
		$arr = array('gid'=>$data['gid']);
		foreach($data['label'] as $k => $v){
			$arr['lid'] = $v;
			$re = M('shop_goods_label')->add($arr);
			if($re === FALSE){
				$this->error = '添加失败';return FALSE;
			};
		};
//		dump($goods_gid);die;

//		商品详情添加开始
		$data['goods_gid'] = $goods_data['gid'];
		$goods_detail_data = $this->goodsDetails($data);
//		dump($goods_detail_data);die;
		$goods_detail = new ShopDetailsGoodsModel;
		$detail_re = $goods_detail->edit($goods_detail_data);

		if(!$detail_re && $detail_re === FALSE){
			$this->error = $goods_detail->getError();
			M()->rollback();//失败后回滚
			return FALSE;
		}
		
//		商品详情添加结束
//		商品类型属性添加
//		$goods_attr_data = $this->goodsAttr($data);
//		$goods_attr = new ShopAttrGoodsModel;
//		$attr_re = $goods_attr->edit($goods_attr_data);
//		if(!$attr_re){
//			$this->error = $goods_attr->getError();
//			M()->rollback();//失败后回滚
//			return FALSE;
//		}
//		商品类型属性添加结束
		M()->commit();//提交事务
		return TRUE;
	
	}
	
	
	/**
	 * 创建获取随机货号方法
	 */
	public function get_goods_num(){
		$num = mt_rand(1,4);
		$goods_num = '';
		for($num;$num>0;$num--){
			$goods_num .= substr($this->seek,mt_rand(0,4),1);
		}
		$number =  mt_rand(100000,999999);
		$goods_num .=  $number;
		$re = $this->where(array('gnumber'=>$goods_num))->find();
		if($re){
			$this->get_goods_num();
		}else{
			return $goods_num;
		}
	}
	
	/**
	 * 创建拼接商品表数据方法
	 */
	public function goodsData($data){
		$goods = array();
		$goods['gname'] = $data['gname'];
		$goods['gnumber'] = $this->get_goods_num();
		$goods['unit'] = $data['unit'];
		$goods['marketprice'] = $data['marketprice'];
		$goods['shopprice'] = $data['shopprice'];
		$goods['pic'] = $data['pic'];
		$goods['click'] = $data['click'];
		$goods['addtime'] = time();
		$goods['admin_id'] = $_SESSION['admin_id'];
		$goods['type_id'] = M('shop_category')->where(array('is_del'=>0,'cid'=>$data['category_id']))->getField('type_id');
		$data['url'] = array_filter($data['url']);//去除空元素
		$data['url'] = array_unique($data['url']);//去除重复元素
		$goods['url'] = implode("|",$data['url']);
		$goods['des'] = $data['des'];
		$goods['category_cid'] = $data['category_id'];
		$goods['spec_id'] = implode('|',$data['spec_id']);
		return $goods;
	}
	
	/**
	 * 创建拼接商品详细表数据
	 */
	public function goodsDetails($data){
		$goods_detail_data = array();
		$goods_detail_data['big'] = implode("|", $data['big']);
		$goods_detail_data['medium'] = implode("|", $data['medium']);
		$goods_detail_data['details'] = $data['details'];
		$goods_detail_data['goods_gid'] = $data['goods_gid'];
		return $goods_detail_data;
	}
	
	/**
	 * 创建拼接商品属性规格表数据
	 */
	public function goodsAttr($data){
		$goods_attr_data = array();
		$goods_attr_data['spec_id'] = $data['s_id'];
		$goods_attr_data['gavalue'] = $data['gavalue'];
		$goods_attr_data['goods_gid'] = $data['goods_gid'];
		return $goods_attr_data;
	}

	//获得关联商品id
	public function get_ids($id,$bool=FALSE){
		//获得商品id
	 	$all = M('shop_goods_links')->field('one,two')->where(array('one|two'=>$id,'is_del'=>0))->select();//获得两侧商品id
	 	$gids = array();
	 	foreach($all as $k => $v){
	 		$gids = array_merge(array_values($v),$gids);
	 	};
	 	$gids = array_unique($gids);//去重商品id
	 	if($bool == true){
	 		foreach($gids as $k => $v){
	 			if($v == $id){
	 				unset($gids[$k]);
	 			};
	 		};
	 	};
	 	return $gids;
	}
	
}
