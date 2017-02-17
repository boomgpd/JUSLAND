<?php
namespace Admin\Controller;

//节点控制器
class NodeController extends CommonController {
	
	private $db;
	
	public function __construct(){
		parent::__construct();
		$this -> db = D('Node');
	}
	
	//控制器首页
    public function index(){
    	//获得数据
    	$sql = array('level' => 2);
    	$data = $this -> db -> where($sql) -> select();
    	//分配
    	$this -> assign('data',$data);
        $this -> display();
    }
    
    //添加控制器
    public function addCtl(){
    	//POST请求
    	if(IS_POST){
            //执行添加方法
            $_POST['level'] = 2;
            $_POST['pid'] = 1;
            $data = I('post.');
    		$result = $this -> db -> store_ctl($data);
            //添加失败
            if(!$result){
                $this -> error($this -> db -> getError());
            };
    		$this -> success('添加控制器成功',U('Node/index'));
    	};
    	$this -> display();
    }
    
    //添加方法
    public function addAct(){
    	//POST请求
    	if(IS_POST){
            //执行添加方法
            $_POST['level'] = 3;
            $data = I('post.');
            $result = $this -> db -> store_act($data);
            //添加失败
            if(!$result){
                $this -> error($this -> db -> getError());
            };
    		$this -> success('添加方法成功',U('addAct'));
    	};
		//获得所有控制器
		$sql = array('level' => 2);
		$ctlData = $this -> db -> where($sql) -> select();
		$this -> assign('ctlData',$ctlData);
		$this -> display();
    }
    

    //方法列表
    public function actList(){
    	$id = I('get.id',0,'intval');
    	$sql = array('pid' => $id,'level' => 3);
    	$data = $this -> db -> where($sql) -> select();
    	$this -> assign('data',$data);
    	$this -> display();
    }
    
    //删除
    public function del(){
    	$id = I('get.id',0,'intval');
    	$sql = array('id' => $id);
    	$level = $this -> db  -> where($sql) -> getfield('level');
        //判断删除的是什么
    	if($level == 3){
    		$this -> db -> where($sql) -> delete();
    	}else{
    		$sql['pid'] = $id;
    		$sql['_logic'] = 'OR';
    		$this -> db -> where($sql) -> delete();
    	};
    	$this -> success('删除成功',U('index'));
    }
    
    //控制器编辑
    public function ctlEdit(){
    	$id = I('get.id',0,'intval');
    	//POST请求
    	if(IS_POST){
    		$result = $this -> db -> edit('ctl');
            if(!$result){
                $this -> error($this -> getError());
            };
    		$this -> success('编辑成功',U('index'));die;
    	};
    	$sql = array('id' => $id);
    	$data = $this -> db -> where($sql) -> find();
    	$this -> assign('data',$data);
    	$this -> display();
    }
    
    //方法编辑
    public function actEdit(){
    	//POST请求
    	if(IS_POST){
    		$result = $this -> db -> edit('act');
            if(!$result){
                $this -> error($this -> getError());
            };
            $this -> success('编辑成功',U('index'));die;
    	}
    	$id = I('get.id',0,'intval');
    	$sql = array('id' => $id);
    	$data = $this -> db -> where($sql) -> find();
    	$this -> assign('data',$data);
    	//查询所有控制器
    	$sql = array('level' => 2);
		$ctlData = $this -> db -> where($sql) -> select();
		$this -> assign('ctlData',$ctlData);
    	$this -> display();
    }

}