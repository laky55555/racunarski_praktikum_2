<?php 

class PrijavaController extends BaseController
{

	/**
		Funkcija koja proslijedi odgovarajuci view ovisno o tome jesmo li 
		logirani ili ne.
	*/
	public function index() 
	{
		//session_start();
		if( isset( $_SESSION['username'] ) )
			$this->registry->template->show( 'prijava_index' );	
		else
		{
			$this->registry->template->title = 'Prijava/registracija';
			$this->registry->template->show( 'prijava_logiranje' );
		}	
	}


	/**
		Funkcija koja ce pokusat napravit login.
		Ako su uneseni trazeni podaci, poziva funkciju koja ce provjeriti
		postoji li korisnik s danim podacima u bazi.
	*/
	public function login()
	{
		//session_start();
		if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) )
		{
			$this->registry->template->errorMsg = 'Trebate unijeti korisničko ime i lozinku.';
			$this->registry->template->show( 'prijava_logiranje' );
			exit();
		}

		if( !preg_match( '/^[a-zA-Z]{3,10}$/', $_POST['username'] ) )
		{
			$this->registry->template->errorMsg = 'Korisničko ime treba imati između 3 i 10 slova.';
			$this->registry->template->show( 'prijava_logiranje' );
			exit();		
		}


		$ts = new TrgovinaService();

		$rezultat = $ts->ispravanLogin();		
		switch ($rezultat) 
		{
			case -1:
				$this->registry->template->errorMsg = 'Kod prijave ne postoji dani username';
				break;
			case 0:
				$this->registry->template->errorMsg = 'Korisnik s tim imenom se nije još registrirao. Provjerite e-mail.';
				break;
			case 1:
				$this->registry->template->errorMsg = 'Lozinka nije ispravna.';
				break;
			case 2:
				$this->registry->template->show( 'prijava_index' );	
				exit();		
				break;
			default:
				exit( 'Greška kod provjere logina: ' );
		}
		$this->registry->template->show( 'prijava_logiranje' );
		exit();
		
	}


	/**
		Funkcija koja je zaduzena za registraciju novih korisnika.
		Provjeri jesu li dobiveni podaci legalni, te ako jesu pozove funkciju
		koja provjeri da li se registracija moze provesi te ispise odgovarajucu
		poruku.
	*/
	public function registracija()
	{
		//session_start();
		if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) || !isset( $_POST['email'] ) )
		{
			$this->registry->template->errorMsg = 'Trebate unijeti korisničko ime, lozinku i e-mail adresu.';
			$this->registry->template->show( 'prijava_logiranje' );
			exit();
		}

		if( !preg_match( '/^[A-Za-z]{3,10}$/', $_POST['username'] ) )
		{
			$this->registry->template->errorMsg = 'Username mora imati između 3 i 10 znakova i biti sastavljen samo od slova.';
			$this->registry->template->show( 'prijava_logiranje' );
			exit();
		}
		else if( !filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL) )
		{
			$this->registry->template->errorMsg = 'Email adresa nije valjana.';
			$this->registry->template->show( 'prijava_logiranje' );
			exit();
		}

		$ts = new TrgovinaService();

		$rezultat = $ts->ispravnaRegistracija();

		$this->registry->template->errorMsg = 'Username je vec u upotrebi.';
		if($rezultat)
		{
			$this->registry->template->errorMsg = 'Registracija je uspjesno izvrsena. Prije mogucnosti logiranje treba potvrditi registraciju na mailu.';
		}

		$this->registry->template->show( 'prijava_logiranje' );	
		exit();

	}

	/**
		Funkcija koja izvrsi odjavu.
		Nakon odjave prebaci na pocetnu stranicu.
	*/
	public function logout()
	{
		session_unset();
		session_destroy();

		header( 'Location: ' . __SITE_URL . '/index.php?rt=predmeti' );
		exit();
	}


	/**
		Funkcija koja je zaduzena za korisnikovu potvrdu da je primio mail.
		Nakon poziva provjerava postoji li korisnik u bazi s danim nizom, te 
		ako postoji korisnik postaje aktivan.
	*/
	public function potvrda()
	{

		require_once 'model/db.class.php';

		// Ova skripta analizira $_GET['niz'] i u bazi postavlja has_registered=1 za onog korisnika koji ima taj niz.
		// Jako je mala šansa da dvojica imaju isti.

		if( !isset( $_GET['niz'] ) || !preg_match( '/^[a-z]{20}$/', $_GET['niz'] ) )
			exit( 'Nešto ne valja s nizom.' );


		$ts = new TrgovinaService();

		$this->registry->template->errorMsg = 'Došlo je do pogreške, username s danim nizom nije pronađen.';
		if( $ts->updatePotvrde())
			$this->registry->template->errorMsg = 'Korisnički račun je aktiviran. Želimo Vam ugodno kupovanje i brz internet.';	

		$this->registry->template->show( 'prijava_logiranje' );	
		exit();

	}


}; 

?>









