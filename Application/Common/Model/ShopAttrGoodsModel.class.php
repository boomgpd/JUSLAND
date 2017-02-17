<?php namespace Common\Model;
use Think\Model;

class ShopAttrGoodsModel extends Model{
	protected $tableName="shop_goods_attr";
	protected $_validate = array(
	    array('type_attr_taid','require','类型属性主键必须存在必须填写！'), 
	    array('gavalue','require','商品详情必须填写！'), 
	    array('goods_gid','require','商品主键必须填写！'), 
   );
	/**
	 * 创建添加方法
	 */	
	public function store($data){
		/**
		 * 添加属性
		 */
		 $id = array();
		foreach ($data['gavalue'] as $key => $value) {
			$list = $this->where(array('gavalue'=>$value,'spec_id'=>$data['s_id'][$key],'is_del'=>0))->find();
			if($list){
				unset($data['gavalue'][$key]);
				unset($data['s_id'][$key]);
				$id[] = $list['gaid']; 
			}else{
				$attr = array('goods_gid'=>$data['goods_id']);
				$attr['spec_id'] = $data['s_id'][$key];
				$attr['gavalue'] = $value;
				if(!$this->create($attr)) return FALSE;
				$re = M('shop_goods_attr')->add($this->data);
				$id[] = $re; 
			}
		}
		return $id;
	}
	
	/**
	 * 创建编辑方法
	 */	
	public function edit($data){
		$data['list'] = explode('|', $data['list']['combine']);
//		dump($data);die;
		foreach ($data['list'] as $key => $value) {
			$re = $this->where(array('is_del'=>0,'spec_id'=>$data['s_id'][$key],'gavalue'=>$data['combine'][$key]))->find();
			if(!$re){
				$info[] = $this->add(array('gavalue'=>$data['combine'][$key],'spec_id'=>$data['s_id'][$key],'goods_gid'=>$data['goods_id']));
			}else{
				$info[] = $re['gaid']; 
			}
//			$save = $this->where(array('gaid'=>$value))->save(array('gavalue'=>$data['combine'][$key]));
		}
		$info = implode("|",$info);
		
//		//先将属性都删除掉
//		foreach ($data['combine'] as $key => $value) {
//			$delete = $this->where(array('goods_gid'=>$data['goods_id'],'gavalue'=>$value,'spec_id'=>$data['s_id'][$key]))->save(array('is_del'=>1));
//		}
//		//然后再将规格属性依此添加到attr表里
//		$attr = array('goods_gid'=>$data['goods_id']);
//		foreach($data['s_id'] as $k=>$v){
//			$attr['gavalue'] = $data['combine'][$k];
//			$attr['spec_id'] = $v;
//			if(!$this->create($attr)) return FALSE;
//			$re = M('shop_goods_attr')->add($this->data);
//			$combine[] = $re; 
//		}
		return $info;
//		//查询规格是否有改变
//		$alter = empty($data['spec'])?FALSE:TRUE;
//		if($alter === TRUE){
//			//获得规格id
//			$taids = $this->alias('a')->join('__SHOP_TYPE_ATTR__ ta ON a.type_attr_taid = ta.taid')->group('type_attr_taid')->where(array('a.goods_gid'=>$data['goods_gid'],'a.is_del'=>0,'ta.class'=>2))->getField('type_attr_taid',true);
//			if(is_null($taids)) $alter = FALSE;
//			foreach($taids as $k => $v){
//				$temp = $this->where(array('goods_gid'=>$data['goods_gid'],'type_attr_taid'=>$v,'is_del'=>0))->getField('gavalue',true);
//				$arr = is_null($data['spec'][$v])?array():$data['spec'][$v];
//				if(count($arr)>count($temp)){
//					$re = array_diff($arr,$temp);
//				}else{
//					$re = array_diff($temp,$arr);
//				}
//				if(!empty($re)){
//					$alter = FALSE;
//					break;
//				};
//			};
//		};
//		
//		//删除属性
//		$gaids = $this->alias('a')->join('__SHOP_TYPE_ATTR__ ta ON a.type_attr_taid = ta.taid')->where(array('a.goods_gid'=>$data['goods_gid'],'ta.class'=>1))->getField('gaid',true);
//		if(is_null($gaids)) $gaids = array();
//		$re = $this->where(array('gaid'=>array('IN',implode(',',$gaids))))->setField(array('is_del'=>1));
//		if(!$re && $re===FALSE){
//			$this->error = '删除对应属性失败';
//			return FALSE;
//		}
//		/**
//		 * 添加属性
//		 */
//		if(isset($data['attr'])){
//			$data['attr'] = array_filter($data['attr']);
//			$attr = array('goods_gid'=>$data['goods_gid']);
//			foreach($data['attr'] as $k=>$v){
//				$attr['gavalue'] = $v;
//				$attr['type_attr_taid'] = $k;
//				if(!$this->create($attr)) return FALSE;
//				$this->add($this->data);
//			}
//		};
//
//		/**
//		 * 添加规格
//		 */
//		if(isset($data['spec']) && $alter === FALSE){
//			$re = M('shop_goods_list')->where(array('goods_id'=>$data['goods_gid']))->setField(array('is_del'=>1));
//			if(!$re && $re===FALSE){
//				$this->error = '删除对应货品失败';
//				return FALSE;
//			};
//			$gaids = $this->alias('a')->where(array('a.goods_gid'=>$data['goods_gid'],'ta.class'=>2))->join('__SHOP_TYPE_ATTR__ ta ON a.type_attr_taid = ta.taid')->getField('gaid',true);//获得规格id
//			if(is_null($gaids)) $gaids = array();
//			$re = $this->where(array('gaid'=>array('IN',implode(',',$gaids))))->setField(array('is_del'=>1));
//			if(!$re && $re===FALSE){
//				$this->error = '删除对应规格失败';
//				return FALSE;
//			};
//			
//			foreach($data['spec'] as $k=>&$v){
//				$data['spec'][$k] = array_filter($v);//去除空元素
//				$data['spec'][$k] = array_unique($v);//去除重复元素
//				if($v){
//					$spec = array('goods_gid'=>$data['goods_gid']);
//					foreach($v as $kk=>$vv){
//						$spec['gavalue'] = $vv;
//						$spec['type_attr_taid'] = $k;
//						if(!$this->create($spec)) return FALSE;
//						$this->add($this->data);
//					}
//				}
//				
//			}
//		};
//		return TRUE;
	}
	
	
}
