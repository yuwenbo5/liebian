<?php

namespace app\hout\controller;

use think\Db;

class Loginmode extends Base
{
    /**
     * 日志列表
     * @return \think\response\View
     */
    public function index()
    {

        if (request()->isPost()) {
            $data = input('post.'); //获得post请求参数

            $arr=Db::name('loginmode')->select();
            if(empty($arr)){
              $res = Db::name('loginmode')->insert($data);
            }else{
              $data['id']=1;
              $res = Db::name('loginmode')->update($data);
            }
        }

        $title = "登入模式管理";
        $data = Db::name('loginmode')->where('id = 1')->find();

        return view('index', compact('data', 'title'));
    }
}
