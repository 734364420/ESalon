<?php
/**
* 
*/
//此处需要引入数据库的配置文件
class LfDatabase
{
	private static $pdo;
	static function Init()
	{	
		require str_replace('system/','', BASEPATH).'application/config/database.php';
		self::$pdo=new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8','localhost',LfConfig::$DbDatabase),LfConfig::$DbUserName,LfConfig::$DbPassword);
		self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	static function query($sql,$data=array()){
		self::Init();
		if(empty($data)){
			$obj=self::$pdo->query($sql);
		}else{
			$obj=self::$pdo->prepare($sql);
			$obj->execute($data);
		}
		return $obj->fetchAll(\PDO::FETCH_ASSOC);
	}
	static function insert($table,$data=array()){
		self::Init();
		if(empty($data)){
			throw new Exception ("\$data can’t be empty");
		}
		$i=0;
		$KeyStr='';
		$ValueStr='';
		$Values=array();
		foreach ($data as $k => $v) {
			if($i!=0){
				$link=',';
			}else{
				$link='';
			}
			$KeyStr.=$link.'`'.$k.'`';
			$ValueStr.=$link.'?';
			$Values[]=$v;
			$i++;
		}
		$sql=sprintf('insert into `%s`(%s) values(%s)',$table,$KeyStr,$ValueStr);
		$obj=self::$pdo->prepare($sql);
		$obj->execute($Values);
	}
	static function update($table,$where=array(),$data=array()){
		self::Init();
		if(empty($where)){
			throw new Exception("\$where is required");
		}
		if(empty($data)){
			throw new Exception ("\$data can’t be empty");
		}
		$set=self::CombineParam($data,', ');
		$param=self::CombineParam($where,'and ');
		$Values=array_merge($set['Values'],$param['Values']);
		$sql=sprintf('update `%s` set %s where %s',$table,$set['Str'],$param['Str']);
		$obj=self::$pdo->prepare($sql);
		$obj->execute($Values);
	}
	//$limit第一个为偏移量，第二个为查询条数
	static function get($table,$where=array(),$limit=array()){
		self::Init();
		if(!empty($limit)&&count($limit)!=2){
			Throw new Exception('Param limit is Wrong');
		}
		if(!empty($limit)&&count($limit)==2){
			$limitstr=sprintf('limit %s,%s',$limit[0],$limit[1]);
		}else{
			$limitstr='';
		}
		if(empty($where)){
			$sql=sprintf("select * from %s %s",$table,$limitstr);
			return self::query($sql);
		}else{
			$param=self::CombineParam($where,'and ');
			$sql=sprintf("select * from %s %s  where %s",$table,$limitstr,$param['Str']);
			return self::query($sql,$param['Values']);
		}
	}
	static function delete($table,$where=array()){
		if(empty($where)){
			throw new Exception("\$where can’t be empty");
			return;
		}
		$param=self::CombineParam($where,'and ');
		$sql=sprintf('delete from %s where %s',$table,$param['Str']);
		self::query($sql,$param['Values']);
	}
	static function CombineParam($data=array(),$l=''){
		$Str='';
		$i=0;
		foreach ($data as $k => $v) {
			if($i!=0){
				$link=$l;
			}else{
				$link='';
			}
			$Str.=$link.'`'.$k.'`'.'=?  ';
			$Values[]=$v;
			$i++;
		}
		return array(
			'Str'=>$Str,
			'Values'=>$Values,
			);
	}
}
