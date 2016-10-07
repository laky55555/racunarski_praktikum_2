<?php

class BriskulaService{

	/* Svi elementi baze:
	   :id, :imena_igraca, :karte_u_ruci, :karte_u_spilu, :karte_izasle, :karte_bacene_u_rundi,
	   :bodovi, :igrac_na_potezu, :broj_odigralih, :gotova_igra, :karte_briskula, :dupla, :broj_igraca */


	/**
	 * Funkcija koja nam sluzi da pronademo da li za dani id igre i dano ime
	 * postoji igra u bazi.
	 * @param  int $id_igre Id igre koju trazimo u bazi.
	 * @param  int $session_username Username za koje porvjeravamo postoji li u bazi.
	 * @return boolean Vraca true ako za dobivene podatke postoji igra u bazi.
	 */
	public function session_id_je_valjan($id_igre, $session_username)
	{
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT imena_igraca FROM igre WHERE id=:id');
			$st->execute( array( 'id' => $_SESSION['id_igre']));
		}
		catch( PDOException $e ) { exit( 'Greska u bazi kod trazenja igre po username-u: ' . $e->getMessage() ); }

		$row = $st->fetch();

		if($row === false)
			return false;
		$imena_igraca = explode(', ', $row['imena_igraca']);
		foreach ($imena_igraca as $ime)
			if($ime === $session_username)
				return true;

