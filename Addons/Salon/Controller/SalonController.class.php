<?php
namespace Addons\Salon\Controller;
use Addons\Coupons\Model\CouponsModel;
use Addons\Credit\Model\CreditModel;
use Home\Controller\AddonsController;
class SalonController extends AddonsController{
	public function __construct() {
		parent::__construct();
		$this->model = M('Model')->getByName('e_salon');
		$this->assign ( 'model', $this->model );
		$this->assign('is_iteam',0);
	}

	function  Instruction() {
		$this->assign('title','教你玩转E沙龙');
		$this->display();
	}
	//我的沙龙
	function MySalon() {
		e_auth();
//		$run = new \LfRunTime();
//		$run->star();
		$status=\LfRequest::inStr('status');
		if(empty($status)){
			$status='sign';
		}
		$this->assign('url','Salon://Salon/CheckSalon');
		$participattions=M('e_participate')->where('user_id='.session('user_id').' and is_iteam=0')->select();
	if(IS_POST) {
		$type = \LfRequest::inStr('type');
		$salon_status = \LfRequest::inNum('salon_status');
		$salon_summary_status = \LfRequest::inNum('salon_summary_status');
		$data='';
		if ($type != null) {
			$data .= 'type = '."'".$type."'".' AND ';
		}
		if (!empty($salon_status)) {
			if ($salon_status == 1) {
				$data .='end_date < '.time().' AND ';
			} elseif ($salon_status == 2) {
				$data .='end_date >= '.time().' AND ';
			}
		}
		if (!empty($salon_summary_status)) {
			if ($salon_summary_status == 1) {
				$data .='summary = 1 AND ';
			} elseif ($salon_summary_status == 2) {
				$data .='summary = 0 AND ';
			}
		}
	}
		$in='(0';
		foreach($participattions as $v) {
			$in .= ','.$v['e_id'];
		}
		$in .= ')';
		$Pdata = $data .' id in '.$in.' AND publish_userid  != '.session('user_id');
		$data = $data.' publish_userid = '.session('user_id');
		$salons_publish = M('e_salon')->where($data)->select();
		$salons_participate= M('e_salon')->where($Pdata)->count();
		$param = array(
			'type'=>I('type',''),
			'salon_status'=>I('salon_status',''),
			'salon_summary_status'=>I('salon_summary_status','')
		);
		$PublishPage = \LfPageData::Page($salons_publish,addons_url('Salon://Salon/MySalon/status/sign',$param));
		$ParticipatePage = \LfPageData::Page($salons_participate,addons_url('Salon://Salon/MySalon/status/end',$param));

		$salons_publish = M('e_salon')->where($data)->order('id desc')->limit($PublishPage['offset'],$PublishPage['perpagenum'])->select();
		$salons_participate= M('e_salon')->where($Pdata)->order('id desc')->limit($ParticipatePage['offset'],$ParticipatePage['perpagenum'])->select();
		$user = M('e_user')->where('id=' . session('user_id'))->find();
		$this->assign('status',$status);
		$this->assign('PublishPage',$PublishPage);
		$this->assign('ParticipatePage',$ParticipatePage);
		$this->assign('user',$user);
		$this->assign('type',I('type',''));
		$this->assign('salon_status',I('salon_status',''));
		$this->assign('salon_summary_status',I('salon_summary_status',''));
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
		$participate_users=M('e_participate')->where('e_id='.$id.' and is_iteam=0')->select();
		$summaries=M('e_summary')->where('e_id='.$id.' and is_iteam=0'.' and is_pass=1')->select();
		for($i=0;$i<count($summaries);$i++) {
			$summaries_users[$i] = M('e_user')->where('id=' . $summaries[$i]['user_id'])->find();
		}
		$this->summaries_users=$summaries_users;
		$this->summaries=$summaries;
		$this->participate_users=$participate_users;
		$this->assign('isSalon','1');
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
			if($data['end_date']<=time()){
				$this->error('活动时间已结束咯，请检查活动时间');
			}
			$data['space']=\LfRequest::inStr('space');
			$room = M('room')->where(array('name'=>$data['space']))->find();
			if($room) {
                if($data['start_date'] < $room['end_time'] && $data['start_date'] > $room['start_time'])
                    $this->error($data['space']."已被借用");
                if($data['end_date'] < $room['end_time'] && $data['end_date'] > $room['start_time'])
                    $this->error($data['space']."已被借用");
                if($data['end_date'] > $room['end_time'] && $data['start_date'] > $room['start_time'])
                    $this->error($data['space']."已被借用");
                exit();
            }
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
                /*
                 * 活动发起人获得20元代金劵
                 */
                $this->createCoupons(session('user_id'),$id,20);
                $this->success('新建成功',addons_url('Salon://Salon/MySalon'),3);
			}else{
				$this->error('创建失败咯，请仔细确认填写内容');
			}
		} else {
			//只能预约创建七天之内的salon
			for($i=0;$i<=7;$i++){
				$times[$i]=date("Y-m-d",strtotime("+$i day"));
			}
			$IteamType = M('e_iteam_type');
			$types = $IteamType->select();
			$this->assign('types',$types);
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
		$data['is_iteam']=0;
		$participated_number=M('e_salon')->where('id='.$id)->getField('participated_number');
		$participate_number=M('e_salon')->where('id='.$id)->getField('participate_number');
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
            /*
             * 活动参与人获得10元代金劵
             */
            $this->createCoupons(session('user_id'),$id,10);
			$this->success('参加成功',addons_url('Salon://Salon/SalonSquare',$param),3);
		}else{
			$this->error('参加失败');
		}
	}
	//沙龙广场
	function SalonSquare() {
		e_auth();
		$status=\LfRequest::inStr('status');
		if(empty($status)){
			$status='sign';
		}
		$map1['end_date']=array('egt',time());
		$salons=M('e_salon')->where($map1)->count();
		$SignPage=\LfPageData::Page($salons,addons_url('Salon://Salon/SalonSquare/status/sign'));
		$this->salons=M('e_salon')->where($map1)->order('id desc')->limit($SignPage['offset'],$SignPage['perpagenum'])->select();
		$map2['end_date']=array('lt',time());
		$end_salons=M('e_salon')->where($map2)->count();
		$EndPage=\LfPageData::Page($end_salons,addons_url('Salon://Salon/SalonSquare/status/end'));
		$this->end_salons=M('e_salon')->where($map2)->order('id desc')->limit($EndPage['offset'],$EndPage['perpagenum'])->select();
		$this->assign('status',$status);
		$this->assign('EndPage',$EndPage);
		$this->assign('SignPage',$SignPage);
		$this->assign('status',$status);
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
		$param = array(
			'type'=>I('type',''),
			'day'=>I('day',''),
			'space'=>I('space','')
		);
		$data='';
		if(empty($type) && empty($day) &&empty($space)){
			redirect(addons_url('Salon://Salon/SalonSquare',array('status'=>$status)));
		}
		if ($type != null) {
			if(!empty($space) || !empty($day)){
				$data .= 'type = '."'".$type."'".' AND ';
			}else {
				$data .= 'type = '."'".$type."'";
			}
		}
		if ($space != null) {
			if(!empty($day)){
				$data .= 'space = '."'".$space."'".' AND ';
		}	else {
				$data .= 'space = '."'".$space."'";
			}
		}
		$today=date('Y-m-d',time());
		if($day==1) {
			$data .='start_date >= '.strtotime($today).' AND start_date <= '.(strtotime($today)+24*3600);
		}
		if (!empty($day) && $day!=1) {
			if($day>=0){
				$data .='start_date >= '.strtotime($today).' AND start_date <= '.(strtotime($today)+24*3600*$day).' AND end_date >= '.time();
			}else{
				$data .='start_date >= '.(strtotime($today)+24*3600*$day).' AND start_date <= '.strtotime($today).' AND end_date <'.time();
			}
		}
		$map2 = $data.' AND end_date < '.time();
		$end_salons=M('e_salon')->where($map2)->select();
		$EndPage=\LfPageData::Page($end_salons,addons_url('Salon://Salon/GetSalonWith/status/end',$param));
		$this->end_salons=M('e_salon')->where($map2)->order('id desc')->limit($EndPage['offset'],$EndPage['perpagenum'])->select();
		$map1 = $data.' AND end_date >= '.time();
		$salons=M('e_salon')->where($map1)->select();
		$SignPage=\LfPageData::Page($salons,addons_url('Salon://Salon/GetSalonWith/status/sign',$param));
		$this->salons=M('e_salon')->where($map1)->order('id desc')->limit($SignPage['offset'],$SignPage['perpagenum'])->select();
		$this->assign('status',$status);
		$this->assign('EndPage',$EndPage);
		$this->assign('SignPage',$SignPage);
		$this->assign('url','Salon://Salon/CheckSalon');
		$this->assign('type',I('type',''));
		$this->assign('day',I('day',''));
		$this->assign('space',I('space',''));
		$this->assign('title','查询结果');
		$this->display('Salon/SalonSquare');
	}
    /*
     * 活动代金劵
     */
    private function createCoupons($uid,$sid,$money) {
        $coupons = new CouponsModel();
        if(!$coupons->create($uid,$sid,$money)) $this->error("啊哦,获得代金劵失败了!");
    }






	function lists()
	{
		$users = M('e_user');
		$page = I('p', 1, 'intval'); // 默认显示第一页数据

		// 解析列表规则
		$list_data = $this->_list_grid($this->model);
		$grids = $list_data ['list_grids'];
		$fields = $list_data ['fields'];

		// 关键字搜索
		$map ['token'] = get_token();
		$key = $this->model ['search_key'] ? $this->model ['search_key'] : 'title';
		if (isset ($_REQUEST [$key])) {
			$map [$key] = array(
				'like',
				'%' . htmlspecialchars($_REQUEST [$key]) . '%'
			);
			unset ($_REQUEST [$key]);
		}
		// 条件搜索
		foreach ($_REQUEST as $name => $val) {
			if (in_array($name, $fields)) {
				$map [$name] = $val;
			}
		}
		$row = empty ($this->model ['list_row']) ? 20 : $this->model ['list_row'];

		// 读取模型数据列表

		empty ($fields) || in_array('id', $fields) || array_push($fields, 'id');
		if(\LfRequest::inNum('summary')&&in_array(\LfRequest::inNum('summary'),[1,2,3])) {
			$map['summary'] = \LfRequest::inNum('summary')-1;
		}
		$name = parse_name(get_table_name($this->model ['id']), true);
		$data = M($name)->field(empty ($fields) ? true : $fields)->where($map)->order('id DESC')->page($page, $row)->select();
		foreach($data as $i=>$d) {
			if($d['summary'] == 0) {
				$data[$i]['summary'] = "否";
			}
			if($d['type'] == 2) {
				$data[$i]['summary'] = "是";
			}
		}
		/* 查询记录总数 */
		$count = M($name)->where($map)->count();

		// 分页
		if ($count > $row) {
			$page = new \Think\Page ($count, $row);
			$page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
			$this->assign('_page', $page->show());
		}

		$this->assign('list_grids', $grids);
		$this->assign('list_data', $data);
		$this->assign('publish', '1');
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

	public function show(){
		$id=I('id');
		$salon=M('e_salon')->where('id='.$id)->find();
		$this->assign('iteam',$salon);
		$user = M('e_user')->where('id='.$salon['publish_userid'])->find();
		$this->assign('publisher',$user);
		$participates=M('e_participate')->where('is_iteam=0 and e_id='.$id.' and is_iteam=0')->select();
		for($i=0;$i<count($participates);$i++){
			$participates[$i]=M('e_user')->where('id='.$participates[$i]['user_id'])->find();
		}
		$this->assign('participates',$participates);
		$this->display();
	}
	public function passSummary() {
		$id = \LfRequest::inNum('id');
		$salon=M('e_salon')->where('id='.$id)->find();
		if($salon['summary']==0) $this->error("未总结");
		if($salon['summary']==2) $this->error("已经审核通过");
		$res = M('e_salon')->where('id='.$id)->save(['summary'=>2]);
		if($res) {
			$summarys = M('e_summary')->where(array('e_id'=>$id))->select();
			foreach($summarys as $s) {
				if($s['user_id']==$salon['publish_userid']) {
					$creditValue = 10;
				} else {
					$creditValue = 2;
				}
				$credit = new CreditModel();
				$credit->updateCredit($s['user_id'],$creditValue);
			}
			$this->success("审核成功!");
		} else {
			$this->error("审核失败,请重试");
		}
	}
    public function deleteSummary() {
        $id = \LfRequest::inNum('id');
        $res = M('e_summary')->delete($id);
        if($res) {
            /*
             * TODO
             * 删除成功后发送短信通知重新提交
             * sendMessage()
             */
            $this->success("删除成功!");
        } else {
            $this->error("删除失败,请重试");
        }
    }
    public function export() {
        $data = M('e_salon')->select();
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("GuGoo.Ltd")
            ->setLastModifiedBy("GuGoo.Ltd")
            ->setTitle("Excel数据导出")
            ->setSubject("Excel数据导出")
            ->setDescription("Excel数据导出")
            ->setKeywords("excel")
            ->setCategory("result file");
        $objPHPExcel->setActiveSheetIndex(0)
            //Excel的第A列，uid是你查出数组的键值，下面以此类推
            ->setCellValue('A1', "主题")
            ->setCellValue('B1', "分类")
            ->setCellValue('C1', "简介")
            ->setCellValue('D1', "活动时间")
            ->setCellValue('E1', "已参与人数")
            ->setCellValue('F1', "参与人数")
            ->setCellValue('G1', "点击量")
            ->setCellValue('H1', "发起人")
            ->setCellValue('I1', "参与人");
        foreach($data as $k => $v){
            $num=$k+2;
            $publishUser = M('e_user')->find($v['publish_userid']);
            $participateUsers = M('e_participate')->where('e_id='.$v['id'])->select();
            $participate = "";
            foreach($participateUsers as $u) {
                $participate .= M('e_user')->find($u['user_id'])['student_name']."/";
            }
            $objPHPExcel->setActiveSheetIndex(0)
                //Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, $v['title'])
                ->setCellValue('B'.$num, $v['type'])
                ->setCellValue('C'.$num, $v['brief'])
                ->setCellValue('D'.$num, date("Y-m-d H:i",$v['start_date']).'-'.date("H:i",$v['end_date']))
                ->setCellValue('E'.$num, $v['participated_number'])
                ->setCellValue('F'.$num, $v['participate_number'])
                ->setCellValue('G'.$num, $v['hits'])
                ->setCellValue('H'.$num, $publishUser['student_name'])
                ->setCellValue('I'.$num, $participate);
        }
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="export.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}
