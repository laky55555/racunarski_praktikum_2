<?php


class SobaService{

	/**
	 * Pomocna funkcija koja iz dobivenog id sobe i broj igraca za koliko je soba
	 * vraca sobu iz baze.
	 * @param  int $id_sobe Int koji oznacava id sobe u odgovarajucoj tablici.
	 * @param  int $broj_igraca Int koji govori u kojoj tablici moramo sobu traziti.
	 * @return Soba Vraca se soba pronadena u bazi, ili false ako nije pronadena.
	 */
	public function vrati_sobu_po_idu($id_sobe, $broj_igraca)
	{
		$db = DB::getConnection();

		if($broj_igraca == 2)
		{
			try
			{
				$st = $db->prepare( 'SELECT * FROM sobe2 WHERE id=:id_sobe' );
				$st->execute( array( ':id_sobe' => $id_sobe));
			}
			catch( PDOException $e ) { exit( 'Greska u trazenju sobe2, SobaService/vrati_sobu_po_idu: ' . $e->getMessage() ); }

			return new Soba($st->fetch());
		}
		else if($broj_igraca == 4)
		{
			try
			{
				$st = $db->prepare( 'SELECT * FROM sobe4 WHERE id=:id_sobe' );
				$st->execute( array( ':id_sobe' => $id_sobe));
			}
			catch( PDOException $e ) { exit( 'Greska u trazenju sobe4, SobaService/vrati_sobu_po_idu: ' . $e->getMessage() ); }

			return new Soba($st->fetch());
		}

		return false;
	}

	/**
	 * Pomocna funkcija koja prima izmijenjenu sobu te ju takvu update-a u bazi.
	 * @param  Soba $soba Izmijenjena soba koju treba update-ati.
	 * @return boolean Vraca true ako je baza uspjesno stvorena, inace false.
	 */
	private function update_sobe($soba)
	{

		$db = DB::getConnection();
		if($soba->broj_igraca == 2)
		{
			try
			{
				$st = $db->prepare( 'UPDATE sobe2 SET igrac0=:igrac0, igrac1=:igrac1, potvrda0=:potvrda0,
					potvrda1=:potvrda1, id_igre=:id_igre WHERE id=:id_sobe' );
				$st->execute( array( 'igrac0' => $soba->igraci[0], 'igrac1' => $soba->igraci[1],
					'potvrda0' => $soba->potvrde[0], 'potvrda1' => $soba->potvrde[1],
					'id_igre' => $soba->id_igre, 'id_sobe' => $soba->id_sobe));
			}
			catch( PDOException $e ) { exit( 'Greska u update-u baze sobe2 SobaService/update_sobe: ' . $e->getMessage() . "  " . var_dump($soba->potvrde)   ); }

			return true;
		}
		elseif ($soba->broj_igraca == 4)
		{
			try
			{
				$st = $db->prepare( 'UPDATE sobe4 SET igrac0=:igrac0, igrac1=:igrac1, igrac2=:igrac2, igrac3=:igrac3,
					potvrda0=:potvrda0, potvrda1=:potvrda1, potvrda2=:potvrda2, potvrda3=:potvrda3,
					id_igre=:id_igre WHERE id=:id_sobe' );
				$st->execute( array( 'igrac0' => $soba->igraci[0], 'igrac1' => $soba->igraci[1], 'igrac2' => $soba->igraci[2],
					'igrac3' => $soba->igraci[3], 'potvrda0' => $soba->potvrde[0], 'potvrda1' => $soba->potvrde[1],
					'potvrda2' => $soba->potvrde[2], 'potvrda3' => $soba->potvrde[3],
					'id_igre' => $soba->id_igre, 'id_sobe' => $soba->id_sobe));
			}
			catch( PDOException $e ) { exit( 'Greska u update-u baze sobe4 SobaService/update_sobe: ' . $e->getMessage() ); }

			return true;
		}

		return false;
	}


	/**
	 * Izbacuje sobu iz liste soba.
	 * @param  int    $id           Id sobe koja se brise
	 * @param  int 	  $broj_igraca  Broj igraca da se zna u kojoj tablici se trazi.
	 * @return boolean              Vraca true ako je uspjesno.
	 */
	private function izbrisi_sobu($id, $broj_igraca)
	{
		$ime = 'sobe2';
		if($broj_igraca == 2)
		{
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare( 'DELETE FROM sobe2 WHERE id=:id' );
				$st->execute( array( ':id' => $id));
			}
			catch( PDOException $e ) { exit( 'Greska u SobaService/izbrisi_sobu2: ' . $e->getMessage() ); }

			return true;
		}
		else if($broj_igraca == 4)
		{
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare( 'DELETE FROM sobe4 WHERE id=:id' );
				$st->execute( array( ':id' => $id));
			}
			catch( PDOException $e ) { exit( 'Greska u SobaService/izbrisi_sobu4: ' . $e->getMessage() ); }

		return true;
	}

	return false;

}




