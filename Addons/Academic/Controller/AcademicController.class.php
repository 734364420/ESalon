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
			    $maps .= ' type = '.$data['type'];
		    }
		    if($data['iteam_status'] == 0) {
			    $maps .= ' end_date > '.date("Y-m-d");
		    }
		    if($data['iteam_status'] == 1) {
			    $maps .= ' end_date < '.date("Y-m-d");
		    }
		    if($data['summary_status'] != '') {
			    $maps .= ' summary_status = '.$data['summary_status'];
		    }
	    }
	    $PublishIteams = M('e_iteam')->where($maps)->select();
	    $ParticipateIteams = M('e_iteam')->where($maps)->join('eagerfor_e_participate on eagerfor_e_iteam.id = eagerfor_e_participate.e_id')->select();
	    var_dump($maps);
        $this->assign('PublishIteams',$PublishIteams);
        $this->assign('ParticipateIteams',$ParticipateIteams);
        $this->display();
    }
    //团队约详情页面
    function IteamDetail() {
        $item_id = intval(I('id'));
        $item = M('e_iteam')->find($item_id);
        $this->assign('iteam',$item);
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
            $iteam->start_date = I('start_date');
            $iteam->end_date = I('end_date');
            $iteam->participate_number = I('participate_number');
            $iteam->type = I('type');
            $iteam->brief = I('brief');
            $iteam->publish_userid = session('user_id');
            $res = $iteam->add();
	        if($res) {
		        $this->success("添加成功",addons_url('Academic://Academic/MyIteam'));
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
        $participate->save();
    }
}
