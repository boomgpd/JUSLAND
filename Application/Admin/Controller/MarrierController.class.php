<?php
namespace Admin\Controller;
use Common\Model\Diy_marrierModel;


//婚礼人控制器
class MarrierController extends CommonController {
	
	static private $db;
	
	public function __construct(){
		parent::__construct();
		$this -> db = new Diy_marrierModel;
	}
	
	//首页
    public function index(){
    	$data = $this -> db -> where(array('is_del' => 0,'is_apply' => 1)) -> select();
        $this -> assign('data',$data);
        $this -> display();
    }

    //审核页面
    public function apply(){
        $data = $this -> db -> where(array('is_del' => 0,'is_apply' => 0)) -> select();
        $this -> assign('data',$data);
        $this -> display();
    }

    //审核通过
    public function check_apply(){
        $id = I('get.id',0,'intval');
        $this -> db -> where(array('diy_marrier_id' => $id)) -> save(array('is_apply' => 1));
        echo '<script type="text/javascript">alert("成功");location.href = "'.U('Marrier/apply').'";</script>';
    }

    //删除
    public function del(){
        $id = I('get.id',0,'intval');
        $this -> db -> where(array('diy_marrier_id' => $id)) -> save(array('is_del' => 1));
        echo '<script type="text/javascript">alert("删除成功");location.href = "'.U('Marrier/apply').'";</script>';

    }
    
}