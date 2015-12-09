<?php
class LfDateAndWeek {
	static function GetWeek() {
		$data = date('w');
		switch ($data) {
			case 1:
				$week = '星期一';
				break;
			case 2:
				$week = '星期二';
				break;
			case 3:
				$week = '星期三';
				break;
			case 4:
				$week = '星期四';
				break;
			case 5:
				$week = '星期五';
				break;
			case 6:
				$week = '星期六';
				break;
			case 0:
				$week = '星期日';
				break;
		}
		return $week;
	}
//传入时间戳，返回时间差
	static function timediff($begin_time, $end_time) {
		if ($begin_time < $end_time) {
			//判断传入的时间哪一个大
			$starttime = $begin_time;
			$endtime = $end_time;
		} else {
			$starttime = $end_time;
			$endtime = $begin_time;
		}
		$timediff = $endtime - $starttime; //获取整个时间差
		$days = intval($timediff / 86400); //除以86400获得相差的天数
		$remain = $timediff % 86400; //获得除以86400余数
		$hours = intval($remain / 3600); //获取小时差
		$remain = $remain % 3600;
		$mins = intval($remain / 60); //获取分钟数差
		$secs = $remain % 60;
		$res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
		return $res;
	}
}