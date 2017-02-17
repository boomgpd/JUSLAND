<?php namespace Merchant\Controller;
use Think\Controller;
use Merchant\Model\Merchant_caseModel;
use Merchant\Model\Merchant_comboModel;
use Merchant\Model\Merchant_messageModel;
use Common\Model\Merchant_remarkModel;
use Common\Model\Merchant_discussModel;
/**
 * 商家店铺管理控制器
 * boom
 * 08/12
 */
class DecorateController extends CommonController {
	protected $merchant_case;
	protected $merchant_combo;
	protected $merchant_message;
	protected $merchant_remark;
	protected $merchant_discuss;
	
	public function __construct(){
		parent::__construct();
		$this->merchant_case = new Merchant_caseModel;
		$this->merchant_combo = new Merchant_comboModel;
		$this->merchant_message = new Merchant_messageModel;
		$this->merchant_remark = new Merchant_remarkModel;
		$this->merchant_discuss = new Merchant_discussModel;
	}
	
	/**
	 * 创建店铺装修主页面
	 */
	public function index(){
		/**
		 * 1.获取商户表信息
		 * 2.获取商家案例信息
		 * 3.获取商家套餐信息
		 * 4.商家问答信息
		 * 5.会员点评信息（有交易记录的可以评论，后台可以单独添加）
		 * 6.获取用户问答信息(后台可以独立添加)
		 */
		 $data = array();
		 $data['merchant'] = M('merchant')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
		 $data['merchant_message'] = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
		 $data['merchant_case'] = $this->merchant_case->getData();
		 $data['merchant_combo'] = $this->merchant_combo->getData();
		 $data['merchant_remark'] = $this->merchant_remark->getData($_SESSION['merchant_id']);
		 $data['merchant_discuss'] = $this->merchant_discuss->getData($_SESSION['merchant_id']);
//		 dump($data['merchant_remark']);die;
		 $this->assign('data',$data);
		 $this->display();
	}
	
	/**
	 * 创建修改logo方法
	 */
	public function change_logo(){
		if(IS_POST){
			$data = I('post.');
			$name = key($data);
//			dump($name);die;
			$re = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
//			dump($re);die;
			if($re){
				$result = M('merchant_message')->where(array('merchant_message_id'=>$re['merchant_message_id']))->save(array($name=>$data[$name]));    
			}else{
				$arr = array(
					$name =>$data[$name],
					'merchant_id'=>$_SESSION['merchant_id'],
				);
				$result = M('merchant_message')->add($arr);
			}
			
			if(!$result){
				echo '<script type="text/javascript">alert("上传失败");</script>';
			}else{
				echo '<script type="text/javascript">alert("上传成功");window.location.href = "'.U('Decorate/index').'";</script>';
			}
			
		}
	}
	
	public function change_logo_yuan(){
		$data = M('merchant_message')->where(array('merchant_id'))->getField('logo_yuan');
		$this->assign('data',$data);
		$this->display();
	}
	
	public function change_logo_fang(){
		$data = M('merchant_message')->where(array('merchant_id'))->getField('logo_fang');
		$this->assign('data',$data);
		$this->display();
	}
	
	public function change_list_img(){
		$data = M('merchant_message')->where(array('merchant_id'))->getField('img_list');
		$this->assign('data',$data);
		$this->display();
	}

	/**
	 * 创建修改营业时间方法
	 */
	 public function change_merchant_sale_time(){
	 	if(IS_POST){
	 		$data = I('post.');
			$re = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
	 		if($re){
	 			$result = M('merchant_message')->where(array('merchant_message_id'=>$re['merchant_message_id']))->save($data);
	 		}else{
	 			$arr = $data;
	 			$arr['merchant_id'] = $_SESSION['merchant_id'];
				$result = M('merchant_message')->add($arr);
	 		}
			if(!$result){
				echo '<script type="text/javascript">alert("上传失败");</script>';
			}else{
				echo '<script type="text/javascript">alert("上传成功");window.location.href = "'.U('Decorate/index').'";</script>';
			}
		}

		$this->display();
	 }
	

