<?php namespace Home\Controller;
use Think\Controller;
use Common\Model\BespokeModel;
use Common\Model\AreaModel;
/**
 * 创建策划报价
 * 9.23
 * geyu
 */
class PlotController extends CommonController {
	protected $bespoke;
	protected $area;
	public function __construct (){
		parent::__construct();
		$this->bespoke = new BespokeModel;
		$this->area = new AreaModel;
	}
	/**
	 * 创建免费上门显示的方法
	 */
	public function index_free(){
		if(IS_POST){
			$data = I('post.');
			$re   = $this->bespoke->store_plot($data);
			if(!$re){
				echo '<script type="text/javascript">alert("申请失败");location.href = "'.U('home/plot/index_free').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("申请成功，工作人员会在12小时内与您取得联系，请保持手机畅通；");location.href = "'.U('home/plot/index_free').'"</script>';
			}
		}
		$data['area'] = $this->area->getData();
		$this->assign('data',$data);
		$re     = "王，李，张，刘，陈，杨，黄，吴，赵，周，徐，孙，马，朱，胡，林，郭，何，高，罗，郑，梁，谢，宋，唐，许，邓，冯，韩，曹，曾，彭，萧，蔡，潘，田，董，袁，于，余，叶，蒋，杜，苏，魏，程，吕，丁，沈，任，姚，卢，傅，钟，姜，崔，谭，廖，范，汪，陆，金，石，戴，贾，韦，夏，邱，方，侯，邹，熊，孟，秦，白，江，阎，薛，尹，段，雷，黎，史，龙，陶，贺，顾，毛，郝，龚，邵，万，钱，严，赖，覃，洪，武，莫，孔";
		$name   = explode('，',$re);
		$sex    = array(0=>'先生',1=>'女士'); 
		$type   = array(0=>'定制婚礼',1=>'大众婚礼',2=>'主题婚礼');
		$phone1 = array(0=>'136',1=>'147',2=>'138',3=>'150',4=>'151',5=>'187',6=>'155',7=>'177',8=>'144');
		$phone2 = array(0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9');
		$phone3 = array(0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9');
		for ($i=0; $i <C('__INFO_NUM__') ; $i++) { 
			$names = array_rand($name,1);
			$sexs = array_rand($sex,1);
			$types = array_rand($type,1);
			$phone1s = array_rand($phone1,1);
			$phone2s = array_rand($phone2,1);
			$phone3s = array_rand($phone3,1);
			$array[] = array(
				'member' => $name[$names].$sex[$sexs],
				'type'   => $type[$types],
				'phone'  => $phone1[$phone1s].'******'.$phone2[$phone2s].$phone3[$phone3s],
				'time'   => date("m月d日")
			);
		}
		$this->assign('array',$array);
		$this->display();
	}
	/**
	 * 创建策划报价显示的方法
	 */
	public function index_plot(){
		if(IS_POST){
			$data = I('post.');
			$re   = $this->bespoke->store_plot($data);
			if(!$re){
				echo '<script type="text/javascript">alert("申请失败");location.href = "'.U('home/plot/index_plot').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("申请成功，工作人员会在12小时内与您取得联系，请保持手机畅通；");location.href = "'.U('home/plot/index_plot').'"</script>';
			}
		}
		$data['area'] = $this->area->getData();
		$this->assign('data',$data);
		$sex    = array(0=>'先生',1=>'女士'); 
		$type   = array(0=>'定制婚礼',1=>'大众婚礼',2=>'主题婚礼');
		$phone1 = array(0=>'136',1=>'147',2=>'138',3=>'150',4=>'151',5=>'187',6=>'155',7=>'177',8=>'144');
		$phone2 = array(0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9');
		$phone3 = array(0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9');
		$price  = array(0=>'3万元左右',1=>'10万元以上',2=>'5万元左右',3=>'20万元左右',4=>'8万元以下',5=>'55万元左右'); 
		$serce  = array(0=>'全包',1=>'人员+服饰'); 
		$form   = array(0=>'酒楼婚礼',1=>'酒店婚礼',2=>'草坪婚礼',3=>'教堂婚礼',4=>'户外婚礼',5=>'其他婚礼');
		for ($i=0; $i <C('__INFO_NUM__') ; $i++) { 
			$sexs = array_rand($sex,1);
			$types = array_rand($type,1);
			$phone1s = array_rand($phone1,1);
			$phone2s = array_rand($phone2,1);
			$phone3s = array_rand($phone3,1);
			$forms = array_rand($form,1);
			$serces = array_rand($serce,1);
			$prices = array_rand($price,1);
			$array[] = array(
				'type'   => $type[$types],
				'phone'  => $phone1[$phone1s].'******'.$phone2[$phone2s].$phone3[$phone3s],
				'time'   => date("m月d日"),
				'form'   => $form[$forms],
				'serce'  => $serce[$serces],
				'price'  => $price[$prices]
			);
		}
		$this->assign('array',$array);
		$this->display();
	}
	/**
	 * 创建结婚保显示的方法
	 */
	public function index_ensure(){
		if(IS_POST){
			$data = I('post.');
			$re   = $this->bespoke->store_plot($data);
			if(!$re){
				echo '<script type="text/javascript">alert("申请失败");location.href = "'.U('home/plot/index_ensure').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("申请成功，工作人员会在12小时内与您取得联系，请保持手机畅通；");location.href = "'.U('home/plot/index_ensure').'"</script>';
			}
		}
		$data['area'] = $this->area->getData();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建结婚贷显示的方法
	 */
	public function index_credit(){
		if(IS_POST){
			$data = I('post.');
			$re   = $this->bespoke->store($data);
			
			if(!$re){
				echo '<script type="text/javascript">alert("申请失败");location.href = "'.U('home/plot/index_credit').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("申请成功，工作人员会在12小时内与您取得联系，请保持手机畅通；");location.href = "'.U('home/plot/index_credit').'"</script>';
			}
		}
		$this->display();
	}
	/**
	 * 创建策划报价立即报价计算机
	 */
	public function index_computer(){
		if(IS_AJAX){
			$data = I('post.area_id');
			$re = M('area')->where(array('pid'=>$data))->select();
			$this->ajaxReturn($re);exit;
		}
		$data['area'] = M('area')->where(array('pid'=>0))->select();
		$data['count'] = M('count_type_prica')->where(array('is_del'=>0))->select();
//		dump($data['count']);die;
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 计算
	 */
	public function chenk(){
		if(IS_AJAX){
			$data = I('post.');
//			$where1['area_id'] = implode('|', $data['area_id']);
			$where['count_type_prica_id'] = array('in',implode(",", $data['count_type_prica_id']));
			$re['detail'] = M('detail_area_prica')->field('addtime,area_id,detail_area_prica_id,is_del',TRUE)->where(array('area_id',implode('|', $data['area_id'])))->find();
			$count =  array_sum(M('count_type_prica')->where($where)->getField('add_prica',TRUE));
			foreach($re['detail'] as $k=>$v){
				$re['detail'][$k] = sprintf("%.2f", ($v+$count)/10000);
			}
			$re['zhong'] = sprintf("%.2f", array_sum($re['detail']));
			$this->ajaxReturn($re);exit;
		}
	}
}
	
	
	
	
?>