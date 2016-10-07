<?php require_once __SITE_PATH . '/view/_header.php'; 

	if( isset($errorMsg) && $errorMsg !== '' )
		echo '<p>Napomena: ' . $errorMsg . '</p>';
?>
	</br>
	<p>Za logiranje ispuniti iduće podatke</p>
	<form method="post" action="<?php echo __SITE_URL . '/index.php?rt=prijava/login'?>">
		Korisničko ime:
		<input type="text" name="username" />
		<br />
		Lozinka:
		<input type="password" name="password" />
		<br />
		<button type="submit">Ulogiraj se!</button>
	</form>

	</br></br>
	<p>Za registraciju novog korisnika ispuni iduće podatke</p>
	<form method="post" action="<?php echo __SITE_URL . '/index.php?rt=prijava/registracija'?>">
		Odaberite korisničko ime:
		<input type="text" name="username" />
		<br />
		Odaberite lozinku:
		<input type="password" name="password" />
		<br />
		Vaša mail-adresa:
		<input type="text" name="email" />
		<br />
		<button type="submit">Stvori korisnički račun!</button>
	</form>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
