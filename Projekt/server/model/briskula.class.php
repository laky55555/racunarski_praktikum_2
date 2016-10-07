<?php

class Briskula
{
    /**
     * Int koji oznacava za koliko igraca je igra (2 ili 4).
     */
    private $broj_igraca;
    /**
     * Int koji ozvacava s koliko karata se igra, 3 ako s tri karte, 4 ako s cetiri karte.
     */
    private $dupla;
    /**
     * Array intova koji oznacavanju karte u ruci. Uvijek ima broj_igraca * dupla elemenata,
     * Ako nema vise karata na odgovarajuce pozicije stavlja -1.
     */
    private $karte_u_ruci;
    /**
     * Pomocna varijabla koja ima ukupan broj karata u rukama svih igraca ukljucujuci i -1 karte.
     */
    private $broj_karti_u_ruci;
    /**
     * Array intova koji oznacava sve karte u spilu. Ako nema karata onda je prazan.
     */
    private $karte_u_spilu;
    /**
     * Array intova koji ima spremljeno sve izasle karte. Ako nema izaslih onda je prazan.
     */
    private $karte_izasle;
    /**
     * Int koji oznacava briskulu.
     */
    private $karte_briskula;
    /**
     * Array intova koji ima spremljene sve bacene karte u trenutnoj rundi.
     */
    private $karte_bacene_u_rundi;
    /**
     * Niz od 2 inta koji oznacava bodove 1
     */
    private $bodovi;
    /**
     * Int koji oznacava igraca koji je trenutno igra.
     */
    private $igrac_na_potezu;
    /**
     * Int koji oznacava koliko je igraca odigralo u rundi. (0,1,2...,broj_igraca)
     */
    private $broj_odigralih;
    /**
     * String u kojem su spremljena sva imena igraca odvojena zarezom.
     */
    private $imena_igraca;
    /**
     * Int koji je id igre u bazi podataka.
     */
    private $id;
    /**
     * Int koji oznacava je li igra gotova, tj. je su li svi potezi odigrani.
     */
    private $gotova_igra;


    function __get( $prop ) { return $this->$prop; }

    /**
     * Konstruktor briskule. Poziva se s cijelim redkom iz tablice.
     * Konstruktor provjeri jeli igra vec u tijeku, ako je izvrsi obradu poteza inace konstruira novu igru.
     * Igra jos nije pokrenuta i zahtjeva inicijalizaciju.
     * @param array $igra Citav redak igre iz baze podataka.
     * @param int $broj_igraca Upisuje se samo kod inicijalizacije, oznacava koliko ce igraca igrati igru.
     * @param int $dupla Upisuje se samo kod inicijalizacije, oznacava koliko ce karti svaki igrac imat u rukama.
     * @param string $imena_igraca String u kojem su spremljena sva imena igraca koji sudjeluju u igri.
     */
    public function __construct($igra, $broj_igraca = -1, $dupla = -1, $imena_igraca = -1)
    {
        if($igra === false)
        {
            $this->broj_igraca = $broj_igraca;
            $this->dupla = $dupla;
            $this->broj_karti_u_ruci = $this->broj_igraca * $this->dupla;
            $this->imena_igraca = $imena_igraca;
            $this->id = -1;
            $this->postavi_pocetak_igre();

        }
        else
        {
            $this->karte_u_ruci = array();
            if($igra['karte_u_ruci'] !== '')
                $this->karte_u_ruci = array_map('intval', explode(', ', $igra['karte_u_ruci']));

            $this->karte_u_spilu = array();
            if($igra['karte_u_spilu'] !== '')
                $this->karte_u_spilu = array_map('intval', explode(', ', $igra['karte_u_spilu']));

            $this->karte_izasle = array();
            if($igra['karte_izasle'] !== '')
                $this->karte_izasle = array_map('intval', explode(', ', $igra['karte_izasle']));

            $this->karte_bacene_u_rundi = array();
            if($igra['karte_bacene_u_rundi'] !== '')
                $this->karte_bacene_u_rundi = array_map('intval', explode(', ', $igra['karte_bacene_u_rundi']));

            $this->bodovi = array_map('intval', explode(', ', $igra['bodovi']));
            $this->karte_briskula = $igra['karte_briskula'];
            $this->igrac_na_potezu = $igra['igrac_na_potezu'];
            $this->broj_odigralih = $igra['broj_odigralih'];
            $this->imena_igraca = $igra['imena_igraca'];
            $this->gotova_igra = $igra['gotova_igra'];
            $this->dupla = $igra['dupla'];
            $this->broj_igraca = $igra['broj_igraca'];
            $this->id = $igra['id'];
            $this->broj_karti_u_ruci = $this->broj_igraca * $this->dupla;
        }



	}

