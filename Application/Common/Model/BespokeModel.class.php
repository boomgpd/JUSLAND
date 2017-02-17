<?php namespace Common\Model;
use Think\Model;

class BespokeModel extends Model{
	protected $tableName="bespoke";
	
	//	自动验证属性
	protected $_validate = array(
     array('member_name','require','用户名称必须填写'),
     array('phone','require','电话号码必须填写'), 
     array('bespoke_type_id','require','预约类型'), 
   );
	
	/**
	 * 创建添加方法
	 */
	public function store($data){
		$data['bespoke_type_id'] = M('bespoke_type')->where(array('b_name'=>$data['b_name']))->getField('bespoke_type_id');
		unset($data['b_name']);
		$data['is_deal'] = 1;
		$data['addtime'] = time();
		$re = $this->create($data);
		if(!$re) return FALSE;
		$re = $this->add($this->data);
		return $re;
	}
	
	
	/**
	 * 创建获取数据方法
	 */
	public function getData($arr){
//		dump($arr);die;
		if($arr['bespoke_id']){//获取
			$condition = array('bespoke_id'=>$arr['bespoke_id'],'is_del'=>0);
		}elseif($arr['admin_id']){
			$condition = array('admin_id'=>$arr['admin_id'],'is_del'=>0);
		}elseif($arr['is_deal'] && !$arr['bespoke_type']){
			$condition = array('is_deal'=>$arr['is_deal'],'is_del'=>0);
		}elseif(!$arr['is_deal'] && $arr['bespoke_type']){
			$condition = array('bespoke_type_id'=>$arr['bespoke_type'],'is_del'=>0);
		}elseif($arr['is_deal'] && $arr['bespoke_type']){
			$condition = array('is_deal'=>$arr['is_deal'],'bespoke_type_id'=>$arr['bespoke_type'],'is_del'=>0);
		}else{
			$condition = array('is_del'=>0);
		}
		$data = $this->where($condition)->order(array('addtime'=>'DESC','is_deal'=>'ASC'))->select();
		foreach($data as $k=>&$v){
			$v['bespoke_type'] = M('bespoke_type')->field('rob_time,deal_time',TRUE)->where(array('bespoke_type_id'=>$v['bespoke_type_id']))->getField('b_name');
			$v['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
			$v['admin_name'] = M('admin')->where(array('admin_id'=>$_SESSION['admin_id']))->getField('admin_name');
			if($v['rob_time'] != 0){
				$v['rob_time'] = date("Y-m-d H:i:s",$v['rob_time']);
			}
			
			if($v['deal_time'] != 0){
				$v['deal_time'] = date("Y-m-d H:i:s",$v['deal_time']);
			}
			switch ($v['is_deal']) {
				case '1':
					$v['deal_name'] = '未处理'; 
					break;
				case '2':
					$v['deal_name'] = '处理中';
					break;
				case '3':
					$v['deal_name'] = '考虑中';
					break;
				case '4':
					$v['deal_name'] = '成交';
					break;
				case '5':
					$v['deal_name'] = '未成交';
					break;	
			}
		}
		
		
		return $data;
	}
	
	 /**
	 * 创建添加灵感库预约方法
	 */
	public function store_mo($data){
		$data['bespoke_type_id'] = M('bespoke_type')->where(array('b_name'=>$data['b_name']))->getField('bespoke_type_id');
		unset($data['b_name']);
		unset($data['url']);
		$arr = array(
			'member_name'  		=> $data['member_name'],
			'phone'        		=> $data['phone'],
			'area_id'      		=> implode('|', $data['area_id']),
			'is_deal'      		=> 1,
			'bespoke_type_id'	=>$data['bespoke_type_id'],
			'url'				=> $data['url1']
		);
		$re = $this->create($arr);
		if(!$re) return FALSE;
		$re = $this->add($this->$arr);
		return $re;
	}
	/**
	 * 创建添加首页预约策划、报价、找公司、立即报价的方法
	 */
	 public function store_plot($data){
	 	$data['bespoke_type_id'] = M('bespoke_type')->where(array('b_name'=>$data['b_name']))->getField('bespoke_type_id');
		unset($data['b_name']);
		$arr = array(
			'member_name'  		=> $data['member_name'],
			'phone'        		=> $data['phone'],
			'area_id'      		=> implode('|', $data['area_id']),
			'is_deal'      		=> 1,
			'url'				=> '',
			'bespoke_type_id'	=>$data['bespoke_type_id'],
			'wedding'           =>implode('|',$data['wedding'])
		);
//		dump($arr);die;
		$re = $this->create($arr);
		if(!$re) return FALSE;
		$re = $this->add($this->$arr);
		return $re;
	 }
	
}
