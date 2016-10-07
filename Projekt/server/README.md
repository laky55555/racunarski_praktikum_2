# README #

Briskula je web platforma za igranje igre briskula.
Live primjerak igre nalazi se na briskula.pe.hu/server adresi.

## Potrebno ##
1. Server s instaliranim php.om (5.3 ili noviji).
2. Server s instaliranim mysql-om (Ver 14.14 Distrib 5.5.49 ili noviji).
3. Omoguceno slanje mailova (potrebno pri registraciji novih korisnika).

## Instalacija briskule ##

1. "Skinuti" sve podatke iz repozitorija.
2. Postaviti mapu server na zeljeni server.
3. Podesiti podatke iz server/model/db.php na odgovarajuce iz svoje baze.
4. Pokrenuti skriptu server/app/boot/prepare.php da se konstruiraju relacije i procedure u bazi.
5. U file-u server/model/userservice.class.php u funkciji na 179 liniji podesiti podatke za slanje maila na zeljene.
6. Server je spreman za igranje. :)


## Izradili ##

* [Mirjana Jukic-Braculj](https://bitbucket.org/mirjana_jukicbraculj/)
* [Mia Filic](https://bitbucket.org/mfilic/)
* [Gregor Boris Banusic](https://bitbucket.org/greg93/)
* [Ivan Lakovic](https://bitbucket.org/Laky55555/)