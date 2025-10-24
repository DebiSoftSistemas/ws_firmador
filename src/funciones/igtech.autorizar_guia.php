<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');
include_once('src/funciones/igtech.funciones_correo.php');

function autorizar_guia($gr_numero, $i_autorizar){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' Autorizar Guia ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Guia:' . $gr_numero);

        $w_parametros = buscarParametro('RUTA_FIRMADOR', 'http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl');
        if ($w_parametros['error'] == '0') {
            $ruta_firmador = $w_parametros['datos'];
        }
        $procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
        $configApp = new \configAplicacion();
        $configCorreo = new \configCorreo();
        $guiaRemision = new  guiaRemision();

        $w_parametros = buscarParametro('RUTA_EMPRESA', 'D:/Desarrollos/');
        if ($w_parametros['error'] == '0') {
            $dir = $w_parametros['datos'];
        }

        $w_parametros = buscarParametro('RUTA_IREPORT', 'D:/Desarrollo/IReport');
        if ($w_parametros['error'] == '0') {
            $dir_ireport = $w_parametros['datos'];
        }

        $w_datos_guia = seleccionarDatosGuia($gr_numero);

        if ($w_datos_guia['error'] == '0') {
            $var_empresa = $w_datos_guia['datos']['empresa'];
            $var_establecimiento = $w_datos_guia['datos']['establecimiento'];
            $var_tipo_emision = $w_datos_guia['datos']['tipo_emision'];
        }
        if ($var_tipo_emision == 'E') {

            $w_datos_empresa = seleccionarDatosEmpresaSRI($var_empresa, $var_establecimiento);
            if ($w_datos_empresa['error'] == '0') {
                $configApp->dirAutorizados = $dir . $w_datos_empresa['datos']['ruc'] . "/documentos/";
                $configApp->dirLogo =        $dir . $w_datos_empresa['datos']['ruc'] . "/logo/" . $w_datos_empresa['datos']['logo'];
                $configApp->dirFirma =       $dir . $w_datos_empresa['datos']['ruc'] . "/firma/" . $w_datos_empresa['datos']['firma'];
                $configApp->passFirma =      $w_datos_empresa['datos']['passFirma'];
                $configApp->dirIreport =      $dir_ireport;
                $guiaRemision->configAplicacion = $configApp;

                $configCorreo->correoAsunto = "Nueva Guia de Remision";
                $configCorreo->correoHost =      $w_datos_empresa['datos']['correoHost'];
                $configCorreo->correoPass =      $w_datos_empresa['datos']['correoPass'];
                $configCorreo->correoPort =      $w_datos_empresa['datos']['correoPort'];
                $configCorreo->correoRemitente = $w_datos_empresa['datos']['correoRemitente'];
                $configCorreo->sslHabilitado =   $w_datos_empresa['datos']['tipoConexion'];
                $configCorreo->rutaLogo =        $w_datos_empresa['datos']['rutaLogo'].$w_datos_empresa['datos']['ruc'] .'/'. $w_datos_empresa['datos']['logo'];
                $guiaRemision->configCorreo =    $configCorreo;

                $guiaRemision->ruc                       = $w_datos_empresa['datos']['ruc'];
                $guiaRemision->razonSocial               = $w_datos_empresa['datos']['razonSocial'];
                $guiaRemision->nombreComercial           = $w_datos_empresa['datos']['nombreComercial'];
                $guiaRemision->dirMatriz                 = $w_datos_empresa['datos']['dirMatriz'];
                $guiaRemision->obligadoContabilidad      = $w_datos_empresa['datos']['obligadoContabilidad'];
                $guiaRemision->tipoEmision               = $w_datos_empresa['datos']['tipoEmision'];
                if ($w_datos_empresa['datos']['contribuyenteEspecial'] != '') {
                    $guiaRemision->contribuyenteEspecial = $w_datos_empresa['datos']['contribuyenteEspecial'];
                }
                $guiaRemision->padronMicroempresa    = $w_datos_empresa['datos']['padronMicroempresa'];
                $guiaRemision->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
                if ($w_datos_empresa['datos']['padronAgenteRetencion'] == 'S') {
                    $w_parametros = buscarParametro('NUMERORESOAR', '1');
                    if ($w_parametros['error'] == '0') {
                        $guiaRemision->numeroResolucion = $w_parametros['datos'];
                    }
                }
                $guiaRemision->artesanoCalificado    = $w_datos_empresa['datos']['artesanoCalificado'];
            }

            $w_datosGuiaSRI = seleccionarDatosGuiaSRI($gr_numero);
            if ($w_datosGuiaSRI['error'] == '0') {
                $correo_enviado =                                   $w_datosGuiaSRI['datos']['correo_enviado'];
                $guiaRemision->ambiente =                           $w_datosGuiaSRI['datos']['ambiente'];
                $guiaRemision->codDoc =                             $w_datosGuiaSRI['datos']['codDoc'];
                $guiaRemision->establecimiento =                    $w_datosGuiaSRI['datos']['establecimiento'];
                $guiaRemision->ptoEmision =                         $w_datosGuiaSRI['datos']['ptoEmision'];
                $guiaRemision->secuencial =                         $w_datosGuiaSRI['datos']['secuencial'];
                $guiaRemision->dirEstablecimiento =                 $w_datosGuiaSRI['datos']['dirEstablecimiento'];
                $guiaRemision->dirPartida =                         $w_datosGuiaSRI['datos']['dirPartida'];
                $guiaRemision->razonSocialTransportista =           $w_datosGuiaSRI['datos']['razonSocialTransportista'];
                $guiaRemision->tipoIdentificacionTransportista =    $w_datosGuiaSRI['datos']['tipoIdentificacionTransportista'];
                $guiaRemision->rucTransportista =                   $w_datosGuiaSRI['datos']['rucTransportista'];
                $guiaRemision->fechaIniTransporte =                 $w_datosGuiaSRI['datos']['fechaIniTransporte'];
                $guiaRemision->fechaFinTransporte =                 $w_datosGuiaSRI['datos']['fechaFinTransporte'];
                $guiaRemision->placa =                              $w_datosGuiaSRI['datos']['placa'];
                $guiaRemision->rise = "RISE";
                //aqui van los DESTINATARIOS
                $w_destinatariosGuiaSRI = listaDestinatariosGuiSRI($gr_numero);
                if ($w_destinatariosGuiaSRI['error'] == '0') {
                    $destinatarios_guiaRemision = array();
                    $w_destinatarios = $w_destinatariosGuiaSRI['datos'];
                    $w_cantidad_items = count($w_destinatarios);
                    for ($i = 0; $i < $w_cantidad_items; $i++) {
                        $item = $w_destinatarios[$i];
                        $destinatario = new Destinatario();
                        $destinatario->identificacionDestinatario =     $item['identificacionDestinatario'];
                        $destinatario->razonSocialDestinatario =        $item['razonSocialDestinatario'];
                        $destinatario->dirDestinatario =                $item['dirDestinatario'];
                        $destinatario->motivoTraslado =                 $item['motivoTraslado'];
                        $destinatario->docAduaneroUnico =               $item['docAduaneroUnico'];
                        $destinatario->codEstabDestino =                $item['codEstabDestino'];
                        $destinatario->ruta =                           $item['ruta'];
                        $destinatario->codDocSustento =                 $item['codDocSustento'];
                        $destinatario->numDocSustento =                 $item['numDocSustento'];
                        $destinatario->numAutDocSustento =              $item['numAutDocSustento'];
                        $destinatario->fechaEmisionDocSustento =        $item['fechaEmisionDocSustento'];
                        $detalles = array();
                        $w_detallesDestinatario = listaDetallesDestinatario($item['idDestinatario']);
                        if ($w_detallesDestinatario['error'] == '0') {
                            $w_detalles = $w_detallesDestinatario['datos'];
                            for ($j = 0; $j < count($w_detalles); $j++) {
                                $itemDetalle = $w_detalles[$j];
                                $detalle = new DetalleGuiaRemision();
                                $detalle->codigoInterno =   $itemDetalle['codigoInterno'];
                                $detalle->codigoAdicional = $itemDetalle['codigoAdicional'];
                                $detalle->descripcion =     $itemDetalle['descripcion'];
                                $detalle->cantidad =        $itemDetalle['cantidad'];
                                $detalles[$j] = $detalle;
                            }
                            $destinatario->detalles = $detalles;
                        }
                        $destinatarios_guiaRemision[$i] = $destinatario;
                    }
                    $guiaRemision->destinatarios = $destinatarios_guiaRemision;
                }
                $i = 0;
                $camposAdicionales = array();
                if ($w_datosGuiaSRI['datos']['email'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Email";
                    $campoAdicional->valor = $w_datosGuiaSRI['datos']['email'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
                $guiaRemision->infoAdicional = $camposAdicionales;
                $procesarComprobante = new procesarComprobante();
                $procesarComprobante->comprobante = $guiaRemision;
                $procesarComprobante->envioSRI = false; //El sistema si es true //1-Crea XML en el directorio de autorizado 
                $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
                $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
                // var_dump($res);
                if ($i_autorizar == 'S') {
                    // var_dump('paso');
                    if ($res->return->estadoComprobante == "FIRMADO") {
                        $procesarComprobante = new procesarComprobante();
                        $procesarComprobante->comprobante = $guiaRemision;
                        $procesarComprobante->envioSRI = true; //El sistema si es false 
                        $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
                        $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
                    } else {
                        if ($res->return->estadoComprobante == "PROCESANDOSE") {
                            $comprobantePendiente = new \comprobantePendiente();
                            $comprobantePendiente->configAplicacion = $configApp;
                            $comprobantePendiente->configCorreo = $configCorreo;
                            $comprobantePendiente->ambiente =               $w_datosGuiaSRI['datos']['ambiente'];
                            $comprobantePendiente->codDoc =                 $w_datosGuiaSRI['datos']['codDoc'];
                            $comprobantePendiente->establecimiento =        $w_datosGuiaSRI['datos']['establecimiento'];
                            $comprobantePendiente->fechaEmision =           $w_datosGuiaSRI['datos']['fechaEmision'];
                            $comprobantePendiente->ptoEmision =             $w_datosGuiaSRI['datos']['ptoEmision'];
                            $comprobantePendiente->ruc =                    $w_datos_empresa['datos']['ruc'];
                            $comprobantePendiente->secuencial =             $w_datosGuiaSRI['datos']['secuencial'];
                            $comprobantePendiente->tipoEmision =            $w_datos_empresa['datos']['tipoEmision'];
                            $comprobantePendiente->padronMicroempresa =     $w_datos_empresa['datos']['padronMicroempresa'];
                            $comprobantePendiente->padronAgenteRetencion =  $w_datos_empresa['datos']['padronAgenteRetencion'];
                            $procesarComprobantePendiente = new \procesarComprobantePendiente();
                            $procesarComprobantePendiente->comprobantePendiente = $comprobantePendiente;
                            $res = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
                            if ($res->return->estadoComprobante == "PROCESANDOSE") {
                                $res->return->estadoComprobante = "ERROR";
                            }
                        }
                    }
                }
                $mensaje_final =    $res->return->estadoComprobante;
                if ($res->return->estadoComprobante == 'ERROR') {
                    $mensaje_final .= $res->return->mensajes->mensaje;
                    $update_sql =  "UPDATE del_guia_remision 
                                    SET gr_error_sri='" . $res->return->mensajes->mensaje . "',
                                        gr_archivo='".crearNombreFicheroSinExtension($guiaRemision)."' 
                                    WHERE gr_numero=" . $gr_numero;
                }
                if ($res->return->estadoComprobante == "FIRMADO") {
                    $update_sql =  "UPDATE del_guia_remision 
                                    SET gr_estado_sri='" . $res->return->estadoComprobante . "',
                                        gr_archivo='".crearNombreFicheroSinExtension($guiaRemision)."' 
                                    WHERE gr_numero=" . $gr_numero;
                }

                if ($res->return->estadoComprobante == 'AUTORIZADO') {
                   
                    if($correo_enviado=="NO"){
                        $var_envio_correo=enviarCorreo($guiaRemision,$w_datosGuiaSRI['datos']['tr_email']);
                        $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
                        if($var_envio_correo){
                            $correo_enviado="SI";
                        }else{
                            $correo_enviado="NO";
                        }
                    }
                    $update_sql = "UPDATE del_guia_remision 
                                    SET  gr_estado_sri='" . $res->return->estadoComprobante . "',
                                         gr_archivo='".crearNombreFicheroSinExtension($guiaRemision)."',
                                         gr_clave='" . $res->return->claveAcceso . "',   
                                         gr_autorizacion='" . $res->return->numeroAutorizacion . "',
                                         gr_fecha_autorizacion='" . $res->return->fechaAutorizacion . "',
                                         gr_error_sri='',
                                         gr_correo_enviado='" . $correo_enviado . "'
                                    WHERE gr_numero=" . $gr_numero;
                }
                if ($res->return->estadoComprobante == 'DEVUELTA') {
                    if ($res->return->mensajes->mensaje == 'CLAVE ACCESO REGISTRADA') {
                        if($correo_enviado=="NO"){
                            $var_envio_correo=enviarCorreo($guiaRemision,$w_datosGuiaSRI['datos']['tr_email']);
                            $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
                            if($var_envio_correo){
                                $correo_enviado="SI";
                            }else{
                                $correo_enviado="NO";
                            }
                        }
                        $update_sql = "UPDATE del_guia_remision 
                                    SET  gr_estado_sri='AUTORIZADO',
                                         gr_archivo='".crearNombreFicheroSinExtension($guiaRemision)."',
                                         gr_clave='" . $res->return->claveAcceso . "',   
                                         gr_autorizacion='" . $res->return->claveAcceso . "',
                                         gr_correo_enviado='" . $correo_enviado . "'
                                    WHERE gr_numero=" . $gr_numero;
                    } else {
                        $update_sql = "UPDATE del_guia_remision 
                                    SET  gr_estado_sri='" . $res->return->estadoComprobante . "',
                                         gr_archivo='".crearNombreFicheroSinExtension($guiaRemision)."',
                                         gr_clave='" . $res->return->claveAcceso . "',   
                                         gr_error_sri='" . $res->return->mensajes->mensaje . "'
                                    WHERE gr_numero=" . $gr_numero;
                    }
                    $mensaje_final .= "-" . json_encode($res->return->mensajes);
                }
                if ($res->return->estadoComprobante == 'NO AUTORIZADO') {
                    $update_sql = "UPDATE del_guia_remision 
                                    SET  gr_estado_sri='" . $res->return->estadoComprobante . "',
                                         gr_archivo='".crearNombreFicheroSinExtension($guiaRemision)."',
                                         gr_clave='" . $res->return->claveAcceso . "',   
                                         gr_error_sri='" . $res->return->mensajes->mensaje . "',
                                         gr_fecha_autorizacion='" . $res->return->fechaAutorizacion . "'
                                    WHERE gr_numero=" . $gr_numero;
                    $mensaje_final .= "-" . json_encode($res->return->mensajes);
                }
                $ws_conexion = ws_coneccion_bdd();
                if (!$result = pg_query($ws_conexion, $update_sql)) {
                    $o_respuesta = array('error' => '9997', 'mensaje' => pg_last_error($ws_conexion));
                } else {
                    $o_respuesta = array('error' => '0', 'mensaje' => 'Ejecución exitosa', 'datos' => $mensaje_final);
                }
                $Log->EscribirLog(' Respuesta: ' . var_export($o_respuesta, true));
                return $o_respuesta;
            }
        }
    } catch (Throwable $e) {
        $o_respuesta = array('error' => '9999', 'mensaje' => $e->getMessage());
    }
    return $o_respuesta;
}

function seleccionarDatosGuia($gr_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' Seleccionar Datos Guia ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Guia :' . $gr_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT gr_empresa,
                            gr_establecimiento,
                            gr_tipo_libretin
                      FROM del_guia_remision 
                      WHERE gr_numero=" . $gr_numero;
        $Log->EscribirLog(' Consulta:' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            if (pg_num_rows($result) > 0) {
                $row = pg_fetch_object($result, 0);
                $w_respuesta = array(
                    'empresa' => $row->gr_empresa,
                    'establecimiento' => $row->gr_establecimiento,
                    'tipo_emision' => $row->gr_tipo_libretin
                );
            }
            $o_respuesta = array('error' => '0', 'mensaje' => 'ok', 'datos' => $w_respuesta);
        } else {
            $o_respuesta = array('error' => '9997', 'mensaje' => pg_last_error($ws_conexion));
        }
        $close = pg_close($ws_conexion);
    } catch (Throwable $e) {
        $o_respuesta = array('error' => '9999', 'mensaje' => $e->getMessage());
    }
    $Log->EscribirLog(' Respuesta: ' . var_export($o_respuesta, true));
    return $o_respuesta;
}

