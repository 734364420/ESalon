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
		$data=M('e_salon')->where('publish_userid='.session('user_id'))->select();
		var_dump($data);
		$this->assign($data);
		$this->display('Salon/mysalon');
	}

	//查看发布沙龙详细信息
	function CheckSalon() {
		$id=\LfRequest::inNum('id');
		$salon=M('e_salon')->where('id='.$id)->find();
		$this->assign($salon);
		$this->display();
	}
	//总结
	function Summary(){
		if(IS_POST) {
			$data['summary']=\LfRequest::inStr('summary');
		}else{
			$this->display();
		}
	}

	//发起E沙龙模块
	function CreateSalon() {
		//if(IS_POST) {
			$data['title']='好好';
			$data['date']='haohao';
			$data['space']='haohao';
			$data['participate_number']=
			$data['type']='haohao';
			$data['brief']='haohao';
			$data['publish_userid']='1';
			$data['participated_number']='2';
			$result=M('e_salon')->add($data);
			if($result){
				$this->success('添加成功',addons_url('Salon://Salon/instruction'),3);
			}else{
				$this->error('添加失败，请检查信息填写');
			}
//		} else {
//			$this->display();
//		}
	}
	//参与E沙龙
	function ParticipateSalon() {
		$id=\LfRequest::isGet('id');
		$data['user_id']=session('user_id');
		//$data['e_id']
		$participated_number=M('e_salon')->where('id='.$id)->getField('participated_number');
		$participate_number=M('e_salon')->where('id='.$id)->getField('participate_number');
		if($participated_number>=$participate_number){
			$this->error('参与人数已满，不能报名咯');
		}
		$result=M('e_participate')->add($data);
		if($result){
			$this->success('参与成功',addons_url('Salon://Salon/instruction'),3);
		}else{
			$this->error('参与失败，请检查信息填写');
		}
	}
	//E沙龙广场
	function SalonSquare() {
		$list = M('e_salon')->limit(20)->select();
		$this->assign($list);
		$this->display();
	}
	//联系我们
	function Contact() {
		if(IS_POST){
			$data['uid']=session('user_id');
			$data['content']=\LfRequest::inStr('content');
			$result=M('suggestions')->add($data);
			if($result){
				$this->success('留言成功，感谢您的建议',addons_url('Salon://Salon/instruction'),3);
			}else{
				$this->error('留言失败咯，稍后再试……');
			}
		}else {
			$this->display();
		}
	}
}
