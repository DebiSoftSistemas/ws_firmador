<?php
//__NM____NM__FUNCTION__NM__//
class comprobanteGeneral {
    public $configAplicacion; // configAplicacion
    public $configCorreo; // configCorreo
    public $ambiente; // string
    public $tipoEmision; // string
    public $razonSocial; // string
    public $nombreComercial; // string
    public $ruc; // string
    public $codDoc; // string
    public $claveAcc; // string
    public $establecimiento; // string
    public $ptoEmision; // string
    public $secuencial; // string
    public $dirMatriz; // string
    public $dirEstablecimiento; // string
    public $contribuyenteEspecial; // string
    public $obligadoContabilidad; // string
    public $tipoDoc; // string
    public $fechaEmision; // string
	public $padronMicroempresa;
	public $padronAgenteRetencion;
	public $numeroResolucion;
	public $artesanoCalificado;
	public $padronRimpe;
	public $leyendaRimpe;
}

class comprobanteLote {
    public $configAplicacion; // configAplicacion
    public $configCorreo; // configCorreo
    public $idUnico; // string
    public $claveAcceso; // string
    public $ruc; // string
    public $fechaEmision; // string
    public $ambiente; // string
    public $tipoEmision; // string
    public $secuencial; // string
    public $codDoc; // string
    public $establecimiento; // string
    public $ptoEmision; // string
    public $comprobantes; // comprobanteGeneral
	public $padronMicroempresa;
	public $padronAgenteRetencion;
	public $artesanoCalificado;
	public $padronRimpe;
}

class comprobantePendiente {
    public $configAplicacion; // configAplicacion
    public $configCorreo; // configCorreo
    public $clavAcc;
    public $codDoc; // string
    public $ruc; // string
    public $ambiente; // string
    public $establecimiento; // string
    public $ptoEmision; // string
    public $secuencial; // string
    public $tipoEmision; // string
    public $fechaEmision; // string
    public $enviarEmail;
    public $otrosDestinatarios;
	public $padronMicroempresa;
	public $padronAgenteRetencion;
	public $artesanoCalificado;
	public $padronRimpe;
}

class comprobanteRetencion extends comprobanteGeneral {
    public $tipoIdentificacionSujetoRetenido; // string
	public $razonSocialSujetoRetenido;
	public $identificacionSujetoRetenido; // string
	public $periodoFiscal; // string
	public $impuestos;
	public $infoAdicional;
}

class comprobanteRetencionDos extends comprobanteGeneral {
    public $tipoIdentificacionSujetoRetenido; // string
    public $tipoSujetoRetenido;//string
    public $parteRel;//string
    public $razonSocialSujetoRetenido;//string
    public $identificacionSujetoRetenido; // string
    public $periodoFiscal; // string
    public $docsSustento; // sustentoRetencion
    public $infoAdicional; // campoAdicional
}

class factura extends comprobanteGeneral {
    public $tipoIdentificacionComprador; // string
	public $guiaRemision; // string
	public $razonSocialComprador; // string
	public $identificacionComprador; // string
	public $direccionComprador;//direccion de l comprador
    public $totalSinImpuestos; // string
    public $totalDescuento; // string
    public $propina; // string
    public $totalConImpuesto; // totalImpuesto
	public $totalSubsidio;
    public $importeTotal; // string
	public $moneda; // string
    public $detalles; // detalleFactura
    public $infoAdicional; // campoAdicional
	public $pagos;
}

class guiaRemision extends comprobanteGeneral{
    public $dirPartida; // string
    public $razonSocialTransportista; // string
    public $tipoIdentificacionTransportista; // string
    public $rucTransportista; // string
    public $rise; // string
    public $fechaFinTransporte; // string
    public $fechaIniTransporte; // string
    public $placa; // string
    public $destinatarios; // destinatario
    public $infoAdicional; // campoAdicional
}

class liquidacionCompra extends comprobanteGeneral {
    public $tipoIdentificacionProveedor; // string
    public $razonSocialProveedor; // string
    public $identificacionProveedor; // string
    public $direccionProveedor; // string
    public $totalSinImpuestos; // string
    public $totalDescuento; // string
    public $totalConImpuesto; // totalImpuesto
    public $importeTotal; // string
    public $moneda; // string
    public $detalles; // detalleLiquidacionCompra
    public $infoAdicional; // campoAdicional
    public $pagos;
    public $guiaRemision;
    public $propina;
}

