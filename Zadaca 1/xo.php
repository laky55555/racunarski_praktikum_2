
<?php

/*
	Prva zadaca iz RP2.
	Cilj je napraviti igricu krizic kruzic.
*/

/*
	Razred KrizicKruzic se brine za sve djelove igre.
*/
class KrizicKruzic
{
	// Pomocne varijable koje sluze za kontrolu tijeka igre.
	protected $igracNaRedu, $pobjednik, $igracJeKliknuo, $dosloJeDoPromjene;
	// Pomocne varijable koje sluze za notifikaciju greske ili kraja igre
	protected $errorMsg, $gameOver;

	/*
		Konstruktor se poziva na pocetku te inicijalizira igru.
		Igrac prvi na redu je X, te svako polje (gumb za vizualizaciju)
		ima ? sto oznacava da jos niti jedan igrac na njega nije kliknuo.
		Inicijalizira i polje button koje oznacava koju css klasu gumbi koriste,
		sto ce trebati kod farbanja pobjednickih polja.
	*/
	function __construct()
	{
		$this->imeIgraca = array(false, false);
		$this->slovo = array("X", "O");
		$this->igracNaRedu = 0;
		$this->errorMsg = false;
		$this->gameOver = false;
		$this->tipka = array('?', '?', '?', '?', '?', '?', '?', '?', '?');
		$this->pobjednik = 99;
		$this->button = array("button", "button", "button", "button",
                    "button", "button", "button", "button", "button",);
	}


	/*
		Funkcija ispisuje formu za upis imena igraca.
		Ako smo vec pokusali upisati imena i doslo je do greske, poruka
		o gresci se ispisuje.
		Ukoliko igrac klikne na vec zauzeto polje, ispisuje se opet forma
		s porukom da je polje vec zauzeto, te je opet na potezu.
	*/
	function ispisiFormuZaIme()
	{
		// Ispisi formu koja ucitava ime igraca
		?>

		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<title>Križić kružić - početak!</title>
		</head>
		<body>
            <h1>Dobro došli u igru XO!</h1>
			<form method="POST" action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
				Unesite ime igrača X: <input type="text" name="imeIgracaX" />
				</br>
				Unesite ime igrača O: <input type="text" name="imeIgracaO" />
				</br>
				</br>
				<button type="submit">Započni igru!</button>
			</form>

			<?php if( $this->errorMsg !== false )
					echo '<p>Greška: ' . htmlentities( $this->errorMsg ) . '</p>'; ?>
		</body>
		</html>

		<?php
	}


	/*
		Funkcija ispisuje formu za igranje.
		Ako je igra gotova onda u formi ispisuje i tko je pobjedio,
		te boja gumbe koji su donijeli pobjedu.
		Forma ima i gumb koji resetira igru. Nakon klika na gumb igra se
		pokrece na novo.
	*/
	function ispisiFormuZaIgranje()
	{
  		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<title>Križić kružić - igraj</title>
			<style>
                .button{
                    padding: 15px 32px;
                    text-align: center;
                    font-size: 16px;
                }
                .button2{
                    background-color: #008CBA;
                    padding: 15px 32px;
                    text-align: center;
                    font-size: 16px;
                }
            </style>
		</head>
		<body>
        <h1>Dobro došli u igru XO!</h1>
        </br>
        </br>
		<table>

          <tr>
             <td> <form method="POST"  action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="tipka0" />
                <input class=<?php echo $this->button[0]; ?> type="submit" value="<?php echo $this->tipka[0]; ?>"   />
            </form> </td>
            <td> <form method="POST"  action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="tipka1" />
                <input class=<?php echo $this->button[1]; ?> type="submit" value="<?php echo $this->tipka[1]; ?>"  />
            </form> </td>
            <td> <form method="POST"  action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="tipka2" />
                <input class=<?php echo $this->button[2]; ?> type="submit" value="<?php echo $this->tipka[2]; ?>"  />
            </form> </td>
          </tr>

          <tr>
             <td> <form method="POST"  action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="tipka3" />
                <input class=<?php echo $this->button[3]; ?> type="submit" value="<?php echo $this->tipka[3]; ?>"   />
            </form> </td>
            <td> <form method="POST"  action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="tipka4" />
                <input class=<?php echo $this->button[4]; ?> type="submit" value="<?php echo $this->tipka[4]; ?>"  />
            </form> </td>
            <td> <form method="POST"  action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="tipka5" />
                <input class=<?php echo $this->button[5]; ?> type="submit" value="<?php echo $this->tipka[5]; ?>"  />
            </form> </td>
          </tr>

          <tr>
             <td> <form method="POST"  action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="tipka6" />
                <input class=<?php echo $this->button[6]; ?> type="submit" value="<?php echo $this->tipka[6]; ?>"   />
            </form> </td>
            <td> <form method="POST"  action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="tipka7" />
                <input class=<?php echo $this->button[7]; ?> type="submit" value="<?php echo $this->tipka[7]; ?>"  />
            </form> </td>
            <td> <form method="POST"  action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="tipka8" />
                <input class=<?php echo $this->button[8]; ?> type="submit" value="<?php echo $this->tipka[8]; ?>"  />
            </form> </td>
          </tr>

        </table>

			<?php if( $this->errorMsg !== false ) echo '<p>Greška: ' . htmlentities( $this->errorMsg ) . '</p>';

			//sljedeci igrac je na potezu inace imamo pobjednika ili je nerjeseno
			if($this->pobjednik === 99)
            {
                ?> </br> <h2>Na potezu je <?php echo $this->imeIgraca[$this->igracNaRedu]; ?>, igrač: <?php echo $this->slovo[$this->igracNaRedu]; ?> </h2>
                <?php
			}
			else
			{
                if($this->pobjednik === -1)
                {
                    ?> </br> <h2>Nema pobjednika, nerjeseno je.</h2>
                    <?php
                }
                else
                {
                    ?> </br> <h2>Pobjednik je <?php echo $this->imeIgraca[$this->pobjednik]; ?>, igrač: <?php echo $this->slovo[$this->pobjednik]; ?> </h2>
                    <?php
                }
			}


			?>
            </br>
            </br>
			<form method="POST"  action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="reset" />
                <input type="submit" value="Restartaj igru!">  </input>
            </form>

		</body>
		</html>

		<?php
	}