		return false;

	}


	/**
	 * Funkcija koja pomocu username-a korisnika pokusava pronaci igru koju on trenutno igra.
	 * @param string $session_username username po kojem trazimo igru u bazi igre.
	 * @return Int, 0 ako igra s trenutnim korisnikom ne postoji, inace broj igre.
	 */
	public function pronadi_igru_po_username($session_username)
	{
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM igre WHERE imena_igraca LIKE :username');
			$st->execute( array( 'username' => '%'. $session_username .'%' ));
		}
		catch( PDOException $e ) { exit( 'Greska u bazi kod trazenja igre po username-u: ' . $e->getMessage() ); }

		$row = $st->fetch();

		if($row === false)
			return 0;

		return $row['id'];
	}

	/**
	 * Funkcija koja pomocu id-a igre pokusava pronaci igru.
	 * @param int $id_igre Id igre koju trazimo u bazi.
	 * @return Array, vraca cijeli redak tablice.
	 */
	public function pronadi_igru_po_id($id_igre)
	{
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM igre WHERE id=:id');
			$st->execute( array( 'id' => $id_igre ));
		}
		catch( PDOException $e ) { exit( 'Greska u bazi kod trazenja igre po id-u: ' . $e->getMessage() ); }

		$row = $st->fetch();

		return $row;
	}

	/**
	 * Pomocna funkcija koja iz dobivenog parametra konstruira novu igru u bazi.
	 * @param array $igra Niz koji sadrzi sve varijable potrebne za izradu nove instance u bazi.
	 * @return int Vraća id novostvorene igre.
	 */
	private function stavi_novu_igru_u_bazu($igra)
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO igre(imena_igraca, karte_u_ruci, karte_u_spilu, karte_izasle, karte_bacene_u_rundi,
				bodovi, igrac_na_potezu, broj_odigralih, gotova_igra, karte_briskula, dupla, broj_igraca) VALUES '.
				'(:imena_igraca, :karte_u_ruci, :karte_u_spilu, :karte_izasle, :karte_bacene_u_rundi,
				:bodovi, :igrac_na_potezu, :broj_odigralih, :gotova_igra, :karte_briskula, :dupla, :broj_igraca)');
			$st->execute(array('imena_igraca' => $igra['imena_igraca'], 'karte_u_ruci' => $igra['karte_u_ruci'],
				'karte_u_spilu' => $igra['karte_u_spilu'], 'karte_izasle' => $igra['karte_izasle'],
				'karte_bacene_u_rundi' => $igra['karte_bacene_u_rundi'], 'bodovi' => $igra['bodovi'],
				'igrac_na_potezu' => $igra['igrac_na_potezu'], 'broj_odigralih' => $igra['broj_odigralih'],
				'gotova_igra' => $igra['gotova_igra'], 'karte_briskula' => $igra['karte_briskula'], 'dupla' => $igra['dupla'],
				'broj_igraca' => $igra['broj_igraca']));
		}
		catch( PDOException $e ) { exit( 'Greska u bazi kod stvaranja nove igre: ' . $e->getMessage() ); }
		return $db->lastInsertId();
	}

	/**
	 * Pomocna funkcija koja prima varijablu koja sadrzi sve parametre koje baza ima te
	 * nakon pozivanja osvjezi podatke u bazi s dobivenim podacima iz parametra.
	 * @param array $igra Polje koje sadrzi sve varijable koje se nalaze u bazi podataka koja cuva igranje.
	 */
	private function update_baze($igra)
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE igre SET karte_u_ruci=:karte_u_ruci, karte_u_spilu=:karte_u_spilu,
				karte_izasle=:karte_izasle, karte_bacene_u_rundi=:karte_bacene_u_rundi, bodovi=:bodovi,
				igrac_na_potezu=:igrac_na_potezu, broj_odigralih=:broj_odigralih, gotova_igra=:gotova_igra
				WHERE id=:id' );
			$st->execute( array( 'karte_u_ruci' => $igra['karte_u_ruci'], 'karte_u_spilu' => $igra['karte_u_spilu'],
				'karte_izasle' => $igra['karte_izasle'], 'karte_bacene_u_rundi' => $igra['karte_bacene_u_rundi'],
				'bodovi' => $igra['bodovi'], 'igrac_na_potezu' => $igra['igrac_na_potezu'], 'broj_odigralih' => $igra['broj_odigralih'],
				'gotova_igra' => $igra['gotova_igra'], 'id' => $igra['id']));
		}
		catch( PDOException $e ) { exit( 'Greska u update-u baze igre: ' . $e->getMessage() ); }
	}


	/**
	 * Funkcija koja je zaduzena za stvaranje nove igre u bazi games.
	 * Funkcija iz baze onlines dozna tko s kime igramo, te pomocu toga proslijedi
	 * pomocnoj funkciji stavi_novu_igru_u_bazu sve potrebne informacije za novu igru.
	 * @param  int $id_sobe Id sobe iz u kojoj se nalazi osoba koja stvara novu igru.
	 * @param  int $broj_igraca Broj igraca za koliko ce biti nova igra.
	 * @return int Id novo stvorene igre.
	 */
	public function stvori_novu_igru($id_sobe, $broj_igraca)
	{
		$ss = new SobaService();

		$soba = $ss->vrati_sobu_po_idu($id_sobe, $broj_igraca);
		if($soba->id_igre != -1)
			return $soba->id_igre;
		$imena_igraca = "";
		if($soba->broj_igraca == 4)
			$imena_igraca = $soba->igraci[0] . ", " . $soba->igraci[1] . ", " . $soba->igraci[2] . ", " . $soba->igraci[3];
		else
		 $imena_igraca = $soba->igraci[0] . ", " . $soba->igraci[1];
		$igra = new Briskula(false, $broj_igraca, 3, $imena_igraca);
		$redak = $igra->vrati_redak();
		return $this->stavi_novu_igru_u_bazu($redak);
	}

	/**
	 * Funkcija koja dohvaca trenutno stanje igre tako da se igracu moze nacrtati
	 * sto se trenutno dogada te je li na redu. Funkcija zna koji igrac i koju igru
	 * treba pomocu SESSION username i id_igre.
	 * @param  int $id_igre Id igre za koju zelimo dobiti podatke
	 * @param  string $username Username igraca koji poziva funkciju.
	 * @return Array koji sadrzi: trenutne karte igraca, tko sve igra, koja je briskula,
	 *			karte koje su vec bacene i 0/1 ovisno je li igrac na potezu.
	 */
	public function vrati_stanje_igre($id_igre, $username)
	{
		$redak = $this->pronadi_igru_po_id($id_igre);
		$igra = new Briskula($redak);

		if($redak === false)
			exit('Ne postoji igra s ovim igracem.');

		$imena_igraca = explode(', ', $igra->imena_igraca);
		$moj_broj_u_igri = array_search($username, $imena_igraca);
		if($moj_broj_u_igri === false)
			exit('Ovo nije moja igra, nesto se cudno dogodilo. (BriskulaService/vrati_stanje_igre)');

		$moje_karte = array();
		$kraj_trazenja = ($moj_broj_u_igri + 1)*$igra->dupla;
		for ($i=$moj_broj_u_igri*$igra->dupla; $i < $kraj_trazenja; $i++)
			$moje_karte[] = $igra->karte_u_ruci[$i];

		$na_potezu = 0;
		if($igra->igrac_na_potezu == $moj_broj_u_igri)
			$na_potezu =1;

		return array('moje_karte' => $moje_karte, 'imena_igraca' => explode(", ",  $igra->imena_igraca),  'briskula' => $igra->karte_briskula,
			'bacene_karte' => $igra->karte_bacene_u_rundi, 'gotovo' => $igra->gotova_igra, 'jesam_na_potezu' => $na_potezu,
			'moji_bodovi' => $igra->bodovi[$moj_broj_u_igri%2], 'tudi_bodovi' => $igra->bodovi[($moj_broj_u_igri+1)%2],
			'broj_karti_u_spilu' => $igra->broj_karti_u_spilu(), 'izasle_u_prosloj' => $igra->izasle_u_prosloj(), "igrac_na_potezu" => $igra->igrac_na_potezu);


	}

	/**
	 * Funkcija koja obraduje bacanje karte.
	 * Funkcija dohvati trenutno stanje baze, te se osloni na klasu briskula da se
	 * pozabavi update-om stanja.
	 * Po zavrsetku pozove pomocnu funkciju update_baze koja spremi promjene u bazu.
	 * @param  [type] $id_igre Id igre kojoj je korisnik bacio kartu.
	 * @param  [type] $id_bacene_karte Broj karte koju je igrac bacio_kartu
	 */
	public function bacio_kartu($id_igre, $id_bacene_karte)
	{
		$redak = $this->pronadi_igru_po_id($id_igre);

		$trenutna_igra = new Briskula($redak);
		$trenutna_igra->obradi_potez($id_bacene_karte);

		$redak = $trenutna_igra->vrati_redak();
		$this->update_baze($redak);
	}


	/**
	 * Pomocna funkcija koja procita stanje igre u bazi, izbrise ju, te update-a
	 * bazu s odgovarajucom sobom (stavi id_igre na -1).
	 * @param  int $id_igre Id igre koju zelimo procitati/izbrisati.
	 * @param  int $id_sobe Id sobe iz koje je igra krenula, potrebno za update id_igre.
	 * @param  int $broj_igraca Broj igraca koji su igrali igru, potrebno da znamo koju bazu gledati.
	 * @return Array Niz koji sadrzi imena igraca koji su igrali, te bodovno stanje na kraju igre.
	 */
	public function obrada_kraja($id_igre, $id_sobe, $broj_igraca)
	{
		try
		{
			$db = DB::getConnection();
			if($broj_igraca == 2)
				$db->query('CALL procitaj_i_obrisi2(' . $id_igre . ", ". $id_sobe . ',  @imena, @bodovi)');
			else if($broj_igraca == 4)
				$db->query('CALL procitaj_i_obrisi4(' . $id_igre . ", ". $id_sobe . ',  @imena, @bodovi)');

		}
		catch( PDOException $e ) { exit( 'Greska u update-u baze igre: ' . $e->getMessage() . " parametri: " . $id_igre . "," .	 $id_sobe . "," . $broj_igraca); }

		foreach($db->query( 'SELECT @imena, @bodovi' ) as $row)
		{
			return array($row['@imena'], $row['@bodovi'], $row[0], $row[1]);
		}

	}

};


?>
