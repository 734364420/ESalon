<?php
class LfRegex {
	/*
	传入一个邮箱地址，通过正则判断是否为正确
	若正确返回true，否则返回false
	正则@前面表示至少由一个字母，数字，下划线，连字符，点号或者这些字符的组合为开始的字符串
	@后面表示至少出现一次由字母数字连字符组成的一个字符串
	最后面表示出现2或者3个字母
	 */
	static function JudgeEmail($email) {
		if (preg_match("/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3}$/", $email)) {
			return true;
		}
		return false;
	}

	/*
	 * 传入一个手机电话号码，判断是否为11位，并且是否为13，15，18开头
	 * 若匹配则返回true，否则返回false
	 */
	static function JudgeMobile($str) {
		if (preg_match("/^1(3|4|5|7|8)[0-9]{9}$/", $str)) {
			return true;
		}
		return false;
	}

	/*
	 * 过滤限制级词语
	 * 传入的参数分别是要输入的文字，要过滤的词语,true或者false
	 * 如果为true，则返回的是过滤后的文字，若存在限制级词语则用***替代
	 * 如果为false，则如果存在限制级词语，返回1，不存在返回0
	 */
	static function FilterWords($texto, $filtradas, $reemplazo) {
		$f = explode(',', $filtradas);
		$f = array_map('trim', $f);
		$filtro = implode('|', $f);
		if ($reemplazo) {
			return preg_replace("#$filtro#i", "***", $texto);
		} else {
			return preg_match("#$filtro#i", $texto);
		}
	}

	/*
	 * 提取网页中得所的链接
	 * 输入为url
	 * 输出为数组格式，每个值为一个网页上得链接的url
	 */
	static function getPageLink($url) {
		set_time_limit(0);
		$html = file_get_contents($url);
		preg_match_all("/<a(s*[^>]+s*)href=([\"|']?)([^\"'>\s]+)([\"|']?)/ies", $html, $out);
		$arrLink = $out[3];
		$arrUrl = parse_url($url);
		$dir = '';
		if (isset($arrUrl['path']) && !empty($arrUrl['path'])) {
			$dir = str_replace("\\", "/", $dir = dirname($arrUrl['path']));
			if ($dir == "/") {
				$dir = "";
			}
		}
		if (is_array($arrLink) && count($arrLink) > 0) {
			$arrLink = array_unique($arrLink);
			foreach ($arrLink as $key => $val) {
				$val = strtolower($val);
				if (preg_match('/^#*$/isU', $val)) {
					unset($arrLink[$key]);
				} elseif (preg_match('/^\//isU', $val)) {
					$arrLink[$key] = 'http://' . $arrUrl['host'] . $val;
				} elseif (preg_match('/^javascript/isU', $val)) {
					unset($arrLink[$key]);
				} elseif (preg_match('/^mailto:/isU', $val)) {
					unset($arrLink[$key]);
				} elseif (!preg_match('/^\//isU', $val) && strpos($val, 'http://') === FALSE) {
					$arrLink[$key] = 'http://' . $arrUrl['host'] . $dir . '/' . $val;
				}
			}
		}
		sort($arrLink);
		return $arrLink;
	}

}