<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model{

	public $main_table = 'user';
	public $pass_hashing;

	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->pass_hashing = 2;
	}	
	
	public function verifyUser($username, $password, $status = 1)
	{
		$san_user = $this->encrypt_model->decryptString($username);
		$san_pass = $this->encrypt_model->decryptString($password);		
		$san_pass = $this->encrypt_model->hashPassword($san_pass, $this->pass_hashing);
		$this->db->where("username", $san_user);
		$this->db->where("password", $san_pass);
		$this->db->where("status_id", $status);
		$query = $this->db->get($this->main_table);
		if($query->num_rows == 1):
			return $query->result();
		endif;
	}

	public function updateUser($id, $data)
	{
		$data = $this->encrypt_model->decryptArray($data);
		$id = $this->encrypt_model->decryptString($id);
		$this->db->where('id',$id);
		if($this->db->update($this->main_table,$data)):
			$new_data = array_merge($data, array("id" => $id));
			return TRUE;
		endif;
		return FALSE;
	}

	public function createUser($data)
	{
		$data = $this->encrypt_model->decryptArray($data);
		$data["password"] = $this->encrypt_model->hashPassword($data["password"], $this->pass_hashing);
		if($this->db->insert($this->main_table, $data)):
			$new_data = array_merge( $data, array( "record_id" => $this->db->insert_id() ) );
			return TRUE;
		endif;
		return FALSE;
	}

	public function deleteUser($id)
	{
		$id = $this->encrypt_model->decryptString($id);
		//SET FOREIGN_KEY_CHECKS=0; TRUNCATE table1; SET FOREIGN_KEY_CHECKS=1;
		$this->db->where('id',$id);
		if($this->db->delete($this->main_table)):
			$q = $this->db->count_all($this->main_table);
			$this->db->query("SET FOREIGN_KEY_CHECKS=0");
			if($q == 0): 
				$this->db->query("TRUNCATE TABLE ".$this->main_table); 
			endif;
			//$this->db->query("SET FOREIGN_KEY_CHECKS=1");
			return TRUE;
		endif;
		return FALSE;
	}

	public function checkExisting($username, $table)
	{	
		$data = $this->encrypt->decode($username);
		$this->db->where('username', $username);
		$result = $this->db->count_all_results($table);
		return $result;
	}
	
	public function operateThis()
	{
		$id = $this->encrypt_model->decryptString($_POST['user_id']);
		$data = $this->global_model->excludeInArray($_POST, "user_id");
		if(isset($data["permission"]) && is_array($data["permission"]) ):
			$data["permission"] = serialize($data["permission"]);
		endif;
		( $id == NULL ) ? $op = "add" : $op = "edit";
		switch($op){
			/*case "add": echo $this->_addThis($data); break;
			case "edit": echo $this->_editThis($id, $data); break;
			default: break;*/
			case "add": print_r(); break;
			case "edit": echo "A"; break;
			default: break;
		}
	}

	public function updateThis()
	{
		$msg = '';
		$id = $this->encrypt_model->decryptString($this->input->post("user_id"));
		$data['username'] = $this->input->post("username");
		$password = $this->input->post("password");
		$cpassword = $this->input->post("cpassword");
		if( $password != '' && $password == $cpassword ): $data['password'] = $this->encrypt_model->hashPassword($password, $this->pass_hashing) ;endif;
		$data['status_id'] = $this->input->post("status_id");
		$data['first_name'] = $this->input->post("first_name");
		$data['last_name'] = $this->input->post("last_name");
		$data['email'] = $this->input->post("email");
		$data['permission'] = serialize($this->input->post("permission"));
		$data['barangay_id'] = $this->session->userdata("barangay_id");

		if($data['username'] == NULL || strlen($data['username']) == 0 || $data['username'] == ''):
			$msg = array("status" => "failed", "msg" => "Username is invalid.");
		else:
			if($this->global_model->update($id, "user", $data) ):
				if(!isset($data['password']) || $data['password'] == NULL): 
					$data['password'] = $this->global_model->getPassword($id); 
				endif;
				$new_data = array_merge($data, array("record_id" => $id));
				$this->global_model->audit_trail("user", "update", $new_data);
				$msg = array("status" => "success", "msg" => "User successfully updated.");	
			else:
				$msg = array("status" => "failed", "msg" => "Failed to update user.");	
			endif;
		endif;

		echo json_encode($msg);
	}
	
}
