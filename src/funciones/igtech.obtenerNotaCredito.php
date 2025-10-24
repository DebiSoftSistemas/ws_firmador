<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');

function obtener_notaCredito($var_autorizacion)
{
  var_dump('nc');
  $w_parametros = buscarParametro('RUTA_FIRMADOR', 'http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl');
  if ($w_parametros['error'] == '0')
    $ruta_firmador = $w_parametros['datos'];
  $procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
  $obtenerComprobante = new obtenerComprobante();
  $obtenerComprobante->claveAcceso = $var_autorizacion;
  $obtenerComprobante->ambiente = substr($var_autorizacion, 23, 1);
  $tipo_comprobante = substr($var_autorizacion, 8, 2);
  $comprobante = array();
  if ($tipo_comprobante == '04') {
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

      $contenido_infoNotaCredito = $xml->infoNotaCredito;
      $infoNotaCredito = array();
      $infoNotaCredito['fechaEmision'] =        (string)$contenido_infoNotaCredito->fechaEmision;
      $infoNotaCredito['dirEstablecimiento'] =      (string)$contenido_infoNotaCredito->dirEstablecimiento;
      $infoNotaCredito['tipoIdentificacionComprador'] = (string)$contenido_infoNotaCredito->tipoIdentificacionComprador;
      $infoNotaCredito['razonSocialComprador'] =    (string)$contenido_infoNotaCredito->razonSocialComprador;
      $infoNotaCredito['identificacionComprador'] =  (string)$contenido_infoNotaCredito->identificacionComprador;
      $infoNotaCredito['direccionComprador'] =      (string)$contenido_infoNotaCredito->direccionComprador;
      $infoNotaCredito['obligadoContabilidad'] =    (string)$contenido_infoNotaCredito->obligadoContabilidad;
      $infoNotaCredito['contribuyenteEspecial'] =    (string)$contenido_infoNotaCredito->contribuyenteEspecial;
      $infoNotaCredito['rise'] =                   (string)$contenido_infoNotaCredito->rise;
      $infoNotaCredito['codDocModificado'] =           (string)$contenido_infoNotaCredito->codDocModificado;
      $infoNotaCredito['numDocModificado'] =           (string)$contenido_infoNotaCredito->numDocModificado;
      $infoNotaCredito['fechaEmisionDocSustento'] =    (string)$contenido_infoNotaCredito->fechaEmisionDocSustento;
      $infoNotaCredito['totalSinImpuestos'] =      (string)$contenido_infoNotaCredito->totalSinImpuestos;
      $infoNotaCredito['valorModificacion'] =      (string)$contenido_infoNotaCredito->valorModificacion;
      $infoNotaCredito['moneda'] =                  (string)$contenido_infoNotaCredito->moneda;
      $impuestos = array();
      foreach ($contenido_infoNotaCredito->totalConImpuestos->totalImpuesto as $item) {
        $impuesto = array();
        $impuesto['codigo'] =            (string)$item->codigo;
        $impuesto['codigoPorcentaje'] =  (string)$item->codigoPorcentaje;
        $impuesto['baseImponible'] =     (string)$item->baseImponible;
        $impuesto['valor'] =             (string)$item->valor;
        $impuestos[] = $impuesto;
        unset($impuesto);
      }
      $infoNotaCredito['totalConImpuestos'] = $impuestos;
      $infoNotaCredito['motivo'] =    (string)$contenido_infoNotaCredito->motivo;
      $comprobante['infoNotaCredito'] = $infoNotaCredito;

      $detalles = array();
      $contenido_detalles = $xml->detalles;
      foreach ($contenido_detalles->detalle as $item) {
        $detalle = array();
        $detalle['codigoInterno'] =        (string)$item->codigoInterno;
        $detalle['codigoAdicional'] =    (string)$item->codigoAdicional;
        $detalle['descripcion'] =      (string)$item->descripcion;
        $detalle['cantidad'] =        (string)$item->cantidad;
        $detalle['precioUnitario'] =      (string)$item->precioUnitario;
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