function seleccionarDatosGuiaSRI($gr_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' Seleccionar Datos Guia SRI ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Guia: ' . $gr_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT gr_numero,
                            gr_ambiente,
                            gr_tipo_comprobante,
                            est_codigo,
                            pen_serie,
                            gr_secuencial,
                            est_direccion,
                            gr_direccion_partida,
                            tr_nombre,
                            tr_tipo_identificacion,
                            tr_identificacion,
                            fecha_inicio,
                            fecha_fin,
                            tr_placa,
                            tr_email,
                            gr_correo_enviado
                    FROM v_del_datos_guia_remision
                    WHERE gr_numero=" . $gr_numero;
        $Log->EscribirLog(' Consulta:' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            if (pg_num_rows($result) > 0) {
                $row = pg_fetch_object($result, 0);
                $w_respuesta = array(
                    'numero' => $row->gr_numero,
                    'ambiente' => $row->gr_ambiente,
                    'codDoc' => $row->gr_tipo_comprobante,
                    'establecimiento' => $row->est_codigo,
                    'ptoEmision' => $row->pen_serie,
                    'secuencial' => $row->gr_secuencial,
                    'dirEstablecimiento' => $row->est_direccion,
                    'dirPartida' => $row->gr_direccion_partida,
                    'razonSocialTransportista' => $row->tr_nombre,
                    'tipoIdentificacionTransportista' => $row->tr_tipo_identificacion,
                    'rucTransportista' => $row->tr_identificacion,
                    'fechaIniTransporte' => $row->fecha_inicio,
                    'fechaFinTransporte' => $row->fecha_fin,
                    'placa' => $row->tr_placa,
                    'email' => $row->tr_email,
                    'correo_enviado' => $row->gr_correo_enviado
                );
            }
            $o_respuesta = array('error' => '0', 'mensaje' => 'ok', 'datos' => $w_respuesta);
        } else {
            $o_respuesta = array('error' => '9997', 'mensaje' => pg_last_error($ws_conexion));
        }
        $close = pg_close($ws_conexion);
    } catch (Throwable $e) {
        $o_respuesta = array('error' => '9999', 'mensaje' => $e->getMessage());
    }
    $Log->EscribirLog(' Respuesta: ' . var_export($o_respuesta, true));
    return $o_respuesta;
}

