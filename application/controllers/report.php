<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {
	
	private $wxid;
	private $user;

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url','html','form'));
		$this->load->model('reportdb');
	}
	
	/**
	 * 从url当中获取用户信息，wxid -> user
	 */
	private function load_user($wxid) {
		$this->wxid = $wxid;
		$this->user = $this->reportdb->user_get($wxid);
	}

	/**
	 */
	public function index($wxid='ddd')
	{
		$this->load_user($wxid);

		$this->load->view('report/index.html',array('base_url'=>base_url(), 'user'=>$this->user));
	}

	/**
	 */
	public function user($wxid,$action='show')
	{
		$this->load_user($wxid);

		$this->load->view('report/user.html',array('base_url'=>base_url(), 'user'=>$this->user, 'wxid'=>$wxid));
	}

	/**
	 */
	public function report()
	{
		echo 'hehe';
	}

	/**
	 */
	public function all()
	{
		echo 'hehe';
	}

	/**
	 */
	public function album($wxid)
	{
		$this->load_user($wxid);
		$album = $this->reportdb->album_list($wxid);

		$this->load->view('report/album.html',array('base_url'=>base_url(), 'album'=>$album));
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */