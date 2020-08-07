<?php
namespace app\index\controller;

use app\index\model\User;
use think\Exception;
use think\exception\DbException;
use think\Request;

// 调用相关表的模型，并使用别名易区分类名和函数名
use app\index\model\Post as Post_Model;
use app\index\model\Reply as Reply_Model;
use app\index\model\User as User_Model;
use app\index\model\Reply_like;
use app\index\model\Post_like;


class Post
{
    // ult函数
    private function error_user_id()
    {
        return json(1, "user_id 不正确或未查询到该用户");
    }

    private function miss_params($params = array())
    {
        $miss = "";
        for($x = 0; $x < count($params); $x ++)
        {
            $miss .= ($params[$x] . " ");
        }
        return json(2, $miss . "参数为空");
    }

    private function error_inner()
    {
        return json(3, "服务器内部错误 ");
    }

    // 发帖接口函数
    public function post(Request $request)
    {
        // 获取前端传入json数据
        $input_Arr = json_decode($request->getInput(), true);

        // 检测空键值
        $miss = array();
        foreach($input_Arr as $key=>$value)
        {
            if($value == "")
                $miss[count($miss)] = $key;
        }
        if(count($miss) > 0)
            return $this->miss_params($miss);

        // 检测user_id
        try {
            $user = User_Model::get($input_Arr['user_id']);
            if(!$user)
                return $this->error_user_id();
        } catch (DbException $e) {
            return $this->error_inner();
        }

        // 存储帖子数据
        $post_ins['body'] = serialize($input_Arr['content']);
        $post_ins['user_id'] = $user->user_id;
        $post_ins['likes'] = 0;
        $post_ins['layer'] = 1;
        $post_ins['tag'] = $input_Arr['post_type'];
        $post_ins['pic_url'] = serialize($input_Arr['pic_Arr']);
        $post_ins['time'] = date('Y-m-d h:i:s', time());

        if($result = Post_Model::create($post_ins)){
            $data = array(
                "post_id" => $result->post_id,
                "post_type" => $result->tag,
                "time" => $result->time,
                "content" => unserialize($result->body),
                "pic_Arr" => unserialize($result->pic_url),
                "starter" => array(
                    "user_id" => $user->user_id,
                    "avatarUrl" => $user->icon,
                    "nickname" => $user->name
                ),
                "interaction" => array(
                    "likes" => $result->likes,
                    "responds" => ($result->layer - 1)
                )
            );
            json(0, "发帖成功", $data);
        }else{
            return $this->error_inner();
        }
    }

