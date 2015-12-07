<?php

namespace Addons\IteamType;
use Common\Controller\Addon;

/**
 * 学术分类插件
 * @author 无名
 */

    class IteamTypeAddon extends Addon{

        public $info = array(
            'name'=>'IteamType',
            'title'=>'学术分类',
            'description'=>'这是一个临时描述',
            'status'=>1,
            'author'=>'无名',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/IteamType/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/IteamType/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }