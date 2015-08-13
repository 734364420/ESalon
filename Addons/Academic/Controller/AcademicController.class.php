<?php

namespace Addons\Academic\Controller;
use Home\Controller\AddonsController;

class AcademicController extends AddonsController{
	public function __construct() {
		parent::__construct();
		$openid = $_GET['openid'];
        //e_auth($openid);
	}
    //最新学术动态页面
    function LastNews() {

    }
    //我的iteam页面
    function MyIteam() {}
    //发起团队约
    function Publish() {}
    //Iteam广场
    function Square() {}
}
