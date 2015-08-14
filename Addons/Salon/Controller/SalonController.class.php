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
		$user=M('e_user')->where('id='.session('user_id'))->getField('student_name');
		$method=\LfRequest::inStr('sign');
		var_dump($method);
		if(empty($method)) {
			$salons = M('e_salon')->where('publish_userid=' . session('user_id'))->select();
			$this->active1='active';
		}else{
			$participattions=M('e_participate')->where('user_id='.session('user_id'))->select();
			for($i=0;$i<count($participattions);$i++){
				$salons[$i]=M('e_salon')->where('id='.$participattions[$i]['e_id'])->find();
			}
			$this->active2='active';
		}
		for($i=0;$i<count($salons);$i++){
			if(empty($salons[$i]['summary'])){
				$salons[$i]['summary']='未总结';
			}else{
				$salons[$i]['summary']='已总结';
			}
		}
		$this->username=$user;
		$this->salons=$salons;
		$this->display();
	}

	//查看发布沙龙详细信息
	function CheckSalon() {
		$id=\LfRequest::inNum('id');
		$salon=M('e_salon')->where('id='.$id)->find();
		$data['hits']=$salon['hits']+1;
		$salon=M('e_salon')->where('id='.$id)->save($data);
		$this->salon=$salon;
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
			$data['title']=\LfRequest::inStr('title');
			$date=\LfRequest::inStr('date');
			$time=\LfRequest::inStr('time');
			$hour=\LfRequest::inStr('hour');
			$data['date']=$date.' '.$time.'~'.date('H:i',strtotime("'+'.$hour.' hour'",$time));
			$data['space']=\LfRequest::inStr('space');
			$data['participate_number']=\LfRequest::inStr('participate_number');
			$data['type']=\LfRequest::inStr('type');
			$data['brief']=\LfRequest::inStr('brief');
			$data['publish_userid']=session('user_id');
			$data['hits']=0;
			var_dump($data);
			die();
			$user = M('e_salon');
			$result=$user->add($data);
			if($result){
				$this->success('新建成功',addons_url('Salon://Salon/MySalon'),3);
			}else{
				$this->error($user->getDbError());
			}
		} else {
			for($i=0;$i<=10;$i++){
				$times[$i]=date("Y-m-d",strtotime("+$i day"));
			}
			$this->times=$times;
			$this->display();
		}
	}
	//参加沙龙
	function ParticipateSalon() {
		$id=\LfRequest::inNum('id');
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
