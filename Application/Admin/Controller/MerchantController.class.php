<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\AreaModel;
use Common\Model\MerchantModel;
use Common\Model\Merchant_remarkModel;
use Common\Model\Merchant_discussModel;
use Common\Model\Merchant_chooseModel;

/**
 * 创建后台商家控制器
 * 16/08/10
 * boom
 */
class MerchantController extends CommonController {
	protected $area;
	protected $merchant;
	protected $merchant_remark;
	protected $merchant_discuss;
	
	/**
	 * 创建构造函数
	 */
	 public function __construct(){
	 	parent::__construct();
		$this->area = new AreaModel;
		$this->merchant = new MerchantModel;
		$this->merchant_remark = new  Merchant_remarkModel;
		$this->merchant_discuss = new Merchant_discussModel;
		$this->merchant_choose = new Merchant_chooseModel;
	 }
	
	
	/**
	 * 创建商家显示方法
	 */
    public function index(){
    	/**
		 * 1.获取审核通过的商家信息
		 * 2.通过循环将地区信息转化为字符内容
		 * 3.分配数据
		 */
    	$data = $this->merchant->where(array('is_del'=>0,'is_apply'=>1))->select();
		foreach($data as $k=>&$v){
			$area[] = $v['provience'];
			$area[] = $v['city'];
			foreach($area as $key=>&$value){
				$value = M('area')->where(array('area_id'=>$value))->getField('aname');
			}
			$v ['area']= implode(',', $area);
		};
		$this->assign('data',$data);
		$this->display();
    }
	
	/*
	 * 首页推荐方法
	 * */
	 public function choose(){
	 	if(IS_POST){
	 		$post = I('post.');
			$result = $this->merchant_choose->edit($post);
			if(!$result){
				alert($this->merchant_choose->getError());die;
			};
			alert('修改成功',U('Merchant/choose'));die;
	 	};
	 	//获得所有推荐数据
	 	$data = $this->merchant_choose->alias('mc')->join('merchant m ON mc.merchant_id = m.merchant_id')->order('rank ASC')->select();
		$this->assign('data',$data);
	 	$this->display();
	 }
	 
	 /*
	 * 添加推荐方法
	 * */
	 public function add_choose(){
	 	if(IS_POST){
	 		$post = I('post.');
			if(!isset($post['keyword'])){
				$result = $this->merchant_choose->store($post);
				if(!$result){
					alert($this->merchant_choose->getError());die;
				};
				alert('添加推荐成功',U('Merchant/choose'));die;
			};	
	 	};
	 	$chids = $this->merchant_choose->getField('merchant_id',true);
		$condition = array(
			'is_del'=>0,
			'is_apply'=>1
		);
		if(!empty($chids)){
			$chids = implode(',',$chids);
			$condition['merchant_id'] = array('NOT IN',$chids);
		};
		if(IS_POST){
			if(isset($_POST['keyword'])){
				$condition['m_name'] = array('LIKE','%'.$_POST['keyword'].'%');
			};
		};
	 	$data = $this->merchant->where($condition)->select();
		foreach($data as $k=>&$v){
			$area[] = $v['provience'];
			$area[] = $v['city'];
			foreach($area as $key=>&$value){
				$value = M('area')->where(array('area_id'=>$value))->getField('aname');
			}
			$v ['area']= implode(',', $area);
		};
		$this->assign('data',$data);
	 	$this->display();
	 }

	/*
	 * 编辑推荐
	 * */
	 public function edit_choose($id=NULL){
	 	if(empty($id) || !IS_AJAX) return FALSE;
		//获得没有添加的排名和星级
		$data = array();
		$data['rank'] = $this->merchant_choose->where(array('merchant_choose_id'=>$id))->getField('rank');
		$data['level'] = $this->merchant_choose->where(array('merchant_choose_id'=>$id))->getField('level');
		$this->ajaxReturn($data);die;
	 }

	//删除推荐
	public function del_choose($id=NULL){
		if(empty($id)) return FALSE;
		$this->merchant_choose->where(array('merchant_choose_id'=>$id))->delete();
		alert('删除推荐成功',U('Merchant/choose'));
	}
	
	/**
	 * 创建商家添加方法
	 */
	 public function add(){
	 	if(IS_POST){
		 	$data = I('post.');
			$re = $this->merchant->store($data);
			if(!$re){
				$error = $this->merchant->getError();
				echo '<script type="text/javascript">alert("'.$error.'");location.href = "'.U('add').'";</script>';
			}else{
				echo '<script type="text/javascript">alert("添加成功");location.href = "'.U('add').'";</script>';
			}
	 	}
		
		$this->display();
	 }
	 
