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
		 $data['merchant_case'] = $this->merchant_case->getData($_SESSION['merchant_id']);
		 $data['merchant_combo'] = $this->merchant_combo->getData($_SESSION['merchant_id']);
		 $data['merchant_remark'] = $this->merchant_remark->getData($_SESSION['merchant_id']);
		 $data['merchant_discuss'] = $this->merchant_discuss->getData($_SESSION['merchant_id']);
//		 dump($data['merchant_remark'][0]['list_img']);die;
		 $remark_num = count($data['merchant_remark']);
		 $this->assign('remark_num',$remark_num);
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
		$data = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->getField('logo_fang');
		$this->assign('data',$data);
		$this->display();
	}
	
	public function change_list_img(){
		$data = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->getField('img_list');
		$this->assign('data',$data);
		$this->display();
	}
	
	public function change_logo_list(){
		$data = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->getField('logo_list');
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
	        $data = $info['Filedata']['savepath'].$info['Filedata']['savename'];
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
				echo '<script type="text/javascript">alert("'.$error.'");window.location.href = "'.U('Decorate/change_merchant_case').'";</script>';
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
			$data = $this->merchant_case->getData($_SESSION['merchant_id']);
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
	  		$data = $this->merchant_combo->getData($_SESSION['merchant_id']);
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
		  		$number = '';
		  		if(isset($data['provience']) && isset($data['city'])){
		  			$model = new \Home\Controller\CommonController;
		  			$num = M('Merchant')->where(array('provience'=>$data['provience'],'city'=>$data['city'],'is_apply'=>1,'is_del'=>0))->count()+1;
		  			$num = str_pad($num,3,0,STR_PAD_LEFT);
		  			$number = $model->_getFirstCharter(M('Area')->where(array('area_id'=>$data['provience']))->getField('aname')).'-'.$model->_getFirstCharter(M('Area')->where(array('area_id'=>$data['city']))->getField('aname')).'-'.$num;
		  		};
				$re = M('merchant')->save(array('merchant_id'=>$_SESSION['merchant_id'],'provience'=>$data['provience'],'city'=>$data['city'],'number'=>$number));
				if(!$re && $re!==0){
					echo '<script type="text/javascript">alert("修改失败");window.location.href = "'.U('Decorate/change_merchant_message').'";</script>';
				}else{
					unset($data['m_name']);
					$re = $this->merchant_message->chenge($data);
					if(!$re && $re!==0){
						$error = $this->merchant_message->getError();
						echo '<script type="text/javascript">alert("'.$error.'");window.location.href = "'.U('Decorate/change_merchant_message').'";</script>';
					}else{
						echo '<script type="text/javascript">alert("基本信息修改成功");window.location.href = "'.U('Decorate/change_merchant_message').'";</script>';
					}
				}
		  	}else{
		  		
		  		$data['self'] = M('merchant')->field('m_name,provience,city')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
		  		$data['message'] = $this->merchant_message->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
		  		$this->assign('data',$data);
//				dump($data);die;
				
				$area['provience'] = M('area')->where(array('pid'=>0))->select();
				$area['city'] = M('area')->where(array('pid'=>$data['self']['provience']))->select();
				$this->assign('area',$area);
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
			$check = $this->check_replay($data['replay_content']);
			if(!$check){
				echo '<script type="text/javascript">alert("回复内容中不得包含数字");window.location.href = "'.U('Decorate/replay_remark',array('id'=>$data['merchant_remark_id'])).'"</script>';
			}else{
				$re = M('merchant_remark')->save($data);
				if(!$re){
					echo '<script type="text/javascript">alert("回复失败");</script>';
				}else{
					echo '<script type="text/javascript">alert("回复成功");window.location.href = "'.U('Decorate/change_merchant_remark').'";</script>';
				}
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
			$check = $this->check_replay($data['replay_content']);
			if(!$check){
				echo '<script type="text/javascript">alert("回复内容中不得包含数字");window.location.href = "'.U('Decorate/replay_discuss',array('id'=>$data['merchant_discuss_id'])).'"</script>';
			}else{
				$re = M('merchant_discuss')->save($data);
				if(!$re){
					echo '<script type="text/javascript">alert("回复失败");</script>';
				}else{
					echo '<script type="text/javascript">alert("回复成功");window.location.href = "'.U('Decorate/change_merchant_discuss').'";</script>';
				}
			}
			
	   	}
	   		$id = I('get.id');
			$data = M('merchant_discuss')->where(array('merchant_discuss_id'=>$id))->find();
			$data['addtime'] = date('Y-m-d H:i:s',$data['addtime']);
			$this->assign('data',$data);
			$this->display();
	   }
	   
	   
	   /**
	    * 创建显示案例列表方法
	    */
	    public function list_merchant_case(){
			$data = $this->merchant_case->getData($_SESSION['merchant_id']);
			$this->assign('data',$data);
			$this->display();
	    }

		/**
		 * 创建删除案例方法
		 */
		 public function case_del(){
		 	$case_id = I('get.case_id');
			$re = $this->merchant_case->save(array('merchant_case_id'=>$case_id,'is_del'=>1));
			if(!$re){
				echo '<script type="text/javascript">alert("删除失败");window.location.href = "'.U('list_merchant_case').'"</script>'; 
			}else{
				echo '<script type="text/javascript">alert("删除成功");window.location.href = "'.U('list_merchant_case').'"</script>';
			}
		 }
		 
		 /**
		  * 创建编辑案例方法
		  */
		 public function edit_case(){
		 	if(IS_POST){
		 		$data = I('post.');
				$data['img_url'] = implode("|", $data['img_url']);
//				dump($data);die;
				$re = $this->merchant_case->save($data);
//				dump($re);die;
				if(!$re && $re != 0){
					echo '<script type="text/javascript">alert("保存失败");</script>'; 
				}else{
					echo '<script type="text/javascript">alert("保存成功");window.location.href = "'.U('list_merchant_case').'"</script>'; 
				}
		 	}else{
		 		$case_id = I('get.case_id');
			 	$data = $this->merchant_case->where(array('merchant_case_id'=>$case_id))->find();
				$data['img_url'] = explode("|", $data['img_url']);
				$this->assign('data',$data);
	//			dump($data);die;
				$this->display();
		 	}
		 }
		 
		 
		
		/**
	    * 创建显示套餐列表方法
	    */
	    public function list_merchant_combo(){
			$data = $this->merchant_combo->getData($_SESSION['merchant_id']);
			$this->assign('data',$data);
			$this->display();
	    }


		/**
		 * 创建删除套餐方法
		 */
		 public function combo_del(){
		 	$combo_id = I('get.combo_id');
			$re = $this->merchant_combo->save(array('merchant_combo_id'=>$combo_id,'is_del'=>1));
			if(!$re){
				echo '<script type="text/javascript">alert("删除失败");window.location.href = "'.U('list_merchant_case').'"</script>'; 
			}else{
				echo '<script type="text/javascript">alert("删除成功");window.location.href = "'.U('list_merchant_case').'"</script>';
			}
		 }

		/**
		 * 创建套餐编辑方法
		 */
		 public function combo_edit(){
		 	if(IS_POST){
		 		$data = I('post.');
				$data['img_url'] = implode("|", $data['img_url']);
//				dump($data);die;
				$re = $this->merchant_combo->save($data);
//				dump($re);die;
				if(!$re && $re != 0){
					echo '<script type="text/javascript">alert("保存失败");</script>'; 
				}else{
					echo '<script type="text/javascript">alert("保存成功");window.location.href = "'.U('list_merchant_combo').'"</script>'; 
				}
		 	}else{
		 		$combo_id = I('get.combo_id');
				$data = $this->merchant_combo->where(array('merchant_combo_id'=>$combo_id))->find();
				$data['img_url'] = explode("|", $data['img_url']);
				$this->assign('data',$data);
				$this->assign('combo_id',$combo_id);
				$this->display();
		 	}
		 }
		 
		/**
		 * 创建正则验证方法
		 * 验证回复内容是否包含数字
		 */
		public function check_replay($data){
			
			if( preg_match('/\\d+/',$data,$matchs1) == 1){
			    return FALSE;
			}else {
			    return TRUE;
			}
			
		}
		/**
		 * 创建当前商家的已选价格区间的方法
		 */
		public function index_range(){
			if(IS_AJAX){//删除
				$data = I('post.del');
				$re = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
				$lid = explode('|',$re['server_prices_id']);
					foreach( $lid as $k=>$v) {
    					if($data == $v){
    						unset($lid[$k]);
    					}	
					}
				$arr['server_prices_id'] = implode('|',$lid);
				$re = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->save($arr);
				$this->ajaxReturn($re);exit;
			}
			$data = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
			if($data['server_prices_id']){
				$bol  = explode('|',$data['server_prices_id']);
				foreach ($bol as $key => $value) {
					$re[] = M('merchant_server_price')->where(array('server_price_id'=>$value))->find();
				}
			}else{
				$re = false;
			}
			$this->assign('re',$re);
			$this->display();
		}
		/**
		 * 创建选择价格区间的方法
		 */
		public function choice_range(){
			if(IS_POST){
				$data = I('post.');
				if(!$data['li']){
					echo '<script type="text/javascript">alert("请勾选对应价格区间");</script>'; 
				}else{
					$oldData = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->getField('server_prices_id');
					if($oldData){
						$oldData = explode("|",$oldData);
						$dat['server_prices_id'] = array_merge($data['li'],$oldData);
					}else{
						$dat['server_prices_id'] = $data['li'];
					}
					$dat['server_prices_id'] = implode('|',$dat['server_prices_id']);
					$re = M('merchant_message')->where(array("merchant_id"=>$data['merchant_id']))->save($dat);
					if(!$re){
						echo '<script type="text/javascript">alert("选取失败");</script>'; 
					}else{
						echo '<script type="text/javascript">alert("选取成功");window.location.href = "'.U('index_range').'";</script>';
					}
				}
			}
			
			
			$data = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
			$bol  = explode('|',$data['server_prices_id']);
			$box  = implode(',',$bol);
			$where['server_price_id'] = array('not in',$box);
			$data   = M('merchant_server_price')->where($where)->select();
			$this->assign('data',$data);
			$this->display();
		}
		
	/**
	 * 创建获取对应省份的市区
	 */
	public function step_area(){
		if(!IS_AJAX) return FALSE;
		$pid = I('post.pid');
		if($pid == 0){
			$this->ajaxReturn(FALSE);exit;
		}
		
		$data = M('area')->where(array('pid'=>$pid))->select();
		if($data){
			$this->ajaxReturn($data);exit;
		}else{
			$this->ajaxReturn(FALSE);exit;
		}
		
	}
}