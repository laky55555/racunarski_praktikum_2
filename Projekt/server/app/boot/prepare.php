<?php

// Manualno inicijaliziramo bazu ako već nije.
require_once '../../model/db.class.php';

$db = DB::getConnection();

try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS igre (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'imena_igraca varchar(100) NOT NULL,' .
		'karte_u_ruci varchar(50),' .
		'karte_u_spilu varchar(200),' .
		'karte_izasle varchar(200),' .
		'karte_bacene_u_rundi varchar(20),' .
		'bodovi varchar(10),' .
		'igrac_na_potezu int,' .
		'broj_odigralih int,' .
		'gotova_igra int,' .
		'karte_briskula int,' .
		'dupla int,' .
		'broj_igraca int)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error kod izrade tablice igre: " . $e->getMessage() ); }

echo "Napravio tablicu games.<br />";

try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS userList (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'username varchar(20) NOT NULL,' .
		'password varchar(255) NOT NULL,' .
		'email varchar(30),' .
		'reg_seq varchar(30),' .
		'broj_pobjeda int,' .
		'broj_odigralih int,' .
		'has_registered varchar(30))'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error kod izrade userList: " . $e->getMessage() ); }

echo "Napravio tablicu users.<br />";


try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS onlines (' .
		'id int NOT NULL PRIMARY KEY,' .
		'username varchar(50) NOT NULL,' .
		'broj_pobjeda int,' .
		'broj_odigralih int,' .
		'id_sobe int NOT NULL DEFAULT -1,' .
		'igrac0 varchar(50) NOT NULL,' .
		'igrac1 varchar(50) NOT NULL,' .
		'igrac2 varchar(50) NOT NULL,' .
		'igrac3 varchar(50) NOT NULL,' .
		'broj_igraca int)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error kod tablice onlines: " . $e->getMessage() ); }

echo "Napravio tablicu onlines.<br />";

try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS sobe2 (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'igrac0 varchar(50) NOT NULL,' .
		'igrac1 varchar(50) DEFAULT "",' .
		'potvrda0 boolean DEFAULT true,' .
		'potvrda1 boolean DEFAULT true,' .
		'broj_igraca int DEFAULT 2,' .
		'id_igre int DEFAULT -1,' .
		'posljednja_promjena TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error kod tablice sobe2: " . $e->getMessage() ); }

echo "Napravio tablicu sobe2.<br />";

try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS sobe4 (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'igrac0 varchar(50) NOT NULL,' .
		'igrac1 varchar(50) DEFAULT "",' .
		'igrac2 varchar(50) DEFAULT "",' .
		'igrac3 varchar(50) DEFAULT "",' .
		'potvrda0 boolean DEFAULT true,' .
		'potvrda1 boolean DEFAULT true,' .
		'potvrda2 boolean DEFAULT true,' .
		'potvrda3 boolean DEFAULT true,' .
		'broj_igraca int DEFAULT 4,' .
		'id_igre int DEFAULT -1,' .
		'posljednja_promjena TIMESTAMP DEFAULT CURRENT_TIMESTAMP
				ON UPDATE CURRENT_TIMESTAMP)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error kod tablice sobe4: " . $e->getMessage() ); }

echo "Napravio tablicu sobe4.<br />";

try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS sobe (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'igraci varchar(200) NOT NULL,' .
		'potvrde varchar(20) NOT NULL,' .
		'broj_igraca int DEFAULT 2,' .
		'id_igre int DEFAULT -1,' .
		'posljednja_promjena TIMESTAMP DEFAULT CURRENT_TIMESTAMP
				ON UPDATE CURRENT_TIMESTAMP)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error kod tablice sobe: " . $e->getMessage() ); }

echo "Napravio tablicu sobe.<br />";


try
{

	/*$st = $db->prepare( 'DROP PROCEDURE IF EXISTS `procitaj_i_obrisi`;'.
		'DELIMITER $$ ' .
			'CREATE PROCEDURE  procitaj_i_obrisi (IN id_igre INT, OUT imena VARCHAR(100), OUT rezultat VARCHAR(10)) '.
			'BEGIN'.
				'SELECT imena_igraca, bodovi INTO imena, rezultat FROM igre  WHERE id = id_igre;'.
				'DELETE FROM igre WHERE id = id_igre;'.
			'END $$ '.
		'DELIMITER ; '
	);*/
	$st = $db->prepare('DROP PROCEDURE IF EXISTS `procitaj_i_obrisi2`;');
	$st->execute();

	$st = $db->prepare('CREATE PROCEDURE `procitaj_i_obrisi2` (IN id_igre INT, IN id_sobe INT, OUT imena VARCHAR(100), OUT rezultat VARCHAR(10)) '.
	'BEGIN SELECT imena_igraca, bodovi INTO imena, rezultat FROM igre  WHERE id = id_igre; DELETE FROM igre WHERE id = id_igre; UPDATE sobe2 SET id_igre = -1 WHERE id = id_sobe; END');
	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error kod izrade procedure procitaj_i_obrisi2: " . $e->getMessage() ); }

echo "Napravio proceduru procitaj_i_obrisi2.<br />";


try
{

	/*$st = $db->prepare( 'DROP PROCEDURE IF EXISTS `procitaj_i_obrisi`;'.
		'DELIMITER $$ ' .
			'CREATE PROCEDURE  procitaj_i_obrisi (IN id_igre INT, OUT imena VARCHAR(100), OUT rezultat VARCHAR(10)) '.
			'BEGIN'.
				'SELECT imena_igraca, bodovi INTO imena, rezultat FROM igre  WHERE id = id_igre;'.
				'DELETE FROM igre WHERE id = id_igre;'.
			'END $$ '.
		'DELIMITER ; '
	);*/
	$st = $db->prepare('DROP PROCEDURE IF EXISTS `procitaj_i_obrisi4`;');
	$st->execute();

	$st = $db->prepare('CREATE PROCEDURE  `procitaj_i_obrisi4` (IN id_igre INT, IN id_sobe INT, OUT imena VARCHAR(100), OUT rezultat VARCHAR(10)) '.
	'BEGIN SELECT imena_igraca, bodovi INTO imena, rezultat FROM igre  WHERE id = id_igre; DELETE FROM igre WHERE id = id_igre; UPDATE sobe4 SET id_igre = -1 WHERE id = id_sobe; END;');
	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error kod izrade procedure procitaj_i_obrisi4: " . $e->getMessage() ); }

echo "Napravio proceduru procitaj_i_obrisi4.<br />";

//POZIVAMO FUNKCIJU:
//set @id_igre = 1;
//call procitaj_i_obrisi(@id_igre, @imena_igraca, @kjk);
//select @imena_igraca, @id_igre;
/*

DROP PROCEDURE IF EXISTS `procitaj_i_obrisi`;
DELIMITER $$
CREATE PROCEDURE  `procitaj_i_obrisi`( IN id_igre INT, OUT imena VARCHAR(100), OUT rezultat VARCHAR(10) )
		BEGIN
			SELECT imena_igraca, bodovi INTO imena, rezultat FROM igre  WHERE id = id_igre;
			DELETE FROM igre WHERE id = 6;
		 END $$
DELIMITER ;*/
