<?php
class LfSession
{
	public static function set($name,$value) {
		$_SESSION[$name]=$value;
	}
	public static function get($name) {
		return $_SESSION[$name];
	}
}