<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weixin extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url','html','form'));
		$this->load->library(array('weixinutil'));
	}

	/**
	 */
	public function index()
	{
		//echo 'hi weixin';
		$this->weixinutil->parse('weixiao', $_GET, isset($GLOBALS["HTTP_RAW_POST_DATA"])?$GLOBALS["HTTP_RAW_POST_DATA"]:"");
		//$this->weixinutil->mock('weixiao');
		$tools = $this->weixinutil;
		//echo print_r($tools);
		switch ($tools->request_type) {
			case Weixinutil::TYPE_NEW_MESSAGE:
				$keyword = $tools->message->content;
				if (in_array($keyword, array('help','/h','/?','?','/help')))
					echo $this->show_help($tools);
				else 
					echo $this->show_help($tools);
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
		return $tools->reply_text('帮助：\n 1.输入help,?,/h,/? 获得本帮助\n 2.');
	}
	
	/**
	 * 友宝故障报告平台的入口
	 */
	function handle_bug_report($rools) {
		return $tools->reply_article('友宝故障报告平台','', 'http://weixiao001.com/img/focus01.png', 'http://weixiao001.com/?wxid='.$tools->message->from_username);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */