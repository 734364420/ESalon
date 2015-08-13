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
	//�ҵ�Eɳ��ģ��
	function MySalon() {
		$this->display('Salon/instruction');
	}
	//�ҷ����Eɳ���б�
	function MysSalonLists() {
		$data=M('e_salon')->where('publish_userid='.session('user_id'))->select();
		$this->assign($data);
		$this->display();
	}
	//�鿴����ɳ����ϸ��Ϣ
	function CheckSalon() {
		$id=\LfRequest::isGet('id');
		$salon=M('e_salon')->where('id='.$id)->find();
		$this->assign($salon);
		$this->display();
	}
	//�ܽ�

	//����Eɳ��ģ��
	function CreateSalon() {
		if(IS_POST) {
			$data['title']='�ú�';
			$data['date']='haohao';
			$data['space']='haohao';
			$data['participate_number']=
			$data['type']='haohao';
			$data['brief']='haohao';
			$data['publish_userid']=seesion('user_id');
			$data['participated_number']=
			$result=$db=M('e_salon')->add($data);
			if($result){
				$this->success('��ӳɹ�',addons_url('Salon://Salon/instruction'),3);
			}else{
				$this->error('���ʧ�ܣ�������Ϣ��д');
			}
		} else {
			$this->display();
		}
	}
	//����Eɳ��
	function ParticipateSalon() {
		if(IS_POST) {
			$data['user_id']=session('user_id');
			$data['e_id']
			$result=M('e_participate')->add($data);
			if($result){
				$this->success('����ɹ�',addons_url('Salon://Salon/instruction'),3);
			}else{
				$this->error('����ʧ�ܣ�������Ϣ��д');
			}
		}
	}

	function SalonSquare() {
		$list = M('e_salon')->limit(20)->select();
		$this->assign($list);
		$this->display();
	}

	function Contact() {
		$this->display();
	}
}
