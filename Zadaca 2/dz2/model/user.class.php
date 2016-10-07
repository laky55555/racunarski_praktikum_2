<?php

class User
{
	protected $id, $username, $password, $has_registered, $reg_seq, $email;

	function __construct( $id, $username, $password)//, $email, $reg_seq, $has_registered )
	{
		$this->id = $id;
		$this->username = $username;
		$this->password = $password;
		/*$this->email = $email;
		$this->$reg_seq = $reg_seq; 
		$this->$has_registered = $has_registered;*/
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>

