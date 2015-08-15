<?php
namespace Addons\Salon\Controller;
use Home\Controller\AddonsController;
class SalonController extends AddonsController{
	public function __construct() {
		parent::__construct();
	}

	function  instruction() {
		$this->display();
	}
	//我的沙龙
	function MySalon() {
		e_auth();
		if(IS_POST){
			$participattions=M('e_participate')->where('user_id='.session('user_id'))->select();
			$type=\LfRequest::inStr('type');
			$salon_status=\LfRequest::inNum('salon_status');
			$salon_summary_status=\LfRequest::inNum('salon_summary_status');
			$today = date('Y-m-d',time());
			if($type != null) {
				$data['type'] = $type;
			}
			if(!empty($salon_status)){
				if($salon_status == 1){
					$data['date']=array('lt',$today);
				}elseif($salon_status == 2){
					$data['date']=array('egt',$today);
				}
			}
			if(!empty($salon_summary_status)) {
				if ($salon_summary_status == 1) {
					$data['summary'] = 1;
				} elseif ($salon_summary_status == 2) {
					$data['summary'] = 0;
				}
			}
			$data['publish_userid']=session('user_id');
			$user=M('e_salon');
			$salons_publish = M('e_salon')->where($data)->select();
			for ($i = 0,$j = 0; $i < count($participattions); $i++) {
				$result= M('e_salon')->where('id='.$participattions[$i]['e_id'].' AND publish_userid!='.session('user_id'))->find();
				if($result){
					$salons_participate[$j]=$result;
					$j++;
				}
			}
			for ($i = 0; $i < count($salons_participate); $i++) {
				if ($salons_participate[$i]['summary']==0) {
					$salons_participate[$i]['summary'] = '未总结';
				} else {
					$salons_participate[$i]['summary'] = '已总结';
				}
			}
			for ($i = 0; $i < count($salons_publish); $i++) {
				if ($salons_participate[$i]['summary']==0) {
					$salons_publish[$i]['summary'] = '未总结';
				} else {
					$salons_publish[$i]['summary'] = '已总结';
				}
			}
			$this->salons_publish=$salons_publish;
			$this->salons_participate=$salons_participate;
			$this->display();

		}else {
			$user = M('e_user')->where('id=' . session('user_id'))->getField('student_name');
			$salons_publish = M('e_salon')->where('publish_userid=' . session('user_id'))->select();
			$participattions = M('e_participate')->where('user_id=' . session('user_id'))->select();
			for ($i = 0,$j = 0; $i < count($participattions); $i++) {
				$result= M('e_salon')->where('id='.$participattions[$i]['e_id'].' AND publish_userid!='.session('user_id'))->find();
				if($result){
					$salons_participate[$j]=$result;
					$j++;
				}
			}
			for ($i = 0; $i < count($salons_participate); $i++) {
				if ($salons_participate[$i]['summary']==0) {
					$salons_participate[$i]['summary'] = '未总结';
				} else {
					$salons_participate[$i]['summary'] = '已总结';
				}
			}
			for ($i = 0; $i < count($salons_publish); $i++) {
				if ($salons_participate[$i]['summary']==0) {
					$salons_publish[$i]['summary'] = '未总结';
				} else {
					$salons_publish[$i]['summary'] = '已总结';
				}
			}
			$this->username = $user;
			$this->salons_participate = $salons_participate;
			$this->salons_publish = $salons_publish;
			$this->display();
		}
	}

	//查看发布沙龙详细信息
	function CheckSalon() {
		e_auth();
		$id=\LfRequest::inNum('id');
		$salon=M('e_salon')->where('id='.$id)->find();
		$data['hits']=$salon['hits']+1;
		M('e_salon')->where('id='.$id)->save($data);
		$this->salon=$salon;
		$this->publish_user=M('e_user')->where('id='.$salon['publish_userid'])->find();
		$participate_users=M('e_participate')->where('e_id='.$id)->select();
		for($i=0;$i<count($participate_users);$i++){
			$participate_users[$i]=M('e_user')->where('id='.$participate_users[$i]['user_id'])->find();
			if($participate_users[$i]['id']==session('user_id')){
				$this->status='已参加';
			}
		}
		$summaries=M('e_summary')->where('e_id='.$id)->select();
		for($i=0;$i<count($summaries);$i++) {
			$summaries_users[$i] = M('e_user')->where('id=' . $summaries[$i]['user_id'])->find();
		}
		$this->summaries_users=$summaries_users;
		$this->summaries=$summaries;
		$this->participate_users=$participate_users;
		$this->display('Salon/Detail');
	}
	//评价
	function Summary(){
		e_auth();
		if(IS_POST) {
			$data['stars']=\LfRequest::inNum('stars');
			$data['comment']=\LfRequest::inStr('comment');
			$data['e_id']=$id;
			$data['user_id']=session('user_id');
			$data['content']=\LfRequest::inStr('content');
			$result=M('e_suggestionscd')->add($data);
			if($result){
				$this->success('留言成功啦，谢谢啦',addons_url('Salon://Salon/SalonSquare'),3);
			}else{
				$this->error('出错啦，检查下建议呗？');
			}
		}else{
			$this->display();
		}
	}

