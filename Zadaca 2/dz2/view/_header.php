<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8">
	<title>Trgovina</title>
	<link rel="stylesheet" href="<?php echo __SITE_URL;?>/css/style.css">
</head>
<body>
	<div class="mainContainer">
		<div class="heading">
			<h1><?php if(isset($title)) echo $title; ?></h1>
			<?php if(isset($_SESSION['username'])) echo '<p>Dobrodošli, '. $_SESSION['username'] . '</p>'; ?>
			<form method="post" action="<?php echo __SITE_URL . '/index.php?rt=prijava/index'?>">
				<button type="submit">Login/logout!</button>
			</form>
			<p><a href="<?php echo __SITE_URL; ?>/index.php?rt=predmeti">Popis svih predmeta u web-shopu</a></p></br>
		</div>
