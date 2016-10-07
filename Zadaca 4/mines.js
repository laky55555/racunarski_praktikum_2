//Postavimo sve vrijednosti koje su nam potrebne kao globalne varijable.
var id = -1,
    nRows = 0,
    nCols = 0,
    nMines = 0,
    brojOtkrivenih = 0;


/**
 * Lijevim klikom misa na tipku Zapocni novu igru pozove se funkcija za konstrukciju nove igre.
 */
$(document).ready(function() {
    $("#btn").on("click", zapocniNovuIgru);
});


/**
 * Pomocna funkcija koja provjerava korisnikov upis.
 * Ako korisnik nije upisao prirodne brojeve, ili su upisani brojevi
 * izvan granica funkcija vraca false.
 * @return boolean Vraca true ako su parametri odgovarajuci, inace false.
 */
function provjeraParametara() {
    var int = /^(0|[1-9]\d*)$/;
    if (!int.test(nRows) || !int.test(nCols) || !int.test(nMines)) {
        alert("Retci, stupaci i mine moraju biti prirodni brojevi.");
        return false;
    }

    var brojR = parseInt(nRows);
    var brojS = parseInt(nCols);
    var brojM = parseInt(nMines);
    if (brojR < 1 || brojR > 20) {
        alert("Broj redaka mora biti izmedu 1 i 20");
        return false;
    }
    if (brojS < 1 || brojS > 20) {
        alert("Broj stupaca mora biti izmedu 1 i 20");
        return false;
    }
    if (brojM < 0 || brojM > brojS * brojR) {
        alert("Broj mina mora biti izmedu 0 i " + (brojS * brojR));
        return false;
    }

    return true;
}

/**
 * Funkcija pogleda koji su podaci upisani u varijable za broj stupaca, redaka i broj
 * mina te posalje serveru te podatke. Ako je proslo sve uredu od servera prima id igre
 * po kojem se moze dohvatiti trenurno stanje igre. Ako je doslo do greske u konzolu ispise
 * sto se je dogodilo.
 * Nakon postavljanja id-a funkcija poziva funkciju koja ispisuje sucelje igre.
 */
function zapocniNovuIgru() {
    nRows = $("#nRow").val();
    nCols = $("#nCol").val();
    nMines = $("#nMine").val();
    brojOtkrivenih = 0;

    if (provjeraParametara()) {
        $.ajax({
            url: "http://rp2.studenti.math.hr/~zbujanov/dz4/getGameId.php",
            type: "GET",
            data: {
                nRows: encodeURI(nRows),
                nCols: encodeURI(nCols),
                nMines: encodeURI(nMines)
            },
            dataType: "jsonp",
            success: function(data) {
                if (typeof(data.error) !== "undefined") {
                    //console.log( "zapocniNovuIgru :: error :: server javio grešku " + data.error );
                    dosloDoGreske("zapocniNovuIgru :: error :: server javio grešku " + data.error);
                } else {
                    //console.log( "zapocniNovuIgru :: success :: data = " + JSON.stringify( data ) );
                    id = data.id;
                    nacrtajIgru();
                    $(".pokriven").on("click", posaljiPolje).on("contextmenu", postaviZastavicu);
                }
            },
            error: function(xhr, status) {
                if (status !== null) {
                    //console.log( "zapocniNovuIgru :: greška pri slanju poruke (" + status + ")" );
                    dosloDoGreske("zapocniNovuIgru :: greška pri slanju poruke (" + status + ")");
                }
                dosloDoGreske();
            }
        });

    }

}

/**
 * Pomocna funkcija koja postavlja, odnosno mice zastavicu s kliknutog mjesta ovisno o tome
 * nalazi li se vec zastavica u polju.
 * @return boolean Vraca false tako da na desni klik ne iskoci klasicni meni desnog klika.
 */
function postaviZastavicu() {
    if ($(this).html() === '<img src="flag.png" alt="z" height="30" width="30">')
        $(this).html("");
    else
        $(this).html('<img src="flag.png" alt="z" height="30" width="30">');

    return false;
}


/**
 * Funkcija koju se pozove nakon sto korisnik odluci napraviti novu igru.
 * Funkcija izbrise prijasnju igru ako postoji, te konstruira sucelje za novu.
 */
