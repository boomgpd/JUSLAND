<?php
namespace Admin\Controller;


//用户控制器
class AdminController extends CommonController {
	
	static private $db;
	
	public function __construct(){
		parent::__construct();
		$this -> db = D('Admin');
	}
	
	//首页
    public function index(){
    	//执行获取首页数据方法
    	$data = $this -> db -> index_msg();
    	$this -> assign('data',$data);
        $this -> display();
    }
    
    //添加
    public function add(){
    	//POST请求
    	if(IS_POST){
    		//执行添加
            $data = I('post.');
    		$result = $this -> db -> store($data);
    		//添加失败
    		if(!$result){
    			$this -> error($this -> db -> getError());
    		};
    		$this -> success('添加用户成功',U('index'));die;
    	};
    	//获得所有角色
    	$roleData = M('Role') -> select();
    	$this -> assign('roleData',$roleData);
    	//获得第一条对应的描述
    	$id = (current($roleData)['id']);
    	$sql = array('id' => $id,);
    	$remark = M('Role') -> where($sql) -> getfield('remark');
    	$this -> assign('remark',$remark);
    	$this -> display();
    }
    
    //编辑用户
    public function edit(){
        //POST请求
        if(IS_POST){
            //执行编辑
            $data = I('post.');
            $result = $this -> db -> edit($data);
            //编辑失败
            if(!$result){
            	$this -> error($this -> db -> getError());
            };
            $this -> success('修改成功',U('index'));die;
        };
        //执行获取用户编辑页面数据
        $id = I('get.id',0,'intval');
        $data = $this -> db -> edit_msg($id);
        $this -> assign('data',$data);
        //获得所有角色数据
        $roleData = M('Role') -> select();
        $this -> assign('roleData',$roleData);
    	$this -> display();
    }
    
    //删除用户
    public function del(){
    	$id = I('get.id',0,'intval');
    	//删除表数据	
    	$sql = array('admin_id'=> $id);
    	M('Role_admin') -> where($sql) -> delete();
    	$this -> db -> where($sql) -> delete();
    	$this -> success('删除成功',U('index'));
    }
    
    //获得描述
    public function getRemark(){
    	if(!IS_AJAX) return false;
    	$id = I('post.id',0,'intval');
    	$sql = array('id' => $id);
    	$remark = M('Role')  -> where($sql) -> getfield('remark');
    	echo $remark;die;
    }
    
    //角色列表
    public function roleList(){
    	$id = I('get.id',0,'intval');
    	$sql = array('admin_id' => $id);
    	$roles = M('Role_admin')  -> where($sql) -> getfield('role_id',true);
    	if(!empty($roles)){
	    	$roles = implode(',',$roles);
	    	//查询对应的角色
	    	$sql = array(
	    		'id' => array('IN',$roles),
	    	);
	    	$data = M('Role') -> where($sql) -> select();
	    	$this -> assign('data',$data);
    	};
    	$this -> display();
    }
    
    //删除角色
    public function delRole(){
    	$id = I('get.id',0,'intval');
    	$sql = array('role_id' => $id);
		M('Role_admin') -> where($sql) -> delete();
		$this -> success('删除成功',U('index'));
    }

    //异步添加角色
    public function add_role(){
        $str = '';
        $str .= '<tr class="info"><td width="50%"><span id="">请选择角色:</span><select name="role[]" class="" style="padding-left:20%;padding-right: 20%;margin-left:10%">';
        $roleData = M('Role') -> select();
        //不为空时循环压入
        if(!empty($roleData)){
            foreach($roleData as $k => $v){
                $str .=("<option value='{$v['id']}'>{$v['name']}</option>");
            };
        }else{
            echo false;die;
        };
        $str .= '</select></td><td class="msg" width="40%"><span id="">角色描述:</span><input type="" name="" id="" value="'.$roleData[0]['remark'].'"style="margin-left:10%;"/></td><td><span class="btn btn-danger del">删除角色</span></td>';
        echo $str;die;
    }
}