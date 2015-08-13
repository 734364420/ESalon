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
		$data=M('e_salon')->where('publish_userid='.session('user_id'))->select();
		var_dump($data);
		$this->assign($data);
		$this->display('Salon/mysalon');
	}

	//�鿴����ɳ����ϸ��Ϣ
	function CheckSalon() {
		$id=\LfRequest::inNum('id');
		$salon=M('e_salon')->where('id='.$id)->find();
		$this->assign($salon);
		$this->display();
	}
	//�ܽ�
	function Summary(){
		if(IS_POST) {
			$data['summary']=\LfRequest::inStr('summary');
		}else{
			$this->display();
		}
	}

	//����Eɳ��ģ��
	function CreateSalon() {
		//if(IS_POST) {
			$data['title']='�ú�';
			$data['date']='haohao';
			$data['space']='haohao';
			$data['participate_number']=
			$data['type']='haohao';
			$data['brief']='haohao';
			$data['publish_userid']='1';
			$data['participated_number']='2';
			$result=M('e_salon')->add($data);
			if($result){
				$this->success('��ӳɹ�',addons_url('Salon://Salon/instruction'),3);
			}else{
				$this->error('���ʧ�ܣ�������Ϣ��д');
			}
//		} else {
//			$this->display();
//		}
	}
	//����Eɳ��
	function ParticipateSalon() {
		$id=\LfRequest::isGet('id');
		$data['user_id']=session('user_id');
		//$data['e_id']
		$participated_number=M('e_salon')->where('id='.$id)->getField('participated_number');
		$participate_number=M('e_salon')->where('id='.$id)->getField('participate_number');
		if($participated_number>=$participate_number){
			$this->error('�����������������ܱ�����');
		}
		$result=M('e_participate')->add($data);
		if($result){
			$this->success('����ɹ�',addons_url('Salon://Salon/instruction'),3);
		}else{
			$this->error('����ʧ�ܣ�������Ϣ��д');
		}
	}
	//Eɳ���㳡
	function SalonSquare() {
		$list = M('e_salon')->limit(20)->select();
		$this->assign($list);
		$this->display();
	}
	//��ϵ����
	function Contact() {
		if(IS_POST){
			$data['uid']=session('user_id');
			$data['content']=\LfRequest::inStr('content');
			$result=M('suggestions')->add($data);
			if($result){
				$this->success('���Գɹ�����л���Ľ���',addons_url('Salon://Salon/instruction'),3);
			}else{
				$this->error('����ʧ�ܿ����Ժ����ԡ���');
			}
		}else {
			$this->display();
		}
	}
}
