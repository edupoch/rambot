<?php
	define('ARCHIVO_CONCEPTOS', __DIR__  . '/conceptos.json');

	function leerConceptos() {
		if (!file_exists(ARCHIVO_CONCEPTOS)) {
			guardarConceptos([]);
		}

		return json_decode(file_get_contents(ARCHIVO_CONCEPTOS), true);
	}

	function guardarConceptos($conceptos) {
		file_put_contents(ARCHIVO_CONCEPTOS, json_encode($conceptos));
	}
?>	