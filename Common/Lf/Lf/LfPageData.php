<?php
class LfPageData {
	static function Page($totalnum,$url,$perpagenum=10) {
		$page = new LfPage;
		$page->totalnum = $totalnum;
		$page->perpagenum = $perpagenum;
		$page->baseurl = $url;
		$page->currentpage = $page->GetCurrentPage();
		$data = $page->GetData();
		if (($offset = $page->offset) < 0) {
			$offset = 0;
		}
		$data['offset'] = $offset;
		$data['perpagenum'] = $perpagenum;
		return $data;
	}
}