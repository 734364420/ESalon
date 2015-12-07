<?php

namespace Addons\Coupons\Model;
use Think\Model;

/**
 * Coupons模型
 */
class CouponsModel extends Model{
    public function get($id=0,$where=array()){
        if($id) {
            return M('coupons')->find($id);
        }
        if(empty($where)) return M('coupons')->select();
        return M('coupons')->where($where)->select();
    }
    /*
     * uid 用户id
     * sid 沙龙id
     * money 代金劵金额
     */
    public function create($uid,$sid,$money=10) {
        $coupons = M('coupons');
        $coupons->user_id = $uid;
        $coupons->salon_id = $sid;
        $coupons->code = md5(uniqid().microtime());
        $coupons->created_at = time();
        $coupons->is_used = 0;
        $coupons->money = $money;
        return $coupons->create();
    }
    public function useCoupons($cid,$address) {
        $coupon = M('coupons');
        $coupon->used_at = time();
        $coupon->used_where = $address;
        return $coupon->where('id='.$cid)->save();
    }
    public function delete($cid)
    {
        $coupon = M('coupons');
        return $coupon->delete($cid);
    }
}
