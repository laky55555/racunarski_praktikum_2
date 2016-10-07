<?php

// Manualno inicijaliziramo bazu ako već nije.
require_once '../../model/db.class.php';

$db = DB::getConnection();

try
{
	$st = $db->prepare( 
		'CREATE TABLE IF NOT EXISTS userList (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'username varchar(20) NOT NULL,' .
		'password varchar(255) NOT NULL,'.
		'email varchar(30) NOT NULL,'.
		'reg_seq varchar(30) NOT NULL,'.
		'has_registered int NOT NULL)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error #1: " . $e->getMessage() ); }

echo "Napravio tablicu users.<br />";

try
{
	$st = $db->prepare( 
		'CREATE TABLE IF NOT EXISTS trgovina (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'naziv varchar(255) NOT NULL,' .
		'opis varchar(1000) NOT NULL,' .
		'cijena INT NOT NULL)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error #2: " . $e->getMessage() ); }

echo "Napravio tablicu trgovina.<br />";


try
{
	$st = $db->prepare( 
		'CREATE TABLE IF NOT EXISTS recenzije (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'id_predmet INT NOT NULL,' .
		'id_user INT NOT NULL,' .
		'ocjena INT NOT NULL,' .
 		'recenzija varchar(1000) NOT NULL)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error #3: " . $e->getMessage() ); }

echo "Napravio tablicu recenzije.<br />";


// Ubaci neke korisnike unutra

try
{
	$st = $db->prepare( 'INSERT INTO userList (username, password, email, reg_seq, has_registered) VALUES (:username, :password, :email, :reg_seq, :has_registered)' );
	$st->execute( array( 'username' => 'Pero', 'password' => password_hash( 'perinasifra', PASSWORD_DEFAULT ), 'email' => 'pero@peric.com', 'reg_seq' => 'abc', 'has_registered' => 1 ) );                                                                                           
	$st->execute( array( 'username' => 'Mirko', 'password' => password_hash( 'mirkovasifra', PASSWORD_DEFAULT ), 'email' => 'pero2@peric.com', 'reg_seq' => 'abc', 'has_registered' => 1) );
	$st->execute( array( 'username' => 'Slavko', 'password' => password_hash( 'slavkovasifra', PASSWORD_DEFAULT ), 'email' => 'pero3@peric.com', 'reg_seq' => 'abc', 'has_registered' => 1 ) );
	$st->execute( array( 'username' => 'Ana', 'password' => password_hash( 'aninasifra', PASSWORD_DEFAULT ), 'email' => 'pero4@peric.com', 'reg_seq' => 'abc', 'has_registered' => 1 ) );
	$st->execute( array( 'username' => 'Maja', 'password' => password_hash( 'majinasifra', PASSWORD_DEFAULT ), 'email' => 'pero5@peric.com', 'reg_seq' => 'abc', 'has_registered' => 1 ) );
}
catch( PDOException $e ) { exit( "PDO error #4: " . $e->getMessage() ); }

echo "Ubacio korisnike u tablicu users.<br />";


// Ubaci neke proizvode unutra
try
{
	$st = $db->prepare( 'INSERT INTO trgovina(naziv, opis, cijena) VALUES (:naziv, :opis, :cijena)' );

	$st->execute( array( 'naziv' => 'Samsung Galaxy S7', 
				   'opis' => 'A brand-new, unused, unopened, undamaged item in its original packaging (where packaging is applicable). Packaging should be the same as what is found in a retail store, unless the item is handmade or was packaged by the manufacturer in non-retail packaging, such as an unprinted box or plastic bag.', 
				   'cijena' => 707 ) );
	$st->execute( array( 'naziv' => 'iPhone 6S 16GB',
				   'opis' => 'Apple iPhone 6S 16GB SILVER, 4.7 inch (diagonal) Retina HD display with 1334-by-750 resolution, 3D Touch, A9 chip with integrated M9 motion coprocessor, 12 megapixel iSight camera with Focus Pixels, True Tone flash and Live Photos, In the Box, iPhone with iOS 9, Apple EarPods with Remote and Mic, Lightning to USB Cable, USB Power Adapter.',
				   'cijena' => 1000 ) );
	$st->execute( array( 'naziv' => 'GoPro HERO 3+ Black Edition Action Camera Camcorder',
				   'opis' => 'All GoPro certified refurbished cameras have passed a rigorous testing process in a GoPro purpose built refurb facility. - Includes Wi-Fi Remote, and starter kit of adhesive mounts and accessories - Must pass a strict 14-step testing process - Feature the same high quality standards as new GoPro cameras - May have minor cosmetic flaws (small scratches or nicks) - Lenses have been cleaned and the software reset - Item comes reboxed in official GoPro refurbished packaging - 12 month GoPro warranty is included.',
	 			   'cijena' => 215 ) );
	$st->execute( array( 'naziv' => 'Samsung 40" Curved 4K UHD Smart Ultra Full HD LED 6 Series TV JU6670', 
				   'opis' => 'An LED Smart TV boasting a 40" Ultra High Definition 4K screen with a stunning curved design to provide greater natural viewing angles, plus a powerful quad-core processor for speedy internet connection, as well as the ease of integrated Freeview HD. Discover a fantastic, immersive viewing experience with this stylish Samsung JU6670 TV that has built-in Wi-Fi connectivity and incredible features.', 
				   'cijena' => 661 ) );
}
catch( PDOException $e ) { exit( "PDO error #5: " . $e->getMessage() ); }

echo "Ubacio predmete u tablicu trgovina.<br />";


// Ubaci neke posudbe unutra (ovo nije baš pametno ovako raditi, preko hardcodiranih id-eva usera i knjiga)
try
{
	$st = $db->prepare( 'INSERT INTO recenzije(id_predmet, id_user, ocjena, recenzija) VALUES (:id_predmet, :id_user, :ocjena, :recenzija)' );

	$st->execute( array( 'id_predmet' => 2, 'id_user' => 1, 'ocjena' => 5, 
				   'recenzija' => 'I had Galaxy S3, and just upgraded to iPhone 6. I am still new to this Apple iOS, but I have to admit it\'s much simpler than the S3 Android platform, though it has its limits set by Apple, so worth it to take into consideration when comparing Galaxy phones with iPhone 6. Putting that aside, the iPhone 6 is super fast and display is much brighter and offer better resolution compared to my old S3. Other thing is worth to mention is the long battery life of iPhone 6 as other Apple products offer; Really powerful battery that keeps my iPhone 6 alive for 3-4 days without worrying to plug in the charger cord. ') );
	$st->execute( array( 'id_predmet' => 1, 'id_user' => 2, 'ocjena' => 5, 
				   'recenzija' => 'Excellent performance. A superb phone') );
	$st->execute( array( 'id_predmet' => 1, 'id_user' => 3, 'ocjena' => 4, 
				   'recenzija' => 'Just got this phone yesterday and so far I am liking the phone. I need to find a better screen protector for it. The ones on it now has the sides lifting because it doesn\'t appear to go all the way to the edge or tuck into the case. Fast processor is great!') );
	$st->execute( array( 'id_predmet' => 3, 'id_user' => 1, 'ocjena' => 4, 
				   'recenzija' => 'The camera overall is amazing. Battery life could have certainly been a lot better, especially for the price. Other than that this is the best camera hands down. I would definitely recommend this camera.') );
}
catch( PDOException $e ) { exit( "PDO error #5: " . $e->getMessage() ); }

echo "Ubacio recenzije u tablicu recenzije.<br />";

?> 