	//OVO CEMO KORISTITI AKO SE ODLUCIMO NA SAMO JEDNU BAZU ZA SOBE
	/**
	 * Stvara novu sobu u bazi. Novu sobu stavlja u bazu za broj igraca
	 * koji dobije preko POST['broj_igraca'] varijable.
	 * @return boolean Vraca true ako je baza uspjesno stvorena, inace false.
	 */
	/*public function stvori_sobu()
	{
		if(!isset($_POST['broj_igraca']))
			return -1;

		$db = DB::getConnection();
		if ($_POST['broj_igraca'] == 2)
		{
			$imena_igraca = $_SESSION['username'] . ', ';
			$potvrde = '0, 0';
		}
		else
		{
			$imena_igraca = $_SESSION['username'] . ', , , ';
			$potvrde = '0, 0, 0, 0';
		}

		try
		{
			$st = $db->prepare( 'INSERT INTO sobe(igraci, potvrde, broj_igraca) VALUES '.
				'(:imena_igraca, potvrde, :broj_igraca)');
			$st->execute(array('imena_igraca' => $imena_igraca, 'potvrde' => $potvrde 'broj_igraca' => $_POST['broj_igraca']));
		}
		catch( PDOException $e ) { exit( 'Greska u bazi SobaService/stvori_sobu: ' . $e->getMessage() ); }

	}*/

	/**
	 * Funkcija koja prima broj igraca da znamo u kojoj tablici trazit, te pomocu username-a u sessionu
	 * pronalazi id sobe u koju je igrac stvorio.
	 * @param int $broj_igraca Broj igraca za koliko je soba predvidena.
	 * @param string $session_username Username igraca koji je stvorio sobu.
	 * @return int Vraca id sobe koju je korisnik stvorio.
	 */
	public function pronadi_sobu_po_username($broj_igraca, $session_username)
	{
		$db = DB::getConnection();

		if($broj_igraca == 2)
		{
			try
			{
				$st = $db->prepare( 'SELECT id FROM sobe2 WHERE igrac0=:username' );
				$st->execute( array( ':username' => $session_username));
			}
			catch( PDOException $e ) { exit( 'Greska u trazenju sobe2, SobaService/pronadi_sobu_po_username: ' . $e->getMessage() ); }

			return $st->fetch()['id'];
		}
		else if($broj_igraca == 4)
		{
			try
			{
				$st = $db->prepare( 'SELECT id FROM sobe4 WHERE igrac0=:username' );
				$st->execute( array( ':username' => $session_username));
			}
			catch( PDOException $e ) { exit( 'Greska u trazenju sobe4, SobaService/vrati_sobu_po_idu: ' . $e->getMessage() ); }

			return $st->fetch()['id'];
		}

		return false;
	}

	/**
	 * Stvara novu sobu u bazi. Novu sobu stavlja u bazu za broj igraca
	 * koji dobije preko POST['broj_igraca'] varijable.
	 * @param int $broj_igraca Broj igraca za koliko je soba predvidena.
	 * @param string $session_username Username igraca koji je stvorio sobu.
	 * @return mixed Vraca id sobe ako je soba uspjesno stvorena u bazi, inace false.
	 */
	public function stvori_sobu($broj_igraca, $session_username)
	{
		if(!isset($_POST['broj_igraca']))
			return false;

		$db = DB::getConnection();
		if ($broj_igraca == 2)
		{
			try
			{
				$st = $db->prepare( 'INSERT INTO sobe2(igrac0) VALUES '. '(:imena)');
				$st->execute(array('imena' => $session_username));
			}
			catch( PDOException $e ) { exit( 'Greska u bazi SobaService/stvori_sobu za 2: ' . $e->getMessage() ); }

			return $db->lastInsertId();
		}
		else if($_POST['broj_igraca'] == 4) //bug bilo 2 umjesto 4
		{
			try
			{
				$st = $db->prepare( 'INSERT INTO sobe4(igrac0) VALUES '. '(:imena)');
				$st->execute(array('imena' => $session_username));
			}
			catch( PDOException $e ) { exit( 'Greska u bazi SobaService/stvori_sobu za 4: ' . $e->getMessage() ); }

			return $db->lastInsertId();
		}

		return false;
	}

