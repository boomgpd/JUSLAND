<?php namespace Common\Model;
use Think\Model;
/**
 * 影楼分类表
 */
class Hotel_categoryModel extends Model{
	protected $tableName="hotel_category";
	
	protected $_validate = array(
     array('cname','require','分类名称必须填写！'), 
     array('cname','','分类名称已经存在！',0,'unique',1),
     array('addtime','require','添加时间必须填写！'),
     array('admin_id','require','用户主键必须填写！',0),
     array('clogo','require','分类logo必须填写！'),
     array('p_area_name','require','分类logo必须填写！'),
     array('p_type_name','require','分类logo必须填写！'),
     array('p_money_name','require','分类logo必须填写！'),
   );
   
	/**
	 * 创建添加顶级分类方法
	 */
	public function add($data){
		$data['admin_id'] = $_SESSION['admin_id'];
		$data['addtime'] = time();
		if(!$this->create($data)) return FALSE;
		$re = M('hotel_category')->add($this->data);
		return $re;
	}
	
	/**
	 * 创建编辑方法
	 */
	public function edit($data){
		$this->_validate = array(
	     array('cname','require','分类名称必须填写！'), 
	     array('clogo','require','分类logo必须填写'),
	   );
	   if(!$this->create($data)) return FALSE;
	   $re = $this->where(array('p_c_id'=>$data['p_c_id']))->save($data);
		return $re;
	}
	/**
	 * 前台显示烦人方法
	 */
	public function getData(){
		$re['one'] = $this->Field('p_c_id,cname')->where(array('is_del'=>0,'level'=>2))->select();
		$temp = array('type'=>'','p_area_name'=>'','p_type_name'=>'','p_money_name'=>'');
		foreach ($re['one'] as $key => $value) {
			$two[] = $this->Field('p_c_id,p_area_name,p_type_name,p_money_name')->where(array('is_del'=>0,'level'=>2,'p_c_id'=>$value['p_c_id']))->select();
			$temp['type'] .= $value['cname'] .'|';
		};
		foreach($two as $k => $v){
			foreach($v as $a => $b){
				$temp['p_area_name'] .= $b['p_area_name'].'|';
				$temp['p_type_name'] .= $b['p_type_name'].'|';
				$temp['p_money_name'] .= $b['p_money_name'].'|';
			};
		};
		foreach($temp as $k => $v){
			$v = rtrim($v,'|');
			$temp[$k] = $v;
		};
		$attr[] = explode('|', $temp['type']);
		$attr[] = explode('|', $temp['p_money_name']);
		$attr[] = explode('|', $temp['p_type_name']);
		$attr[] = explode('|', $temp['p_area_name']);
		return $attr;
	}
	/**
	 * 列表页显示商品的方法
	 */
	public function goods($get){
		if($get['desc'] && $get['area_id']){
			$info = M('hotel')->where(array('city'=>$get['area_id']))->getField('hotel_id',TRUE);
			$where['hotel_id'] = array('in',implode(',', $info));
			$data = explode('_', $get['desc']);
			if($data[0]){$where['p_c_id'] = $this->where(array('cname'=>$data[0]))->getField('p_c_id');}
			if($data[3]){$where['p_area'] = array('like','%'. $data[3] .'%');}
			if($data[2]){$where['p_type'] = $data[2];}
			if($data[1]){$where['p_money'] = $data[1];}
		}else if($get['desc'] && !$get['area_id']){
			$data = explode('_', $get['desc']);
			if($data[0]){$where['p_c_id'] = $this->where(array('cname'=>$data[0]))->getField('p_c_id');}
			if($data[3]){$where['p_area'] = array('like','%'. $data[3] .'%');}
			if($data[2]){$where['p_type'] = $data[2];}
			if($data[1]){$where['p_money'] = $data[1];}
		}else if(!$get['desc'] && $get['area_id']){
			$info = M('photo')->where(array('city'=>$get['area_id']))->getField('photo_id',TRUE);
			$where['photo_id'] = array('in',implode(',', $info));
		}
			$where['is_del'] = 0;
			if($get['page']){$page = $get['page'];}else{$page = 1;}
			$limit = 10;
		if($get['moods']){
			$re = M('hotel_product')->field('hotel_id,list_img,p_name,price,p_p_id')->order('moods desc')->where($where)->limit($limit)->page($page)->select();
		}else if($get['make']){
			$re = M('hotel_product')->field('hotel_id,list_img,p_name,price,p_p_id')->order('make_num desc')->where($where)->limit($limit)->page($page)->select();
		}else if($get['price']){
			$re = M('hotel_product')->field('hotel_id,list_img,p_name,price,p_p_id')->order('price desc')->where($where)->limit($limit)->page($page)->select();
		}else{
			$re = M('hotel_product')->field('hotel_id,list_img,p_name,price,p_p_id')->where($where)->limit($limit)->page($page)->select();
		}
		if(!$re) return FALSE;
		$re[0]['count'] = ceil(M('hotel_product')->where($where)->count()/$limit);
		foreach ($re as $key => &$value) {
			$value['hotel_id'] = M('hotel')->field('h_name,provience,city')->where(array('hotel_id'=>$value['hotel_id']))->find();
			$value['hotel_id']['provience'] = M('area')->where(array('area_id'=>$value['hotel_id']['provience']))->getField('aname');
			$value['hotel_id']['city'] = M('area')->where(array('area_id'=>$value['hotel_id']['city']))->getField('aname');
			$arr = M('hotel_name')->field('p_a_name,p_p_a_id')->where(array('is_del'=>0,'is_list'=>1))->select();
			$value['attr'] = array();
			foreach ($arr as $k => $v) {
				$add = M('hotel_value')->field('hotel_value')->where(array('p_p_id'=>$value['p_p_id'],'p_anme_id'=>$v['p_p_a_id']))->select();
				$add['name'] = $v['p_a_name'];
				$value['attr'][] = $add;
			}
		}
		return $re;
	}
}
