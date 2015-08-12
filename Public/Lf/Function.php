<?php
/*
html内容转义并返回,可以在html的标签内容和标签属性值处,输出数据
因为调用者太多,太常用而被简写
$html = '<h1>'.h($title).'</h1>';
$html = '<a href="'.h($url).'">123</a>';
在标签属性值输出时,必须使用引号把属性值括起来
*/
if (!function_exists('h')){
	function h($string){
		return LfSecurity::XssHtmlBodyEncode($string);
	}
}
/*
html内容转义并echo,可以在html的标签内容和标签属性值处,输出数据
因为调用者太多,太常用而被简写
<h1><?php eh($title);?></h1>
<a href="<?php eh($url);?>">123</a>
在标签属性值输出时,必须使用引号把属性值括起来
*/
if (!function_exists('eh')){
	function eh($string){
		echo LfSecurity::XssHtmlBodyEncode($string);
	}
}
/*
html上的json的转义并echo,可以向html模板传递json数据.
<script>
var initData = <?php exje($data); ?>
</script>
echo xss json encode 因为太常用而被简写ejson
*/
function ejson($obj){
	echo LfSecurity::XssJsonEncode($obj);
}
/*
url的参数值转义并返回,可以在url中输出数据,也可以在html的url中输出数据
$url = 'http://www.somesite.com?test='.xupve($test);
xss url param value encode因为太常用而被简写hurl
*/
function hurl($s){
	return LfSecurity::XssUrlParamValueEncode($s);
}
/*
*判断当前的标签是否为active状态,返回class的值
*/
function IsActive($name='',$active){
	if($name==$active){
		return 'class="active"';
	}else{
		return '';
	}
}
function IsBlock($name='',$active){
	if($name==$active){
		return 'style="display:block;"';
	}else{
		return '';
	}
}
/*
判断当前的option选项中的value与循环中的value是否一致，一致时返回selected="selected"
*/
function IsSelected($value,$str){
	if($value==$str){
		return 'selected="selected"';
	}else{
		return '';
	}
}
function CheckLogin(){
	try {
		if(isset($_SESSION['expiretime'])) {
			if($_SESSION['expiretime'] < time()) {
				unset($_SESSION['expiretime']);
				redirect('admin/index');
				exit(0);
			}
		}
		if(@empty($_SESSION['UserName'])||@empty($_SESSION['user'])||@empty($_SESSION['uid'])){
			return false;
		}
		$user=LfDatabase::get("adminuser",array('Id'=>intval(@$_SESSION['uid'])));
		if(@md5($user[0]['UserName'])==@$_SESSION['user']){
			return true;
		}else{
			return false;
		}
	} catch (Exception $e) {
		return false;
	}
}
###使用在公共函数中，直接调用
function Page($totalnum, $perpagenum, $url) {
	$page              = new LfPage;
	$page->totalnum    = $totalnum;
	$page->perpagenum  = $perpagenum;
	$page->baseurl     = $url;
	$page->currentpage = $page->GetCurrentPage();
	$data              = $page->GetData();
	if (($offset = $page->offset) < 0) {
		$offset = 0;
	}
	$data['offset'] = $offset;
	$data['perpagenum']=$perpagenum;
	return $data;
}
function str_cut($string, $length, $dot = '...')
{
	$strlen = strlen($string);
	if ($strlen <= $length) return $string;
	$string = str_replace(array(' ', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	$length = intval($length - strlen($dot) - $length / 3);
	$n = $tn = $noc = 0;
	while ($n < strlen($string)) {
		$t = ord($string[$n]);
		if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
			$tn = 1;
			$n++;
			$noc++;
		} elseif (194 <= $t && $t <= 223) {
			$tn = 2;
			$n += 2;
			$noc += 2;
		} elseif (224 <= $t && $t <= 239) {
			$tn = 3;
			$n += 3;
			$noc += 2;
		} elseif (240 <= $t && $t <= 247) {
			$tn = 4;
			$n += 4;
			$noc += 2;
		} elseif (248 <= $t && $t <= 251) {
			$tn = 5;
			$n += 5;
			$noc += 2;
		} elseif ($t == 252 || $t == 253) {
			$tn = 6;
			$n += 6;
			$noc += 2;
		} else {
			$n++;
		}
		if ($noc >= $length) {
			break;
		}
	}
	if ($noc > $length) {
		$n -= $tn;
	}
	$strcut = substr($string, 0, $n);
	return $strcut . $dot;
}
function getExt($file) {
	$ext = strtolower(strrchr($file, '.'));

	return $ext;
}
function timediff($begin_time,$end_time)
{
    if($begin_time < $end_time){                                    //判断传入的时间哪一个大
    	$starttime = $begin_time;
    	$endtime = $end_time;
    }
    else{
    	$starttime = $end_time;
    	$endtime = $begin_time;
    }
    $timediff = $endtime-$starttime;                                //获取整个时间差
    $days = intval($timediff/86400);                                //除以86400获得相差的天数
    $remain = $timediff%86400;                                      //获得除以86400余数
    $hours = intval($remain/3600);                                  //获取小时差
    $remain = $remain%3600;
    $mins = intval($remain/60);                                     //获取分钟数差
    $secs = $remain%60;
    $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
    return $days;
}
function base64url_encode($data) { 
	return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 

function base64url_decode($data) { 
	return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
}
function isPermission($cateId,$uid){
	$data=LfDatabase::get('adminuser',array('Id'=>$uid));
	if($data[0]['Type']=="超级管理员"){
		return true;
	}
	$data[0]['permission']=json_decode($data[0]['permission']);
	return @in_array($cateId,$data[0]['permission']);	
}
function isChecked($cateId,$permission){
	$data['permission']=json_decode($permission);
	if(@in_array($cateId, $data['permission'])) {
		return 'checked="checked"';
	}	
}
function ilog($obj)
{ 
	    $LogPath=ROOTPATH.'/application/logs';
		$logName='userLog';
		$s=$obj.'------'.date("Y-m-d H:i:s")."\r\n";
	    @mkdir($LogPath);
	    @file_put_contents($LogPath . '/' . $logName . '.log', $s, FILE_APPEND);
}
function ArrayUnique($arr, $key)
{
    	$rAr=array();
    	for($i=0;$i<count($arr);$i++)
    	{
    		if(!isset($rAr[$arr[$i][$key]]))
    		{
    			$rAr[$arr[$i][$key]]=$arr[$i];
    		}
    	}
    	$arr=array_values($rAr);
    	return $arr;
}
function redirect($url, $time=0, $msg='') {
	//多行URL地址支持
	$url        = str_replace(array("\n", "\r"), '', $url);
	if (empty($msg))
		$msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (!headers_sent()) {
		// redirect
		if (0 === $time) {
			header('Location: ' . $url);
		} else {
			header("refresh:{$time};url={$url}");
			echo($msg);
		}
		exit();
	} else {
		$str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0)
			$str .= $msg;
		exit($str);
	}
}
