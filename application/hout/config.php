<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    //后台菜单配置
    'menu' => [
        'name' => '系统',
        'icon' => 'fa-server',
        'menus' => [
            [
                'name' => '管理员',
                'url' => 'hout/Admin/index',
                'icon' => 'fa-user',
            ],
            [
                'name' => '管理员组',
                'url' => 'hout/AdminGroup/index',
                'icon' => 'fa-group',
            ],
            [
                'name' => '登入模式',
                'url' => 'hout/Loginmode/index',
                'icon' => 'fa-cog',
            ],
            [
                'name' => '日志管理',
                'url' => 'hout/Syslog/index',
                'icon' => 'fa-file-text-o',
            ],
            [
                'name' => '个人信息',
                'url' => 'hout/admin/personal',
                'icon' => 'fa-user-plus',
            ],
        ],
    ],
    // 应用Trace
    'app_trace'              => true,
    'app_debug'              => true,
    'view_replace_str' => [
        '__STATIC__' => '/static/admin',
    ],
    'secret' => 'csyg',
];
