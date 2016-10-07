<?php require_once __SITE_PATH . '/view/_header.php'; ?>


<div class="row carousel-parent-style padding">
    <div id="slideshow" data-interval="5000" class="carousel slide carousel-style" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-inner carousel-size">
                <div class="item carousel-style">
                    <img src="view/carousel-pics/briskula1.jpg">
                </div>
                <div class="item active carousel-style">
                    <img src="view/carousel-pics/briskula2.jpg">
                </div>
            </div>
            <div>
                <h1 class="text-center" style="padding-top: 30px;">Dobrodošlli na briškulu!</h1>
            </div>
        </div>
        <a class="left carousel-control" href="#slideshow" data-slide="prev"><i class="icon-prev  fa fa-angle-left"></i></a>
        <a class="right carousel-control" href="#slideshow" data-slide="next"><i class="icon-next fa fa-angle-right"></i></a>
    </div>
</div>

<div class="section text-center centerButton">
    <div class="container">
        <div class="text-center">
            <div class="text-center">
                <p></p>
                <a id="create_game_button" class="active btn btn-lg btn-primary playButtonSize">Pokreni igru</a>
                <p></p>
            </div>
        </div>
    </div>
</div>

<script>
    $('#create_game_button').attr('href','<?php echo __SITE_URL .  "/index.php?online" ?>');
</script>


<div class="section section-info padding" style="background-color: #cccccc;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section">
                    <div class="container ">
                        <div class="row">
                            <div class="col-md-17">
                                <div class="padding">
                                    <h2>Pravila igre:</h2>
                                    <p class="text-info">
                                        Okrenuta karta ispod maca, predstavlja "zog" (boju), u koji se igra. Taj zog je
                                        jači
                                        od svih ostalih, a za to dijeljenje predstavlja briškulu (odnosno adut).
                                    </p>
                                    <p class="text-info">
                                        Postoje 4 zoga: kupe (kope), bate (baštone), špade i dinare. Unutar svakog zoga
                                        postoje karte od broja 1-7 i od 11-13. Unutar jednog zoga, najjači je as (aš)
                                        (1),
                                        pa trica (3), pa kralj (13), konj (12), fanat (11) i dalje od sedmice (7) prema
                                        dvici (2). As i trica se još zovu i "karik" (korga, karig).
                                    </p>
                                    <p class="text-info">
                                        As se broji kao 11 punata (bodova), trica vrijedi 10 punata, a karte 2 i 4-7
                                        zovu se
                                        "lišine" ili "škart" i ne donose punte (usporedi s izrazom lišo bez punta). Kod
                                        ostalih zbrajaju se znamenke pa kralj vrijedi 4 punta, konj 3, a fanat 2 punta.
                                        Prema originalnom pravilu, i karta 2 je nosila 10 punat. Ove zadnje 3 karte sa
                                        slikama, u igri se zbirno nazivaju – figure ili punti. Ima ukupno 120 punata,
                                        tako
                                        da partija može završiti i neriješeno, a za pobjedu je potrebno sakupiti 61
                                        punat.
                                    </p>
                                    <p class="text-info">
                                        Igrači bacaju po jednu kartu naizmjenično, dok svi ne bace, a zatim najjača
                                        strana
                                        pokupi bačene karte. Time je završila jedna "ruka" te partije. Nakon toga svaki
                                        igrač, počevši od pobjednika te ruke, "peška" (uzima) po jednu kartu s maca i
                                        započinje nova ruka, i tako dok se ne odigraju sve karte. Izuzetno, u duploj
                                        bruškuli, ako se igra u dvoje, svaki igrač baca po dvije karte u svakoj
                                        odigranoj
                                        ruci, jednu po jednu, a isto tako i peškaje 2 karte s maca, jednu po jednu.
                                    </p>
                                    <p class="text-info">
                                        Igru započinje igrač kojemu su prvom podijeljene karte. Bačena karta predstavlja
                                        zog
                                        u kojemu se igra ta ruka. Igrač s najjačom kartom te ruke, kupi karte, prvi
                                        uzima
                                        novu kartu s maca i započinje novu ruku. Prva bačena karta može se "ubiti":
                                        <br><br>
                                        &#09   1. jačom kartom istog zoga,
                                        <br><br>
                                        &#09   2. ili bilo kojom briškulom.
                                    </p>
                                    <p class="text-info">
                                        Igrači nisu dužni poštivati boju, niti "iberovati". Ako je ruka dobivena uz
                                        bacanje
                                        karika, to se zove "štrocatura". Npr. za igrača koji je na briškulu svog druga,
                                        u
                                        dobivenoj poziciji bacio karika, kaže se da je: "štroca' karika". Postoji još
                                        jedan
                                        aspekt sagledavanja odigranih karata u igri koji se podrazumjeva pod pojmom
                                        franko
                                        karta iliti frankuša a vezan je za najjače karte koje su u igri a nisu briškule.
                                        Često se pod tim izrazom sagledavaju kraljevi, konji i fanti pa i u datim
                                        trenutcima
                                        i ostale karte istog zoga kojima u igri nisu ostali asevi i trice te se zbog
                                        toga
                                        ukoliko su odigrane prve ne mogu ubiti ničim osim adutom. Pod odigravanjem ovih
                                        karata prvih po redu igranja, protivnika se prisiljava da ubija briškulama
                                        (troši
                                        briškule) karte koje ne nose značajne punte ili da propušta ubiti ili nema uopće
                                        briškula za ubiti takve karte, koje strana koja ih odigra koristi za sakupljanje
                                        što
                                        više punata uz minimalni rizik gubitka punata ukoliko je franko karta presječena
                                        briškulom.
                                    </p>
                                    <p class="text-info">
                                        Specifičnost igre u parovima jest da je dopuštena komunikacija drugova unutar
                                        para
                                        za cijelo vrijeme igre osim zadnje ruke. Kada igrači peškaju zadnje karte s
                                        maca,
                                        daljnja komunikacija mora prestati. Međutim tada drug drugu pruža svoje karte,
                                        koje
                                        se pregledaju i zatim vrate, pa se u tišini odigra zadnja ruka te partije, nakon
                                        čega se pristupi brojenju dobijenih punata i proglašavanju pobjednika te
                                        partije.
                                        Postoji varijanta da obje strane imaju po 60 punata, tada igrač koji je mješao
                                        tu
                                        ruku ponovno miješa i dijeli karte a partija se kaže da je ostala bez pobjednika
                                        ili
                                        po šezdeset.
                                    </p>
                                    <p class="text-info">
                                        Igra se na 4 dobijene partije. Evidencija o pobjedama se vodi tako da se na
                                        papiru
                                        nacrta jedna okomita crta i preko nje 4 vodoravne crte. Za svaku pobjedu
                                        označava se
                                        crna točka na strani pobjednika. Ako jedna strana završi partiju sa 4 pobjede, u
                                        4
                                        odigrane partije – to se zove "češalj".
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
