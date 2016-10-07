<?php

class User
{
	public $id, $username, $password, $has_registered, $reg_seq, $email, $broj_odigralih, $broj_pobjeda;		

	function __construct( $id, $username, $broj_pobjeda = 0,$broj_odigralih = 0) //$password), $email, $reg_seq, $has_registered )
	{
		$this->id = $id;
		$this->username = $username;
		$this->broj_odigralih = $broj_odigralih;
		$this->broj_pobjeda = $broj_pobjeda;
		/*$this->password = $password;
		$this->email = $email;
		$this->$reg_seq = $reg_seq; 
		$this->$has_registered = $has_registered;*/
	}

	function __get( $prop ) { return $this->$prop; }

	function zakodiraj_me(){
		return json_encode([$this->username, $this->broj_pobjeda, $this->broj_odigralih]);
	}
}

?>
