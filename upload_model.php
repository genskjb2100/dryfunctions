<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}

	public function uploadDocFiles($input, $loc, $max_size = 0)
	{
		$config['upload_path'] = $loc;
		$config['allowed_types'] = 'xls|XLS|csv|CSV|doc|DOC|docx|DOCX|pdf|PDF';	
		$config['max_size']	= $max_size;
		$this->load->library('upload', $config);

		if( $this->upload->do_upload($input)  ):
			$filedata= array( 'upload_data' => $this->upload->data() );
			return $imgdata;
		else:
			return FALSE;
		endif;
	}
	
	public function uploadImg($input, $loc, $newname = NULL, $max_size = 0)
	{
		$config['upload_path'] = $loc;
		$config['allowed_types'] = 'gif|jpg|png|jpeg|JPG|JPEG|GIF|PNG';
		$config['max_size']	= $max_size;
		$config['remove_spaces'] = TRUE;
		if($newname != NULL) $config['file_name'] = $newname;
		
		$this->load->library('upload', $config);
		if( $this->upload->do_upload($input)  ):
			$imgdata= array('upload_data' => $this->upload->data() );
			return $imgdata;
		else:
			return FALSE;
		endif;
	}	
}