function listaDestinatariosGuiSRI($gr_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' Lista Destinatarios Guia SRI ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Guia :' . $gr_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT dg_guia,
                            dg_identificacion_destinatario,
                            dg_nombre_destinatario,
                            dg_direccion_destino,
                            dg_motivo_traslado,
                            dg_documento_aduanero,
                            coalesce(est_codigo,'') as est_codigo,
                            dg_ruta,
                            dg_coddoc_sustento,
                            dg_documento_sustento,
                            dg_autorizacion_sustento,
                            coalesce(dg_fecha_sustento,'') as fecha_sustento,
                            dg_id
                        FROM v_del_destinatario_guia_sri
                        where dg_guia=" . $gr_numero . "
                        order by dg_id";
        $Log->EscribirLog(' Consulta:' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            while ($row = pg_fetch_array($result)) {
                $w_respuesta[] = array(
                    'idGuia' =>                      $row['dg_guia'],
                    'identificacionDestinatario' =>  $row['dg_identificacion_destinatario'],
                    'razonSocialDestinatario' =>     $row['dg_nombre_destinatario'],
                    'dirDestinatario' =>             $row['dg_direccion_destino'],
                    'motivoTraslado' =>              $row['dg_motivo_traslado'],
                    'docAduaneroUnico' =>            $row['dg_documento_aduanero'],
                    'codEstabDestino' =>             $row['est_codigo'],
                    'ruta' =>                        $row['dg_ruta'],
                    'codDocSustento' =>              $row['dg_coddoc_sustento'],
                    'numDocSustento' =>              $row['dg_documento_sustento'],
                    'numAutDocSustento' =>           $row['dg_autorizacion_sustento'],
                    'fechaEmisionDocSustento' =>     $row['fecha_sustento'],
                    'idDestinatario' =>              $row['dg_id'],
                );
            }
            $o_respuesta = array('error' => '0', 'mensaje' => 'ok', 'datos' => $w_respuesta);
        } else {
            $o_respuesta = array('error' => '9997', 'mensaje' => pg_last_error($ws_conexion));
        }
        $close = pg_close($ws_conexion);
    } catch (Throwable $e) {
        $o_respuesta = array('error' => '9999', 'mensaje' => $e->getMessage());
    }
    $Log->EscribirLog(' Respuesta: ' . var_export($o_respuesta, true));
    return $o_respuesta;
}

