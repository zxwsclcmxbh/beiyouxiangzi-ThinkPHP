<?php
namespace Home\Controller;

use Think\Controller;
class MessageController extends BaseController {
	
    /**
     * 发布新帖
     * @return [type] [description]
     */
    public function publish_new_message(){
        //校验参数是否存在
        if(!$_POST['user_id']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：user_id';
            $this->ajaxReturn($return_data);
        }
        if(!$_POST['username']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：username';
            $this->ajaxReturn($return_data);
        }
        if(!$_POST['face_url']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：face_url';
            $this->ajaxReturn($return_data);
        }
        if(!$_POST['content']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：content';
            $this->ajaxReturn($return_data);
        }
        //设置要插入的数据
        $data=array();
        $Message=M('Message');
        $data['user_id']=$_POST['user_id'];
        $data['username']=$_POST['username'];
        $data['face_url']=$_POST['face_url'];
        $data['content']=$_POST['content'];
        $data['total_likes']=0;
        $data['send_timestamp']=time();
        //插入数据
        dump($data);
        $result=$Message->add($data);
        if($result){
            $return_data=array();
            $return_data['error_code']=0;
            $return_data['mes']='数据添加成功';
            $this->ajaxReturn($return_data);
        }else{
            $return_data=array();
            $return_data['error_code']=2;
            $return_data['mes']='数据添加失败';
            $this->ajaxReturn($return_data);
        }
    }
    /**
     * 得到所有帖子
     * @return [type] [description]
     */
    public function get_all_message(){
        //实例化数据表
        $Message=M('Message');
        $all_messages=$Message->order('id desc')->select();//按照时间倒叙
        foreach ($all_messages as $key => $message) {
            $all_messages[$key]['send_timestamp']=date('Y-m-d H:i:s',$message['send_timestamp']);//将所有的时间戳转化
        }
        $return_data=array();
        $return_data['error_code']=0;
        $return_data['mes']='数据获取成功';
        $return_data['data']=$all_messages;
        $this->ajaxReturn($return_data);
        }
        /**
     * 得到指定用户的所有帖子
     * @return [type] [description]
     */
    public function get_one_user_all_message(){
        if(!$_POST['user_id']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：user_id';
            $this->ajaxReturn($return_data);
        }
        $Message=M('Message');
        $where=array();
        $where['user_id']=$_POST['user_id'];
        $one_user_all_message=$Message->where($where)->order('id desc')->select();//按照时间倒叙
        foreach ($one_user_all_message as $key => $message) {
            $one_user_all_message[$key]['send_timestamp']=date('Y-m-d H:i:s',$message['send_timestamp']);//将所有的时间戳转化
        }
        if($one_user_all_message){
        $return_data=array();
        $return_data['error_code']=0;
        $return_data['mes']='数据获取成功';
        $return_data['data']=$one_user_all_message;
        $this->ajaxReturn($return_data);
    }else{
        
        $return_data=array();
        $return_data['error_code']=2;
        $return_data['mes']='该用户未发表任何帖子';
        $this->ajaxReturn($return_data);
    }
    }
    /**
     * 点赞接口
     * @return [type] [description]
     */
    public function do_like(){
        if(!$_POST['message_id']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：message_id';
            $this->ajaxReturn($return_data);
        }
        if(!$_POST['user_id']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：user_id';
            $this->ajaxReturn($return_data);
        }
        
        $Message=M('Message');
        //查询条件
        $where=array();
        $where['id']=$_POST['message_id'];
        $message=$Message->where($where)->find();
        if(!$message){
            $return_data=array();
            $return_data['error_code']=2;
            $return_data['msg']='指定的帖子不存在';
            $this->ajaxReturn($return_data);
        }
        $data=array();
        $data['total_likes']=$message['total_likes']+1;
        //构造要保存的条件
        $where=array();
        $where['id']=$_POST['message_id'];
        $result=$Message->where($where)->save($data);
        if($result){
            $return_data=array();
            $return_data['error_code']=0;
            $return_data['msg']='点赞成功';
            $return_data['data']['message_id']=$_POST['message_id'];
            $return_data['data']['total_likes']=$data['total_likes'];
            $this->ajaxReturn($return_data);
        }else{
            $return_data=array();
            $return_data['error_code']=2;
            $return_data['msg']='数据保存失败';
            $this->ajaxReturn($return_data);
        }
    }
    /**
     * 删除指定帖子
     * @return [type] [description]
     */
    public function delete_message(){
        if(!$_POST['user_id']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：user_id';
            $this->ajaxReturn($return_data);
        }
        $Message=M('Message');
        //查询条件
        $where1=array();
        $where1['id']=$_POST['message_id'];
        $where2=array();
        $where2['id']=$_POST['user_id'];
        $message=$Message->where($where2)->where($where1)->delete();
        if($message){
            $return_data=array();
            $return_data['error_code']=0;
            $return_data['msg']='删除成功';
            $return_data['data']['message_id']=$_POST['message_id'];
            $this->ajaxReturn($return_data);
        }else{
            $return_data=array();
            $return_data['error_code']=2;
            $return_data['msg']='指定的数据查询不存在';
        }
    }
}