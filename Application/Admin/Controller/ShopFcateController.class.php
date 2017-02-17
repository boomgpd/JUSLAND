<?php
namespace Admin\Controller;
use Common\Model\Shop_fcateModel;
use Common\Model\Shop_fcgModel;

//商城首推分类控制器
class ShopFcateController extends CommonController {
	
    private $fcate;
	private $fcg;
	
	public function __construct(){
        $this->fcate = new Shop_fcateModel;
		$this->fcg = new Shop_fcgModel;
		parent::__construct();
	}

    //首页
    public function index(){
        $data = $this->fcate->where(array('is_del'=>0))->select();
        $this->assign('data',$data);
        $this->display();
    }

    //首推分类添加
    public function add(){
        if(IS_POST){
            $post = I('post.');
           $re = $this->fcate->store($post);
           if(!$re){
                alert($this->fcate->getError());die;
           };
           alert('添加成功',U('index'));die;
        };
        //获得顶级分类数据
        $cate = M('Shop_category')->where(array('pid'=>0,'is_del'=>0))->select();
        $this->assign('cate',$cate);
        $this->display();
    }

    //首推分类编辑
    public function edit($id=NULL){
        if(empty($id)) return FALSE;
        if(IS_POST){
            $post = I('post.');
            $re = $this->fcate->edit($post);
            if(!$re){
                alert($this->fcate->getError());die;
            };
            alert('添加成功',U('index'));die;
        };
        //获得顶级分类数据
        $cate = M('Shop_category')->where(array('pid'=>0,'is_del'=>0))->select();
        $this->assign('cate',$cate);
        $data = $this->fcate->where(array('fcid'=>$id,'is_del'=>0))->find();
        $this->assign('data',$data);
        $this->display();
    }

    //首推分类删除
    public function del($id=NULL){
        if(empty($id)) return FALSE;
        $this->fcate->where(array('fcid'=>$id))->save(array('is_del'=>1));
        M('Shop_fc_goods')->where(array('fcid'=>$id))->save(array('is_del'=>1));
        alert('删除成功',U('index'));die;
    }

    //展示首推分类商品
    public function show($id=NULL){
        if(empty($id)) return FALSE;
        $temp = $this->fcg->where(array('fcid'=>$id,'is_del'=>0))->order('sort ASC')->select();
        $data = array();
        foreach($temp as $k => $v){
            $goods = M('Shop_goods')->where(array('gid'=>$v['gid'],'is_del'=>0))->find();
            $v = array_merge($goods,$v);
            $data[$v['sort']] = $v;
        };
        $this->assign('data',$data);
        $this->assign('id',$id);
        $this->display();
    }

    //显示商品列表
    public function goods_list($id=NULL,$sort=NULL){
        if(empty($id) || empty($sort)) return FALSE;
        $gids = $this->fcg->where(array('fcid'=>$id,'is_del'=>0))->getField('gid',true);
        if(empty($gids)) $gids = array();
        $gids = implode(',',$gids);
        $data = M('Shop_goods')->where(array('gid'=>array('NOT IN',$gids),'is_del'=>0))->select();
        $this->assign('data',$data);
        $this->assign('id',$id);
        $this->assign('sort',$sort);
        $this->display();
    }

    //添加商品到推荐
    public function goods_add($gid=NULL,$id=NULL,$sort=NULL){
        if(empty($gid) || empty($id) || empty($sort)) return FALSE;
        if(IS_POST){
            $post = I('post.');
            $post['fcid'] = $id;
            $post['gid'] = $gid;
            $post['sort'] = $sort;
            $re = $this->fcg->store($post);
            if(!$re){
                alert($this->fcg->getError());die;
            };
            alert('添加成功',U('show',array('id'=>$id)));die;
        };
        $this->assign('sort',$sort);
        $this->display();
    }

    //编辑商品推荐
    public function goods_edit($id=NULL,$gid=NULL){
        if(empty($id) || empty($gid)) return FALSE;
        $data = $this->fcg->where(array('fcid'=>$id,'gid'=>$gid,'is_del'=>0))->find();
        if(is_null($data)) return FALSE;
        if(IS_POST){
            $post = I('post.');
            $post['fcid'] = $id;
            $post['gid'] = $gid;
            $post['sort'] = $data['sort'];
            $re = $this->fcg->edit($post);
            if(!$re){
                alert($this->fcg->getError());die;
            };
            alert('编辑成功',U('show',array('id'=>$id)));
        };
        $this->assign('data',$data);
        $this->display();
    }

    //删除商品推荐
    public function goods_del($id=NULL,$gid=NULL){
        if(empty($id) || empty($gid)) return FALSE;
        $this->fcg->where(array('fcid'=>$id,'gid'=>$gid))->save(array('is_del'=>1));
        alert('删除成功',U('show',array('id'=>$id)));
    }
}