    // 回复接口函数
    public function reply(Request $request)
    {
        // 获取前端传入json数据
        $input_Arr = json_decode($request->getInput(), true);

        // 检测空键值
        $miss = array();
        foreach($input_Arr as $key=>$value)
        {
            if($value == "")
                $miss[count($miss)] = $key;
        }
        if(count($miss) > 0)
            return $this->miss_params($miss);

        // 检测user_id
        try {
            $user = User_Model::get($input_Arr['user_id']);
            if(!$user)
                return $this->error_user_id();
        } catch (DbException $e) {
            return $this->error_inner();
        }

        // 存储回复数据
        $reply_ins = array(
            'user_id' => $user->user_id,
            'post_id' => $input_Arr['post_id'],
            'reply_body' => $input_Arr['content'],
            'pic_url' => serialize($input_Arr['pic_Arr']),
            'time' => date('Y-m-d h:i:s', time()),
            'likes' => 0,
        );

        if($input_Arr['layer'] == 1){
            try {
                $post = Post_Model::get($input_Arr['post_id']);
                if(!$post){
                    return $this->error_inner();
                }else{
                    $reply_ins['layer'] = $post->layer + 1;
                    $reply_ins['id'] = 1;
                    $post->layer = $post->layer + 1;
                    if($post->save() == false)
                        return $this->error_inner();
                }
            } catch (DbException $e) {
                return $this->error_inner();
            }
        }else{
            try {
                $responds = Reply_Model::all(['post_id' => $input_Arr['post_id'], 'layer' => $input_Arr['layer']]);
            } catch (DbException $e) {
                return $this->error_inner();
            }
            $reply_ins['layer'] = $input_Arr['layer'];
            $reply_ins['id'] = count($responds) + 1;
        }

        if($result = Reply_Model::create($reply_ins)){
            $data = array();
            try {
                $post = Post_Model::get($input_Arr['post_id']);
                $user = User_Model::get($post->user_id);

                $data[0] = array(
                    "post_id" => $post->post_id,
                    "post_type" => $post->tag,
                    "time" => $post->time,
                    "content" => unserialize($post->body),
                    "pic_Arr" => unserialize($post->pic_url),
                    "starter" => array(
                        "user_id" => $user->user_id,
                        "avatarUrl" => $user->icon,
                        "nickname" => $user->name
                    ),
                    "interaction" => array(
                        "likes" => $post->likes,
                        "responds" => ($post->layer - 1)
                    )
                );

                for($x = 1; $x < $post->layer; $x ++)
                {
                    $count = 0;
                    $reply_list = Reply_Model::where('post_id', $post->post_id)->where('layer', $x + 1)->order('id', 'asc')->select();
                    $replies = array();
                    foreach ($reply_list as $reply)
                    {
                        if($count == 0){
                            $user = User_Model::get($reply->user_id);
                            $data[$x] = array(
                                "layer" => ($x + 1),
                                "time" => $reply->time,
                                "content" => $reply->reply_body,
                                "pic_Arr" => unserialize($reply->pic_url),
                                "commenter" => array(
                                    "user_id" => $user->user_id,
                                    "avatarUrl" => $user->icon,
                                    "nickname" => $user->name
                                ),
                                "interaction" => array(
                                    "likes" => $reply->likes,
                                    "responds" => (count($reply_list) - 1)
                                )
                            );
                        }else{
                            $user = User_Model::get($reply->user_id);
                            $replies[$count - 1] = array(
                                "time" => $reply->time,
                                "content" => $reply->reply_body,
                                "pic_Arr" => unserialize($reply->pic_url),
                                "commenter" => array(
                                    "user_id" => $user->user_id,
                                    "avatarUrl" => $user->icon,
                                    "nickname" => $user->name
                                )
                            );
                        }
                        $count ++;
                    }
                    $data[$x]['replies'] = $replies;
                }
                json(0, "回复成功", $data);
            } catch (DbException $e) {
                return $this->error_inner();
            }
        }else{
            return $this->error_inner();
        }

    }

    // 点赞接口函数
    public function agree(Request $request)
    {
        // 获取前端传入json数据
        $input_Arr = json_decode($request->getInput(), true);

        // 检测空键值
        $miss = array();
        foreach($input_Arr as $key=>$value)
        {
            if($value == "")
                $miss[count($miss)] = $key;
        }
        if(count($miss) > 0)
            return $this->miss_params($miss);

        // 检测user_id
        try {
            $user = User_Model::get($input_Arr['user_id']);
            if(!$user)
                return $this->error_user_id();
        } catch (DbException $e) {
            return $this->error_inner();
        }

        // 储存点赞数据
        if($input_Arr['layer'] == 1){
            try {
                $post = Post_Model::get($input_Arr['post_id']);
                if(!$post){
                    return $this->error_inner();
                }else{
                    $post->likes = $post->likes + 1;
                    if($post->save() == false) return $this->error_inner();
                    $post_like = Post_like::get(['post_id' => $input_Arr['post_id'], 'user_id' => $input_Arr['user_id']]);
                    $post_like->is_like = 1;
                    if($post_like->save() == false) return $this->error_inner();
                }
            } catch (DbException $e) {
                return $this->error_inner();
            }
        }else{
            try {
                $reply_like = Reply_like::get(['post_id' => $input_Arr['post_id'], 'user_id' => $input_Arr['user_id'], 'id' => $input_Arr['layer']]);
                $reply = Reply_Model::get(['post_id' => $input_Arr['post_id'], 'user_id' => $input_Arr['user_id'], 'layer' => $input_Arr['layer'], 'id' => 1]);
                $reply->likes = $reply->likes + 1;
                if($reply->save() == false) return $this->error_inner();
                $reply_like->is_like = 1;
                if($reply_like->save() == false) return $this->error_inner();

            } catch (DbException $e) {
                return $this->error_inner();
            }
        }
        json(0, "点赞成功");
    }
}