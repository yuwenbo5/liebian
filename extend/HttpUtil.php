<?php

class HttpUtil {
    /**
     * 下载远程文件
     * @param  string  $url     网址
     * @param  string  $filename    保存文件名
     * @param  integer $timeout 过期时间
     * return boolean|string
     */
    function http_down($url, $dir, $timeout = 60) {
        preg_match('/mmbiz_[\w]+/i', $url,$a);
        $ext = explode('_', $a[0])[1];
        $filename = $dir.md5(time().mt_rand()).'.'.$ext;

    	ini_set('php_curl','on');
        $path = dirname($filename);
        if (!is_dir($path) && !mkdir($path, 0755, true)) {
            return false;
        }
        $fp = fopen($filename, 'w');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $filename;
    }

    function httpRequest($url, $method, $params = array())
    {
        if (trim($url) == '' || !in_array($method, array('get', 'post')) || !is_array($params)) {
            return false;
        }
        $curl = curl_init();

        switch ($method) {
            case 'get':
                $str = '?';
                foreach ($params as $k => $v) {
                    $str .= $k . '=' . $v . '&';
                }
                $str = substr($str, 0, -1);
                $url .= $str;//$url=$url.$str;
                curl_setopt($curl, CURLOPT_URL, $url);
                break;
            case 'post':
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            default:
                $result = '';
                break;
        }
        curl_setopt($curl, CURLOPT_HEADER, 0);// 设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  // 跳过SSL证书检查
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); // 防止417错误 - 执行失败

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}
