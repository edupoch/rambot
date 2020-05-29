<?php 
	require_once __DIR__ . '/funciones.php';

	if ($_POST) {
		$nuevosConceptos = [
			'artur' => extraerConceptos($_POST['artur']),
			'xulian' => extraerConceptos($_POST['xulian']),
		];

		guardarConceptos($nuevosConceptos);	
	}

	header('Location: /', true, 303);
  	die();
?>