    /**
     * Funkcija koja inicijalizira igru. Poziva se samo jednom na pocetku. Izmjesa karte, i podijeli ih igracima.
     * Postavi bodove, briskulu, broj_odigralih i igrac_na_potezu.
     */
    private function postavi_pocetak_igre()
    {
        $this->karte_u_spilu = range(0, 39);
        shuffle($this->karte_u_spilu);

        $this->karte_u_ruci = array();
        for($i = 0;   $i < $this->broj_karti_u_ruci;   $i++)
            $this->karte_u_ruci[] = array_pop($this->karte_u_spilu);

        $this->karte_briskula = array_pop($this->karte_u_spilu);
        array_unshift($this->karte_u_spilu, $this->karte_briskula);

        $this->bodovi = array(0, 0);
        $this->broj_odigralih = 0;
        $this->igrac_na_potezu = 0;
        $this->gotova_igra = 0;

        $this->karte_izasle = array();
        $this->karte_bacene_u_rundi = array();

    }

    /**
     * Funkcija koja sve varijable u klasi stavlja u polje redak.
     * Takav redak je spreman za update u bazi podataka.
     * @return Polje popunjeno sa svim vrijednostima potrebnim za update baze podataka.
     */
    public function vrati_redak()
    {
        $redak = array();
        $redak['id'] = strval($this->id);
        $redak['broj_odigralih'] = strval($this->broj_odigralih);
        $redak['broj_igraca'] = strval($this->broj_igraca);
        $redak['dupla'] = strval($this->dupla);
        $redak['igrac_na_potezu'] = strval($this->igrac_na_potezu);
        $redak['karte_briskula'] = strval($this->karte_briskula);
        $redak['gotova_igra'] = strval($this->gotova_igra);

        $redak['karte_u_ruci'] = implode(', ', $this->karte_u_ruci);

        $redak['karte_u_spilu'] = '';
        if(!empty($this->karte_u_spilu))
            $redak['karte_u_spilu'] = implode(', ', $this->karte_u_spilu);

        $redak['karte_bacene_u_rundi'] = '';
        if(!empty($this->karte_bacene_u_rundi))
            $redak['karte_bacene_u_rundi'] = implode(', ', $this->karte_bacene_u_rundi);

        $redak['karte_izasle'] = '';
        if(!empty($this->karte_izasle))
            $redak['karte_izasle'] = implode(', ', $this->karte_izasle);

        $redak['bodovi'] = implode(', ', $this->bodovi);
        $redak['imena_igraca'] = $this->imena_igraca;

        return $redak;

    }

    /**
     * Kljucna funkcija klase. Brine se o svemu. Poziva se svaki put nakon bacene karte.
     * Azurira podatke o kartama te ovisno o tome je li kraj runde pobrine se za podjelu
     * novih karata, provjeru je li igra gotova tko sljedeci igra ...
     * @param int $bacena_karta Broj koji oznacava koju kartu je igrac bacio.
     */
    public function obradi_potez($bacena_karta)
    {
        $karte_igraca_na_redu = array();
        for ($i = $this->igrac_na_potezu * $this->dupla; $i < ($this->igrac_na_potezu+1) * $this->dupla; $i++)
            $karte_igraca_na_redu[] = $this->karte_u_ruci[$i];

        if(in_array($bacena_karta, $karte_igraca_na_redu) === false)
            exit('Netko je prckao po POST-u');

        $this->karte_bacene_u_rundi[] = $bacena_karta;
        $this->karte_izasle[] = $bacena_karta;
        $this->broj_odigralih++;
        $this->igrac_na_potezu = ($this->igrac_na_potezu+1)%$this->broj_igraca;

        if($this->broj_odigralih == $this->broj_igraca * ($this->dupla-2) )
            $this->obradi_kraj_runde();
    }


    /**
     * Pomocna funkcija koju poziva obradi_potez() na kraju svake runde.
     * Funkcija se brine za to da se bodovi azuriraju,
     * bacene karte izbace iz ruke, podijeli nove karte
     * i odluci tko ce igrati slijedeci. Ako je odigrana zadnja runda postavi
     * varijablu gotova_igra na 1.
     */
    private function obradi_kraj_runde()
    {
        list($uzeo_rundu, $broj_bodova) = $this->trenutno_uzima();

        $this->bodovi[$uzeo_rundu % 2] += $broj_bodova;

        $this->dodjeli_karte($uzeo_rundu);

        $this->karte_bacene_u_rundi = array();
        $this->igrac_na_potezu = $uzeo_rundu;
        $this->broj_odigralih = 0;

        if($this->provjera_kraja_igre())
            $this->gotova_igra = 1;
    }

