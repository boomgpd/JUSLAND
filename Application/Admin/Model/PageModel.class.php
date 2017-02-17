<?php
	namespace Admin\Model;
	use Think\Model;
	
	class PageModel extends Model{
		
		protected $tableName = 'site_config_type';
		
		protected $_validate = array(
		    array('type_name','require','请填写页面名名'), //默认情况下用正则进行验证
		    array('remark','require','请填写中文描述'), //默认情况下用正则进行验证
		    array('type_name','','已有相同的页面名',self::MUST_VALIDATE,'unique',self::MODEL_INSERT),
		    array('remark','','已有相同的中文描述',self::MUST_VALIDATE,'unique',self::MODEL_INSERT),
   		);

   		//添加
   		public function store($data){
   			if(!$this -> create($data)) return false;
   			return $this -> add($data);
   		}

   		//编辑
   		public function edit($data){
   			if(!$this -> create($data)) return false;
   			return $this -> save($data);
   		}

	}
?>