<?php
/**
 * 
 * 故障报告平台所需的数据库操作
 * @author lxy
 *
 */
class Reportdao extends CI_Model
{
	
	function __construct()
	{
		define( "TBL_USER" , 'user' );
		define( "TBL_ALBUM" , 'album' );
	}
	
	/**
	 * 
	 * 从数据库中获取某一唯一值的方法
	 * 没找到返回null
	 * 找到一个就返回那个
	 * 找到多个就抛exception（要注意哦！！）
	 * @param string $table_name
	 * @param array $query 如：array('email'=>$email)
	 * @throws Excpetion
	 */
	private function get_uniq($table_name, $query)
	{
		$q = $this->db->get_where($table_name, $query);
		if($q->num_rows()==0)
		{
			return null;
		}
		else if($q->num_rows()>1)
		{
			throw new Exception("damn! we got something dupulicated that should be uniq: table(".$table_name."), query(".var_dump($query).")");
		}
		else
		{
			return $q->row();
		}
	}


	/**
	 * 
	 * 根据wxid查找用户
	 * @param string $wxid
	 */
	function user_get($wxid) {
		return $this->get_uniq(TBL_USER, array('wxid'=>$wxid));
	}
	
	function user_add($wxid, $uboxid) {
		$data = array(
			'wxid' => $wxid,
			'uboxid' => $uboxid,
			'create_time' => date("Y-m-d H:i:s")
		);
		$this->db->insert(TBL_USER, $data);
	}
	
	
	function album_list($wxid, $length = 0, $page = 0) {
		if(!$length) $length = 50;

		$this->db->order_by("create_time", "desc");
		$query = $this->db->get(TBL_ALBUM , $length , $page*$length);
		
		return $query->result();
	}

	function album_add($wxid, $url) {
		$data = array(
			'wxid' => $wxid,
			'url' => $url,
			'create_time' => date("Y-m-d H:i:s")
		);
		$this->db->insert(TBL_ALBUM, $data);
	}

}
?>