	 /**
	  * 创建商家审核方法
	  */
	  public function apply(){
	  	/**
		 * 1.获取审核未通过的商家信息
		 * 2.通过循环将地区信息转化为字符内容
		 * 3.分配数据
		 */
	  	$data = $this->merchant->where(array('is_del'=>0,'is_apply'=>0))->select();
		foreach($data as $k=>&$v){
			$area[] = $v['provience'];
			$area[] = $v['city'];
			foreach($area as $key=>&$value){
				$value = M('area')->where(array('area_id'=>$value))->getField('aname');
			}
			$v ['area']= implode(',', $area);
		};
		$this->assign('data',$data);
		$this->display();
	  }
	 
	 
	 /**
	  * 创建获取地址异步方法
	  */
	public function step_area(){
		if(!IS_AJAX) return FALSE;
		$pid = I('post.pid');
		$data = $this->area->getData($pid);
		$this->ajaxReturn($data);exit;
	}
	 
	 
	 /**
	  * 创建异步审核方法
	  */
	  public function audit(){
	  	/**
		 * 1.判定请求方式
		 * 2.获取传输数据
		 * 3.调用模型，更新字段
		 */
	  	if(!IS_AJAX) return FALSE;
		$data = I('post.');
		$re = D('Merchant')->audit($data);
		if(!$re){
			$this->ajaxReturn(array('type'=>'0','content'=>'审核失败'));exit;
		}else{
			$this->ajaxReturn(array('type'=>'1','content'=>'审核成功'));exit;
		}
		
	  }
	  
	  /**
	   * 创建删除商家方法
	   */
	   public function del(){
	   	if(!IS_AJAX) return FALSE;
		/**
		 * 获取主键
		 * 调用模型更新对应字段
		 * 判定返出结果
		 */
		 $id = I('post.id');
		 $re = D('Merchant')->del($id);
		 if(!$re){
		 	$this->ajaxReturn(array('type'=>0,'content'=>'删除失败'));exit;
		 }else{
		 	$this->ajaxReturn(array('type'=>1,'content'=>'删除成功'));exit;
		 }
		
	   }
	   
