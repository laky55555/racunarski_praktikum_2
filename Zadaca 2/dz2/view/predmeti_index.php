<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<h2>Popis svih predmeta:</h2>
	<div class="container">
	<?php 
		foreach( $predmetiList as $predmet )
		{
			echo '<div class="predmet">';
			echo '<h3>'.$predmet->naziv.'</h3>';
			echo '<font color="red">$'.$predmet->cijena.'</font><br>';
			for( $i = 0; $i < 5; $i++)
			{
				if($i < floor($predmet->pros_ocjena))
					echo '<img src="cijela.png" alt="puna" height="40" width="40">';
				else if ($i>=ceil($predmet->pros_ocjena))
					echo '<img src="prazna.png" alt="prazna" height="40" width="40">';
				else
				{
					if(abs($i-$predmet->pros_ocjena) < 0.25)
						echo '<img src="prazna.png" alt="prazna" height="40" width="40">';
					else if(abs($i-$predmet->pros_ocjena) > 0.75)
						echo '<img src="cijela.png" alt="cijela" height="40" width="40">';
					else	
						echo '<img src="pola.png" alt="pola" height="40" width="40">';
				}
			}
			echo '<a href="'. __SITE_URL . '/index.php?rt=predmeti/showRecenzije&predmet_id=predmet_'. $predmet->id .'">('. $predmet->broj_recenzija .' reviews)</a>';
			echo '<p>'.$predmet->opis .'</p><br><br>';
			echo '</div>';
		}
	?>
	</div>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
