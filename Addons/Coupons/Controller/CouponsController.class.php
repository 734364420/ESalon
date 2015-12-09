<?php

namespace Addons\Coupons\Controller;
use Home\Controller\AddonsController;

class CouponsController extends AddonsController{
    public function __construct()
    {
        parent::__construct();
        $this->model = M('Model')->getByName('coupons');
        $this->assign ( 'model', $this->model );
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
        $password = \LfRequest::inStr('password');
        $hash = 'UESTC_ESALON_80';
        if(md5($password)!= md5($hash)) {
            $this->error("密码错误,请重试!");
            exit();
        }
        $coupon = M('coupons')->find($id);
        if($coupon['user_id']!=$uid) $this->error('代金劵信息不符合,请确认');
        if($coupon['salon_id']!=$sid) $this->error('代金劵信息不符合,请确认');
        if($coupon['is_used']) $this->error('代金劵已经使用!');
        $data['is_used'] = 1;
        $data['used_at'] = time();
        $data['used_where'] = $address;
        $res = M('coupons')->where(['id'=>$id])->save($data);
        if($res) $this->success("使用成功",addons_url('Coupons://Coupons/showCoupon',array('code'=>$coupon['code'])));

    }
    //竞赛列表
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

        foreach($data as $i=>$d) {
            $data[$i]['user_id'] = M('e_user')->find($d['user_id'])['student_name'];
            $data[$i]['created_at'] = date("Y-m-d H:i",$d['created_at']);
            $data[$i]['used_at'] = date("Y-m-d H:i",$d['used_at']);
            $data[$i]['is_used'] = $d['is_used']?"是":"否";
        }
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
    public function edit() {
        // 获取模型信息
        $id = I ( 'id', 0, 'intval' );

        if (IS_POST) {
            $_POST ['mTime'] = time ();

            $Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
            // 获取模型的字段信息
            $Model = $this->checkAttr ( $Model, $this->model ['id'] );
            if ($Model->create () && $Model->save ()) {
                // 增加选项
                D ( 'Addons://Vote/VoteOption' )->set ( I ( 'post.id' ), I ( 'post.' ) );

                // 保存关键词
                D ( 'Common/Keyword' )->set ( I ( 'post.keyword' ), 'Vote', I ( 'post.id' ) );

                $this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'] ) );
            } else {
                $this->error ( $Model->getError () );
            }
        } else {
            $fields = get_model_attribute ( $this->model ['id'] );

            // 获取数据
            $data = M ( get_table_name ( $this->model ['id'] ) )->find ( $id );
            $data || $this->error ( '数据不存在！' );

            $token = get_token ();
            if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
                $this->error ( '非法访问！' );
            }

            $option_list = M ( 'vote_option' )->where ( 'vote_id=' . $id )->order ( '`order` asc' )->select ();
            $this->assign ( 'option_list', $option_list );

            $this->assign ( 'fields', $fields );
            $this->assign ( 'data', $data );
            $this->meta_title = '编辑' . $this->model ['title'];
            $this->display ();
        }
    }
    public function add() {
        if (IS_POST) {
            // 自动补充token
            $_POST ['token'] = get_token ();
            $Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
            // 获取模型的字段信息
            $Model = $this->checkAttr ( $Model, $this->model ['id'] );
            if ($Model->create () && $vote_id = $Model->add ()) {
                // 增加选项
                D ( 'Addons://Vote/VoteOption' )->set ( $vote_id, I ( 'post.' ) );

                // 保存关键词
                D ( 'Common/Keyword' )->set ( I ( 'keyword' ), 'Vote', $vote_id );

                $this->success ( '添加' . $this->model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'] ) );
            } else {
                $this->error ( $Model->getError () );
            }
        } else {

            $vote_fields = get_model_attribute ( $this->model ['id'] );
            $this->assign ( 'fields', $vote_fields );
            // 选项表
            $option_fields = get_model_attribute ( $this->option ['id'] );
            $this->assign ( 'option_fields', $option_fields );

            $this->meta_title = '新增' . $this->model ['title'];
            $this->display ();
        }
    }
}
