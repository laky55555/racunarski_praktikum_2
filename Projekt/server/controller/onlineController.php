<?php

class OnlineController extends BaseController
{

	/**
	 * Funkcija koja se poziva svaki put kad netko dode u online.
	 * Stavi trenutnog korisnika kao aktivnog u online i prikaze
	 * stranicu gdje se ceka na poziv i moze konstruirati sobaa.
	 */
	public function index()
	{
		$us = new UserService();
		$os = new OnlineService();

		// Provjerava je li username valjan, da li postoji u bazi.
		// Ako nije redirekta korisnika na stranicu za login.

		$validini_podaci = $us->validan_id_i_username($_SESSION['id'], $_SESSION['username']);
		if($validini_podaci['baza'] === false)
			exit($validini_podaci['greska']);
		elseif($validini_podaci['postoji'] === false)
		{
			header('Location: '  . __SITE_URL  . "/index.php?rt=prijava" );
			exit();
		}

		if(isset($_SESSION['id_igre']))
		{
			header('Location: '  . __SITE_URL  . "/index.php?rt=igra" );
			exit();
		}

		if(!isset($_SESSION['id_sobe']) || $_SESSION['id_sobe'] == -1)
		{
			//ubacuje u onlines ako vec nije u onlinesu
			$os->ubaci_u_onlines($_SESSION['id']);
			$this->registry->template->postoji_soba = '0';
		}
		else
		{
				$this->registry->template->postoji_soba = '1';
		}

		$this->registry->template->show( 'create_game' );
		exit();
	}

	/**
	 * Pomocna funkcija.
	 * Po pozivu funkcija pokusava trenutnog korisnika dodati u
	 * sobu dobiveno u parametru id_sobe.
	 * Ako je dodavanje uspjesno korisnika se redirecta u sobu,
	 * inace vrati poruku pomocu echo.
	 * @param  int $id_sobe Id sobe u koju zelimo uci.
	 * @param  int $broj_igraca Broj igraca za koliko je briskula. Potrebno jer imamo 2 baze za sobe.
	 * @return Vrati poruku s greskom.
	 */
	private function ubaci_me_u_sobu($id_sobe, $broj_igraca)
	{
		$ss = new SobaService();
		$os = new OnlineService();
		$id_sobe = $ss->ubaci_me_u_sobu($id_sobe, $broj_igraca);
		if($id_sobe != -1)
		{
			$os->izbaci_iz_onlines($_SESSION['id']);
			$_SESSION['id_sobe'] = $id_sobe;
			$_SESSION['broj_igraca'] = $broj_igraca;
			return $id_sobe;
		}
		else
			return -1;

	}

	/**
	 * Funkciju se poziva iz view-a create_game.
	 * Po pozivu funkcija pokusava trenutnog korisnika dodati u
	 * sobu dobiveno u POST varijabli id_sobe.
	 * Ako je dodavanje uspjesno korisnika se redirecta u sobu,
	 * inace vrati poruku pomocu echo.
	 * @return Vrati poruku s greskom.
	 */
	public function udi_u_sobu()
	{
		$this->ubaci_me_u_sobu($_POST['id_sobe'], $_POST['broj_igraca']);
		exit();
	}


	/**
	 * Funkcija preko POST varijable prima 'odgovor' i
	 * preko elemenata u bazi zna u koju sobu treba ici,
	 * te ovisno o odgovoru redirecta klijenta
	 * u sobu.
	 */
	public function odgovori_na_poziv()
	{
		$os = new OnlineService();
		$redak = $os->vrati_redak_po_idu($_SESSION['id']);
		$id_sobe = $redak['id_sobe'];
		$broj_igraca = $redak['broj_igraca'];

		if($_POST['odgovor'] === "true")
		{
			if($this->ubaci_me_u_sobu($id_sobe, $broj_igraca) != -1)
				echo json_encode(['ubacen_u_sobu' => 'true']);
			else{
				$os->ocisti_pitanje($_SESSION['id']);
				echo json_encode(['ubacen_u_sobu' => 'false']);
			}
		}
		else
		{
			$os->ocisti_pitanje($_SESSION['id']);
			echo json_encode(['ubacen_u_sobu' => 'false']);
		}

	}


	/**
	 * Funkcija se poziva iz view-a create_game.
	 * Po pozivu konstruira se nova soba i stavlja u bazu.
	 * Funkcija preko POST varijable prima broj_igraca (2/4).
	 * Ako je konstrukcija uspjesna u SESSION['id_sobe'] se sprema,
	 * id sobe te se korisnika redirecta u sobu.
	 * Inace ispisuje gresku.
	 * @return Vrati poruku s greskom.
	 */
	public function stvori_sobu()
	{
		$os = new OnlineService();
		$ss = new SobaService();
		$broj_sobe = $ss->stvori_sobu($_POST['broj_igraca'], $_SESSION['username']);
		if($broj_sobe)
		{
			$os->izbaci_iz_onlines($_SESSION['id']);
			$_SESSION['broj_igraca'] = $_POST['broj_igraca'];
			$_SESSION['id_sobe'] = $broj_sobe;
			echo json_encode("uspjesno smo stvorili sobu" . $_SESSION['id_sobe']);			//mora biti json_encode jer ajax ocekuje takve podatke
			//header('Location: '  . __SITE_URl  . "/index.php?rt=online/cekaj_pocetak_igre" );  //ne bi trebalo biti tu?ajax funkcija
		}
		else
			echo json_encode("Doslo je do greske kod stvaranje sobe.");

		exit();
	}


