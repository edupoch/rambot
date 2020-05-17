<?php 

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

error_reporting(E_ALL);

$conceptosArtur = ['buildings'];
$conceptosXulian = ['puke'];

function muestraImagen($imagen) {
	header('Content-Type: image/png');
	echo $imagen;
	die();
}

function obtenerImagen($config, $nombre, $conceptos) {

	// $fb = new \Facebook\Facebook([
	//   'app_id' => '278068986920244',
	//   'app_secret' => '1e8e2e04adab9fa05d863770559ec023',
	//   //'default_graph_version' => 'v2.10',
	//   //'default_access_token' => '{access-token}', // optional
	// ]);

	// try {
	//   // Get the \Facebook\GraphNode\GraphUser object for the current user.
	//   // If you provided a 'default_access_token', the '{access-token}' is optional.
	//   $response = $fb->get('/me', '{access-token}');
	// } catch(\Facebook\Exception\FacebookResponseException $e) {
	//   // When Graph returns an error
	//   echo 'Graph returned an error: ' . $e->getMessage();
	//   exit;
	// } catch(\Facebook\Exception\FacebookSDKException $e) {
	//   // When validation fails or other local issues
	//   echo 'Facebook SDK returned an error: ' . $e->getMessage();
	//   exit;
	// }

	// // $me = $response->getGraphUser();
	// // echo 'Logged in as ' . $me->getName();

	// $helper = $fb->getRedirectLoginHelper();

	// $permissions = ['instagram_basic'];
	// $loginUrl = $helper->getLoginUrl('https://ram.ocre.soy', $permissions);

	// echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';

	// Create a client with a base URI
	// $client = new \GuzzleHttp\Client(['base_uri' => 'https://api.github.com/repos/']);
	// $response = $client->request('GET', 'guzzle/guzzle');

	$client = new \GuzzleHttp\Client();


	$response = $client->request('GET', 'https://customsearch.googleapis.com/customsearch/v1',
	[
		'query' => [
			'q' => $conceptos[rand(0, count($conceptos) - 1)],
			'num' => 1,
			'start' => rand(1, 99),
			//'start' => 1,
			'imgSize' => 'xlarge',
			//'imgType' => 'photo',
			'searchType' => 'image',
			'key' => $config['GOOGLE_KEY'],
			'safe' => 'off',
			'cx' => $config['GOOGLE_CX'],
			//'filetype' => 'png'
			//'dateRestrict' => 'm1',

		]
	]);

	// echo $response->getStatusCode();
	
	$body = json_decode($response->getBody(), true);

	if ($body && isset($body['items']) && count($body['items'])) {
		$link = $body['items'][0]['link'];
		$pathinfo = pathinfo($link);

		//$fichero = $nombre . '.' . $pathinfo['extension'];
		$fichero = 'imgs/' . $nombre . '.png';

		$client->request('GET', $body['items'][0]['link'], ['sink' => $fichero]);

		return $fichero;
	}

	return null;

}

function creaCoordenadasDeMascara($tamano) {
	$metodo = rand(1, 2);

	switch ($metodo) {
	 	case 1:
	 		//Diagonales der-izq
	 		$ancho = rand(10, $tamano / 2);
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
	 		$ancho = rand(10, $tamano / 2);
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
	$image->trimimage(0);

	return $image;
}

$xulian = obtenerImagen($config, 'xulian', $conceptosXulian);
$artur = obtenerImagen($config, 'artur', $conceptosArtur);

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

// muestraImagen($imageXulian);

// Combinación

$resultado = new Imagick();
$resultado->newimage($tamano, $tamano, new ImagickPixel('transparent'));
$resultado->setimageformat('png');

$resultado->compositeImage($imageXulian, Imagick::COMPOSITE_BLEND, 0, 0);
$resultado->compositeImage($imageArtur, Imagick::COMPOSITE_BLEND, 0, 0);

muestraImagen($resultado);

?>