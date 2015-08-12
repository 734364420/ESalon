<?php

namespace Addons\e_salon;
use Common\Controller\Addon;

/**
 * 成电沙龙插件
 * @author 异格科技
 */

    class e_salonAddon extends Addon{

        public $info = array(
            'name'=>'e_salon',
            'title'=>'成电沙龙',
            'description'=>'这是一个临时描述',
            'status'=>1,
            'author'=>'异格科技',
            'version'=>'1.0',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/e_salon/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/e_salon/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }