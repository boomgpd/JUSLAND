<?php
namespace Admin\Model;
use Think\Model;

class MerrModel extends Model{
	
	protected $tableName = 'mer_role';
	
	protected $_validate = array(
		array('name','require','角色名不能为空',1),
		array('remark','require','描述不能为空',1),
	);

	protected $auto = array(
		array('addtime','time',1,'function')
	);

	//添加
	public function store($post){
		if(!$this->create($post)) return FALSE;
		$id = $this->add($post);
		foreach($post['data'] as $k => $v){
			M('Mer_role_node')->add(array('role_id'=>$id,'node_id'=>$k));//系统
			foreach($v as $a => $b){
				M('Mer_role_node')->add(array('role_id'=>$id,'node_id'=>$a));//模块
				foreach($b as $d => $w){
					M('Mer_role_node')->add(array('role_id'=>$id,'node_id'=>$d));//控制器
					$w = array_unique($w);//去重
					foreach($w as $kk => $vv){//方法
						M('Mer_role_node')->add(array('role_id'=>$id,'node_id'=>$vv));
					};
				};
			};
		};
		return true;
	}

	//获得指定数据
	public function get_data($id,$level,$type=FALSE,$pid=NULL){
		$nodes = M('Mer_role_node')->where(array('role_id'=>$id,'is_del'=>0))->getField('node_id',true);
		$nodes = implode(',',$nodes);
		$condition = array(
			'id' => array('IN',$nodes),
			'level' => $level,
			'is_del' => 0,
		);
		if($type === true){
			$condition['id'] = array('NOT IN',$nodes);
		};
		if(!is_null($pid)){
			$condition['pid'] = $pid;
		};
    	$data = M('Mer_node')->where($condition)->select();
    	return $data;
	}
}
?>