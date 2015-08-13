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
            $user->save();
            $this->success("认证成功");
        } else {
            $this->display();
        }
    }
}
