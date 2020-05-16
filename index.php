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

function obtenerImagen($nombre, $conceptos) {

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
			'cx' => $config['GOOGLE_KEY'],
			//'filetype' => 'png'
			//'dateRestrict' => 'm1',

		]
	]);

	// echo $response->getStatusCode();
	// echo $response->getBody();

	$body = json_decode($response->getBody(), true);

	//print_r($body);

	if ($body && isset($body['items']) && count($body['items'])) {
		$link = $body['items'][0]['link'];
		$pathinfo = pathinfo($link);

		$fichero = $nombre . '.' . $pathinfo['extension'];

		$client->request('GET', $body['items'][0]['link'], ['sink' => $fichero]);

		return $fichero;
	}

	return null;

}

function creaCoordenadasDeMascara($tamano) {

	// 50 / 50
	// return [
	// 	[
	// 	    ['x' => 0, 'y' => 0],
	// 	    ['x' => 0, 'y' => $tamano],
	// 	    ['x' => $tamano / 2, 'y' => $tamano],
	// 	    ['x' => $tamano / 2, 'y' => 0],
	// 	]
	// ];

	// Diagonales
	// $ancho = 20;
	// $coordenadas = [];

	// $x = 0;
	// $y = 0;
	// for ($x = 0; $x < $tamano; $x = $x + 2 * $ancho) {
	// 	$coordenadas[] = [
	// 		['x' => $x, 'y' => 0],
	// 		['x' => 0, 'y' => $x],
	// 		['x' => 0, 'y' => $x + $ancho],
	// 		['x' => $x + $ancho, 'y' => 0],
	// 	];
	// }

	// $x = 0;
	// $y = 0;
	// for ($y = $ancho; $y < $tamano; $y = $y + 2 * $ancho) {
	// 	$coordenadas[] = [
	// 		['x' => $tamano, 'y' => $y],
	// 		['x' => $y, 'y' => $tamano],			
	// 		['x' => $y + $ancho, 'y' => $tamano],			
	// 		['x' => $tamano, 'y' => $y + $ancho],
			
	// 	];
	// }

	// Diagonales der-izq
	$ancho = rand(1, $tamano);
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

	// Otras formas

			// ['x' => $y, 'y' => 0],
			// ['x' => $y + $ancho, 'y' => 0],
			// ['x' => $tamano, 'y' => $tamano - $y],
			// ['x' => $tamano, 'y' => $tamano - $y - $ancho],

			// ['x' => $tamano, 'y' => $y],
			// ['x' => 0, 'y' => $y],			
			// ['x' => 0, 'y' => $y + $ancho],			
			// ['x' => $tamano, 'y' => $y + $ancho],

			// ['x' => $tamano, 'y' => $y],
			// ['x' => 0, 'y' => $tamano - $y - $ancho],			
			// ['x' => 0, 'y' => $tamano - $y],			
			// ['x' => $tamano, 'y' => $y + $ancho],

	return $coordenadas;
}

// phpinfo();

// $xulian = obtenerImagen('xulian', $conceptosXulian);
// $artur = obtenerImagen('artur', $conceptosArtur);

$artur = 'artur.png';
$xulian = 'xulian.jpg';

$tamano = 500;

$coordenadas = creaCoordenadasDeMascara($tamano);

$mask = new Imagick();
$mask->newimage($tamano, $tamano, new ImagickPixel('transparent'));
$mask->setimageformat('png');

$polygon = new ImagickDraw();
$polygon->setFillColor(new ImagickPixel('black'));
foreach ($coordenadas as $cs) {
	$polygon->polygon($cs);
}
$mask->drawimage($polygon);

//muestraImagen($mask);

// Artur

$imageArtur = new Imagick();
$imageArtur->readimage($artur);
$imageArtur->setImageFormat('png');
$imageArtur->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
$imageArtur->setImageMatte(true);
$imageArtur->compositeimage($mask, Imagick::COMPOSITE_DSTIN, 0, 0, Imagick::CHANNEL_ALPHA);
$imageArtur->trimimage(0);

//muestraImagen($imageArtur);

// Xulian

$mask2 = new Imagick();
$mask2->newimage($tamano, $tamano, new ImagickPixel('black'));
$mask2->setimageformat('png');

$polygon2 = new ImagickDraw();
$polygon2->setFillColor(new ImagickPixel('white'));
foreach ($coordenadas as $cs) {
	$polygon2->polygon($cs);
}
$mask2->drawimage($polygon2);

$mask2->transparentPaintImage(new ImagickPixel('white'), 0, 1, false);

//muestraImagen($mask2);

$imageXulian = new Imagick();
$imageXulian->readimage($xulian);
$imageXulian->setImageFormat('png');
$imageXulian->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
$imageXulian->setImageMatte(true);
$imageXulian->compositeimage($mask2, Imagick::COMPOSITE_DSTIN, 0, 0, Imagick::CHANNEL_ALPHA);
$imageXulian->trimimage(0);

// muestraImagen($imageXulian);

// Combinación

$resultado = new Imagick();
$resultado->newimage($tamano, $tamano, new ImagickPixel('transparent'));
$resultado->setimageformat('png');

$resultado->compositeImage($imageXulian, Imagick::COMPOSITE_BLEND, 0, 0);
$resultado->compositeImage($imageArtur, Imagick::COMPOSITE_BLEND, 0, 0);

muestraImagen($resultado);

?>