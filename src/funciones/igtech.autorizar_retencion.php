<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');
include_once('src/funciones/igtech.funciones_correo.php');
function seleccionarEERetemcion($ret_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR DATOS RETENCION');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion: ' . $ret_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT ret_empresa,ret_establecimiento FROM del_retencion WHERE ret_numero=" . $ret_numero;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result, 0);
        $w_respuesta = array(
          'empresa' => $row->ret_empresa,
          'establecimiento' => $row->ret_establecimiento
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

function seleccionarDatosRetencion($ret_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR DATOS RETENCION');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion: ' . $ret_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT	
                    ret_numero,
                    ret_ambiente,
                    ret_tipo_comprobante,
                    fecha,
                    est_codigo,
                    pen_serie,
                    ret_secuencial,
                    est_direccion,
                    pr_tipo_identificacion,
                    pr_nombre,
                    pr_identificacion,
                    ret_periodo_fiscal, 
                    pr_direccion,
                    pr_email,
                    pr_telefono,
                    ret_comentario,
                    est_padronrimpe,
                    est_leyenda_rimpe,
                    ret_correo_enviado
                  FROM
                    v_del_datos_retencion_sri
                  WHERE ret_numero=" . $ret_numero;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result, 0);
        $w_respuesta = array(
          'numero' => $row->ret_numero,
          'ambiente' => $row->ret_ambiente,
          'tipo_comprobante' => $row->ret_tipo_comprobante,
          'fecha' => $row->fecha,
          'est_codigo' => $row->est_codigo,
          'pen_serie' => $row->pen_serie,
          'ret_secuencial' => $row->ret_secuencial,
          'est_direccion' => $row->est_direccion,
          'pr_tipo_identificacion' => $row->pr_tipo_identificacion,
          'pr_nombre' => $row->pr_nombre,
          'pr_identificacion' => $row->pr_identificacion,
          'ret_periodo_fiscal' => $row->ret_periodo_fiscal,
          'pr_direccion' => $row->pr_direccion,
          'pr_email' => $row->pr_email,
          'pr_telefono' => $row->pr_telefono,
          'ret_comentario' => $row->ret_comentario,
          'est_padronrimpe' => $row->est_padronrimpe,
          'est_leyenda_rimpe' => $row->est_leyenda_rimpe,
          'ret_correo_enviado' => $row->ret_correo_enviado,
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

function selecionarDetallesRetencion($ret_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR DETALLES RETENCION');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion: ' . $ret_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT	
                  dr_retencion,
                  dr_impuesto,
                  pri_codigo,
                  dr_base_imponible,
                  dr_porcentaje_retencion,
                  dr_valor_retenido,
                  dr_cod_doc_sustento,
                  doc_sustento,
                  fecha 
                FROM
                  v_del_detalle_retencion_sri
                where dr_retencion=" . $ret_numero . "
                order by dr_id";
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        for ($i = 0; $i < pg_num_rows($result); $i++) {
          $row = pg_fetch_object($result, $i);
          array_push($w_respuesta, array(
            'retencion' => $row->dr_retencion,
            'impuesto' => $row->dr_impuesto,
            'pri_codigo' => $row->pri_codigo,
            'base_imponible' => $row->dr_base_imponible,
            'porcentaje_retencion' => $row->dr_porcentaje_retencion,
            'valor_retenido' => $row->dr_valor_retenido,
            'cod_doc_sustento' => $row->dr_cod_doc_sustento,
            'doc_sustento' => $row->doc_sustento,
            'fecha' => $row->fecha,
          ));
        }
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

function autorizar_retencion($ret_numero, $i_autorizar){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' Autorizar Retencion ');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion :' . $ret_numero);
    $w_parametros = buscarParametro('RUTA_FIRMADOR', 'http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl');
    //if ($i_debug) var_dump($w_parametros);
    if ($w_parametros['error'] == '0') {
      $ruta_firmador = $w_parametros['datos'];
    }
    $correo_enviado = "";
    $procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
    $configApp = new \configAplicacion();
    $configCorreo = new \configCorreo();
    $retencion = new comprobanteRetencion();
    $w_parametros = buscarParametro('RUTA_EMPRESA', 'D:/Desarrollo/');
    //if ($i_debug) var_dump($w_parametros);

    if ($w_parametros['error'] == '0') {
      $dir = $w_parametros['datos'];
    }

    $w_parametros = buscarParametro('RUTA_IREPORT', 'D:/Desarrollo/IReport');
    //if ($i_debug) var_dump($w_parametros);

    if ($w_parametros['error'] == '0') {
      $dir_ireport = $w_parametros['datos'];
    }
    $w_datos_re = seleccionarEERetemcion($ret_numero);
    if ($w_datos_re['error'] == '0') {
      $var_empresa = $w_datos_re['datos']['empresa'];
      $var_establecimiento = $w_datos_re['datos']['establecimiento'];
    }
    $w_datos_empresa = seleccionarDatosEmpresaSRI($var_empresa, $var_establecimiento);
    if ($w_datos_empresa['error'] == '0') {
      $configApp->dirAutorizados = $dir . $w_datos_empresa['datos']['ruc'] . "/documentos/";
      $configApp->dirLogo =  $dir . $w_datos_empresa['datos']['ruc'] . "/logo/" . $w_datos_empresa['datos']['logo'];
      $configApp->dirFirma =  $dir . $w_datos_empresa['datos']['ruc'] . "/firma/" . $w_datos_empresa['datos']['firma'];
      $configApp->passFirma = $w_datos_empresa['datos']['passFirma'];
      $configApp->dirIreport = $dir_ireport;
      $retencion->configAplicacion = $configApp;

      $configCorreo->correoAsunto = "Nueva Retencion";
      $configCorreo->correoHost =       $w_datos_empresa['datos']['correoHost'];
      $configCorreo->correoPass =       $w_datos_empresa['datos']['correoPass'];
      $configCorreo->correoPort =       $w_datos_empresa['datos']['correoPort'];
      $configCorreo->correoRemitente =  $w_datos_empresa['datos']['correoRemitente'];
      $configCorreo->sslHabilitado =    $w_datos_empresa['datos']['tipoConexion'];
      $configCorreo->rutaLogo =         $w_datos_empresa['datos']['rutaLogo'].$w_datos_empresa['datos']['ruc'] .'/'. $w_datos_empresa['datos']['logo'];
      $retencion->configCorreo = $configCorreo;

      $retencion->ruc = $w_datos_empresa['datos']['ruc'];
      $retencion->razonSocial = $w_datos_empresa['datos']['razonSocial'];
      $retencion->nombreComercial = $w_datos_empresa['datos']['nombreComercial'];
      $retencion->dirMatriz = $w_datos_empresa['datos']['dirMatriz'];
      $retencion->obligadoContabilidad = $w_datos_empresa['datos']['obligadoContabilidad'];
      $retencion->tipoEmision = $w_datos_empresa['datos']['tipoEmision'];
      if ($w_datos_empresa['datos']['contribuyenteEspecial'] != '') {
        $retencion->contribuyenteEspecial = $w_datos_empresa['datos']['contribuyenteEspecial'];
      }
      $retencion->padronMicroempresa = $w_datos_empresa['datos']['padronMicroempresa'];
      $retencion->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
      if ($w_datos_empresa['datos']['padronAgenteRetencion'] == 'S') {
        $w_parametros = buscarParametro('NUMERORESOAR', '1');
        if ($w_parametros['error'] == 0) {
          $retencion->numeroResolucion = $w_parametros['datos'];
        }
      }
      $retencion->artesanoCalificado = $w_datos_empresa['datos']['artesanoCalificado'];
    }
    $w_datos_retencion = seleccionarDatosRetencion($ret_numero);
    // var_dump($w_datos_retencion);
    if ($w_datos_retencion['error'] == '0') {
      $correo_enviado = $w_datos_retencion['datos']['ret_correo_enviado'];
      $retencion->padronRimpe = $w_datos_retencion['datos']['est_padronrimpe'];
      $retencion->leyendaRimpe = $w_datos_retencion['datos']['est_leyenda_rimpe'];
      $retencion->ambiente = $w_datos_retencion['datos']['ambiente'];
      $retencion->codDoc = $w_datos_retencion['datos']['tipo_comprobante'];
      $retencion->fechaEmision = $w_datos_retencion['datos']['fecha'];
      $retencion->establecimiento = $w_datos_retencion['datos']['est_codigo'];
      $retencion->ptoEmision = $w_datos_retencion['datos']['pen_serie'];
      $retencion->secuencial = $w_datos_retencion['datos']['ret_secuencial'];
      $retencion->dirEstablecimiento = $w_datos_retencion['datos']['est_direccion'];
      $retencion->tipoIdentificacionSujetoRetenido = $w_datos_retencion['datos']['pr_tipo_identificacion'];
      $retencion->razonSocialSujetoRetenido = $w_datos_retencion['datos']['pr_nombre'];
      $retencion->identificacionSujetoRetenido = $w_datos_retencion['datos']['pr_identificacion'];
      $retencion->periodoFiscal = $w_datos_retencion['datos']['ret_periodo_fiscal'];
      $camposAdicionales = array();
      $i = 0;

      if ($w_datos_empresa['datos']['artesanoCalificado'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "artesanoCalificado";
        $campoAdicional->valor = 'Nro. ' . $w_datos_empresa['datos']['artesanoCalificado'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      if ($w_datos_retencion['datos']['pr_telefono'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "Telefono";
        $campoAdicional->valor = $w_datos_retencion['datos']['pr_telefono'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      if ($w_datos_retencion['datos']['pr_email'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "Email";
        $campoAdicional->valor = $w_datos_retencion['datos']['pr_email'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      if ($w_datos_retencion['datos']['pr_direccion'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "Direccion";
        $campoAdicional->valor = $w_datos_retencion['datos']['pr_direccion'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      if ($w_datos_retencion['datos']['ret_comentario'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "Comentario";
        $campoAdicional->valor = $w_datos_retencion['datos']['ret_comentario'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      $retencion->infoAdicional = $camposAdicionales;
      $impuestos = array();
      $w_detalles_ret = selecionarDetallesRetencion($ret_numero);
      if ($w_detalles_ret['error'] <> '0') {
        echo "error al acceder a los detalles";
      } else {
        for ($i = 0; $i < count($w_detalles_ret['datos']); $i++) {
          $w_detallesRet = $w_detalles_ret['datos'][$i];
          $impuesto = new impuestoComprobanteRetencion();
          $impuesto->codigo = $w_detallesRet['impuesto'];
          $impuesto->codigoRetencion = $w_detallesRet['pri_codigo'];
          $impuesto->baseImponible = $w_detallesRet['base_imponible'];
          $impuesto->porcentajeRetener = $w_detallesRet['porcentaje_retencion'];
          $impuesto->valorRetenido = $w_detallesRet['valor_retenido'];
          $impuesto->codDocSustento = $w_detallesRet['cod_doc_sustento'];
          $impuesto->numDocSustento = $w_detallesRet['doc_sustento'];
          $impuesto->fechaEmisionDocSustento = $w_detallesRet['fecha'];
          array_push($impuestos, $impuesto);
        }
        $retencion->impuestos = $impuestos;
      }
      // var_dump($retencion);
      $procesarComprobante = new procesarComprobante();
      $procesarComprobante->comprobante = $retencion;
      $procesarComprobante->envioSRI = false;
      $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
      $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
      if ($i_autorizar == 'S') {
        if ($res->return->estadoComprobante == "FIRMADO") {
          $procesarComprobante = new procesarComprobante();
          $procesarComprobante->comprobante = $retencion;
          $procesarComprobante->envioSRI = true;
          $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
          $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
        } else {
          if ($res->return->estadoComprobante == "PROCESANDOSE") {
            $retencionPendiente = new \comprobantePendiente();
            $retencionPendiente->configAplicacion = $configApp;
            $retencionPendiente->configCorreo = $configCorreo;
            $retencionPendiente->ambiente = $w_datos_retencion['datos']['ambiente'];
            $retencionPendiente->codDoc = $w_datos_retencion['datos']['tipo_comprobante'];
            $retencionPendiente->establecimiento = $w_datos_retencion['datos']['est_codigo'];
            $retencionPendiente->fechaEmision = $w_datos_retencion['datos']['fecha'];
            $retencionPendiente->ptoEmision = $w_datos_retencion['datos']['pen_serie'];
            $retencionPendiente->ruc = $w_datos_empresa['datos']['ruc'];
            $retencionPendiente->secuencial = $w_datos_retencion['datos']['ret_secuencial'];
            $retencionPendiente->tipoEmision = $w_datos_empresa['datos']['tipoEmision'];
            $retencionPendiente->padronMicroempresa = $w_datos_empresa['datos']['padronMicroempresa'];
            $retencionPendiente->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
            $retencionPendiente->padronRimpe = $w_datos_retencion['datos']['est_leyenda_rimpe'];
            $procesarComprobantePendiente = new \procesarComprobantePendiente();
            $procesarComprobantePendiente->comprobantePendiente = $retencionPendiente;
            $res = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
            if ($res->return->estadoComprobante == "PROCESANDOSE") {
              $res->return->estadoComprobante = "ERROR";
            }
          }
        }
      }

      $mensaje_final =  $res->return->estadoComprobante;
      if ($res->return->estadoComprobante == 'ERROR') {
        $mensaje_final .= $res->return->mensajes->mensaje;
        $update_sql =  "UPDATE  del_retencion
                        SET  ret_error_sri='" .$res->return->mensajes->mensaje . "',
                            ret_archivo='".crearNombreFicheroSinExtension($retencion)."'
                        WHERE ret_numero=" . $ret_numero;
      }

      if ($res->return->estadoComprobante == "FIRMADO") {
        $update_sql =  "UPDATE  del_retencion
                        SET ret_estado_sri='" . $res->return->estadoComprobante . "',
                            ret_archivo='".crearNombreFicheroSinExtension($retencion)."'
                        WHERE ret_numero=" . $ret_numero;
      }
      if ($res->return->estadoComprobante == 'AUTORIZADO') {
        
        if($correo_enviado=="NO"){
					$var_envio_correo=enviarCorreo($retencion,$w_datos_retencion['datos']['pr_email']);
                    $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
                    if($var_envio_correo){
						$correo_enviado="SI";
					}else{
						$correo_enviado="NO";
					}
				}  
        $update_sql = "UPDATE del_retencion 
                        SET ret_estado_sri='" . $res->return->estadoComprobante . "',
                            ret_archivo='".crearNombreFicheroSinExtension($retencion)."',
                            ret_clave='" . $res->return->claveAcceso . "',   
                            ret_autorizacion='" . $res->return->numeroAutorizacion . "',
                            ret_fecha_autorizacion='" . $res->return->fechaAutorizacion . "',
                            ret_correo_enviado='" . $correo_enviado . "'
                        WHERE ret_numero=" . $ret_numero;
      }
      if ($res->return->estadoComprobante == 'DEVUELTA') {
        if ($res->return->mensajes->mensaje == 'CLAVE ACCESO REGISTRADA') {
          if($correo_enviado=="NO"){
            $var_envio_correo=enviarCorreo($retencion,$w_datos_retencion['datos']['pr_email']);
                      $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
                      if($var_envio_correo){
              $correo_enviado="SI";
            }else{
              $correo_enviado="NO";
            }
          }  
          $update_sql = "UPDATE del_retencion 
								SET ret_estado_sri='AUTORIZADO',
                    ret_archivo='".crearNombreFicheroSinExtension($retencion)."',
									  ret_clave='" . $res->return->claveAcceso . "',   
									  ret_autorizacion='" . $res->return->claveAcceso . "',
									  ret_correo_enviado='" . $correo_enviado . "'
								WHERE ret_numero=" . $ret_numero;
        } else {
          $update_sql = "UPDATE del_retencion 
								SET ret_estado_sri='" . $res->return->estadoComprobante . "',
                    ret_archivo='".crearNombreFicheroSinExtension($retencion)."',
									  ret_clave='" . $res->return->claveAcceso . "',   
									  ret_error_sri='" . $res->return->mensajes->mensaje . "'
								WHERE ret_numero=" . $ret_numero;
        }
        $mensaje_final .= "-" . json_encode($res->return->mensajes);
      }
      if ($res->return->estadoComprobante == 'NO AUTORIZADO') {
        $update_sql = "UPDATE del_retencion 
								SET ret_estado_sri='" . $res->return->estadoComprobante . "',
                    ret_archivo='".crearNombreFicheroSinExtension($retencion)."',
									  ret_clave='" . $res->return->claveAcceso . "',   
									  ret_error_sri='" . $res->return->mensajes->mensaje . "',
									  ret_fecha_autorizacion='" . $res->return->fechaAutorizacion . "'
								WHERE ret_numero=" . $ret_numero;
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
  } catch (Throwable $e) {
    $o_respuesta = array('error' => '9999', 'mensaje' => $e->getMessage());
  }
  return $o_respuesta;
}
