<?php namespace Common\Model;
use Think\Model;

/**
 * 创建轮播图管理模型
 */
class BananerModel extends Model{
	protected $tableName="bananer";
	protected $_validate = array(
     array('bananer_type_id','require','轮播图类型必须勾选！'), //默认情况下用正则进行验证
     array('url','require','跳转地址必须填写!'), //默认情况下用正则进行验证
   );
	
	public function store($data){
		if(!$this->create($data)) return FALSE;
		$re = $this->add($this->data);
		return $re;
	}
	
	/**
	 * 创建获取瀑布流数据方法
	 */
	 public function getData($type_id){
		if($type_id){
			$condition = array('bananer_type_id'=>$type_id,'is_del'=>0);
		}else{
			$condition = array('is_del'=>0);
		}
		$data = $this->where($condition)->select();
		return $data;
	 }
	 
	 
	 /**
	 * 创建前台首页轮播图显示的方法
	 */
	 public function bananer_show($type_id){
	 	if($type_id==1){
			$condition = array('bananer_type_id'=>$type_id,'is_del'=>0);
			$data = $this->where($condition)->field('pic_src,bananer_type_id')->limit(5)->select();
		}else if($type_id==3){	 	
			$condition = array('bananer_type_id'=>$type_id,'is_del'=>0);
	 		$data = $this->where($condition)->field('pic_src,bananer_type_id')->limit(4)->select();
		}else if($type_id == '影楼顶部轮播'){
			$id = M('bananer_type')->where(array('type_name'=>'影楼顶部轮播'))->getField('bananer_type_id');
			$condition = array('bananer_type_id'=>$id,'is_del'=>0);
	 		$data = $this->where($condition)->field('pic_src,bananer_type_id')->select();
		}else if($type_id == '酒店顶部轮播'){
			$id = M('bananer_type')->where(array('type_name'=>'酒店顶部轮播'))->getField('bananer_type_id');
			$condition = array('bananer_type_id'=>$id,'is_del'=>0);
	 		$data = $this->where($condition)->field('pic_src,bananer_type_id')->select();
		}
		return $data;
	 }
	
}
