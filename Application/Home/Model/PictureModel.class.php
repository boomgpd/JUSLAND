<?php
namespace Home\Model;
use Think\Model;

class PictureModel extends Model {
	protected $tableName = "picture";

	//	获取picture后台上传的数据
	public function getData($data) {
		/**
		 * 1.获取所有picture表内图片
		 * 2.通过循环将对应字段压入返出数据内
		 */
		if ($data == null) {
			$pictures = $this->where(array('is_del'=>0)) -> order('addtime DESC')->limit(C('__PAGE_NUM__'))->page(1) -> select();
		} else if($data['keydorw'] && !$data['page_num']) {
			$keydorw['lables'] = array('like', '%' . $data['keydorw'] . '%', );
			$pictures = $this->where(array('is_del'=>0)) -> where($keydorw) -> select();
		}else if(!$data['keydorw'] && $data['page_num'] && !$data['merchant_merchant_id']){
			if($data['page_type'] != NULL && $data['page_type'] != 2){
				$where['type_tid'] = $data['page_type'];
			}else if($data['page_type'] == 2){
			}else{
				$where['type_tid'] = M('type')->where(array('tname'=>'婚礼灵感'))->getField('tid');
			}
			$where['is_del']=0;
			$pictures = $this->where($where)-> order('addtime DESC')->limit(C('__PAGE_NUM__'))->page($data['page_num']) -> select();
			shuffle($pictures);
		}else if($data['merchant_merchant_id']==0 || $data['merchant_merchant_id']){
			$pictures = $this->where(array('is_del'=>0)) -> order('addtime DESC')->where(array('merchant_merchant_id'=>$data['merchant_merchant_id']))->limit(C('__PAGE_NUM__'))->page($data['page_num'])-> select();
		}
		if(!$pictures){
			return false;
		}else{
			$q = I('get.q');//搜索
			$data = array();
			foreach ($pictures as $k => $v) {
				if(!empty($q)){//有搜索
					$lids = str_replace('|',',',$v['lables']);
					$names = implode(',',M('lable')->where(array('lid'=>array('IN',$lids)))->getField('lname',true));//获得所有标签名
					if(strpos($v['title'],$q) === FALSE && strpos($v['keyword'],$q) === FALSE && strpos($names,$q) === FALSE){
						continue;
					};
				};
				$arr = array();
				$arr['box'] = $v;
				$arr['box']['url'] = explode("|", $v['thumb']);
				$url = array();
				$height = 0;
				for ($i = 0; $i < $arr['box']['show_num']; $i++) {
					//修改前
					// $url[] = $arr['box']['url'][$i];
					//修改前
					$img['path'] = $arr['box']['url'][$i];
					$img_type = getimagesize('./Uploads/'.$arr['box']['url'][$i]);
					if(is_array($img_type)){
						$ratio = 205/$img_type[0];
						$height = $img_type[1]*$ratio+$height;
						$img['size'] = $height;
					}else{
						$img['size'] = $height;
					};
					$url[] = $img;
				}
				$arr['box']['url'] = $url;
				$arr['box']['keyword'] = explode("#",$v['keyword']);
				$arr['box']['picture_collect'] = implode(",", $arr['box']['picture_click']);
				if(!$v['merchant_name']){
					$arr['box']['merchant_name'] = M('merchant')->where(array('merchant_id'=>$v['merchant_merchant_id']))->getField('m_name');
				}
				$arr['member'] = M('admin') -> where(array('admin_id' => $v['author_id'])) -> find();
				$data[] = $arr;
			}
			return $data;
		}
	}

	public function getDatas($p_id){
		 if($p_id) {
		 	$re = $this->where(array('p_id'=>$p_id))->find();
			$keyword = explode('#',$re['keyword']);
			$all = $this->where(array('p_id'=>array('NEQ',$p_id),'is_del'=>0))->select();
			$temp = array();
			foreach($keyword as $k => $v){
				foreach($all as $kk => $vv){
					$arr = explode('#',$vv['keyword']);
					if(in_array($v,$arr)){
						$temp[] = $vv;
					};
				};
			};
			$data = array();
			foreach ($temp as $k => $v) {
				$arr = array();
				$arr['box'] = $v;
				$arr['box']['url'] = explode("|", $v['url']);
				$url = array();
				for ($i = 0; $i < $arr['box']['show_num']; $i++) {
					$url[] = $arr['box']['url'][$i];
				}
				$arr['box']['url'] = $url;
				$arr['box']['keyword'] = explode("#",$v['keyword']);
				$arr['box']['picture_collect'] = implode(",", $arr['box']['picture_click']);
				if(!$v['merchant_name']){
					$arr['box']['merchant_name'] = M('merchant')->where(array('merchant_id'=>$v['merchant_merchant_id']))->getField('m_name');
				}
				$arr['member'] = M('admin') -> where(array('admin_id' => $v['author_id'])) -> find();
				$data[] = $arr;
			}
			return $data;
		 }
	}

