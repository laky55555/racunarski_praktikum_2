<?php 

class PredmetiController extends BaseController
{

	/**
		Funkcija koja poziva view koji ce ispisati sve predmete u trgovini.
		Popuni template predmetiList sa svim predmetima u bazi.
	*/
	public function index() 
	{
		$ls = new TrgovinaService();

		// Popuni template potrebnim podacima
		$this->registry->template->title = 'Popis svih predmeta u trgovini';
		$this->registry->template->predmetiList = $ls->getAllPredmeti();

      	$this->registry->template->show( 'predmeti_index' );
	}


	/**
		Funkcija koja ce pozvati view koji ce prikazati sve komentare na odabrani predmet koji
		primi po ID-u u obliku predmet_xxx gdje je xxx ID predmeta. View ce imati i mogucnostu upisa
		nove recenzije. Funkcija moze primiti predmet_id pomocu POST i GET metode.
	*/
	public function showRecenzije()
	{
		
		$ls = new TrgovinaService();


		// predmet_id iz post/get-a izgleda ovako "predmet_123" -> pravi id je zapravo samo 123 -> preskoči prvih 8 znakova
		if( isset( $_POST["predmet_id"] ) )
			$predmet_id = substr( $_POST["predmet_id"], 8 );
		else if( isset( $_GET["predmet_id"] ) )
			$predmet_id = substr( $_GET["predmet_id"], 8 );
		else
		{
			// Nema treće opcije -- nešto ne valja. Preusmjeri na početnu stranicu.
			header( 'Location: ' . __SITE_URL . '/index.php?rt=predmeti' );
			exit;
		}

		// Dohvati podatke o korisniku
		$predmet = $ls->getPredmetById( $predmet_id );
		if( $predmet === null )
			exit( 'Nema predmeta s id-om ' . $predmet_id );

		// Dohvati sve njegove recenzije
		$recenzijeList = $ls->getRecenzijeByPredmetId( $predmet_id );

		// Napravi popis recenzija koje predmet ima.
		// Svaki element liste recenzija je par (osoba, ocjena, recenzija)
		$recenzijeListZaIspis = array();
		foreach( $recenzijeList as $recenzija )
			$recenzijeListZaIspis[] = array( "user" => $ls->getUserById( $recenzija->id_user ), 
				                       "ocjena" => $recenzija->ocjena,
				                       "recenzija" => $recenzija->recenzija );


		$this->registry->template->predmet_id = $predmet_id;
		$this->registry->template->recenzijeListZaIspis = $recenzijeListZaIspis;
		$this->registry->template->title = 'Popis recenzija za predmet: ' . $predmet->naziv;
        $this->registry->template->show( 'predmeti_showRecenzije' );
	}	


	/**
		Funkcija koja ce recenziju koju je korisnik tek unio obraditi.
		Provjerava je li korisnik postoji/ulogiran, te da li je unio ocjenu.
		Ako su svi parametri zadovoljeni ubacuje novu recenziju u bazu.
		Na kraju osvjezava stranicu s novom recenzijom koja je tek upisana. 
	*/
	public function napisiRecenziju()
	{
		$ls = new TrgovinaService();

		// Ako nismo ulogirani otvori stranicu za logiranje
		if( !isset( $_SESSION['username' ] ) || !$ls->haveUserInDB($_SESSION['username' ]) )
		{
			$this->registry->template->errorMsg = 'Za recenzirati predmet potrebno je biti ulogiran.';
   		    $this->registry->template->show( 'prijava_logiranje' );
   			exit();
		}

		$user_id = $ls->getUserByUsername($_SESSION[ 'username' ])->id;

		if( isset( $_POST["predmet_id"] ) )
		{
			// predmet_id iz post-a izgleda ovako "predmet_123" -> pravi id je zapravo samo 123 -> preskoči prvih 5 znakova
			$predmet_id = substr( $_POST["predmet_id"], 8 );
		}
		else
		{
			// Nema treće opcije -- nešto ne valja. Preusmjeri na početnu stranicu.
			header( 'Location: ' . __SITE_URL . '/index.php?rt=predmeti' );
			exit;
		}

		if( !isset($_POST["ocjena"]) )
		{
			// Ocjenu moramo dodati.
			$this->registry->template->poruka = 'Za recenzirati ocjena je nužna.';
			header( 'Location: ' . __SITE_URL . '/index.php?rt=predmeti/showRecenzije&predmet_id=predmet_'.$predmet_id );
			exit;
		}

		$ocjena = $_POST["ocjena"];
	
		$recenzija = $_POST["recenzija"];
		if( (!isset($_POST["recenzija"])) )
			$recenzija = '';
	
		$ls->makeNewRecenzija( $predmet_id, $user_id, $ocjena, $recenzija );
		$this->registry->template->poruka = 'Recenzija je uspješno spremljena.';
		header( 'Location: ' . __SITE_URL . '/index.php?rt=predmeti/showRecenzije&predmet_id=predmet_'.$predmet_id );
		exit();
	}

}; 

?>
