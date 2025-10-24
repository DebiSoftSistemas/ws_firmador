<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');
include_once('src/funciones/igtech.funciones_correo.php');
function seleccionarDatosFactura($fac_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' SELECCIONAR DATOS FACTURA');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Factura: ' . $fac_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT fac_empresa,
                            fac_establecimiento 
                     FROM del_factura 
                     WHERE fac_numero =" . $fac_numero;
        $Log->EscribirLog(' Consulta: ' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            if (pg_num_rows($result) > 0) {
                $row = pg_fetch_object($result, 0);
                $w_respuesta = array(
                    'empresa' => $row->fac_empresa,
                    'establecimiento' => $row->fac_establecimiento
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

function seleccionarDatosFacturaSRI($fac_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' SELECCIONAR DATOS FACTURA');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Factura: ' . $fac_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT
                            fac_numero,
                            fac_ambiente,
                            fac_tipo_comprobante,
                            fecha,
                            est_direccion,
                            est_codigo,
                            pen_serie,
                            fac_secuencial,
                            cl_tipo_identificacion,
                            cl_nombre,
                            cl_identificacion,
                            cl_direccion,
                            cl_telefono,
                            cl_email,
                            fac_subtotal,
                            fac_total_descuento,
                            fac_subtotal_iva,
                            fac_valor_iva,
                            fac_subtotal_cero,
                            fac_subtotal_no_objeto,
                            fac_subtotal_excento,
                            fac_valor_ice,
                            fac_valor_irbpnr,
                            fac_propina,
                            fac_total,
                            fac_guia_remision,
                            fac_comentario,
                            fac_moneda,
                            usuario,
                            usu_cedula,
                            usu_telefono,
                            usu_email,
                            usu_placa,
                            usu_tipo_documento,
                            dia_descripcion,
                            est_padronrimpe,
                            fac_total_subsidio,
                            est_leyenda_rimpe,
                            usu_direccion,
                            fac_correo_enviado
                        FROM
                            v_del_datos_factura_sri
                        WHERE fac_numero=" . $fac_numero;
        $Log->EscribirLog(' Consulta: ' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            if (pg_num_rows($result) > 0) {
                $row = pg_fetch_object($result, 0);
                $w_respuesta = array(
                    'numero' => $row->fac_numero,
                    'ambiente' => $row->fac_ambiente,
                    'codDoc' => $row->fac_tipo_comprobante,
                    'fechaEmision' => $row->fecha,
                    'dirEstablecimiento' => $row->est_direccion,
                    'establecimiento' => $row->est_codigo,
                    'ptoEmision' => $row->pen_serie,
                    'secuencial' => $row->fac_secuencial,
                    'tipoIdentificacionComprador' => $row->cl_tipo_identificacion,
                    'razonSocialComprador' => $row->cl_nombre,
                    'identificacionComprador' => $row->cl_identificacion,
                    'direccionComprador' => $row->cl_direccion,
                    'telefonoComprador' => $row->cl_telefono,
                    'email' => $row->cl_email,
                    'totalSinImpuestos' => $row->fac_subtotal,
                    'totalDescuento' => $row->fac_total_descuento,
                    'baseIva' => $row->fac_subtotal_iva,
                    'valorIva' => $row->fac_valor_iva,
                    'baseCero' => $row->fac_subtotal_cero,
                    'baseNoObjeto' => $row->fac_subtotal_no_objeto,
                    'baseExcento' => $row->fac_subtotal_excento,
                    'valorIce' => $row->fac_valor_ice,
                    'valorIrbpnr' => $row->fac_valor_irbpnr,
                    'propina' => $row->fac_propina,
                    'importeTotal' => $row->fac_total,
                    'guiaRemision' => $row->fac_guia_remision,
                    'comentario' => $row->fac_comentario,
                    'moneda' => $row->fac_moneda,
                    'usuario' => $row->usuario,
                    'cedulaUsuario' => $row->usu_cedula,
                    'telefonoUsuario' => $row->usu_telefono,
                    'emailUsuario' => $row->usu_email,
                    'placaUsuario' => $row->usu_placa,
                    'tipoDocUsuario' => $row->usu_tipo_documento,
                    'diagnostico' => $row->dia_descripcion,
                    'padronrimpe' => $row->est_padronrimpe,
                    'totalSubsidio' => $row->fac_total_subsidio,
                    'leyendaRimpe' => $row->est_leyenda_rimpe,
                    'direccionUsuario' => $row->usu_direccion,
                    'correoEnviado' => $row->fac_correo_enviado,

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

function listaDetallesFacturaSRi($fac_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' LISTA DETALLES FACTURA SRI');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Factura: ' . $fac_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT
                            df_factura,
                            df_producto,
                            pro_codigo_aux,
                            pro_descripcion,
                            df_cantidad,
                            df_precio_unitario,
                            df_descuento,
                            valor_sin_impuesto,
                            pro_iva,
                            df_porcentaje_iva,
                            iva_porcentaje,
                            df_base_iva,
                            df_valor_iva,
                            pro_ice,
                            ice_codigo,
                            round(df_subtotal,2) as df_subtotal,
                            ice_tarifa,
                            df_valor_ice,
                            pro_irbpnr,
                            df_porcentaje_irbpnr,
                            irbpnr_tarifa,
                            round(df_base_irbpnr*df_cantidad,2) as df_base_irbpnr,
                            df_valor_irbpnr,
                            df_descripcion,
                            df_precio_sin_subsidio
                        FROM
                            v_del_detalle_factura_sri
                        where df_factura=" . $fac_numero . "
                        order by df_id";
        $Log->EscribirLog(' Consulta: ' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            while ($row = pg_fetch_array($result)) {
                $w_respuesta[] = array(
                    'factura'               => $row['df_factura'],
                    'codigoPrincipal'       => $row['df_producto'],
                    'codigoAuxiliar'        => $row['pro_codigo_aux'],
                    'descripcion'           => $row['pro_descripcion'],
                    'cantidad'              => $row['df_cantidad'],
                    'precioUnitario'        => $row['df_precio_unitario'],
                    'descuento'             => $row['df_descuento'],
                    'precioTotalSinImpuesto' => $row['valor_sin_impuesto'],
                    'codigoImpuestoIva'     => $row['pro_iva'],
                    'codigoIva'             => $row['df_porcentaje_iva'],
                    'porcentajeIva'         => $row['iva_porcentaje'],
                    'baseIva'               => $row['df_base_iva'],
                    'valoriva'              => $row['df_valor_iva'],
                    'codigoImpuestoIce'     => $row['pro_ice'],
                    'codigoIce'             => $row['ice_codigo'],
                    'subtotal'               => $row['df_subtotal'],
                    'porcentajeIce'         => $row['ice_tarifa'],
                    'valorIce'              => $row['df_valor_ice'],
                    'codigoImpuestoIrbpnr'  => $row['pro_irbpnr'],
                    'codigoIrbpnr'          => $row['df_porcentaje_irbpnr'],
                    'porcentajeIrbpnr'      => $row['irbpnr_tarifa'],
                    'baseIrbpnr'            => $row['df_base_irbpnr'],
                    'valorIrbpnr'           => $row['df_valor_irbpnr'],
                    'adicional'             => $row['df_descripcion'],
                    'precioSinSubsidio'     => $row['df_precio_sin_subsidio'],
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

function listaIceFactura($fac_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' LISTA ICE FACTURA ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Factura :' . $fac_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT  '3' as codigo,
                             ice_codigo,
                        sum(df_subtotal) as baseimponible,
                        sum(df_valor_ice) as valor
                    FROM del_detalle_factura 
                    join sri_tarifa_ice on df_porcentaje_ice::integer=ice_id
                    WHERE df_porcentaje_ice<>'0' 
                    and df_factura=" . $fac_numero . "
                    group by df_porcentaje_ice,ice_codigo";
        $Log->EscribirLog(' Consulta:' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            while ($row = pg_fetch_array($result)) {
                $w_respuesta[] = array(
                    'codigo' => $row['codigo'],
                    'codigoPorcentaje' => $row['ice_codigo'],
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

function listaIvasFactura($fac_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' LISTA IVAS FACTURA ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Factura :' . $fac_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT  '2' as codigo,
                                df_porcentaje_iva,
                                ROUND(sum(df_cantidad*(df_precio_unitario-df_descuento)+df_valor_ice),2) as baseimponible,
                                round(sum(df_base_iva) *iva_porcentaje/100,2) as valor
                        FROM  del_detalle_factura 
                        inner join sri_tarifa_iva on iva_codigo=df_porcentaje_iva
                        WHERE df_factura=" . $fac_numero . "
                        group by df_porcentaje_iva,iva_porcentaje";
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

function listaIrbpnrFactura($fac_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' LISTA IRBPNR FACTURA ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Factura: ' . $fac_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql = "SELECT  '5' as codigo,
                            df_porcentaje_irbpnr,
                            sum(df_base_irbpnr*df_cantidad) as baseimponible,
                            sum(df_valor_irbpnr) as valor 
                    FROM del_detalle_factura 
                    WHERE df_porcentaje_irbpnr<>'0' 
                    and df_factura=" . $fac_numero . "
                    group by df_porcentaje_irbpnr";
        $Log->EscribirLog(' Consulta: ' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            while ($row = pg_fetch_array($result)) {
                $w_respuesta[] = array(
                    'codigo' => $row['codigo'],
                    'codigoPorcentaje' => $row['df_porcentaje_irbpnr'],
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

function listaPagosFactura($fac_numero){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' Lista Pagos Factura ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Factura:' . $fac_numero);
        $ws_conexion = ws_coneccion_bdd();
        $select_sql =    "SELECT  a.fp_id,
                                sri_forma_pago.fp_codigo as formapago,
                                a.fp_valor as valor,
                                coalesce(a.fp_plazo,0) as plazo,
                                coalesce(a.fp_unidad_tiempo,'DIAS') as unidadtiempo
                        FROM del_forma_pago_factura a 
                        inner join del_forma_pago b on a.fp_forma_pago=b.fp_id	
                        inner join sri_forma_pago on b.fp_sri=sri_forma_pago.fp_codigo
                        where a.fp_factura=" . $fac_numero;
        $Log->EscribirLog(' Consulta:' . $select_sql);
        if ($result = pg_query($ws_conexion, $select_sql)) {
            $w_respuesta = array(); //creamos un array
            while ($row = pg_fetch_array($result)) {
                $w_respuesta[] = array(
                    'formaPago' => $row['formapago'],
                    'total' => $row['valor'],
                    'plazo' => $row['plazo'],
                    'unidadTiempo' => $row['unidadtiempo'],
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

function autorizar_factura($fac_numero, $i_autorizar){
    try {
        $Log = new IgtechLog();
        $Log->Abrir();
        $Log->EscribirLog(' Autorizar Factura ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Factura :' . $fac_numero);
        $w_parametros = buscarParametro('RUTA_FIRMADOR', 'http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl');
        //var_dump( $w_parametros);
        if ($w_parametros['error'] == '0') {
            $ruta_firmador = $w_parametros['datos'];
        }
        $procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
        $configApp = new \configAplicacion();
        $configCorreo = new \configCorreo();
        $factura = new factura();

        $w_parametros = buscarParametro('RUTA_EMPRESA', 'D:/Desarrollos/');
        //var_dump($w_parametros);
        if ($w_parametros['error'] == '0') {
            $dir = $w_parametros['datos'];
        }

        $w_parametros = buscarParametro('RUTA_IREPORT', 'D:/Desarrollo/IReport');
        //var_dump($w_parametros);
        if ($w_parametros['error'] == '0') {
            $dir_ireport = $w_parametros['datos'];
        }

        $w_datos_factura = seleccionarDatosFactura($fac_numero);
        if ($w_datos_factura['error'] == '0') {
            $var_empresa = $w_datos_factura['datos']['empresa'];
            $var_establecimiento = $w_datos_factura['datos']['establecimiento'];
        }
        //var_dump($w_datos_factura);
        $w_datos_empresa = seleccionarDatosEmpresaSRI($var_empresa, $var_establecimiento);
        //var_dump($w_datos_empresa);
        if ($w_datos_empresa['error'] == '0') {
            $configApp->dirAutorizados = $dir . $w_datos_empresa['datos']['ruc'] . "/documentos/";
            $configApp->dirLogo =        $dir . $w_datos_empresa['datos']['ruc'] . "/logo/" . $w_datos_empresa['datos']['logo'];
            $configApp->dirFirma =       $dir . $w_datos_empresa['datos']['ruc'] . "/firma/" . $w_datos_empresa['datos']['firma'];
            $configApp->passFirma =      $w_datos_empresa['datos']['passFirma'];
            $configApp->dirIreport =      $dir_ireport;
            $factura->configAplicacion = $configApp;

            $configCorreo->correoAsunto = "Nueva Factura";
            $configCorreo->correoHost =      $w_datos_empresa['datos']['correoHost'];
            $configCorreo->correoPass =      $w_datos_empresa['datos']['correoPass'];
            $configCorreo->correoPort =      $w_datos_empresa['datos']['correoPort'];
            $configCorreo->correoRemitente = $w_datos_empresa['datos']['correoRemitente'];
            $configCorreo->sslHabilitado =   $w_datos_empresa['datos']['tipoConexion'];
            $configCorreo->rutaLogo =        $w_datos_empresa['datos']['rutaLogo'].$w_datos_empresa['datos']['ruc'] .'/'. $w_datos_empresa['datos']['logo'];
            $factura->configCorreo =         $configCorreo;

            $factura->ruc                       = $w_datos_empresa['datos']['ruc'];
            $factura->razonSocial               = $w_datos_empresa['datos']['razonSocial'];
            $factura->nombreComercial           = $w_datos_empresa['datos']['nombreComercial'];
            $factura->dirMatriz                 = $w_datos_empresa['datos']['dirMatriz'];
            $factura->obligadoContabilidad      = $w_datos_empresa['datos']['obligadoContabilidad'];
            $factura->tipoEmision               = $w_datos_empresa['datos']['tipoEmision'];
            if ($w_datos_empresa['datos']['contribuyenteEspecial'] != '') {
                $factura->contribuyenteEspecial = $w_datos_empresa['datos']['contribuyenteEspecial'];
            }
            $factura->padronMicroempresa    = $w_datos_empresa['datos']['padronMicroempresa'];
            $factura->padronAgenteRetencion = $w_datos_empresa['datos']['padronAgenteRetencion'];
            if ($w_datos_empresa['datos']['padronAgenteRetencion'] == 'S') {
                $w_parametros = buscarParametro('NUMERORESOAR', '1');
                if ($w_parametros['error'] == '0') {
                    $factura->numeroResolucion = $w_parametros['datos'];
                }
            }
            $factura->artesanoCalificado    = $w_datos_empresa['datos']['artesanoCalificado'];
            //var_dump($factura);
        }


        $w_datosFacturaSRI = seleccionarDatosFacturaSRI($fac_numero);
        //var_dump($w_datosFacturaSRI);
        if ($w_datosFacturaSRI['error'] == '0') {
            $correo_enviado =                        $w_datosFacturaSRI['datos']['correoEnviado'];
            $factura->padronRimpe =                  $w_datosFacturaSRI['datos']['padronrimpe'];
            $factura->leyendaRimpe =                 $w_datosFacturaSRI['datos']['leyendaRimpe'];
            $factura->ambiente =                    $w_datosFacturaSRI['datos']['ambiente'];
            $factura->codDoc =                      $w_datosFacturaSRI['datos']['codDoc'];
            $factura->fechaEmision =                $w_datosFacturaSRI['datos']['fechaEmision'];
            $factura->dirEstablecimiento =          $w_datosFacturaSRI['datos']['dirEstablecimiento'];
            $factura->establecimiento =             $w_datosFacturaSRI['datos']['establecimiento'];
            $factura->ptoEmision =                  $w_datosFacturaSRI['datos']['ptoEmision'];
            $factura->secuencial =                  $w_datosFacturaSRI['datos']['secuencial'];
            $factura->tipoIdentificacionComprador = $w_datosFacturaSRI['datos']['tipoIdentificacionComprador'];
            if ($w_datosFacturaSRI['datos']['guiaRemision'] <> '') {
                $factura->guiaRemision = $w_datosFacturaSRI['datos']['guiaRemision'];
            }
            $factura->razonSocialComprador =    $w_datosFacturaSRI['datos']['razonSocialComprador'];
            $factura->identificacionComprador = $w_datosFacturaSRI['datos']['identificacionComprador'];
            $factura->direccionComprador =       $w_datosFacturaSRI['datos']['direccionComprador'];
            $factura->totalSinImpuestos =       $w_datosFacturaSRI['datos']['totalSinImpuestos'];
            if ($w_datosFacturaSRI['datos']['totalSubsidio'] > 0) {
                $factura->totalSubsidio = $w_datosFacturaSRI['datos']['totalSubsidio'];
            }
            $factura->totalDescuento =          $w_datosFacturaSRI['datos']['totalDescuento'];
            $total_Impuestos = array();
            $i = 0;
            //var_dump($factura);
            //if ($w_datosFacturaSRI['datos']['baseIva'] > 0) {
                $w_datos_ivas = listaIvasFactura($fac_numero);
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
            if ($w_datosFacturaSRI['datos']['valorIce'] > 0) {
                $w_datos_ice = listaIceFactura($fac_numero);
                if ($w_datos_ice['error'] == '0') {
                    $w_item_ice = $w_datos_ice['datos'];
                    for ($j = 0; $j < count($w_item_ice); $j++) {
                        $item_ice = $w_item_ice[$j];
                        $totalImpuesto = new totalImpuesto();
                        $totalImpuesto->codigo =            $item_ice['codigo'];
                        $totalImpuesto->codigoPorcentaje =  $item_ice['codigoPorcentaje'];
                        $totalImpuesto->baseImponible =     $item_ice['baseImponible'];
                        $totalImpuesto->valor =             $item_ice['valor'];
                        $total_Impuestos[$i] = $totalImpuesto;
                        $i += 1;
                    }
                }
            }
            if ($w_datosFacturaSRI['datos']['valorIrbpnr'] > 0) {
                $w_datos_irbpnr = listaIrbpnrFactura($fac_numero);
                if ($w_datos_irbpnr['error'] == '0') {
                    $w_item_irbpnr = $w_datos_irbpnr['datos'];
                    for ($j = 0; $j < count($w_item_irbpnr); $j++) {
                        $item_irbpnr = $w_item_irbpnr[$j];
                        $totalImpuesto = new totalImpuesto();
                        $totalImpuesto->codigo =            $item_irbpnr['codigo'];
                        $totalImpuesto->codigoPorcentaje =  $item_irbpnr['codigo`Porcentaje'];
                        $totalImpuesto->baseImponible =     $item_irbpnr['baseImponible'];
                        $totalImpuesto->valor =             $item_irbpnr['valor'];
                        $total_Impuestos[$i] = $totalImpuesto;
                        $i += 1;
                    }
                }
            }
            $factura->totalConImpuesto = $total_Impuestos;
            $factura->propina =         $w_datosFacturaSRI['datos']['propina'];
            $factura->importeTotal =    $w_datosFacturaSRI['datos']['importeTotal'];
            $factura->moneda =          $w_datosFacturaSRI['datos']['moneda'];
            //aqui van los detalles
            $w_detalleFacturaSRI = listaDetallesFacturaSRi($fac_numero);
            //var_dump($w_detalleFacturaSRI);
            if ($w_detalleFacturaSRI['error'] == '0') {
                $detalles_factura = array();
                $w_productos = $w_detalleFacturaSRI['datos'];
                $w_cantidad_items = count($w_productos);
                for ($i = 0; $i < $w_cantidad_items; $i++) {
                    $item = $w_productos[$i];
                    $detalleFactura = new detalleFactura();
                    $detalleFactura->codigoPrincipal =          $item['codigoPrincipal'];
                    $detalleFactura->codigoAuxiliar =           $item['codigoAuxiliar'];
                    $detalleFactura->descripcion =              $item['descripcion'];
                    $detalleFactura->cantidad =                 $item['cantidad'];
                    $detalleFactura->precioUnitario =           $item['precioUnitario'];
                    $detalleFactura->descuento =                $item['descuento'];
                    $detalleFactura->precioTotalSinImpuesto =   $item['precioTotalSinImpuesto'];
                    $detalleFactura->precioSinSubsidio      =   $item['precioSinSubsidio'];
                    if ($item['adicional'] <> "") {
                        $informacion_adicional = array();
                        $detalle_adicional = new detalleAdicional();
                        $detalle_adicional->nombre =        'Adicional';
                        $detalle_adicional->valor =         $item['adicional'];
                        $informacion_adicional[0] =          $detalle_adicional;
                        $detalleFactura->detalleAdicional = $informacion_adicional;
                    }
                    $impuestos_det = array();
                    $j = 0;
                    $impuesto = new impuesto();
                    $impuesto->codigo =             $item['codigoImpuestoIva'];
                    $impuesto->codigoPorcentaje =   $item['codigoIva'];
                    $impuesto->tarifa =             $item['porcentajeIva'];
                    $impuesto->baseImponible =      $item['baseIva'];
                    $impuesto->valor =              $item['valoriva'];
                    $impuestos_det[$j] = $impuesto;
                    $j += 1;
                    if ($item['codigoIce'] <> '0') {
                        $impuesto = new impuesto();
                        $impuesto->codigo =             $item['codigoImpuestoIce'];
                        $impuesto->codigoPorcentaje =   $item['codigoIce'];
                        $impuesto->baseImponible =      $item['baseIce'];
                        //$impuesto->tarifa =             $item['porcentajeIce'];  
                        $impuesto->tarifa = "0.00";
                        $impuesto->valor =              $item['valorIce'];
                        $impuestos_det[$j] = $impuesto;
                        $j += 1;
                    }
                    if ($item['codigoIrbpnr'] <> '0') {
                        $impuesto = new impuesto();
                        $impuesto->codigo =             $item['codigoImpuestoIrbpnr'];
                        $impuesto->codigoPorcentaje =   $item['codigoIrbpnr'];
                        $impuesto->tarifa =             $item['porcentajeIrbpnr'];
                        $impuesto->baseImponible =      $item['baseIrbpnr'];
                        $impuesto->valor =          $item['valorIrbpnr'];
                        $impuestos_det[$j] = $impuesto;
                        $j += 1;
                    }
                    $detalleFactura->impuestos = $impuestos_det;
                    $detalles_factura[$i] = $detalleFactura;
                }
                $factura->detalles = $detalles_factura;
            }
            $pagos = array();
            $w_pagosFactura = listaPagosFactura($fac_numero);
            //var_dump($w_pagosFactura);
            if ($w_pagosFactura['error'] == '0') {
                $w_pagos = $w_pagosFactura['datos'];
                for ($i = 0; $i < count($w_pagos); $i++) {
                    $itemPago = $w_pagos[$i];
                    $pago = new pagos();
                    $pago->formaPago =  $itemPago['formaPago'];
                    $pago->total =      $itemPago['total'];
                    $pago->plazo =      $itemPago['plazo'];
                    $pago->unidadTiempo = $itemPago['unidadTiempo'];
                    $pagos[$i] = $pago;
                }
            }
            $factura->pagos = $pagos;
            $camposAdicionales = array();
            $i = 0;

            if ($w_datos_empresa['datos']['esOpTransporte'] == 'S') {
                if ($w_datosFacturaSRI['datos']['cedulaUsuario'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Ruc";
                    $campoAdicional->valor = $w_datosFacturaSRI['datos']['cedulaUsuario'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
                if ($w_datosFacturaSRI['datos']['usuario'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Razon Social";
                    $campoAdicional->valor = $w_datosFacturaSRI['datos']['usuario'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
                if ($w_datosFacturaSRI['datos']['placaUsuario'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Placa";
                    $campoAdicional->valor = $w_datosFacturaSRI['datos']['placaUsuario'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
                if ($w_datosFacturaSRI['datos']['ptoEmision'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Punto Emision";
                    $campoAdicional->valor = $w_datosFacturaSRI['datos']['ptoEmision'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
                if ($w_datosFacturaSRI['datos']['tipoDocUsuario'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Contribuyente";
                    $campoAdicional->valor = $w_datosFacturaSRI['datos']['tipoDocUsuario'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
                if ($w_datosFacturaSRI['datos']['telefonoUsuario'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Telefono Socio";
                    $campoAdicional->valor = $w_datosFacturaSRI['datos']['telefonoUsuario'];
                    $camposAdicionales[$i] = $campoAdicional;
                    $i += 1;
                }
                if ($w_datosFacturaSRI['datos']['direccionUsuario'] <> '') {
                    $campoAdicional = new campoAdicional();
                    $campoAdicional->nombre = "Direccion Emisor";
                    $campoAdicional->valor = $w_datosFacturaSRI['datos']['direccionUsuario'];
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
            if ($w_datosFacturaSRI['datos']['telefonoComprador'] <> '') {
                $campoAdicional = new campoAdicional();
                $campoAdicional->nombre = "Telefono";
                $campoAdicional->valor = $w_datosFacturaSRI['datos']['telefonoComprador'];
                $camposAdicionales[$i] = $campoAdicional;
                $i += 1;
            }
            if ($w_datosFacturaSRI['datos']['email'] <> '') {
                $campoAdicional = new campoAdicional();
                $campoAdicional->nombre = "Email";
                $campoAdicional->valor = $w_datosFacturaSRI['datos']['email'];
                $camposAdicionales[$i] = $campoAdicional;
                $i += 1;
            }
            if ($w_datosFacturaSRI['datos']['comentario'] <> '') {
                $campoAdicional = new campoAdicional();
                $campoAdicional->nombre = "Comentario";
                $campoAdicional->valor = $w_datosFacturaSRI['datos']['comentario'];
                $camposAdicionales[$i] = $campoAdicional;
                $i += 1;
            }
            if ($w_datosFacturaSRI['datos']['diagnostico'] <> '') {
                $campoAdicional = new campoAdicional();
                $campoAdicional->nombre = "Diagnostico";
                $campoAdicional->valor = $w_datosFacturaSRI['datos']['diagnostico'];
                $camposAdicionales[$i] = $campoAdicional;
                $i += 1;
            }
            $factura->infoAdicional = $camposAdicionales;
            $Log->EscribirLog(' Factura: ' . var_export($factura, true));
            $procesarComprobante = new procesarComprobante();
            $procesarComprobante->comprobante = $factura;
            $procesarComprobante->envioSRI = false; //El sistema si es true //1-Crea XML en el directorio de autorizado 
            $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
            $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
            if ($i_autorizar == 'S') {
                if ($res->return->estadoComprobante == "FIRMADO") {
                    $procesarComprobante = new procesarComprobante();
                    $procesarComprobante->comprobante = $factura;
                    $procesarComprobante->envioSRI = true; //El sistema si es false 
                    $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
                    $Log->EscribirLog(' Respuesta: ' . var_export($res, true));
                    //var_dump($res);
                } else {
                    if ($res->return->estadoComprobante == "PROCESANDOSE") {
                        $comprobantePendiente = new \comprobantePendiente();
                        $comprobantePendiente->configAplicacion = $configApp;
                        $comprobantePendiente->configCorreo = $configCorreo;
                        $comprobantePendiente->ambiente =               $w_datosFacturaSRI['datos']['ambiente'];
                        $comprobantePendiente->codDoc =                 $w_datosFacturaSRI['datos']['codDoc'];
                        $comprobantePendiente->establecimiento =        $w_datosFacturaSRI['datos']['establecimiento'];
                        $comprobantePendiente->fechaEmision =           $w_datosFacturaSRI['datos']['fechaEmision'];
                        $comprobantePendiente->ptoEmision =             $w_datosFacturaSRI['datos']['ptoEmision'];
                        $comprobantePendiente->ruc =                    $w_datos_empresa['datos']['ruc'];
                        $comprobantePendiente->secuencial =             $w_datosFacturaSRI['datos']['secuencial'];
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
                $mensaje_final .= "-" . $res->return->mensajes->mensaje;
                $update_sql =  "UPDATE del_factura 
                                SET  fac_error_sri='" . $mensaje_final . "',
                                     fac_archivo='" . crearNombreFicheroSinExtension($factura) . "'
                                WHERE fac_numero=" . $fac_numero;
            }
            if ($res->return->estadoComprobante == "FIRMADO") {
                $update_sql =  "UPDATE del_factura 
                                SET  fac_estado_sri='" . $res->return->estadoComprobante . "',
                                     fac_archivo='" . crearNombreFicheroSinExtension($factura) . "'
                                WHERE fac_numero=" . $fac_numero;
            }
            if ($res->return->estadoComprobante == 'AUTORIZADO') {
                if($correo_enviado=="NO"){
					$var_envio_correo=enviarCorreo($factura,$w_datosFacturaSRI['datos']['email']);
                    $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
                    if($var_envio_correo){
						$correo_enviado="SI";
					}else{
						$correo_enviado="NO";
					}
				}

                $update_sql = "UPDATE del_factura 
                                SET  fac_estado_sri='" . $res->return->estadoComprobante . "',
                                     fac_archivo='" . crearNombreFicheroSinExtension($factura) . "',
                                    fac_clave='" . $res->return->claveAcceso . "',   
                                    fac_autorizacion='" . $res->return->numeroAutorizacion . "',
                                    fac_fecha_autorizacion='" . $res->return->fechaAutorizacion . "',
                                    fac_error_sri='',
                                    fac_correo_enviado='".$correo_enviado."'
                                WHERE fac_numero=" . $fac_numero;
            }
            if ($res->return->estadoComprobante == 'DEVUELTA') {
                if ($res->return->mensajes->mensaje == 'CLAVE ACCESO REGISTRADA') {
                    if($correo_enviado=="NO"){
                        $var_envio_correo=enviarCorreo($factura,$w_datosFacturaSRI['datos']['email']);
                        $Log->EscribirLog(' Envio de Correo: ' . var_export($var_envio_correo, true));
                        if($var_envio_correo){
                            $correo_enviado="SI";
                        }else{
                            $correo_enviado="NO";
                        }
                    }
                    $update_sql = "UPDATE del_factura 
                                    SET fac_estado_sri='AUTORIZADO',
                                        fac_archivo='" . crearNombreFicheroSinExtension($factura) . "',
                                        fac_clave='" . $res->return->claveAcceso . "',   
                                        fac_autorizacion='" . $res->return->claveAcceso . "',
                                        fac_correo_enviado='".$correo_enviado."'
                                    WHERE fac_numero=" . $fac_numero;
                } else {
                    $update_sql = "UPDATE del_factura 
                                SET fac_estado_sri='" . $res->return->estadoComprobante . "',
                                    fac_archivo='" . crearNombreFicheroSinExtension($factura) . "',
                                    fac_clave='" . $res->return->claveAcceso . "',   
                                    fac_error_sri='" . $res->return->mensajes->mensaje . "'
                                WHERE fac_numero=" . $fac_numero;
                }
                // var_dump($res);
                $mensaje_final .= "-" . json_encode($res->return->mensajes);
            }
            if ($res->return->estadoComprobante == 'NO AUTORIZADO') {
                $update_sql = "UPDATE del_factura 
                                SET fac_estado_sri='" . $res->return->estadoComprobante . "',
                                    fac_archivo='" . crearNombreFicheroSinExtension($factura) . "',
                                    fac_clave='" . $res->return->claveAcceso . "',   
                                    fac_error_sri='" . $res->return->mensajes->mensaje . "',
                                    fac_fecha_autorizacion='" . $res->return->fechaAutorizacion . "'
                                WHERE fac_numero=" . $fac_numero;

                $mensaje_final .= "-" . json_encode($res->return->mensajes);
            }
            // var_dump($mensaje_final);
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
        return $o_respuesta;
    }
}
