<?php
/**
 *$perpagenum为每页显示的数目
 *$totalnum为总的数据数目
 *$baseurl为分页的地url址
 */
/*使用实例
 * 		$page=new page;
$page->totalnum=200550;
$page->perpagenum=1000000;
$page->baseurl="http://btc456.dashi.us";
$page->currentpage=$page->GetCurrentPage();
$data=$page->GetData();
$data['offset']=$page->offset;
$data['perpagenum']=$page->perpagenum;
 */
class LfPage {
	private $perpagenum;
	private $totalnum;
	private $baseurl;
	private $currentpage;
	private $totalpage;
	private $offset;
	function __get($key) {
		if ($key == 'offset') {
			return $this->offset;
		}
		if ($key == 'perpagenum') {
			return $this->perpagenum;
		}
	}
	function __set($key, $value) {
		if ($key == 'perpagenum') {
			$this->perpagenum = intval($value);
			return;
		}
		if ($key == 'totalnum') {
			$this->totalnum = intval($value);
			return;
		}
		if ($key == 'baseurl') {
			$this->baseurl = $value;
			return;
		}
		if ($key == 'currentpage') {
			if ($value >= $this->GetTotalPage()) {
				$value = $this->GetTotalPage();
			}
			$this->currentpage = intval($value);
			return;
		}
	}
	//获取总的页数
	private function GetTotalPage() {
		$res = ceil($this->totalnum / $this->perpagenum);
		return $res;
	}
	//执行分页，返回分页的总数据
	public function GetData() {
		$this->offset = $this->GetOffset();
		$this->totalpage = $this->GetTotalPage();
		$page = $this->showpage();
		$next = $this->nexturl();
		$prev = $this->prevurl();
		$data = array(
			'totalpage' => $this->totalpage,
			'page' => $page,
			'prev' => $prev,
			'next' => $next,
		);
		return $data;

	}
	//获取下一页的url
	private function nexturl() {
		$next = $this->currentpage + 1;
		if ($next >= $this->totalpage) {
			$next = $this->totalpage;
		}
		if ($next <= 1) {
			$next = 1;
		}
		if (strstr($this->baseurl, '?')) {
			$url = $this->baseurl . "&";
		} else {
			$url = $this->baseurl . "?";
		}
		$url = $url . 'page=' . intval($next);
		return $url;
	}
	//获取上一页的url
	private function prevurl() {
		$prev = $this->currentpage - 1;
		if ($prev <= 1) {
			$prev = 1;
		}
		if (strstr($this->baseurl, '?')) {
			$url = $this->baseurl . "&";
		} else {
			$url = $this->baseurl . "?";
		}
		$url = $url . 'page=' . intval($prev);
		return $url;
	}
	//获取中间的分页显示
	private function showpage() {
		$n = 5; //设置页面上显示的分页的个数
		//		$row=floor(($this->currentpage)-1/$n)*$n+1;
		if (($this->currentpage - 2) > 1) {
			$row = $this->currentpage - 2;
		} else {
			$row = 1;
		}
		if (($row - $this->currentpage + $n) >= $this->totalpage) {
			$num = $this->totalpage;
		} else {
			$num = $row + $n;
		}
		if ($num >= $this->totalpage) {
			$num = $this->totalpage;
		}
		if (strstr($this->baseurl, '?')) {
			$url = $this->baseurl . "&";
		} else {
			$url = $this->baseurl . "?";
		}
		$page = array();
		if (($num - $row) <= 0) {
			$num = 1;
			$row = 1;
		}
		for ($i = $row; $i <= $num; $i++) {
			$page[] = array(
				'num' => $i,
				'isnow' => $i == $this->currentpage,
				'url' => $url . 'page=' . intval($i),
			);
		}
		return $page;
	}
	private function GetOffset() {
		return ($this->currentpage - 1) * $this->perpagenum;
	}
	public function GetCurrentPage() {
		if (empty($_REQUEST['page'])) {
			$currentpage = 1;
		} else {
			$currentpage = intval($_REQUEST['page']);
		}
		return $currentpage;
	}
}
###使用在公共函数中，直接调用
// function Page($totalnum, $perpagenum, $url) {
// 	$page              = new LfPage;
// 	$page->totalnum    = $totalnum;
// 	$page->perpagenum  = $perpagenum;
// 	$page->baseurl     = $url;
// 	$page->currentpage = $page->GetCurrentPage();
// 	$data              = $page->GetData();
// 	if (($offset = $page->offset) < 0) {
// 		$offset = 0;
// 	}
// 	$data['offset'] = $offset;
// 	return $data;
// }