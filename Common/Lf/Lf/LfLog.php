<?php
/**
* 生成日志文件
* 需要先定义日志文件路径；
*/
class Log
{
//	public $LogPath
	static function log($obj,$LogPath)
	{
		$logName='userLog';
	    $s = json_encode(array('_time' => date('Y-m-d H:i:s'), 'data' => $obj), JSON_UNESCAPED_UNICODE) . "\n";
	    @mkdir($LogPath);
	    file_put_contents($LogPath . '/' . $logName . '.log', $s, FILE_APPEND);
	}
}