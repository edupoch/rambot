<?php 

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

error_reporting(E_ALL);

$conceptosArtur = ['buildings', 'beatnik', 'chet baker', 'moroder'];
$conceptosXulian = ['puke', 'pus', 'drugs', 'porn'];

function muestraImagen($imagen) {
	header('Content-Type: image/png');
	echo $imagen;
	die();
}

function obtenerImagen($config, $nombre, $concepto) {
	$client = new \GuzzleHttp\Client();

	$response = $client->request('GET', 'https://customsearch.googleapis.com/customsearch/v1',
	[
		'query' => [
			'q' => $concepto,
			'num' => 1,
			'start' => rand(1, 99),
			'imgSize' => 'xlarge',
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

function aplicaMascara($mask, $fichero) {
	$maskDimensions = $mask->getImageGeometry();

	$image = new Imagick();
	$image->readimage($fichero);
	$imageDimensions = $image->getImageGeometry();
	$maskX = rand(0, $imageDimensions['width'] - $maskDimensions['width']);
	$maskY = rand(0, $imageDimensions['height'] - $maskDimensions['height']);
	$image->setImageFormat('png');
	$image->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
	$image->setImageMatte(true);
	$image->compositeimage($mask, Imagick::COMPOSITE_DSTIN, $maskX, $maskY, Imagick::CHANNEL_ALPHA);
	$image->cropImage($maskDimensions['width'], $maskDimensions['height'], $maskX, $maskY);

	return $image;
}

$conceptoArtur = $conceptosArtur[rand(0, count($conceptosArtur) - 1)];
$conceptoXulian = $conceptosXulian[rand(0, count($conceptosXulian) - 1)];

$xulian = obtenerImagen($config, 'xulian', $conceptoXulian);
$artur = obtenerImagen($config, 'artur', $conceptoArtur);

// $artur = 'imgs/artur.png';
// $xulian = 'imgs/xulian.png';

$tamano = 500;

$coordenadas = creaCoordenadasDeMascara($tamano);

$mask = creaMascara($tamano, $coordenadas,'transparent', 'black');

//muestraImagen($mask);

// Artur

$imageArtur = aplicaMascara($mask, $artur);

//muestraImagen($imageArtur);

// Xulian

$mask2 = creaMascara($tamano, $coordenadas, 'black', 'white');
$mask2->transparentPaintImage(new ImagickPixel('white'), 0, 1, false);

//muestraImagen($mask2);

$imageXulian = aplicaMascara($mask2, $xulian);

//muestraImagen($imageXulian);

// Combinación

$resultado = new Imagick();
$resultado->newimage($tamano, $tamano, new ImagickPixel('transparent'));
$resultado->setimageformat('png');

$resultado->compositeImage($imageXulian, Imagick::COMPOSITE_BLEND, 0, 0);
$resultado->compositeImage($imageArtur, Imagick::COMPOSITE_BLEND, 0, 0);

//muestraImagen($resultado);

$ficheroResultado = 'imgs/resultado.png';
$resultado->writeImage($ficheroResultado);

echo json_encode([
	'img' => $ficheroResultado,
	'conceptos' => '#' . preg_replace('/\s/', '', $conceptoArtur) . ' #' . preg_replace('/\s/', '', $conceptoXulian)
]);

?>