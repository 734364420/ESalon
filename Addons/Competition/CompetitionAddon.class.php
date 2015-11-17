<?php

namespace Addons\Competition;
use Common\Controller\Addon;

/**
 * 竞赛列表插件
 * @author Hivekay
 */

    class CompetitionAddon extends Addon{

        public $info = array(
            'name'=>'Competition',
            'title'=>'竞赛列表',
            'description'=>'竞赛列表',
            'status'=>1,
            'author'=>'Hivekay',
            'version'=>'1.0',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Competition/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Competition/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }