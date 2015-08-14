<?php
namespace Addons\Salon\Controller;
use Home\Controller\AddonsController;
class SalonController extends AddonsController{
	public function __construct() {
		parent::__construct();
		e_auth();
	}

	function  instruction() {
		$this->display();
	}
	//我的沙龙
	function MySalon() {
		$salons=M('e_salon')->where('publish_userid=1')->select();
		for($i=0;$i<count($salons);$i++){
			if(empty($salons[$i]['summary'])){
				$salons[$i]['summary']='未总结';
			}else{
				$salons[$i]['summary']='已总结';
			}
		}
		$this->salons=$salons;
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

	//新建沙龙
	function CreateSalon() {
		if(IS_POST) {
		$data['title']='啊啊啊啊';
		$data['date']='haohao';
		$data['space']='haohao';
		$data['participate_number']=1;
		$data['type']='haohao';
		$data['brief']='haohao';
		$data['publish_userid']=session('user_id');
		$data['participated_number']=2;
		$data['hits']=0;
		$user = M('e_salon');
		$result=$user->add($data);
		if($result){
			$this->success('新建成功',addons_url('Salon://Salon/MySalon'),3);
		}else{
			$this->error($user->getDbError());
		}
		} else {
			$this->display();
		}
	}
	//参加沙龙
	function ParticipateSalon() {
		$id=\LfRequest::isGet('id');
		$data['user_id']=session('user_id');
		//$data['e_id']
		$participated_number=M('e_salon')->where('id='.$id)->getField('participated_number');
		$participate_number=M('e_salon')->where('id='.$id)->getField('participate_number');
		if($participated_number>=$participate_number){
			$this->error('参加失败咯，稍后再试');
		}
		$result=M('e_participate')->add($data);
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();
		if($result){
			$this->success('参加成功',addons_url('Salon://Salon/instruction',$param),3);
		}else{
			$this->error('参加失败');
		}
	}
	//沙龙广场
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
				$this->success('留言成功',addons_url('Salon://Salon/instruction'),3);
			}else{
				$this->error('留言失败');
			}
		}else {
			$this->display();
		}
	}
}
