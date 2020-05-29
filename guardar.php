<?php 
	require_once __DIR__ . '/funciones.php';

	if ($_POST) {
		$nuevosConceptos = [
			'artur' => extraeConceptos($_POST['artur']),
			'xulian' => extraeConceptos($_POST['xulian']),
		];

		guardaConceptos($nuevosConceptos);	
	}

	header('Location: /', true, 303);
  	die();
?>