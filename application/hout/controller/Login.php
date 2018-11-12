<?php

namespace app\hout\controller;

use app\hout\model\AdminLog;
use think\Controller;
use app\hout\model\Admin;
use app\hout\extend\SysCrypt;
use think\Db;
use think\Loader;

class Login extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Loader::import("HttpUtil",EXTEND_PATH);
    }

    /**
     * 显示
     * @return \think\response\View
     */
    public function index()
    {
        if(session("admin_login")){
            $this->redirect(url('index/index'));
        }
        $find=Db::name('loginmode')->where('id = 1')->find();
        if(empty($find)){
            $find = array(
                'id' => 1,
                'loginmode' => 1,
            );
        }
        $this->assign('find',$find);
        return view();
    }

    /**
     * 登录
     */
    public function login()
    {
        $admins = new Admin();
        $admin = $admins->getOne(['username'=>input("post.username")], ['id', 'name', 'password', 'username', 'group']);
        if($admin){
            $password = SysCrypt::php_decrypt($admin['password'],config('secret'));
//            echo $password;die;
            if(input("post.password") == $password){
                unset($admin['password']);
                session("admin_login", $admin);
                record_log('用户【'.$admin['name'].'】('.$admin['username'].')登录系统！');
                $this->redirect(url('index/index'));
            }else{
                $this->redirect(url('hout/login/index'), [], 302, ['msg'=>"密码错误！"]);
            }
        }else{
            $this->redirect(url('hout/login/index'), [], 302, ['msg'=>"用户不存在！"]);
        }
    }

    // 微信登录 回调
    public function wxlogin_callback() {
        $state = input('state');
        $state = str_replace('.html','',$state);
        $http = new \HttpUtil();
        // 获取access_token
        $param = [
            'appid'      => 'wx22d7a0063685b948',
            'secret'     => '4cfabaf6330a8a6f58becfaa2eb8d6db',
            'code'       => input('code'),
            'grant_type' => 'authorization_code'
        ];
        $path = 'https://api.weixin.qq.com/sns/oauth2/access_token';
        $rs = $http->httpRequest($path,'get',$param);
        $obj = json_decode($rs);

        if(isset($obj->errcode)) {
            $this->error(''.json_encode($obj), 'hout/login/index');
        }
        $arr = explode('_', base64_decode($state));


        // 解除绑定操作
        if($arr[0] == 'unbind' && $arr[1] >=1) {
            $user = Admin::where('wx_unionid', $obj->unionid)->find();
            $user->wx_unionid = '';
            $user->wx_headimgurl = '';
            $user->wx_nickname = '';
            $user->wx_sex = '';
            $user->save();
            session(null);
            $this->success('解除绑定成功', 'hout/admin/index');
        }
        // 获取用户信息
        $param2 = [
            'access_token' => $obj->access_token,
            'openid' => $obj->unionid
        ];
        $path2 = 'https://api.weixin.qq.com/sns/userinfo';
        $rs2 = $http->httpRequest($path2,'get',$param2);
        $obj2 = json_decode($rs2);


        if($arr[0] == 'bind' && $arr[1] >=1) {
            // 绑定操作
            $user = Admin::get($arr[1]);
            $user->wx_unionid    = $obj2->unionid;
            $user->wx_headimgurl = $obj2->headimgurl;
            $user->wx_nickname   = $obj2->nickname;
            $user->wx_sex        = $obj2->sex;
            $user->wx_bindtime   = time();
            $r = $user->save();
            if($r) {
                record_log('用户【'.$user->name.'】('.$user->username.')通过<微信扫码>绑定微信成功，并登录系统！');
                $this->success('绑定成功', 'hout/admin/index');
            } else {
                $this->success('绑定失败', 'hout/admin/index');
            }
        } else {
            // 登录逻辑
            $user = Admin::where('wx_unionid', $obj2->unionid)->find();
            if(!empty($user)) {
                $admin=array(
                        'id'=>$user->id,
                        'name'=>$user->name,
                        'username'=>$user->username,
                        'group'=>$user->group
                    );
                $time = time();
                $user->login_time = $time;
                $user->update_time = $time;
                $user->login_ip = htmlspecialchars(trim(get_client_ip()));
                $user->save();
                session("admin_login", $admin);
                record_log('用户【'.$user->name.'】('.$user->username.')通过<微信扫码>登录系统！');
                $this->success('登录成功', 'index/index');
            } else {
                $this->error('登录失败，您还没有绑定账号哦！', 'hout/login/index');
            }
        }
    }

    public function logout()
    {
        session(null);
        $this->redirect(url('hout/login/index'));
    }


}
