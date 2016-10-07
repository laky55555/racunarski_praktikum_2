<?php


/**
 * Klasa koja se brine o svemu vezanom s bazom onlines.
 */
class OnlineService{


	/**
	 * Pomocna funkcija koja vraca redak u onlines
	 * koji ima id dobiven preko parametra.
	 * Ukoliko se dogodila pogreška pri komunikaciji s bazom,
	 * izlazi iz aplikacije s dojavom o grešci koja je nastupila.
	 * @param int $id Id igraca kojeg trazimo u tablici onlines.
	 * @return Array Vraca niz svih podataka o osobi iz onlines, ili false ako nema te osobe.
	 */
	public function vrati_redak_po_idu($id)
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM onlines WHERE id=:id' );
			$st->execute( array( 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'Greska u OnlineService/vrati_redak_po_idu: ' . $e->getMessage() ); }

		return $st->fetch();
	}

	/**
	 * Ubacuje korisnika čije je username u session-u u online
	 * ako vec ne postoji.
	 * Ukoliko se dogodila pogreška pri komunikaciji s bazom,
	 * izlazi iz aplikacije s dojavom o grešci koja je nastupila.
	 * @param int $id_igraca Id igraca kojeg zelimo ubaciti u onlines tablicu.
	 */
	public function ubaci_u_onlines($id_igraca)
	{
		if(!$this->vrati_redak_po_idu($id_igraca))
		{
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare( 'INSERT INTO onlines(id, username, broj_pobjeda, broj_odigralih, id_sobe, igrac0, igrac1, igrac2, igrac3, broj_igraca) VALUES ' .'(:id, :username, :broj_pobjeda, :broj_odigralih, -1, "", "", "", "", -1)' );

				$st->execute( array( 'username' => $_SESSION['username'],
									'id' => $_SESSION['id'], 'broj_odigralih' => $_SESSION['broj_odigralih'],
									'broj_pobjeda' => $_SESSION['broj_pobjeda'] ));
			}
			catch( PDOException $e ) { exit( 'Greska u OnlineService/ubaci_u_onlines: ' . $e->getMessage() ); }
		}
	}


	/**
	 * Izbacuje korisnika iz liste online ljudi pomocu id-a iz sessiona.
	 * Ukoliko se dogodila pogreška pri komunikaciji s bazom,
	 * izlazi iz aplikacije s dojavom o grešci koja je nastupila.
	 * @param int $id_igraca Id igraca kojeg izbacujemo iz tablice onlines.
	 */
	public function izbaci_iz_onlines($id_igraca)
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM onlines WHERE ' .
				                'id=:id' );

			$st->execute( array( ':id' => $id_igraca ));
		}
		catch( PDOException $e ) { exit( 'Greska u OnlineService/izbaci_iz_onlines: ' . $e->getMessage() ); }
	}


	/**
	 * Funkcija koja provjerava u korisnikovom redku u onlines,
	 * je li ga netko pitao da im se pridruzi u sobi.
	 * Ako ima pitanje konstruira Sobu koja ga je pitala, inace vrati false.
	 * Ukoliko se dogodila pogreška pri komunikaciji s bazom,
	 * izlazi iz aplikacije s dojavom o grešci koja je nastupila.
	 * @param int $id_igraca Id igraca kojem provjeravamo je li ga netko pitao da ude u sobu.
	 * @return Soba Vraca sobu koja ga je pitala ili false.
	 */
	public function imam_li_poziv($id_igraca)
	{
		$row = $this->vrati_redak_po_idu($id_igraca);

		if($row['id_sobe'] != -1)
		{
			$ss = new SobaService();
			return $ss->vrati_sobu_po_idu($row['id_sobe'], $row['broj_igraca']);
		}
		return false;
	}


	/**
	 * Poziva se kad netko odbije pristupiti u sobu.
	 * Funkcija vrati na pocetne vrijednosti elemente vezane uz sobu
	 * Ukoliko se dogodila pogreška pri komunikaciji s bazom,
	 * izlazi iz aplikacije s dojavom o grešci koja je nastupila.
	 * @param  int $id_igraca Id igraca kojemu cistimo pitanje za ulaz u sobu.
	 */
	public function ocisti_pitanje($id_igraca)
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE onlines SET id_sobe=-1, igrac0="", igrac1="", igrac2="", igrac3="", broj_igraca=-1
				WHERE id=:id' );
			$st->execute( array( 'id' => $id_igraca ) );
		}
		catch( PDOException $e ) { exit( 'Greska u OnlineService/ocisti_pitanje: ' . $e->getMessage() ); }

	}


	/**
	 * Ako nitko jos nije pitao igraca za uci u sobu,
	 * update-a podatke u redku onlines s podacima o sobi koja zeli igrati s nama.
	 * @param  Soba $soba Soba u koju se poziva igrac.
	 * @param  int $id_igraca Id_igraca kojeg se poziva u sobu.
	 */
	public function postavi_sobu($soba, $id_igraca)
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id_sobe FROM onlines WHERE id=:id' );
			$st->execute( array( 'id' => $id_igraca ));
		}
		catch( PDOException $e ) { exit( 'Greska u OnlineService/ocisti_pitanje: ' . $e->getMessage() ); }

		if($st->fetch()['id_sobe'] == -1)
		{
			if($soba->broj_igraca == 2)
			{
				$igraci[2] = "";
				$igraci[3] = "";
			}
			else if($soba->broj_igraca == 4)
			{
				$igraci[2] = $soba->igraci[2];
				$igraci[3] = $soba->igraci[3];
			}
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare( 'UPDATE onlines SET id_sobe=:id_sobe, igrac0=:igrac0, igrac1=:igrac1, igrac2=:igrac2,
					igrac3=:igrac3, broj_igraca=:broj_igraca WHERE id=:id' );
				$st->execute( array( 'id' => $id_igraca, 'id_sobe' => $soba->id_sobe, 'igrac0' => $soba->igraci[0],
					'igrac1' => $soba->igraci[1], 'igrac2' => $igraci[2], 'igrac3' => $igraci[3],
					'broj_igraca' => $soba->broj_igraca  ) );
			}
			catch( PDOException $e ) { exit( 'Greska u OnlineService/postavi_sobu: ' . $e->getMessage() ); }
		}

	}



};

?>
