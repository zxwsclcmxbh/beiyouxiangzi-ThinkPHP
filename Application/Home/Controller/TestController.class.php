<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends BaseController {
	public function test(){
		var_dump(123);
	}
	public function insert_test(){
    //实例化数据表
		$Message=M('Message');
    //插入数据
		$data=array();
		$data['user_id']=1;
		$data['username']='张三';
		$data['face_url']='xxx.jpg';
		$data['content']='我爱火锅';
		$data['total_likes']=0;
		$data['send_timestamp']=time();
		// $result=$Message->add($data);
		var_dump($result);
	}    
	public function select_test(){
	//实例化数据表
		$Message=M('Message');
    //设置查询条件
		$where = array();
		$where['user_id'] = 1;
    //查询多条数据
		$all_messages = $Message->where($where)->select();
		dump($all_messages);
	}
	public function find_test(){
	//实例化数据表
		$Message=M('Message');
    //设置查询条件
		$where = array();
		$where['user_id'] = 1;
    //查询一条数据
		$all_messages = $Message->where($where)->find();
		dump($all_messages);
	}
	public function save_test(){
	//实例化数据表
		$Message=M('Message');
    //设置修改条件
		$where = array();
		$where['id'] = 1;
    //要保存的数据
		$data=array();
		$data['total_likes']=1;
    //保存
		$result=$Message->where($where)->save($data);
		dump($result);
	}
	public function delete_test(){
	//实例化数据表
		$Message=M('Message');
    //设置条件
		$where = array();
		$where['id'] = 1;
    //删除
		$result=$Message->where($where)->delete();
		dump($result);
	}
}