	//新建沙龙
	function CreateSalon() {
		e_auth();
		if(IS_POST) {	
			$data['title']=\LfRequest::inStr('title');
			$data['date']=\LfRequest::inStr('date');
			$time=\LfRequest::inStr('time');
			$hour=\LfRequest::inStr('hour');
			$data['time_range']=$time.'~'.date('H:i',strtotime($time)+$hour*3600);
			$data['space']=\LfRequest::inStr('space');
			$data['participate_number']=\LfRequest::inStr('participate_number');
			$data['type']=\LfRequest::inStr('type');
			$data['brief']=\LfRequest::inStr('brief');
			$data['publish_userid']=session('user_id');
			$data['participated_number']=1;
			$data['hits']=0;
			$user = M('e_salon');
			$id=$user->add($data);
			if($id){
				$data['user_id']=session('user_id');
				$data['e_id']=$id;
				M('e_participate')->add($data);
				$this->success('新建成功',addons_url('Salon://Salon/MySalon'),3);
			}else{
				$this->error('创建失败咯，请仔细确认填写内容');
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
		e_auth();
		$id=\LfRequest::inNum('id');
		$data['user_id']=session('user_id');
		$data['e_id']=$id;
		$participated_number=M('e_salon')->where('id='.$id)->getField('participated_number');
		$participate_number=M('e_salon')->where('id='.$id)->getField('participate_number');
		if($participated_number>=$participate_number){
			$this->error('人数已满，稍后再试');
		}else{
			$number['participated_number']=$participated_number+1;
			$result=M('e_salon')->where('id='.$id)->save($number);
		}
		$result=M('e_participate')->add($data);
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();
		if($result){
			$this->success('参加成功',addons_url('Salon://Salon/SalonSquare',$param),3);
		}else{
			$this->error('参加失败');
		}
	}
	//沙龙广场
	function SalonSquare() {
		e_auth();
		$today=date('Y-m-d',time());
		$map1['date']=array('egt',$today);
		$salons=M('e_salon')->where($map1)->select();
		$map2['date']=array('lt',$today);
		$end_salons=M('e_salon')->where($map2)->select();
		for($i=0;$i<count($salons);$i++) {
			$salons[$i]['username'] = M('e_user')->where('id=' . $salons[$i]['publish_userid'])->getField('student_name');
		}
		for($i=0;$i<count($end_salons);$i++){
			$end_salons[$i]['username'] = M('e_user')->where('id=' . $end_salons[$i]['publish_userid'])->getField('student_name');
		}
		$this->salons = $salons;
		$this->end_salons = $end_salons;
		$this->display('Salon/SalonSquare');
	}
	//联系我们
	function Contact() {
		if(IS_POST){
			$data['uid']=session('user_id');
			$data['content']=\LfRequest::inStr('content');
			$result=M('suggestions')->add($data);
			if($result){
				$this->success('留言成功',addons_url('Salon://Salon/SalonSquare'),3);
			}else{
				$this->error('留言失败');
			}
		}else {
			$this->display();
		}
	}

	//E沙龙广场根据条件查找
	function GetSalonWith()
	{
		$type = \LfRequest::inStr('type');
		$day = \LfRequest::inStr('day');
		$space = \LfRequest::inStr('space');
		if ($type != null) {
			$data['type'] = $type;
		}
		$today=date('Y-m-d',time());
		if($day!=''&&$day==0) {
			$data['date']=$day;
		}
		if (!empty($day)) {
			if($day>=0){
				$data['date']=array('egt',$today);
				$data['date']=array('elt',date('Y-m-d',strtotime($today)+3600*$day));
			}else{
				$data['date']=array('egt',date('Y-m-d',strtotime($today)+3600*$day));
				$data['date']=array('elt',$today);
			}
		}
		if ($space != null) {
			$data['space'] = $space;
		}
		$this->display('Salon/SalonSquare');
	}
}
