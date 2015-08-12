<?php
class LfSecurity {
	static function PwdCrypt($pwd, $salt) {
		return crypt($pwd, $salt);
	}
	//密码强度测试,长度必须大于等于6,必须有字母和数字两种不同字符
	//爆客户端错误
	static function PasswordStrongCheck($plain) {
		if (strlen($plain) < 6) {
			die('为了您的安全,密码长度至少6位');
			return;
		}
		if (!(preg_match('/[a-zA-Z]/', $plain) && preg_match('/[0-9]/', $plain))) {
			die('为了您的安全,密码中必须包含字母和数字');
			return;
		}
	}

	//POST请求完全不被跨域的网站,的csrf防御方法,返回false的时候表示发生了csrf
	//会修改数据的方法请全部使用POST请求.
	static function isNoCrossDomainCsrfSafe() {
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			return true;
		}
		if (!empty($_SERVER['HTTPS'])) {
			$http = 'https';
		} else {
			$http = 'http';
		}
		//检查refer
		$hostUrl = $http . '://' . $_SERVER['HTTP_HOST'];
		$refer = '';
		if (!empty($_SERVER['HTTP_REFERER'])) {
			$refer = $_SERVER['HTTP_REFERER'];
		}
		if ($refer === $hostUrl || strpos($refer, $hostUrl . '/') === 0) {
			return true;
		}
		$origin = '';
		if (!empty($_SERVER['HTTP_ORIGIN'])) {
			$origin = $_SERVER['HTTP_ORIGIN'];
		}
		if ($origin === $hostUrl) {
			return true;
		}
		return false;
	}

	// https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet#RULE_.231_-_HTML_Escape_Before_Inserting_Untrusted_Data_into_HTML_Element_Content
	// rule #1 #2
	static function XssHtmlBodyEncode($s) {
		return str_replace(array("\0", '&', '<', '>', '"', "'", '+'),
			array('&#xfffd;', '&amp;', '&lt;', '&gt;', '&#34;', '&#39;', '&#43;'), (string) $s);
	}
	//rule #3
	//正常的json_encode,但是替换 < 为 \u003c
	static function XssJsonEncode($obj) {
		$s = json_encode($obj);
		return str_replace('<', '\u003c', $s);
	}
	//rule #5
	//除了字母和数字,全部进行url转义,按照字节进行转义就可以了.
	static function XssUrlParamValueEncode($s) {
		$s = (string) $s;
		$output = '';
		for ($i = 0; $i < strlen($s); $i++) {
			$ci = ord($s[$i]);
			if (($ci >= ord('0') && $ci <= ord('9')) ||
				($ci >= ord('A') && $ci <= ord('Z')) ||
				($ci >= ord('a') && $ci <= ord('z'))) {
				$output .= chr($ci);
			} else {
				$output .= '%' . dechex($ci);
			}
		}
		return $output;
	}
}