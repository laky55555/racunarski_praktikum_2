<?php

class Soba
{

    /**
     *   Id sobe, jedinstvo odreden broj sobe u bazi.
     * @var integer
     */
    public $id_sobe = -1;

    /**
     * Broj igraca u planiranoj igri. Potrebno da znamo koju tablicu u bazi gledamo.
     * @var integer
     */
    public $broj_igraca = -1;

    /**
     * Niz stringova koji sadrzi imena igraca. Ako igraca nema sadrzi prazan string.
     * @var array
     */
    public $igraci = array();

    /**
     * Niz boolean vrijednosti. Uvijek false dok igrac eksplicitno ne promjeni stanje u 
     * spreman. Tada poprima true.
     * @var array
     */
    public $potvrde = array();

    /**
     * Id igre koja ce se igrati. Dok se ne postavi -1.
     * @var integer
     */
    public $id_igre = -1;

    /**
     * Datum i vrijeme posljednje izmjene u sobi
     * @var timestamp
     */
    public $posljednja_promjena;

    /**
     * Vrijednost ovisno o tome postoji li soba ili ne.
     * @var boolean
     */
    public $postoji;

    //GETTER MOZDA NECE NI TREBATI
    /**
     * Getter varijabli.
     * @param  string $prop  Ime varijable koju trazimo.
     * @return Vraca vrijednost trazene varijable.
     */
    function __get( $prop ) { return $this->$prop; }


    /**
     * Konstruira objekt sobu iz danog redka u tablici.
     * @param array $soba Niz sa svim elementima koje soba treba za konstrukciju.
     */
    public function __construct($soba)
    {
        if($soba !== false)
        {
            $this->postoji = true;

            $this->id_sobe = $soba['id'];
            $this->broj_igraca = $soba['broj_igraca'];
            $this->id_igre = $soba['id_igre'];
            $this->posljednja_promjena = $soba['posljednja_promjena'];
        
            for ($i=0; $i < $this->broj_igraca ; $i++) 
            { 
                $this->igraci[] = $soba["igrac" . $i];
                $this->potvrde[] = $soba["potvrda" . $i];
            }

        }
        else
            $this->postoji = false;

    }

    /**
     * Pomocna funkcija koja iz danog username-a vraca njegov index.
     * Ako te osobe nema vraca -1;
     * @param  string $username Ime igraca kojeg trazimo.
     * @return int              Vraca indeks danog igraca, ako ne postoji -1.
     */
    private function moj_index($username)
    {
        for ($i=0; $i < $this->broj_igraca; $i++) 
            if($this->igraci[$i] === $username)
                return $i;

        return -1;
    }

    /**
     * Funkcija ubacuje igraca dobivenog preko parametra u sobu.
     * @param  string $username Ime igraca kojeg zelimo ubaciti u sobu.
     * @return boolean Vraca true ako je operacija uspjela, inace false.
     */
    public function ubaci_u_sobu($username)
    {
        for ($i=0; $i < $this->broj_igraca; $i++) 
        { 
            if($this->igraci[$i] === "")
            {
                $this->igraci[$i] = $username;
                return true;
            }
        }

        return false;
    }

    /**
     * Funkcija izbacuje igraca dobivenog preko parametra iz sobe.
     * @param  string $username Ime igraca kojeg zelimo izbaciti iz sobe.
     * @return boolean Vraca true ako je operacija uspjela, inace false.
     */
    public function izbaci_iz_sobe($username)
    {
        $moj_index = $this->moj_index($username);
        
        if($moj_index == -1)
            return false;

        $this->igraci[$moj_index] = "";
        $this->potvrde[$moj_index] = 0;
        return true;
    }

    /**
     * Funkcija mijenja status igraca dobivenog preko parametra u spreman za igru.
     * @param  string $username Ime igraca kojem zelimo promjeniti status spreman.
     * @return boolean Vraca true ako je operacija uspjela, inace false.
     */
    public function spreman($username)
    {
        $moj_index = $this->moj_index($username);
        
        if($moj_index == -1)
            return false;

        $this->potvrde[$moj_index] = true;
        return true;
    }

    /**
     * Funkcija mijenja status igraca dobivenog preko parametra u nije spreman za igru.
     * @param  string $username Ime igraca kojem zelimo promjeniti status spreman.
     * @return boolean Vraca true ako je operacija uspjela, inace false.
     */
    public function nisam_spreman($username)
    {
        $moj_index = $this->moj_index($username);
        
        if($moj_index == -1)
            return false;

        $this->potvrde[$moj_index] = false;
        return true;
    }


    /**
     * Funkcija koja postavlja id igre. Ako je id vec postavljen doslo je do 
     * neke pogreske.
     * @param  int $id_igre Id igre iz tablice igre koji oznacava igru.
     * @return boolean Vraca true ako je operacija uspjela, inace false.
     */
    public function postavi_id_igre($id_igre)
    {
        if($this->id_igre != -1)
            return false;

        $this->id_igre = $id_igre;
        return true;
    }


    /**
     * Funkcija koja provjerava postoji li jos netko u sobi.
     * @return boolean Vraca true ako je soba nije prazna, inace false.
     */
    public function nije_prazna()
    {
        for ($i=0; $i < $this->broj_igraca; $i++)  
            if($this->igraci[$i] !== "")
                return true;

        return false;
    }

}

?>
