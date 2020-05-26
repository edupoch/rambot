<?php 
	require_once __DIR__ . '/funciones.php';

	if ($_POST) {
		$nuevosConceptos = [
			'artur' => explode(',', $_POST['artur']),
			'xulian' => explode(',', $_POST['xulian']),
		];

		guardarConceptos($nuevosConceptos);
	}

	header('Location: /', true, 303);
  	die();
?>