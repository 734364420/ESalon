<?php
class LfConfig {
	static $DbUserName;
	static $DbPassword;
	static $DbDatabase;
	static $DbHost = 'localhost';
	static function GetCiConfig() {
		return array(
			'default' => array(
				'hostname' => self::$DbHost,
				'username' => self::$DbUserName,
				'password' => self::$DbPassword,
				'database' => self::$DbDatabase,
				'dbdriver' => 'mysql',
				'dbprefix' => '',
				'pconnect' => TRUE,
				'db_debug' => TRUE,
				'cache_on' => FALSE,
				'cachedir' => '',
				'char_set' => 'utf8',
				'dbcollat' => 'utf8_general_ci',
				'swap_pre' => '',
				'autoinit' => TRUE,
				'stricton' => FALSE,
			),
		);
	}
}