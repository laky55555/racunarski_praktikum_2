<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<h2>Recenzije</h2>


<div class="container">
<?php 
	foreach( $recenzijeListZaIspis as $recenzija )
	{
		echo '<div class="recenzija">';
		echo '<h5> By: '. $recenzija['user']->username . '</h5>';
		for( $i = 0; $i < 5; $i++ )
		{
			if($i < $recenzija['ocjena'])
				echo '<img src="cijela.png" alt="puna" height="40" width="40">';
			else
				echo '<img src="prazna.png" alt="prazna" height="40" width="40">';
		}
		echo '</br>';
		echo '<p>'. $recenzija['recenzija'] . '</p></br>';
		echo '</div>';
	}
?>
</div>


<div class="container">
<h4>Napiši recenziju</h4>
<form method="post" action="<?php echo __SITE_URL . '/index.php?rt=predmeti/napisiRecenziju'?>">
	<input value="1" type="radio" name="ocjena" /> 1
	<input value="2" type="radio" name="ocjena" /> 2
	<input value="3" type="radio" name="ocjena" /> 3
	<input value="4" type="radio" name="ocjena" /> 4
	<input value="5" type="radio" name="ocjena" /> 5
	</br>
	<textarea name="recenzija" rows="10" cols="50"> Ovdje napišite recenziju. </textarea>

	<button type="submit" name="predmet_id" value= <?php echo '"predmet_' . $predmet_id . '"'; ?> >Recenziraj!</button>
</form>
</div>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
