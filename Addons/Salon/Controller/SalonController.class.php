<?php
namespace Addons\Salon\Controller;
use Home\Controller\AddonsController;
class SalonController extends AddonsController{
	public function __construct() {
		parent::__construct();
		$openid = I('openid');
		//e_auth($openid);
	}

	function  instruction() {
		$this->display();
	}

	function MySalon() {
		$this->display('Salon/instruction');
	}
	//发起E沙龙模块
	function CreateSalon() {
		if(IS_POST) {
			$data['title']='好好';
			$data['date']='haohao';
			$data['space']='haohao';
			$data['type']='haohao';
			$data['brief']='haohao';
			$result=$db=M('e_salon')->add($data);
			if($result){
				$this->success('添加成功',addons_url('Salon://Salon/instrucion'),3);
			}else{
				$this->error('添加失败，请检查原因');
			}
		} else {
			$this->display();
		}
	}

	function SalonSquare() {
		$this->display();
	}

	function Contact() {
	}
}
