<?php
class LfRequest {
	/*
	 *传入请求的参数名$str,错误时抛出的信息
	 *判断请求的参数是否设置，为空
	 *此处只做请求判断，并抛出异常，不显示页面
	 */
	static function JudgeRequst($str) {
		if (!isset($_REQUEST[$str])) {
			die($str . ' hasn`t be setted');
		}
		if (empty($_REQUEST[$str])) {
			die($str . ' can`t be empty');
		} else {
			return $_REQUEST[$str];
		}
	}
	//读入一个参数,并且类型是字符串,不符合要求的返回''
	static function inStr($key) {
		if (!isset($_REQUEST[$key])) {
			return '';
		}
		$val = $_REQUEST[$key];
		//可能是数组
		if (!is_string($val)) {
			return '';
		}
		return $val;
	}

	//读入一个参数,并且类型是数值,不符合要求的返回0
	static function inNum($key) {
		if (empty($_REQUEST[$key])) {
			return 0;
		}
		$val = $_REQUEST[$key];
		//可能是数组
		if (!is_string($val)) {
			return 0;
		}
		return intval($val);
	}
	//读入一个参数,并且类型是数组,不符合要求返回array()
	//注意此处不能保证返回的array的内部的数据结构.
	static function inArray($key) {
		if (empty($_REQUEST[$key])) {
			return array();
		}
		$val = $_REQUEST[$key];
		if (!is_array($val)) {
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

}