<?php namespace Common\Model;
use Think\Model;
/**
 * 影楼分类表
 */
class Photo_categoryModel extends Model{
	protected $tableName="photo_category";
	
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
		if($data['level'] == 1){
			$data['admin_id'] = $_SESSION['admin_id'];
			$data['addtime'] = time();
		}else if($data['level'] == 2){
			$data['admin_id'] = $_SESSION['admin_id'];
			$data['addtime'] = time();
		}else if($data['level'] == 3){
			$data['admin_id'] = $_SESSION['admin_id'];
			$data['addtime'] = time();
		}
		if(!$this->create($data)) return FALSE;
		$re = M('photo_category')->add($this->data);
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
		$re['one'] = $this->Field('p_c_id,cname')->where(array('is_del'=>0,'level'=>1,'p_id'=>0))->select();
		$re['two'] = array();
		foreach ($re['one'] as $key => $value) {
			$two = array();
			$two = $this->Field('p_c_id,cname')->where(array('is_del'=>0,'level'=>2,'p_id'=>$value['p_c_id']))->select();
			$three = array();
			foreach ($two as $k => $v) {
				$v['three'] = $this->Field('p_c_id,cname')->where(array('is_del'=>0,'level'=>3,'p_id'=>$v['p_c_id']))->select();
				$three[] = $v;
			}
			$re['two'][] = $three;
		}
		return $re;
	}
	/**
	 * 列表页显示分类的方法
	 */
	public function getList($id){
		$re = $this->where(array('is_del'=>0,'p_id'=>$id))->select();
		if($re){
			foreach ($re as $key => $value) {
				$re = $this->where(array('is_del'=>0,'p_id'=>$value['p_c_id']))->select();
				foreach ($re as $k => $v) {
					foreach (explode('|', $v['p_area_name']) as $kk => $vv) {
						$attr[0][] .= $vv;
					}
					foreach (explode('|', $v['p_type_name']) as $kk => $vv) {
						$attr[1][] .= $vv;
					}
					foreach (explode('|', $v['p_money_name']) as $kk => $vv) {
						$attr[2][] .= $vv;
					}
				}
			}
			foreach ($attr as $key => &$value) {
				$value = array_unique($value);
			}
		}else{
			$re = $this->where(array('is_del'=>0,'p_c_id'=>$id))->find();
			$attr[] = explode('|', $re['p_area_name']);
			$attr[] = explode('|', $re['p_type_name']);
			$attr[] = explode('|', $re['p_money_name']);
			$attr[] = $re['p_c_id'];
		}
//		dump($attr);die;
		return $attr;
	}
	/**
	 * 列表页显示商品的方法
	 */
	public function list_goods($get){
		if($get['id'] && $get['desc'] && $get['area_id']){
			$info = M('photo')->where(array('city'=>$get['area_id']))->getField('photo_id',TRUE);
			$where['photo_id'] = array('in',implode(',', $info));
			$data = explode('_', $get['desc']);
			if($data[0]){$where['p_area'] = $data[0];}
			if($data[1]){$where['p_typ'] = array('like','%'. $data[1] .'%');}
			if($data[2]){$where['p_money'] = $data[2];}
		}else if($get['id'] && $get['desc'] && !$get['area_id']){
			$data = explode('_', $get['desc']);
			if($data[0]){$where['p_area'] = $data[0];}
			if($data[1]){$where['p_typ'] = array('like','%'. $data[1] .'%');}
			if($data[2]){$where['p_money'] = $data[2];}
		}else if($get['id'] && !$get['desc'] && $get['area_id']){
			$info = M('photo')->where(array('city'=>$get['area_id']))->getField('photo_id',TRUE);
			$where['photo_id'] = array('in',implode(',', $info));
		}
		$re = $this->where(array('is_del'=>0,'p_id'=>$get['id']))->select();
		if($re){
			$res = array();
			foreach ($re as $key => $value) {
				$res[] = $this->where(array('is_del'=>0,'p_id'=>$value['p_c_id']))->select();
			}
//				$id = '';
			foreach ($res as $key => $value) {
				foreach ($value as $k => $v) {
					$id .= $v['p_c_id'].',';
				}
			}
			$where['p_c_id'] = array('in',rtrim($id,','));
		}else{
			$where['p_c_id'] = $get['id'];
		}
		$where['is_del'] = 0;
		if($get['page']){$page = $get['page'];}else{$page = 1;}
		$limit = 15;
		if($get['moods']){
			$re = M('photo_product')->order('moods desc')->where($where)->limit($limit)->page($page)->select();
		}else if($get['make']){
			$re = M('photo_product')->order('make_num desc')->where($where)->limit($limit)->page($page)->select();
		}else if($get['price']){
			$re = M('photo_product')->order('price desc')->where($where)->limit($limit)->page($page)->select();
		}else{
			$re = M('photo_product')->where($where)->limit($limit)->page($page)->select();
		}
		$num = ceil(M('photo_product')->where($where)->count()/$limit);
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
		$re['count'] =array('page_left'=>$page_left,'num'=>$num,'page_right'=>$page_right,'page_type'=>$page_type,'page'=>$page); //返回你生成的数据
		return $re; 
	}
}
