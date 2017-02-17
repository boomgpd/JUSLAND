<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\AreaModel;
/**
 * 创建后台地区控制器
 * 16/08/03
 * boom
 */
class AreaController extends CommonController {
	/**
	 * 创建成员属性
	 */
	 protected $area;
	
	/**
	 * 创建构造函数
	 */
	 public function __construct(){
	 	parent::__construct();
		$this->area = new AreaModel;
	 }
	
	/**
	 * 创建地区列表
	 */
	public function show(){
		$pid = I('get.pid');
		/**
		 * 获取对应地区信息
		 * 将时间格式化为字符串格式
		 */
		$areaData = $this->area->getData($pid);
		foreach($areaData as $k=>&$v){
			$v['addtime'] = date('Y-m-d H:i:s');
		}
		$this->assign('data',$areaData);
		$this->display();
	}
	
	/**
	 * 创建编辑方法
	 */
	 public function edit(){
	 	if(!IS_AJAX)	return FALSE;
		$data = I('post.');
		$re = M('area')->save($data);
		if(!$re){
			echo '编辑成功';
		}else{
			echo '编辑失败';
		}
		
	 }
	 
	 /**
	 * 创建删除方法
	 */
	 public function del(){
	 	if(!IS_AJAX)	return FALSE;
		$data['area_id'] = I('post.area_id');
		$re = M('area')->where(array('area_id'=>$data['area_id']))->delete();
		if(!$re){
			echo '删除失败';
		}else{
			echo '删除成功';
		}
		
	 }
	 
	 /**
	  * 创建添加省份方法
	  */
	 public function add(){
		$data = M('area')->where(array('pid'=>0,'type'=>1,'is_del'=>0))->select();
		
	 	if(IS_POST){
	 		$data = I("post.");
//			dump($data);die;
			if(!$data['area']){
				echo '<script type="text/javascript">alert("请填写对应地区");history.back(0);</script>';
					
			}else if(!$data['province'] && !$data['city']){
//				echo 1;die;
					$arr = array(
						'type'	=>	1, 
						'addtime' => time(),
						'pid'	=>	0,
						'is_del' => 0
					);
					$data['area'] = explode('|',$data['area']);
					foreach($data['area'] as $k=>$v){
						$arr['aname'] = $v;
//						dump($arr);
						M('area')->add($arr);
					}
					echo '<script type="text/javascript">alert("添加成功");history.back(0);</script>';
			}else if($data['province'] && !$data['city']){
				$arr = array(
						'type'	=>	2, 
						'addtime' => time(),
						'pid'	=>	$data['province'],
						'is_del' => 0
					);
					$data['area'] = explode('|',$data['area']);
					foreach($data['area'] as $k=>$v){
						$arr['aname'] = $v;
//						dump($arr);
						M('area')->add($arr);
					}
					echo '<script type="text/javascript">alert("添加成功");history.back(0);</script>';
			}else if($data['province'] && $data['city']){
				$arr = array(
						'type'	=>	3, 
						'addtime' => time(),
						'pid'	=>	$data['city'],
						'is_del' => 0
					);
					$data['area'] = explode('|',$data['area']);
					foreach($data['area'] as $k=>$v){
						$arr['aname'] = $v;
//						dump($arr);
						M('area')->add($arr);
					}
					echo '<script type="text/javascript">alert("添加成功");history.back(0);</script>';
			}
	 	}
		$this->assign('data',$data);
		$this->display();
	 }
	 
	 /**
	  * 创建异步获取城市信息方法
	  */
	  public function step(){
		if(!IS_AJAX) return FALSE;
		$pid = I("post.pid");
		if(!$pid){
			$data = FALSE;
		}else{
			$data = $this->area->getData($pid);
		}
		$this->ajaxReturn($data);exit;
	  }
	
	
}