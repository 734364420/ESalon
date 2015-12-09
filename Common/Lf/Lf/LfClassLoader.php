<?php
/**
 *
 */
class LfClassLoader {
	static function AutoLoad($ClassPrefix, $FilePath) {
		spl_autoload_register(function ($class) use ($ClassPrefix, $FilePath) {
			$len = strlen($ClassPrefix);
			if (strncmp($ClassPrefix, $class, $len) !== 0) {
				return;
			}
			$File = $FilePath . '/Lf/' . $class . '.php';
			if (file_exists($File)) {
				require $File;
			}
		});
	}
}