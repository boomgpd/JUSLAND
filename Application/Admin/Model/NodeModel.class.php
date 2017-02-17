<?php
	namespace Admin\Model;
	use Think\Model;
	
	class NodeModel extends Model{
		
		protected $tableName = 'node';
		
		protected $_validate = array(
        array('name','require','请填写名称'), //默认情况下用正则进行验证
		    array('pid','require','请选择所属'), //默认情况下用正则进行验证
   		);

		//添加方法
   		public function store_act($data){
   			if(!$this -> create()) return false;
            //查询同样的控制器是否有相同的方法
            $sql = array('name' => $data['name'],'pid' => $data['pid'],'level' => 3);
            $result = $this -> where($sql) -> find();
            if($result){
               	$this -> error = '该控制器下已有'.$data['name'].'方法';
               	return false;
            };
            return $this -> add();
   		}

   		//添加控制器
   		public function store_ctl($data){
   			if(!$this -> create()) return false;
            //查找是否有一样的控制器
            $sql = array('name' => $data['name'],'level' => 2);
            $result = $this -> where($sql) -> count();
            if($result){
                $this -> error = '已有'.$data['name'].'控制器';
                return false;
            };
    		return $this -> add();
   		}

      //编辑
      public function edit($msg){
        if(!$this -> create()) return false;
        //方法编辑
        if($msg == 'act'){
            if(empty($_POST['pid'])){
              $this -> error = '请选择控制器';
              return false;
            };
        };
        return $this -> save();
      }
	}
?>