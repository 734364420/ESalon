<?php

namespace Addons\Salon\Controller;
use Home\Controller\AddonsController;

class SalonController extends AddonsController{
	public function __construct() {
		parent::__construct();
		$openid = $_GET['openid'];
		e_auth($openid);
	}

	function  instruction() {
		$this->display();
	}

	function MySalon() {
		$this->display('Salon/instruction');
	}

	function Summary() {
		$this->display
	}
}
