<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');
include_once('src/funciones/igtech.funciones_correo.php');
function seleccionarEENotaDebito($nd_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR DATOS NOTA DEBITO');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Nota Debito: ' . $nd_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT nd_empresa,nd_establecimiento FROM del_nota_debito WHERE nd_numero=" . $nd_numero;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result, 0);
        $w_respuesta = array(
          'empresa' => $row->nd_empresa,
          'establecimiento' => $row->nd_establecimiento
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

function seleccionarDatosNotaDebito($nd_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR DATOS NOTA DEBITO');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Nota Debito: ' . $nd_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT
                    nd_numero,
                    nd_ambiente,
                    nd_tipo_comprobante,
                    fecha,
                    est_direccion,
                    est_codigo,
                    pen_serie,
                    nd_secuencial,
                    cl_tipo_identificacion,
                    cl_nombre,
                    cl_identificacion,
                    nd_cod_docmod,
                    nd_serie_docmod||'-'||nd_secuencial_docmod as serie_docmod,
                    fecha_docmod,
                    nd_subtotal,
                    nd_subtotal_iva,
                    nd_valor_iva,
                    nd_subtotal_cero,
                    nd_subtotal_no_objeto,
                    nd_subtotal_excento,
                    nd_valor_ice,
                    nd_total,
                    cl_direccion,
                    cl_email,
                    cl_telefono,
                    est_padronrimpe,
                    est_leyenda_rimpe,
                    nd_correo_enviado
                FROM
                    v_del_datos_nota_debito_sri
                WHERE nd_numero=" . $nd_numero;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result, 0);
        $w_respuesta = array(
          'numero' => $row->nd_numero,
          'ambiente' => $row->nd_ambiente,
          'tipo_comprobante' => $row->nd_tipo_comprobante,
          'fecha' => $row->fecha,
          'est_direccion' => $row->est_direccion,
          'est_codigo' => $row->est_codigo,
          'pen_serie' => $row->pen_serie,
          'nd_secuencial' => $row->nd_secuencial,
          'cl_tipo_identificacion' => $row->cl_tipo_identificacion,
          'cl_nombre' => $row->cl_nombre,
          'cl_identificacion' => $row->cl_identificacion,
          'nd_cod_docmod' => $row->nd_cod_docmod,
          'serie_docmod' => $row->serie_docmod,
          'fecha_docmod' => $row->fecha_docmod,
          'nd_subtotal' => $row->nd_subtotal,
          'nd_subtotal_iva' => $row->nd_subtotal_iva,
          'nd_valor_iva' => $row->nd_valor_iva,
          'nd_subtotal_cero' => $row->nd_subtotal_cero,
          'nd_subtotal_no_objeto' => $row->nd_subtotal_no_objeto,
          'nd_subtotal_excento' => $row->nd_subtotal_excento,
          'nd_valor_ice' => $row->nd_valor_ice,
          'nd_total' => $row->nd_total,
          'cl_direccion' => $row->cl_direccion,
          'cl_email' => $row->cl_email,
          'cl_telefono' => $row->cl_telefono,
          'est_padronrimpe' => $row->est_padronrimpe,
          'est_leyenda_rimpe' => $row->est_leyenda_rimpe,
          'nd_correo_enviado' => $row->nd_correo_enviado,
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

function seleccionarPorcentajeIce2($nd_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR PORCENTAJE ICE DE NOTA DEBITO');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Nota Debito: ' . $nd_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT  '3' as impuesto,
                    dnd_porcentaje_ice,
                  sum(dnd_base_ice)as sum_base_ice,
                  sum(dnd_valor_ice) as sum_valor_ice
                FROM del_detalle_nota_debito 
                WHERE dnd_porcentaje_ice<>'0' 
                and dnd_nota_debito=" . $nd_numero . "
                group by dnd_porcentaje_ice";
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result, 0);
        $w_respuesta = array(
          'impuesto' => $row->impuesto,
          'porcentaje_ice' => $row->dnd_porcentaje_ice,
          'sum_base_ice' => $row->sum_base_ice,
          'sum_valor_ice' => $row->sum_valor_ice
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

function listaIvasNotaDebito($nd_numero){
  try {
      $Log = new IgtechLog();
      $Log->Abrir();
      $Log->EscribirLog(' LISTA ICE FACTURA ');
      $Log->EscribirLog(' DATOS DE ENTRADA');
      $Log->EscribirLog(' Factura :' . $nd_numero);
      $ws_conexion = ws_coneccion_bdd();
      $select_sql = "SELECT  '2' as codigo,
                              dnd_porcentaje_iva,
												ROUND(sum(1*(dnd_precio_unitario)+dnd_valor_ice),2) as baseimponible,
												round(sum(dnd_base_iva) *iva_porcentaje/100,2) as valor
										FROM  del_detalle_nota_debito 
										inner join sri_tarifa_iva on iva_codigo=dnd_porcentaje_iva
										WHERE dnd_nota_debito=".$nd_numero."
										group by dnd_porcentaje_iva,iva_porcentaje";
      $Log->EscribirLog(' Consulta:' . $select_sql);
      if ($result = pg_query($ws_conexion, $select_sql)) {
          $w_respuesta = array(); //creamos un array
          while ($row = pg_fetch_array($result)) {
              $w_respuesta[] = array(
                  'codigo' => $row['codigo'],
                  'codigoPorcentaje' => $row['dnd_porcentaje_iva'],
                  'baseImponible' => $row['baseimponible'],
                  'valor' => $row['valor'],
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

function seleccionarPagoNotaDebito($nd_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR PAGOS DE NOTA DEBITO');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Nota Debito: ' . $nd_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT 	a.fp_id as id,
                  sri_forma_pago.fp_codigo as codigo,
                  a.fp_valor as valor,
                  coalesce(a.fp_plazo,0) as plazo,
                  coalesce(a.fp_unidad_tiempo,'DIAS') as unidad_tiempo
              FROM del_forma_pago_nd a 
              inner join del_forma_pago b on a.fp_forma_pago=b.fp_id	
              inner join sri_forma_pago on b.fp_sri=sri_forma_pago.fp_codigo
              where a.fp_nota_debito=" . $nd_numero;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        for ($i = 0; $i < pg_num_rows($result); $i++) {
          $row = pg_fetch_object($result, $i);
          array_push($w_respuesta, array(
            'id' => $row->id,
            'codigo' => $row->codigo,
            'valor' => $row->valor,
            'plazo' => $row->plazo,
            'unidad_tiempo' => $row->unidad_tiempo
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

function seleccionarMotivos($nd_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR MOTIVOS DE NOTA DEBITO');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Nota Debito: ' . $nd_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT pro_descripcion,
                    dnd_precio_unitario 
                FROM v_del_detalle_nota_debito_sri
                WHERE dnd_nota_debito=" . $nd_numero . "
                order by dnd_id";
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        for ($i = 0; $i < pg_num_rows($result); $i++) {
          $row = pg_fetch_object($result, $i);
          array_push($w_respuesta, array(
            'descripcion' => $row->pro_descripcion,
            'precio_unitario' => $row->dnd_precio_unitario,
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

function autorizar_nd($nd_numero, $i_autorizar, $i_log = false){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' Autorizar Nota Debito ');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Nota Debito :' . $nd_numero);
    $w_parametros = buscarParametro('RUTA_FIRMADOR', 'http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl');
    if ($w_parametros['error'] == '0') {
      $ruta_firmador = $w_parametros['datos'];
    }
    $correo_enviado = '';
    $procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
    $configApp = new \configAplicacion();
    $configCorreo = new \configCorreo();
    $notaDebito = new notaDebito();
    $w_parametros = buscarParametro('RUTA_EMPRESA', 'D:/Desarrollos/');
    if ($w_parametros['error'] == '0') {
      $dir = $w_parametros['datos'];
    }

    $w_parametros = buscarParametro('RUTA_IREPORT', 'D:/Desarrollo/IReport');
    if ($w_parametros['error'] == '0') {
      $dir_ireport = $w_parametros['datos'];
    }
    $w_datos_nd = seleccionarEENotaDebito($nd_numero);
    if ($w_datos_nd['error'] == '0') {
      $var_empresa = $w_datos_nd['datos']['empresa'];
      $var_establecimiento = $w_datos_nd['datos']['establecimiento'];
    }
    $w_datos_empresa = seleccionarDatosEmpresaSRI($var_empresa, $var_establecimiento);
    if ($w_datos_empresa['error'] == '0') {
      $configApp->dirAutorizados =    $dir . $w_datos_empresa['datos']['ruc'] . "/documentos/";
      $configApp->dirLogo =           $dir . $w_datos_empresa['datos']['ruc'] . "/logo/" .  $w_datos_empresa['datos']['logo'];
      $configApp->dirFirma =          $dir . $w_datos_empresa['datos']['ruc'] . "/firma/" . $w_datos_empresa['datos']['firma'];
      $configApp->passFirma =         $w_datos_empresa['datos']['passFirma'];
      $configApp->dirIreport =         $dir_ireport;
      $notaDebito->configAplicacion = $configApp;

      $configCorreo->correoAsunto = "Nueva Nota de Debito";
      $configCorreo->correoHost =       $w_datos_empresa['datos']['correoHost'];
      $configCorreo->correoPass =       $w_datos_empresa['datos']['correoPass'];
      $configCorreo->correoPort =       $w_datos_empresa['datos']['correoPort'];
      $configCorreo->correoRemitente =  $w_datos_empresa['datos']['correoRemitente'];
      $configCorreo->sslHabilitado =    $w_datos_empresa['datos']['tipoConexion'];
      $configCorreo->rutaLogo =         $w_datos_empresa['datos']['rutaLogo'].$w_datos_empresa['datos']['ruc'] .'/'. $w_datos_empresa['datos']['logo'];
      $notaDebito->configCorreo = $configCorreo;

      $notaDebito->ruc = $w_datos_empresa['datos']['ruc'];
      $notaDebito->razonSocial = $w_datos_empresa['datos']['razonSocial'];
      $notaDebito->nombreComercial = $w_datos_empresa['datos']['nombreComercial'];
      $notaDebito->dirMatriz = $w_datos_empresa['datos']['dirMatriz'];
      $notaDebito->obligadoContabilidad = $w_datos_empresa['datos']['obligadoContabilidad'];
      $notaDebito->tipoEmision = $w_datos_empresa['datos']['tipoEmision'];
      if ($w_datos_empresa['datos']['contribuyenteEspecial'] != '') {
        $notaDebito->contribuyenteEspecial = $w_datos_empresa['datos']['contribuyenteEspecial'];
      }

      $notaDebito->padronMicroempresa = $w_datos_empresa['datos']['padronMicroempresa'];
      $notaDebito->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
      if ($w_datos_empresa['datos']['padronAgenteRetencion'] == 'S') {
        $w_parametros = buscarParametro('NUMERORESOAR', '1');
        if ($w_parametros['error'] == 0) {
          $notaDebito->numeroResolucion = $w_parametros['datos'];
        }
      }
      $notaDebito->artesanoCalificado = $w_datos_empresa['datos']['artesanoCalificado'];
    }
    $w_datos_notaDebito = seleccionarDatosNotaDebito($nd_numero);
    if ($w_datos_notaDebito['error'] == '0') {
      $correo_enviado = $w_datos_notaDebito['datos']['nd_correo_enviado'];
      $notaDebito->padronRimpe = $w_datos_notaDebito['datos']['est_padronrimpe'];
      $notaDebito->leyendaRimpe = $w_datos_notaDebito['datos']['est_leyenda_rimpe'];
      $notaDebito->ambiente = $w_datos_notaDebito['datos']['ambiente'];
      $notaDebito->codDoc = $w_datos_notaDebito['datos']['tipo_comprobante'];
      $notaDebito->fechaEmision = $w_datos_notaDebito['datos']['fecha'];
      $notaDebito->dirEstablecimiento = $w_datos_notaDebito['datos']['est_direccion'];
      $notaDebito->establecimiento = $w_datos_notaDebito['datos']['est_codigo'];
      $notaDebito->ptoEmision = $w_datos_notaDebito['datos']['pen_serie'];
      $notaDebito->secuencial = $w_datos_notaDebito['datos']['nd_secuencial'];
      $notaDebito->tipoIdentificacionComprador = $w_datos_notaDebito['datos']['cl_tipo_identificacion'];
      $notaDebito->razonSocialComprador = $w_datos_notaDebito['datos']['cl_nombre'];
      $notaDebito->identificacionComprador = $w_datos_notaDebito['datos']['cl_identificacion'];
      $notaDebito->codDocModificado = $w_datos_notaDebito['datos']['nd_cod_docmod'];
      $notaDebito->numDocModificado = $w_datos_notaDebito['datos']['serie_docmod'];
      $notaDebito->fechaEmisionDocSustento = $w_datos_notaDebito['datos']['fecha_docmod'];
      $notaDebito->totalSinImpuestos = $w_datos_notaDebito['datos']['nd_subtotal'];
      $total_Impuestos = array();
      $i = 0;
      //if ($w_datos_notaDebito['datos']['nd_subtotal_iva'] > 0) {
        $w_datos_ivas = listaIvasNotaDebito($nd_numero);
          if ($w_datos_ivas['error'] == '0') {
              $w_item_iva = $w_datos_ivas['datos'];
              for ($j = 0; $j < count($w_item_iva); $j++) {
                  $item_iva = $w_item_iva[$j];
                  $totalImpuesto = new totalImpuesto();
                  $totalImpuesto->codigo =            $item_iva['codigo'];
                  $totalImpuesto->codigoPorcentaje =  $item_iva['codigoPorcentaje'];
                  $totalImpuesto->baseImponible =     $item_iva['baseImponible'];
                  $totalImpuesto->valor =             $item_iva['valor'];
                  $total_Impuestos[$i] = $totalImpuesto;
                  $i += 1;
              }
          }
      //}
      if ($i==0){
        $totalImpuesto = new totalImpuesto();
        $totalImpuesto->codigo ='2'; 
        $totalImpuesto->codigoPorcentaje = '0'; 
        $totalImpuesto->baseImponible = '0.00'; 
        $totalImpuesto->valor = '0.00';
        $total_Impuestos[$i]=$totalImpuesto;
        $i += 1;
      }
      if ($w_datos_notaDebito['datos']['nd_valor_ice'] > 0) {
        $w_porcentajeIce = seleccionarPorcentajeIce2($nd_numero);
        if ($w_porcentajeIce['error'] == '0') {
          $w_item_ice = $w_porcentajeIce['datos'];
          for ($j = 0; $j < count($w_item_ice); $j++) {
            $item_ice = $w_item_ice[$j];
            $totalImpuesto = new totalImpuesto();
            $totalImpuesto->codigo = $item_ice['impuesto'];
            $totalImpuesto->codigoPorcentaje =  $item_ice['porcentaje_ice'];
            $totalImpuesto->baseImponible =  $item_ice['sum_base_ice'];
            $totalImpuesto->valor = $item_ice['sum_valor_ice'];
            $total_Impuestos[$i] = $totalImpuesto;
            $i += 1;
          }
        }
      }
      $notaDebito->impuestos = $total_Impuestos;
      $notaDebito->valorTotal = $w_datos_notaDebito['datos']['nd_total'];
      //pagos
      $pagos = array();
      $w_pagos_notaCredito = seleccionarPagoNotaDebito($nd_numero);
      if ($w_pagos_notaCredito['error'] <> '0') {
        echo "Error al acceder a las formas de pago";
      } else {
        for ($i = 0; $i < count($w_pagos_notaCredito['datos']); $i++) {
          $w_formaPago = $w_pagos_notaCredito['datos'][$i];
          $pago = new pagos();
          $pago->formaPago = $w_formaPago['codigo'];
          $pago->total = $w_formaPago['valor'];
          $pago->plazo = $w_formaPago['plazo'];
          $pago->unidadTiempo = $w_formaPago['unidad_tiempo'];
          array_push($pagos, $pago);
        }
      }
      $notaDebito->pagos = $pagos;
      //aqui van los detalles
      $w_motivos = seleccionarMotivos($nd_numero);
      // var_dump($w_motivos);
      if ($w_motivos['error'] <> '0') {
        echo "Error al acceder al detalle de la notaDebito";
      } else {
        for ($i = 0; $i < count($w_motivos['datos']); $i++) {
          $w_dato_motivos = $w_motivos['datos'][$i];
          $motivo = new motivo();
          $motivo->razon = $w_dato_motivos['descripcion'];
          $motivo->valor = $w_dato_motivos['precio_unitario'];
          $motivos[$i] = $motivo;
          array_push($motivos, $motivo);
        }
        $notaDebito->motivos = $motivos;
      }
      $camposAdicionales = array();
      $i = 0;
      if ($w_datos_empresa['datos']['artesanoCalificado'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "artesanoCalificado";
        $campoAdicional->valor = 'Nro. ' . $w_datos_empresa['datos']['artesanoCalificado'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      if ($w_datos_notaDebito['datos']['cl_telefono'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "Telefono";
        $campoAdicional->valor = $w_datos_notaDebito['datos']['cl_telefono'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      if ($w_datos_notaDebito['datos']['cl_email'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "Email";
        $campoAdicional->valor = $w_datos_notaDebito['datos']['cl_email'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      if ($w_datos_notaDebito['datos']['cl_direccion'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "Direccion";
        $campoAdicional->valor = $w_datos_notaDebito['datos']['cl_direccion'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      $notaDebito->infoAdicional = $camposAdicionales;

      $procesarComprobante = new procesarComprobante();
      $procesarComprobante->comprobante = $notaDebito;
      $procesarComprobante->envioSRI = false; //El sistema si es true //1-Crea XML en el directorio de autorizado 
      $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
      $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
      if ($i_autorizar == 'S') {
        if ($res->return->estadoComprobante == "FIRMADO") {
          $procesarComprobante = new procesarComprobante();
          $procesarComprobante->comprobante = $notaDebito;
          $procesarComprobante->envioSRI = true; //El sistema si es false 
          $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
          $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
        } else {
          if ($res->return->estadoComprobante == "PROCESANDOSE") {
            $comprobantePendiente = new \comprobantePendiente();
            $comprobantePendiente->configAplicacion = $configApp;
            $comprobantePendiente->configCorreo = $configCorreo;
            $comprobantePendiente->ambiente = $w_datos_notaDebito['datos']['ambiente'];
            $comprobantePendiente->codDoc = $w_datos_notaDebito['datos']['tipo_comprobante'];
            $comprobantePendiente->establecimiento = $w_datos_notaDebito['datos']['est_codigo'];
            $comprobantePendiente->fechaEmision = $w_datos_notaDebito['datos']['fecha'];
            $comprobantePendiente->ptoEmision = $w_datos_notaDebito['datos']['pen_serie'];
            $comprobantePendiente->ruc = $w_datos_empresa['datos']['ruc'];
            $comprobantePendiente->secuencial = $w_datos_notaDebito['datos']['nd_secuencial'];
            $comprobantePendiente->tipoEmision = $w_datos_empresa['datos']['tipoEmision'];
            $comprobantePendiente->padronMicroempresa = $w_datos_empresa['datos']['padronMicroempresa'];
            $comprobantePendiente->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
            $comprobantePendiente->padronRimpe = $w_datos_notaDebito['datos']['est_padronrimpe'];
            $procesarComprobantePendiente = new \procesarComprobantePendiente();
            $procesarComprobantePendiente->comprobantePendiente = $comprobantePendiente;
            $res = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
            if ($res->return->estadoComprobante == "PROCESANDOSE") {
              $res->return->estadoComprobante = "ERROR";
            }
          }
        }
      }
      $mensaje_final =  $res->return->estadoComprobante;
      //echo $res->return->estadoComprobante;
      if ($res->return->estadoComprobante == 'ERROR') {
        $mensaje_final .= $res->return->mensajes->mensaje;
        $update_sql =  "UPDATE del_nota_debito 
                        SET nd_error_sri='" . $res->return->mensajes->mensaje . "',
                            nd_archivo='".crearNombreFicheroSinExtension($notaDebito)."' 
                        WHERE nd_numero=" . $nd_numero;
      }

      if ($res->return->estadoComprobante == "FIRMADO") {
        $update_sql =  "UPDATE del_nota_debito 
                        SET nd_estado_sri='" . $res->return->estadoComprobante . "',
                            nd_archivo='".crearNombreFicheroSinExtension($notaDebito)."' 
                        WHERE nd_numero=" . $nd_numero;
      }

      if ($res->return->estadoComprobante == 'AUTORIZADO') {
        if ($correo_enviado == "NO") {
          $var_envio_correo=enviarCorreo($notaDebito, $w_datos_notaDebito['datos']['cl_email']);
          $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
          if ($var_envio_correo) {
            $correo_enviado = "SI";
          } else {
            $correo_enviado = "NO";
          }
        }
        $update_sql = "UPDATE del_nota_debito 
                        SET nd_estado_sri='" . $res->return->estadoComprobante . "',
                            nd_archivo='".crearNombreFicheroSinExtension($notaDebito)."' ,
                            nd_clave='" . $res->return->claveAcceso . "',   
                            nd_autorizacion='" . $res->return->numeroAutorizacion . "',
                            nd_fecha_autorizacion='" . $res->return->fechaAutorizacion . "',
                            nd_error_sri='',
                            nd_correo_enviado='" . $correo_enviado . "'
                        WHERE nd_numero=" . $nd_numero;
      }
      if ($res->return->estadoComprobante == 'DEVUELTA') {
        if ($res->return->mensajes->mensaje == 'CLAVE ACCESO REGISTRADA') {
          if ($correo_enviado == "NO") {
            $var_envio_correo=enviarCorreo($notaDebito, $w_datos_notaDebito['datos']['cl_email']);
            $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
            if ($var_envio_correo) {
              $correo_enviado = "SI";
            } else {
              $correo_enviado = "NO";
            }
          }
          $update_sql = "UPDATE del_nota_debito 
                          SET nd_estado_sri='AUTORIZADO',
                              nd_archivo='".crearNombreFicheroSinExtension($notaDebito)."' ,
                              nd_clave='" . $res->return->claveAcceso . "',   
                              nd_autorizacion='" . $res->return->claveAcceso . "',
                              nd_correo_enviado='" . $correo_enviado . "'
                          WHERE nd_numero=" . $nd_numero;
        } else {
          $update_sql = "UPDATE del_nota_debito 
                          SET nd_estado_sri='" . $res->return->estadoComprobante . "',
                              nd_archivo='".crearNombreFicheroSinExtension($notaDebito)."' ,
                              nd_clave='" . $res->return->claveAcceso . "',   
                            nd_error_sri='" . $res->return->mensajes->mensaje . "'
                          WHERE nd_numero=" . $nd_numero;
        }
        $mensaje_final .= "-" . json_encode($res->return->mensajes);
      }
      if ($res->return->estadoComprobante == 'NO AUTORIZADO') {
        $update_sql = "UPDATE del_nota_debito 
                        SET nd_estado_sri='" . $res->return->estadoComprobante . "',
                            nd_archivo='".crearNombreFicheroSinExtension($notaDebito)."' ,
                            nd_clave='" . $res->return->claveAcceso . "',   
                            nd_error_sri='" . $res->return->mensajes->mensaje . "',
                            nd_fecha_autorizacion='" . $res->return->fechaAutorizacion . "'
                        WHERE nd_numero=" . $nd_numero;
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
