<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');
include_once('src/funciones/igtech.funciones_correo.php');

function seleccionarEENotaCredito($nc_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR DATOS NOTA CREDITO');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Nota Debito: ' . $nc_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT nc_empresa,nc_establecimiento FROM del_nota_credito WHERE nc_numero=" . $nc_numero;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result, 0);
        $w_respuesta = array(
          'empresa' => $row->nc_empresa,
          'establecimiento' => $row->nc_establecimiento
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
function seleccionarDatosNotaCredito($nc_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR DATOS NOTA CREDITO');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Factura: ' . $nc_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT
                    nc_numero,
                    nc_ambiente,
                    nc_tipo_comprobante,
                    fecha,
                    est_direccion,
                    est_codigo,
                    pen_serie,
                    nc_secuencial,
                    cl_tipo_identificacion,
                    cl_nombre,
                    cl_identificacion,
                    nc_cod_docmod,
                    nc_secuencial_docmod,
                    fecha_docmod,
                    nc_subtotal,
                    nc_total_descuento,
                    nc_subtotal_iva,
                    nc_valor_iva,
                    nc_subtotal_cero,
                    nc_subtotal_no_objeto,
                    nc_subtotal_excento,
                    nc_valor_ice,
                    nc_valor_irbpnr,
                    nc_total,
                    nc_motivo,
                    sp_busca_parametro('MONEDASRI'::character varying, 'DOLAR'::character varying) AS nc_moneda,
                    cl_direccion,
                    cl_email,
                    cl_telefono,
                    usuario,
                    usu_cedula,
                    usu_telefono,
                    usu_email,
                    usu_placa,
                    usu_tipo_documento,
                    est_padronrimpe,
                    est_leyenda_rimpe,
                    nc_correo_enviado
                  FROM
                    v_del_datos_nota_credito_sri
                  WHERE nc_numero=" . $nc_numero;
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result, 0);
        $w_respuesta = array(
          'numero' => $row->nc_numero,
          'ambiente' => $row->nc_ambiente,
          'tipo_comprobante' => $row->nc_tipo_comprobante,
          'fecha' => $row->fecha,
          'est_direccion' => $row->est_direccion,
          'est_codigo' => $row->est_codigo,
          'pen_serie' => $row->pen_serie,
          'nc_secuencial' => $row->nc_secuencial,
          'cl_tipo_identificacion' => $row->cl_tipo_identificacion,
          'cl_nombre' => $row->cl_nombre,
          'cl_identificacion' => $row->cl_identificacion,
          'nc_cod_docmod' => $row->nc_cod_docmod,
          'nc_secuencial_docmod' => $row->nc_secuencial_docmod,
          'fecha_docmod' => $row->fecha_docmod,
          'nc_subtotal' => $row->nc_subtotal,
          'nc_total_descuento' => $row->nc_total_descuento,
          'nc_subtotal_iva' => $row->nc_subtotal_iva,
          'nc_valor_iva' => $row->nc_valor_iva,
          'nc_subtotal_cero' => $row->nc_subtotal_cero,
          'nc_subtotal_no_objeto' => $row->nc_subtotal_no_objeto,
          'nc_subtotal_excento' => $row->nc_subtotal_excento,
          'nc_valor_ice' => $row->nc_valor_ice,
          'nc_valor_irbpnr' => $row->nc_valor_irbpnr,
          'nc_total' => $row->nc_total,
          'nc_motivo' => $row->nc_motivo,
          'nc_moneda' => $row->nc_moneda,
          'cl_direccion' => $row->cl_direccion,
          'cl_email' => $row->cl_email,
          'cl_telefono' => $row->cl_telefono,
          'usuario' => $row->usuario,
          'usu_cedula' => $row->usu_cedula,
          'usu_telefono' => $row->usu_telefono,
          'usu_email' => $row->usu_email,
          'usu_placa' => $row->usu_placa,
          'usu_tipo_documento' => $row->usu_tipo_documento,
          'est_padronrimpe' => $row->est_padronrimpe,
          'est_leyenda_rimpe' => $row->est_leyenda_rimpe,
          'nc_correo_enviado' => $row->nc_correo_enviado,
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

function seleccionarPorcentajeIce($nc_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR PORCENTAJE ICE DE NOTA CREDITO');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Nota Credito: ' . $nc_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT  '3' as impuesto,
                    dnc_porcentaje_ice,
                  sum(dnc_base_ice) as sum_base_ice,
                  sum(dnc_valor_ice) as sum_valor_ice
                FROM del_detalle_nota_credito 
                WHERE dnc_porcentaje_ice<>'0' 
                and dnc_nota_credito=" . $nc_numero . "
                group by dnc_porcentaje_ice";
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result, 0);
        $w_respuesta = array(
          'impuesto' => $row->impuesto,
          'porcentaje_ice' => $row->dnc_porcentaje_ice,
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

function listaIvasNotaCredito($nc_numero){
  try {
      $Log = new IgtechLog();
      $Log->Abrir();
      $Log->EscribirLog(' LISTA ICE FACTURA ');
      $Log->EscribirLog(' DATOS DE ENTRADA');
      $Log->EscribirLog(' Factura :' . $nc_numero);
      $ws_conexion = ws_coneccion_bdd();
      $select_sql = "SELECT  '2' as codigo,
                             dnc_porcentaje_iva,
												ROUND(sum(dnc_cantidad*(dnc_precio_unitario-dnc_descuento)+dnc_valor_ice),2) as baseimponible,
												round(sum(dnc_base_iva) *iva_porcentaje/100,2) as valor
										FROM  del_detalle_nota_credito 
										inner join sri_tarifa_iva on iva_codigo=dnc_porcentaje_iva
										WHERE dnc_nota_credito=".$nc_numero."
										group by dnc_porcentaje_iva,iva_porcentaje";
      $Log->EscribirLog(' Consulta:' . $select_sql);
      if ($result = pg_query($ws_conexion, $select_sql)) {
          $w_respuesta = array(); //creamos un array
          while ($row = pg_fetch_array($result)) {
              $w_respuesta[] = array(
                  'codigo' => $row['codigo'],
                  'codigoPorcentaje' => $row['dnc_porcentaje_iva'],
                  'baseImponible' => $row['basemmponible'],
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

function seleccionarPorcentajeIrbpnr($nc_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR PORCENTAJE ICE DE NOTA CREDITO');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Factura: ' . $nc_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT  '5' as impuesto,
                  dnc_porcentaje_irbpnr,
                  sum(dnc_base_irbpnr) as sum_base_irbpnr,
                  sum(dnc_valor_irbpnr) as sum_valor_irbpnr
              FROM del_detalle_nota_credito 
              WHERE dnc_porcentaje_irbpnr<>'0' 
              and dnc_nota_credito=" . $nc_numero . "
              group by dnc_porcentaje_irbpnr";
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result, 0);
        $w_respuesta = array(
          'impuesto' => $row->impuesto,
          'porcentaje_irbpnr' => $row->dnc_porcentaje_irbpnr,
          'sum_base_irbpnr' => $row->sum_base_irbpnr,
          'sum_valor_irbpnr' => $row->sum_valor_irbpnr
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

function SeleccionarDetallesNotaCredito($nc_numero){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' SELECCIONAR PORCENTAJE ICE DE NOTA CREDITO');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Factura: ' . $nc_numero);
    $ws_conexion = ws_coneccion_bdd();
    $select_sql = "SELECT
                dnc_nota_credito,
                dnc_producto,
                pro_codigo_aux,
                pro_descripcion,
                dnc_cantidad,
                dnc_precio_unitario,
                dnc_descuento,
                valor_sin_impuesto,
                pro_iva,
                dnc_porcentaje_iva,
                iva_porcentaje,
                dnc_base_iva,
                dnc_valor_iva,
                pro_ice,
                dnc_porcentaje_ice,
                dnc_base_ice,
                ice_tarifa,
                dnc_valor_ice,
                pro_irbpnr,
                dnc_porcentaje_irbpnr,
                irbpnr_tarifa,
                dnc_base_irbpnr,
                dnc_valor_irbpnr,
                dnc_descripcion 
              FROM
                v_del_detalle_nota_credito_sri
              where dnc_nota_credito=" . $nc_numero . "
              order by dnc_id";
    $Log->EscribirLog(' Consulta: ' . $select_sql);
    if ($result = pg_query($ws_conexion, $select_sql)) {
      $w_respuesta = array(); //creamos un array
      if (pg_num_rows($result) > 0) {
        for ($i = 0; $i < pg_num_rows($result); $i++) {
          $row = pg_fetch_object($result, $i);
          array_push($w_respuesta, array(
            'nota_credito' => $row->dnc_nota_credito,
            'producto' => $row->dnc_producto,
            'pro_codigo_aux' => $row->pro_codigo_aux,
            'pro_descripcion' => $row->pro_descripcion,
            'dnc_cantidad' => $row->dnc_cantidad,
            'dnc_precio_unitario' => $row->dnc_precio_unitario,
            'dnc_descuento' => $row->dnc_descuento,
            'valor_sin_impuesto' => $row->valor_sin_impuesto,
            'pro_iva' => $row->pro_iva,
            'dnc_porcentaje_iva' => $row->dnc_porcentaje_iva,
            'iva_porcentaje' => $row->iva_porcentaje,
            'dnc_base_iva' => $row->dnc_base_iva,
            'dnc_valor_iva' => $row->dnc_valor_iva,
            'pro_ice' => $row->pro_ice,
            'dnc_porcentaje_ice' => $row->dnc_porcentaje_ice,
            'dnc_base_ice' => $row->dnc_base_ice,
            'ice_tarifa' => $row->ice_tarifa,
            'dnc_valor_ice' => $row->dnc_valor_ice,
            'pro_irbpnr' => $row->pro_irbpnr,
            'dnc_porcentaje_irbpnr' => $row->dnc_porcentaje_irbpnr,
            'irbpnr_tarifa' => $row->irbpnr_tarifa,
            'dnc_base_irbpnr' => $row->dnc_base_irbpnr,
            'dnc_valor_irbpnr' => $row->dnc_valor_irbpnr,
            'dnc_descripcion' => $row->dnc_descripcion,
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

function autorizar_nc($nc_numero, $i_autorizar){
  try {
    $Log = new IgtechLog();
    $Log->Abrir();
    $Log->EscribirLog(' Autorizar Nota Credito ');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Nota Credito :' . $nc_numero);
    $w_parametros = buscarParametro('RUTA_FIRMADOR', 'http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl');
    if ($w_parametros['error'] == '0') {
      $ruta_firmador = $w_parametros['datos'];
    }

    $correo_enviado = '';
    $procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
    $configApp = new \configAplicacion();
    $configCorreo = new \configCorreo();
    $notaCredito = new notaCredito();

    $w_parametros = buscarParametro('RUTA_EMPRESA', 'D:/Desarrollos/');
    if ($w_parametros['error'] == '0') {
      $dir = $w_parametros['datos'];
    }

    $w_parametros = buscarParametro('RUTA_IREPORT', 'D:/Desarrollo/IReport');
    if ($w_parametros['error'] == '0') {
      $dir_ireport = $w_parametros['datos'];
    }

    $w_datos_nc = seleccionarEENotaCredito($nc_numero);
    if ($w_datos_nc['error'] == '0') {
      $var_empresa = $w_datos_nc['datos']['empresa'];
      $var_establecimiento = $w_datos_nc['datos']['establecimiento'];
    }
    $w_datos_empresa = seleccionarDatosEmpresaSRI($var_empresa, $var_establecimiento);
    if ($w_datos_empresa['error'] == '0') {
      $configApp->dirAutorizados = $dir . $w_datos_empresa['datos']['ruc'] . "/documentos/";
      $configApp->dirLogo =        $dir . $w_datos_empresa['datos']['ruc'] . "/logo/" . $w_datos_empresa['datos']['logo'];
      $configApp->dirFirma =       $dir . $w_datos_empresa['datos']['ruc'] . "/firma/" . $w_datos_empresa['datos']['firma'];
      $configApp->passFirma =      $w_datos_empresa['datos']['passFirma'];
      $configApp->dirIreport = $dir_ireport;
      $notaCredito->configAplicacion = $configApp;

      $configCorreo->correoAsunto = "Nueva Nota de Credito";
      $configCorreo->correoHost =  $w_datos_empresa['datos']['correoHost'];
      $configCorreo->correoPass =  $w_datos_empresa['datos']['correoPass'];
      $configCorreo->correoPort =  $w_datos_empresa['datos']['correoPort'];
      $configCorreo->correoRemitente =  $w_datos_empresa['datos']['correoRemitente'];
      $configCorreo->sslHabilitado = $w_datos_empresa['datos']['tipoConexion'];
      $configCorreo->rutaLogo = $w_datos_empresa['datos']['rutaLogo'].$w_datos_empresa['datos']['ruc'] .'/'. $w_datos_empresa['datos']['logo'];
      $notaCredito->configCorreo = $configCorreo;

      $notaCredito->ruc = $w_datos_empresa['datos']['ruc'];
      $notaCredito->razonSocial = $w_datos_empresa['datos']['razonSocial'];
      $notaCredito->nombreComercial = $w_datos_empresa['datos']['nombreComercial'];
      $notaCredito->dirMatriz = $w_datos_empresa['datos']['dirMatriz'];
      $notaCredito->obligadoContabilidad = $w_datos_empresa['datos']['obligadoContabilidad'];
      $notaCredito->tipoEmision = $w_datos_empresa['datos']['tipoEmision'];
      if ($w_datos_empresa['datos']['contribuyenteEspecial'] != '') {
        $notaCredito->contribuyenteEspecial = $w_datos_empresa['datos']['contribuyenteEspecial'];
      }
      $notaCredito->padronMicroempresa = $w_datos_empresa['datos']['padronMicroempresa'];
      $notaCredito->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
      if ($w_datos_empresa['datos']['padronAgenteRetencion'] == 'S') {
        $w_parametros = buscarParametro('NUMERORESOAR', '1');
        if ($w_parametros['error'] == 0) {
          $notaCredito->numeroResolucion = $w_parametros['datos'];
        }
      }
      $notaCredito->artesanoCalificado = $w_datos_empresa['datos']['artesanoCalificado'];
    }
    $w_datos_notaCredito = seleccionarDatosNotaCredito($nc_numero);
    if ($w_datos_notaCredito['error'] == '0') {
      $correo_enviado = $w_datos_notaCredito['datos']['nc_correo_enviado'];
      $notaCredito->padronRimpe = $w_datos_notaCredito['datos']['est_padronrimpe'];
      $notaCredito->leyendaRimpe = $w_datos_notaCredito['datos']['est_leyenda_rimpe'];
      $notaCredito->ambiente = $w_datos_notaCredito['datos']['ambiente'];
      $notaCredito->codDoc = $w_datos_notaCredito['datos']['tipo_comprobante'];
      $notaCredito->fechaEmision = $w_datos_notaCredito['datos']['fecha'];
      $notaCredito->dirEstablecimiento = $w_datos_notaCredito['datos']['est_direccion'];
      $notaCredito->establecimiento = $w_datos_notaCredito['datos']['est_codigo'];
      $notaCredito->ptoEmision = $w_datos_notaCredito['datos']['pen_serie'];
      $notaCredito->secuencial = $w_datos_notaCredito['datos']['nc_secuencial'];
      $notaCredito->tipoIdentificacionComprador = $w_datos_notaCredito['datos']['cl_tipo_identificacion'];
      $notaCredito->razonSocialComprador = $w_datos_notaCredito['datos']['cl_nombre'];
      $notaCredito->identificacionComprador = $w_datos_notaCredito['datos']['cl_identificacion'];
      $notaCredito->codDocModificado = $w_datos_notaCredito['datos']['nc_cod_docmod'];
      $notaCredito->numDocModificado = $w_datos_notaCredito['datos']['nc_secuencial_docmod'];
      $notaCredito->fechaEmisionDocSustento = $w_datos_notaCredito['datos']['fecha_docmod'];
      $notaCredito->totalSinImpuestos = $w_datos_notaCredito['datos']['nc_subtotal'];
      $notaCredito->totalDescuento = $w_datos_notaCredito['datos']['nc_total_descuento'];
      $total_Impuestos = array();
      $i = 0;
      //if ($w_datos_notaCredito['datos']['nc_subtotal_iva'] > 0) {
        $w_datos_ivas = listaIvasNotaCredito($nc_numero);
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
      if ($w_datos_notaCredito['datos']['nc_valor_ice'] > 0) {
        $w_porcentajeIce = seleccionarPorcentajeIce($nc_numero);
        if ($w_porcentajeIce['error'] == '0') {
          $w_item_ice = $w_porcentajeIce['datos'];
          for ($j = 0; $j < count($w_item_ice); $j++) {
            $item_ice = $w_item_ice[$j];
            $totalImpuesto = new totalImpuesto();
            $totalImpuesto->codigo = $item_ice['impuesto'];
            $totalImpuesto->codigoPorcentaje =  $item_ice['porcentaje_ice'];
            $totalImpuesto->baseImponible =  $item_ice['sum_base_ice'];
            $totalImpuesto->valor =  $item_ice['sum_valor_ice'];
            $total_Impuestos[$i] = $totalImpuesto;
            $i += 1;
          }
        }
      }

      if ($w_datos_notaCredito['datos']['nc_valor_irbpnr'] > 0) {
        $w_porcentajeIrbpnr = seleccionarPorcentajeIrbpnr($nc_numero);
        if ($w_porcentajeIrbpnr['error'] == '0') {
          $w_item_irbpmr = $w_porcentajeIrbpnr['datos'];
          for ($j = 0; $j < count($w_item_irbpmr); $j++) {
            $item_irbpnr = $w_item_irbpmr[$j];
            $totalImpuesto = new totalImpuesto();
            $totalImpuesto->codigo = $w_item_irbpmr['impuesto'];
            $totalImpuesto->codigoPorcentaje = $w_item_irbpmr['porcentaje_irbpnr'];
            $totalImpuesto->baseImponible =  $w_item_irbpmr['sum_base_irbpnr'];
            $totalImpuesto->valor = $w_item_irbpmr['sum_valor_irbpnr'];
            $total_Impuestos[$i] = $totalImpuesto;
            $i += 1;
          }
        }
      }

      $notaCredito->totalConImpuesto = $total_Impuestos;
      $notaCredito->valorModificacion = $w_datos_notaCredito['datos']['nc_total'];
      $notaCredito->motivo = $w_datos_notaCredito['datos']['nc_motivo'];
      $notaCredito->moneda = $w_datos_notaCredito['datos']['nc_moneda'];

      //aqui van los detalles
      $w_datos_detalles_nc = SeleccionarDetallesNotaCredito($nc_numero);
      if ($w_datos_detalles_nc['error'] <> '0') {
        echo "Error al acceder al detalle de la notaCredito";
      } else {
        $w_detalles_nc = [];
        for ($i = 0; $i < count($w_datos_detalles_nc['datos']); $i++) {
          $w_detalleNC = $w_datos_detalles_nc['datos'][$i];
          $detallenotaCredito = new detalleNotaCredito();
          $detallenotaCredito->codigoInterno = $w_detalleNC['producto'];
          $detallenotaCredito->codigoAdicional = $w_detalleNC['pro_codigo_aux'];
          $detallenotaCredito->descripcion =  $w_detalleNC['pro_descripcion'];
          $detallenotaCredito->cantidad = $w_detalleNC['dnc_cantidad'];
          $detallenotaCredito->precioUnitario = $w_detalleNC['dnc_precio_unitario'];
          $detallenotaCredito->descuento = $w_detalleNC['dnc_descuento'];
          $detallenotaCredito->precioTotalSinImpuesto = $w_detalleNC['valor_sin_impuesto'];
          if ($w_detalleNC['dnc_descripcion'] <> "") {
            $informacion_adicional = array();
            $detalle_adicional = new detalleAdicional();
            $detalle_adicional->nombre = 'Adicional';
            $detalle_adicional->valor = $w_detalleNC['dnc_descripcion'];
            $informacion_adicional[0] = $detalle_adicional;
            $detallenotaCredito->detallesAdicionales = $informacion_adicional;
          }
          $impuestos_det = array();
          $j = 0;
          $impuesto = new impuesto();
          $impuesto->codigo = $w_detalleNC['pro_iva'];
          $impuesto->codigoPorcentaje = $w_detalleNC['dnc_porcentaje_iva'];
          $impuesto->tarifa = $w_detalleNC['iva_porcentaje'];
          $impuesto->baseImponible = $w_detalleNC['dnc_base_iva'];
          $impuesto->valor = $w_detalleNC['dnc_valor_iva'];
          $impuestos_det[$j] = $impuesto;
          $j += 1;
          if ($w_detalleNC['dnc_porcentaje_ice'] <> '0') {
            $impuesto = new impuesto();
            $impuesto->codigo = $w_detalleNC['pro_ice'];
            $impuesto->codigoPorcentaje = $w_detalleNC['dnc_porcentaje_ice'];
            $impuesto->tarifa = $w_detalleNC['ice_tarifa'];
            $impuesto->baseImponible = $w_detalleNC['dnc_base_ice'];
            $impuesto->valor = $w_detalleNC['dnc_valor_ice'];
            $impuestos_det[$j] = $impuesto;
            $j += 1;
          }
          if ($w_detalleNC['dnc_porcentaje_irbpnr'] <> '0') {
            $impuesto = new impuesto();
            $impuesto->codigo = $w_detalleNC['pro_irbpnr'];
            $impuesto->codigoPorcentaje = $w_detalleNC['dnc_porcentaje_irbpnr'];
            $impuesto->tarifa = $w_detalleNC['irbpnr_tarifa']; // 
            $impuesto->baseImponible = $w_detalleNC['dnc_base_irbpnr'];
            $impuesto->valor = $w_detalleNC['dnc_valor_irbpnr'];
            $impuestos_det[$j] = $impuesto;
            $j += 1;
          }
          $detallenotaCredito->impuestos = $impuestos_det;
          array_push($w_detalles_nc, $detallenotaCredito);
        }
        $notaCredito->detalles = $w_detalles_nc;
      }
      $camposAdicionales = array();
      $i = 0;
      if ($w_datos_empresa['datos']['esOpTransporte'] == 'S') {
        if ($w_datos_notaCredito['datos']['pen_serie'] <> '') {
          $campoAdicional = new campoAdicional();
          $campoAdicional->nombre = "Punto Emision";
          $campoAdicional->valor = $w_datos_notaCredito['datos']['pen_serie'];
          $camposAdicionales[$i] = $campoAdicional;
          $i += 1;
        }
        if ($w_datos_notaCredito['datos']['usuario'] <> '') {
          $campoAdicional = new campoAdicional();
          $campoAdicional->nombre = "Razon Social";
          $campoAdicional->valor =  $w_datos_notaCredito['datos']['usuario'];
          $camposAdicionales[$i] = $campoAdicional;
          $i += 1;
        }
        if ($w_datos_notaCredito['datos']['usu_cedula'] <> '') {
          $campoAdicional = new campoAdicional();
          $campoAdicional->nombre = "Ruc";
          $campoAdicional->valor = $w_datos_notaCredito['datos']['usu_cedula'];
          $camposAdicionales[$i] = $campoAdicional;
          $i += 1;
        }
        if ($w_datos_notaCredito['datos']['usu_telefono'] <> '') {
          $campoAdicional = new campoAdicional();
          $campoAdicional->nombre = "Telefono Socio";
          $campoAdicional->valor = $w_datos_notaCredito['datos']['usu_telefono'];
          $camposAdicionales[$i] = $campoAdicional;
          $i += 1;
        }
        if ($w_datos_notaCredito['datos']['usu_placa'] <> '') {
          $campoAdicional = new campoAdicional();
          $campoAdicional->nombre = "Placa";
          $campoAdicional->valor = $w_datos_notaCredito['datos']['usu_placa'];
          $camposAdicionales[$i] = $campoAdicional;
          $i += 1;
        }
        if ($w_datos_notaCredito['datos']['usu_tipo_documento'] <> '') {
          $campoAdicional = new campoAdicional();
          $campoAdicional->nombre = "Contribuyente";
          $campoAdicional->valor = $w_datos_notaCredito['datos']['usu_tipo_documento'];
          $camposAdicionales[$i] = $campoAdicional;
          $i += 1;
        }
      }
      if ($w_datos_empresa['datos']['artesanoCalificado'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "artesanoCalificado";
        $campoAdicional->valor = 'Nro. ' . $w_datos_empresa['datos']['artesanoCalificado'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      if ($w_datos_notaCredito['datos']['cl_telefono'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "Telefono";
        $campoAdicional->valor =  $w_datos_notaCredito['datos']['cl_telefono'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      if ($w_datos_notaCredito['datos']['cl_email'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "Email";
        $campoAdicional->valor = $w_datos_notaCredito['datos']['cl_email'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      if ($w_datos_notaCredito['datos']['cl_direccion'] <> '') {
        $campoAdicional = new campoAdicional();
        $campoAdicional->nombre = "Direccion";
        $campoAdicional->valor =  $w_datos_notaCredito['datos']['cl_direccion'];
        $camposAdicionales[$i] = $campoAdicional;
        $i += 1;
      }
      $notaCredito->infoAdicional = $camposAdicionales;
      $Log->EscribirLog(' Liquidacion: ' . var_export($notaCredito, true));
      $procesarComprobante = new procesarComprobante();
      $procesarComprobante->comprobante = $notaCredito;
      $procesarComprobante->envioSRI = false; //El sistema si es true //1-Crea XML en el directorio de autorizado 
      $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
      $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
      if ($i_autorizar == 'S') {
        if ($res->return->estadoComprobante == "FIRMADO") {
          $procesarComprobante = new procesarComprobante();
          $procesarComprobante->comprobante = $notaCredito;
          $procesarComprobante->envioSRI = true; //El sistema si es false 
          $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
          $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
        } else {
          if ($res->return->estadoComprobante == "PROCESANDOSE") {
            $comprobantePendiente = new \comprobantePendiente();
            $comprobantePendiente->configAplicacion = $configApp;
            $comprobantePendiente->configCorreo = $configCorreo;
            $comprobantePendiente->ambiente = $w_datos_notaCredito['datos']['ambiente'];
            $comprobantePendiente->codDoc = $w_datos_notaCredito['datos']['tipo_comprobante'];
            $comprobantePendiente->establecimiento = $w_datos_notaCredito['datos']['est_codigo'];
            $comprobantePendiente->fechaEmision = $w_datos_notaCredito['datos']['fecha'];
            $comprobantePendiente->ptoEmision = $w_datos_notaCredito['datos']['pen_serie'];
            $comprobantePendiente->ruc = $w_datos_empresa['datos']['ruc'];
            $comprobantePendiente->secuencial = $w_datos_notaCredito['datos']['nc_secuencial'];
            $comprobantePendiente->tipoEmision = $w_datos_empresa['datos']['tipoEmision'];
            $comprobantePendiente->padronMicroempresa = $w_datos_empresa['datos']['padronMicroempresa'];
            $comprobantePendiente->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
            $comprobantePendiente->padronRimpe = $w_datos_notaCredito['datos']['est_padronrimpe'];
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
        $update_sql =  "UPDATE del_nota_credito 
                        SET nc_error_sri='" . $mensaje_final . "',
                            nc_archivo='".crearNombreFicheroSinExtension($notaCredito)."'
                        WHERE nc_numero=" . $nc_numero;
      }

      if ($res->return->estadoComprobante == "FIRMADO") {
        $update_sql =  "UPDATE del_nota_credito 
                        SET nc_estado_sri='" . $res->return->estadoComprobante . "',
                            nc_archivo='".crearNombreFicheroSinExtension($notaCredito)."'
                        WHERE nc_numero=" . $nc_numero;
      }
      if ($res->return->estadoComprobante == 'AUTORIZADO') {
       
        if($correo_enviado=="NO"){
					$var_envio_correo=enviarCorreo($notaCredito,$w_datos_notaCredito['datos']['cl_email']);
                    $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
          if($var_envio_correo){
						$correo_enviado="SI";
					}else{
						$correo_enviado="NO";
					}
				}

        $update_sql = "UPDATE del_nota_credito 
                        SET nc_estado_sri='" . $res->return->estadoComprobante . "',
                            nc_archivo='".crearNombreFicheroSinExtension($notaCredito)."',
                            nc_clave='" . $res->return->claveAcceso . "',   
                            nc_autorizacion='" . $res->return->numeroAutorizacion . "',
                            nc_fecha_autorizacion='" . $res->return->fechaAutorizacion . "',
                            nc_error_sri='',
                            nc_correo_enviado='" . $correo_enviado . "'
                        WHERE nc_numero=" . $nc_numero;
      }
      if ($res->return->estadoComprobante == 'DEVUELTA') {
        if ($res->return->mensajes->mensaje == 'CLAVE ACCESO REGISTRADA') {
          if($correo_enviado=="NO"){
            $var_envio_correo=enviarCorreo($notaCredito,$w_datos_notaCredito['datos']['cl_email']);
                      $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
            if($var_envio_correo){
              $correo_enviado="SI";
            }else{
              $correo_enviado="NO";
            }
          }
          $update_sql = "UPDATE del_nota_credito 
                          SET  nc_estado_sri='AUTORIZADO',
                              nc_archivo='" . crearNombreFicheroSinExtension($notaCredito) . "',
                              nc_clave='" . $res->return->claveAcceso . "',   
                              nc_autorizacion='" . $res->return->claveAcceso . "',
                              nc_correo_enviado='" . $correo_enviado . "'
                            WHERE nc_numero=" . $nc_numero;
        } else {
          $update_sql = "UPDATE del_nota_credito 
                          SET   nc_estado_sri='" . $res->return->estadoComprobante . "',
                                nc_archivo='" . crearNombreFicheroSinExtension($notaCredito) . "',
                                nc_clave='" . $res->return->claveAcceso . "',   
                                nc_error_sri='" . $res->return->mensajes->mensaje . "'
                          WHERE nc_numero=" . $nc_numero;
        }
        $mensaje_final.="-".json_encode($res->return->mensajes); 
      }
      if ($res->return->estadoComprobante == 'NO AUTORIZADO') {
        $update_sql = "UPDATE del_nota_credito 
                        SET   nc_estado_sri='" . $res->return->estadoComprobante . "',
                              nc_archivo='" . crearNombreFicheroSinExtension($notaCredito) . "',
                              nc_clave='" . $res->return->claveAcceso . "',   
                              nc_error_sri='" . $res->return->mensajes->mensaje . "',
                              nc_fecha_autorizacion='" . $res->return->fechaAutorizacion . "'
                        WHERE nc_numero=" . $nc_numero;
        $mensaje_final.="-".json_encode($res->return->mensajes); 
      }
      $ws_conexion=ws_coneccion_bdd();
      if (!$result = pg_query($ws_conexion, $update_sql)){
          $o_respuesta=array('error'=>'9997','mensaje'=>pg_last_error($ws_conexion));
      }else{
          $o_respuesta=array('error'=>'0','mensaje'=>'Ejecución exitosa','datos'=>$mensaje_final);     
      }
      $Log->EscribirLog(' Respuesta: '.var_export($o_respuesta,true));
      return $o_respuesta;
  }
  } catch (Throwable $e) {
    $o_respuesta = array('error' => '9999', 'mensaje' => $e->getMessage());
  }
  return $o_respuesta;
}
