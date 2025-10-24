<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

include_once('src/funciones/igtech.autorizar_factura.php');
include_once('src/funciones/igtech.autorizar_liquidacioncompra.php');
include_once('src/funciones/igtech.autorizar_nota_credito.php');
include_once('src/funciones/igtech.autorizar_nota_debito.php');
include_once('src/funciones/igtech.autorizar_guia.php');
include_once('src/funciones/igtech.autorizar_retencion.php');
include_once('src/funciones/igtech.autorizar_retencion_dos.php');

include_once('src/funciones/igtech.obtenerFactura.php');
include_once('src/funciones/igtech.obtenerNotaCredito.php');
include_once('src/funciones/igtech.obtenerNotaDebito.php');
include_once('src/funciones/igtech.obtenerRetencion.php');



$app->get('/autorizarfactura/{id}', function (Request $request, Response $response) {
    $i_token = $request->getHeaderLine('Authorization');
    $i_ct = $request->getHeaderLine('Content-Type');
    $i_accept = $request->getHeaderLine('Accept');
    $i_factura = $request->getAttribute('id');
    try {
        $w_datos_token = Auth::GetData($i_token);
        $w_autorizacion = seleccionarToken($i_token);
        if ($w_datos_token->respuesta == 0 and $w_autorizacion['datos']['estado'] == 'V') {
            $o_respuesta = autorizar_factura($i_factura, 'S');
        } else {
            $o_respuesta = imprimirError('9998', 'Token invalido');
        }
    } catch (Throwable $e) {
        $o_respuesta = imprimirError('9999', $e->getMessage());
    }
    $response->getBody()->write(enviarRespuesta($i_accept, $o_respuesta));
    return $response;
});

$app->post('/autorizarfactura', function (Request $request, Response $response) {
    $i_token = $request->getHeaderLine('Authorization');
    $i_ct = $request->getHeaderLine('Content-Type');
    $i_accept = $request->getHeaderLine('Accept');
    $i_body = $request->getBody();
    $w_data = json_decode($i_body, true);
    if (!isset($w_data["numero"]))
        $o_respuesta = imprimirError('9998', 'numero dont defind');
    else if (!isset($w_data["sri_inmediata"]))
        $o_respuesta = imprimirError('9998', 'sri_inmediata dont defind');
    else {
        try {
            $w_datos_token = Auth::GetData($i_token);
            $w_autorizacion = seleccionarToken($i_token);
            if ($w_datos_token->respuesta == 0 and $w_autorizacion['datos']['estado'] == 'V') {
                $o_respuesta = autorizar_factura($w_data['numero'], $w_data["sri_inmediata"], );
            } else {
                $o_respuesta = imprimirError('9998', 'Token invalido');
            }
        } catch (Throwable $e) {
            $o_respuesta = imprimirError('9999', $e->getMessage());
        }
    }
    $response->getBody()->write(enviarRespuesta($i_accept, $o_respuesta));
    return $response;
});

$app->post('/autorizar', function (Request $request, Response $response) {
    $i_token = $request->getHeaderLine('Authorization');
    $i_ct = $request->getHeaderLine('Content-Type');
    $i_accept = $request->getHeaderLine('Accept');
    $i_body = $request->getBody();
    $w_data = json_decode($i_body, true);
    if (!isset($w_data["numero"]))
        $o_respuesta = imprimirError('9998', 'numero dont defind');
    else if (!isset($w_data["sri_inmediata"]))
        $o_respuesta = imprimirError('9998', 'sri_inmediata dont defind');
    else if (!isset($w_data["tipoDoc"]))
        $o_respuesta = imprimirError('9998', 'tipoDoc dont defind');
    else {
        try {
            $w_datos_token = Auth::GetData($i_token);
            $w_autorizacion = seleccionarToken($i_token);
            if ($w_datos_token->respuesta == 0 and $w_autorizacion['datos']['estado'] == 'V') {
                switch ($w_data["tipoDoc"]) {
                    case '01':
                        $o_respuesta = autorizar_factura($w_data['numero'], $w_data["sri_inmediata"] );
                        break;
                    case '03':
                        $o_respuesta = autorizar_liquidacion($w_data['numero'], $w_data["sri_inmediata"]);
                        break;
                    case '04':
                        $o_respuesta = autorizar_nc($w_data['numero'], $w_data["sri_inmediata"] );
                        break;
                    case '05':
                        $o_respuesta = autorizar_nd($w_data['numero'], $w_data["sri_inmediata"] );
                        break;
                    case '06':
                        $o_respuesta = autorizar_guia($w_data['numero'], $w_data["sri_inmediata"] );
                        break;
                    case '07': {
                            switch ($w_data["version"]) {
                                case '1':
                                    $o_respuesta = autorizar_retencion($w_data['numero'], $w_data["sri_inmediata"] );
                                    break;
                                case '2':
                                    $o_respuesta = autorizar_retencion_dos($w_data['numero'], $w_data["sri_inmediata"] );
                                    break;
                                default:
                                    $o_respuesta = autorizar_retencion($w_data['numero'], $w_data["sri_inmediata"] );
                            }
                        }
                        break;
                    default:
                        $o_respuesta = autorizar_factura($w_data['numero'], $w_data["sri_inmediata"] );
                }
            } else {
                $o_respuesta = imprimirError('9998', 'Token invalido');
            }
        } catch (Throwable $e) {
            $o_respuesta = imprimirError('9999', $e->getMessage());
        }
    }
    $response->getBody()->write(enviarRespuesta($i_accept, $o_respuesta));
    return $response;
});

$app->post('/obtener_documentos', function (Request $request, Response $response) {
    $i_token = $request->getHeaderLine('Authorization');
    $i_ct = $request->getHeaderLine('Content-Type');
    $i_accept = $request->getHeaderLine('Accept');
    $i_body = $request->getBody();
    $w_data = json_decode($i_body, true);
    if (!isset($w_data["autorizacion"]))
        $o_respuesta = imprimirError('9998', 'autorizacion dont defind');
    else if (!isset($w_data["tipoDoc"]))
        $o_respuesta = imprimirError('9998', 'tipoDoc dont defind');
    else {
        try {
            $w_datos_token = Auth::GetData($i_token);
            $w_autorizacion = seleccionarToken($i_token);
            if ($w_datos_token->respuesta == 0 and $w_autorizacion['datos']['estado'] == 'V') {
                switch ($w_data["tipoDoc"]) {
                    case '01':
                        $o_respuesta = obtener_factura($w_data['autorizacion']);
                        break;
                    case '04':
                        $o_respuesta = obtener_notaCredito($w_data['autorizacion']);
                        break;
                    case '05':
                        $o_respuesta = obtener_notaDebito($w_data['autorizacion']);
                        break;
                    case '07':
                        $o_respuesta = obtener_retencion($w_data['autorizacion']);
                        break;
                    default:
                        $o_respuesta = $o_respuesta = obtener_factura($w_data['autorizacion']);
                }
            } else {
                $o_respuesta = imprimirError('9998', 'Token invalido');
            }
        } catch (Throwable $e) {
            $o_respuesta = imprimirError('9999', $e->getMessage());
        }
    }
    $response->getBody()->write(enviarRespuesta($i_accept, $o_respuesta));
    return $response;
});
