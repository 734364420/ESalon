<?php

namespace Addons\Auth\Controller;

use Addons\Credit\Model\CreditModel;
use Home\Controller\AddonsController;
use Overtrue\Wechat\Notice;

class AuthController extends AddonsController
{
    function __construct()
    {
        parent::__construct();
        $this->model = M('Model')->getByName('e_user');
        $this->assign('model', $this->model);
    }

    function Auth()
    {
        if (IS_POST) {
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
            if (empty($user->student_id)) {
                $this->error("请输入学号");
            }
            if (empty($user->student_name)) {
                $this->error("请输入姓名");
            }
            $url = "http://222.197.183.98:45003/Xscxjk/xscxjkAction.action?xh=" . $user->student_id . "&xm=" . $user->student_name;
            $result = json_decode(https_request($url));
            if($result['result']!="success") {
                $this->error("姓名或学号错误,请重新认证!");
                exit();
            }
            $isAuth = M('e_user')->where('openid = ' . get_openid())->find();
            if (!empty($isAuth)) {
                $this->error("不能重复认证");
            }
            $id = $user->add();
            $credit = new CreditModel();
            $credit->createCredit($id);
            $this->success("认证成功", addons_url('Salon://Salon/Instruction'));
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
            $this->assign('title', "用户认证");
            $this->assign('user', $user);
            $this->display();
        }
    }

    function UserProfile()
    {
        $user_id = I('id');
        $user = M('e_user')->find($user_id);
        if (empty($user)) {
            $this->error("该用户不存在");
        }
        $this->assign('user', $user);
        $this->title = "个人资料";
        $this->display();
    }

    function EditProfile()
    {
        $this->title = "编辑个人资料";
        if (IS_POST) {
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
            $res = $user->where('id = ' . session('user_id'))->save();
            if ($res) {
                $this->success("修改成功");
            } else {
                $this->error("修改失败");
            }
        } else {
            $user_id = session('user_id');
            $user = M('e_user')->find($user_id);
            $this->assign('user', $user);
            $this->display('Auth/Auth');
        }

    }

    /*
     * 显示当前活动代金劵
     */
    public function myCoupon()
    {
        $sid = \LfRequest::inNum('id');
        $uid = session('user_id');
        $coupon = M('coupons')->where(['salon_id' => $sid, 'user_id' => $uid])->find();
        if (!$coupon) {
            $this->error("您还没有代金劵");
            exit();
        }
        $user = M('e_user')->find($coupon['user_id']);
        $salon = M('e_salon')->find($coupon['salon_id']);
        if (!$user || !$salon) $this->error("该代金劵信息有误,请核实!");
        $this->assign('coupon', $coupon);
        $this->assign('user', $user);
        $this->assign('salon', $salon);
        $this->assign('url', addons_url('Coupons://Coupons/showCoupon', ['code' => $coupon['code']]));
        $this->assign("coupon", $coupon);
        $this->display();
    }


    //list列表，已认证学生列表
    function lists()
    {
        $users = M('e_user');
        $page = I('p', 1, 'intval'); // 默认显示第一页数据

        // 解析列表规则
        $list_data = $this->_list_grid($this->model);
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];

        // 关键字搜索
        $map ['token'] = get_token();
        $key = $this->model ['search_key'] ? $this->model ['search_key'] : 'title';
        if (isset ($_REQUEST [$key])) {
            $map [$key] = array(
                'like',
                '%' . htmlspecialchars($_REQUEST [$key]) . '%'
            );
            unset ($_REQUEST [$key]);
        }
        // 条件搜索
        foreach ($_REQUEST as $name => $val) {
            if (in_array($name, $fields)) {
                $map [$name] = $val;
            }
        }
        $row = empty ($this->model ['list_row']) ? 20 : $this->model ['list_row'];

        // 读取模型数据列表

        empty ($fields) || in_array('id', $fields) || array_push($fields, 'id');
        $name = parse_name(get_table_name($this->model ['id']), true);
        $data = M($name)->field(empty ($fields) ? true : $fields)->where($map)->order('id DESC')->page($page, $row)->select();

        /* 查询记录总数 */
        $count = M($name)->where($map)->count();

        // 分页
        if ($count > $row) {
            $page = new \Think\Page ($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }

        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->display();
    }

    public function del()
    {
        $ids = I('id', 0);
        if (empty ($ids)) {
            $ids = array_unique(( array )I('ids', 0));
        }
        if (empty ($ids)) {
            $this->error('请选择要操作的数据!');
        }

        $Model = M(get_table_name($this->model ['id']));
        $map = array(
            'id' => array(
                'in',
                $ids
            )
        );
        $map ['token'] = get_token();
        $data_iteam = M('e_iteam')->where('publish_userid = ' . $_GET['id'])->select();
        $data_salon = M('e_salon')->where('publish_userid = ' . $_GET['id'])->select();
        $data_sign = M('e_participate')->where('user_id = ' . $_GET['id'])->select();
        if (!empty($data_iteam) || !empty($data_salon) || !empty($data_sign)) {
            $this->error("该用户下还有数据，请先删除数据");
        }
        if ($Model->where($map)->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    function show()
    {
        $id = I('id');
        $data = M('e_user')->where('id=' . $id)->find();
        $this->assign('data', $data);
        $this->display();
    }

    public function export()
    {
        $data = M('e_user')->select();
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("GuGoo.Ltd")
            ->setLastModifiedBy("GuGoo.Ltd")
            ->setTitle("Excel数据导出")
            ->setSubject("Excel数据导出")
            ->setDescription("Excel数据导出")
            ->setKeywords("excel")
            ->setCategory("result file");
        $objPHPExcel->setActiveSheetIndex(0)
            //Excel的第A列，uid是你查出数组的键值，下面以此类推
            ->setCellValue('A1', "姓名")
            ->setCellValue('B1', "学号")
            ->setCellValue('C1', "专业")
            ->setCellValue('D1', "学历")
            ->setCellValue('E1', "性别")
            ->setCellValue('F1', "联系电话")
            ->setCellValue('G1', "邮箱");
        foreach ($data as $k => $v) {
            $num = $k + 2;
            $objPHPExcel->setActiveSheetIndex(0)
                //Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A' . $num, $v['student_name'])
                ->setCellValue('B' . $num, $v['student_id'])
                ->setCellValue('C' . $num, $v['major'])
                ->setCellValue('D' . $num, $v['student_status'])
                ->setCellValue('E' . $num, $v['gender'])
                ->setCellValue('F' . $num, $v['phone'])
                ->setCellValue('G' . $num, $v['email']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="export.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}
