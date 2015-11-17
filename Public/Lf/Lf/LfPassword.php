<?php
/**
 * Created by PhpStorm.
 * User: skyekeven
 * Date: 2015/5/10 0010
 * Time: 16:32
 */

class LfPassword {
	static function CreateSalt() {
		return substr(md5(uniqid(rand(), true)), 0, 9);
	}
	static function PasswordHash($salt, $password) {
		return sha1($salt . sha1($salt . sha1($password)));
	}
	static function PasswordVerify($salt, $password, $hash) {
		return $hash == self::PasswordHash($salt, $password);
	}
}