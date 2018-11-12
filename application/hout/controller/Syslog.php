<?php

namespace app\hout\controller;

use app\hout\model\Log;

class Syslog extends Base
{
    /**
     * 日志列表
     * @return \think\response\View
     */
    public function index()
    {
        $title = "日志管理";
        $where = [];
        $order = input('order') ? trim(input('order')) : "id";
        input('username') && $where['username'] = ['like', trim(input('username')) . '%'];
        $log = new Log();
        $data = $log->getList($where, "", 30, [$order => 'desc']);
        return view('index', compact('data', 'title'));
    }
}