    /*
        Pomocna funkcija koja provjerava jesu li imena igraca unesena, te
        ako nisu postavlja ih ako su dana te ako su legalna.
        Ime moze biti sstavljeno jedino od slova te mora imati izmedu
        1 i 20 znakova.
    */
	function get_imeIgraca()
	{
		// Je li već definirano ime igrača?
		if( $this->imeIgraca[0] !== false && $this->imeIgraca[1] !== false)
            return true;

		// Možda nam se upravo sad šalje ime igrača?
		if( isset( $_POST['imeIgracaX'] ) && isset( $_POST['imeIgracaO'] ))
		{
			// Šalje nam se ime igrača. Provjeri da li se sastoji samo od slova.
			if( !preg_match( '/^[a-zA-Z]{1,20}$/', $_POST['imeIgracaX'] ) || !preg_match( '/^[a-zA-Z]{1,20}$/', $_POST['imeIgracaO'] ) )
			{
				// Nije dobro ime. Dakle nemamo ime igrača.
				$this->errorMsg = 'Ime igrača treba imati između 1 i 20 slova.';
				return false;
			}
			else
			{
				// Dobro je ime. Spremi ga u objekt.
				$this->imeIgraca[0] = $_POST['imeIgracaX'];
				$this->imeIgraca[1] = $_POST['imeIgracaO'];
				return true;
			}
		}

		// Ne šalje nam se sad ime. Dakle nemamo ga uopće.
		return false;
	}


    /*
        Pomocna funkcija koju poziva funkcija obradiPokusaj koja gleda je li
        doslo do klika, te ako je je li klik legalan.
        Brine se za to koji je igrac na redu te mijenja varijable igracJeKliknuo
        i dosloJeDoPromjene koje oznacavaju da li je potez legalan i je li bio.
    */
	function pomocnaObradiPokusaj($broj)
	{
        $slovo = strval($broj);
        if( isset( $_POST['tipka'.$broj] ) )
		{
            $this->igracJeKliknuo = 1;
            if($this->tipka[$broj] === '?' )
            {
                $this->dosloJeDoPromjene = 1;
                $this->tipka[$broj] = $this->slovo[$this->igracNaRedu];
                $this->igracNaRedu = ($this->igracNaRedu + 1) % 2;
            }
		}
	}

    /*
        Pomocna funkcija koja gleda je li igrac kliknuo na neki gumb, te
        ako je kliknuo provjerava je li klik legalan, te je li doslo do
        kraja igre (moguce ako nema vise slobodnih mjesta ili je neki
        igrac pobjedio).
        Ako je doslo do kraja vraca true, inace false.
    */
	function obradiPokusaj()
	{
		// Da li je igrač uopće pokusao pogađati broj?
        $this->igracJeKliknuo = 0;
        $this->dosloJeDoPromjene = 0;
        for($i = 0; $i < 9; $i++)
            $this->pomocnaObradiPokusaj($i);

		if($this->igracJeKliknuo === 0)
            return false;
        else if($this->igracJeKliknuo === 1 && $this->dosloJeDoPromjene === 0)
        {
            $this->errorMsg = 'Pozicija je vec zauzeta';
            return false;
        }
        else
            if($this->imaLiPobjednika())
                return true;

        //igra se nastavlja
		return false;
	}


