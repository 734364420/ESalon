<?php

namespace Addons\Coupons\Controller;
use Home\Controller\AddonsController;

class CouponsController extends AddonsController{
    public function __construct()
    {
        parent::__construct();
        $this->model = M('Model')->getByName('coupons');
    }
    public function showCoupon() {
        $code = \LfRequest::inStr('code');
        $coupon = M('coupons')->where(['code'=>$code])->find();
        if(!$coupon) $this->error("该代金劵不存在!");
        $user = M('e_user')->find($coupon['user_id']);
        $salon = M('e_salon')->find($coupon['salon_id']);
        if(!$user || !$salon) $this->error("该代金劵信息有误,请核实!");
        $this->assign('coupon',$coupon);
        $this->assign('user',$user);
        $this->assign('salon',$salon);
        $this->assign('url',addons_url('Coupons://Coupons/useCoupon'));
        /*u
         * 再页面中判断是否已使用
         * 如果已使用,则不显示使用代金劵的按钮
         */
        $this->display();
    }
    public function useCoupon() {
        $id = \LfRequest::inNum('id');
        $uid = \LfRequest::inNum('user_id');
        $sid = \LfRequest::inNum('salon_id');
        $address = \LfRequest::inStr('address');
        $coupon = M('coupons')->find($id);
        if($coupon['user_id']!=$uid) $this->error('代金劵信息不符合,请确认');
        if($coupon['salon']!=$sid) $this->error('代金劵信息不符合,请确认');
        if($coupon['is_used']) $this->error('代金劵已经使用!');
        $data['is_used'] = 1;
        $data['used_at'] = time();
        $data['used_where'] = $address;
        $res = M('coupons')->where(['id'=>$id])->save($data);
        if($res) $this->redirect(addons_url('Coupons://Coupons/showCoupon'),['code'=>$coupon['code']]);

    }
}
