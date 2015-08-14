<?php

namespace Addons\Academic;
use Common\Controller\Addon;

/**
 * E学术插件
 * @author 无名
 */

    class AcademicAddon extends Addon{

        public $info = array(
            'name'=>'Academic',
            'title'=>'E学术',
            'description'=>'发送E学术触发插件',
            'status'=>1,
            'author'=>'无名',
            'version'=>'1.0',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Academic/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Academic/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }