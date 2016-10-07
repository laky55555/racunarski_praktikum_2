<?php



class UserService{

	/**
	 * Funkcija provjerava postoji li korsnik u bazi s username-om dobivenim pomocu parametra.
	 * Ukoliko se dogodila pogreška pri komunikaciji s bazom,
	 * izlazi iz aplikacije s dojavom o grešci koja je nastupila.
	 * @param int $id Id korisnika kojeg provjeravamo postoji li u bazi.
	 * @param string $username Ime koje provjeravamo postoji li u bazi.
	 * @return array Vraca niz s elementima baza => true/false ovisno je li upit uspjesno obavljen,
	 *                     postoji => true/false ako je korisnik s danim parametrima postoji u bazi,
	 *                     greska => string koji oznacava gresku koju je vratila baza, u slucaju baza = false.
	 */
	public function validan_id_i_username($id, $username)
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id FROM userList WHERE username=:username AND id=:id' );
			$st->execute( array( 'id' => $id, 'username' => $username ) );
		}
		catch( PDOException $e ) { return array('baza' => false, 'greska' => 'Greska u UserService/validan_id_i_username: ' . $e->getMessage() ); }

		$row = $st->fetch();
		if($row !== false)
			return array('baza' => true, 'postoji' => true);
		return array('baza' => true, 'postoji' => false);
	}


	///MOZDA BI TREBALO MIJENAJTI BAZU ONLINES I IZBAITI BROJ_POBJEDA I BROJ_ODIGRALIH IZ NJE TE ONDA POMOCU VANJSKOG KLJUCA
	///NAPRAVITI JOIN ONLINES I USERLIST (STEDI SE PROSTOR A SMANJUJE BRZINA, MOZDA JE I OVO BOLJE????)
	/**
	 *	Funkcija koja popunjava polje sa svim trenutno akitvnim korisnicima koje nitko nije
	 *	pitao za igrati.
	 *	Funkcija vraća popunjeno polje s korisnicima.
	 *	@return array Vraća polje aktivnih usera ako je upit prosao, inace niz baza => false i greska => opis greske
	*/
	public function svi_slobodni()
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, username, broj_pobjeda, broj_odigralih FROM onlines WHERE igrac0="" AND igrac1="" AND igrac2="" AND igrac3="" ORDER BY username' );
			$st->execute();
		}
		catch( PDOException $e ) { return array( 'baza' => false, 'greska' => 'PDO error UserService/svi_slobodni: ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
			$arr[] = new User( $row['id'], $row['username'], $row['broj_pobjeda'], $row['broj_odigralih']);

		return $arr;

	}

	/**
	 * Funkcija koja provjeravaj je li login ispravan.
	 * Funkcija vraca -1 ako username ne postoji u bazi,
	 * 0 ako korisnik postoji ali se jos nije registrirao,
	 * 1 ako password ne odgovara korisniku, te 2 ako je login ispravan.
	 * Ukoliko je je login uspjesan vrati i id igraca, broj_pobjeda i broj_odigralih.
	 * @param  string $username Ime koje trazimo u bazi i za njega provjeravamo odgovara li password
	 * @param  string $password Niz koji proveravamo je li password za danog korisnika
	 * @return array Vraca baza => true/false ovisno je li doslo do greske u bazi, te greska => opis greske ako je,
	 *                     vraca kod => -1/0/1/2 ovisno o provjeri u bazi te u slucaju ispravne provjere
	 *                     vraca 'id' , 'broj_pobjeda' i 'broj_odigralih'.
	 */
	function ispravna_prijava($username, $password)
	{
		// Dakle dobro je korisničko ime.
		// Provjeri taj korisnik postoji u bazi; dohvati njegove ostale podatke.
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM userList WHERE username=:username' );
			$st->execute( array( 'username' => $username ) );
		}
		catch( PDOException $e ) { return array( 'baza' => false, 'greska' => 'PDO error UserService/ispravna_prijava: ' . $e->getMessage() ); }

		$row = $st->fetch();

		if( $row === false ) { return array('baza' => true, 'kod' => -1); }
		else if( $row['has_registered'] === '0' ) {	return array('baza' => true, 'kod' => 0); }
		else if( !password_verify( $password, $row['password'] ) ) { return array('baza' => true, 'kod' => 1); }
		else
			return array('baza' => true, 'kod' => 2, 'id' => $row['id'], 'broj_pobjeda' => $row['broj_pobjeda'], 'broj_odigralih' => $row['broj_odigralih']);
	}



	/**
	 * Funkcaija koja sluzi za registraciju novih korisnika.
	 * Prvo provjeri da li je username vec u bazi (zauzet), u slucaju da je vrati
	 * odgovarajucu poruku, ako nije stvori novog korisnika i stavi ga u bazu.
	 * Novi korisnik nece biti aktivan dok ne potvrdi registraciju pozivom na
	 * link koji ce mu server poslati.
	 * @param  string $username Ime korisnika.
	 * @param  string $password Password korisnika.
	 * @param  string $email    Email korisnika.
	 * @return array Vraca baza => true/false ovisno je li doslo do greske u bazi, te greska => opis greske ako je,
	 *                     vraca uspjesno => true/false ovisno o tome je li novi korisnik stvoren i
	 *                     vraca poruka => s opisom radnje sto se dogodilo.
	 */
	public function ispravna_registracija($username, $password, $email)
	{

		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM userList WHERE username=:username' );
			$st->execute( array( 'username' => $username ) );
		}
		catch( PDOException $e ) { return array( 'baza' => false, 'greska' => 'PDO error UserService/ispravna_prijava/SELECT: ' . $e->getMessage() ); }

		// U bazi vec postoji netko s tim imenom
		if( $st->rowCount() !== 0 )
			return array('baza' => true, 'uspjesno' => false, 'poruka' => 'Username je vec u upotrebi.');

		// Dakle sad je napokon sve ok.
		// Dodaj novog korisnika u bazu. Prvo mu generiraj random string od 10 znakova za registracijski link.
		$reg_seq = '';
		for( $i = 0; $i < 20; ++$i )
			$reg_seq .= chr( rand(0, 25) + ord( 'a' ) ); // Zalijepi slučajno odabrano slovo

		try
		{
			$st = $db->prepare( 'INSERT INTO userList(username, password, email, reg_seq, broj_pobjeda, broj_odigralih, has_registered) VALUES ' .
				                '(:username, :password, :email, :reg_seq, 0, 0, 0)' );

			$st->execute( array( 'username' => $username,
				                 'password' => password_hash( $password, PASSWORD_DEFAULT ),
				                 'email' => $email,
				                 'reg_seq'  => $reg_seq ) );
		}
		catch( PDOException $e ) { return array( 'baza' => false, 'greska' => 'PDO error UserService/ispravna_prijava/INSERT: ' . $e->getMessage() ); }


		if($this->posalji_mail($reg_seq, $email, $username) === false)
		{
			$izbrisi = $this->izbrisi_korisnika($username);
			if($izbrisi['baza'] === true)
				return array('baza' => true, 'uspjesno' => false, 'poruka' => 'Doslo je do pogreske kod slanje maila, pokusajte se prijaviti ponovno.');
			else
				return array('baza' => false, 'uspjesno' => false, 'poruka' => $izbrisi['greska']);
		}

		return array('baza' => true, 'uspjesno' => true, 'poruka' => 'Registracija je uspjesno izvrsena. Prije mogucnosti logiranje treba potvrditi registraciju na mailu.');

	}

	/**
	 * Pomocna funkcija koja salje na danu adresu zadani niz za registraciju.
	 * Ukoliko je mail uspjesno poslan vraca true, ince false.
	 * @param  string $niz_za_registraciju Niz znakova za potvrdu registracije.
	 * @param  string $email               Mail na koji saljemo korisniku niz za registraciju.
	 * @param  string $username            Osoba kojoj saljemo mail.
	 * @return boolean					   Vraca treu ili false ovisno o uspjesnosti sljanja mail-a.
	 */
	private function posalji_mail($niz_za_registraciju, $email, $username)
	{
		// Sad mu još pošalji mail
		$to       = $email;
		$subject  = 'Registracijski mail';
		$message  = 'Poštovani ' . $username . "!\nZa dovršetak registracije kliknite na sljedeći link: ";
		$message .= 'http://' . $_SERVER['SERVER_NAME'] . htmlentities( dirname( $_SERVER['PHP_SELF'] ) ) . '/index.php?rt=prijava/potvrda&niz=' . $niz_za_registraciju . "\n";
		$headers  = 'From: register@briskula.pe.hu ' . "\r\n" .
		            'Reply-To: register@briskula.pe.hu ' . "\r\n" .
		            'X-Mailer: PHP/' . phpversion();

		$isOK = mail($to, $subject, $message, $headers);

		if( !$isOK ){
			return false;
		}
		return true;
	}

	/**
	 * Pomocna funkcija koja prima username korisnika te tog korisnika brise iz baze userList.
	 * @param  string $username Ime korisnika kojeg zelimo izbrisati.
	 * @return array Vraca polje baza => true/false ovisno o uspjesnoti brisanja, te u slucaju false,
	 *                     vrati i greska => opis greske koji se je dogodio.
	 */
	public function izbrisi_korisnika($username)
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM userList WHERE username=:username' );
			$st->execute( array( 'username' => $username ) );
		}
		catch( PDOException $e ) { return array( 'baza' => false, 'greska' => 'PDO error UserService/izbrisi_korisnika: ' . $e->getMessage() ); }

		return array('baza' => true);
	}

	/**
	 * Funkcija koja vraca true ako se dobiveni niz pomocu GET metode nalazi
	 * u bazi podataka, te aktivira korisnicki racun za klijenta koji ima takav niz.
	 * Inace izbaci gresku jer je netko prtljao po nizu.
	*/
	/**
	 * Funkcija koja prima registracijiski niz te pokusa korisnika s tim nizom "aktivirati",
	 * tj postaviti mu varijablu has_registered u bazi userList na 1. U slucaju greske vraca
	 * odgovarajucu poruku.
	 * @param  string $reg_niz Niz koji trazimo u bazi i kojeg korisnika zelimo aktivirati.
	 * @return array Vraca polje baza => true/false ovisno o uspjesnoti trazenja/update-a baze,
	 *                     te u slucaju false, vrati i greska => opis greske koji se je dogodio,
	 *                     u slucaju true vrati poruka => opisuje sto se je dogodilo u bazi.
	 */
	public function update_potvrde($reg_niz)
	{
		// Nađi korisnika s tim nizom u bazi
		$db = DB::getConnection();

		try
		{
			$st = $db->prepare( 'SELECT * FROM userList WHERE reg_seq=:reg_seq' );
			$st->execute( array( 'reg_seq' => $reg_niz ) );
		}
		catch( PDOException $e ) { return array( 'baza' => false, 'greska' => 'PDO error UserService/update_potvrde/SELECT: ' . $e->getMessage() ); }

		$row = $st->fetch();

		if( $st->rowCount() !== 1 )
			return array('baza' => true, 'poruka' => 'Došlo je do pogreške, danim nizom nije pronađen u bazi.');
		else
		{
			// Sad znamo da je točno jedan takav. Postavi mu has_registered na 1.
			try
			{
				$st = $db->prepare( 'UPDATE userList SET has_registered=1 WHERE reg_seq=:reg_seq' );
				$st->execute( array( 'reg_seq' => $reg_niz ) );
			}
			catch( PDOException $e ) { return array( 'baza' => false, 'greska' => 'PDO error UserService/update_potvrde/UPDATE: ' . $e->getMessage() ); }

			// Sve je uspjelo, zahvali mu na registraciji.

			return array('baza' => true, 'poruka' => 'Registracija je uspjesno obavljena. Uzivajte u igri :).');
		}
	}

	/**
	 * Funkcija osvježava tablicu userList sukladno ishodu igre
	 * ako je korisnik pobjedio povećaje mu se broj_pobjeda za 1
	 * inače mu se samo povećava broj odigralih igara za 1.
	 * @param string $ime Ime korisnika kojem mijenjamo broj_pobjeda i broj_odigralih
	 * @param int $rezultat Ishod igre
	 */
	public function dodaj_odigranu($ime, $rezultat){

		$db = DB::getConnection();

		try
		{
			if($rezultat === 1)
				$st = $db->prepare( 'UPDATE userList SET broj_pobjeda=broj_pobjeda+1, broj_odigralih=broj_odigralih+1 WHERE username LIKE :ime' );
			else if ($rezultat === 0)
				$st = $db->prepare( 'UPDATE userList SET broj_odigralih=broj_odigralih+1 WHERE username LIKE :ime' );
			else
				$st = $db->prepare( 'UPDATE userList SET broj_odigralih=broj_odigralih+1 WHERE username LIKE :ime' );

			$st->execute( array( 'ime' => $ime ) );
		}
		catch( PDOException $e ) { exit( 'Greska 2 u bazi: ' . $e->getMessage() ); }

	}

};


?>
