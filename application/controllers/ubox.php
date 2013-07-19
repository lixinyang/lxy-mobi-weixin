<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ubox extends CI_Controller {
	
	private $wxid;
	private $user;

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url','html','form'));
	}

	/**
	 */
	public function index()
	{
		$this->load->view('ubox/index.html',array('base_url'=>base_url()));
	}

	/**
	 */
	public function con()
	{
		$this->load->view('ubox/con.html',array('base_url'=>base_url()));
	}

	/**
	 */
	public function weixin()
	{
		$this->load->view('ubox/weixin.html',array('base_url'=>base_url()));
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */