<?php
function weixin_log($txt) {
	$file = '/tmp/weixin.log';
	file_put_contents($file, $txt."\r\n", FILE_APPEND);
}
/**
 * design as a tool, not a framework
 */
class Weixinutil
{
	const TYPE_NEW_MESSAGE = 'new_message';
	const TYPE_SUBSCRIBE = 'subscribe';
	const TYPE_UNSUBSCRIBE = 'unsubscribe';
	const TYPE_VALIDATE_URL = 'validate_url';
	const TYPE_SIGNATURE_ERROR = 'signature_error';
	const TYPE_UNKNOWN_ERROR = 'unknown_error';

	private $token = 'weixin';
	public $request_type = 'message';
	public $message = null;
	
	/**
	 * parse the request
	 * @param token 
	 * @param get $_GET
	 * @param raw_post $GLOBALS["HTTP_RAW_POST_DATA"]
	 */
	function parse($token='', $get='', $raw_post='')
	{
		$this->token = $token;
        //签名不合法
        if(!$this->checkSignature($get)){
        	$this->request_type = Weixinutil::TYPE_SIGNATURE_ERROR;
        }
		elseif (!empty($_GET["echostr"])) {
			$this->request_type = Weixinutil::TYPE_VALIDATE_URL;
		}
		elseif (!empty($raw_post)) {
			$this->message = new Message($raw_post);
			if($this->message->msg_type == Message::TYPE_TEXT and $this->message->content == 'Hello2BizUser') {
				$this->request_type = Weixinutil::TYPE_SUBSCRIBE;
			}
			elseif ($this->message->msg_type == Message::TYPE_EVENT and $this->message->event == 'unsubscribe') {
				$this->request_type = Weixinutil::TYPE_UNSUBSCRIBE;
			}
			elseif ($this->message->msg_type == Message::TYPE_EVENT and $this->message->event == 'subscribe') {
				$this->request_type = Weixinutil::TYPE_SUBSCRIBE;
			}
			else {
				$this->request_type = Weixinutil::TYPE_NEW_MESSAGE;
			}
		}
		else {
			$this->request_type = Weixinutil::TYPE_UNKNOWN_ERROR;
		}
		weixin_log('==========================================');
		weixin_log('token: '.$token);
		weixin_log('request type: '.$this->request_type);
		weixin_log("raw post: \n".$raw_post);
	}
	
