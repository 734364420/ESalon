<?php

namespace Addons\Auth\Controller;
use Home\Controller\AddonsController;

class AuthController extends AddonsController{
    function __construct() {
        parent::__construct();
        $this->model = M('Model')->getByName('e_user');
        $this->assign ( 'model', $this->model );
    }
    function Auth() {
        if(IS_POST) {
            $user = M('e_user');
            $user->student_id = I('student_id');
            $user->student_name = I('student_name');
            $user->major = I('major');
            $user->phone = I('phone');
            $user->email = I('email');
            $user->gender = I('gender');
            $user->school = I('school');
            $user->student_status = I('student_status');
            $user->good = I('good');
            $user->openid = get_openid();
            if(empty($user->student_id)) {
                $this->error("请输入学号");
            }
            if(empty($user->student_name)) {
                $this->error("请输入姓名");
            }
            $user->add();
            $this->success("认证成功",addons_url('Salon://Salon/instruction'));
        } else {
            $user = '';
            $user['student_id'] = '';
            $user['student_name'] = '';
            $user['major'] = '';
            $user['phone'] = '';
            $user['email'] = '';
            $user['gender'] = '';
            $user['school'] = '';
            $user['student_status'] = '';
            $user['good'] = '';
            $this->assign('title',"用户认证");
            $this->assign('user',$user);
            $this->display();
        }
    }
    function UserProfile() {
        $user_id = I('uid');
        $user = M('user_id')->find($user_id);
        if(empty($user)) {
            $this->error("该用户不存在");
        }
        $this->assign('user',$user);
        $this->display();
    }
    function EditProfile() {
        if(IS_POST) {
            $user_id = session('user_id');
            $user = M('e_user');
            $user->student_id = I('student_id');
            $user->student_name = I('student_name');
            $user->major = I('major');
            $user->phone = I('phone');
            $user->email = I('email');
            $user->gender = I('gender');
            $user->school = I('school');
            $user->student_status = I('student_status');
            $user->good = I('good');
            $res = $user->where('id = '.$user_id)->save();
	        if($res) {
		        $this->success("修改成功");
	        } else {
		        $this->error("修改失败");
	        }
        } else {
            $user_id = session('user_id');
            $user = M('e_user')->find($user_id);
            $this->assign('user',$user);
            $this->display('Auth/Auth');
        }

    }
    //list列表，已认证学生列表
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
