<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 获取客户端地址
 * @param $ip ip地址
 * @return string 地址
 */
function GetIpAddress($ip = '')
    {
        if (!$ip) {
            $ips = getips();
            $ip = $ips["ip"];
        }
        $area = "";
        $url = "http://api.map.baidu.com/location/ip?ak=XhdDfRCFCrDRee5XCaGObwSmvqwsruEo&ip={$ip}";
        $ch = curl_init();
        $timeout = 2;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $str = curl_exec($ch);
        curl_close($ch);
        if($str){
          $array = json_decode($str, true);
          if($array['status']==0){
            $json=$array['content']['address_detail'];
            if (isset($json['city']) && $json['city']) {
              return $json['city'];
            }
            if (isset($json['province']) && $json['province']) {
              return $json['province'];
            }
          }
        }
        return $area;
    }

/**
* 服务器通过get请求获得内容
* @param  string $URL 请求URL
* @return string          [description]
*/
function http_gets($url){
    $ch = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    $sContent = curl_exec($ch);
    $aStatus = curl_getinfo($ch);
    curl_close($ch);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}

/**
 * 获取客户端用户的浏览器信息
 * @param string $agent http请求头信息，默认为空，为空则自动获取
 * @return string 客户端用户的浏览器信息
 */
function get_client_browser($agent = null) {
    if (empty($agent) || $agent == null) {
        $request = \think\Request::instance();
        $agent = $request->header('user-agent'); //获得请求头信息
    }

    $browseragent="";   //浏览器
    $browserversion=""; //浏览器的版本
    if (preg_match('/MSIE ([0-9].[0-9]{1,2})/i',$agent,$version)) {
        $browserversion=$version[1];
        $browseragent="Internet Explorer";
    } else if(preg_match('/UBrowser\/([0-9].[0-9]{1,2})/i',$agent,$version)) {
        $browserversion=$version[1];
        $browseragent="UC浏览器";
    } else if (preg_match('/Opera\/([0-9]{1,2}.[0-9]{1,2})/i',$agent,$version)) {
        $browserversion=$version[1];
        $browseragent="Opera";
    } else if (preg_match( '/Firefox\/([0-9.]{1,5})/i',$agent,$version)) {
        $browserversion=$version[1];
        $browseragent="Firefox";
    } else if (preg_match( '/Chrome\/([0-9.]{1,3})/i',$agent,$version)) {
        $browserversion=$version[1];
        $browseragent="Chrome";
    } else if (preg_match( '/Safari\/([0-9.]{1,3})/i',$agent,$version)) {
        $browseragent="Safari";
        $browserversion="";
    } else {
        $browserversion="";
        $browseragent="Unknown";
    }
    return $browseragent."/".$browserversion;
}

/**
 * 获取客户端用户的操作平台信息
 * @param string $agent http请求头信息，默认为空，为空则自动获取
 * @return string 客户端用户的操作平台信息
 */
