<?php

class Predmet
{
	protected $id, $naziv, $opis, $cijena, $broj_recenzija, $pros_ocjena;

	function __construct( $id, $naziv, $opis, $cijena, $broj_recenzija=0, $pros_ocjena=0 )
	{
		$this->id = $id;
		$this->naziv = $naziv;
		$this->opis = $opis;
		$this->cijena = $cijena;
		$this->broj_recenzija = $broj_recenzija;
		$this->pros_ocjena = $pros_ocjena;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
