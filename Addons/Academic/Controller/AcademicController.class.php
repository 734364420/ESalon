<?php

namespace Addons\Academic\Controller;
use Home\Controller\AddonsController;

class AcademicController extends AddonsController{
	public function __construct() {
		parent::__construct();
        e_auth();
		$user = M('e_user')->find(session('user_id'));
		$this->assign('user',$user);
	}
    //最新学术动态页面，竞赛列表页
    function LastNews() {
	    if(IS_POST) {
		    $and = ' AND ';
		    $maps = '';
		    $type = \LfRequest::inStr('type');
		    $date = \LfRequest::inStr('date');
		    $mode = \LfRequest::inStr('mode');
		    if(!empty($type)) {
				$maps .= ' type = '.$type.$and;
		    }
		    if($date != '' && $date ==0) {
			    $maps .= ' date < '.time().$and;
		    }
		    if(!empty($date)) {
			    $maps .= time().' < date < '.strtotime($date).$and;
		    }
		    if(!empty($mode)) {
			    $maps .= ' mode = '.$mode.$and;
		    }
	    }
	    $this->type = I('type','');
	    $this->date = I('date','');
	    $this->mode = I('mode','');
        $news = M('e_competition')->where($maps)->order('id DESC')->select();
        $this->assign('news',$news);
	    $this->title = "最新竞赛动态";
        $this->display();
    }
    //竞赛内容页
    function NewsDetail() {
        $competition_id = intval(I('id'));
        $competition = M('e_competition')->find($competition_id);
        if(empty($competition)) {
            $this->error("数据不存在了！");
        }
	    M('e_competition')->where('id ='.$competition_id)->save(array('hits'=>$competition['hits']+1));
        $this->assign('competition',$competition);
	    $this->title = "竞赛详情";
        $this->display();
    }
    //我的iteam页面
    function MyIteam() {
		$maps = '';
	    if(IS_POST) {
		    $data['type'] = \LfRequest::inStr('type');
		    $data['iteam_status'] = \LfRequest::inStr('iteam_status');
		    $data['summary_status'] = \LfRequest::inStr('summary_status');
		    if(!empty($data['type'])) {
			    $maps .= '  type = '.$data['type'].' AND ';
		    }
		    if($data['iteam_status'] == 0 && $data['iteam_status'] != '' ) {
			    $maps .= '  end_date > '.strtotime(date("Y-m-d")).' AND ';
		    }
		    if($data['iteam_status'] == 1) {
			    $maps .= '  end_date < '.strtotime(date("Y-m-d")).' AND ';
		    }
		    if($data['summary_status'] != '') {
			    $maps .= '  summary = '.$data['summary_status'].' AND ';
		    }
	    }
	    $participate = M('e_participate')->where('user_id = '.session('user_id'))->select();
	    $in='(0';
	    foreach($participate as $v) {
		    $in .= ','.$v['e_id'];
	    }
	    $in .= ')';
	    $Pmaps = $maps.' id in '.$in;
	    $maps .= ' publish_userid = '.session('user_id');
	    $PublishIteams = M('e_iteam')->where($maps)->select();
	    $ParticipateIteams= M('e_iteam')->where($Pmaps)->select();
	    $this->assign('type',I('type',''));
	    $this->assign('iteam_status',I('iteam_status',''));
	    $this->assign('summary_status',I('summary_status',''));
        $this->assign('PublishIteams',$PublishIteams);
        $this->assign('ParticipateIteams',$ParticipateIteams);
	    $this->title = "我的Iteam";
        $this->display();
    }
    //团队约详情页面
    function IteamDetail() {
        $iteam_id = intval(I('id'));
	    $iteam = M('e_iteam')->find($iteam_id);
	    M('e_iteam')->where('id = '.$iteam_id)->save(array('hits'=>$iteam['hits']+1));
	    $this->user = M('e_user')->find($iteam['publish_userid']);
	    $this->participate_users = M('e_participate')->where('e_id = '.$iteam_id)->select();
        $this->assign('iteam',$iteam);
	    $this->title = "Iteam详情";
        $this->display();
    }
    //发起团队约,填写表单页面
    function Publish() {
        if(IS_POST) {
            $title = I('title');
            $iteams = M('e_iteam')->where(array('title'=>$title))->select();
            if(!empty($iteams)) {
                /*
                 * todo 提示当前主题已有团队约
                 */
            }
            $iteam = M('e_iteam');
            $iteam->title = $title;
            $iteam->start_date = strtotime(\LfRequest::inStr('start_date'));
            $iteam->end_date = strtotime(\LfRequest::inStr('end_date'));
            $iteam->participate_number = \LfRequest::inStr('participate_number');
            $iteam->type = \LfRequest::inStr('type');
            $iteam->brief = \LfRequest::inStr('brief');
            $iteam->publish_brief = \LfRequest::inStr('publish_brief');
            $iteam->publish_userid = session('user_id');
	        $iteam->participated_number = 1;
            $res = $iteam->add();
	        if($res) {
		        $participate = M('e_participate');
		        $participate->e_id = $res;
		        $participate->user_id = session('user_id');
		        $participate->add();
		        $this->success("添加成功",addons_url('Academic://Academic/MyIteam'));
	        } else {
		        $this->error("添加失败");
	        }
        } else {
	        $this->title = "发起Iteam";
            $this->display();
        }
    }
    //Iteam广场
    function Square() {
	    $sign_maps = 'end_date > '.date("Y-m-d");
	    $end_maps = 'end_date < '.date("Y-m-d");
	    $and = ' AND ';
	    $maps = '';
	    if(IS_POST) {
		    $data['type'] = \LfRequest::inStr('type');
		    $data['date'] = \LfRequest::inStr('date');
		    $data['number'] = \LfRequest::inStr('number');
		    $maps = '';
		    if(!empty($data['type'])) {
				$maps .= 'type = '.$data['type'].$and;
		    }
		    if(!empty($data['date'])) {
			    $maps .= 'start_date = '.$data['date'].$and;
		    }
		    if(!empty($data['number'])) {
			    $maps .= 'participate_number = '.$data['number'].$and;
		    }
	    }
	    $sign_maps = $maps.$sign_maps;
	    $end_maps = $maps.$end_maps;
	    $sign_iteams = M('e_iteam')->where($sign_maps)->select();
	    $end_iteams = M('e_iteam')->where($end_maps)->select();
	    $this->type = I('type','');
	    $this->date = I('date','');
	    $this->number = I('number','');
	    $this->status = I('status','sign');
	    $this->assign('sign_iteams',$sign_iteams);
	    $this->assign('end_iteams',$end_iteams);
	    $this->title = "Iteam广场";
        $this->display();
    }
    //报名微团队
    function SignIteam() {
        $iteam_id = \LfRequest::inNum('e_id');
        $iteam = M('e_iteam')->find($iteam_id);
        if(empty($iteam)) {
            $this->error('你要报名的iteam不存在');
        }
        M('e_iteam')->where('id = '.$iteam_id)->save(array('participated_number'=>$iteam['participated_number']+1));
        $participate = M('e_participate');
        $participate->e_id = $iteam_id;
        $participate->user_id = session('user_id');
        $res = $participate->add();
        if($res) {
            $this->success('报名成功');
        } else {
            $this->error('报名失败');
        }
    }
    //活动总结
    function Summary() {
        if(IS_POST) {
            $summary = M('e_summary');
            $summary->user_id = session('user_id');
            $summary->e_id = \LfRequest::inNum('e_id');
            $summary->stars = \LfRequest::inNum('stars');
            $summary->comment = \LfRequest::inStr('comment');
            $summary->picture = \LfRequest::inStr('picture');
            $res = $summary->add();
            if($res) {
                $this->success('总结成功',addons_url('Academic://Academic/IteamDetail',array('id'=>$summary->e_id)));
            } else {
                $this->error('总结失败');
            }
        } else {
            $id = \LfRequest::inNum('id');
            $iteam = M('e_iteam')->find($id);
	        $this->e_id = $id;
            $this->assign('iteam',$iteam);
	        $this->title = "活动总结";
            $this->display();
        }
    }
}