class notaCredito extends comprobanteGeneral {
    public $tipoIdentificacionComprador; // string
    public $razonSocialComprador; // string
    public $identificacionComprador; // string
    public $rise; // string
    public $codDocModificado; // string
    public $numDocModificado; // string
    public $fechaEmisionDocSustento; // string
    public $totalSinImpuestos; // string
    public $valorModificacion; // string
    public $moneda; // string
    public $totalConImpuesto; // totalImpuesto
    public $motivo; // string
    public $detalles; // detalleNotaCredito
    public $infoAdicional; // campoAdicional
    public $totalDescuento; // campoAdicional
}

class notaDebito extends comprobanteGeneral {
    public $tipoIdentificacionComprador; // string
    public $razonSocialComprador; // string
    public $identificacionComprador; // string
    public $rise; // string
    public $codDocModificado; // string
    public $numDocModificado; // string
    public $fechaEmisionDocSustento; // string
    public $totalSinImpuestos; // string
    public $impuestos; // impuesto
    public $valorTotal; // string
	public $pagos;
    public $motivos; // motivo
    public $infoAdicional; // campoAdicional
}

class campoAdicional {
    public $nombre; // string
    public $valor; // string
}

class compraCajaBanana{
    public $numCajBan;
    public $precCajBan;
}

class destinatario {
    public $identificacionDestinatario; // string
    public $razonSocialDestinatario; // string
    public $dirDestinatario; // string
    public $motivoTraslado; // string
    public $docAduaneroUnico; // string
    public $codEstabDestino; // string
    public $ruta; // string
    public $codDocSustento; // string
    public $numDocSustento; // string
    public $numAutDocSustento; // string
    public $fechaEmisionDocSustento; // string
    public $detalles; // detalleGuiaRemision
}

class detalleAdicional {
    public $nombre; // string
    public $valor; // string
}

class detalleFactura {
    public $codigoAuxiliar; // string
    public $codigoPrincipal; // string
    public $descripcion; // string
    public $cantidad; // string
    public $precioUnitario; // string
    public $descuento; // string
    public $precioTotalSinImpuesto; // string
    public $detalleAdicional; // detalleAdicional
    public $impuestos; // impuesto
	public $precioSinSubsidio;
}

class detalleGuiaRemision {
    public $codigoInterno; // string
    public $codigoAdicional; // string
    public $descripcion; // string
    public $cantidad; // string
    public $detallesAdicionales; // detalleAdicional
}

class detalleImpuestoReeembolso{
    public $codigo;
    public $codigoPorcentaje;
    public $tarifa;
    public $baseImponibleReembolso;
    public $impuestoReembolso;
}

class detalleLiquidacionCompra {
    public $codigoPrincipal; // string
    public $codigoAuxiliar; // string
    public $descripcion; // string
    public $cantidad; // string
    public $precioUnitario; // string
    public $descuento; // string
    public $precioTotalSinImpuesto; // string
    public $detalleAdicional; // detalleAdicional
    public $impuestos; // impuesto
}

class detalleNotaCredito {
    public $codigoInterno; // string
    public $codigoAdicional; // string
    public $descripcion; // string
    public $cantidad; // string
    public $precioUnitario; // string
    public $descuento; // string
    public $precioTotalSinImpuesto; // string
    public $detallesAdicionales; // detalleAdicional
    public $impuestos; // impuesto
}

class detalleReembolsoSustento{
    public $tipoIdentificacionProveedorReembolso;
    public $identificacionProveedorReembolso;
    public $codPaisPagoProveedorReembolso;
    public $tipoProveedorReembolso;
    public $codDocReembolso;
    public $estabDocReembolso;
    public $ptoEmiDocReembolso;
    public $secuencialDocReembolso;
    public $fechaEmisionDocReembolso;
    public $numeroAutorizacionDocReemb;
    public $detalleImpuestos;
}