    /**
     * Funkcija koja provjerava jesu li svi potezi odigrani, tj. je su li
     * sve karte bacene.
     * @return Boolean, true ako je igra gotova inace false
     */
    private function provjera_kraja_igre()
    {
        for( $i = 0; $i < $this->broj_karti_u_ruci; $i++ )
            if($this->karte_u_ruci[$i] != -1)
                return false;
        return true;
    }

    /**
     * Pomocna funkcija koju poziva obradi_kraj_runde() koja se
     * brine o tome da igraci dobiju odgovarajucu kartu na kraju runde.
     * @param int $uzeo_rundu Broj koji oznacava igraca koji je uzeo rundu, njemu prvom dijelimo.
     */
    private function dodjeli_karte($uzeo_rundu)
    {
        $potrebne_promjene = $this->broj_igraca;
        $pocetak_trazenja = $uzeo_rundu*$this->dupla;
        while ($potrebne_promjene != 0)
        {
            if(in_array($this->karte_u_ruci[$pocetak_trazenja], $this->karte_bacene_u_rundi))
            {
                if(empty($this->karte_u_spilu))
                    $this->karte_u_ruci[$pocetak_trazenja] = -1;
                else
                    $this->karte_u_ruci[$pocetak_trazenja] = array_pop($this->karte_u_spilu);
                $potrebne_promjene--;
            }

            $pocetak_trazenja = ($pocetak_trazenja + 1) % $this->broj_karti_u_ruci;
        }
    }

    /**
     * Pomocna funkcija koja racuna tko je pobjedio u rundi, tj tko uzima rundu.
     * @return Polje brojeva u kojem 1. broj oznacava broj igraca koji je uzeo, a 2. koliko bodova je uzeo.
     */
    private function trenutno_uzima(){
        $najveca_briskula = -1;
        $najveca_prva_boja = $this->karte_bacene_u_rundi[0];
        $bodovi = 0;
        $broj_bacenih_karti = $this->broj_igraca * ($this->dupla - 2);

        for($i = 0; $i < $broj_bacenih_karti; $i++)
        {
            if((int)($this->karte_bacene_u_rundi[$i]/10) == (int)($this->karte_briskula/10) && $najveca_briskula < $this->karte_bacene_u_rundi[$i])
                $najveca_briskula = $this->karte_bacene_u_rundi[$i];

            if((int)($this->karte_bacene_u_rundi[$i]/10) == (int)($najveca_prva_boja/10) && $this->karte_bacene_u_rundi[$i] > $najveca_prva_boja)
                $najveca_prva_boja = $this->karte_bacene_u_rundi[$i];

            $bodovi += $this->bodovi_karte($this->karte_bacene_u_rundi[$i]);
        }


        if($najveca_briskula != -1)
        {
            for($i=0;  $i < $broj_bacenih_karti;  $i++)
            {
                if($najveca_briskula == $this->karte_bacene_u_rundi[$i])
                    return array(($i+$this->igrac_na_potezu) % $this->broj_igraca, $bodovi);
            }
        }

        for($i=0; $i < $broj_bacenih_karti; $i++)
        {
                if($najveca_prva_boja == $this->karte_bacene_u_rundi[$i])
                   return array(($i+$this->igrac_na_potezu) % $this->broj_igraca, $bodovi);
        }

    }

    /**
     * Funkcija koja prima kartu te vraca broj bodova koji ta karta nosi.
     * @param int $karta Broj koji simbolizira jednu kartu kojoj zelimo doznati vrijednost.
     * @return Int koji oznacava bodove koje karta nosi.
     */
    private function bodovi_karte($karta)
    {
        if($karta%10 == 0 || $karta%10 == 1 || $karta%10 == 2 || $karta%10 == 3 || $karta%10 == 4)
            return 0;
        else if($karta%10 == 5)
            return 2;
        else if($karta%10 == 6)
            return 3;
        else if($karta%10 == 7)
            return 4;
        else if($karta%10 == 8)
            return 10;
        else
            return 11;
    }


    /**
     * Funkcija koja vraca koliko ima karata u spilu.
     * @return Int koja oznacava broj karti u spilu.
     */
    public function broj_karti_u_spilu()
    {
        return count($this->karte_u_spilu);
    }


    /**
     * Funkcija koja vraca niz karti koje su izasle u prosloj rundi.
     * @return Array koji sadrzi brojeve karti koje su izasle u prosloj rundi.
     */
    public function izasle_u_prosloj()
    {
        $broj_bacenih_karti_u_rundi = count($this->karte_bacene_u_rundi);
        $niz = [];
        if(count($this->karte_izasle) >= $this->broj_igraca )
            for ($i=0; $i < $this->broj_igraca; $i++) {
                $niz[] = $this->karte_izasle[count($this->karte_izasle)-$i-$broj_bacenih_karti_u_rundi-1];
            }

        return array_reverse($niz);
    }

}

?>
