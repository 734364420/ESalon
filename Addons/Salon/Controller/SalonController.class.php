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
	//我的E沙龙模块
	function MySalon() {
		$this->display('Salon/instruction');
	}
	//我发起的E沙龙列表
	function MysSalonLists() {
		$data=M('e_salon')->where('publish_userid='.session('user_id'))->select();
		$this->assign($data);
		$this->display();
	}
	//查看发布沙龙详细信息
	function CheckSalon() {
		$id=\LfRequest::isGet('id');
		$salon=M('e_salon')->where('id='.$id)->find();
		$this->assign($salon);
		$this->display();
	}
	//总结

	//发起E沙龙模块
	function CreateSalon() {
		if(IS_POST) {
			$data['title']='好好';
			$data['date']='haohao';
			$data['space']='haohao';
			$data['participate_number']=
			$data['type']='haohao';
			$data['brief']='haohao';
			$data['publish_userid']=seesion('user_id');
			$data['participated_number']=
			$result=$db=M('e_salon')->add($data);
			if($result){
				$this->success('添加成功',addons_url('Salon://Salon/instruction'),3);
			}else{
				$this->error('添加失败，请检查信息填写');
			}
		} else {
			$this->display();
		}
	}
	//参与E沙龙
	function ParticipateSalon() {
		if(IS_POST) {
			$data['user_id']=session('user_id');
			$data['e_id']
			$result=M('e_participate')->add($data);
			if($result){
				$this->success('参与成功',addons_url('Salon://Salon/instruction'),3);
			}else{
				$this->error('参与失败，请检查信息填写');
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
