<?php

namespace Addons\Credit\Model;
use Think\Model;

/**
 * Credit模型
 */
class CreditModel extends Model{
    public function updateCredit($uid,$creditValue) {
        $credit = M('credit')->where(array('user_id'=>$uid))->find();
        $data['credit'] = $credit->credit + $creditValue;
        $data['updated_at'] = time();
        return M('credit')->where(array('id'=>$credit->id))->save($data);

    }
    public function createCredit($uid) {
        return M('credit')->add(array('user_id'=>$uid,'updated_at'=>time(),'credit'=>0));
    }
    public function getCredit($uid) {
        return M('credit')->where(array('user_id'=>$uid))->find();
    }
}