class dividendo{
    public $fechaPagoDiv;
    public $imRentaSoc;
    public $ejerFisUtDiv;
}

class impuesto {
    public $codigo; // string
    public $codigoPorcentaje; // string
    public $tarifa; // string
    public $baseImponible; // string
    public $valor; // string
}

class impuestoComprobanteRetencion {
    public $codigo; // string
    public $codigoRetencion; // string
	public $baseImponible; // string
    public $porcentajeRetener; // string
	public $valorRetenido; // string
    public $codDocSustento;
	public $numDocSustento;
	public $fechaEmisionDocSustento;
}

class impuestoComprobanteRetencionDos {
    public $codigo; // string
    public $codigoRetencion; // string
    public $baseImponible; // string
    public $porcentajeRetener; // string
    public $valorRetenido; // string
    public $dividendos;
    public $compraCajaBanano;
}

class impuestoDocSustento{
    public $codImpuestoDocSustento;
    public $codigoPorcentaje;
    public $baseImponible;
    public $tarifa;
    public $valorImpuesto;
}

class motivo {
    public $razon; // string
    public $valor; // string
}

class pagos {
    public $formaPago;
    public $total;
    public $plazo;
    public $unidadTiempo;
}

class pagoSustento{
    public $formapago;
    public $total;
}

class sustentoRetencion{
    public $codSustento;
    public $codDocSustento;
    public $numDocSustento;
    public $fechaEmisionDocSustento;
    public $fechaRegistroContable;
    public $numAutDocSustento;
    public $pagoLocExt;
    public $tipoRegi;
    public $paisEfecPago;
    public $aplicConvDobTrib;
    public $pagExtSujRetNorLeg;
    public $pagoRegFis;
    public $totalComprobantesReembolso;
    public $totalBaseImponibleReembolso;
    public $totalImpuestoReembolso;
    public $totalSinImpuestos;
    public $importeTotal;
    public $impuestosDocSustento;
    public $retenciones;
    public $reembolsos;
    public $pagos;
}

class totalImpuesto {
    public $codigo; // string
    public $codigoPorcentaje; // string
    public $descuentoAdicional; // string
    public $baseImponible; // string
    public $tarifa; // string
    public $valor; // string
}

class configAplicacion {
    public $dirAutorizados; // string
    public $dirFirma; // string
    public $dirLogo; // string
    public $passFirma; // string
	public $dirIreport;//string
}

class configCorreo {
    public $correoAsunto; // string
    public $correoHost; // string
    public $correoPass; // string
    public $correoPort; // string
    public $correoRemitente; // string
    public $sslHabilitado; // string
	public $rutaLogo;
}

class procesarComprobanteLote {
    public $comprobanteLote; // comprobanteLote
}

class procesarComprobanteLoteResponse {
    public $return; // respuestaComprobanteLote
}

class respuestaComprobanteLote {
    public $claveAccesoConsultada; // string
    public $error; // boolean
    public $mensajeGeneral; // mensajeGenerado
    public $respuestas; // respuesta
}

class mensajeGenerado {
    public $identificador; // string
    public $informacionAdicional; // string
    public $mensaje; // string
    public $tipo; // string
}

class respuesta {
    public $claveAcceso; // string
    public $comprobanteID; // string
    public $estadoComprobante; // string
    public $mensajes; // mensajeGenerado
    public $numeroAutorizacion; // string
    public $fechaAutorizacion;
}

class procesarComprobantePendiente {
    public $comprobantePendiente; // comprobantePendiente
}

class procesarComprobantePendienteResponse {
    public $return; // respuesta
}

class procesarComprobante {
    public $comprobante; // comprobanteGeneral
    public $envioSRI;
}

class procesarComprobanteResponse {
    public $return; // respuesta
}