	/**
	 * 构建php的上传logo方法
	 */
  	public function upload(){
  		$name = I('get.name');
	    $upload = new \Think\Upload();// 实例化上传类
	    $upload->maxSize   =     3145728 ;// 设置附件上传大小
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath  =     __ROOT__; // 设置附件上传根目录
	    $upload->savePath  =     $name.'/'; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    if (empty($info)) {
	        echo json_encode($this->error());exit;
	    } else {
	        //上传成功，把上传好的信息返给js
	        $data = '/Uploads/'.$info['Filedata']['savepath'].$info['Filedata']['savename'];
	        echo json_encode($data);exit;
	    }
	}

	/**
	 * 创建修改案例
	 */
	public function change_merchant_case(){
		if(IS_POST){
			$data = I('post.');
			$re = $this->merchant_case->store($data);
			
			if(!$re){
				$error = $this->merchant_case->getError();
				echo '<script type="text/javascript">alert("'.$error.'");</script>';
			}else{
				echo '<script type="text/javascript">alert("案例修改成功");window.location.href = "'.U('Decorate/change_merchant_case').'";</script>';
			}
		}else{
			$data = $this->merchant_case->getData();
			$this->assign('data',$data);
			$this->display();
		}
		
	}
	
	/**
	 * 创建显示案例方法
	 */
	public function show_merchant_case(){
		if(IS_POST){
			$data = I('post.');
			$re = $this->merchant_case->edit_sort($data);
			if(!$re){
				$error = $this->merchant_case->getError();
				echo '<script type="text/javascript">alert("'.$error.'");</script>';
			}else{
				echo '<script type="text/javascript">alert("案例修改成功");window.location.href = "'.U('Decorate/show_merchant_case').'";</script>';
			}
		}else{
			$data = $this->merchant_case->getData();
			$this->assign('data',$data);
			$this->display();
		}
	}
	
	
	/**
	 * 创建修改套餐
	 */
	 public function change_merchant_combo(){
	 	if(IS_POST){
	 		$data = I('post.');
			$re = $this->merchant_combo->store($data);
			if(!$re){
				$error = $this->merchant_case->getError();
				echo '<script type="text/javascript">alert("'.$error.'");</script>';
			}else{
				echo '<script type="text/javascript">alert("套餐修改成功");window.location.href = "'.U('Decorate/change_merchant_combo').'";</script>';
			}
	 	}else{
	 		$this->display();
	 	}
	 }
	 
