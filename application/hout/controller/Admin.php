<?php

namespace app\hout\controller;

use app\hout\extend\SysCrypt;
use app\hout\model\AdminGroup;
use app\hout\model\Admin as User;


class Admin extends Base
{
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new \app\hout\model\Admin();
    }

    public function index()
    {
        $title="管理员管理";
        $where = [];
        input('name') && $where['name'] = ['like', '%' . trim(input('name')) . '%'];
        $data = $this->model->_getList($where);
        foreach($data['data'] as $k=>$v){
            $data['data'][$k]['state'] = base64_encode('bind_'.$v['id']);
            $data['data'][$k]['unbind'] = urlencode(encrypt('unbind_'.$v['id'], 'E', 'xspxzying'));
        }
        return view('index', compact('data', 'title'));
    }

    public function edit($id = "")
    {
        $title = $id ? '修改管理员' : '添加管理员';
        $data = $this->model->getOne(['id' => $id]);
        $adminGroup = new AdminGroup();
        $group = $adminGroup->getAll(['status'=>1]);
        return view('edit', compact('title', 'data', 'group'));
    }

    public function save($id = '')
    {
        $data = input("post.");
        if ($data['password']) {
            $data['password'] = SysCrypt::php_encrypt($data['password'], config('secret'));
        } else {
            unset($data['password']);
        }
        $where = [];
        $id && $where['id'] = $id;
        $res = $this->model->addOrUp($where, $data);
        return redirect('index');
    }

    public function del($id)
    {
        if ($id != 1) {
            $this->model->del(['id' => $id]);
        }
        return redirect('index');
    }

    public function personal()
    {
        $id = session('admin_login.id');
        if(request()->isPost()){
            $data = input("post.");
            if ($data['password']) {
                $data['password'] = SysCrypt::php_encrypt($data['password'], config('secret'));
            } else {
                unset($data['password']);
            }
            $where = [];
            $where['id'] = $id;
            $res = $this->model->addOrUp($where, $data);
            return redirect('hout/Login/logout');
        }else{
            $title = '个人信息';
            $data = $this->model->_getOne(['id' => $id]);
            return view('personal', compact('title', 'data'));
        }
    }

    //绑定微信
    public function bindwx(){
        $state = empty(input('state')) ? 'login':input('state');
        $this->assign('state',$state);
        return view();
    }

    //解绑微信
    public function unbindwx(){
        $id = input('id');
        if($id) {
            $user = User::find($id);
            $user->wx_unionid = '';
            $user->wx_headimgurl = '';
            $user->wx_nickname = '';
            $user->wx_sex = '';
            $user->save();
            $this->success('解除绑定成功', 'hout/admin/index');
        }
    }
}
