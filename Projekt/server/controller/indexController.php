<?php 

class IndexController extends BaseController
{
	public function index() 
	{
		// Samo preusmjeri na predmeti podstranicu (pocetna stranica).
		header( 'Location: ' . __SITE_URL . '/index.php?rt=prijava' );
		exit();
	}
}; 

?>
