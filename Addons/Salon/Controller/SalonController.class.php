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
	//����Eɳ��ģ��
	function CreateSalon() {
		if(IS_POST) {
			$data['title']='�ú�';
			$data['date']='haohao';
			$data['space']='haohao';
			$data['type']='haohao';
			$data['brief']='haohao';
			$result=$db=M('e_salon')->add($data);
			if($result){
				$this->success('��ӳɹ�',addons_url('Salon://Salon/instrucion'),3);
			}else{
				$this->error('���ʧ�ܣ�����ԭ��');
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
