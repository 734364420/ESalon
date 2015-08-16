<?php

namespace Addons\Academic\Controller;
use Home\Controller\AddonsController;

class AcademicController extends AddonsController{
	public function __construct() {
		parent::__construct();
		$this->model = M('Model')->getByName('e_iteam');
		$this->assign ( 'model', $this->model );
	}
    //最新学术动态页面，竞赛列表页
    function LastNews() {
	    e_auth();
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
        $count = M('e_competition')->where($maps)->order('id DESC')->count();
		$page = \LfPageData::Page($count,addons_url('Academic://Academic/LastNews'));
        $news = M('e_competition')->where($maps)->limit($page['offset'],$page['perpagenum'] )->order('id DESC')->select();
        $this->assign('news',$news);
	    $this->assign('page',$page);
	    $this->title = "最新竞赛动态";
        $this->display();
    }
    //竞赛内容页
    function NewsDetail() {
	    e_auth();
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
	    e_auth();
//		$run = new \LfRunTime();
//		$run->star();
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
	    $Pmaps = $maps.' id in '.$in.' AND publish_userid  != '.session('user_id');
	    $maps .= ' publish_userid = '.session('user_id');
	    $PublishIteamsCount = M('e_iteam')->where($maps)->count();
	    $ParticipateIteamsCount= M('e_iteam')->where($Pmaps)->count();
		$PublishPage = \LfPageData::Page($PublishIteamsCount,addons_url('Academic://Academic/MyIteam/status/sign'));
		$ParticipatePage = \LfPageData::Page($ParticipateIteamsCount,addons_url('Academic://Academic/MyIteam/status/end'));

	    $PublishIteams = M('e_iteam')->where($maps)->limit($PublishPage['offset'],$PublishPage['perpagenum'])->select();
	    $ParticipateIteams= M('e_iteam')->where($Pmaps)->limit($ParticipatePage['offset'],$ParticipatePage['perpagenum'])->select();
	    $user = M('e_user')->find(session('user_id'));
	    $this->assign('user',$user);
	    $this->assign('type',I('type',''));
	    $this->assign('iteam_status',I('iteam_status',''));
	    $this->assign('summary_status',I('summary_status',''));
        $this->assign('PublishIteams',$PublishIteams);
        $this->assign('ParticipateIteams',$ParticipateIteams);
        $this->assign('url','Academic://Academic/IteamDetail');
	    $this->assign('PublishPage',$PublishPage);
	    $this->assign('ParticipatePage',$ParticipatePage);
	    $this->title = "我的Iteam";
//		var_dump($run->spent());
        $this->display();
    }
    //团队约详情页面
    function IteamDetail() {
	    e_auth();
        $iteam_id = intval(I('id'));
	    $iteam = M('e_iteam')->find($iteam_id);
	    M('e_iteam')->where('id = '.$iteam_id)->save(array('hits'=>$iteam['hits']+1));
	    $user = M('e_user')->find($iteam['publish_userid']);
	    $this->assign('user',$user);
	    $participate_users = M('e_participate')->where('e_id = '.$iteam_id)->select();
	    $this->assign('participate_users',$participate_users);
        $this->assign('iteam',$iteam);
		$this->assign('sign_url','Academic://Academic/SignIteam');
		$this->title = "Iteam详情";
        $this->display();
    }
    //发起团队约,填写表单页面
    function Publish() {
	    e_auth();
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
	    e_auth();
	    $sign_maps = 'end_date > '.strtotime(date("Y-m-d"));
	    $end_maps = 'end_date < '.strtotime(date("Y-m-d"));
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
			    switch($data['date']) {
				    //今天
				    case 1 :
					    $maps .= 'start_date = '.strtotime(date("Y-m-d")).$and;
					    break;
				    //明天
				    case 2 :
					    $maps .= 'start_date = '.strtotime(date("Y-m-d",time()+1*24*3600)).$and;
					    break;
				    //三天后
				    case 3 :
					    $maps .= 'start_date >= '.strtotime(date("Y-m-d",time()+3*24*3600)).$and;
					    break;
				    //昨天
				    case -1 :
					    $maps .= 'start_date = '.strtotime(date("Y-m-d",time()-1*24*3600)).$and;
					    break;
				    //过去三天
				    case -3 :
					    $maps .= strtotime(date("Y-m-d",time()-3*24*3600)).' =< start_date <= '.strtotime(date("Y-m-d")).$and;
					    break;
					//过去七天
				    case -7 :
					    $maps .= strtotime(date("Y-m-d",time()-7*24*3600)).' =< start_date <= '.strtotime(date("Y-m-d")).$and;
					    break;

			        }
		    }
		    if(!empty($data['number'])) {
			    $maps .= 'participate_number = '.$data['number'].$and;
		    }
	    }
	    $sign_maps = $maps.$sign_maps;
	    $end_maps = $maps.$end_maps;
	    $SignIteamsCount = M('e_iteam')->where($sign_maps)->count();
	    $EndIteamCount = M('e_iteam')->where($end_maps)->count();
		$SignPage = \LfPageData::Page($SignIteamsCount,addons_url('Academic://Academic/Square/status/sign'));
		$EndPage = \LfPageData::Page($EndIteamCount,addons_url('Academic://Academic/Square/status/end'));


	    $sign_iteams = M('e_iteam')->where($sign_maps)->limit($SignPage['offset'],$SignPage['perpagenum'])->select();
	    $end_iteams = M('e_iteam')->where($end_maps)->limit($EndPage['offset'],$EndPage['perpagenum'])->select();
	    $this->type = I('type','');
	    $this->date = I('date','');
	    $this->number = I('number','');
	    $this->status = \LfRequest::inStr('status')?\LfRequest::inStr('status'):'sign';
	    $this->assign('sign_iteams',$sign_iteams);
	    $this->assign('end_iteams',$end_iteams);
		$this->assign('url','Academic://Academic/IteamDetail');
	    $this->assign('SignPage',$SignPage);
	    $this->assign('EndPage',$EndPage);
		$this->title = "Iteam广场";
        $this->display();
    }
    //报名微团队
    function SignIteam() {
	    e_auth();
        $iteam_id = \LfRequest::inNum('e_id');
        $iteam = M('e_iteam')->find($iteam_id);
        if(empty($iteam)) {
            $this->error('你要报名的iteam不存在');
        }
	    if($iteam['publish_userid'] == session('user_id')) {
		    $this->error('不能报名自己的iteam哦');
	    }
	    if($iteam['participate_number'] == $iteam['participated_number']) {
		    $this->error("报名人数已达上限");
	    }
	    $isParticipate = M('e_participate')->where('user_id ='.session('user_id').' AND e_id = '.$iteam_id)->find();
	    if(!empty($isParticipate)) {
		    $this->error('不能重复参加哦');
	    }
	    $participate = M('e_participate');
	    $participate->e_id = $iteam_id;
	    $participate->user_id = session('user_id');
	    $res = $participate->add();
	    M('e_iteam')->where('id = '.$iteam_id)->save(array('participated_number'=>$iteam['participated_number']+1));
	    if($res) {
            $this->success('报名成功');
        } else {
            $this->error('报名失败');
        }
    }
    //活动总结
    function Summary() {
	    e_auth();
        if(IS_POST) {
	        $e_id = \LfRequest::inNum('e_id');
            $summary = M('e_summary');
            $summary->user_id = session('user_id');
            $summary->e_id = $e_id;
            $summary->stars = \LfRequest::inNum('stars');
            $summary->comment = \LfRequest::inStr('comment');
            $summary->picture = \LfRequest::inStr('picture');
            $res = $summary->add();
            if($res) {
	            /*
	             * 当所有人都总结后设置iteam为已总结
	            $SummaryNumber = $summary->where('e_id = '.$e_id)->count();
	            $iteam = M('e_iteam')->find($e_id);
	            if($iteam['participated_number'] == $SummaryNumber) {
		            M('e_iteam')->where('id ='.$e_id)->save(array('summary'=>1));
	            }
	            */
	            /*
	             * 暂定为有一人总结则该iteam为已总结
	             */
	            M('e_iteam')->where('id ='.$e_id)->save(array('summary'=>1));
	            $this->success('总结成功',addons_url('Academic://Academic/IteamDetail',array('id'=>$e_id)));
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
