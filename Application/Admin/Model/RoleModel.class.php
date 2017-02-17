<?php
	namespace Admin\Model;
	use Think\Model;
	
	class RoleModel extends Model{
		
		protected $tableName = 'role';
		
		protected $_validate = array(
		    array('name','require','请填写角色名'), //默认情况下用正则进行验证
		    array('name','','已有相同的角色名',self::MUST_VALIDATE,'unique'), 
   		);

		//添加角色
		public function store(){
			if(!$this -> create()) return false;
			$data = I('post.');
    		//添加角色表数据
    		$sql = array(
    			'name' => $data['name'],
    			'remark' => $data['remark']
    		);
    		$id = $this -> add($sql);
    		M('Access') -> add(array('role_id' => $id,'node_id' => 1));
    		//添加中间表数据
    		foreach($data['node'] as $k => $v){
    			//控制器
    			$sql = array(
    				'role_id' => $id,
    				'node_id' => $k
    			);
    			M('Access') -> add($sql);
    			//去重
    			$v = array_unique($v);
    			//方法
    			foreach($v as $kk => $vv){
    				$sql = array(
    					'role_id' => $id,
    					'node_id' => $vv
    				);
    				M('Access') -> add($sql);
    			};
    		};
    		return true;
		}
		
		//编辑角色
		public function edit(){
			if(!$this -> create()) return false;
			$data = I('post.');
            $sql = array('id' => $data['id'],'name' => $data['name'],'remark' => $data['remark']);
            $this -> save($sql);
            //删除对应角色的所有中间表数据
            $sql = array('role_id' => $data['id']);
            M('Access') -> where($sql) -> delete();
            M('Access') -> add(array('role_id'=>$data['id'],'node_id'=>1));
            // dump($data);die;
            //添加中间表数据
            foreach($data['node'] as $k => $v){
                //添加控制器
                $sql = array('role_id' => $data['id'],'node_id' => $k);
                M('Access')->add($sql);
                $v = array_unique($v);
                //添加方法
                foreach($v as $kk => $vv){
                    $sql = array('role_id' => $data['id'],'node_id' => $vv);
                    M('Access') -> add($sql);
                };
            };
            return true;
		}
		
		//处理编辑页面数据
		public function deal($id){
	        //角色拥有的控制器数据
	        $sql = array('role_id' => $id);
	        $nodes = M('Access') -> where($sql) -> getfield('node_id',true);
	        if(!empty($nodes)){
	            $nodes = implode(',',$nodes);
	            //查询方法数据并找到对应的控制器数据
	            $sql = array(
	                'level' => 3,
	                'id' => array('IN',$nodes),
	            );
	            $nodeData = M('Node') -> where($sql) -> select();

	            foreach($nodeData as $k => $v){
	                //判断类型
	                $sql = array('id' => $v['pid']);
	                $nodeData[$k]['controller'] = M('Node') -> where($sql) -> find();
	                $sql = array('pid' => $v['pid'],'level' => 3);
	                $nodeData[$k]['actData'] = M('Node') -> where($sql) -> select();
	            };
	        };
	        return $nodeData;
		}
	}
?>