	/**
	 * Funkcija koja se poziva iz view-a create_game.
	 * Funkcija provjerava u bazi da li je netko pozvao nas za igrati.
	 * Ako imamo poziv funkcija ispisuje iz koje sobe dolazi zahtjev,
	 * koje osobe su u sobi i za koliko igraca je soba.
	 * @return echo json Sadrzi instancu klase Soba ili false ako nema poziva.
	 */
	public function obradi_poziv_u_sobu()
	{
		$os = new OnlineService();
		$soba = $os->imam_li_poziv($_SESSION['id']);
		if($soba)
			echo json_encode($soba);
		else
			echo json_encode(['poziv' => 'nema_poziva']);

	}

	/**
	 * Funkcija koja po pozivu preko echo json-a
	 * kodira niz trenutno aktivnih soba.
	 * @return echo json Sadrzi niz instanci klase Soba.
	 */
	public function sve_sobe()
	{
		$ss = new SobaService();
		echo json_encode($ss->sve_sobe());
		exit();
	}




////////////////////////////////	SOBA 	/////////////	SOBA 	/////////////////


	/**
	 * Funkcija koja simbolizira stanje u sobi u kojoj se
	 * klijent trenutno nalazi. Ukoliko varijabala id_igre
	 * nije -1 znaci da je igra zapocela i  vrijeme je da se
	 * pridruzimo igri.
	 * Inace vrati sve podatke o sobi.
	 * @return echo json Sadrzi instancu klase Soba
	 */
	public function cekaj_pocetak_igre()
	{
		$ss = new SobaService();
		$soba = $ss->vrati_sobu_po_idu($_SESSION['id_sobe'], $_SESSION['broj_igraca']);
		if($soba->id_igre > 0)
		{
			$_SESSION['id_igre'] = $soba->id_igre;
			//header('Location: '  . __SITE_URL  . "/index.php?rt=igra" );
		}

		echo json_encode($soba);
	}


	/**
	 * Funkcija koja izbaci klijenta iz baze sa sobama,
	 * vrati ga u online bazu i prikaze pocetnu stranicu
	 * online.
	 */
	public function izbaci_iz_sobe()
	{
		$ss = new SobaService();
		$os = new OnlineService();

		$ss->izbaci_iz_sobe($_SESSION['id_sobe'], $_SESSION['broj_igraca'], $_SESSION['username']);

		unset($_SESSION['id_sobe']);
		unset($_SESSION['broj_igraca']);

		$os->ubaci_u_onlines($_SESSION['id']);

		//$this->registry->template->show( 'create_game' );
		echo json_encode("uspjesno sam izbacio iz sobe");
	}


	/**
	 * Funkcija koja oznaci da je klijent spreman za igrati.
	 */
	public function spreman_za_igru()
	{
		$ss = new SobaService();
		$ss->spreman_za_igru($_SESSION['id_sobe'], $_SESSION['broj_igraca'], $_SESSION['username']);
		echo json_encode(['success' => true, 'poruka' => "uspjesno stavljen: Spreman za igru"]);
	}

	/**
	 * Funkcija koja oznaci da je klijent nije spreman za igrati.
	 */
	public function nisam_spreman_za_igru()
	{
		$ss = new SobaService();
		$ss->nisam_spreman_za_igru($_SESSION['id_sobe'], $_SESSION['broj_igraca'], $_SESSION['username']);
		echo json_encode(['success' => true, 'poruka' => "uspjesno stavljen: Nisam spreman za igru"]);
	}


	/**
	 * Funkcija koja stvori novu igru,
	 * i postavi id te igre u bazu u sobu
	 * tako da ostali igraci znaju id igre.
	 * Nakon konstrukcije odlazi u igru.
	 */
	public function konstruiraj_igru()
	{
		$bs = new BriskulaService();
		$ss = new SobaService();

		$_SESSION['id_igre'] = $bs->stvori_novu_igru($_SESSION['id_sobe'], $_SESSION['broj_igraca']);
		$ss->postavi_id_igre_u_sobu($_SESSION['id_sobe'], $_SESSION['broj_igraca'], $_SESSION['id_igre']);

		header('Location: '  . __SITE_URL  . "/index.php?rt=igra" );
		exit();
	}

	/**
	 * Klijent u sobi pomocu POST['id_igraca'] poziva osobu iz onlines u sobu.
	 */
	public function pozovi_osobu_u_sobu()
	{
		$ss = new SobaService();
		$soba = $ss->vrati_sobu_po_idu($_SESSION['id_sobe'], $_SESSION['broj_igraca']);

		$os = new OnlineService();			//gdje je OsobaService?
		$os->postavi_sobu($soba, $_POST['id_igraca']);

		echo json_encode("osoba uspjesno pozvana u sobu");
	}

	public function online_ajax(){
		$ss = new SobaService();
		$imena_aktivnih = array();
		echo json_encode( $imena_aktivnih	);
	}

	public function svi_online_users(){
		$us = new UserService();
		/*$useri = $us->svi_aktivni();
		$data = "";
		foreach($useri as $user)
			$data .= $user->zakodiraj_me();
		echo json_encode($data);*/

		$niz = $us->svi_slobodni();
		if(isset($niz['baza']))
			exit($niz['greska']);
		echo json_encode($niz);

	}

};

?>
