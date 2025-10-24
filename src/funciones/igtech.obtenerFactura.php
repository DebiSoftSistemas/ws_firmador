<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');

function obtener_factura($var_autorizacion)
{
  $w_parametros = buscarParametro('RUTA_FIRMADOR', 'http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl');
  if ($w_parametros['error'] == '0')
    $ruta_firmador = $w_parametros['datos'];
  $procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
  $obtenerComprobante = new obtenerComprobante();
  $obtenerComprobante->claveAcceso = $var_autorizacion;
  $obtenerComprobante->ambiente = substr($var_autorizacion, 23, 1);
  $tipo_comprobante = substr($var_autorizacion, 8, 2);
  $comprobante = array();
  if ($tipo_comprobante == '01') {
    $res = $procesarComprobanteElectronico->obtenerComprobante($obtenerComprobante);
    if ($res->return->estadoComprobante == 'AUTORIZADO') {
      $xml = new SimpleXMLElement($res->return->comprobante);

      $contenido_infoTributaria = $xml->infoTributaria;
      $infotributaria = array();
      $infotributaria['ambiente'] =    (string)$contenido_infoTributaria->ambiente;
      $infotributaria['tipoEmision'] =    (string)$contenido_infoTributaria->tipoEmision;
      $infotributaria['razonSocial'] =    (string)$contenido_infoTributaria->razonSocial;
      $infotributaria['nombreComercial'] =  (string)$contenido_infoTributaria->nombreComercial;
      $infotributaria['ruc'] =        (string)$contenido_infoTributaria->ruc;
      $infotributaria['claveAcceso'] =    (string)$contenido_infoTributaria->claveAcceso;
      $infotributaria['codDoc'] =      (string)$contenido_infoTributaria->codDoc;
      $infotributaria['estab'] =      (string)$contenido_infoTributaria->estab;
      $infotributaria['ptoEmi'] =      (string)$contenido_infoTributaria->ptoEmi;
      $infotributaria['secuencial'] =    (string)$contenido_infoTributaria->secuencial;
      $infotributaria['dirMatriz'] =    (string)$contenido_infoTributaria->dirMatriz;
      $comprobante['infoTributaria'] = $infotributaria;

      $contenido_infoFactura = $xml->infoFactura;
      $infoFactura = array();
      $infoFactura['fechaEmision'] =        (string)$contenido_infoFactura->fechaEmision;
      $infoFactura['dirEstablecimiento'] =      (string)$contenido_infoFactura->dirEstablecimiento;
      $infoFactura['obligadoContabilidad'] =    (string)$contenido_infoFactura->obligadoContabilidad;
      $infoFactura['tipoIdentificacionComprador'] = (string)$contenido_infoFactura->tipoIdentificacionComprador;
      $infoFactura['razonSocialComprador'] =    (string)$contenido_infoFactura->razonSocialComprador;
      $infoFactura['identificacionComprador'] =  (string)$contenido_infoFactura->identificacionComprador;
      $infoFactura['direccionComprador'] =      (string)$contenido_infoFactura->direccionComprador;
      $infoFactura['totalSinImpuestos'] =      (string)$contenido_infoFactura->totalSinImpuestos;
      $infoFactura['totalDescuento'] =        (string)$contenido_infoFactura->totalDescuento;
      $impuestos = array();
      foreach ($contenido_infoFactura->totalConImpuestos->totalImpuesto as $item) {
        $impuesto = array();
        $impuesto['codigo'] =            (string)$item->codigo;
        $impuesto['codigoPorcentaje'] =  (string)$item->codigoPorcentaje;
        $impuesto['baseImponible'] =     (string)$item->baseImponible;
        $impuesto['valor'] =             (string)$item->valor;
        $impuestos[] = $impuesto;
        unset($impuesto);
      }
      $infoFactura['totalConImpuestos'] = $impuestos;
      $infoFactura['propina'] =    (string)$contenido_infoFactura->propina;
      $infoFactura['importeTotal'] =  (string)$contenido_infoFactura->importeTotal;
      $infoFactura['moneda'] =      (string)$contenido_infoFactura->moneda;
      $pagos = array();
      foreach ($contenido_infoFactura->pagos->pago as $item) {
        $pago = array();
        $pago['formaPago'] =     (string)$item->formaPago;
        $pago['total'] =      (string)$item->total;
        $pago['plazo'] =       (string)$item->plazo;
        $pago['unidadTiempo'] =  (string)$item->unidadTiempo;
        $pagos[] = $pago;
        unset($pago);
      }
      $infoFactura['pagos'] = $pagos;
      $comprobante['infoFactura'] = $infoFactura;
      $detalles = array();
      $contenido_detalles = $xml->detalles;
      foreach ($contenido_detalles->detalle as $item) {
        $detalle = array();
        $detalle['codigoPrincipal'] =    (string)$item->codigoPrincipal;
        $detalle['codigoAuxiliar'] =      (string)$item->codigoAuxiliar;
        $detalle['descripcion'] =      (string)$item->descripcion;
        $detalle['cantidad'] =        (string)$item->cantidad;
        $detalle['precioUnitario'] =      (string)$item->precioUnitario;
        $detalle['precioSinSubsidio'] =    (string)$item->precioSinSubsidio;
        $detalle['descuento'] =        (string)$item->descuento;
        $detalle['precioTotalSinImpuesto'] =  (string)$item->precioTotalSinImpuesto;
        $impuestos = array();
        foreach ($item->impuestos->impuesto as $item_impuesto) {
          $impuesto = array();
          $impuesto['codigo'] =            (string)$item_impuesto->codigo;
          $impuesto['codigoPorcentaje'] =  (string)$item_impuesto->codigoPorcentaje;
          $impuesto['tarifa'] =            (string)$item_impuesto->tarifa;
          $impuesto['baseImponible'] =     (string)$item_impuesto->baseImponible;
          $impuesto['valor'] =             (string)$item_impuesto->valor;
          $impuestos[] = $impuesto;
          unset($impuesto);
        }

        $detalle['impuestos'] = $impuestos;
        $detalles[] = $detalle;
        unset($detalle);
      }
      $comprobante['detalles'] = $detalles;
      $contenido_infoAdicional = $xml->infoAdicional;
      if (isset($contenido_infoAdicional[0])) {
        $infoAdicional = array();
        foreach ($contenido_infoAdicional->campoAdicional as $item) {
          $campoAdicional = array();
          $atributos_campoAdicional = $item->attributes();
          $campoAdicional['campo'] =  (string)$atributos_campoAdicional->nombre;
          $campoAdicional['valor'] =  (string)$item;
          $infoAdicional[] = $campoAdicional;
          unset($campoAdicional);
        }
        $comprobante['infoAdicional'] = $infoAdicional;
      }
    }
  }
  return $comprobante;
}
