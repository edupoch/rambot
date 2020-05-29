<?php 

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funciones.php';

error_reporting(E_ALL);

$conceptos = leeConceptos();

$conceptosArtur = $conceptos['artur'];
$conceptosXulian = $conceptos['xulian'];

$conceptoArtur = $conceptosArtur[rand(0, count($conceptosArtur) - 1)];
$conceptoXulian = $conceptosXulian[rand(0, count($conceptosXulian) - 1)];

try {
	$ficheroXulian = obtenImagen($config, 'xulian', $conceptoXulian);
	$ficheroArtur = obtenImagen($config, 'artur', $conceptoArtur);
} catch (Exception $e) {
	$ficheroArtur = 'imgs/artur.png';
	$ficheroXulian = 'imgs/xulian.png';
}

$tamano = 600;

/*
 * MÁSCARAS
 */

$coordenadas = creaCoordenadasDeMascara($tamano);

$mask = creaMascara($tamano, $coordenadas,'transparent', 'black');

$mask2 = creaMascara($tamano, $coordenadas, 'black', 'white');
$mask2->transparentPaintImage(new ImagickPixel('white'), 0, 1, false);

//muestraImagen($mask);
//muestraImagen($mask2);

/*
 * ARTUR
 */

$imagenArtur = new Imagick();
$imagenArtur->readimage($ficheroArtur);

$imagenArtur = ajustaImagen($imagenArtur, $tamano);
$imagenArtur = aplicaMascara($mask, $imagenArtur);

//muestraImagen($imagenArtur);

/*
 * XULIÁN
 */

$imagenXulian = new Imagick();
$imagenXulian->readimage($ficheroXulian);

$imagenXulian = ajustaImagen($imagenXulian, $tamano);
$imagenXulian = aplicaMascara($mask2, $imagenXulian);

//muestraImagen($imagenXulian);

/*
 * COMBINACIÓN
 */

$resultado = new Imagick();
$resultado->newimage($tamano, $tamano, new ImagickPixel('transparent'));
$resultado->setimageformat('png');

$resultado->compositeImage($imagenXulian, Imagick::COMPOSITE_BLEND, 0, 0);
$resultado->compositeImage($imagenArtur, Imagick::COMPOSITE_BLEND, 0, 0);

//muestraImagen($resultado);

$ficheroResultado = 'imgs/resultado.png';
$resultado->writeImage($ficheroResultado);

echo json_encode([
	'img' => $ficheroResultado,
	'conceptos' => '#' . preg_replace('/\s/', '', $conceptoArtur) . ' #' . preg_replace('/\s/', '', $conceptoXulian)
]);

?>