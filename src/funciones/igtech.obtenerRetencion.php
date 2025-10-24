<?php
include_once('src/funciones/igtech.procesar_documentos.php');
include_once('src/funciones/igtech.funciones_generales.php');

function obtener_retencion($var_autorizacion)
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
  if ($tipo_comprobante == '07') {
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

      $contenido_infoCompRetencion = $xml->infoCompRetencion;
      $infoCompRetencion = array();
      $infoCompRetencion['fechaEmision'] =        (string)$contenido_infoCompRetencion->fechaEmision;
      $infoCompRetencion['dirEstablecimiento'] =      (string)$contenido_infoCompRetencion->dirEstablecimiento;
      $infoCompRetencion['obligadoContabilidad'] =    (string)$contenido_infoCompRetencion->obligadoContabilidad;
      $infoCompRetencion['tipoIdentificacionSujetoRetenido'] = (string)$contenido_infoCompRetencion->tipoIdentificacionSujetoRetenido;
      $infoCompRetencion['razonSocialSujetoRetenido'] =    (string)$contenido_infoCompRetencion->razonSocialSujetoRetenido;
      $infoCompRetencion['identificacionSujetoRetenido'] =  (string)$contenido_infoCompRetencion->identificacionSujetoRetenido;
      $infoCompRetencion['periodoFiscal'] =      (string)$contenido_infoCompRetencion->periodoFiscal;
      $comprobante['infoCompRetencion'] = $infoCompRetencion;

      $impuestos = array();
      foreach ($xml->impuestos->impuesto as $item) {
        $impuesto = array();
        $impuesto['codigo'] =                    (string)$item->codigo;
        $impuesto['codigoRetencion'] =           (string)$item->codigoRetencion;
        $impuesto['baseImponible'] =             (string)$item->baseImponible;
        $impuesto['porcentajeRetener'] =         (string)$item->porcentajeRetener;
        $impuesto['valorRetenido'] =             (string)$item->valorRetenido;
        $impuesto['codDocSustento'] =            (string)$item->codDocSustento;
        $impuesto['numDocSustento'] =            (string)$item->numDocSustento;
        $impuesto['fechaEmisionDocSustento'] =   (string)$item->fechaEmisionDocSustento;
        $impuestos[] = $impuesto;
        unset($impuesto);
      }
      $comprobante['impuestos'] = $impuestos;

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
