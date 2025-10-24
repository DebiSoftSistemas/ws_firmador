<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
include_once ('src/funciones/igtech.funciones_login.php');

$app->get('/',function(Request $request,Response $response){
	$response->getBody()->write("Bienvenidos");
    return $response;
});

$app->get('/hola/{nombre}',function(Request $request,Response $response){
	$nombre= $request->getAttribute('nombre');
	$response->getBody()->write("Bienvenido ".$nombre);
    return $response;
});

$app->post('/login', function(Request $request, Response $response, array $args){
	$i_ct=	$request->getHeaderLine('Content-Type');
	$i_accept=$request->getHeaderLine('Accept');
	$i_body = $request->getBody();
	$w_data = json_decode($i_body, true);
	try{
		$o_respuesta=verificarLogin($w_data);	
	}catch(Throwable $e){
		$o_respuesta = imprimirError('9999', $e->getMessage());
	}
	$response->getBody()->write(enviarRespuesta($i_accept, $o_respuesta));
	return $response;
}); 

$app->post('/login_usuario', function(Request $request, Response $response){
	$i_ct=	$request->getHeaderLine('Content-Type');
	$i_accept=$request->getHeaderLine('Accept');
	$i_body = $request->getBody();
	$w_data = json_decode($i_body, true);
	try{
		$o_respuesta=verificarLoginUsuario($w_data);	
	}catch(Throwable $e){
		$o_respuesta=imprimirError('9999', $e->getMessage());
	}
	$response->getBody()->write(enviarRespuesta($i_accept,$o_respuesta));
	return $response;
}); 

$app->post('/logOut', function(Request $request, Response $response){
	$i_ct=	$request->getHeaderLine('Content-Type');
	$i_accept=$request->getHeaderLine('Accept');
	$i_token=$request->getHeaderLine('Authorization');	
	try{
		$o_respuesta=logOut($i_token);	
	}catch(Throwable $e){
		$o_respuesta=imprimirError('9999', $e->getMessage());
	}
	$response->getBody()->write(enviarRespuesta($i_accept,$o_respuesta));
	return $response;
});
?>