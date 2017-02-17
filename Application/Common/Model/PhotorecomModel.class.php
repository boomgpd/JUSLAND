<?php namespace Common\Model;
use Think\Model;
/**
 * 影楼分类表
 */
class PhotorecomModel extends Model{
	protected $tableName="photo_recom_type";
	
	protected $_validate = array(
     array('p_r_t_name','require','分类名称必须填写！'), 
     array('cid','require','分类id必须勾选！'), 
   );
   /**
    * 创建添加推荐的方法
    */
    public function add($data){
    	if($data['is_poric'] == 1){
    		$info = M('photo_recom_type')->where(array('is_del'=>0,'cid'=>$data['p_id']))->getField('p_id');
    		$cid = M("photo_recom_type")->where(array('is_del'=>0,'p_id'=>$info))->getField('cid',TRUE);
    		foreach ($cid as $key => $value) {
    			$bool[] = M('photo_recom_type')->where(array('pid'=>$value,'is_del'=>0,'is_poric'=>1))->find();
    		}
    		if($bool){
    			$this->error = '已有左侧重点推荐，只可以推荐一个';
    			return FALSE;
    		}
    	}
    	if(!$this->create($data)) return false;//验证失败
    	$re = M('photo_recom_type')->add($this->data);
		return $re;
    }
	/**
	 * 创建受限显示的方法
	 */
	public function getData(){
		$lists = $this->field('p_r_t_name,cid')->where(array('is_del'=>0,'p_id'=>0))->select();
		foreach ($lists as $key => &$value) {
			$value['cids'] = $this->field('p_r_t_name,cid')->where(array('is_del'=>0,'p_id'=>$value['cid']))->select();
			foreach ($value['cids'] as $k => &$val) {
				$val['cids'] = $this->field('pro_id,list_src')->where(array('is_del'=>0,'p_id'=>$val['cid'],'is_poric'=>0))->limit('6')->select();
				$val['left'] = $this->field('pro_id,list_src')->where(array('is_del'=>0,'p_id'=>$val['cid'],'is_poric'=>1))->find();
				if($val['left']){
					$val['left']['pro_id'] = M('photo_product')->field('p_name,price,p_p_id')->where(array('is_del'=>0,'p_p_id'=>$val['left']['pro_id']))->find();
				}
				foreach ($val['cids'] as $ke => &$v) {
					$v['pro_id'] = M('photo_product')->field('p_name,price,p_p_id')->where(array('is_del'=>0,'p_p_id'=>$v['pro_id']))->find();
				}
			}
		}

		// dump($lists);die;
		return $lists;
	}
	/**
	 * 创建限时推荐的方法
	 */
	public function time_limit(){
		$re = M('photo_odds')->where(array('is_del'=>0,'is_page'=>1))->select();
//		dump(strtotime(date('2017/2/12')));die;
		$att = array();
		foreach ($re as $key => $value) {
			if(strtotime(date('Y-m-d')) >= $value['start_time'] && strtotime(date('Y-m-d')) < $value['end_time']){
				$arr  = array();
				$arr['box'] = $value;
				$arr['box']['photo'] = M('photo_product')->where(array('is_del'=>0,'p_p_id'=>$value['p_p_id']))->find(); 
				$arr['box']['photo']['picture'] =  explode(',', $arr['box']['photo']['picture']);
				$arr['box']['photo']['p_c_id'] = $this->where(array('is_del'=>0,'cid'=>$arr['box']['photo']['p_c_id']))->getField('p_r_t_name');
				$time = strtotime(date('Y-m-d H:i:s'));
				$time1 = strtotime(date('Y-m-d'));
				$arr['box']['limit_time'] = ($value['end_time'] - $time1)/(3600*24);
				$arr['box']['limit_timess'] = explode(":",date('H:i',$value['end_time'] - $time));
				$arr['box']['limit_times'] = $value['end_time'] - $time1;
				$att[] = $arr;
			}
		}
		return $att;
	}
	/**
	 * 畅销商品推荐
	 */
	public function sell(){
		$re = M('photo_sell')->field('is_del,addtime',TRUE)->where(array('is_del'=>0))->select();
		$att = array();
		foreach ($re as $key => $value) {
			$arr = array();
			$arr = $value;
			$arr['p_p_id'] = M('photo_product')->field('price,p_p_id,p_c_id')->where(array('is_del'=>0,'p_p_id'=>$value['p_p_id']))->find();
			$type = M('photo_category')->where(array('p_c_id'=>$arr['p_p_id']['p_c_id']))->getField('p_id');
			$arr['type'] = M('photo_category')->where(array('p_c_id'=>$type))->getField('cname');
			$att[] = $arr;
		}
//		dump($att);die;
		return $att;
	}
}