<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;
require 'src/config/auth.php';
require 'src/config/ws_config.php';
require 'src/config/respuesta.php';
require __DIR__ .'/vendor/autoload.php';
include_once ('src/funciones/igtech.log.php');
$app = AppFactory::create();
$app->setBasePath("/ws_firmador");
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

require_once 'src/rutas/rutas.php';

$app->run();


?>