class obtenerComprobante{
    public $claveAcceso;
    public $ambiente;
}
/**
 * ProcesarComprobanteElectronico class
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class ProcesarComprobanteElectronico extends SoapClient {
    private static $classmap = array(
        'comprobanteGeneral' => 'comprobanteGeneral',
		'comprobanteLote' => 'comprobanteLote',
        'comprobantePendiente' => 'comprobantePendiente',
		'comprobanteRetencion' => 'comprobanteRetencion',
        'comprobanteRetencionDos'=>'comprobanteRetencionDos',
		'factura' => 'factura',
        'guiaRemision' => 'guiaRemision',
		'liquidacionCompra' => 'liquidacionCompra',
		'notaCredito' => 'notaCredito',
		'notaDebito' => 'notaDebito',
		'campoAdicional' => 'campoAdicional',
		'compraCajaBanana' => 'compraCajaBanana',
		'destinatario' => 'destinatario',
		'detalleAdicional' => 'detalleAdicional',
		'detalleFactura' => 'detalleFactura',
        'detalleGuiaRemision' => 'detalleGuiaRemision',
		'detalleImpuestoReeembolso' => 'detalleImpuestoReeembolso',
		'detalleLiquidacionCompra' => 'detalleLiquidacionCompra',
        'detalleNotaCredito' => 'detalleNotaCredito',
		'detalleReembolsoSustento' => 'detalleReembolsoSustento',
		'dividendo' => 'dividendo',
		'impuesto' => 'impuesto',
        'impuestoComprobanteRetencion' => 'impuestoComprobanteRetencion',
        'impuestoComprobanteRetencionDos'=>'impuestoComprobanteRetencionDos',
		'impuestoDocSustento' => 'impuestoDocSustento',
		'motivo' => 'motivo',
        'pagos' => 'pagos',
		'pagoSustento' => 'pagoSustento',
		'sustentoRetencion' => 'sustentoRetencion',
		'totalImpuesto' => 'totalImpuesto',
        'configAplicacion' => 'configAplicacion',
        'configCorreo' => 'configCorreo',
        'procesarComprobanteLote' => 'procesarComprobanteLote',
        'procesarComprobanteLoteResponse' => 'procesarComprobanteLoteResponse',
        'respuestaComprobanteLote' => 'respuestaComprobanteLote',
        'mensajeGenerado' => 'mensajeGenerado',
        'respuesta' => 'respuesta',
        'procesarComprobantePendiente' => 'procesarComprobantePendiente',
        'procesarComprobantePendienteResponse' => 'procesarComprobantePendienteResponse',
        'procesarComprobante' => 'procesarComprobante',
        'procesarComprobanteResponse' => 'procesarComprobanteResponse',
		'obtenerComprobante' => 'obtenerComprobante',
    );

	public function ProcesarComprobanteElectronico($wsdl = "http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl", $options = array()) {
        foreach (self::$classmap as $key => $value) {
            if (!isset($options['classmap'][$key])) {
                $options['classmap'][$key] = $value;
            }
        }
        parent::__construct($wsdl, $options);
    }

    /**
     *  
     *
     * @param procesarComprobantePendiente $parameters
     * @return procesarComprobantePendienteResponse
     */
    public function procesarComprobantePendiente(procesarComprobantePendiente $parameters) {
        return $this->__soapCall('procesarComprobantePendiente', array($parameters), array(
                    'uri' => 'http://Servicio/',
                    'soapaction' => ''
                        )
        );
    }

    /**
     *  
     *
     * @param procesarComprobanteLote $parameters
     * @return procesarComprobanteLoteResponse
     */
    public function procesarComprobanteLote(procesarComprobanteLote $parameters) {
        return $this->__soapCall('procesarComprobanteLote', array($parameters), array(
                    'uri' => 'http://Servicio/',
                    'soapaction' => ''
                        )
        );
    }

    /**
     *  
     *
     * @param procesarComprobante $parameters
     * @return procesarComprobanteResponse
     */
    public function procesarComprobante(procesarComprobante $parameters) {
        //var_dump($parameters);
        return $this->__soapCall('procesarComprobante', array($parameters), array(
                    'uri' => 'http://Servicio/',
                    'soapaction' => ''
                        )
        );
    }
	

	public function obtenerComprobante(obtenerComprobante $parameters){
        return $this->__soapCall('obtenerComprobante',array($parameters),array(
                                                                            'uri' => 'http://Servicio/',
                                                                            'soapaction' => ''            
                                                                        )

        );
    }

}

?>