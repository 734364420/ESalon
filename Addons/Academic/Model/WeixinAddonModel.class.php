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
  $url = addons_url ( 'Suggestions://Suggestions/suggest', $param );
  $articles [0] = array (
    'Title' => '建议意见',
    'Description' => '请点击进入填写反馈内容',
    'PicUrl' => 'http://weiphp.cn/Public/Home/images/about/logo.jpg',
    'Url' => $url
  );
 
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
        	
