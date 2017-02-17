<?php
	namespace Admin\Model;
	use Think\Model;
	
	class AdminModel extends Model{
		
		protected $tableName = 'admin';
		
		protected $_validate = array(
		    array('admin_name','require','请填写用户名'), //默认情况下用正则进行验证
		    array('admin_name','','已有相同的用户名',self::MUST_VALIDATE,'unique',self::MODEL_INSERT),
		    array('password','require','请填写密码',2),
   		);

		//获取首页展示数据
		public function index_msg(){
			$sql = array(
				'is_admin' => array('NEQ',1),
	    		'admin_id' => array('NEQ',1)
	    	);
	    	$data = $this -> where($sql) -> select();
	    	//查询对应角色描述
	    	foreach($data as $k => $v){
	    		$sql = array('admin_id' => $v['admin_id']);
	    		$roles = M('Role_admin') -> where($sql) -> getfield('role_id',true);
	    		$roles = implode(',',$roles);
	    		$sql = array(
	    			'id' => array('IN',$roles),
	    		);
	    		$remark = M('Role') -> where($sql) -> getField('remark',true);
	    		$data[$k]['remark'] = implode(',',$remark);
	    	};
	    	return $data;
		}
		
		//获得用户编辑页面数据
		public function edit_msg($id){
			//获得当前用户数据
	    	$sql = array('admin_id' => $id);
	    	$data = $this -> where($sql) -> find();
	    	//获得对应的角色数据
	    	$sql = array('admin_id' => $data['admin_id']);
	    	$roles = M('Role_admin') -> where($sql) -> getfield('role_id',true);
	    	if(!empty($roles)){
	    		$roles = implode(',',$roles);
	    		$sql = array(
	    			'id' => array('IN',$roles)
	    		);
	    		$data['role'] = M('Role') -> where($sql) -> select();
	    	}else{
	    		$data['role'] = array();
	    	};
			return $data;
		}
		
		//添加
		public function store($data){
			if(!$this -> create()) return false;
    		if($data['admin_name'] == 'admin'){
    			$this -> error = '不能创建超级管理员';
    			return false;
    		};
    		$sql = array(
    			'admin_name' => $data['admin_name'],
    			'password' => md5(md5($data['admin_name']).md5($data['password']))
    		);
    		$id = $this -> add($sql);
    		//添加用户角色中间表数据
    		foreach($data['role'] as $k => $v){
    			$sql = array('role_id' => $v,'admin_id' => $id);
    			M('Role_admin') -> add($sql);
    		};
    		return true;
		}
		
		//编辑
		public function edit($data){
			if(!$this -> create()) return false;
			//判断是否更改密码
			if(isset($data['password'])){
				$data['admin_name'] = $this -> where(array('admin_id'=>$data['admin_id'])) -> getField('admin_name');
           		$sql = array('admin_id' => $data['admin_id'],'password' => md5(md5($data['admin_name']).md5($data['password'])));
			}else{
            	$sql = array('admin_id' => $data['admin_id']);	
			};
            $this -> save($sql);
            $sql = array('admin_id' => $data['admin_id']);
            //删除中间表数据
            M('Role_admin') -> where($sql) -> delete();
            //添加中间表数据
            foreach($data['role'] as $k => $v){
                $sql = array('role_id' => $v,'admin_id' => $data['admin_id']);
                M('Role_admin') -> add($sql);
            };
            return true;
		}
	}
?>