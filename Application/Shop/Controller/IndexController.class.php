<?php
namespace Shop\Controller;
use Common\Model\Shop_cateModel;
use Common\Model\Shop_fcateModel;
use Common\Model\ShopGoodsModel;
use Common\Model\Shop_ccateModel;

//商城前台首页控制器
class IndexController extends CommonController {
	private $special = array('special_one','special_two','special_threr','special_frou','special_five','special_six','special_seven','special_eight');
    private $cate;
	private $ccate;
	private $fcate;
	private $goods;
	
	public function __construct(){
        $this->cate = new Shop_cateModel;
		$this->ccate = new Shop_ccateModel;
		$this->fcate = new Shop_fcateModel;
		$this->goods = new ShopGoodsModel;
		parent::__construct();
	}
//	public function haha(){
//		$get = I('get.type');
//		$alert = $this->php_alert('haha',U('mian/index/index'));
//		dump($alert);die;
//		$this->index($alert);
//	}
    //首页
    public function index($name='婚礼道具'){
     	//轮播图管理
    	$bananer_type = M('bananer_type')->where(array('type_name'=>'商城顶部轮播图'))->getField('bananer_type_id');
		$bananer = M('bananer')->order('sort desc')->where(array('bananer_type_id'=>$bananer_type,'is_del'=>0))->select();
		$this->assign('bananer',$bananer);
		
        $top_cate = $this->cate->where(array('pid'=>0,'is_del'=>0))->select();
        $first_cate = $this->fcate->get_data();
        $cate = $this->cate->get_data();
        $cid = $this->cate->where(array('cname'=>$name,'pid'=>0,'is_del'=>0))->getField('cid');
        if(is_null($cid)) $cid = $this->cate->where(array('pid'=>0,'is_del'=>0))->getField('cid');
        $cids = $this->cate->get_son($cid);
        $ccate = $this->ccate->where(array('category_id'=>array('IN',implode(',',$cids)),'is_show'=>1,'is_del'=>0))->order('sort ASC')->select();
        foreach($ccate as $k => $v){
            $temp = M('shop_cc_goods cg')->where(array('cg.cid'=>$v['cid'],'cg.is_del'=>0))->join('__SHOP_GOODS__ sg ON cg.gid = sg.gid')->order('cg.sort ASC')->select();
            $arr = array();
            foreach($temp as $a => $b){
                $arr[$b['sort']] = $b;
            };
            $ccate[$k]['son'] = $arr;
        };
        $this->assign('ccate',$ccate);
        $this->assign('cate',$cate);
        $this->assign('first_cate',$first_cate);
        $this->assign('top_cate',$top_cate);
        $this->display();
    }

    //查看更多
    public function more($id=NULL){
        if(is_null($id)) return FALSE;
        $cate = $this->ccate->where(array('cid'=>$id,'is_show'=>1,'is_del'=>0))->find();
        if(is_null($cate)) return FALSE;
        $gids = M('shop_cc_goods')->where(array('cid'=>$id,'is_del'=>0))->getField('gid',true);//获得推荐的商品
        if(is_null($gids)) $gids = array();
        $labels = M('shop_goods_label')->where(array('gid'=>array('IN',implode(',',$gids)),'is_del'=>0))->getField('lid',true);//获得对应的标签id
        $gids = M('shop_goods_label')->where(array('lid'=>array('IN',implode(',',$labels)),'is_del'=>0))->getField('gid',true);//获得对应标签的商品id
        $gids = implode(',',$gids);

        //条件筛选
        if(IS_POST){
            $post_data = I('post.');
            $post_data = array_merge($post_data,I('post.'));
        }else{
            $post_data = I('get.');
        }
        $condition = array();
        
        if(isset($_GET['goods_name'])){
            $condition['gname'] = array('LIKE',"%".$post_data['goods_name']."%");//从商品名称和商品描述里查找
            $get['goods_name'] = $post_data['goods_name'];
        }
        
        if($post_data['small_price'] && !$post_data['big_price']){
            $condition['shopprice'] = array('gt',$post_data['small_price']);//只有最小值
            $get['small_price'] = $post_data['small_price'];
        }else if(!$post_data['small_price'] && $post_data['big_price']){
            $condition['shopprice'] = array('lt',$post_data['big_price']);//只有最大值
            $get['big_price'] = $post_data['big_price'];
        }else if($post_data['small_price'] && $post_data['big_price']){
            $condition['shopprice'] = array('between',array($post_data['small_price'],$post_data['big_price']));//只有最大值
            $get['big_price'] = $post_data['big_price'];
            $get['small_price'] = $post_data['small_price'];
        }
        $get = I('get.');
        $this->assign('get',$get);
        
        /**
         * 分页开始
         */
        $page_number = I('get.page');
        if(!$page_number){
            $page_number = 1;
        }
        $goods_data = $this->goods->where(array('is_del'=>0,'gid'=>array('IN',$gids)))->where($condition)->limit(C('__SHOP_LIST_NUM__'))->page($page_number)->order($order)->select();
        $page_num  = ceil($this->goods->where(array('is_del'=>0,'gid'=>array('IN',$gids)))->where($condition)->limit()->page()->count()/C('__SHOP_LIST_NUM__'));
        if($page_number == 1){
            $page_left = 0;
        }else if($page_number == 2){
            $page_left = 1;
        }else if($page_number == 3){
            $page_left = 2;
        }else if($page_number > 3){
            $page_left = 3;
        }
        
        if($page_num == 1 || $page_num == 0){
            $page_type = FALSE;
        }else{
            $page_type = TRUE;
        }
        
        $page_right = $page_num - $page_number;
        if($page_right > 3) $page_right = 3;
        $page = array('num'=>$page_num,'page_number'=>$page_number,'page_left'=>$page_left,'page_right'=>$page_right,'page_type'=>$page_type);
        $this->assign('page',$page);
//      分页结束
//      dump($page);die;
        $top_cate = $this->cate->where(array('pid'=>0,'is_del'=>0))->select();
        $this->assign('goods_data',$goods_data);
        $this->assign('top_cate',$top_cate);
        $this->display();
    }


    /**
     * 专题
     */
    public function special(){
    	$num = I('get.num');
		$url = $this->special[$num];
        $this->display($url);
    }
}