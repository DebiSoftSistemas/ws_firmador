<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');

function obtener_notaDebito($var_autorizacion)
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
  if ($tipo_comprobante == '05') {
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

      $contenido_infoNotaDebito = $xml->infoNotaDebito;
      $infoNotaDebito = array();
      $infoNotaDebito['fechaEmision'] =          (string)$contenido_infoNotaDebito->fechaEmision;
      $infoNotaDebito['dirEstablecimiento'] =      (string)$contenido_infoNotaDebito->dirEstablecimiento;
      $infoNotaDebito['tipoIdentificacionComprador'] = (string)$contenido_infoNotaDebito->tipoIdentificacionComprador;
      $infoNotaDebito['razonSocialComprador'] =    (string)$contenido_infoNotaDebito->razonSocialComprador;
      $infoNotaDebito['identificacionComprador'] =      (string)$contenido_infoNotaDebito->identificacionComprador;
      $infoNotaDebito['direccionComprador'] =      (string)$contenido_infoNotaDebito->direccionComprador;
      $infoNotaDebito['contribuyenteEspecial'] =    (string)$contenido_infoNotaDebito->contribuyenteEspecial;
      $infoNotaDebito['obligadoContabilidad'] =    (string)$contenido_infoNotaDebito->obligadoContabilidad;
      $infoNotaDebito['codDocModificado'] =            (string)$contenido_infoNotaDebito->codDocModificado;
      $infoNotaDebito['numDocModificado'] =            (string)$contenido_infoNotaDebito->numDocModificado;
      $infoNotaDebito['fechaEmisionDocSustento'] =     (string)$contenido_infoNotaDebito->fechaEmisionDocSustento;
      $infoNotaDebito['totalSinImpuestos'] =      (string)$contenido_infoNotaDebito->totalSinImpuestos;

      $impuestos = array();
      foreach ($contenido_infoNotaDebito->impuesto as $item) {
        $impuesto = array();
        $impuesto['codigo'] =            (string)$item->codigo;
        $impuesto['codigoPorcentaje'] =  (string)$item->codigoPorcentaje;
        $impuesto['tarifa'] =  (string)$item->tarifa;
        $impuesto['baseImponible'] =     (string)$item->baseImponible;
        $impuesto['valor'] =             (string)$item->valor;
        $impuestos[] = $impuesto;
        unset($impuesto);
      }
      $infoNotaDebito['impuestos'] = $impuestos;
      $infoNotaDebito['valorTotal'] =  (string)$contenido_infoNotaDebito->valorTotal;

      $pagos = array();
      foreach ($contenido_infoNotaDebito->pagos->pago as $item) {
        $pago = array();
        $pago['formaPago'] =     (string)$item->formaPago;
        $pago['total'] =      (string)$item->total;
        $pago['plazo'] =       (string)$item->plazo;
        $pago['unidadTiempo'] =  (string)$item->unidadTiempo;
        $pagos[] = $pago;
        unset($pago);
      }
      $infoNotaDebito['pagos'] = $pagos;
      $comprobante['infoNotaDebito'] = $infoNotaDebito;

      $motivos = array();
      $contenido_motivos = $xml->motivos;
      foreach ($contenido_motivos->motivo as $item) {
        $motivo = array();
        $motivo['razon'] = (string)$item->razon;
        $motivo['valor'] =  (string)$item->valor;
        $motivos[] = $motivo;
        unset($motivo);
      }
      $comprobante['motivos'] = $motivos;

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