function get_client_platform($agent = null) {
    if (empty($agent) || $agent == null) {
        $request = \think\Request::instance();
        $agent = $request->header('user-agent'); //获得请求头信息
    }

    $browserplatform = '';
    if (preg_match('/win/i',$agent) && strpos($agent, '95')) {
        $browserplatform="Windows 95";
    } elseif (preg_match('/win 9x/i',$agent) && strpos($agent, '4.90')) {
        $browserplatform="Windows ME";
    } elseif (preg_match('/win/i',$agent) && preg_match('/98/',$agent)) {
        $browserplatform="Windows 98";
    } elseif (preg_match('/win/i',$agent) && preg_match('/nt 5.0/i',$agent)) {
        $browserplatform="Windows 2000";
    } elseif (preg_match('/win/i',$agent) && preg_match('/nt 5.1/i',$agent)) {
        $browserplatform="Windows XP";
    } elseif (preg_match('/win/i',$agent) && preg_match('/nt 6.0/i',$agent)) {
        $browserplatform="Windows Vista";
    } elseif (preg_match('/win/i',$agent) && preg_match('/nt 6.1/i',$agent)) {
        $browserplatform="Windows 7";
    } elseif (preg_match('/win/i',$agent) && preg_match('/32/',$agent)) {
        $browserplatform="Windows 32";
    } elseif (preg_match('/win/i',$agent) && preg_match('/nt 10.0/i',$agent)) {
        $browserplatform="Windows 10.0";
    } elseif (preg_match('/win/i',$agent) && preg_match('/nt/i',$agent)) {
        $browserplatform="Windows NT";
    } elseif (preg_match('/iPhone/i',$agent)) {
        $browserplatform="iPhone";
    } elseif (preg_match('/iPad/i',$agent)) {
        $browserplatform="iPad";
    } elseif (preg_match('/Mac OS/i',$agent)) {
        $browserplatform="Mac OS";
    } elseif (preg_match('/android/i',$agent)) {
        $browserplatform="Android";
    } elseif (preg_match('/linux/i',$agent)) {
        $browserplatform="Linux";
    } elseif (preg_match('/unix/i',$agent)) {
        $browserplatform="Unix";
    } elseif (preg_match('/sun/i',$agent) && preg_match('/os/i',$agent)) {
        $browserplatform="SunOS";
    } elseif (preg_match('/ibm/i',$agent) && preg_match('/os/i',$agent)) {
        $browserplatform="IBM OS/2";
    } elseif (preg_match('/Mac/i',$agent) && preg_match('/PC/i',$agent)) {
        $browserplatform="Macintosh";
    } elseif (preg_match('/PowerPC/i',$agent)) {
        $browserplatform="PowerPC";
    } elseif (preg_match('/AIX/i',$agent)) {
        $browserplatform="AIX";
    } elseif (preg_match('/HPUX/i',$agent)) {
        $browserplatform="HPUX";
    } elseif (preg_match('/NetBSD/i',$agent)) {
        $browserplatform="NetBSD";
    } elseif (preg_match('/BSD/i',$agent)) {
        $browserplatform="BSD";
    } elseif (preg_match('/OSF1/i',$agent)) {
        $browserplatform="OSF1";
    } elseif (preg_match('/IRIX/i',$agent)) {
        $browserplatform="IRIX";
    } elseif (preg_match('/FreeBSD/i',$agent)) {
        $browserplatform="FreeBSD";
    }

    if ($browserplatform=='') {
        $browserplatform = "Unknown";
    }
    return $browserplatform;
}

/**
 * 记录系统日志信息
 * @param string $info 日志内容
 * @return bool 记录结果，成功-true，失败-false
 */
function record_log($log = '') {
    $logModel = new \app\hout\model\Log;
    return $logModel->writeLog($log);
}

/**
 * 函数encrypt($string, $operation, $key)中
 * $string：需要加密解密的字符串；
 * $operation：判断是加密还是解密，
 * E表示加密，D表示解密；
 * $key：密匙。
 * @param  [type] $string    [description]
 * @param  [type] $operation [description]
 * @param  string $key       [description]
 * @return [type]            [description]
 */
function encrypt($string, $operation, $key='') {
    $key=md5($key);
    $key_length=strlen($key);
    $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
    $string_length=strlen($string);
    $rndkey=$box=array();
    $result='';
    for($i=0;$i<=255;$i++){
        $rndkey[$i]=ord($key[$i%$key_length]);
        $box[$i]=$i;
    }
    for($j=$i=0;$i<256;$i++){
        $j=($j+$box[$i]+$rndkey[$i])%256;
        $tmp=$box[$i];
        $box[$i]=$box[$j];
        $box[$j]=$tmp;
    }
    for($a=$j=$i=0;$i<$string_length;$i++){
        $a=($a+1)%256;
        $j=($j+$box[$a])%256;
        $tmp=$box[$a];
        $box[$a]=$box[$j];
        $box[$j]=$tmp;
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
    }
    if($operation=='D'){
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
            return substr($result,8);
        }else{
            return'';
        }
    }else{
        return str_replace('=','',base64_encode($result));
    }
}