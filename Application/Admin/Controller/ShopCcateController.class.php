<?php
namespace Admin\Controller;
use Think\Controller;
use Common\Model\Shop_ccateModel;
use Common\Model\Shop_ccModel;

//商城推荐分类控制器
class ShopCcateController extends CommonController {
	
    private $ccate;
    private $cc;
	
	public function __construct(){
        $this->ccate = new Shop_ccateModel;
        $this->cc = new Shop_ccModel;
		parent::__construct();
	}

    //首页
    public function index(){
        
        $condition = array('cc.is_del'=>0,'c.is_del'=>0);
        if(IS_POST){
            $keyword = I('post.keyword');
            if(!empty($keyword)){
                $condition['cc.name|c.cname'] = array('LIKE','%'.$keyword.'%');
            };
        };
        $data = $this->ccate->alias('cc')->field('cc.cid,cc.name,cc.addtime,cc.is_show,c.cname')->where($condition)->join('__SHOP_CATEGORY__ c ON cc.category_id = c.cid')->select();
        $this->assign('data',$data);
        $this->display();
    }

    //切换状态
    public function cut(){
        if(!IS_AJAX || !IS_POST) return FALSE;
        $id = I('post.key');
        $data = $this->ccate->where(array('cid'=>$id,'is_del'=>0))->find();
        if(is_null($data)){
            $this->ajaxReturn(array('type'=>0));die;
        };
        if($data['is_show'] == 0){
            $re = $this->ccate->where(array('cid'=>$id,'is_del'=>0))->setField(array('is_show'=>1));
        }else{
            $re = $this->ccate->where(array('cid'=>$id,'is_del'=>0))->setField(array('is_show'=>0));
        };
        if(!$re){
            $this->ajaxReturn(array('type'=>0));die;
        }else{
            $this->ajaxReturn(array('type'=>1));die;
        };
    }

    //添加
    public function add(){
        if(IS_POST){
            $post = I('post.');
            $re = $this->ccate->store($post);
            if(!$re){
                alert($this->ccate->getError());die;
            };
            alert('添加成功',U('index'));die;
        };
        //获得顶级分类数据
        $cate = M('Shop_category')->where(array('pid'=>0,'is_del'=>0))->select();
        $this->assign('cate',$cate);
        $this->display();
    }

    //编辑
    public function edit($id=NULL){
        if(empty($id)) return FALSE;
        if(IS_POST){
            $post = I('post.');
            $re = $this->ccate->edit($post);
            if(!$re){
                alert($this->ccate->getError());die;
            };
            alert('编辑成功',U('index'));die;
        };
        //获得顶级分类数据
        $cate = M('Shop_category')->where(array('pid'=>0,'is_del'=>0))->select();
        $this->assign('cate',$cate);
        $data = $this->ccate->where(array('cid'=>$id,'is_del'=>0))->find();
        $this->assign('data',$data);
        $this->display();
    }

    //删除
    public function del($id=NULL){
        if(empty($id)) return FALSE;
        $this->ccate->where(array('cid'=>$id))->save(array('is_del'=>1));
        $this->cc->where(array('cid'=>$id))->save(array('is_del'=>1));
        alert('删除成功',U('index'));
    }

    //已推荐商品
    public function show($id=NULL){
        if(empty($id)) return FALSE;
        $data = $this->ccate->where(array('cid'=>$id,'is_del'=>0))->find();
        if($data['type'] == 1){
            $temp = $this->cc->alias('cc')->where(array('cc.cid'=>$id,'cc.is_del'=>0))->join('__SHOP_GOODS__ sg ON cc.gid = sg.gid')->select();
        }else{
            $temp = $this->cc->where(array('cid'=>$id,'is_del'=>0))->select();
        };
        $goods = array();
        foreach($temp as $k => $v){
            $goods[$v['sort']] = $v;
        };
        $this->assign('goods',$goods);
        $this->assign('data',$data);
        $this->display();
    }

    //商品列表
    public function goods_list($id=NULL,$sort=NULL){
        if(empty($id) || empty($sort)) return FALSE;
        $one = $this->cc->where(array('cid'=>$id,'is_del'=>0))->find();
        $gids = $this->cc->where(array('category_cid'=>$one['category_cid'],'is_del'=>0))->getField('gid',true);
        if(empty($gids)) $gids = array();
        $gids = implode(',',$gids);        
        $ccate = $this->ccate->where(array('cid'=>$id,'is_del'=>0))->find();
        $model = new \Common\Model\Shop_cateModel;
        $cids = $model->get_son($ccate['category_id']);//获得所有子级id
        $condition = array('gid'=>array('NOT IN',$gids),'category_cid'=>array('IN',implode(',',$cids)),'is_del'=>0);//条件
        //搜索
        if(IS_POST){
            $gname = I('post.keyword');
            if(!empty($gname)){
                $condition['gname'] = array('LIKE','%'.$gname.'%');
            };
        };
        $data = M('Shop_goods')->where($condition)->select();
        $this->assign('data',$data);
        $this->assign('id',$id);
        $this->assign('type',$ccate['type']);
        $this->assign('sort',$sort);
        $this->assign('action',$action);
        $this->display();
    }

    //添加商品
    public function goods_add(){
        $get = I('get.');
        if(IS_POST){
            $post = I('post.');
            if(!empty($get)){
                $post['gid'] = $get['gid'];
                $post['cid'] = $get['id'];
                $post['sort'] = $get['sort'];
            };
            $re = $this->cc->store($post);
            if(!$re){
                if(IS_AJAX){
                    $this->ajaxReturn(array('type'=>'error','msg'=>$this->cc->getError()));die;
                }else{
                    alert($this->cc->getError());die;
                };
            };
            if(IS_AJAX){
                $this->ajaxReturn(array('type'=>'success','msg'=>'添加成功'));die;
            }else{
                alert('添加成功',U('show',array('id'=>$get['id'])));die;
            };
        };
        $this->assign('sort',$get['sort']);
        $this->display();
    }

    //编辑
    public function goods_edit($cid=NULL,$gid=NULL){
        if(empty($cid) || empty($gid)) return FALSE;
        if(IS_POST){
            $post = I('post.');
            $post['cid'] = $cid;
            $post['gid'] = $gid;
            $re = $this->cc->edit($post);
            if(!$re){
                alert($this->cc->getError());die;
            };
            alert('编辑成功',U('show',array('id'=>$cid)));die;
        };
        $data = $this->cc->where(array('cid'=>$cid,'gid'=>$gid,'is_del'=>0))->find();
        $this->assign('data',$data);
        $this->display();
    }

    //删除
    public function goods_del($cid=NULL,$gid=NULL){
        if(empty($cid) || empty($gid)) return FALSE;
        $this->cc->where(array('cid'=>$cid,'gid'=>$gid,'is_del'=>0))->save(array('is_del'=>1));
        alert('删除成功',U('show',array('id'=>$cid)));
    }


}