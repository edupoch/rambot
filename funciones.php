<?php
	define('ARCHIVO_CONCEPTOS', __DIR__  . '/conceptos.json');

	/*
	 * CONCEPTOS
	 */

	function leeConceptos() {
		if (!file_exists(ARCHIVO_CONCEPTOS)) {
			guardarConceptos([]);
		}

		return json_decode(file_get_contents(ARCHIVO_CONCEPTOS), true);
	}

	function extraeConceptos($cadena) {
		$conceptos = explode(',', $cadena);
		$nuevosConceptos = [];

		if ($conceptos && count($conceptos)) {
			foreach ($conceptos as $concepto) {
				$nuevoConcepto = trim($concepto);
				if ($nuevoConcepto) {
					$nuevosConceptos[] = $nuevoConcepto;
				}
			}
		}

		return $nuevosConceptos;
	}

	function guardaConceptos($conceptos) {
		file_put_contents(ARCHIVO_CONCEPTOS, json_encode($conceptos));
	}

	/*
	 * IMÁGENES
	 */

	function muestraImagen($imagen) {
		header('Content-Type: image/png');
		echo $imagen;
		die();
	}
	
	function obtenImagen($config, $nombre, $concepto) {
		$client = new \GuzzleHttp\Client();
	
		$response = $client->request('GET', 'https://customsearch.googleapis.com/customsearch/v1',
		[
			'query' => [
				'q' => $concepto,
				'num' => 1,
				'start' => rand(1, 99),
				'imgSize' => 'xlarge',
				'imgType' => 'photo',
				'searchType' => 'image',
				'safe' => 'off',
				'key' => $config['GOOGLE_KEY'],			
				'cx' => $config['GOOGLE_CX'],
			]
		]);
	
		// echo $response->getStatusCode();
		
		$body = json_decode($response->getBody(), true);
	
		if ($body && isset($body['items']) && count($body['items'])) {
			$link = $body['items'][0]['link'];
			$pathinfo = pathinfo($link);
	
			$fichero = 'imgs/' . $nombre . '.png';
	
			$client->request('GET', $body['items'][0]['link'], ['sink' => $fichero]);
	
			return $fichero;
		}
	
		return null;
	
	}

	function ajustaImagen($imagen, $tamano) {
		$imageDimensions = $imagen->getImageGeometry();
		if ($imageDimensions['width'] < $tamano) {
			$imagen->scaleImage($tamano, 0);
			$imageDimensions = $imagen->getImageGeometry();
		}

		if ($imageDimensions['height'] < $tamano) {
			$imagen->scaleImage(0, $tamano);
		}

		return $imagen;
	}
	
	function creaCoordenadasDeMascara($tamano) {
		$metodo = rand(1, 5);
		$ancho = rand(10, 40);
	
		switch ($metodo) {
			 case 1:
				 //Diagonales der-izq
				 $coordenadas = [];
	
				 $x = 0;
				 $y = 0;
				 for ($x = 0; $x < 2 * $tamano; $x = $x + 2 * $ancho) {
					 $coordenadas[] = [
						 ['x' => $x, 'y' => 0],
						 ['x' => 0, 'y' => $x],
						 ['x' => 0, 'y' => $x + $ancho],
						 ['x' => $x + $ancho, 'y' => 0],
					 ];
				 }
				 break;
	
			 case 2: 
				 // Diagonales izq-der
				 $coordenadas = [];
	
				 $x = 0;
				 $y = 0;
				 for ($x = 0; $x < 2 * $tamano; $x = $x + 2 * $ancho) {
					 $coordenadas[] = [
						 ['x' => 0, 'y' => $tamano - $ancho - $x],
						 ['x' => $x + $ancho, 'y' => $tamano],
						 ['x' => $x, 'y' => $tamano],
						 ['x' => 0, 'y' => $tamano - $x],
					 ];
				 }
				 break;
	
			 case 3: 
				 // Líneas verticales
				 $coordenadas = [];
	
				 $x = 0;
				 $y = 0;
				 for ($x = 0; $x < $tamano; $x = $x + 2 * $ancho) {
					 $coordenadas[] = [
						 ['x' => $x, 'y' => 0],
						 ['x' => $x + $ancho, 'y' => 0],
						 ['x' => $x + $ancho, 'y' => $tamano],
						 ['x' => $x, 'y' => $tamano],
					 
					 ];
				 }
				 break;
	
			 case 4: 
				 // Líneas horizontales
				 $coordenadas = [];
	
				 $x = 0;
				 $y = 0;
				 for ($x = 0; $x < $tamano; $x = $x + 2 * $ancho) {
					 $coordenadas[] = [
						 ['x' => 0, 'y' => $x],
						 ['x' => $tamano, 'y' => $x],
						 ['x' => $tamano, 'y' => $x + $ancho],
						 ['x' => 0, 'y' => $x + $ancho],
					 ];
				 }
				 break;
	
			 case 5: 
				 // Cuadrado en el centro
				 $coordenadas = [];
				 $anchoCuadrado = rand($tamano / 5, $tamano / 2);
	
				 $coordenadas[] = [
					 ['x' => $tamano / 2 - $anchoCuadrado,'y' => $tamano / 2 - $anchoCuadrado],
					 ['x' => $tamano / 2 + $anchoCuadrado,'y' => $tamano / 2 - $anchoCuadrado],
					 ['x' => $tamano / 2 + $anchoCuadrado,'y' => $tamano / 2 + $anchoCuadrado],
					 ['x' => $tamano / 2 - $anchoCuadrado,'y' => $tamano / 2 + $anchoCuadrado],
				 ];
				 break;	
		}
	
		return $coordenadas;
	}
	
	function creaMascara($tamano, $coordenadas, $colorFondo, $colorMascara) {
		$mask = new Imagick();
		$mask->newimage($tamano, $tamano, new ImagickPixel($colorFondo));
		$mask->setimageformat('png');
	
		$polygon = new ImagickDraw();
		$polygon->setFillColor(new ImagickPixel($colorMascara));
		foreach ($coordenadas as $cs) {
			$polygon->polygon($cs);
		}
		$mask->drawimage($polygon);
	
		return $mask;
	}
	
	function aplicaMascara($mask, $imagen) {
		$maskDimensions = $mask->getImageGeometry();
	
		$imageDimensions = $imagen->getImageGeometry();
		$maskX = rand(0, $imageDimensions['width'] - $maskDimensions['width']);
		$maskY = rand(0, $imageDimensions['height'] - $maskDimensions['height']);
		$imagen->setImageFormat('png');
		$imagen->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
		$imagen->setImageMatte(true);
		$imagen->compositeimage($mask, Imagick::COMPOSITE_DSTIN, $maskX, $maskY, Imagick::CHANNEL_ALPHA);
		$imagen->cropImage($maskDimensions['width'], $maskDimensions['height'], $maskX, $maskY);
	
		return $imagen;
	}
	
?>	