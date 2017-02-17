<?php namespace Home\Model;
use Think\Model;

class MemberModel extends Model{
	protected $tableName="member";
	
	/**
	 * 获取用户信息
	 */
	public function getData(){
		if(isset($_SESSION['member_id'])){
			$data                   = $this->where(array('member_id'=>$_SESSION['member_id']))->find();
			$data['board_num']      = M('board')->where(array('member_member_id'=>$_SESSION['member_id']))->count();
			$data['board_list_num'] = M('board')->alias('a')->join('__BOARD_LIST__ b ON b.board_board_id = a.board_id AND a.member_member_id = '.$_SESSION['member_id'].'')->count();
			$data['picture_click']  = M('water_click')->where(array('member_id'=>$_SESSION['member_id']))->getField('picture_id',TRUE);
//			foreach ($data['picture_click'] as $key => $value) {
//				$click = 
//			}
			$data['picture_click']  = implode(",", $data['picture_click']);
//			dump($data);die;
			return $data;
		}else{
			return FALSE;
		}

	}
	
	/**
	 * 创建登录验证方法
	 */
	 public function check_login($data){
	 	/**
		 * 验证对应用户名是否存在
		 * 验证密码是否正确
		 */
		 $oldData = $this->where(array('member_name'=>$data['name']))->find();
		 if(!$oldData){
		 	$oldData = $this->where(array('phone'=>$data['member_name']))->find();
		 }
		 if(!$oldData) return array('type'=>102,'content'=>'用户名不存在');
		 $new_pwd = md5(md5($oldData['member_name']).md5($data['password']));
		 if($new_pwd != $oldData['password']) return array('type'=>103,'content'=>'密码输入错误');
		 unserialize($_SESSION);
		 $_SESSION['member_id'] = $oldData['member_id'];
		 //如果有购物车数据就合并
		 if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
		 	$this->merge_cart();
		 };
		 return array('type'=>100,'content'=>'登录成功','urls'=>U('home/index/index'));
	 }

	 //合并购物车方法
	 private function merge_cart(){
	 	$s_cart = $_SESSION['cart'];
	 	$cart = M('Shop_goods_cart')->where(array('member_id'=>$_SESSION['member_id'],'is_del'=>0))->select();
	 	if(is_null($cart)){//数据库没有
	 		foreach($s_cart as $k => $v){
	 			$v['member_id'] = $_SESSION['member_id'];
	 			M('Shop_goods_cart')->add($v);
	 		};
	 	}else{
	 		foreach($cart as $k => $v){
	 			foreach($s_cart as $kk => $vv){
	 				if($vv['gid'] == $v['gid'] && $vv['glid'] == $v['glid']){
	 					$v['num'] = $v['num'] + $vv['num'];
	 					M('Shop_goods_cart')->save($v);
	 				}else{
	 					$vv['member_id'] = $_SESSION['member_id'];
	 					M('Shop_goods_cart')->add($vv);
	 				};
	 			};
	 		};
	 	};
	 	unset($_SESSION['cart']);
	 }
	
}
