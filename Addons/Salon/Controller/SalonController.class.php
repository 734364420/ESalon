<?php
namespace Addons\Salon\Controller;
use Home\Controller\AddonsController;
class SalonController extends AddonsController{
	public function __construct() {
		parent::__construct();
		$this->model = M('Model')->getByName('e_salon');
		$this->assign ( 'model', $this->model );
	}

	function  Instruction() {
		$this->display();
	}
	//我的沙龙
	function MySalon() {
		e_auth();
//		$run = new \LfRunTime();
//		$run->star();
		$this->assign('url','Salon://Salon/CheckSalon');
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
					$data['end_date']=array('lt',strtotime($today));
				}elseif($salon_status == 2){
					$data['end_date']=array('egt',strtotime($today));
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
			$salons_publish = M('e_salon')->where($data)->select();
			for ($i = 0,$j = 0; $i < count($participattions); $i++) {
				$result= M('e_salon')->where('id='.$participattions[$i]['e_id'].' AND publish_userid!='.session('user_id'))->find();
				if($result){
					$salons_participate[$j]=$result;
					$j++;
				}
			}
		}else {
			$salons_publish = M('e_salon')->where('publish_userid=' . session('user_id'))->select();
			$participattions = M('e_participate')->where('user_id=' . session('user_id'))->select();
			for ($i = 0,$j = 0; $i < count($participattions); $i++) {
				$result= M('e_salon')->where('id='.$participattions[$i]['e_id'].' AND publish_userid!='.session('user_id'))->find();
				if($result){
					$salons_participate[$j]=$result;
					$j++;
				}
			}
		}
		$user = M('e_user')->where('id=' . session('user_id'))->find();
		$this->assign('user',$user);
		$this->salons_participate = $salons_participate;
		$this->salons_publish = $salons_publish;
		$this->assign('title','我的E沙龙');
//		$run->stop();
//		var_dump($run->spent());
		$this->display();
	}

	//查看发布沙龙详细信息
	function CheckSalon() {
		e_auth();
		$id=\LfRequest::inNum('id');
		$salon=M('e_salon')->where('id='.$id)->find();
		$data['hits']=$salon['hits']+1;
		M('e_salon')->where('id='.$id)->save($data);
		$this->assign('iteam',$salon);
		$user = M('e_user')->where('id='.$salon['publish_userid'])->find();
		$this->assign('user',$user);
		$participate_users=M('e_participate')->where('e_id='.$id)->select();
		$summaries=M('e_summary')->where('e_id='.$id)->select();
		for($i=0;$i<count($summaries);$i++) {
			$summaries_users[$i] = M('e_user')->where('id=' . $summaries[$i]['user_id'])->find();
		}
		$this->summaries_users=$summaries_users;
		$this->summaries=$summaries;
		$this->participate_users=$participate_users;
		$this->assign('sign_url','Salon://Salon/ParticipateSalon');
		$this->assign('title','沙龙活动详情');
		$this->display();
	}

	//新建沙龙
	function CreateSalon() {
		e_auth();
		if(IS_POST) {	
			$data['title']=\LfRequest::inStr('title');
			$date=\LfRequest::inStr('date');
			$time=\LfRequest::inStr('time');
			$hour=\LfRequest::inStr('hour');
			$start_date=$date.' '.$time;
			$end_date=$date.' '.date('H:i',strtotime($time)+3600*$hour);
			$data['start_date']=strtotime($start_date);
			$data['end_date']=strtotime($end_date);
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
			$this->assign('title','新建我的E沙龙');
			$this->display();
		}
	}
	//参加沙龙
	function ParticipateSalon() {
		e_auth();
		$id=\LfRequest::inNum('e_id');
		$data['user_id']=session('user_id');
		$data['e_id']=$id;
		$participated_number=M('e_salon')->where('id='.$id)->getField('participated_number');
		$participate_number=M('e_salon')->where('id='.$id)->getField('participate_number');
		var_dump($participated_number.'----'.$participate_number);
		die();
		if($participated_number == $participate_number){
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
		$map1['end_date']=array('egt',strtotime($today));
		$salons=M('e_salon')->where($map1)->select();
		$map2['end_date']=array('lt',strtotime($today));
		$end_salons=M('e_salon')->where($map2)->select();
		for($i=0;$i<count($salons);$i++) {
			$salons[$i]['username'] = M('e_user')->where('id=' . $salons[$i]['publish_userid'])->getField('student_name');
		}
		for($i=0;$i<count($end_salons);$i++){
			$end_salons[$i]['username'] = M('e_user')->where('id=' . $end_salons[$i]['publish_userid'])->getField('student_name');
		}
		$this->salons = $salons;
		$this->end_salons = $end_salons;
		$status=\LfRequest::inStr('status');
		if($status=='right'){
			$this->active2='active';
		}else{
			$this->active1='active';
		}
		$this->assign('title','E沙龙广场');
		$this->assign('url','Salon://Salon/CheckSalon');
		$this->display('Salon/SalonSquare');
	}
	//联系我们
	function Contact() {
		if(IS_POST){
			$data['uid']=session('user_id');
			$data['content']=\LfRequest::inStr('content');
			$result=M('suggestions')->add($data);
			if($result){
				$this->success('留言成功',addons_url('Salon://Salon/SalonSquare'));
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
		$status=\LfRequest::inStr('status');
		$type = \LfRequest::inStr('type');
		$day = \LfRequest::inStr('day');
		$space = \LfRequest::inStr('space');
		if(empty($type) && empty($day) &&empty($space)){
			redirect(addons_url('Salon://Salon/SalonSquare',array('status'=>$status)));
		}
		if ($type != null) {
			$data['type'] = $type;
		}
		if ($space != null) {
			$data['space'] = $space;
		}
		$today=date('Y-m-d',time());
		if($day==''&&$day!=0) {
			$data['start_date']=array('egt',strtotime($today));
			$data['start_date']=array('elt',strtotime($today)+3600);
			$user=M('e_salon');
			$this->salons=M('e_salon')->where($data)->select();
			$user->getLastSql();
		}
		if (!empty($day)) {
			if($day>=0){
				$data['start_date']=array('egt',strtotime($today));
				$data['start_date']=array('elt',strtotime($today)+3600*$day);
				$this->salons=M('e_salon')->where($data)->select();
			}else{
				$data['start_date']=array('egt',strtotime($today)+3600*$day);
				$data['start_date']=array('elt',strtotime($today));
				$this->end_salons=M('e_salon')->where($data)->select();
			}
		}
		if($status=='right'){
			$this->active2='active';
		}else{
			$this->active1='active';
		}
		$this->assign('title','查询结果');
		$this->display('Salon/SalonSquare');
	}

	function lists() {
		$users = M('e_user');
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据

		// 解析列表规则
		$list_data = $this->_list_grid ( $this->model );
		$grids = $list_data ['list_grids'];
		$fields = $list_data ['fields'];

		// 关键字搜索
		$map ['token'] = get_token ();
		$key = $this->model ['search_key'] ? $this->model ['search_key'] : 'title';
		if (isset ( $_REQUEST [$key] )) {
			$map [$key] = array (
				'like',
				'%' . htmlspecialchars ( $_REQUEST [$key] ) . '%'
			);
			unset ( $_REQUEST [$key] );
		}
		// 条件搜索
		foreach ( $_REQUEST as $name => $val ) {
			if (in_array ( $name, $fields )) {
				$map [$name] = $val;
			}
		}
		$row = empty ( $this->model ['list_row'] ) ? 20 : $this->model ['list_row'];

		// 读取模型数据列表

		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		$name = parse_name ( get_table_name ( $this->model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();

		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();

		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( '_page', $page->show () );
		}

		$this->assign ( 'list_grids', $grids );
		$this->assign ( 'list_data', $data );
		$this->assign ( 'publish', '1' );
		$this->display();
	}
	public function del() {
		$ids = I ( 'id', 0 );
		if (empty ( $ids )) {
			$ids = array_unique ( ( array ) I ( 'ids', 0 ) );
		}
		if (empty ( $ids )) {
			$this->error ( '请选择要操作的数据!' );
		}

		$Model = M ( get_table_name ( $this->model ['id'] ) );
		$map = array (
			'id' => array (
				'in',
				$ids
			)
		);
		$map ['token'] = get_token ();
		if ($Model->where ( $map )->delete ()) {
			$this->success ( '删除成功' );
		} else {
			$this->error ( '删除失败！' );
		}
	}
}
