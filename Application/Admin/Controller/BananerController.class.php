<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\BananerModel;

/**
 * 创建后台轮播图控制器
 * 16/08/03
 * boom
 */
class BananerController extends CommonController {
	protected $bananer;
	
	public function __construct(){
		parent::__construct();
		$this->bananer = new BananerModel;
	}
	
	
    public function index(){
    	$type_id = i('get.type_id');
    	$data = $this->bananer->getData($type_id);
		foreach($data as $k=>&$v){
			$v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
			$v['admin_id'] = M('admin')->where(array('admin_id'=>$v['admin_id']))->getField('admin_name');
			$v['type_name'] = M('bananer_type')->where(array('bananer_type_id'=>$v['bananer_type_id']))->getField('type_name');
			$v['pic_src'] = C('__UPLOAD__').$v['pic_src'];
		}
		$this->assign('data',$data);
		$type = M('bananer_type')->select();
		$this->assign('type',$type);
		$this->display();
    }
	
	/**
	 * 创建保存方法
	 */
	 public function save(){
	 	$data = I('post.');
		foreach($data['bananer_id'] as $k=>$v){
			$arr = array('bananer_id'=>$data['bananer_id'][$k],'url'=>$data['url'][$k]);
			$re = $this->bananer->save($arr);
			if(!$re && $re != 0){
				echo '<script type="text/javascript">alert("保存失败");history.back(0);</script>';
			}
		}
		
		echo '<script type="text/javascript">alert("保存成功");location.replace(document.referrer);</script>';
	 }
	
	/**
	 * 创建添加方法
	 */
	public function add(){
		if(IS_POST){
			$data = I('post.');
			foreach($data['pic_src'] as $k=>$v){
				$arr = array('pic_src'=>$v,'bananer_type_id'=>$data['bananer_type_id'],'admin_id'=>$_SESSION['admin_id'],'addtime'=>time(),'url'=>$data['url']);
				$re = $this->bananer->store($arr);
				if(!$re){
					$error = $this->bananer->getError();
					echo '<script type="text/javascript">alert("'.$error.'");window.location.href = "'.U('add').'"</script>';
				}
			}
			echo '<script type="text/javascript">alert("添加成功");window.location.href = "'.U('index').'"</script>';
		}else{
			/**
			 * 获取分类信息
			 */
			$bananer_type = M('bananer_type')->select();
			$this->assign('bananer_type',$bananer_type);
			$this->display();
		}
    }
	
	/**
	 * 构建php的上传方法
	 */
  	public function upload(){
	    $upload = new \Think\Upload();// 实例化上传类
	    $upload->maxSize   =     3145728 ;// 设置附件上传大小
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath  =     __ROOT__; // 设置附件上传根目录
	    $upload->savePath  =     'Bananer/'; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    if (empty($info)) {
	        echo json_encode($this->error());exit;
	    } else {
	        //上传成功，把上传好的信息返给js
	        $data = $info['Filedata']['savepath'].$info['Filedata']['savename'];
	        echo json_encode($data);exit;
	    }
	}
	
	/**
	 * 创建删除方法
	 */
	 public function del(){
	 	$id = I('get.id');
		$re = M('bananer')->save(array('bananer_id'=>$id,'is_del'=>1));
		redirect(U('index'));
	 }
	 
	
}