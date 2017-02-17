<?php
namespace Admin\Controller;


//角色控制器
class RoleController extends CommonController {
	
	static private $db;
	
	public function __construct(){
		parent::__construct();
		$this -> db = D('Role');
	}
	
	//主页
    public function index(){
    	//获得所有数据
    	$data = $this -> db -> select();
    	//分配
    	$this -> assign('data',$data);
        $this -> display();
    }
    
    //添加
    public function add(){
    	//POST请求
    	if(IS_POST){
    		//执行添加方法
    		$result = $this -> db  -> store();
    		//添加失败
    		if(!$result){
    			$this -> error($this -> db ->  getError());
    		};
    		$this -> success('添加角色成功',U('index'));die;
    	};
    	//获得所有控制器
    	$sql = array('level' => 2);
    	$ctlData = M('Node') -> where($sql) -> select();
    	$this -> assign('ctlData',$ctlData);
    	//获得第一条对应的方法
    	$id = (current($ctlData)['id']);
    	$sql = array('pid' => $id,'level' => 3);
    	$actData = M('Node') -> where($sql) -> select();
    	$this -> assign('actData',$actData);
    	$this -> display();
    }

    //删除角色
    public function del(){
        $id = I('get.id',0,'intval');
        //删除表数据
        $sql = array('role_id' => $id);
        M('Access') -> where($sql) -> delete();
        M('Role_admin') -> where($sql) -> delete();
        $sql = array('id' => $id);
        $this -> db -> where($sql) -> delete();
        $this -> success('删除成功',U('index'));die;
    }

    //角色编辑
    public function edit(){
        //POST请求
        if(IS_POST){
            //执行编辑方法
            $result = $this -> db -> edit();
            //编辑失败
            if(!$result){
            	$this -> error($this -> getError());
            };
            $this -> success('编辑成功',U('index'));die;
        };
    	//当前角色数据
        $id = I('get.id',0,'intval');
        $sql = array('id' => $id);
        $roleData = $this -> db -> where($sql) -> find();
        $this -> assign('roleData',$roleData);
        //执行处理编辑页面数据方法
        $nodeData = $this -> db -> deal($id);
        $this -> assign('nodeData',$nodeData);
        //所有控制器数据
	    $sql = array('level' => 2);
	    $ctlData = M('Node') ->  where($sql) -> select();
	    $this -> assign('ctlData',$ctlData);
        $this -> display();
    }
    
    //获得方法
    public function getAction(){
    	//ajax请求
      	if(!IS_AJAX) return false;
    	$id = I('post.id',0,'intval');
    	$sql = array(
    		'pid' => $id,
    		'level' => 3
    	);
    	//获得对应的方法
    	$actData = M('Node') -> where($sql) -> select();
    	$this -> ajaxReturn($actData);die;
    }
    
}