function nacrtajIgru() {

    $("#tablica").empty();

    var tablica = "";
    for (var i = 0; i < nRows; i++) {
        tablica += "<tr>";

        for (var j = 0; j < nCols; j++) {
            var broj = i * nRows + j;
            tablica += '<td class="polje pokriven" id="poljeIgre' + broj + '" value="' + i + ', ' + j + '"></td>';
        }

        tablica += "</tr>";
    }

    $("#tablica").append(tablica);
}

/**
 * Funkcija koja prilikom klika na button posalje serveru poruku na koji button je doslo do klika,
 * te ovisno o odgovoru servera azurira stanje igre.
 */
function posaljiPolje() {
    var klik = $(this);
    var unos = klik.attr("value").split(", ");

    $.ajax({
        url: "http://rp2.studenti.math.hr/~zbujanov/dz4/uncoverField.php",
        type: "GET",
        data: {
            id: id,
            row: encodeURI(unos[0]),
            col: encodeURI(unos[1])
        },
        dataType: "jsonp",
        success: function(data) {
            if (typeof(data.error) !== "undefined") {
                // Ipak je došlo do greške!
                //console.log( "posaljiPolje :: error :: server javio grešku " + data.error );
                dosloDoGreske("posaljiPolje :: error :: server javio grešku " + data.error);
            } else {
                //console.log("posaljiPolje :: success :: data = " + JSON.stringify(data));
                if (data.boom == true) {
                    klik.html('<img src="bomb.png" alt="*" height="20" width="20">').attr("class", "polje otkriven");
                    otvorioBombu();
                } else
                    promjeniPolja(data.fields);
            }
        },
        error: function(xhr, status) {
            if (status !== null) {
                //console.log( "posaljiPolje :: greška pri slanju poruke (" + status + ")" );
                dosloDoGreske("posaljiPolje :: greška pri slanju poruke (" + status + ")");
            }
            dosloDoGreske();
        }
    });

}

/**
 * Pomocna funkcija koja za sva dobivena polja postavi odgovarajuce vrijednosti.
 * @param  Array fields Niz koji sadrzi Object-e koji imaju brojeve koji oznacavaju
 *                      koordinate polja, te njezinu vrijednost.
 */
function promjeniPolja(fields) {
    for (var i = 0; i < fields.length; i++) {
        var redniBroj = fields[i].row * nCols + fields[i].col;

        if ($(".polje").eq(redniBroj).attr("class") === "polje pokriven") {
            brojOtkrivenih += 1;
            $(".polje").eq(redniBroj).attr("class", "polje otkriven").unbind('click').unbind('contextmenu');

            if (fields[i].mines > 0)
                $(".polje").eq(redniBroj).html(fields[i].mines);
            else
                $(".polje").eq(redniBroj).html("");
        }

    }

    if (brojOtkrivenih == nRows * nCols - nMines)
        pobjedio();
}

/**
 * Pomocna funkcija koja provjerava jesu li otkrivena sva polja koja nemaju bombu.
 * @return boolean Vraca true ako su sva polja na kojima nema bombe otkrivena, inace false.
 */
function provjeriKrajIgre() {
    var broj = 0;
    for (var i = 0; i < nRows * nCols; i++) {
        if ($(".polje").eq(i).attr("class") === "polje otkriven")
            broj++;
    }
    if (broj == nRows * nCols - nMines)
        return true;
    return false;
}

/**
 * Pomocna funkcija koja se pozove nakon sto su sva polja koja nisu bombe otkrivena.
 * Nakon pozivanja funkcija nacrta bombe na preostalim mjestima te onemoguci klikanje
 * na tim poljima te izpise odgovarajucu poruku.
 */
function pobjedio() {
    $(".pokriven").attr("class", "polje otkriven").
    html('<img src="bomb.png" alt="*" height="20" width="20">').unbind('click').unbind('contextmenu');
    alert("Svaka čast. Pobjeda je tvoja");
}

/**
 * Pozove se ukoliko korisnik klikne na bombu.
 * Onemoguci daljnje klikanje po igri, te ispise odgovarajucu poruku.
 */
function otvorioBombu() {
    $(".polje").unbind('click').unbind('contextmenu');
    alert("KABUUUUUUM, više sreće drugi put.");
}

/**
 * Pomocna funkcija koja se pozove ako je doslo do nekakve greske pri komunikaciji
 * sa serverom.
 * @param  string poruka Poruka koju je server poslao.
 */
function dosloDoGreske(poruka) {
    $(".polje").unbind('click').unbind('contextmenu');
    alert("Došlo je do nekve pogreške. Pokušajte ponovno pokrenuti igru. " + poruka);
}
