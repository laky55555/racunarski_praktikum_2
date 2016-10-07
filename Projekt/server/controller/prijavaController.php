<?php

/**
Klasa zadužena za prijavu/login korisnika u aplikaciju.

 */
class PrijavaController extends BaseController
{
	/**
	 * Funkcija korisniku prikazuje početni view.
	 * Ukoliko je korisnik vec prijavljen prebacuje ga se u online korisnike.
	 * U suprotnome, prikazuje mu se forma za login/registraciju.
	 */
	public function index()
	{

		//Ako je settan username prebacimo korisnika u online, te onda online
		//radi provjeru je li settan username validan.
		if( isset( $_SESSION['username'] ) )
		{
			header('Location: '  . __SITE_URL  . "/index.php?rt=online" );
			exit();
		}
		else
		{
			$this->registry->template->title = 'Prijava/registracija';
			$this->registry->template->show( 'login' );
		}
	}

	/**
	 * Funkcija provjerava podatke koje je korisnik poslao za logiranje te odgovarajuci tome
	 * sto je poslao ispisuje ekran za logiranje s porukom o gresci koju je korisnik napravio,
	 * ili ako je login uspjesan korinik prelazi u sobu za igranje.
	 * Funkcija preko POST varijabli prima username i password potreban za prijavu.
	 */
	public function login(){

		if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) )
		{
			$this->registry->template->greska = 'Trebate unijeti korisničko ime, lozinku.';
			$this->registry->template->show( 'login' );
			exit();
		}

		if( !preg_match( '/^[a-zA-Z]{3,10}$/', $_POST['username'] ) )
		{
			$this->registry->template->greska = 'Korisničko ime treba imati između 3 i 10 slova.';
			$this->registry->template->show( 'login' );
			exit();
		}

		$us = new UserService();

		// Provjeri taj korisnik postoji u bazi; dohvati njegove ostale podatke.
		$rezultat = $us->ispravna_prijava($_POST['username'], $_POST['password']);
		if($rezultat['baza'] === false)
			exit($rezultat['greska']);

		switch ($rezultat['kod'])
		{
			case -1:
				$this->registry->template->greska = 'Kod prijave ne postoji dani username';
				break;
			case 0:
				$this->registry->template->greska = 'Korisnik s tim imenom se nije još registrirao. Provjerite e-mail.';
				break;
			case 1:
				$this->registry->template->greska = 'Lozinka nije ispravna.';
				break;
			case 2:
				$_SESSION['username'] = $_POST['username'];
				$_SESSION['broj_pobjeda'] = $rezultat['broj_pobjeda'];
				$_SESSION['broj_odigralih'] = $rezultat['broj_odigralih'];
				$_SESSION['id'] = $rezultat['id'];
				$this->registry->template->show( 'welcome_page' );
				exit();
				break;
			default:
				exit( 'Greška kod provjere logina: ' );
		}
		$this->registry->template->show( 'login' );

	}

	//potrebno je napravti jos ako je u igri izadi iz igre, ako je u sobi izadi iz sobe.
	/**
	*Funkcija odjavljuje korisnika.
	 */
	public function logout(){

		$os = new OnlineService();
		$os->izbaci_iz_onlines($_SESSION['id']);
		session_unset();
		session_destroy();
		header('Location: '  . __SITE_URL . "/index.php" );
		exit();

	}

	public function registracija(){

		if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) || !isset( $_POST['email'] ) )
		{
			$this->registry->template->greska = 'Trebate unijeti korisničko ime, lozinku i e-mail adresu';
			$this->registry->template->show( 'login' );
			return;
		}

		else if( !preg_match( '/^[A-Za-z]{3,10}$/', $_POST['username'] ) )
		{
			$this->registry->template->greska = 'Korisničko ime treba imati između 3 i 10 slova.';
			$this->registry->template->show( 'login' );
			return;

		}
		else if( !filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL) )
		{
			$this->registry->template->greska = 'E-mail adresa nije ispravna..';
			$this->registry->template->show( 'login' );
			return;
		}

		$us = new UserService();


		$rezultat = $us->ispravna_registracija($_POST['username'], $_POST['password'], $_POST['email']);

		if($rezultat['baza'] === false)
			exit('Doslo je do greske u bazi: ' . $rezultat['greska']);
		else
			$this->registry->template->greska = $rezultat['poruka'];


		$this->registry->template->show( 'login' );
	}

	/**
	 * Funkcija koja je zaduzena za aktivaciju korisnika. Poziva ju korisnik koji se
	 * zeli aktivirati. korisnika preko GET['niz'] salje niz koji odgovara niz_za_registraciju
	 * u bazi te u slucaju da niz odgovara, aktivira korisnika.
	 */
	public function potvrda()
	{

		require_once 'model/db.class.php';

		// Ova skripta analizira $_GET['niz'] i u bazi postavlja has_registered=1 za onog korisnika koji ima taj niz.
		// Jako je mala šansa da dvojica imaju isti.

		if( !isset( $_GET['niz'] ) || !preg_match( '/^[a-z]{20}$/', $_GET['niz'] ) )
		{
			$this->registry->template->greska = 'Nešto ne valja s nizom.';
			$this->registry->template->show( 'login' );
			exit();

		}

		$us = new UserService();

		$update = $us->update_potvrde($_GET['niz']);

		if($update['baza'] === false)
			$this->registry->template->greska = $update['greska'];
		else
			$this->registry->template->greska = $update['poruka'];

		$this->registry->template->show( 'login' );
		exit();

	}



};

?>
