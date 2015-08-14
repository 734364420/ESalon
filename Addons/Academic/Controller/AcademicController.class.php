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
        $news = M('e_competition')->order('id')->select();
        $this->assign('news',$news);
        $this->display();
    }
    //竞赛内容页
    function NewsDetail() {
        $competition_id = intval(I('cid'));
        $competition = M('e_competition')->find($competition_id);
        if(empty($competition)) {
            $this->error("数据不存在了！");
        }
        $competition->hits++;
        $competition->save();
        $this->assign('competition',$competition);
        $this->display();
    }
    //我的iteam页面
    function MyIteam() {
	    $maps = 'publish_userid = '.session('user_id');
	    if(IS_POST) {
		    $data['type'] = \LfRequest::inStr('type');
		    $data['iteam_status'] = \LfRequest::inStr('iteam_status');
		    $data['summary_status'] = \LfRequest::inStr('summary_status');
		    if(!empty($data['type'])) {
			    $maps .= ' AND  type = '.$data['type'];
		    }
		    if($data['iteam_status'] == 0 && $data['iteam_status'] != '' ) {
			    $maps .= ' AND  end_date > '.strtotime(date("Y-m-d"));
		    }
		    if($data['iteam_status'] == 1) {
			    $maps .= ' AND  end_date < '.strtotime(date("Y-m-d"));
		    }
		    if($data['summary_status'] != '') {
			    $maps .= '  AND  summary = '.$data['summary_status'];
		    }
	    }
	    $PublishIteams = M('e_iteam')->where($maps)->select();
	    $ParticipateIteams = M('e_iteam')->where($maps)->join('eagerfor_e_participate on eagerfor_e_participate.user_id = '.session('user_id').' AND eagerfor_e_iteam.id = eagerfor_e_participate.e_id')->select();
	    $this->assign('type',I('type',''));
	    $this->assign('iteam_status',I('iteam_status',''));
	    $this->assign('summary_status',I('summary_status',''));
        $this->assign('PublishIteams',$PublishIteams);
        $this->assign('ParticipateIteams',$ParticipateIteams);
        $this->display();
    }
    //团队约详情页面
    function IteamDetail() {
        $iteam_id = intval(I('id'));
        $Miteam = M('e_iteam');
	    $iteam = $Miteam->find($iteam_id);
	    $Miteam->hits = $iteam->hits++;
	    $Miteam->where('id = '.$iteam_id)->save();
	    $this->user = M('e_user')->find($iteam->publish_userid);
	    $this->participate_users = M('e_participate')->where('e_id = '.$iteam_id)->select();
        $this->assign('iteam',$iteam);
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
            $iteam->start_date = strtotime(I('start_date'));
            $iteam->end_date = strtotime(I('end_date'));
            $iteam->participate_number = I('participate_number');
            $iteam->type = I('type');
            $iteam->brief = I('brief');
            $iteam->publish_userid = session('user_id');
	        $iteam->participated_number = 1;
            $res = $iteam->add();
	        if($res) {
		        $this->success("添加成功",addons_url('Academic://Academic/MyIteam'));
		        $participate = M('e_participate');
		        $participate->e_id = $res;
		        $participate->user_id = session('user_id');
		        $participate->add();
	        } else {
		        $this->error("添加失败");
	        }
        } else {
            $this->display();
        }
    }
    //Iteam广场
    function Square() {
	    $sign_iteams = M('e_iteam')->where('start_date > '.date("Y-m-d"))->select();
	    $end_iteams = M('e_iteam')->where('start_date < '.date("Y-m-d"))->select();
	    if(IS_POST) {
		    $data['type'] = \LfRequest::inStr('type');
		    $data['start_date'] = \LfRequest::inStr('start_date');
		    $data['end_date'] = \LfRequest::inStr('end_date');
		    $maps = '';
		    if(!empty($data['type'])) {
				$maps .= 'type = '.$data['type'];
		    }
		    if(!empty($data['start_date'])) {
			    $maps .= 'start_date = '.$data['start_date'];
		    }
		    if(!empty($data['end_date'])) {
			    $maps .= 'end_date = '.$data['end_date'];
		    }
	    }
	    $this->assign('sign_iteams',$sign_iteams);
	    $this->assign('end_iteams',$end_iteams);
        $this->display();
    }
    //报名微团队
    function SignIteam() {
        $iteam_id = I('id');
        $iteam = M('e_iteam')->find($iteam_id);
        if(empty($iteam)) {
            $this->error('你要报名的iteam不存在');
        }
        $iteam->participated_number++;
        $iteam->save();
        $participate = M('e_participate');
        $participate->e_id = $iteam_id;
        $participate->user_id = session('user_id');
        $res = $participate->save();
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
                $this->success('总结成功');
            } else {
                $this->error('总结失败');
            }
        } else {
            $id = \LfRequest::inNum('id');
            $iteam = M('e_iteam')->find($id);
            $this->assign('iteam',$iteam);
            $this->display();
        }
    }
}
