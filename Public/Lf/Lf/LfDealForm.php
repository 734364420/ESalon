<?php
class LfDealForm {

	/*
	 *主要判断表单内容是否全部填写完整
	 * 输入为$_POST
	 * 如果填写完整，返回true，否则返回false
	 */
	static function JudgeForm($form_vars) {
		foreach ($form_vars as $key => $value) {
			if ((!isset($key)) || ($value == '')) {
				return false;
			}
		}
		return true;
	}

	/*
	 * 判断传入的url是否为正确的地址
	 * 传入url，若正确返回true，否则返回false
	 */
	static function JudgeURL($url) {
		if (strstr($url, 'http://') === false) {
			$url = 'http://' . $url;
		}
		if (!(@fopen($url, 'r'))) {
			return false;
		}
		return true;
	}

}