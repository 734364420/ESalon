<?php

namespace Addons\IteamType\Controller;
use Home\Controller\AddonsController;

class IteamTypeController extends AddonsController{
	function __construct() {
		parent::__construct();
		$this->model = M('Model')->getByName('e_iteam_type');
		$this->assign ( 'model', $this->model );
	}
	//�����б�
	function lists() {
		$users = M('e_user');
		$page = I ( 'p', 1, 'intval' ); // Ĭ����ʾ��һҳ����

		// �����б����
		$list_data = $this->_list_grid ( $this->model );
		$grids = $list_data ['list_grids'];
		$fields = $list_data ['fields'];

		// �ؼ�������
		$map ['token'] = get_token ();
		$key = $this->model ['search_key'] ? $this->model ['search_key'] : 'title';
		if (isset ( $_REQUEST [$key] )) {
			$map [$key] = array (
				'like',
				'%' . htmlspecialchars ( $_REQUEST [$key] ) . '%'
			);
			unset ( $_REQUEST [$key] );
		}
		// ��������
		foreach ( $_REQUEST as $name => $val ) {
			if (in_array ( $name, $fields )) {
				$map [$name] = $val;
			}
		}
		$row = empty ( $this->model ['list_row'] ) ? 20 : $this->model ['list_row'];

		// ��ȡģ�������б�

		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		$name = parse_name ( get_table_name ( $this->model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();

		/* ��ѯ��¼���� */
		$count = M ( $name )->where ( $map )->count ();

		// ��ҳ
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( '_page', $page->show () );
		}

		$this->assign ( 'list_grids', $grids );
		$this->assign ( 'list_data', $data );
		$this->display('Competition/lists');
	}
	public function del() {
		$ids = I ( 'id', 0 );
		if (empty ( $ids )) {
			$ids = array_unique ( ( array ) I ( 'ids', 0 ) );
		}
		if (empty ( $ids )) {
			$this->error ( '��ѡ��Ҫ����������!' );
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
			$this->success ( 'ɾ���ɹ�' );
		} else {
			$this->error ( 'ɾ��ʧ�ܣ�' );
		}
	}
	public function edit() {
		// ��ȡģ����Ϣ
		$id = I ( 'id', 0, 'intval' );

		if (IS_POST) {
			$_POST ['mTime'] = time ();

			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// ��ȡģ�͵��ֶ���Ϣ
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $Model->save ()) {
				// ����ѡ��
				D ( 'Addons://Vote/VoteOption' )->set ( I ( 'post.id' ), I ( 'post.' ) );

				// ����ؼ���
				D ( 'Common/Keyword' )->set ( I ( 'post.keyword' ), 'Vote', I ( 'post.id' ) );

				$this->success ( '����' . $this->model ['title'] . '�ɹ���', U ( 'lists?model=' . $this->model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $this->model ['id'] );

			// ��ȡ����
			$data = M ( get_table_name ( $this->model ['id'] ) )->find ( $id );
			$data || $this->error ( '���ݲ����ڣ�' );

			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '�Ƿ����ʣ�' );
			}

			$option_list = M ( 'vote_option' )->where ( 'vote_id=' . $id )->order ( '`order` asc' )->select ();
			$this->assign ( 'option_list', $option_list );

			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '�༭' . $this->model ['title'];
			$this->display ('Competition/edit');
		}
	}
	public function add() {
		if (IS_POST) {
			// �Զ�����token
			$_POST ['token'] = get_token ();
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// ��ȡģ�͵��ֶ���Ϣ
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $vote_id = $Model->add ()) {
				// ����ѡ��
				D ( 'Addons://Vote/VoteOption' )->set ( $vote_id, I ( 'post.' ) );

				// ����ؼ���
				D ( 'Common/Keyword' )->set ( I ( 'keyword' ), 'Vote', $vote_id );

				$this->success ( '���' . $this->model ['title'] . '�ɹ���', U ( 'lists?model=' . $this->model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {

			$vote_fields = get_model_attribute ( $this->model ['id'] );
			$this->assign ( 'fields', $vote_fields );
			// ѡ���
			$option_fields = get_model_attribute ( $this->option ['id'] );
			$this->assign ( 'option_fields', $option_fields );

			$this->meta_title = '����' . $this->model ['title'];
			$this->display ('Competition/add');
		}
	}

	public function show(){
		$id=I('id');
		$data=M('e_iteam_type')->where('id='.$id)->find();
		$data['date']=date('Y-m-d H:i:s',$data['date']);
		$this->assign('data',$data);
		$this->display();
	}
}
