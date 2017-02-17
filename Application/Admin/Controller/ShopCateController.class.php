<?php
namespace Admin\Controller;
use Common\Model\Shop_cateModel;
use Common\Model\Shop_fcateModel;
use Common\Model\ShopSpecModel;

//商城分类控制器
class ShopCateController extends CommonController {
	
   	private $cate;
	private $fcate;
	private $spec;
	
	public function __construct(){
        $this->cate = new Shop_cateModel;
		$this->fcate = new Shop_fcateModel;
		$this->spec = new ShopSpecModel;
		parent::__construct();
	}
    //首页
    public function index($pid=0){
        $data = $this->cate->where(array('pid'=>$pid,'is_del'=>0))->select();
		$type = 'classify';
		if(!$data){
			$list = $this->cate->field('level,spec_id')->where(array('cid'=>$pid,'is_del'=>0))->find();
			if($list['level'] == 3){
				$data = $this->spec->getData(array('s_id'=>$list['spec_id']));
				$type = 'spec';
			}else{
				$data = '';
				$type = 'classify';
			}
		}
        $bread = $this->cate->get_bread($pid);
		if(is_null($bread)) $bread = array();
        $bread = implode(',',$bread);
        $bread = $this->cate->where(array('cid'=>array('IN',$bread)))->select();
        $this->assign('bread',$bread);
    	$this->assign('data',$data);
        $this->assign('pid',$pid);
        $this->assign('type',$type);
        $this->display();
    }
	/**
	 * 删除规格
	 */
//	public function dele(){
//		if(!IS_AJAX) return FALSE;
//		$data = I('post.');
//		M()->startTrans();
//		$re = $this->cate->where(array('cid'=>$data['pid']))->find();
//		$re = explode("|", $res['spec_id']);
//		foreach ($re as $key => $value) {
//			if($value == $data['s_id']){
//				unset($re[$key]);
//			}
//		}
//		$re = implode("|", $re);
//		$re = $this->cate->where(array('cid'=>$data['pid']))->save(array('spec_id'=>$re));
//		if(!$re){
//			$this->ajaxReturn(0);exit;
//			M()->rollback();
//		}
//		$goods = M('shop_goods')->where(array('is_del'=>0,'category_cid'=>$data['pid']))->select();
//		$goods_id = array();
//		$if = array('no_have'=>0,'not_have'=>0);
//		foreach ($goods as $key => &$value) {
//			$value['spec_id'] = explode('|', $value['spec_id']);
//			foreach ($value['spec_id'] as $k => $v) {
//				if($v == $data['s_id']){
//					unset($value['spec_id'][$k]);
//				}
//			}
//			if(is_null($value['spec_id'])){
//				$if['no_have'] ++; 
//				$re = implode("|", $value['spec_id']);
//				$res = M('shop_goods')->where(array('gid'=>$value['gid']))->save(array('spec_id'=>$re));
//			}else{
//				$if['not_have'] ++; 
//			}
//			$goods_id[] = $value['gid'];
//		}
//		if($if['no_have'] == 0 && $if['not_have'] == count($goods)){
//			$res = 1;
//		}
//		if(!$res){
//			$this->ajaxReturn(0);exit;
//			M()->rollback();
//		}
//		$attr = M('shop_goods_attr')->where(array('goods_gid'=>array('IN',implode(',', $goods_id),'spec_id'=>$data['s_id'])))->select();
//		if(is_null($attr)){
//			$attr = M('shop_goods_attr')->where(array('goods_gid'=>array('IN',implode(',', $goods_id),'spec_id'=>$data['s_id'])))->save(array('is_del'=>1));
//		}else{
//			$attr =1;
//		}
//		if(!$attr){
//			$this->ajaxReturn(0);exit;
//			M()->rollback();
//		}
//		M()->commit();
//		$this->ajaxReturn(true);exit;
//	}
    //添加
    public function add($pid=NULL,$type=NULL){
        if(is_null($pid)) return FALSE;
        if(IS_POST){
            $post = I('post.');
	     	$post['pid'] = $pid;
        	if($post['type'] == 'classify'){
				$re = $this->cate->where(array('is_del'=>0,'cid'=>$pid))->getField('level');
				$post['level'] = $re+1;
				if(is_null($re)) $post['level'] = 1;
	            $re = $this->cate->store($post);
				if(!$re){
                	alert($this->cate->getError());die;
            	};
        	}else if($post['type'] == 'spec'){
				$class = $this->cate->where(array('cid'=>$post['pid']))->getField('spec_id');
				//先拆分￥class里的规格，然后和post提交过来的值合并，在转化为字符串
				if($class){
					$spec =  implode("|", array_merge(explode('|',$class),$post['s_id']));
				}else{
					$spec =  implode("|",$post['s_id']);
				}
				$re = $this->cate->where(array('cid'=>$post['pid']))->save(array('spec_id'=>$spec));
				if(!$re){
               		alert('数据异常');die;
            	};
        	}
            alert('添加成功',U('index',array('pid'=>$pid)));die;
        };
        $type_data = array();
        if($pid != 0){
            $type_data = M('Shop_type')->where(array('is_del'=>0))->select();
        };
		$spec = $this->spec->getData(array('cid'=>$pid));
        $this->assign('pid',$pid);
        $this->assign('type',$type);
        $this->assign('spec',$spec);
        $this->assign('type_data',$type_data);
        $this->display();
    }

    //编辑
    public function edit($id=NULL){
        if(empty($id)) return FALSE;
        if(IS_POST){
            $post = I('post.');
            $re = $this->cate->edit($post);
            if(!$re){
                alert($this->cate->getError());die;
            };
            alert('编辑成功',U('index',array('pid'=>$re['pid'])));
        };
        $data = $this->cate->where(array('cid'=>$id,'is_del'=>0))->find();
        $type_data = '';
        if($data['pid'] != 0){
            $type_data = M('Shop_type')->where(array('is_del'=>0))->select();
        };
        $this->assign('data',$data);
        $this->assign('type_data',$type_data);
        $this->display();
    }

    //删除
    // public function del($id=NULL){
    //     if(empty($id)) return FALSE;
    //     $re = $this->cate->where(array('cid'=>$id,'is_del'=>0))->find();
    //     if(is_null($re)) return FALSE;
    //     $cids = $this->cate->get_son($id);
    //     $cids = implode(',',$cids);
    //     $this->cate->where(array('cid'=>array('IN',$cids)))->save(array('is_del'=>1));
    //     alert('删除成功',U('index',array('pid'=>$re['pid'])));
    // }
}