function listaDetallesDestinatario($id_destinatario){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' Lista Detalles Destinatario ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Destinatario:' . $id_destinatario);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT 
                                dgd_destinatario,
                                pro_codigo,
                                pro_codigo_aux,
                                pro_descripcion,
                                dgd_cantidad 
                     FROM v_del_productos_destinatario_guia_sri
                     WHERE dgd_destinatario=" . $id_destinatario . "
                        order by dgd_id";
        $Log->EscribirLog(' Consulta:' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            while ($row = pg_fetch_array($result)) {
                $w_respuesta[] = array(
                    'destinatario' => $row['dgd_destinatario'],
                    'codigoInterno' => $row['pro_codigo'],
                    'codigoAdicional' => $row['pro_codigo_aux'],
                    'descripcion' => $row['pro_descripcion'],
                    'cantidad' => $row['dgd_cantidad'],
                );
            }
            $o_respuesta = array('error' => '0', 'mensaje' => 'ok', 'datos' => $w_respuesta);
        } else {
            $o_respuesta = array('error' => '9997', 'mensaje' => pg_last_error($ws_conexion));
        }
        $close = pg_close($ws_conexion);
    } catch (Throwable $e) {
        $o_respuesta = array('error' => '9999', 'mensaje' => $e->getMessage());
    }
    $Log->EscribirLog(' Respuesta: ' . var_export($o_respuesta, true));
    return $o_respuesta;
}
