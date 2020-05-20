<html>
<head>
	<meta charset="UTF-8">
	<title>Rambot</title>
	<link rel="stylesheet" href="normlize.css">
	
	<style>
		body {
			font-family: monospace;
		}
		
		.wrapper {
			max-width: 500px;
			margin: 0 auto;
		}

		img, textarea {
			display: block;
			width: 100%;
			margin-bottom: 20px;
		}

		button {
			margin: 0 auto;
		}

	</style>
</head>
<body>
	<div class="wrapper">
		<div class="js-estado"></div>
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script>
		function generarImagen() {
			$estado = $('.js-estado');

			$estado.html('Generando imagen...');

			$.get('generar.php', '', function(resultado) {
				console.log(resultado);
				if (resultado && resultado.img) {
					var src = resultado.img + '?v=' + Date.now();
					
					html = '';
					html += '<img src="' + src + '" />';
					html += '<label>Texto para Instagram</label>';
					html += '<textarea rows="4">' + resultado.conceptos + '</textarea>';
					html += '<a href="' + src + '" download="rambot_' + Date.now() + '.png">Descargar imagen</a><br><br>';
					html += '<a href="#" onclick="generarImagen()">Generar otra imagen</a>';
					$estado.html(html);
				} else {
					$estado.html('No se ha podido generar la imagen');
				}
			}, 'json').fail(function() {
			    $estado.html('Ha ocurrido un error. Vuelve a intentarlo en unos minutos');
			});
		}

		$(function() {
			generarImagen();
		});
	</script>	
</body>
</html>