	function mock($token) {
		$this->token = $token;
		$this->request_type = Weixinutil::TYPE_NEW_MESSAGE;
		$this->message = new Message("<xml><ToUserName><![CDATA[gh_e4a32c1fd463]]></ToUserName>
<FromUserName><![CDATA[oiSrojugJ9-RYcd8z6tCLf40UEUs]]></FromUserName>
<CreateTime>1373363549</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<PicUrl><![CDATA[http://mmsns.qpic.cn/mmsns/S5ZiaMicnbfmRP7Iiauc72eWIM3F28yDicb0bartVYicyVpFOE6Rpxib0jgQ/0]]></PicUrl>
<MsgId>5898551528473493573</MsgId>
<MediaId><![CDATA[IJq3G8D6h7IjxndJVY1xKMGoVr9sJEI-WTXZsNgMNvVw7NlKaHoDt0W4n-GQwbYh]]></MediaId>
</xml>");
		$this->message = new Message("<xml><ToUserName><![CDATA[gh_e4a32c1fd463]]></ToUserName>
<FromUserName><![CDATA[oiSrojugJ9-RYcd8z6tCLf40UEUs]]></FromUserName>
<CreateTime>1373347830</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[hi]]></Content>
<MsgId>5898484015882567728</MsgId>
</xml>");
	}

	/**
	 * http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E7.BD.91.E5.9D.80.E6.8E.A5.E5.85.A5
	 */
	public function reply_valid()
    {
        return $_GET["echostr"];
    }
	
	/**
	 * reply a text message to user.
	 * http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E6.96.87.E6.9C.AC.E6.B6.88.E6.81.AF
	 */
	public function reply_text($text)
	{
		$time = time();
        $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>0</FuncFlag>
</xml>";             
        $resultStr = sprintf($textTpl, $this->message->from_username, $this->message->to_username, $time, $text);
		weixin_log("reply_text: \n".$resultStr);
        return $resultStr;

	}
	
	/**
	 * reply a article message to user.
	 * http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E5.9B.9E.E5.A4.8D.E5.9B.BE.E6.96.87.E6.B6.88.E6.81.AF
	 */
	public function reply_article()
	{
        return new ArticleReply($this->message);
	}

	/**
	 * reply a music to user.
	 * http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E5.9B.9E.E5.A4.8D.E9.9F.B3.E4.B9.90.E6.B6.88.E6.81.AF
	 */
	public function reply_music($title, $description, $music_url, $hq_music_url)
	{
		$time = time();
        $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
 <Music>
 <Title><![CDATA[%s]]></Title>
 <Description><![CDATA[%s]]></Description>
 <MusicUrl><![CDATA[%s]]></MusicUrl>
 <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
 </Music>
<FuncFlag>0</FuncFlag>
</xml>";             
        $resultStr = sprintf($textTpl, $this->message->from_username, $this->message->to_username, $time, $title, $description, $music_url, $hq_music_url);
		weixin_log("reply_text: \n".$resultStr);
        return $resultStr;

	}
	
	/**
	 * http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E7.BD.91.E5.9D.80.E6.8E.A5.E5.85.A5
	 */
	private function checkSignature($get)
	{
        $signature = $get["signature"];
        $timestamp = $get["timestamp"];
        $nonce = $get["nonce"];	
        		
		$token = $this->token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

/**
 * http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E6.B6.88.E6.81.AF.E6.8E.A8.E9.80.81
 */
class Message{
	const TYPE_TEXT = 'text';
	const TYPE_IMAGE = 'image';
	const TYPE_LOCATION = 'location';
	const TYPE_LINK = 'link';
	const TYPE_EVENT = 'event';

	public $to_username = null;
	public $from_username = null;
	public $create_time = null;	
	public $msg_type = null;
	public $msg_id = null;
	public $content = null;
	public $pic_url = null;
	public $location_x = null;
	public $location_y = null;
	public $scale = null;
	public $label = null;
	public $title = null;
	public $description = null;
	public $url = null;
	
	function __construct($raw_post) {
        $xml = simplexml_load_string($raw_post, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->from_username = $xml->FromUserName;
        $this->to_username = $xml->ToUserName;
		$this->create_time = $xml->CreateTime;
		$this->msg_type = $xml->MsgType;
		$this->msg_id = $xml->MsgId;
		
		switch ($this->msg_type) {
			case Message::TYPE_TEXT:
				$this->content = trim($xml->Content);
				break;
			case Message::TYPE_IMAGE:
				$this->pic_url = trim($xml->PicUrl);
				break;
			case Message::TYPE_LOCATION:
				$this->location_x = trim($xml->Location_X);
				$this->location_y = trim($xml->Location_Y);
				$this->scale = trim($xml->Scale);
				$this->label = trim($xml->Label);
				break;
			case Message::TYPE_LINK:
				$this->title = trim($xml->Title);
				$this->description = trim($xml->Description);
				$this->url = trim($xml->Url);
				break;
			case Message::TYPE_EVENT:
				$this->event = trim($xml->Event);
				$this->event_key = trim($xml->EventKey);
				break;
		}
	}
}

/**
 * 
 */
class ArticleReply{
	private $the_string;
	private $article_count = 0;
	
	function __construct($input_msg) {
		$time = time();
        $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Articles>";             
        $this->the_string = sprintf($textTpl, $input_msg->from_username, $input_msg->to_username, $time);
	}
	
	function add_article($title, $description, $pic_url, $url) {
		$this->article_count++;
        $textTpl = "
 <item>
 <Title><![CDATA[%s]]></Title> 
 <Description><![CDATA[%s]]></Description>
 <PicUrl><![CDATA[%s]]></PicUrl>
 <Url><![CDATA[%s]]></Url>
 </item>
";             
        $this->the_string .= sprintf($textTpl, $title, $description, $pic_url, $url);
    }
	
	function get_reply_string() {
		$this->the_string .= " </Articles><ArticleCount>".$this->article_count."</ArticleCount><FuncFlag>0</FuncFlag></xml>";
		weixin_log("reply_text: \n".$this->the_string);
        return $this->the_string;
	}

}


?>
