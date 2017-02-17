<?php
namespace Admin\Controller;


//页面类型控制器
class PageController extends CommonController {
	
	static private $db;
	
	public function __construct(){
		parent::__construct();
		$this -> db = D('Page');
	}

    //首页
    public function index(){
        $data = $this -> db -> select();
        $this -> assign('data',$data);
        $this -> display();
    }
	
    //添加
	public function add(){
        //POST请求
        if(IS_POST){
            $data = I('post.');
            $result = $this -> db -> store($data);
            if(!$result){
                $this -> error($this -> db -> getError());
            };
            $this -> success('添加成功',U('index'));
        };
        $this -> display();
    }

    //删除
    public function del(){
        $id = I('get.id',0,'intval');
        $this -> db -> where(array('site_config_type_id' => $id)) -> delete();
        $this -> success('删除成功',U('index'));
    }

    //编辑
    public function edit(){
        if(IS_POST){
            $data = I('post.');
            $result = $this -> db -> edit($data);
            if(!$result){
                $this -> error($this -> db -> getError());
            };
            $this -> success('编辑成功',U('index'));
        };
        $id = I('get.id',0,'intval');
        $data = $this -> db -> where(array('site_config_type_id' => $id)) -> find();
        $this -> assign('data',$data);
        $this -> display();
    }


}