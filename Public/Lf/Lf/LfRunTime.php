<?php
/**
 * Created by PhpStorm.
 * User: skyekeven
 * Date: 2015/5/8 0008
 * Time: 13:25
 */

class LfRunTime {
	private $starTime; //开始时间
	private $stopTime; //结束时间
	private function getMicTime() {
		$mictime = microtime(); //获取时间戳和微秒数
		list($usec, $sec) = explode(" ", $mictime); //把微秒数分割成数组并转换成变量处理
		return (float) $usec + (float) $sec; //把转换后的数据强制用浮点点来处理
	}
	public function star() {
//获取开始时间
		$this->starTime = $this->getMicTime();
	}
	public function stop() {
//获取结束时间
		$this->stopTime = $this->getMicTime();
	}
	public function spent() { //计算程序持续时间
		return round($this->stopTime - $this->starTime) * 1000; //获取毫秒数
	}
}