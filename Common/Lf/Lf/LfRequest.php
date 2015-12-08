<?php

class LfRequest{
	/*
	*传入请求的参数名$str,错误时抛出的信息
	*判断请求的参数是否设置，为空
	*此处只做请求判断，并抛出异常，不显示页面
	*/
	static	function JudgeRequst($str){
		if (!isset($_REQUEST[$str])) {
			die($str.' hasn`t be setted');
		}
		if (empty($_REQUEST[$str])) {
			die($str.' can`t be empty');
		} else {
			return $_REQUEST[$str];
		}
	}
	//读入一个参数,并且类型是字符串,不符合要求的返回''
	static function inStr($key){
		if (!isset($_REQUEST[$key])){
			return '';
		}
		$val = $_REQUEST[$key];
        //可能是数组
		if (!is_string($val)){
			return '';
		}
		return $val;
	}

    //读入一个参数,并且类型是数值,不符合要求的返回0
	static function inNum($key){
		if (empty($_REQUEST[$key])){
			return 0;
		}
		$val = $_REQUEST[$key];
        //可能是数组
		if (!is_string($val)){
			return 0;
		}
		return intval($val);
	}

    //读入一个参数,并且类型是数组,不符合要求返回array()
    //注意此处不能保证返回的array的内部的数据结构.
	static function inArray($key){
		if (empty($_REQUEST[$key])){
			return array();
		}
		$val = $_REQUEST[$key];
		if (!is_array($val)){
			return array();
		}
		return $val;
	}
	/**
	 * 是否是AJAx提交的
	 * @return bool
	 */
	static function isAjax() {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 是否是GET提交的
	 */
	static function isGet() {
		return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
	}

	/**
	 * 是否是POST提交
	 * @return int
	 */
	static function isPost() {
		return ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? 1 : 0;
	}
	 /*
	 *
	 * 根据php的$_SERVER['HTTP_USER_AGENT'] 中各种浏览器访问时所包含各个浏览器特定的字符串来判断是属于PC还是移动端
	 * @author           discuz3x
	 * @lastmodify    2014-04-09
	 * @return  BOOL
	 */
	static function isMobile() {
		global $_G;
		$mobile = array();
		//各个触控浏览器中$_SERVER['HTTP_USER_AGENT']所包含的字符串数组
		static $touchbrowser_list =array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
			'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
			'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
			'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
			'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
			'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
			'benq', 'haier', '^lct', '320x320', '240x320', '176x220');
		//window手机浏览器数组【猜的】
		static $mobilebrowser_list =array('windows phone');
		//wap浏览器中$_SERVER['HTTP_USER_AGENT']所包含的字符串数组
		static $wmlbrowser_list = array('cect', 'compal', 'ctl', 'lg', 'nec', 'tcl', 'alcatel', 'ericsson', 'bird', 'daxian', 'dbtel', 'eastcom',
			'pantech', 'dopod', 'philips', 'haier', 'konka', 'kejian', 'lenovo', 'benq', 'mot', 'soutec', 'nokia', 'sagem', 'sgh',
			'sed', 'capitel', 'panasonic', 'sonyericsson', 'sharp', 'amoi', 'panda', 'zte');
		$pad_list = array('pad', 'gt-p1000');
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(self::dstrpos($useragent, $pad_list)) {
			return false;
		}
		if(($v = self::dstrpos($useragent, $mobilebrowser_list, true))){
			$_G['mobile'] = $v;
			return '1';
		}
		if(($v = self::dstrpos($useragent, $touchbrowser_list, true))){
			$_G['mobile'] = $v;
			return '2';
		}
		if(($v = self::dstrpos($useragent, $wmlbrowser_list))) {
			$_G['mobile'] = $v;
			return '3'; //wml版
		}
		$brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
		if(self::dstrpos($useragent, $brower)) return false;
		$_G['mobile'] = 'unknown';
		//对于未知类型的浏览器，通过$_GET['mobile']参数来决定是否是手机浏览器
		if(isset($_G['mobiletpl'][$_GET['mobile']])) {
			return true;
		} else {
			return false;
		}
	}
	 /*
	 * 判断$arr中元素字符串是否有出现在$string中
	 * @param  $string     $_SERVER['HTTP_USER_AGENT']
	 * @param  $arr          各中浏览器$_SERVER['HTTP_USER_AGENT']中必定会包含的字符串
	 * @param  $returnvalue 返回浏览器名称还是返回布尔值，true为返回浏览器名称，false为返回布尔值【默认】
	 * @author           discuz3x
	 * @lastmodify    2014-04-09
	 */
	static function dstrpos($string, $arr, $returnvalue = false) {
		if(empty($string)) return false;
		foreach((array)$arr as $v) {
			if(strpos($string, $v) !== false) {
				$return = $returnvalue ? $v : true;
				return $return;
			}
		}
		return false;
	}
	    //https请求（支持GET和POST）
    static function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    static function isWeixinBrowser() {
		$agent = $_SERVER ['HTTP_USER_AGENT'];
		if (! strpos ( $agent, "icroMessenger" )) {
			return false;
		}
		return true;
	}


}