	   /**
	    * 创建商家点评后台显示方法
	    */
	    public function remark(){
	    	/**
			 * 获取所有商家信息以及点评条数
			 */
			 $data = $this->merchant->getData();
			 foreach($data as $k=>&$v){
			 	$v['remark_num'] = M('merchant_remark')->where(array('merchant_id'=>$v['merchant_id']))->count();
			 	$v['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
			 }
			 $this->assign('data',$data);	
			 $this->display();
	    }
	   
	   /**
	    * 创建后台商家评论添加方法
	    */
	    public function remark_add(){
	    	/**
			 * 获取到传输的post数据
			 * 调用模型添加方法
			 * 判定返出结果
			 */
	    	if(IS_POST){
	    		$data = I('post.');
//				dump($data);die;
				$re = $this->merchant_remark->store($data);
				if(!$re){
				$error = $this->merchant_remark->getError();
					echo '<script type="text/javascript">alert("'.$error.'");</script>';
				}else{
					echo '<script type="text/javascript">alert("添加评论成功。");location.href = "'.U('remark_add',array('merchant_id'=>$data['merchant_id'])).'";</script>';
				}
	    	}else{
//	    		$data = M('merchant_remark_type')->order('addtime')->select();
//				foreach($data as $k=>&$v){
//					$v['type_select'] = explode('|', $v['type_select']);
//				}
//				$this->assign('data',$data);
				$merchant_id = I('get.merchant_id');
				$this->assign('merchant_id',$merchant_id);
	    		$this->display();
	    	}
	    }
	    
		/**
		 * 创建显示公司具体评价
		 */
		 public function remark_show(){
		 	$merchant_id = I('get.merchant_id');
			$data = $this->merchant_remark->getData($merchant_id);
			$this->assign('data',$data);
			$this->display();
		 }
		 
		 /**
		  * 创建异步删除评论方法
		  */
		  public function del_remark(){
		  	if(!IS_AJAX) return FALSE;
			$id = I('post.id');
			$re = $this->merchant_remark->where(array('merchant_remark_id'=>$id))->delete();
			if($re){
				$this->ajaxReturn(array('type'=>1,'content'=>'删除成功'));exit;
			}else{
				$this->ajaxReturn(array('type'=>0,'content'=>'删除失败'));exit;
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
		    $upload->savePath  =     'Picture/'; // 设置附件上传（子）目录
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
		 * 商家问答区域管理方法
		 */
		 public function discuss(){
		 	/**
			 * 获取所有商家信息以及点评条数
			 */
			 $data = $this->merchant->getData();
			 foreach($data as $k=>&$v){
			 	$v['question_num'] = M('merchant_discuss')->where(array('merchant_id'=>$v['merchant_id']))->count();
			 }
			 $this->assign('data',$data);
			$this->display();
		 }
		
		/**
	    * 创建后台商家问答添加方法
	    */
	    public function discuss_add(){
	    	/**
			 * 获取到传输的post数据
			 * 调用模型添加方法
			 * 判定返出结果
			 */
	    	if(IS_POST){
	    		$data = I('post.');
				$re = $this->merchant_discuss->store($data);
				if(!$re){
					$error = $this->merchant_discuss->getError();
					echo '<script type="text/javascript">alert("'.$error.'");</script>';
				}else{
					echo '<script type="text/javascript">alert("添加问题成功。");location.href = "'.U('discuss_add',array('merchant_id'=>$data['merchant_id'])).'";</script>';
				}
	    	}else{
				$merchant_id = I('get.merchant_id');
				$this->assign('merchant_id',$merchant_id);
	    		$this->display();
	    	}
	    }
		
		
		/**
		 * 创建问答查看方法
		 */
		 public function discuss_show(){
		 	$merchant_id = I("get.merchant_id");
		 	$data = $this->merchant_discuss->getData($merchant_id);
			$this->assign('data',$data);
			$this->display();
		 }
		 
		/**
		  * 创建异步删除问答方法
		  */
		  public function del_discuss(){
		  	if(!IS_AJAX) return FALSE;
			$id = I('post.id');
			$re = $this->merchant_discuss->where(array('merchant_remark_id'=>$id))->delete();
			if($re){
				$this->ajaxReturn(array('type'=>1,'content'=>'删除成功'));exit;
			}else{
				$this->ajaxReturn(array('type'=>0,'content'=>'删除失败'));exit;
			}
		  }


		/**
		 * 创建商家主页视屏管理方法
		 */
		 public function video(){
		 	$data = M('merchant_video')->order('addtime DESC')->where(array('is_del'=>0))->select();
		 	foreach($data as $k=>&$v){
		 		$v['admin_name'] = M('admin')->where(array('admin_id'=>$v['admin_id']))->getField('admin_name');
		 	}
		 	$this->assign('data',$data);
			$this->display();
		 }
		 
		 /**
		  * 创建添加视屏方法
		  */
		  public function add_video(){
		  	if(IS_POST){
		  		$data = I('post.');
				$data['addtime'] = time();
				$data['admin_id'] = $_SESSION['admin_id'];
				$re = M('merchant_video')->add($data);
				if(!$re){
					echo '<script type="text/javascript">alert("添加失败");</script>';
				}else{
					echo '<script type="text/javascript">alert("添加成功");location.href = "'.U('video').'"</script>';
				}
		  	}
		  	
			$this->display();
		  }
		  
		 /**
		  * 创建保存视屏编辑方法
		  */
		  public function video_save(){
		  	$data = I('post.');
			foreach($data['video_src'] as $k=>$v){
				$arr = array(
					'video_src'=>$data['video_src'][$k],
					'is_show'=>$data['is_show'][$k],
					'merchant_video_id'=>$data['merchant_video_id'][$k]
				);
				$re = M('merchant_video')->save($arr);
				if(!$re || $re == 'FALSE'){
					echo '<script type="text/javascript">alert("保存失败");</script>';
				}
			}
			
			echo '<script type="text/javascript">alert("保存成功");location.href = "'.U('video').'"</script>';
			
		  }
		/**
		 * 创建商家的价格区间的方法
		 */  
		public function rice_range(){
			if(IS_POST){
				$data = I('post.');
				for ($i=0; $i < count($data['price_part']); $i++) { 
					$price_part = $data['price_part'][$i];
					$sort = $data['sort'][$i];
					$server_price_id = $data['server_price_id'][$i];
						$arr  = array(
							'price_part'=>$price_part,
							'sort'      =>$sort,
							'server_price_id'=>$server_price_id
						);
						$re   = M('merchant_server_price')->where(array('server_price_id'=>$arr['server_price_id']))->save($arr);
					}
				
			}
			if(IS_AJAX){
				$data = I('post.del');
				$re   = M('merchant_server_price')->where(array('server_price_id'=>$data))->delete();
				$this->ajaxReturn($re);exit;
			}
			$data = M('merchant_server_price')->select();
			$this->assign('data',$data);
			$this->display();
		}
		/**
		 * 创建添加价格区间的方法
		 */
		public function add_range(){
			if(IS_POST){
				$data = I('post.');
				$price_part=explode("|",$data['price_part']);
				for ($i=0; $i <= count($price_part) ; $i++) { 
					$arr = array(
						'price_part'=>$price_part[$i],
						'sort'      =>$data['sort']
					);
					$bil = M('merchant_server_price')->where(array('price_part'=>$arr['price_part']))->find();
					if(!$bil){
						$re   = M('merchant_server_price')->add($arr);
						if(!$re){
							echo '<script type="text/javascript">alert("添加失败");</script>';
						}else{
							echo '<script type="text/javascript">alert("添加成功");location.href = "'.U('rice_range').'"</script>';
						}
					}else{
						echo '<script type="text/javascript">alert("价格重复");</script>';
					}
				}
			}
			$this->display();
		}
}