<?php
	
class Global_model extends CI_Model {

	public $table = "audit_trail";

	function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function paginate($refUrl, $total_rows, $per_page)
	{		
		
		$this->load->library('pagination');
		$config['base_url'] = $refUrl;
		$config['total_rows'] = $total_rows;
		$config['uri_segment'] = 4;
		$config['per_page'] = $per_page;
		$config['num_links'] = 5;
		
		$this->pagination->initialize($config); 
		return $this->pagination->create_links();
	}

	public function humanizeRef($table, $intId)
	{	
		$this->db->select("description");
		$this->db->where("id", $intId);
		$query = $this->db->get($table);
		$tmp = $query->row();
		$query->free_result();
		return $tmp->description;
	}
	
	public function audit_trail($user_id, $tablename, $operation, $overhead)
	{	
		$param = serialize($overhead); #overhead is a key value pair
		$data = array("user_id" => $user_id, "table_name" => $tablename, "operation" => $operation, "param" => $param);
		$this->db->insert($this->table, $data);
	}

	public function getAuditTrail($user_id = NULL, $table_name= NULL, $operation = NULL, $date = NULL)
	{
		if($user_id != NULL) $this->db->where("user_id", $this->encrypt_model->encryptArray($user_id));
	}

	public function is_ajax($redirect_page = '')
	{
		if(!$this->input->is_ajax_request()) redirect($redirect_page);
	}

	public function noJs()
	{
		if(!$this->input->is_ajax_request()) return TRUE;
	}

	public function getRecentAccess()
	{
		$this->db->select("A.*, B.username, C.user_agent, C.user_data");
		$this->db->from("access_trail as A");
		$this->db->join("user as B", "A.user_id = B.id", "LEFT OUTER");
		$this->db->join("ci_sessions as C", "A.session_id = C.session_id", "LEFT OUTER");
		$this->db->where("B.type_id >=", $this->session->userdata("type_id"));
		$this->db->limit(5);
		return $this->db->get()->result();
	}

	public function initSession($data)
	{
		$enc_data = array();
		//$tmp_data = $data;
		$tmp_data = array_merge($data, array("is_logged_in" => 1));
		$settings = get_object_vars($this->_getBarangaySetting());
		$tmp_data = array_merge($tmp_data, $settings);
		$this->session->set_userdata($tmp_data);
		//$data['barangay_id'];
		if( $this->session->userdata('is_logged_in') != NULL):
			return TRUE;
		endif;
		return FALSE;
	}

	public function accessTrail()
	{
		$data['user_id'] = $this->session->userdata('id');
		$data['ip_address'] = $this->session->userdata('ip_address');
		$data['access_url'] = base_url();
		$data['session_id'] = $this->session->userdata('session_id');
		return $this->db->insert("access_trail", $data);
	}

	private function _getBarangaySetting()
	{
		$this->db->select("geoloc, logo, seal, email, app_name, app_version, media_folder");
		return $this->db->get("barangay_setting")->row();
	}

	public function userRole()
	{
		foreach($this->checkUserRole() as $role):
			$data = get_object_vars($role);
		endforeach;
		return $data;
	}

	public function getModulePermission()
	{
		$perms = '';
		$perm = unserialize($this->session->userdata('permission'));
		$this->db->select("description");
		$this->db->where_in("id", $perm);
		foreach( $this->db->get("refsystem_module")->result_array() as $res):
			$perms .= $res['description'].", ";
		endforeach;
		return $perms;
	}

	public function checkUserRole()
	{
		$table = 'refuser_type';
		$type_id = $this->session->userdata('type_id');
		$this->db->where('id', $type_id);
		$this->db->select('id, description as desc');
		$query = $this->db->get($table);
		$result = $query->result();
		$query->free_result();
		return $result;
	}
	public function checkPermission($perm, $page_perm)
	{
		$user_permission = unserialize($perm);
		if(in_array($page_perm, $user_permission)) return TRUE;
		return FALSE;
	}

	public function excludeInArray($array, $index)
	{
		$temp = array();
		foreach($array as $key => $value):
			if($key != $index):
				$temp[$key] = $value;
			endif;
		endforeach;
		return $temp;
	}

	public function checkDup($field, $value,$table)
	{	
		$this->db->where($field, $value);
		$data = $this->db->get($table);
		if($data->num_rows() >= 1) return TRUE;
		return FALSE;
	}	

	 public function getAge( $d1, $d2, $display = 'year' )
    {
        //verified computation. '2007-08-07', '2004-10-01' used this values to verify.
        $d1 = new DateTime($d1);
        $d2 = new DateTime($d2);
        $diff =  $d2->diff($d1);

        $months = $diff->y * 12 + $diff->m + $diff->d / 30; 
        $year = $diff->y + $diff->m /12  + $diff->d / 30; 
        return round($year, 1);
    }

    public function searchFilter($searchString = '', $order = "ASC", $table )
    {
    	$this->db->like("first_name", $searchString);
    	$this->db->or_like("last_name", $searchString);
    	$this->db->or_like("middle_name", $searchString);
    	$this->db->or_like("last_name", $searchString);
    	$this->db->or_like("last_name", $searchString);
    }
}
