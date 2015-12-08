<?php
/**
 *
 */
class LfDealArray {
	/*
	 *初始化二维数组
	 *传入二维数组的键名
	 *返回初始化后的二维数组
	 */
	static function InitArray($arr) {
		$data = array();
		foreach ($arr as $v) {
			$data[0][$v] = '';
		}
		return $data;
	}
	/*
	 *将对象转化为数组，取自Ci框架
	 */
	static function object_to_array($object) {
		if (!is_object($object)) {
			return $object;
		}

		$array = array();
		foreach (get_object_vars($object) as $key => $val) {
			if (!is_object($val) && !is_array($val) && $key != '_parent_name') {
				$array[$key] = $val;
			}
		}

		return $array;
	}
	/*
	 *数组按照某个字段排序
	 *传入数组，字段名，以及排序规则，默认为ASC
	 *返回排序后的数组
	 */
	static function ArraySort($array, $on, $order = SORT_ASC) {
		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}

	/*
	 * 给数组中每个值添加一个字符串
	 * 传入数组和要添加的字符串
	 * 返回添加后的数组
	 */
	static function ArrayAddSomething($array, $something) {
		foreach ($array as &$k) {
			$k = $something . $k;
		}
		return $array;
	}
	/*
	 *二维数组按照某个字段去重
	 *传入数组和字段名
	 *返回去重后的数组
	 */
	static function ArrayUnique($arr, $key) {
		$rAr = array();
		for ($i = 0; $i < count($arr); $i++) {
			if (!isset($rAr[$arr[$i][$key]])) {
				$rAr[$arr[$i][$key]] = $arr[$i];
			}
		}
		$arr = array_values($rAr);
		return $arr;
	}

}