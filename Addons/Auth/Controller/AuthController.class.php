<?php

namespace Addons\Auth\Controller;
use Home\Controller\AddonsController;

class AuthController extends AddonsController{
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
            $this->success("认证成功");
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
        $user_id = I('user_id');
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
            $user = M('e_user')->find($user_id);
//            $user->student_id = I('student_id');
//            $user->student_name = I('student_name');
            $user->major = I('major');
            $user->phone = I('phone');
            $user->email = I('email');
//            $user->gender = I('gender');
            $user->school = I('school');
            $user->student_status = I('student_status');
            $user->good = I('good');
            $user->save();
        } else {
            $user_id = session('user_id');
            $user = M('e_user')->find($user_id);
            $this->assign('user',$user);
            $this->display('Auth/Auth');
        }

    }
}
