<?php namespace Admin\Model;
use Think\Model;

class PictureModel extends Model{
	protected $tableName="picture";
	
	/**
	 * 自动验证
	 */
	protected $_validate = array(
     array('url','require','图片内容必须存在！'), //默认情况下用正则进行验证
     array('thumb','require','图片内容必须存在！'), //默认情况下用正则进行验证
     array('addtime','require','验证码必须！',2), //默认情况下用正则进行验证
     array('title','require','标题必须！'), //默认情况下用正则进行验证
     array('keyword','require','关键词必须！'), //默认情况下用正则进行验证
     array('lables','require','标签必须！'), //默认情况下用正则进行验证
     array('click_num','require','点赞数必须！'), //默认情况下用正则进行验证
     array('collect_num','require','转载数量数必须！'), //默认情况下用正则进行验证
     array('show_num','require','前台显示张数必须！'), //默认情况下用正则进行验证
     array('merchant_merchant_id','require','商家必须勾选必须！',2), //默认情况下用正则进行验证
     array('merchant_name','require','商家名称必须！',2), //默认情况下用正则进行验证
     array('merchant_logo','require','商家logo必须！',2), //默认情况下用正则进行验证
   );
	/**
	 * 创建添加瀑布流方法
	 */
	public function store($data){
		$data['author_id'] = $_SESSION['admin_id'];
		$data['addtime'] = time();
		$data['lables'] = implode('|', $data['lables']);
		if(count($data['url']) != count($data['thumb'])){
			$this->error = '数据异常';
			return FALSE;
		};
		$data['url'] = implode("|", $data['url']);
		$data['thumb'] = implode("|", $data['thumb']);
		if(!$this->create($data)) return FALSE;
		$re = $this->add($this->data);
		return $re;
	}
	
	/**
	 * 创建获取瀑布流数据方法
	 */
	 public function getData(){
	 	$data = $this->where(array('is_del'=>0))->order('p_id DESC')->select();
		foreach($data as $k=>&$v){
			$v['author_id'] = M('admin')->where(array('admin_id'=>$v['author_id']))->getField('admin_name');
			$v['keyword'] = explode("#", $v['keyword']);
			$v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
			$v['url'] = explode('|', $v['url']);
			$v['lables'] = explode("|", $v['lables']);
			foreach($v['lables'] as $key=>&$value){
				$value = M('lable')->where(array('lid'=>$value))->getField('lname');
			}
			$v['type_tid'] = M('type')->where(array('tid'=>$v['type_tid']))->getField('tname');
			
			if($v['merchant_merchant_id']){
				$v['merchant_name'] = M('merchant')->where(array('merchant_id'=>$v['merchant_merchant_id']))->getField('m_name');
			}
		}
		return $data;
	 }
	
}
