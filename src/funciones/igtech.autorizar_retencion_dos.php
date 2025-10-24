<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');
include_once('src/funciones/igtech.funciones_correo.php');

function seleccionarEERetemcionV2($ret_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR DATOS RETENCION V2');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion V2: ' . $ret_numero);
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

function seleccionarDatosRetencionV2($ret_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR DATOS RETENCION V2');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion V2: ' . $ret_numero);
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
                  ret_correo_enviado,
                  pr_tipo_persona,
                  parte_relacionada
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
          'pr_tipo_persona' => $row->pr_tipo_persona,
          'parte_relacionada' => $row->parte_relacionada,
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

function seleccionarImpuestosSustentos($id){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR IMPUESTOS SUSTENTOS RETENCION');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion v2: ' . $id);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT 
                  isr_cod_impuesto,
                  isr_cod_porcentaje,
                  isr_base_imponible,
                  ti_tarifa,
                  isr_valor_impuesto 
                FROM v_del_impuestos_sustento_retencion_sri 
                WHERE isr_doc_sustento=" . $id;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        for ($i = 0; $i < pg_num_rows($result); $i++) {
          $row = pg_fetch_object($result, $i);
          array_push($w_respuesta, array(
            'cod_impuesto' => $row->isr_cod_impuesto,
            'cod_porcentaje' => $row->isr_cod_porcentaje,
            'base_imponible' => $row->isr_base_imponible,
            'tarifa' => $row->ti_tarifa,
            'valor_impuesto' => $row->isr_valor_impuesto,
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

function seleccionarSustentosSri($id){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR SUSTENTOS RETENCION SRI');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion v2: ' . $id);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT 
                   codigo
                  ,codigo_retencion
                  ,irs_base_imponible
                  ,irs_porcentaje_retencion
                  ,irs_valor_retenido 
                FROM v_del_datos_retencion_sustento_sri 
                WHERE irs_sustento=" . $id;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        for ($i = 0; $i < pg_num_rows($result); $i++) {
          $row = pg_fetch_object($result, $i);
          array_push($w_respuesta, array(
            'codigo' => $row->codigo,
            'codigo_retencion' => $row->codigo_retencion,
            'base_imponible' => $row->irs_base_imponible,
            'porcentaje_retencion' => $row->irs_porcentaje_retencion,
            'valor_retenido' => $row->irs_valor_retenido,
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

function selecionarPagosSustentos($id){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR PAGOS SUSTENTOS RETENCION');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion v2: ' . $id);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT 
                  psr_forma_pago,
                  psr_valor_pago 
                FROM del_forma_pago_sustento_retencion 
                WHERE psr_sustento_retencion=" . $id;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        for ($i = 0; $i < pg_num_rows($result); $i++) {
          $row = pg_fetch_object($result, $i);
          array_push($w_respuesta, array(
            'forma_pago' => $row->psr_forma_pago,
            'valor_pago' => $row->psr_valor_pago,
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

function selecionarSustentosRet($ret_numero){
  try {

    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR SUSTENTOS RETENCION');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion v2: ' . $ret_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT
                    sr_id,
                    sr_cod_sustento,
                    sr_cod_docsustento,
                    sr_num_doc_sustento,
                    sr_fechaemision,
                    sr_fecha_registro_contable,
                    sr_autorizacion,
                    sr_pago_loc_ext,
                    sr_tipo_regimen,
                    sr_pais_pago,
                    sr_aplica_doble_trib,
                    sr_pago_sujeto_retencion_nc,
                    sr_pago_reg_fis,
                    sr_total_reembolsos,
                    sr_total_baseimponible_reembolsos,
                    sr_total_impuesto_reeemboso,
                    sr_total_sin_impuestos,
                    sr_importe_total 
                  FROM
                    v_del_datos_sustento_retencion_sri
                  WHERE
                    sr_retencion=" . $ret_numero;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        for ($i = 0; $i < pg_num_rows($result); $i++) {
          $row = pg_fetch_object($result, $i);
          array_push($w_respuesta, array(
            'id' => $row->sr_id,
            'cod_sustento' => $row->sr_cod_sustento,
            'cod_docsustento' => $row->sr_cod_docsustento,
            'num_doc_sustento' => $row->sr_num_doc_sustento,
            'fechaemision' => $row->sr_fechaemision,
            'fecha_registro_contable' => $row->sr_fecha_registro_contable,
            'autorizacion' => $row->sr_autorizacion,
            'pago_loc_ext' => $row->sr_pago_loc_ext,
            'tipo_regimen' => $row->sr_tipo_regimen,
            'pais_pago' => $row->sr_pais_pago,
            'aplica_doble_trib' => $row->sr_aplica_doble_trib,
            'pago_sujeto_retencion_nc' => $row->sr_pago_sujeto_retencion_nc,
            'pago_reg_fis' => $row->sr_pago_reg_fis,
            'total_reembolsos' => $row->sr_total_reembolsos,
            'total_baseimponible_reembolsos' => $row->sr_total_baseimponible_reembolsos,
            'total_impuesto_reeemboso' => $row->sr_total_impuesto_reeemboso,
            'total_sin_impuestos' => $row->sr_total_sin_impuestos,
            'importe_total' => $row->sr_importe_total,
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

function autorizar_retencion_dos($ret_numero, $i_autorizar){
  try {
    // var_dump('dsa');
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' Autorizar Retencion V2');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Retencion V2:' . $ret_numero);
    $w_parametros = buscarParametro('RUTA_FIRMADOR', 'http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl');
    if ($w_parametros['error'] == '0') {
      $ruta_firmador = $w_parametros['datos'];
    }
    $correo_enviado = "";
    $procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
    $configApp = new \configAplicacion();
    $configCorreo = new \configCorreo();
    $retencion = new comprobanteRetencionDos();
    $w_parametros = buscarParametro('RUTA_EMPRESA', 'D:/Desarrollo/');


    if ($w_parametros['error'] == '0') {
      $dir = $w_parametros['datos'];
    }

    $w_parametros = buscarParametro('RUTA_IREPORT', 'D:/Desarrollo/IReport');


    if ($w_parametros['error'] == '0') {
      $dir_ireport = $w_parametros['datos'];
    }
    $w_datos_re = seleccionarEERetemcionV2($ret_numero);
    // if ($i_debug) var_dump($w_datos_re);

    if ($w_datos_re['error'] == '0') {
      $var_empresa = $w_datos_re['datos']['empresa'];
      $var_establecimiento = $w_datos_re['datos']['establecimiento'];
    }
    $w_datos_empresa = seleccionarDatosEmpresaSRI($var_empresa, $var_establecimiento);
    // if ($i_debug) var_dump($w_datos_empresa);
    if ($w_datos_empresa['error'] == '0') {
      $w_datos_retencion = seleccionarDatosRetencionV2($ret_numero);
      // if ($i_debug) var_dump($w_datos_retencion);
      if ($w_datos_retencion['error'] == '0') {
        $configApp->dirAutorizados = $dir . $w_datos_empresa['datos']['ruc'] . "/documentos/";
        $configApp->dirLogo =  $dir . $w_datos_empresa['datos']['ruc'] . "/logo/" . $w_datos_empresa['datos']['logo'];
        $configApp->dirFirma =  $dir . $w_datos_empresa['datos']['ruc'] . "/firma/" . $w_datos_empresa['datos']['firma'];
        $configApp->passFirma =  $w_datos_empresa['datos']['passFirma'];
        $configApp->dirIreport = $dir_ireport;
        $retencion->configAplicacion = $configApp;

        $configCorreo->correoAsunto = "Nueva Retencion V2";
        $configCorreo->correoHost =       $w_datos_empresa['datos']['correoHost'];
        $configCorreo->correoPass =       $w_datos_empresa['datos']['correoPass'];
        $configCorreo->correoPort =       $w_datos_empresa['datos']['correoPort'];
        $configCorreo->correoRemitente =  $w_datos_empresa['datos']['correoRemitente'];
        $configCorreo->sslHabilitado =    $w_datos_empresa['datos']['tipoConexion'];
        $configCorreo->rutaLogo =         $w_datos_empresa['datos']['rutaLogo'].$w_datos_empresa['datos']['ruc'] .'/'. $w_datos_empresa['datos']['logo'];
        $retencion->configCorreo = $configCorreo;
        //aqui
        $retencion->ambiente = $w_datos_retencion['datos']['ambiente'];
        $retencion->tipoEmision =  $w_datos_empresa['datos']['tipoEmision'];
        $retencion->razonSocial =  $w_datos_empresa['datos']['razonSocial'];
        $retencion->nombreComercial =  $w_datos_empresa['datos']['nombreComercial'];
        $retencion->ruc =  $w_datos_empresa['datos']['ruc'];
        $retencion->codDoc = $w_datos_retencion['datos']['tipo_comprobante'];
        $retencion->establecimiento = $w_datos_retencion['datos']['est_codigo'];
        $retencion->ptoEmision = $w_datos_retencion['datos']['pen_serie'];
        $retencion->secuencial = $w_datos_retencion['datos']['ret_secuencial'];
        $retencion->dirMatriz =  $w_datos_empresa['datos']['dirMatriz'];

        $retencion->padronMicroempresa = $w_datos_empresa['datos']['padronMicroempresa'];
        $retencion->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
        if ($w_datos_empresa['datos']['padronAgenteRetencion'] == 'S') {
          $w_parametros = buscarParametro('NUMERORESOAR', '1');
          // if ($i_debug) var_dump($w_parametros);
          if ($w_parametros['error'] == 0) {
            $retencion->numeroResolucion = $w_parametros['datos'];
          }
        }
        $retencion->artesanoCalificado = $w_datos_empresa['datos']['artesanoCalificado'];
        $retencion->padronRimpe = $w_datos_retencion['datos']['est_padronrimpe'];
        $retencion->leyendaRimpe = $w_datos_retencion['datos']['est_leyenda_rimpe'];

        $retencion->fechaEmision = $w_datos_retencion['datos']['fecha'];
        $retencion->dirEstablecimiento = $w_datos_retencion['datos']['est_direccion'];
        if ($w_datos_empresa['datos']['contribuyenteEspecial'] != '') {
          $retencion->contribuyenteEspecial = $w_datos_empresa['datos']['contribuyenteEspecial'];
        }
        $retencion->obligadoContabilidad = $w_datos_empresa['datos']['obligadoContabilidad'];
        $retencion->tipoIdentificacionSujetoRetenido = $w_datos_retencion['datos']['pr_tipo_identificacion'];
        if ($w_datos_retencion['datos']['pr_tipo_identificacion'] == '06' or $w_datos_retencion['datos']['pr_tipo_identificacion'] == '08') {
          $retencion->tipoSujetoRetenido = $w_datos_retencion['datos']['pr_tipo_persona'];
        }
        $retencion->parteRel = $w_datos_retencion['datos']['parte_relacionada'];
        $retencion->razonSocialSujetoRetenido = $w_datos_retencion['datos']['pr_nombre'];
        $retencion->identificacionSujetoRetenido = $w_datos_retencion['datos']['pr_identificacion'];
        $retencion->periodoFiscal = $w_datos_retencion['datos']['ret_periodo_fiscal'];
        $correo_enviado = $w_datos_retencion['datos']['ret_correo_enviado'];
        //cargamos los sustentos de la retencion
        $w_sustentos_ret = selecionarSustentosRet($ret_numero);
        // if ($i_debug) var_dump($w_sustentos_ret);
        $w_sustentos = array();
        if ($w_sustentos_ret['error'] <> '0') {
          echo "error al acceder a los sustentos";
        } else {
          for ($i = 0; $i < count($w_sustentos_ret['datos']); $i++) {
            $w_lis_sus = $w_sustentos_ret['datos'][$i];
            $w_sustento = new sustentoRetencion();
            $w_sustento->codSustento = $w_lis_sus['cod_sustento'];
            $w_sustento->codDocSustento = $w_lis_sus['cod_docsustento'];
            $w_sustento->numDocSustento = $w_lis_sus['num_doc_sustento'];
            $w_sustento->fechaEmisionDocSustento = $w_lis_sus['fechaemision'];
            $w_sustento->fechaRegistroContable = $w_lis_sus['fecha_registro_contable'];
            $w_sustento->numAutDocSustento = $w_lis_sus['autorizacion'];
            $w_sustento->pagoLocExt = $w_lis_sus['pago_loc_ext'];
            if ($w_lis_sus['pago_loc_ext'] == '02') {
              $w_sustento->tipoRegi = $w_lis_sus['tipo_regimen'];
              $w_sustento->paisEfecPago = $w_lis_sus['pais_pago'];
              $w_sustento->aplicConvDobTrib = $w_lis_sus['aplica_doble_trib'];
              $w_sustento->pagExtSujRetNorLeg = $w_lis_sus['pago_sujeto_retencion_nc'];
              $w_sustento->pagoRegFis = $w_lis_sus['pago_reg_fis'];
            }
            if ($w_lis_sus['cod_docsustento'] == '41') {
              $w_sustento->totalComprobantesReembolso = $w_lis_sus['total_reembolsos'];
              $w_sustento->totalBaseImponibleReembolso = $w_lis_sus['total_baseimponible_reembolsos'];
              $w_sustento->totalImpuestoReembolso = $w_lis_sus['total_impuesto_reeemboso'];
            }
            $w_sustento->totalSinImpuestos = $w_lis_sus['total_sin_impuestos'];
            $w_sustento->importeTotal = $w_lis_sus['importe_total'];

            $w_impuestos_sustento = array();
            $w_impuestosSus = seleccionarImpuestosSustentos($w_lis_sus['id']);
            if ($w_impuestosSus['error'] <> '0') {
              echo "error al acceder a los impuestos del sustento";
            } else {
              for ($j = 0; $j < count($w_impuestosSus['datos']); $j++) {
                $w_impuestos = $w_impuestosSus['datos'][$j];
                $w_impuesto_sustento = new impuestoDocSustento();
                $w_impuesto_sustento->codImpuestoDocSustento = $w_impuestos['cod_impuesto'];
                $w_impuesto_sustento->codigoPorcentaje = $w_impuestos['cod_porcentaje'];
                $w_impuesto_sustento->baseImponible = $w_impuestos['base_imponible'];
                $w_impuesto_sustento->tarifa = $w_impuestos['tarifa'];
                $w_impuesto_sustento->valorImpuesto = $w_impuestos['valor_impuesto'];
                $w_impuestos_sustento[$j] = $w_impuesto_sustento;
              }
              $w_sustento->impuestosDocSustento = $w_impuestos_sustento;
            }
            $w_retenciones = array();
            $w_sustentosSri = seleccionarSustentosSri($w_lis_sus['id']);
            if ($w_sustentosSri['error'] <> '0') {
              echo "error al acceder a las retenciones del sustento";
            } else {
              for ($k = 0; $k < count($w_sustentosSri['datos']); $k++) {
                $w_reten = $w_sustentosSri['datos'][$k];
                $w_retencion_sustento = new impuestoComprobanteRetencionDos();
                $w_retencion_sustento->codigo = $w_reten['codigo'];
                $w_retencion_sustento->codigoRetencion = $w_reten['codigo_retencion'];
                $w_retencion_sustento->baseImponible = $w_reten['base_imponible'];
                $w_retencion_sustento->porcentajeRetener = $w_reten['porcentaje_retencion'];
                $w_retencion_sustento->valorRetenido = $w_reten['valor_retenido'];
                $w_retenciones[$k] = $w_retencion_sustento;
              }
            }
            $w_sustento->retenciones = $w_retenciones;
            $w_pagos_sustento = array();
            $w_pagosSus = selecionarPagosSustentos($w_lis_sus['id']);
            // var_dump($w_pagosSus);
            if ($w_pagosSus['error'] <> '0') {
              echo "error al acceder a los pagos del sustento";
            } else {
              for ($l = 0; $l < count($w_pagosSus['datos']); $l++) {
                $w_pagosRet = $w_pagosSus['datos'][$l];
                $w_pago = new pagoSustento();
                $w_pago->formapago = $w_pagosRet['forma_pago'];
                $w_pago->total = $w_pagosRet['valor_pago'];
                $w_pagos_sustento[$l] = $w_pago;
              }
            }
            $w_sustento->pagos = $w_pagos_sustento;

            $w_sustentos[$i] = $w_sustento;
            // if ($i_debug) var_dump($w_sustentos[$i]);
          }
        }
        $retencion->docsSustento = $w_sustentos;
        // if ($i_debug) var_dump($retencion->docsSustento);
        // var_dump($retencion);
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
        // var_dump($camposAdicionales);
        $retencion->infoAdicional = $camposAdicionales;
        // var_dump($retencion);
        $Log->EscribirLog(' Respuesta: ' . var_export($retencion, true));
        $procesarComprobante = new procesarComprobante();
        $procesarComprobante->comprobante = $retencion;
        $procesarComprobante->envioSRI = false;
        $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
        $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
        // var_dump($res);
        if ($i_autorizar == 'S') {
          // var_dump('paso');
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
              $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
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
    }
  } catch (Throwable $e) {
    $o_respuesta = array('error' => '9999', 'mensaje' => $e->getMessage());
  }
  return $o_respuesta;
}
