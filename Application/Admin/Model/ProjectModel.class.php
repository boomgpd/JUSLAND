<?php
namespace Admin\Model;
use Think\Model;

class ProjectModel extends Model{
	
	protected $tableName = 'project';
	
	protected $_validate = array(
	    array('pname','require','请填写项目名',1,'',3),
	    array('pname','','已有一样的项目名',1,'unique',1),
	    array('url','require','请填写跳转地址',1,'',3),
	    array('url','','已有一样的跳转地址',1,'unique',1),
	    array('title','require','请填写项目中文名',1,'',3),
	    array('pid','require','数据异常',1,'',2)
	);

	//添加
	public function store($post){
		if(!$this->create($post)) return FALSE;
		$post['addtime'] = time();
		$post['key'] = md5($post['pname']);
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		$pname = $this->where(array('is_del'=>0,'pid'=>array('NEQ',$post['pid']),'pname'=>$post['pname']))->getField('pname');
		if(!is_null($pname)){
			$this->error = '已有一样的项目';
			return FALSE;
		};
		$url = $this->where(array('is_del'=>0,'pid'=>array('NEQ',$post['pid']),'url'=>$post['url']))->getField('url');
		if(!is_null($url)){
			$this->error = '已有一样的跳转地址';
			return FALSE;
		};
		$this->save($post);
		return true;
	}

	//编辑权限
	public function auth($post=NULL){
		if(!isset($post['aid'])){
			$this->error = '数据异常';
			return FALSE;
		};
		M('Project_admin')->where(array('is_del'=>0,'aid'=>$post['aid']))->save(array('is_del'=>1));
		foreach($post['pid'] as $k => $v){
			$data['aid'] = $post['aid'];
			$data['pid'] = $v;
			$data['addtime'] = time();
			M('Project_admin')->add($data);
		};
		return true;
	}

}
?>