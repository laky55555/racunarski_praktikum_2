<?php

class TrgovinaService
{

	/**
		Funkcija koja vraca korisnika iz danog ID-a
	*/
	function getUserById( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, username, password FROM userList WHERE id=:id' );
			$st->execute( array( 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false )
			return null;
		else
			return new User( $row['id'], $row['username'], $row['password'] );
	}

	/**
		Funkcija koja iz danog korisnickog imena vraca korisnika ako postoji, inace null
	*/
	function getUserByUsername( $username )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, username, password FROM userList WHERE username=:username' );
			$st->execute( array( 'username' => $username ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false )
			return null;
		else
			return new User( $row['id'], $row['username'], $row['password'] );
	}

	/**
		Funkcija koja vraca true ako korisnika s danim username-om postoji u bazi inace false.
	*/
	function haveUserInDB( $username )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, username, password FROM userList WHERE username=:username' );
			$st->execute( array( 'username' => $username ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false )
			return false;
		else
			return true;
	}


	/**
		Funkcija koja vraca predmet iz dobivenog ID-a ako predmet postoji, inace null
	*/
	function getPredmetById( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, naziv, opis, cijena FROM trgovina WHERE id=:id' );
			$st->execute( array( 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false )
			return null;
		else
			return new Predmet( $row['id'], $row['naziv'], $row['opis'], $row['cijena'] );
	}

	/**
		Funkcija koja iz dobivenog ID-a predmeta vraca broja recenzija tog predmeta, i prosjecnu ocjenu.
	*/
	function brojRecenzijaIOcjene($id_predmet)
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, id_predmet, id_user, ocjena, recenzija FROM recenzije WHERE id_predmet=:id' );
			$st->execute( array( 'id' => $id_predmet ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$broj = 0; $ocjena = 0;
		while( $row = $st->fetch() )
		{
			$broj++;
			$ocjena += $row['ocjena'];
		}

		if($broj !== 0)
			$ocjena = $ocjena/$broj;

		return array ($broj, $ocjena);

	}

	/**
		Funkcija koja vraca polje svih predmeta koji se trenutno nalaze u bazi.
	*/
	function getAllPredmeti()
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, naziv, opis, cijena FROM trgovina ORDER BY naziv' );
			$st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
		{	
			$polje = $this->brojRecenzijaIOcjene($row['id']);
			$broj_recenzija = $polje[0]; $ocjena_predmeta = $polje[1];
			$arr[] = new Predmet( $row['id'], $row['naziv'], $row['opis'], $row['cijena'] , $broj_recenzija, $ocjena_predmeta);
		}

		return $arr;
	}


	/**
		Funkcija koja vraca polje recenzija za predmet s ID-iom dobivenim kao parametar.
	*/
	function getRecenzijeByPredmetId( $id_predmet )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, id_predmet, id_user, ocjena, recenzija FROM recenzije WHERE id_predmet=:id' );
			$st->execute( array( 'id' => $id_predmet ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
		{
			$arr[] = new Recenzija( $row['id'], $row['id_predmet'], $row['id_user'], $row['ocjena'], $row['recenzija']  );
		}

		return $arr;
	}

	
	/**
		Funkcija koja upisuje u bazu novu recenziju.
		Elemente koje zahtjeva recenzija su predmet kojoj recenziramo, korisnik koji recenzira,
		ocjenu koju dodjeljuje i tekst koji moze biti i prazan.
	*/
	function makeNewRecenzija( $id_predmet, $id_user, $ocjena, $recenzija )
	{
		// Provjeri prvo jel postoje taj user i taj predmet
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM userList WHERE id=:id' );
			$st->execute( array( 'id' => $id_user ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		echo $id_user;
		if( $st->rowCount() !== 1 )
			throw new Exception( 'makeNewLoan :: User with the given id_user does not exist.' );


		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM trgovina WHERE id=:id' );
			$st->execute( array( 'id' => $id_predmet ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		if( $st->rowCount() !== 1 )
			throw new Exception( 'makeNewLoan :: Predmet with the given id_predmet does not exist.' );


		// Sad napokon možemo stvoriti novu recenziju (isti korisnik moze stvoriti vise recenzija za 1 predmet)
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO recenzije(id_predmet, id_user, ocjena, recenzija) VALUES (:id_predmet, :id_user, :ocjena, :recenzija)' );
			$st->execute( array( 'id_predmet' => $id_predmet, 'id_user' => $id_user, 'ocjena' => $ocjena, 'recenzija' => $recenzija ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	/**
		Funkcija koja provjeravaj je li login ispravan.
		Funkcija vraca -1 ako username ne postoji u bazi,
		0 ako korisnik postoji ali se jos nije registrirao,
		1 ako password ne odgovara korisniku, te 2 ako je login ispravan.
	*/
	function ispravanLogin()
	{
		// Dakle dobro je korisničko ime. 
		// Provjeri taj korisnik postoji u bazi; dohvati njegove ostale podatke.
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT username, password, has_registered FROM userList WHERE username=:username' );
			$st->execute( array( 'username' => $_POST['username'] ) );
		}
		catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

		$row = $st->fetch();

		if( $row === false )
		{
			return -1;
		}
		else if( $row['has_registered'] === '0' )
		{
			return 0;
		}
		else if( !password_verify( $_POST['password'], $row['password'] ) )
		{
			return 1;
		}
		else
		{
			$_SESSION['username'] = $_POST['username'];
			return 2;
		}
	}

	/**
		Funkcija koja provjerava je li registracija s danim podacima moguca.
		Ako se korisnik s istim username-om vec nalazi u bazi vraca 0.
		Ako je korisnik dodan u bazu i mail je poslan na njegovu adresu vraca 1.
	*/
	function ispravnaRegistracija()
	{
		// Provjeri jel već postoji taj korisnik u bazi
		$db = DB::getConnection();

		try
		{
			$st = $db->prepare( 'SELECT * FROM userList WHERE username=:username' );
			$st->execute( array( 'username' => $_POST['username'] ) );
		}
		catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

		if( $st->rowCount() !== 0 )
		{
			return 0;
		}

		// Dakle sad je napokon sve ok.
		// Dodaj novog korisnika u bazu. Prvo mu generiraj random string od 10 znakova za registracijski link.
		$reg_seq = '';
		for( $i = 0; $i < 20; ++$i )
			$reg_seq .= chr( rand(0, 25) + ord( 'a' ) ); // Zalijepi slučajno odabrano slovo

		try
		{
			$st = $db->prepare( 'INSERT INTO userList(username, password, email, reg_seq, has_registered) VALUES ' .
				                '(:username, :password, :email, :reg_seq, 0)' );
			
			$st->execute( array( 'username' => $_POST['username'], 
				                 'password' => password_hash( $_POST['password'], PASSWORD_DEFAULT ), 
				                 'email' => $_POST['email'], 
				                 'reg_seq'  => $reg_seq ) );
		}
		catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

		$to       = $_POST['email'];
		$subject  = 'Registracijski mail';
		$message2  = 'Poštovani ' . $_POST['username'] . "!\nZa dovršetak registracije kliknite na sljedeći link: ";
		$message2 .= 'http://' . $_SERVER['SERVER_NAME'] . htmlentities( dirname( $_SERVER['PHP_SELF'] ) ) . '/index.php?rt=prijava/potvrda&niz=' . $reg_seq . "\n";
		$headers  = 'From: rp2@studenti.math.hr' . "\r\n" .
		            'Reply-To: rp2@studenti.math.hr' . "\r\n" .
		            'X-Mailer: PHP/' . phpversion();


		$isOK = mail($to, $subject, $message2, $headers);



		if( !$isOK )
			exit( 'Greška: ne mogu poslati mail. (Pokrenite na rp2 serveru.)' );

		return 1;
	}


	/**
		Funkcija koja vraca true ako se dobiveni niz pomocu GET metode nalazi 
		u bazi podataka, te aktivira korisnicki racun za klijenta koji ima takav niz.
		Inace izbaci gresku jer je netko prtljao po nizu.
	*/
	function updatePotvrde()
	{
		// Nađi korisnika s tim nizom u bazi
		$db = DB::getConnection();

		try
		{
			$st = $db->prepare( 'SELECT * FROM userList WHERE reg_seq=:reg_seq' );
			$st->execute( array( 'reg_seq' => $_GET['niz'] ) );
		}
		catch( PDOException $e ) { exit( 'Greska 1 u bazi: ' . $e->getMessage() ); }

		$row = $st->fetch();

		if( $st->rowCount() !== 1 )
			exit( 'Taj registracijski niz ima ' . $st->rowCount() . 'korisnika, a treba biti točno 1 takav.' );
		else
		{
			// Sad znamo da je točno jedan takav. Postavi mu has_registered na 1.
			try
			{
				$st = $db->prepare( 'UPDATE userList SET has_registered=1 WHERE reg_seq=:reg_seq' );
				$st->execute( array( 'reg_seq' => $_GET['niz'] ) );
			}
			catch( PDOException $e ) { exit( 'Greska 2 u bazi: ' . $e->getMessage() ); }

			// Sve je uspjelo, zahvali mu na registraciji.
			return true;

		}
	}

};

?>