	/**
	 * 完成采集画面信息的方法
	 */
	public function pic($data) {
		$re = $this -> where(array('p_id' => $data)) -> find();
		$info = M('board') -> where(array('member_member_id' => $_SESSION['member_id'])) -> select();
		$arr = array('img_url' => explode("|", $re['url']), 'title' => $re['title'], 'board' => $info, 'p_id' => $re['p_id']);
		return $arr;
	}

	public function photo($data) {
		$info = M('picture') -> where(array('p_id' => $data)) -> find();
		$arr = array(
			'url' => explode("|", $info['url']), 
			'member' => '商家', 'like' => M('water_click') -> where(array('board_list_id' => $data)) -> count(),
			'p_id' =>$info['p_id']
		);
		return $arr;
	}

	/**
	 * 创建首页获取瀑布流信息方法
	 */
	public function list_show(){
	 	$type = M('type')->where(array('is_del'=>0,'is_suggest'=>1))->select();
		$temp = array();
		foreach($type as $k=>&$v){
				if($k == 0){
					$picture = M('picture_list')->where(array('type_id'=>$v['tid']))->select();
					$adver = M('adver')->alias('a')->join('__ADVER_TYPE__ b ON a.adver_type_id = b.adver_type_id AND b.adver_type_name = "首页瀑布流广告"')->limit(0,2)->select();
				}else{
					$picture = M('picture_list')->where(array('type_id'=>$v['tid']))->select();
					$adver = M('adver')->alias('a')->join('__ADVER_TYPE__ b ON a.adver_type_id = b.adver_type_id AND b.adver_type_name = "首页瀑布流广告"')->limit($k*2,2)->select();
				}
				$att = array();
				$one['picture'] = array_slice($picture, 0,2);
				$a['picture'] = array();
				foreach ($one['picture'] as $key => $value) {
					$arr = array();
					$arr['title'] = $value['title'];
					$arr['picture_id'] = $value['picture_id'];
					$arr['img_src'] =$value['img_src'];
					$arr['picture_list_id'] = $value['picture_list_id'];
					$a['picture'][] = $arr;
				}
				$one['adver'] = $adver[0];
				$a['adver'] = $one['adver'];
//				dump($a);die;
				
				$two['picture']= array_slice($picture, 2,5);
				$b['picture'] = array();
				foreach ($two['picture'] as $key => $value) {
					$arr = array();
					$arr['title'] = $value['title'];
					$arr['picture_id'] = $value['picture_id'];
					$arr['img_src'] =$value['img_src'];
					$arr['picture_list_id'] = $value['picture_list_id'];
					$b['picture'][] = $arr;
				}
				
				$three['adver'] = $adver[1];
				$c['adver'] = $three['adver'];
				
				$three['picture'] = array_slice($picture, 7,2);
				$c['picture'] = array();
				foreach ($three['picture'] as $key => $value) {
					$arr = array();
					$arr['title'] = $value['title'];
					$arr['picture_id'] = $value['picture_id'];
					$arr['img_src'] = $value['img_src'];
					$arr['picture_list_id'] = $value['picture_list_id'];
					$c['picture'][] = $arr;
				}
				$att[] = $a;
				$att[] = $b;
				$att[] = $c;
				$temp[] = $att;
		}
		return $temp;
	}
	
//	public function up_show_num(){
//		$ids = $this->getField('p_id',TRUE);
//		$num = $this->count();
//		for($i=0;$i<$num;$i++){
//			$show_num = $i%5+1;
//			$this->where(array('p_id'=>$ids[$i]))->save(array('show_num'=>$show_num));
//		}
//	}

}
