<?php
/**
 *	HivenKay
 * 此处需要引入数据库的配置文件
 * DB_HOSTNAME,DB_DATABASE,DB_USERNAME,DB_PASSWORD需要实现配置好
 */
class LfDatabase {
	private static $pdo;
	private static function Init() {
		self::$pdo = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8', DB_HOSTNAME, DB_DATABASE), DB_USERNAME, DB_PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
		self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	public static function query($sql, $data = array()) {
		self::Init();
		if (empty($data)) {
			$obj = self::$pdo->query($sql);
		} else {
			$obj = self::$pdo->prepare($sql);
			$obj->execute($data);
		}
		return $obj->fetchAll(\PDO::FETCH_ASSOC);
	}
	public static function insert($table, $data = array()) {
		self::Init();
		if (empty($data)) {
			throw new Exception("\$data can’t be empty");
		}
		$i = 0;
		$KeyStr = '';
		$ValueStr = '';
		$Values = array();
		foreach ($data as $k => $v) {
			if ($i != 0) {
				$link = ',';
			} else {
				$link = '';
			}
			$KeyStr .= $link . '`' . $k . '`';
			$ValueStr .= $link . '?';
			$Values[] = $v;
			$i++;
		}
		$sql = sprintf('insert into `%s`(%s) values(%s)', $table, $KeyStr, $ValueStr);
		$obj = self::$pdo->prepare($sql);
		$obj->execute($Values);
	}
	public static function update($table, $where = array(), $data = array()) {
		self::Init();
		if (empty($where)) {
			throw new Exception("\$where is required");
		}
		if (empty($data)) {
			throw new Exception("\$data can’t be empty");
		}
		$set = self::CombineParam($data, ', ');
		$param = self::CombineParam($where, 'and ');
		$Values = array_merge($set['Values'], $param['Values']);
		$sql = sprintf('update `%s` set %s where %s', $table, $set['Str'], $param['Str']);
		$obj = self::$pdo->prepare($sql);
		$obj->execute($Values);
	}
	/*
	* $order为数组，第一个子元素为排序字段，第二个为排序规则
	* $limit第一个元素为偏移量，第二个元素为查询条数
	* 使用：LfDatabase::get('user',array('user_id'=>1),array('user_id','desc'),array(5,10))
	*/
	public static function get($table, $where = array(), $order=array(),$limit = array()) {
		self::Init();
		if (!empty($limit) && count($limit) != 2) {
			Throw new Exception('Param limit is Wrong');
		}
		if (!empty($limit) && count($limit) == 2) {
			$limitstr = sprintf('limit %d,%d', $limit[0], $limit[1]);
		} else {
			$limitstr = '';
		}
		if(!empty($order)&&count($order) == 2) {
			$orderStr = sprintf('order by %s %s',$order[0],$order[1])
		} else {
			$orderStr = '';
		}
		if (empty($where)) {
			$sql = sprintf("select * from %s %s %s", $table, $limitstr,$orderStr);
			return self::query($sql);
		} else {
			$param = self::CombineParam($where, 'and ');
			$sql = sprintf("select * from %s where %s  %s  %s", $table, $param['Str'], $limitstr,$orderStr);
			return self::query($sql, $param['Values']);
		}
	}

	public static function delete($table, $where = array()) {
		if (empty($where)) {
			throw new Exception("\$where can’t be empty");
			return;
		}
		$param = self::CombineParam($where, 'and ');
		$sql = sprintf('delete from %s where %s', $table, $param['Str']);
		self::Init();
		$obj = self::$pdo->prepare($sql);
		$obj->execute($param['Values']);
	}
	//组合参数
	private static function CombineParam($data = array(), $l = '') {
		$Str = '';
		$i = 0;
		foreach ($data as $k => $v) {
			if ($i != 0) {
				$link = $l;
			} else {
				$link = '';
			}
			$Str .= $link . '`' . $k . '`' . '=?  ';
			$Values[] = $v;
			$i++;
		}
		return array(
			'Str' => $Str,
			'Values' => $Values,
		);
	}
}