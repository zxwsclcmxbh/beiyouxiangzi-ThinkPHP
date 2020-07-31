<?php
namespace Home\Controller;

use Think\Controller;
class UserController extends BaseController {
    /**
    *用户注册
    *@return [type][description]
    */
    public function sign(){
        //校验参数是否存在
        
        if(!$_POST['StudentID']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：StudentID';
            $this->ajaxReturn($return_data);
        }
        if(!$_POST['password']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：password';
            $this->ajaxReturn($return_data);
        }
        if(!$_POST['password_again']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：password_again';           
            $this->ajaxReturn($return_data);
        }
        //检验两次密码是否输入一致
        if($_POST['password']!=$_POST['password_again']){
            $return_data=array();
            $return_data['error_code']=2;
            $return_data['msg']='两次密码不一致';           
            $this->ajaxReturn($return_data);
        }
        //构造查询条件
        $where=array();
        $where['StudentID']=$_POST['StudentID'];
        $User=M('user');
        $user=$User->where($where)->find();
        if($user){
            //如果用户已经注册
            $return_data=array();
            $return_data['error_code']=3;
            $return_data['msg']='该学号已经被注册';           
            $this->ajaxReturn($return_data);
        }
        else{
            //如果用户没有注册
            //构建插入的数据
            $data=array();
            $data['username']=$_POST['username'];
            $data['StudentID']=$_POST['StudentID'];
            $data['password']=md5($_POST['password']);//md5加密
            $data['face_url']=$_POST['face_url'];
            //插入数据
            $result=$User->add($data);//add函数添加成功后返回的就是该条数据得id
            if($result){
                //插入成功
                $return_data=array();
                $return_data['error_code']=0;
                $return_data['msg']='注册成功';
                $return_data['data']['user_id']=$result;
                $return_data['data']['username']=$_POST['username'];
                $return_data['data']['StudentID']=$_POST['StudentID'];
                $return_data['data']['face_url']=$_POST['face_url'];
                $this->ajaxReturn($return_data);
            }else{
                //插入失败
                $return_data=array();
                $return_data['error_code']=4;
                $return_data['msg']='注册失败';
                $this->ajaxReturn($return_data);
            }
        }

    }
    /**
    *用户登录
    *@return [type][description]
    */
    public function login(){
        //校验参数是否存在
        if(!$_POST['StudentID']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：StudentID';
            $this->ajaxReturn($return_data);
        }
        if(!$_POST['password']){
            $return_data=array();
            $return_data['error_code']=1;
            $return_data['msg']='参数不足：password';
            $this->ajaxReturn($return_data);
        }
        //查询用户
        $User=M('user');
        $where=array();
        $where['StudentID']=$_POST['StudentID'];
      
        $user=$User->where($where)->find();
        if($user){
            //如果查询到该学号
            //比对密码是否正确
            if(md5($_POST['password'])!=$user['password']){
                $return_data=array();
                $return_data['error_code']=3;
                $return_data['msg']='密码不正确，请重新输入';
                $this->ajaxReturn($return_data);
            }else{
                //如果密码一样
                $return_data=array();
                $return_data['error_code']=0;
                $return_data['msg']='登陆成功';
                $return_data['data']['user_id']=$user['id'];
                $return_data['data']['username']=$user['username'];
                $return_data['data']['StudentID']=$user['StudentID'];
                $return_data['data']['face_url']=$user['face_url'];
                $this->ajaxReturn($return_data);
            }

        }else{
            $return_data=array();
            $return_data['error_code']=2;
            $return_data['msg']='不存在该学号请注册';
            $this->ajaxReturn($return_data);
        }
        }

     

}