<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weixin extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url','html','form'));
		$this->load->library(array('weixinutil'));
		$this->load->model('reportdao');
	}

	/**
	 */
	public function index()
	{
		//echo 'hi weixin';
		$this->weixinutil->parse('weixiao', $_GET, isset($GLOBALS["HTTP_RAW_POST_DATA"])?$GLOBALS["HTTP_RAW_POST_DATA"]:"");
		//$this->weixinutil->mock('weixiao');
		$tools = $this->weixinutil;
		//$tools->message->content = '报修';
		switch ($tools->request_type) {
			case Weixinutil::TYPE_NEW_MESSAGE:
				if($tools->message->msg_type == Message::TYPE_IMAGE) {
					echo $this->handle_pic_upload($tools);
				}
				else {
					$keyword = $tools->message->content;
					if (in_array($keyword, array('help','/h','/?','?','/help','？','/？'))) {
						echo $this->show_help($tools);
					}
					elseif (in_array($keyword, array('报故障','故障','保障','报修','报障'))) {
						echo $this->handle_bug_report($tools);
					}
					else {
						echo $tools->reply_text('不识别你的输入：“'.$keyword.'”，请输入“?”获得帮助');
					}
				}
				break;
			
			case Weixinutil::TYPE_SUBSCRIBE:
				echo $tools->reply_article('微笑网公益购物','微笑网公益购物是一个有亲朋好友发起的民间公益项目，weixiao001.com 购物同时做公益', 'http://weixiao001.com/img/focus01.png', 'http://weixiao001.com/?wxid='.$tools->message->from_username);
				break;
			
			case Weixinutil::TYPE_UNSUBSCRIBE:
				weixin_log($tools->message->from_username.' unsubscribed.');
				break;
		
			case Weixinutil::TYPE_VALIDATE_URL:
				echo $tools->reply_valid();
				break;
				
			default:
				echo 'Error Request, Info:<br/><br/>';
				foreach($_REQUEST as $k=>$v){
					echo $k.' : '.$v.'<br/><br/>';
				}
				weixin_log('Oops! '.$tools->request_type);
				break;
		}
	}

	/**
	 * 显示帮助
	 */
	function show_help($tools) {
		return $tools->reply_text('帮助：\n 1.输入help,?,/h,/? 获得本帮助\n 2.输入“报修”进入友宝故障报告平台');
	}
	
	/**
	 * 处理用户给微信传图片
	 */
	function handle_pic_upload($tools) {
		//echo 'here:'.$tools->message->from_username.', '.$tools->message->pic_url;
		$this->reportdao->album_add($tools->message->from_username.'', $tools->message->pic_url);
		$reply = $tools->reply_article();
		$reply->add_article('图片上传成功','', 'http://weixiao001.com/img/focus01.png', 'http://lxy.mobi/lxy-mobi-weixin/report/album/'.$tools->message->from_username);
		$reply->add_article('我的图库','', 'http://lxy.mobi/favicon.ico', 'http://lxy.mobi/lxy-mobi-weixin/report/album/'.$tools->message->from_username);
		return $reply->get_reply_string();
	}
	
	/**
	 * 友宝故障报告平台的入口
	 */
	function handle_bug_report($tools) {
		$reply = $tools->reply_article();
		$reply->add_article('友宝故障报告平台','', 'http://weixiao001.com/img/focus01.png', 'http://lxy.mobi/lxy-mobi-weixin/report/index/'.$tools->message->from_username);
		$reply->add_article('报故障','', 'http://lxy.mobi/favicon.ico', 'http://lxy.mobi/lxy-mobi-weixin/report/report/'.$tools->message->from_username);
		$reply->add_article('绑定VMS账号','', 'http://lxy.mobi/favicon.ico', 'http://lxy.mobi/lxy-mobi-weixin/report/user/'.$tools->message->from_username);
		$reply->add_article('我报的故障','', 'http://lxy.mobi/favicon.ico', 'http://lxy.mobi/lxy-mobi-weixin/report/all/'.$tools->message->from_username);
		$reply->add_article('我的图库','', 'http://lxy.mobi/favicon.ico', 'http://lxy.mobi/lxy-mobi-weixin/report/album/'.$tools->message->from_username);
		$reply->add_article('使用说明','', 'http://lxy.mobi/favicon.ico', 'http://lxy.mobi/lxy-mobi-weixin/report/help/'.$tools->message->from_username);
		return $reply->get_reply_string();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */