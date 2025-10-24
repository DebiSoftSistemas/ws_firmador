<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');
include_once('src/funciones/igtech.funciones_correo.php');

function seleccionarDatosLiquidacion($liq_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' SELECCIONAR DATOS  LIQUIDACION');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Liquidacion: ' . $liq_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT fc_empresa,
                          liq_establecimiento 
                   FROM del_factura_compra 
                   WHERE fc_id =" . $liq_numero;
        $Log->EscribirLog(' Consulta: ' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            if (pg_num_rows($result) > 0) {
                $row = pg_fetch_object($result, 0);
                $w_respuesta = array(
                    'empresa' => $row->fc_empresa,
                    'establecimiento' => $row->liq_establecimiento
                );
                // var_dump($w_respuesta);
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

function seleccionarDatosLiquidacionSRI($liq_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' SELECCIONAR DATOS LIQUIDACION');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Liquidacion: ' . $liq_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT
                            fc_id,
							liq_ambiente,
							fc_tipo_comprobante,
							fecha,
							est_direccion,
							est_codigo,
							pen_serie,
							fc_secuencial,
							pr_tipo_identificacion,
							pr_nombre,
							pr_identificacion,
							pr_direccion,
							pr_telefono,
							pr_email,
							fc_subtotal,
							fc_total_descuento,
							fc_subtotal_iva,
							fc_valor_iva,
							fc_subtotal_cero,
							fc_subtotal_no_objeto,
							fc_subtotal_excento,
							fc_valor_ice,
							fc_valor_irbpnr,
							fc_propina,
							fc_total,
							fc_guia_remision,
							fc_comentario,
							sp_busca_parametro('MONEDASRI'::character varying, 'DOLAR'::character varying) AS fc_moneda,
							fc_total-fc_propina as total_menos_propina,
							est_padronrimpe,
							est_leyenda_rimpe,
							fc_correo_enviado
                            FROM    v_del_datos_liquidacion_sri
					WHERE fc_id=" . $liq_numero;
        $Log->EscribirLog(' Consulta: ' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            if (pg_num_rows($result) > 0) {
                $row = pg_fetch_object($result, 0);
                $w_respuesta = array(
                    'numero' => $row->fc_id,
                    'ambiente' => $row->liq_ambiente,
                    'comprobante' => $row->fc_tipo_comprobante,
                    'fecha' => $row->fecha,
                    'establecimiento_direccion' => $row->est_direccion,
                    'codigo' => $row->est_codigo,
                    'serie' => $row->pen_serie,
                    'secuencial' => $row->fc_secuencial,
                    'tipo_identificacion' => $row->pr_tipo_identificacion,
                    'proveedor_nombre' => $row->pr_nombre,
                    'proveedor_identificacion' => $row->pr_identificacion,
                    'proveedor_direccion' => $row->pr_direccion,
                    'proveedor_telefono' => $row->pr_telefono,
                    'proveedor_email' => $row->pr_email,
                    'subtotal' => $row->fc_subtotal,
                    'total_descuento' => $row->fc_total_descuento,
                    'subtotal_iva' => $row->fc_subtotal_iva,
                    'valor_iva' => $row->fc_valor_iva,
                    'subtotal_cero' => $row->fc_subtotal_cero,
                    'subtotal_no_objeto' => $row->fc_subtotal_no_objeto,
                    'subtotal_excento' => $row->fc_subtotal_excento,
                    'valor_ice' => $row->fc_valor_ice,
                    'valor_irbpnr' => $row->fc_valor_irbpnr,
                    'propina' => $row->fc_propina,
                    'total' => $row->fc_total,
                    'guia_remision' => $row->fc_guia_remision,
                    'comentario' => $row->fc_comentario,
                    'moneda' => $row->fc_moneda,
                    'total_menos_propina' => $row->total_menos_propina,
                    'padronrimpe' => $row->est_padronrimpe,
                    'leyenda_rimpe' => $row->est_leyenda_rimpe,
                    'correo_enviado' => $row->fc_correo_enviado
                );
                // var_dump($w_respuesta);
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

function seleccionarPorecentajeIce($liq_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' SELECCIONAR DATOS IMPUESTO DE LIQUIDACION');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Liquidacion: ' . $liq_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT  '3' as impuesto,
                        dfc_porcentaje_ice as porcentaje_ice,
                    sum(dfc_base_ice) as suma_base_ice,
                    sum(dfc_valor_ice) as suma_valor_ice 
                FROM del_detalle_factura_compra 
                WHERE dfc_porcentaje_ice<>'0'
                and dfc_factura=" . $liq_numero . "
                group by dfc_porcentaje_ice";
        $Log->EscribirLog(' Consulta: ' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            if (pg_num_rows($result) > 0) {
                $row = pg_fetch_object($result, 0);
                $w_respuesta = array(
                    'impuesto' => $row->impuesto,
                    'porcentaje_ice' => $row->dfc_porcentaje_ice,
                    'suma_base_ice' => $row->suma_base_ice,
                    'suma_valor_ice' => $row->suma_valor_ice
                );
                // var_dump($w_respuesta);
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

function listaIvasLiquidaciones($liq_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' LISTA ICE FACTURA ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Factura :' . $liq_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT  '2' as codigo,
                                dfc_porcentaje_iva,
                                ROUND(sum(dfc_cantidad*(dfc_precio_unitario-dfc_descuento)+dfc_valor_ice),2) as baseimponible,
                                round(sum(dfc_base_iva) *iva_porcentaje/100,2) as valor
                        FROM  del_detalle_factura_compra 
                        inner join sri_tarifa_iva on iva_codigo=dfc_porcentaje_iva
                        WHERE dfc_factura=".$liq_numero."
                        group by dfc_porcentaje_iva,iva_porcentaje";
        $Log->EscribirLog(' Consulta:' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            while ($row = pg_fetch_array($result)) {
                $w_respuesta[] = array(
                    'codigo' => $row['codigo'],
                    'codigoPorcentaje' => $row['df_porcentaje_iva'],
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

function seleccionarDatosLiquidacionDetallesSri($liq_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' SELECCIONAR DATOS DETALLES LIQUIDACION');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Liquidacion: ' . $liq_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT
                    dfc_factura,
                    codigo,
                    codigo_aux,
                    fp_descripcion,
                    dfc_cantidad,
                    dfc_precio_unitario,
                    dfc_descuento,
                    valor_sin_impuesto,
                    fp_iva,
                    dfc_porcentaje_iva,
                    iva_porcentaje,
                    dfc_base_iva,
                    dfc_valor_iva,
                    fp_ice,
                    dfc_porcentaje_ice,
                    dfc_base_ice,
                    ice_tarifa,
                    dfc_valor_ice,
                    fp_irbpnr,
                    dfc_porcentaje_irbpnr,
                    irbpnr_tarifa,
                    dfc_base_irbpnr,
                    dfc_valor_irbpnr,
                    dfc_descripcion 
                FROM
                    v_del_detalle_liquidacion_sri
                where dfc_factura=" . $liq_numero . "
                order by dfc_id";
        $Log->EscribirLog(' Consulta: ' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            if (pg_num_rows($result) > 0) {
                for ($i = 0; $i < pg_num_rows($result); $i++) {
                    $row = pg_fetch_object($result, $i);
                    array_push($w_respuesta, array(
                        'factura' => $row->dfc_factura,
                        'codigo' => $row->codigo,
                        'codigo_aux' => $row->codigo_aux,
                        'fp_descripcion' => $row->fp_descripcion,
                        'dfc_cantidad' => $row->dfc_cantidad,
                        'dfc_precio_unitario' => $row->dfc_precio_unitario,
                        'dfc_descuento' => $row->dfc_descuento,
                        'valor_sin_impuesto' => $row->valor_sin_impuesto,
                        'fp_iva' => $row->fp_iva,
                        'dfc_porcentaje_iva' => $row->dfc_porcentaje_iva,
                        'iva_porcentaje' => $row->iva_porcentaje,
                        'dfc_base_iva' => $row->dfc_base_iva,
                        'dfc_valor_iva' => $row->dfc_valor_iva,
                        'fp_ice' => $row->fp_ice,
                        'dfc_porcentaje_ice' => $row->dfc_porcentaje_ice,
                        'dfc_base_ice' => $row->dfc_base_ice,
                        'ice_tarifa' => $row->ice_tarifa,
                        'dfc_valor_ice' => $row->dfc_valor_ice,
                        'fp_irbpnr' => $row->fp_irbpnr,
                        'dfc_porcentaje_irbpnr' => $row->dfc_porcentaje_irbpnr,
                        'irbpnr_tarifa' => $row->irbpnr_tarifa,
                        'dfc_base_irbpnr' => $row->dfc_base_irbpnr,
                        'dfc_valor_irbpnr' => $row->dfc_valor_irbpnr,
                        'dfc_descripcion' => $row->dfc_descripcion,
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

function seleccionarFormaPagoLiquidacion($liq_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' SELECCIONAR FORMA PAGO LIQUIDACION');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Liquidacion: ' . $liq_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT 	a.fp_id as id,
                    sri_forma_pago.fp_codigo as fp_codigo,
                    a.fp_valor as fp_valor,
                    coalesce(a.fp_plazo,0) as fp_plazo,
                    coalesce(a.fp_unidad_tiempo,'DIAS') as fp_unidad_tiempo
            FROM del_forma_pago_factura_compra a 
            inner join del_forma_pago b on a.fp_forma_pago=b.fp_id	
            inner join sri_forma_pago on b.fp_sri=sri_forma_pago.fp_codigo
            where a.fp_factura=" . $liq_numero;
        $Log->EscribirLog(' Consulta: ' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            if (pg_num_rows($result) > 0) {
                for ($i = 0; $i < pg_num_rows($result); $i++) {
                    $row = pg_fetch_object($result, $i);
                    array_push($w_respuesta, array(
                        'id' => $row->id,
                        'fp_codigo' => $row->fp_codigo,
                        'fp_valor' => $row->fp_valor,
                        'fp_plazo' => $row->fp_plazo,
                        'fp_unidad_tiempo' => $row->fp_unidad_tiempo
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

function seleccionarPorecentajeIrbpnr($liq_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' SELECCIONAR DATOS IRBPNR DE LIQUIDACION');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Liquidacion: ' . $liq_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT  '5' as impuesto,
                dfc_porcentaje_irbpnr,
            sum(dfc_base_irbpnr) as suma_base_irbpnr,
            sum(dfc_valor_irbpnr) as suma_valor_irbpnr
        FROM del_detalle_factura_compra 
        WHERE dfc_porcentaje_irbpnr<>'0'
        and dfc_factura=" . $liq_numero . "
        group by dfc_porcentaje_irbpnr";
        $Log->EscribirLog(' Consulta: ' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            if (pg_num_rows($result) > 0) {
                $row = pg_fetch_object($result, 0);
                $w_respuesta = array(
                    'impuesto' => $row->impuesto,
                    'porcentaje_irbpnr' => $row->dfc_porcentaje_irbpnr,
                    'suma_base_irbpnr' => $row->suma_base_irbpnr,
                    'suma_valor_irbpnr' => $row->suma_valor_irbpnr
                );
                // var_dump($w_respuesta);
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

function autorizar_liquidacion($liq_numero, $i_autorizar){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' Autorizar LIQUIDACION COMPRA ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Liquidacion :' . $liq_numero);
        $w_parametros = buscarParametro('RUTA_FIRMADOR', 'http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl');
        if ($w_parametros['error'] == '0') {
            $ruta_firmador = $w_parametros['datos'];
        }

        $procesarComprobanteElectronico = new ProcesarComprobanteElectronico($ruta_firmador);
        $configApp = new \configAplicacion();
        $configCorreo = new \configCorreo();
        $liquidacion = new \liquidacionCompra();

        $w_parametros = buscarParametro('RUTA_EMPRESA', 'D:/Desarrollo/');
        

        if ($w_parametros['error'] == '0') {
            $dir = $w_parametros['datos'];
        }

        $w_parametros = buscarParametro('RUTA_IREPORT', 'D:/Desarrollo/IReport');
       

        if ($w_parametros['error'] == '0') {
            $dir_ireport = $w_parametros['datos'];
        }

        $w_datos_liquidacion = seleccionarDatosLiquidacion($liq_numero);
        

        if ($w_datos_liquidacion['error'] == '0') {
            $var_empresa = $w_datos_liquidacion['datos']['empresa'];
            $var_establecimiento = $w_datos_liquidacion['datos']['establecimiento'];
        }

        $w_datos_empresa = seleccionarDatosEmpresaSRI($var_empresa, $var_establecimiento);
       

        if ($w_datos_empresa['error'] == '0') {
            $configApp->dirAutorizados = $dir . $w_datos_empresa['datos']['ruc'] . "/documentos/";
            $configApp->dirLogo =        $dir . $w_datos_empresa['datos']['ruc'] . "/logo/" . $w_datos_empresa['datos']['logo'];
            $configApp->dirFirma =       $dir . $w_datos_empresa['datos']['ruc'] . "/firma/" . $w_datos_empresa['datos']['firma'];
            $configApp->passFirma =      $w_datos_empresa['datos']['passFirma'];
            $configApp->dirIreport =      $dir_ireport;
            $liquidacion->configAplicacion = $configApp;
           

            $configCorreo->correoAsunto = "Nueva Liquidacion de Compras";
            $configCorreo->correoHost =      $w_datos_empresa['datos']['correoHost'];
            $configCorreo->correoPass =      $w_datos_empresa['datos']['correoPass'];
            $configCorreo->correoPort =      $w_datos_empresa['datos']['correoPort'];
            $configCorreo->correoRemitente = $w_datos_empresa['datos']['correoRemitente'];
            $configCorreo->sslHabilitado =   $w_datos_empresa['datos']['tipoConexion'];
            $configCorreo->rutaLogo =        $w_datos_empresa['datos']['rutaLogo'].$w_datos_empresa['datos']['ruc'] .'/'. $w_datos_empresa['datos']['logo'];
            $liquidacion->configCorreo =     $configCorreo;
            
            $liquidacion->ruc                       = $w_datos_empresa['datos']['ruc'];
            $liquidacion->razonSocial               = $w_datos_empresa['datos']['razonSocial'];
            $liquidacion->nombreComercial           = $w_datos_empresa['datos']['nombreComercial'];
            $liquidacion->dirMatriz                 = $w_datos_empresa['datos']['dirMatriz'];
            $liquidacion->obligadoContabilidad      = $w_datos_empresa['datos']['obligadoContabilidad'];
            $liquidacion->tipoEmision               = $w_datos_empresa['datos']['tipoEmision'];
            if ($w_datos_empresa['datos']['contribuyenteEspecial'] != '') {
                $liquidacion->contribuyenteEspecial = $w_datos_empresa['datos']['contribuyenteEspecial'] ?? '';
            }
            $liquidacion->padronMicroempresa    = $w_datos_empresa['datos']['padronMicroempresa'];
            $liquidacion->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
            if ($w_datos_empresa['datos']['padronAgenteRetencion'] == 'S') {
                $w_parametros = buscarParametro('NUMERORESOAR', '1');
                if ($w_parametros['error'] == '0') {
                    $liquidacion->numeroResolucion = $w_parametros['datos'] ?? '';
                }
            }
            $liquidacion->artesanoCalificado    = $w_datos_empresa['datos']['artesanoCalificado'];

            $w_datosLiquidacionSRI = seleccionarDatosLiquidacionSRI($liq_numero);
           
            if ($w_datosLiquidacionSRI['error'] == '0') {
                $correo_enviado =                            $w_datosLiquidacionSRI['datos']['correo_enviado'];
                $liquidacion->padronRimpe =                  $w_datosLiquidacionSRI['datos']['padronrimpe'];
                $liquidacion->leyendaRimpe =                 $w_datosLiquidacionSRI['datos']['leyenda_rimpe'];
                $liquidacion->ambiente =                     $w_datosLiquidacionSRI['datos']['ambiente'];
                $liquidacion->codDoc =                       $w_datosLiquidacionSRI['datos']['comprobante'];
                $liquidacion->fechaEmision =                 $w_datosLiquidacionSRI['datos']['fecha'];
                $liquidacion->dirEstablecimiento =           $w_datosLiquidacionSRI['datos']['establecimiento_direccion'];
                $liquidacion->establecimiento =              $w_datosLiquidacionSRI['datos']['codigo'];
                $liquidacion->ptoEmision =                   $w_datosLiquidacionSRI['datos']['serie'];
                $liquidacion->secuencial =                   $w_datosLiquidacionSRI['datos']['secuencial'];
                $liquidacion->tipoIdentificacionProveedor =  $w_datosLiquidacionSRI['datos']['tipo_identificacion'];
                if ($w_datosLiquidacionSRI['datos']['guia_remision'] <> '')
                    $liquidacion->guiaRemision = $w_datosLiquidacionSRI['datos']['guia_remision'];
                $liquidacion->razonSocialProveedor = $w_datosLiquidacionSRI['datos']['proveedor_nombre'];
                $liquidacion->identificacionProveedor = $w_datosLiquidacionSRI['datos']['proveedor_identificacion'];
                $liquidacion->direccionProveedor = $w_datosLiquidacionSRI['datos']['proveedor_direccion'];
                $liquidacion->totalSinImpuestos = $w_datosLiquidacionSRI['datos']['subtotal'];
                $liquidacion->totalDescuento = $w_datosLiquidacionSRI['datos']['total_descuento'];
                $total_Impuestos = array();
                $i = 0;

                //if ($w_datosLiquidacionSRI['datos']['subtotal_iva'] > 0) {
                    $w_datos_ivas = listaIvasLiquidaciones($liq_numero);
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
                if ($w_datosLiquidacionSRI['datos']['valor_ice'] > 0) {
                    $w_datosPorcentajeIce = seleccionarPorecentajeIce($liq_numero);
                    if ($w_datosPorcentajeIce['error'] == '0') {
                        $w_item_ice = $w_datosPorcentajeIce['datos'];
                        for ($j = 0; $j < count($w_item_ice); $j++) {
                            $item_ice = $w_item_ice[$j];
                            $totalImpuesto = new totalImpuesto();
                            $totalImpuesto->codigo = $item_ice['impuesto'];
                            $totalImpuesto->codigoPorcentaje = $item_ice['porcentaje_ice'];
                            $totalImpuesto->baseImponible = $item_ice['suma_base_ice'];
                            $totalImpuesto->valor = $item_ice['suma_valor_ice'];
                            $total_Impuestos[$i] = $totalImpuesto;
                            $i += 1;
                        }
                    }
                }
                if ($w_datosLiquidacionSRI['datos']['valor_irbpnr'] > 0) {
                    $w_datosPorcentajeIrbpnr = seleccionarPorecentajeIrbpnr($liq_numero);
                    if ($w_datosPorcentajeIrbpnr['error'] == '0') {
                        $w_item_irbpnr = $w_datosPorcentajeIrbpnr['datos'];
                        for ($j = 0; $j < count($w_item_irbpnr); $j++) {
                            $item_irbpnr = $w_item_irbpnr[$j];
                            $totalImpuesto = new totalImpuesto();
                            $totalImpuesto->codigo = $item_irbpnr['impuesto'];
                            $totalImpuesto->codigoPorcentaje = $item_irbpnr['porcentaje_irbpnr'];
                            $totalImpuesto->baseImponible = $item_irbpnr['suma_base_irbpnr'];
                            $totalImpuesto->valor = $item_irbpnr['suma_valor_irbpnr'];
                            $total_Impuestos[$i] = $totalImpuesto;
                            $i += 1;
                        }
                    }
                }
                $liquidacion->totalConImpuesto = $total_Impuestos;
               
                $liquidacion->propina = $w_datosLiquidacionSRI['datos']['propina'];
                $liquidacion->importeTotal = $w_datosLiquidacionSRI['datos']['total_menos_propina'];
                $liquidacion->moneda = $w_datosLiquidacionSRI['datos']['moneda'];
                $w_liquidacion_detalles = seleccionarDatosLiquidacionDetallesSri($liq_numero);
                
                if ($w_liquidacion_detalles['error'] <> '0') {
                    echo "Error al acceder al detalle de la factura";
                } else {
                    $detalles_factura = [];
                    for ($i = 0; $i < count($w_liquidacion_detalles['datos']); $i++) {
                        $w_detalleLiq = $w_liquidacion_detalles['datos'][$i];
                        $detalleFactura = new detalleFactura();
                        $detalleFactura->codigoPrincipal = $w_detalleLiq['codigo'];
                        $detalleFactura->codigoAuxiliar = $w_detalleLiq['codigo_aux'];
                        $detalleFactura->descripcion = $w_detalleLiq['fp_descripcion'];
                        $detalleFactura->cantidad = $w_detalleLiq['dfc_cantidad'];
                        $detalleFactura->precioUnitario = $w_detalleLiq['dfc_precio_unitario'];
                        $detalleFactura->descuento = $w_detalleLiq['dfc_descuento'];
                        $detalleFactura->precioTotalSinImpuesto = $w_detalleLiq['valor_sin_impuesto'];
                        if ($w_detalleLiq['dfc_descripcion'] <> "") {
                            $informacion_adicional = array();
                            $detalle_adicional = new detalleAdicional();
                            $detalle_adicional->nombre = 'Adicional';
                            $detalle_adicional->valor = $w_detalleLiq['dfc_descripcion'];
                            $informacion_adicional[0] = $detalle_adicional;
                            $detalleFactura->detalleAdicional = $informacion_adicional;
                        }
                        $impuestos_det = array();
                        $j = 0;
                        $impuesto = new impuesto();
                        $impuesto->codigo =  $w_detalleLiq['fp_iva'];
                        $impuesto->codigoPorcentaje =  $w_detalleLiq['dfc_porcentaje_iva'];
                        $impuesto->tarifa =  $w_detalleLiq['iva_porcentaje'];
                        $impuesto->baseImponible =  $w_detalleLiq['dfc_base_iva'];
                        $impuesto->valor =  $w_detalleLiq['dfc_valor_iva'];
                        $impuestos_det[$j] = $impuesto;
                        $j += 1;
                        if ($w_detalleLiq['dfc_porcentaje_ice'] <> '0') {
                            $impuesto = new impuesto();
                            $impuesto->codigo = $w_detalleLiq['fp_ice'];
                            $impuesto->codigoPorcentaje = $w_detalleLiq['dfc_porcentaje_ice'];
                            $impuesto->tarifa = $w_detalleLiq['ice_tarifa'];
                            $impuesto->baseImponible = $w_detalleLiq['dfc_base_ice'];
                            $impuesto->valor = $w_detalleLiq['dfc_valor_ice'];
                            $impuestos_det[$j] = $impuesto;
                            $j += 1;
                        }
                        if ($w_detalleLiq['dfc_porcentaje_irbpnr'] <> '0') {
                            $impuesto = new impuesto();
                            $impuesto->codigo =  $w_detalleLiq['fp_irbpnr'];
                            $impuesto->codigoPorcentaje =  $w_detalleLiq['dfc_porcentaje_irbpnr'];
                            $impuesto->tarifa =  $w_detalleLiq['irbpnr_tarifa'];
                            $impuesto->baseImponible =  $w_detalleLiq['dfc_base_irbpnr'];
                            $impuesto->valor =  $w_detalleLiq['dfc_valor_irbpnr'];
                            $impuestos_det[$j] = $impuesto;
                            $j += 1;
                        }
                        $detalleFactura->impuestos = $impuestos_det;
                        array_push($detalles_factura, $detalleFactura);
                    }
                    $liquidacion->detalles = $detalles_factura;
                   
                }
                $pagos = array();
                $w_formas_pago = seleccionarFormaPagoLiquidacion($liq_numero);
              
                if ($w_formas_pago['error']  <> '0') {
                    echo "Error al acceder a las formas de pago";
                } else {
                    for ($i = 0; $i < count($w_formas_pago['datos']); $i++) {
                        $w_formaPago = $w_formas_pago['datos'][$i];
                        $pago = new pagos();
                        $pago->formaPago = $w_formaPago['fp_codigo'];
                        $pago->total = $w_formaPago['fp_valor'];
                        $pago->plazo = $w_formaPago['fp_plazo'];
                        $pago->unidadTiempo = $w_formaPago['fp_unidad_tiempo'];
                        array_push($pagos, $pago);
                    }
                }
                $liquidacion->pagos = $pagos;
              
                $camposAdicionales = array();
                $i = 0;
                // var_dump( $liquidacion->pagos);
                if ($w_datos_empresa['datos']['artesanoCalificado'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "artesanoCalificado";
                    $campoAdicional->valor = 'Nro. ' . $w_datos_empresa['datos']['artesanoCalificado'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
                if ($w_datosLiquidacionSRI['datos']['proveedor_telefono'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Telefono";
                    $campoAdicional->valor = $w_datosLiquidacionSRI['datos']['proveedor_telefono'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
                if ($w_datosLiquidacionSRI['datos']['proveedor_email'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Email";
                    $campoAdicional->valor = $w_datosLiquidacionSRI['datos']['proveedor_email'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
                if ($w_datosLiquidacionSRI['datos']['comentario'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Comentario";
                    $campoAdicional->valor = $w_datosLiquidacionSRI['datos']['comentario'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
               
                $liquidacion->infoAdicional = $camposAdicionales;
                $Log->EscribirLog(' Liquidacion: ' . var_export($liquidacion, true));
                $procesarComprobante = new procesarComprobante();
                $procesarComprobante->comprobante = $liquidacion;
                $procesarComprobante->envioSRI = false;
                $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
                $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
                //  CUANDO FUNCIONE EL SRI PROBAR AUTORIZACION
                if ($i_autorizar == 'S') {
                    if ($res->return->estadoComprobante == "FIRMADO") {
                        $procesarComprobante = new procesarComprobante();
                        $procesarComprobante->comprobante = $liquidacion;
                        $procesarComprobante->envioSRI = true;
                        $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
                        $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
                    } else {
                        if ($res->return->estadoComprobante == "PROCESANDOSE") {
                            $comprobantePendiente = new \comprobantePendiente();
                            $comprobantePendiente->configAplicacion = $configApp;
                            $comprobantePendiente->configCorreo = $configCorreo;
                            $comprobantePendiente->ambiente = $w_datosLiquidacionSRI['datos']['ambiente'];
                            $comprobantePendiente->codDoc = $w_datosLiquidacionSRI['datos']['comprobante'];
                            $comprobantePendiente->establecimiento = $w_datosLiquidacionSRI['datos']['codigo'];
                            $comprobantePendiente->fechaEmision = $w_datosLiquidacionSRI['datos']['fecha'];
                            $comprobantePendiente->ptoEmision = $w_datosLiquidacionSRI['datos']['serie'];
                            $comprobantePendiente->ruc = $w_datos_empresa['datos']['ruc'];
                            $comprobantePendiente->secuencial = $w_datos_empresa['datos']['obligadoContabilidad'];
                            $comprobantePendiente->tipoEmision = $w_datos_empresa['datos']['tipoEmision'];
                            $comprobantePendiente->padronMicroempresa = $w_datos_empresa['datos']['padronMicroempresa'];
                            $comprobantePendiente->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
                            $comprobantePendiente->padronRimpe = $w_datosLiquidacionSRI['datos']['padronrimpe'];
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
                    $update_sql =  "UPDATE del_factura_compra 
                                    SET liq_error_sri='" . $mensaje_final . "',
                                        fc_archivo='".crearNombreFicheroSinExtension($liquidacion) ."'
                                    WHERE fc_id=" . $liq_numero;
                }
                if ($res->return->estadoComprobante == "FIRMADO") {
                    $update_sql =  "UPDATE del_factura_compra 
                                    SET liq_estado_sri='" . $res->return->estadoComprobante . "',
                                        fc_archivo='".crearNombreFicheroSinExtension($liquidacion) ."'
                                    WHERE fc_id=" . $liq_numero;
                }
                if ($res->return->estadoComprobante == 'AUTORIZADO') {
                    
                    if($correo_enviado=="NO"){
                        $var_envio_correo=enviarCorreo($liquidacion,$w_datosLiquidacionSRI['datos']['proveedor_email']);
                        $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
                        if($var_envio_correo){
                            $correo_enviado="SI";
                        }else{
                            $correo_enviado="NO";
                        }
                    }
                    $update_sql = "UPDATE del_factura_compra 
                                    SET liq_estado_sri='" . $res->return->estadoComprobante . "',
                                        fc_archivo='".crearNombreFicheroSinExtension($liquidacion) ."',
                                        liq_clave='" . $res->return->claveAcceso . "',   
                                        fc_autorizacion='" . $res->return->numeroAutorizacion . "',
                                        liq_fecha_autorizacion='" . $res->return->fechaAutorizacion . "',
                                        fc_correo_enviado='" . $correo_enviado . "'
                                WHERE fc_id=" . $liq_numero;
                }
                if ($res->return->estadoComprobante == 'DEVUELTA') {
                    if ($res->return->mensajes->mensaje == 'CLAVE ACCESO REGISTRADA') {
                        if($correo_enviado=="NO"){
                            $var_envio_correo=enviarCorreo($liquidacion,$w_datosLiquidacionSRI['datos']['proveedor_email']);
                            $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
                            if($var_envio_correo){
                                $correo_enviado="SI";
                            }else{
                                $correo_enviado="NO";
                            }
                        }

                        $update_sql = "UPDATE del_factura_compra 
                                        SET liq_estado_sri='AUTORIZADO',
                                            fc_archivo='".crearNombreFicheroSinExtension($liquidacion) ."',
                                            liq_clave='" . $res->return->claveAcceso . "',   
                                            fc_autorizacion='" . $res->return->claveAcceso . "',
                                            fc_correo_enviado='" . $correo_enviado . "'
                                        WHERE fc_id=" . $liq_numero;
                        } else {
                        $update_sql = "UPDATE del_factura_compra 
                                        SET  liq_estado_sri='" . $res->return->estadoComprobante . "',
                                            fc_archivo='".crearNombreFicheroSinExtension($liquidacion) ."',
                                            liq_clave='" . $res->return->claveAcceso . "',   
                                            liq_error_sri='" . $res->return->mensajes->mensaje . "'
                                        WHERE fc_id=" . $liq_numero;
                    }
                    $mensaje_final .= "-" . json_encode($res->return->mensajes);
                }
                if ($res->return->estadoComprobante == 'NO AUTORIZADO') {
                    $update_sql = "UPDATE del_factura_compra 
                                    SET  liq_estado_sri='" . $res->return->estadoComprobante . "',
                                        fc_archivo='".crearNombreFicheroSinExtension($liquidacion) ."',
                                        liq_clave='" . $res->return->claveAcceso . "',   
                                        liq_error_sri='" . $res->return->mensajes->mensaje . "',
                                        liq_fecha_autorizacion='" . $res->return->fechaAutorizacion . "'
                                    WHERE fc_id=" . $liq_numero;
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
