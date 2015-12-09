<?php
class LfDealError {
//在chrome下调试错误
	static function console_log($data) {
		if (is_array($data) || is_object($data)) {
			echo ("<script>console.log('" . json_encode($data) . "');</script>");
		} else {
			echo ("<script>console.log('" . $data . "');</script>");
		}
	}
}