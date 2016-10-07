<?php

class IgraController extends BaseController
{


	/**
	 * Pocetna funkcija koja se pozove.
	 * Ako ne postoji username u sessionu, vratimo klijenta na stranicu za logiranje.
	 * Ako postoji username, ovisno o tome da li igra vec postoji nastavimo igrati ili
	 * stvorimo novu igru.
	 * Radi sigurnosti mozda bi trebalo napraviti provjeru da li postoji username i game id u bazi, ali to kasnije.
	 */
	public function index()
	{
		// Ako je netko prckao i dosao na stranicu a nema username, vrati ga na stranicu za login.
		if(!isset($_SESSION['username']))
		{
			header('Location: '  . __SITE_URL  . "/index.php?rt=prijava" );
			exit();
		}

		$bs = new BriskulaService();

		// Ako ne znamo id igre, treba pronaci igru u kojoj smo aktivni.
		if(!isset($_SESSION['id_igre']) || !$bs->session_id_je_valjan($_SESSION['id_igre'], $_SESSION['username']))
		{
			//exit("index u IgraContoller u id_igre nije setan");
			$rezultat = $bs->pronadi_igru_po_username($_SESSION['username']);

			if($rezultat)
				$_SESSION['id_igre'] = $rezultat;
			else
			{
				$bs->stvori_novu_igru();
				header('Location: '  . __SITE_URL  . "/index.php?rt=igra" );
				exit();
			}

		}
		$this->registry->template->show( 'playGame' );
		exit();

	}


	/**
	 * Funkciju koju ce igraci pozivati da zanaju sto se dogada u igri.
	 * Funkcija vraca kodirano svo znanje sto igracima treba za aktivnu igru.
	 * Vraca array koji sadrzi: trenutne karte igraca, tko sve igra, koja je briskula,
	 * karte koje su vec bacene i 0/1 ovisno je li igrac na potezu, karte izasle
	 * u proslom potezu i koliko jos ima karti u spilu.
	 */
	public function stanje_igre()
	{
		$bs = new BriskulaService();
		echo json_encode ($bs->vrati_stanje_igre($_SESSION['id_igre'], $_SESSION['username']));
	}



	/**
	 * Funkcija koja obraduje bacenu kartu. U nju se dolazi nakon sto klijent klikne na kartu,
	 * te pomocu POST varijable vidimo koju je kartu odabrao te obradimo potez.
	 */
	public function obradi_bacanje()
	{
		$bs = new BriskulaService();

		$bs->bacio_kartu($_SESSION['id_igre'], $_POST['karta']);
		echo json_encode(['poruka' => 'uspjesno smo obradili bacanje']);


		/*header('Location: '  . __SITE_URL  . "/index.php?rt=igra" );
		exit();*/

	}



	/*/
	 * Poziva se nakon zavrsetka igre.
 	 * Funkcija pozove pomocnu funkciju koja izbrise igru iz baze te Postavi
 	 * u sobi -1 na igru.
 	 * Funkcija pozove pomocnu funkciju koja u bazi update-a omjer pobjada/poraza.
 	 */
	public function kraj_igre()
	{
		$bs = new BriskulaService();

		$kraj = $bs->obrada_kraja($_SESSION['id_igre'], $_SESSION['id_sobe'], $_SESSION['broj_igraca']);
		//exit("u igraCtrl" . $kraj[0] . ", " . $kraj[1]);
		if($kraj[0] !== "")
		{
			$us = new UserService();

			$imena = explode(", ", $kraj[0]);
			$bodovi = explode(", ", $kraj[1]);

			$i = 0;
			foreach($imena as $ime)
			{
				$rez = intval($bodovi[$i]);
				if($rez < 60)
					$rez = -1;
				elseif ($rez == 60)
					$rez = 0;
				else
					$rez = 1;

				$us->dodaj_odigranu($ime, $rez);

				$i = 1 - $i;
			}
		}

		unset($_SESSION['id_igre']);
		header('Location: '  . __SITE_URL  . "/index.php?rt=online" );
		exit();
	}


	/**
	 * Poziva se ukoliko igrac zeli odustati od trenutne igre.
	 * Funkcija pozove pomocnu funkciju koja izbrise igru iz baze te Postavi
	 * u sobi -1 na igru.
	 * Funkcija pozove pomocnu funkciju koja u bazi update-a omjer pobjada/poraza.
	 */
	public function odustajem()
	{
		$bs = new BriskulaService();

		$kraj = $bs->obrada_kraja($_SESSION['id_igre'], $_SESSION['id_sobe'], $_SESSION['broj_igraca']);

		if($kraj[0] !== "")
		{
			$us = new UserService();

			$imena = explode(", ", $kraj[0]);
			$moj_indeks = (array_search($_SESSION['username'], $imena))%2;
			$i = 0;
			foreach($imena as $ime)
			{
				if($i == $moj_indeks)
					$us->dodaj_odigranu($ime, -1);
				else
					$us->dodaj_odigranu($ime, 1);

				$i = 1 - $i;
			}
		}

		unset($_SESSION['id_igre']);
		header('Location: '  . __SITE_URL  . "/index.php?rt=online" );
		exit();
	}


};

?>
