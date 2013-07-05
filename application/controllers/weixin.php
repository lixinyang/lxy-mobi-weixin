<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weixin extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url','html','form'));
		$this->load->library(array('Weixinutil'));
	}

	/**
	 */
	public function index()
	{
		echo 'hi weixin';
		//$this->load->library('Weixinools');
		$this->weixinutil->parse('weixiao', $_GET, $GLOBALS["HTTP_RAW_POST_DATA"]);
		$tools = $this->weixinutil;
		switch ($tools->request_type) {
			case WeixinTools::TYPE_NEW_MESSAGE:
				$keyword = $tools->message->content;
					//echo $tools->reply_text('笑友，你说: '.$keyword.'?');
					echo $tools->reply_article('微笑网公益购物','微笑网公益购物是一个有亲朋好友发起的民间公益项目，weixiao001.com 购物同时做公益', 'http://weixiao001.com/img/focus01.png', 'http://weixiao001.com/?wxid='.$tools->message->from_username);
				break;
			
			case WeixinTools::TYPE_SUBSCRIBE:
				echo $tools->reply_article('微笑网公益购物','微笑网公益购物是一个有亲朋好友发起的民间公益项目，weixiao001.com 购物同时做公益', 'http://weixiao001.com/img/focus01.png', 'http://weixiao001.com/?wxid='.$tools->message->from_username);
				break;
			
			case WeixinTools::TYPE_UNSUBSCRIBE:
				weixin_log($tools->message->from_username.' unsubscribed.');
				break;
		
			case WeixinTools::TYPE_VALIDATE_URL:
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
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */