<?php
class LfCategory {
	/*
	 *传入所有栏目的数组
	 *$level为当前栏目的级数
	 *$html加在栏目名前，显示栏目分级效果
	 *返回组合后的数组
	 */
	static function UnLevelCate($data = array(), $pid = 0, $html = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level = 0) {
		$arr = array();
		foreach ($data as $v) {
			if ($v['Pid'] == $pid) {
				$v['html'] = str_repeat($html, $level);
				$v['CateName'] = str_repeat($html, $level) . '|——' . $v['CateName'];
				$arr[] = $v;
				$arr = array_merge($arr, self::UnLevelCate($data, $v['Id'], $html, $level + 1));
			}
		}
		return $arr;
	}
	//将下级栏目压到child中去
	static function UnLevelCateToChild($data, $filter = 0, $pid = 0) {
		$arr = array();
		foreach ($data as $v) {
			if ($filter == 1) {
				if ($v['CateName'] == '学院首页') {
					continue;
				}
			}
			if ($v['Pid'] == $pid) {
				$v['Child'] = self::UnLevelCateToChild($data, $filter, $v['Id']);
				$arr[] = $v;
			}
		}
		return $arr;
	}
	//传递父级栏目Id获得所有子级栏目
	static function GetChildByPid($data, $pid) {
		$arr = array();
		foreach ($data as $v) {
			if ($v['Pid'] == $pid) {
				$arr[] = $v;
				$arr = array_merge($arr, self::GetChildByPid($data, $v['Id']));
			}
		}
		return $arr;

	}
	static function GetParentByPid($data, $pid) {
		$arr = array();
		foreach ($data as $v) {
			if ($v['CateName'] == '学院首页') {
				continue;
			}
			if ($v['Id'] == $pid) {
				$arr[] = $v;
				$arr = array_merge($arr, self::GetParentByPid($data, $v['Pid']));
			}
		}
		return $arr;

	}
}