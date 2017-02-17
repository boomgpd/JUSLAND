<?php namespace Common\Model;
use Think\Model;

/**
 * 创建广告类型模型
 */
class Diy_marrierModel extends Model{
	protected $tableName="diy_marrier";
	
	protected $_validate = array(
      array('name','require','请填写用户名'),
      array('name','','已有相同的用户名',1,'unique',1),
      array('password','require','密码不能为空'),
      array('phone','require','手机号不能为空'),
      array('phone','','该手机已经注册',1,'unique',1),
      array('phone','/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/','手机号格式错误'),

  );

  public function store($data){
      if(!$this->create($data)) return FALSE;//验证失败
      $data['password'] = md5(md5($data['name']).md5($data['password']));
      return $this->add($data);

  } 
   
    /**
    * 创建判定登录信息是否正确方法
    */
    public function check_login($data){
    		$oldData = $this->where(array('name'=>$data['name'],'is_del'=>0))->find();
    		if(!$oldData){
    			$oldData = $this->where(array('phone'=>$data['name'],'is_del'=>0))->find();
    		}
    		
    		if(!$oldData) return array('type'=>102,'content'=>'用户名不存在');
    		$new_pwd = md5(md5($oldData['name']).md5($data['password']));
    		if($new_pwd != $oldData['password'])  return array('type'=>103,'content'=>'密码输入错误');
    		if($oldData['is_apply'] == 0) return array('type'=>104,'content'=>'您的信息暂未通过审核，请耐心等待！');
    		$_SESSION['diy_marrier_id'] = $oldData['diy_marrier_id'];
    		$_SESSION['diy_marrier_name'] = $oldData['name'];
    		return array('type'=>100,'content'=>'登录成功','urls'=>U('Diy/index/index'));
    }


    //列表页数据
    public function lists_data($type_id,$city_id){
      //获得切换城市的最高级父级
      $parent_id = $this->get_parent($city_id).'|%';
      $sql = array(
        'diy_marrier.is_del'=>0,
        'diy_marrier_message.status'=>1,
        'diy_marrier.is_apply'=>1,
        'diy_marrier.address'=>array('LIKE',$parent_id),
        'diy_marrier_message.diy_marrier_type_id'=>1
      );
      if(!is_null($type_id)) $sql['diy_marrier_message.diy_marrier_type_id'] = $type_id;
      $data = M('Diy_marrier')->join('LEFT JOIN diy_marrier_message ON diy_marrier.diy_marrier_id = diy_marrier_message.diy_marrier_id')->join('RIGHT JOIN diy_marrier_type ON diy_marrier_message.diy_marrier_type_id = diy_marrier_type.diy_marrier_type_id')->where($sql)->select();
      foreach($data as $k => $v){
        $v['showed'] = explode(',',$v['showed']);
        if(!is_array($v['showed'])){
          $v['showed'] = array($v['showed']);
        };
        $data[$k]['showed'] = $v['showed'];
      };
      //进行分页处理
      return $this->page($data);
    }


    //进行分页处理
    public function page($data,$num=29){
      $count = count($data);
      $page = new \Think\Page($count,$num);
      $page->setConfig('first','首页');
      $page->setConfig('prev','上一页');
      $page->setConfig('next','下一页');
      $page->lastSuffix = false;
      $page->setConfig('last','尾页');
      $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% <span class="page_des">第 '.I('p',1).' 页/共 %TOTAL_PAGE% 页 ( '.$num.' 条/页 共 %TOTAL_ROW% 条)</span>');
      $result['show'] = $page->show();
      $result['data'] = array_slice($data,$page->firstRow,$page->listRows);
      $result['type']= 1;
      if($count<=$num){
        $result['type']= 0;
      }
      return $result;
    }

    //获得父级id
    private function get_parent($city_id){
        $parent = M('Area')->where(array('area_id'=>$city_id))->find();
        if(!$parent['pid']){
          return $parent['area_id'];
        }else{
          $this->get_parent($parent['area_id']);
        }
    }
   
   
}