	/**
	 * Fukcija koja vraca niz sa svim stvorenim sobama.
	 * @return array Niz instanca klase Soba.
	 */
	public function sve_sobe()
	{
		try
		{
			$db = DB::getConnection();
			$st2 = $db->prepare( 'SELECT * FROM sobe2 ORDER BY username' );
			$st2->execute();
		}
		catch( PDOException $e ) { exit( 'Greska u vracanju soba, SobaService/sve_sobe: ' . $e->getMessage() ); }

		try
		{
			$db = DB::getConnection();
			$st4 = $db->prepare( 'SELECT * FROM sobe4 ORDER BY username' );
			$st4->execute();
		}
		catch( PDOException $e ) { exit( 'Greska u vracanju soba, SobaService/sve_sobe: ' . $e->getMessage() ); }

		$sobe = array();
		while( $soba = $st2->fetch() )
			$sobe[] = new Soba($soba);
		while( $soba = $st4->fetch() )
			$sobe[] = new Soba($soba);

		return $sobe;
	}


	/**
	 * Funkcija prima varijablu s id-em sobe u koju zelimo ubaciti
	 * korisnika, te ga ubaci u sobu ako u sobi ima mjesta.
	 * @param int $id_sobe Id sobe u koju zelimo ubaciti korisnika.
	 * @param  int $broj_igraca Broj igraca za koliko je briskula. Potrebno jer imamo 2 baze za sobe.
	 * @return int Broj sobe u koju smo ubacili korisnika ili -1 ako nismo.
	 */
	public function ubaci_me_u_sobu($id_sobe, $broj_igraca)
	{
		$soba = $this->vrati_sobu_po_idu($id_sobe, $broj_igraca);

		if($soba->postoji === false)
			return -1;

		if($soba->ubaci_u_sobu($_SESSION['username']) === false)
			return -1;

		$this->update_sobe($soba);

		return $soba->id_sobe;
	}

	/**
	 * Izbacuje trenutnog klijenta iz sobe.
	 * Izbrise klijentu u sessionu id_sobe i broj_igraca tako
	 * da moze uci u sljedecu sobu.
	 * @param  int $id_sobe Int potreban za naci sobu u bazi.
	 * @param  int $broj_igraca Int potreban za znati u kojoj bazi traziti.
	 * @param  string $session_username Ime korisnika kojeg izbacujemo iz sobe.
	 */
	public function izbaci_iz_sobe($id_sobe, $broj_igraca, $session_username)
	{
		$soba = $this->vrati_sobu_po_idu($id_sobe, $broj_igraca);

		$soba->izbaci_iz_sobe($session_username);

		if($soba->nije_prazna())
			$this->update_sobe($soba);
		else
			$this->izbrisi_sobu($soba->id_sobe, $soba->broj_igraca);

	}

	/**
	 * Mijenja status klijenta u sobi u spreman za igru, postavlja
	 * true na odgovarajuce mjesto u bazi.
	 * @param  int $id_sobe Int potreban za naci sobu u bazi.
	 * @param  int $broj_igraca Int potreban za znati u kojoj bazi traziti.
	 * @param  string $session_username Ime korisnika kojeg izbacujemo iz sobe.
	 */
	public function spreman_za_igru($id_sobe, $broj_igraca, $session_username)
	{
		$soba = $this->vrati_sobu_po_idu($id_sobe, $broj_igraca);

		$soba->spreman($session_username);

		$this->update_sobe($soba);
	}

	/**
	 * Mijenja status klijenta u sobi u nisam spreman za igru, postavlja
	 * false na odgovarajuce mjesto u bazi.
	 * @param  int $id_sobe Int potreban za naci sobu u bazi.
	 * @param  int $broj_igraca Int potreban za znati u kojoj bazi traziti.
	 * @param  string $session_username Ime korisnika kojeg izbacujemo iz sobe.
	 */
	public function nisam_spreman_za_igru($id_sobe, $broj_igraca, $session_username)
	{
		$soba = $this->vrati_sobu_po_idu($id_sobe, $broj_igraca);

		$soba->nisam_spreman($session_username);

		$this->update_sobe($soba);
	}

	/**
	 * Mijenja id_igre u sobi tako da ostali clanovi sobe mogu
	 * uci u igru.
	 * true na odgovarajuce mjesto u bazi.
	 * @param  int $id_sobe Int potreban za naci sobu u bazi.
	 * @param  int $broj_igraca Int potreban za znati u kojoj bazi traziti.
	 * @param  int $id_igre Id novostvorene igre kojoj ce se ostali igraci sobe pridruziti.
	 */
	public function postavi_id_igre_u_sobu($id_sobe, $broj_igraca, $id_igre)
	{
		$soba = $this->vrati_sobu_po_idu($id_sobe, $broj_igraca);

		$soba->postavi_id_igre($id_igre);

		$this->update_sobe($soba);
	}

};


?>
