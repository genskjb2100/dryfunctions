<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Encrypt_model extends CI_Model{
	
	/*
	*  Model For encrypting strings, passwords, post data
	*/

	function __construct(){
		parent::__construct();
		$this->load->library('encrypt');
	}
	
	public function encryptArray( $array )
	{	
		$temp = array();
		foreach( $array as $key => $value ):
			$temp[$key] = $this->encrypt->encode( $value );
		endforeach;
		return $temp;
	}

	public function decryptArray( $array )
	{	
		$temp = array(); 
		foreach( $array as $key => $value ):
			$temp[$key] = $this->encrypt->decode( $value );
		endforeach;
		return $temp;
	}

	public function encryptString( $string )
	{
		return $this->encrypt->encode( $string );
	}

	public function decryptString( $string )
	{
		return $this->encrypt->decode( $string );
	}

	public function hashPassword( $password, $type = 1 )
	{	
		$str = '';
		switch($type){
			case 1: 
				$str = md5( $password );
				break;
			case 2: 
				$str = sha1( $password );
				break;
			case 3: 
				$str = sha1( md5( base64_encode( $password ) ) ); 
				break;
			default: $str = base64_encode($password); 
					break;
		}
		return $str;
	}

}
