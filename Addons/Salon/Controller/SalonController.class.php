<?php

namespace Addons\Salon\Controller;
use Home\Controller\AddonsController;

class SalonController extends AddonsController{
	public function __construct() {
		parent::__construct();
		$openid = $_GET['openid'];
	}

	function  instruction() {
		$this->display();
	}

	function mysalon() {
		if(!$openid){
			$this->display('Salon/instruction');
		}else{
			$this->display('Salon/addinformation');
		}
	}
}
