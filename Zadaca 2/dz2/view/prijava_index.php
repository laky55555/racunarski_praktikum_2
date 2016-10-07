<?php require_once __SITE_PATH . '/view/_header.php'; 
?>

	<br>
	<br>
	<p>Bravo, poštovani korisniče <?php echo $_SESSION['username']; ?>! Uspješno ste se ulogirali!</p>

	<form method="post" action="<?php echo __SITE_URL . '/index.php?rt=prijava/logout'?>">
		<button type="submit">Logout!</button>
	</form>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
