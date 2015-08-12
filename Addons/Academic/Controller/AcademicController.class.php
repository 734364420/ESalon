<?php

namespace Addons\Academic\Controller;
use Home\Controller\AddonsController;

class AcademicController extends AddonsController{
	public function __construct() {
		parent::__construct();
		$openid = $_GET['openid'];
	}
	function  instruction() {
		$this->success("成功了哦");
	}
}
