<?php

namespace app\hout\model;
use think\Db;


class Log extends Base
{
	/**
     * 获取分页
     * @param string $where
     * @param string $feild
     * @param int $page
     * @param array $order
     * @return mixed
     */
    public function getList($where = "", $feild = "", $page = 30, $order = ['id' => 'desc'])
    {
        $res = $this->where($where)->field($feild)->order($order)->paginate($page, false, ['query' => request()->param()]);
        if ($res) {
            $data = $res->toArray();
            $data['page'] = $res->render();
        } else {
            $data = null;
        }
        return $data;
    }

	/**
	 * 记录日志信息
	 * @param string $info 日志内容
	 * @return bool 记录结果，成功-true，失败false
	 */
	public function writeLog($info = '') {
		$admin = session('admin_login');
		$userid = $admin['id'];

		//如果用户没有登录，则直接返回false
		if (!isset($userid) || empty($userid) || trim($userid) == '' || $userid <= 0)
			return false;

		//获取信息
		$request = \think\Request::instance();
		$ip = get_client_ip(); //获得用户的ip地址
		$area = GetIpAddress($ip);//获得用户具体地址
		$agent = $request->header('user-agent'); //获得请求头信息
		$device = get_client_platform($agent) . ' ' . get_client_browser($agent);

		if (strpos($device, 'Unknown') !== false)
			$device = get_client_platform($agent);
		$log = array(
			'username' => $admin['name'],
			'content' => $info,
			'time' => time(),
			'ip' => $ip,
			'area' => $area,
			'device' => $device
		);
		$data = array(
			'id' => $admin['id'],
			'login_time' => date("Y-m-d H:i",time()),
			'login_ip' => $ip
		);

		Db::name('Admin')->update($data);//更新登录时间和ip

		return $this->insert($log); //添加日志
	}
}
