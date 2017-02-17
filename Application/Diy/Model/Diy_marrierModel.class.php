<?php
namespace Diy\Model;
use Think\Model;
use Home\Controller\CommonController;

class Diy_marrierModel extends Model{
	
	protected $tableName = 'diy_marrier';
	
	protected $_validate = array(
	    array('age','require','请填写年龄'), 
	    array('age','','请输入正确的年龄',1,'length','4',3), 
	    array('market_price','require','市场价不能为空'), 
	    array('price','require','嘉仕澜德价不能为空'),
	    array('phone','require','手机号不能为空'),
	    array('phone','/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/','手机号格式错误'),
	    array('phone','','该手机号已使用',1,'unique',2),
	    array('email','require','邮箱号不能为空'),
	    array('email','/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/','邮箱格式错误'),
	    array('email','','该邮箱号已使用',1,'unique',2),
	    array('remark','require','描述不能为空'),
	    array('experience','require','从业经验不能为空'),
	    array('portrait','require','请选择头像'),
	    array('logo','require','请添加logo'),
	    array('showed','require','请至少添加一张作品图片'),
	    array('diy_marrier_type_id','require','请选择您所属的类型'),
	    array('password','require','请填写密码'),
	    array('address','require','请选择城市'),
	);

	public function edit_basic($data){
		//定义编号
		if(isset($data['address'][0]) && isset($data['diy_marrier_type_id'])){
			//获得城市名和类型名
			$city_id = $data['address'][0];
			$type_id = $data['diy_marrier_type_id'];
			$city_name = M('Area')->where(array('area_id'=>$city_id))->getField('aname');
			$type_name = M('Diy_marrier_type')->where(array('diy_marrier_type_id'=>$type_id))->getField('type_name');
			$common = new CommonController;
			for($i = 0;$i < 5;$i++){
				if($i < 2){
					$re .= $common->_getFirstCharter(mb_substr($city_name,$i,1));
				}else if($i == 2){
					$re .= '-';
				}else{
					$re .= $common->_getFirstCharter(mb_substr($type_name,$i-3,1));
				};
			};
			$sql = array(
				'diy_marrier.diy_marrier_id'=>array('NEQ',$data['diy_marrier_id']),
				'diy_marrier_message.diy_marrier_type_id'=>$type_id,
				'diy_marrier.address'=>array('LIKE',$city_id.'|%'),
			);
			$num = $this->join('LEFT JOIN diy_marrier_message ON diy_marrier.diy_marrier_id = diy_marrier_message.diy_marrier_id')->where($sql)->count()+1;
			if(strlen($num) == 1){
				$re .= '-00'.$num;
			}else if(strlen($num) == 2){
				$re .= '-0'.$num;
			}else{
				$re .= '-'.$num;
			};
			$data['number'] = $re;
		};
		// 是数组用|切割
		if(is_array($data['address'])) $data['address'] = implode('|',$data['address']);
		if(!$this->create($data)) return FALSE;
		$this->save($data);
		//查询详细表是否有数据
		$msgData = M('diy_marrier_message')->where(array('diy_marrier_id'=>$data['diy_marrier_id']))->find();
		$action = isset($msgData)?'save':'add';
		//编辑时存入主键
		if($action == 'save'){
			$data['diy_marrier_message_id'] = $msgData['diy_marrier_message_id'];
		};
		M('diy_marrier_message')->$action($data);
		return true;
	}

	//编辑职业信息
	public function edit_career($data){
		// 是数组截取前20条
		if(is_array($data['showed'])){
			$data['showed'] = array_slice($data['showed'],0,20);
		};
		$data['showed'] = implode(',',$data['showed']);
		$data['remark'] = str_replace("\n",'|',$data['remark']);
		if(!$this->create($data)) return FALSE;
		M('Diy_marrier_message')->where(array('diy_marrier_id'=>$data['diy_marrier_id']))->save($data);
		return true;
	}


	//获得婚礼人和婚礼人详细信息数据
	public function getData($id){
		//查找数据并合并
		$data = $this->where(array('diy_marrier_id'=>$id))-> find();
		$msgData = M('Diy_marrier_message')->where(array('diy_marrier_id'=>$id))->find();
		//没有下线
		if($msgData['status']){
			echo '<script type="text/javascript">alert("当前状态为上线,请下线再操作");parent.location.href = "'.U('index').'";</script>';die;
		};
		//没有数据时赋值为空数组
		if(empty($msgData)){
			$msgData = array();
		}else{
			$msgData['showed'] = explode(',',$msgData['showed']);
			if(!is_array($msgData['showed'])){
				$msgData['showed'] = array($msgData['showed']);
			};
		};
		$data = array_merge($data,$msgData);
		$data['address'] = explode('|',$data['address']);
		$data['remark'] = str_replace('|',"\n",$data['remark']);
		return $data;
	}
}
?>