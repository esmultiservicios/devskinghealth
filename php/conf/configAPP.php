<?php

// Redirigir a HTTPS si no está en HTTPS
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirectURL);
    exit;
}

// Obtener el protocolo (http o https)
$protocol = 'https://';  // Forzar siempre HTTPS

// Obtener el nombre del servidor
$serverName = $_SERVER['SERVER_NAME'];

// Obtener el puerto si no es el puerto estándar
$port = ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') ? ':' . $_SERVER['SERVER_PORT'] : '';

// Obtener la ruta base
$basePath = $serverName == 'localhost' ? '/devskinghealth/' : '/';

// Construir la URL base
$baseURL = $protocol . $serverName . $port . $basePath;
$urlWindows = 'https://wi.fastsolutionhn.com/Rpt/esmultiservicio.aspx';
$urlLogo = "https://wi.fastsolutionhn.com/files/skinghealt_logo.png";

// Definir la constante SERVERURL
define('SERVERURL', $baseURL);
define('SERVERURLWINDOWS', $urlWindows);
define('SERVERURLLOGO', $urlLogo);

// Otras constantes
define('SERVEREMPRESA', 'CAMI');
define('SERVER', 'localhost');
define('DB', 'esmultiservicios_skincenter_cami');
define('DBIZZY', 'esmultiservicios_skincenter_izzy');
define('USER', 'esmultiservicios_root');
define('PASS', 'o8lXA0gtIO$@');

define('SERVER_MAIN', 'localhost');
define('DB_MAIN', 'esmultiservicios_izzy');
define('USER_MAIN', 'esmultiservicios_root');
define('PASS_MAIN', 'o8lXA0gtIO$@');

define('METHOD', 'AES-256-CBC');
define('SECRET_KEY', '$DP_@2020');
define('SECRET_IV', '10172');
define('SISTEMA_PRUEBA', 'NO');  // SI o NO
