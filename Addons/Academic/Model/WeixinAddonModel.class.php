<?php
        	
namespace Addons\Academic\Model;
use Home\Model\WeixinModel;
        	
/**
 * Academic的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Academic' ); // 获取后台插件的配置参数	
        $param ['token'] = get_token ();
        $param ['openid'] = get_openid ();
		//初始化查找条件，56,57，。。。60分别为E学术几个固定的图文项
		$map_news['id'] = array('in',array(56,57,58,59,60));
		$list = M ( 'custom_reply_news' )->where ( $map_news )->select ();
		foreach ( $list as $k => $info ) {
			if ($k > 8)
				continue;

			$articles [] = array (
				'Title' => $info ['title'],
				'Description' => $info ['intro'],
				'PicUrl' => get_cover_url ( $info ['cover'] ),
				'Url' => addons_url ($info['jump_url'], $param )
			);
		}
        $res = $this->replyNews ( $articles );
	} 

	// 关注公众号事件
	public function subscribe() {
		return true;
	}
	
	// 取消关注公众号事件
	public function unsubscribe() {
		return true;
	}
	
	// 扫描带参数二维码事件
	public function scan() {
		return true;
	}
	
	// 上报地理位置事件
	public function location() {
		return true;
	}
	
	// 自定义菜单事件
	public function click() {
		return true;
	}	
}
        	
