<?php

class Recenzija
{
	protected $id, $id_predmet, $id_user, $ocjena, $recenzija;

	function __construct($id, $id_predmet, $id_user, $ocjena, $recenzija)
	{
		$this->id = $id;
		$this->id_user = $id_user;
		$this->id_predmet = $id_predmet;
		$this->ocjena = $ocjena;
		$this->recenzija = $recenzija;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $value ) { $this->$prop = $value; return $this; }
}

?>

