<?php 
	require_once __DIR__ . '/funciones.php';

	$conceptos = leeConceptos();
?>
<html>
<head>
	<meta charset="UTF-8">
	<title>Rambot</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="normlize.css">
	
	<style>
		body {
			font-family: monospace;
		}
		
		.wrapper {
			max-width: 600px;
			margin: 0 auto;
		}

		.mitad {
			width: 50%;
			height: 100%;
			float: left;
		}

		img, textarea {
			display: block;
			width: 100%;
			margin-bottom: 20px;
		}

		button {
			margin: 0 auto;
		}

		.bloque {
			display: block;
			margin-bottom: 20px;
			clear: both;
			overflow: hidden;
		}

	</style>
</head>
<body>
	<div class="wrapper">
		<a href="#" style="float: right" class="bloque js-cambiaConceptos">Cambiar conceptos</a>

		<form class="conceptos js-conceptos" action="guardar.php" method="post" style="display:none">
			<label for="artur">Conceptos para <b>Artur</b>, separados por comas</label>
			<textarea name="artur" id="" cols="30" rows="10"><?= isset($conceptos['artur']) ? implode($conceptos['artur'], ',') : '' ?></textarea>
			<label for="xulian">Conceptos para <b>Xulián</b>, separados por comas</label>
			<textarea name="xulian" id="" cols="30" rows="10"><?= isset($conceptos['xulian']) ? implode($conceptos['xulian'], ',') : '' ?></textarea>
			<button type="sumbit">Guardar</button>
		</form>

		<div class="bloque js-estado"></div>
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script>
		function generaImagen() {
			$estado = $('.js-estado');

			$estado.html('Generando imagen...');

			$.get('generar.php', '', function(resultado) {
				console.log(resultado);
				if (resultado && resultado.img) {
					var src = resultado.img + '?v=' + Date.now();
					
					html = '';
					html += '<img src="' + src + '" />';
					html += '<label>Texto para Instagram</label>';
					html += '<textarea rows="1">' + resultado.conceptos + '</textarea>';
					html += '<a style="float: left" href="' + src + '" download="rambot_' + Date.now() + '.png">Descargar imagen</a>';
					html += '<a style="float: right" href="#" onclick="generaImagen()">Generar otra imagen</a>';
					$estado.html(html);
				} else {
					$estado.html('No se ha podido generar la imagen');
				}
			}, 'json').fail(function() {
			    $estado.html('Ha ocurrido un error. Vuelve a intentarlo en unos minutos');
			});
		}

		$(function() {
			generaImagen();

			$('.js-cambiaConceptos').click(function(e) {
				e.preventDefault();

				$('.js-conceptos').toggle();
			});
		});
	</script>	
</body>
</html>