	 /**
	  * 创建显示套餐方法
	  */
	  public function show_merchant_combo(){
	  	if(IS_POST){
	  		$data = I('post.');
			$re = $this->merchant_combo->edit_sort($data);
			if(!$re){
				$error = $this->merchant_combo->getError();
				echo '<script type="text/javascript">alert("'.$error.'");</script>';
			}else{
				echo '<script type="text/javascript">alert("套餐修改成功");window.location.href = "'.U('Decorate/show_merchant_combo').'";</script>';
			}
	  	}else{
	  		$data = $this->merchant_combo->getData();
			$this->assign('data',$data);
	  		$this->display();
	  	}
	  }
	 
	 
	 /**
	  * 创建修改商家基本信息方法
	  */
	  public function change_merchant_message(){
		  	if(IS_POST){
		  		$data = I('post.');
				$re = M('merchant')->save(array('merchant_id'=>$_SESSION['merchant_id'],'m_name'=>$data['m_name']));
				if(!$re && $re!=0){
					echo '<script type="text/javascript">alert("修改失败");</script>';
				}else{
					unset($data['m_name']);
					$re = $this->merchant_message->chenge($data);
					if(!$re && $re!=0){
						$error = $this->merchant_message->getError();
						echo '<script type="text/javascript">alert("'.$error.'");</script>';
					}else{
							echo '<script type="text/javascript">alert("套餐修改成功");window.location.href = "'.U('Decorate/index').'";</script>';
					}
				}
		  	}else{
		  		$data['m_name'] = M('merchant')->where(array('merchant_id'=>$_SESSION['merchant_id']))->getField('m_name');
		  		$data['message'] = $this->merchant_message->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
		  		$this->assign('data',$data);
		  		$this->display();
		  	}
	  }
	 
	 
	 /**
	  * 创建评论用户信息方法
	  */
	  public function change_merchant_remark(){
	  	/**
		 * 1.获取该商家的所有点评数据
		 * 2.排序方式按添加时间和是否回复排序
		 */
		 $data['question'] = M('merchant_remark')->order('addtime')->where(array('merchant_id'=>$_SESSION['merchant_id'],'replay_content'=>''))->select();
		 foreach($data['qustione'] as $k=>&$v){
		 	$v['addtime'] = date('Y-m-d',$v['addtime']);
			$v['replay_time'] = date('Y-m-d',$v['replay_time']);
		 }
		 
		 $data['replay'] = M('merchant_remark')->order('addtime')->where(array('merchant_id'=>$_SESSION['merchant_id'],'replay_content'=>array('neq','')))->select();
		 foreach($data['replay'] as $k=>&$v){
		 	$v['addtime'] = date('Y-m-d',$v['addtime']);
			 $v['replay_time'] = date('Y-m-d',$v['replay_time']);
		 }
		 
		$this->assign('data',$data);
		$this->display();
	  }
	  
	  
	  /**
	   * 创建回复点评方法
	   */
	   public function replay_remark(){
	   	if(IS_POST){
	   		$data = I('post.');
			$data['replay_time'] = time();
			$re = M('merchant_remark')->save($data);
			if(!$re){
				echo '<script type="text/javascript">alert("回复失败");</script>';
			}else{
				echo '<script type="text/javascript">alert("回复成功");window.location.href = "'.U('Decorate/change_merchant_remark').'";</script>';
			}
	   	}
	   		$id = I('get.id');
			$data = M('merchant_remark')->where(array('merchant_remark_id'=>$id))->find();
			$data['addtime'] = date('Y-m-d H:i:s',$data['addtime']);
			$data['list_img'] = explode('|', $data['list_img']);
			$this->assign('data',$data);
			$this->display();
	   }
	   
	   /**
	    * 创建显示用户提问方法
	    */
	    public function change_merchant_discuss(){
	    	/**
			 * 获取所有用户问题
			 * 排序按添加时间
			 */
			 $data['question'] = M('merchant_discuss')->order('addtime')->where(array('replay_content'=>''))->select();
			 $data['replay'] = M('merchant_discuss')->order('addtime')->where(array('replay_content'=>array('neq','')))->select();
			 foreach($data['question'] as $k=>&$v){
			 	$v['addtime'] = date('Y-m-d',$v['addtime']);
				$v['replay_time'] = date('Y-m-d',$v['replay_time']);
			 }

			 foreach($data['replay'] as $k=>&$v){
			 	$v['addtime'] = date('Y-m-d',$v['addtime']);
				$v['replay_time'] = date('Y-m-d',$v['replay_time']);
			 }
			 $this->assign('data',$data);
	    	 $this->display();
	    }
	    
	/**
	   * 创建回复点评方法
	   */
	   public function replay_discuss(){
	   	if(IS_POST){
	   		$data = I('post.');
			$data['replay_time'] = time();
			$re = M('merchant_discuss')->save($data);
			if(!$re){
				echo '<script type="text/javascript">alert("回复失败");</script>';
			}else{
				echo '<script type="text/javascript">alert("回复成功");window.location.href = "'.U('Decorate/change_merchant_discuss').'";</script>';
			}
	   	}
	   		$id = I('get.id');
			$data = M('merchant_discuss')->where(array('merchant_discuss_id'=>$id))->find();
			$data['addtime'] = date('Y-m-d H:i:s',$data['addtime']);
			$this->assign('data',$data);
			$this->display();
	   }

}