    /*
        POSTter funckija za provjeru je li igra gotova.
    */
	function isGameOver() { return $this->gameOver; }

    /*
        Funkcija koja obavlja jedan potez igre.
        Ako nemamo imena igraca, postavi imena.
        Pozove funkciju obradiPokusaj koja obradi klik
        (ako ga je bilo).
        Ako je doslo do pobjede nacrta trenutno stanje igre
        s obojanim pobjednicikim potezom, te igru stavi u stanje
        pogodno za destrukciju
        Ako nema pobjednika iscrta igru da iduci igrac moze kliknut na
        polje na koje zeli odigrati.
    */
	function run()
	{
		$this->errorMsg = false;

		// Prvo provjeri jel imamo uopće ime igraca
		if($this->get_imeIgraca() === false)
		{
			// Ako nemamo ime igrača, ispiši formu za unos imena i to je kraj.
			$this->ispisiFormuZaIme();
			return;
		}

        // Dakle imamo ime igrača.
		// Ako je igrač pokušao pogoditi broj, provjerimo što se dogodilo s tim pokušajem.
		$potez = $this->obradiPokusaj();

		if( $potez === true )
		{
			// Ako je igrač pogodio, ispiši mu čestitku.
			if($this->pobjednik !== -1)
                $this->pobjednik = ($this->igracNaRedu + 1) % 2;
            $this->errorMsg = false;
            $this->ispisiFormuZaIgranje();
            $this->gameOver = true;
		}
		else
			$this->ispisiFormuZaIgranje();
	}


    /*
        Pomocna funkcija od imaLiPobjednika, provjerava je su li dane
        pozicije pobjednicke, te ako jesu stavlja novi css razred na
        te tipke i vraca true.
    */
	function pomocnaOdImaLiPobjednika($a, $b, $c)
	{
        if($this->tipka[$a] === $this->tipka[$b] && $this->tipka[$a] === $this->tipka[$c] && $this->tipka[$a] !== '?')
        {
            $this->button[$a] = $this->button[$b] = $this->button[$c] = "button2";
            return true;
        }
	}

    /*
        Funkcija koja ide po svim kombinacijama za pobjedu te vraca
        true ako pobjednik postoji. Poziva pomocnu funkciju koja
        radi direktnu provjeru.
    */
	function imaLiPobjednika()
	{

        //pobjednik ima simbol u gornjem lijevom kutu
        if($this->pomocnaOdImaLiPobjednika(0, 1, 2))
            return true;
        if($this->pomocnaOdImaLiPobjednika(0, 4, 8))
            return true;
        if($this->pomocnaOdImaLiPobjednika(0, 3, 6))
            return true;

        //pobjednik je u sredini vodoravno ili okomito
        if($this->pomocnaOdImaLiPobjednika(3, 4, 5))
            return true;
        if($this->pomocnaOdImaLiPobjednika(1, 4, 7))
            return true;

        //pobjednik ima simbol u donjem lijevom kutu
        if($this->pomocnaOdImaLiPobjednika(6, 7, 8))
            return true;
        if($this->pomocnaOdImaLiPobjednika(6, 4, 2))
            return true;

        //pobjednik je najdesniji stupac
        if($this->pomocnaOdImaLiPobjednika(2, 5, 8))
            return true;

        $i = 0;
        for($i = 0; $i <= 8; $i++)
            if($this->tipka[$i] === '?')
                return false;

        $this->pobjednik = -1;
        return true;
	}

};


// --------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------
// Sad ide "glavni program" -- skroz generički, isti za svaku moguću igru.

// U $_SESSION ćemo čuvati cijeli objekt tipa KrizicKruzic.


/*
    Ako je netko kliknuo na reset igre, igra krece od pocetka (igraci
    moraju unesti imena, te se pokrece igra).
*/
if( isset( $_POST['reset'] ))
{
    echo 'glupost';
    $_SESSION = array();
    if (ini_get("session.use_cookies"))
    {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }

    session_unset();
	session_destroy();
    header('Location: '.$_SERVER['PHP_SELF']);

}

else
{

    session_start();

    if( !isset( $_SESSION['igra'] ) )
    {
        // Ako igra još nije započela, stvori novi objekt tipa KrizicKruzic i spremi ga u $_SESSION
        $igra = new KrizicKruzic();
        $_SESSION['igra'] = $igra;
    }
    else
    {
        // Ako je igra već ranije započela, dohvati ju iz $_SESSION-a
        $igra = $_SESSION['igra'];

    }

    // Izvedi jedan korak u igri, u kojoj god fazi ona bila.

    $igra->run();

    if( $igra->isGameOver() )
    {
        // Kraj igre -> prekini session.
        session_unset();
        session_destroy();
    }
    else
    {
        // Igra još nije gotova -> spremi trenutno stanje u SESSION
        $_SESSION['igra'] = $igra;
    }

}
