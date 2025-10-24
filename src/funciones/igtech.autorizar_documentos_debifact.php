<?php
//__NM____NM__FUNCTION__NM__//
	
	function crearNombreCarpeta($xmlComprobante) {
        $nombre = "";
        $codDoc = $xmlComprobante->codDoc;
        if ($codDoc=='01') {
            $nombre = $xmlComprobante->identificacionComprador;
        } else if ($codDoc=="03") {
            $nombre = $xmlComprobante->identificacionProveedor;
        } else if ($codDoc=="04") {
            $nombre = $xmlComprobante->identificacionComprador;
        } else if ($codDoc=="05") {
            $nombre = $xmlComprobante->identificacionComprador;
        } else if ($codDoc=="06") {
            $nombre = $xmlComprobante->rucTransportista;
        } else if ($codDoc=="07") {
            $nombre = $xmlComprobante->identificacionSujetoRetenido;
        } 
        return $nombre;
    }

	function crearNombreFichero($xmlComprobante,$extension) {
        $nombre = "";
        $codDoc = $xmlComprobante->codDoc;
        $establecimiento = $xmlComprobante->establecimiento;
        $ptoEmision = $xmlComprobante->ptoEmision;
        $secuencial_doc = $xmlComprobante->secuencial;
        if ($codDoc=="01") {
            $nombre = "FAC";
        } else if ($codDoc=="03") {
            $nombre = "LIQ";
        } else if ($codDoc=="04") {
            $nombre = "NC";
        } else if ($codDoc=="05") {
            $nombre = "ND";
        } else if ($codDoc=="06") {
            $nombre = "GR";
        } else if ($codDoc=="07") {
            $nombre = "CR";
        } 
        return $nombre . $establecimiento . "-" . $ptoEmision . "-" . $secuencial_doc .".". $extension;
    }
	
	function crearMensajeCorreo($xmlComprobante, $w_ruta_logo) {
        $codDoc = $xmlComprobante->codDoc;
		$establecimiento= $xmlComprobante->establecimiento;
        $ptoEmision = $xmlComprobante->ptoEmision;
        $secuencial_doc = $xmlComprobante->secuencial;
		
        $tipoComprobante = "";
        $dirigido = "";
        $datos = "";
        if ($codDoc=="01") {
            $tipoComprobante = "FACTURA";
            $dirigido = $xmlComprobante->razonSocialComprador;
            $importeTotal = $xmlComprobante->importeTotal;
            $datos = "<strong>Valor Total: </strong>".$importeTotal."<br /><br />";
        } else if ($codDoc=="03") {
            $tipoComprobante = "LIQUIDACIÓN DE COMPRA DE BIENES Y PRESTACIÓN DE SERVICIOS";
            $dirigido = $xmlComprobante->razonSocialProveedor;
            $importeTotal = $xmlComprobante->importeTotal;
            $datos = "<strong>Valor Total: </strong>".$importeTotal."<br /><br />";
        } else if ($codDoc=="04") {
            $tipoComprobante = "NOTA DE CRÉDITO";
            $dirigido = $xmlComprobante->razonSocialComprador;
        } else if ($codDoc=="05") {
            $tipoComprobante = "NOTA DE DÉBITO";
            $dirigido = $xmlComprobante->razonSocialComprador;
        } else if ($codDoc=="06") {
            $tipoComprobante = "GUÍA DE REMISIÓN";
            $dirigido = $xmlComprobante->razonSocialTransportista;
        } else if ($codDoc=="07") {
            $tipoComprobante = "COMPROBANTE DE RETENCIÓN";
            $dirigido = $xmlComprobante->razonSocialSujetoRetenido;
        }
        $razonSocial = $dirigido;
        $razonSocialEmisor = $xmlComprobante->razonSocial;
        $mensaje="<img src='".$w_ruta_logo."' width='395' height='181'/><br /><br />
				  Estimado(a),<br /><br /><strong>".$razonSocial."</strong><br /><br />
				  Esta es una notificación automática de un documento tributario electrónico emitido por <strong>".$razonSocialEmisor."</strong>
				  <br /><br />";
        

        $mensaje .= "<strong>Tipo de Comprobante: </strong>".$tipoComprobante."<br /><br />";
        $mensaje .= "<strong>Nro de Comprobante: </strong>".$establecimiento ."-".$ptoEmision."-".$secuencial_doc."<br /><br />";
        $mensaje .= $datos;
        $mensaje .= "Los detalles generales del comprobante pueden ser consultados en el archivo pdf adjunto en este correo.<br /><br/>
					<strong>Atentamente,</strong><br /><br /><strong>".$razonSocialEmisor."</strong>";
        //echo $mensaje;
		return $mensaje;
    }	
	
	function enviarCorreo($xmlComprobante,$correo_destino){
		
		$check_sql = "SELECT sp_busca_parametro ('RUTA_DOCUMENTOS','D:/Desarrollos/')";
		sc_lookup(rs, $check_sql);
		if(isset({rs[0][0]})){
			$dir={rs[0][0]};
		}
		$empresa=$xmlComprobante->ruc;
		$cliente=crearNombreCarpeta($xmlComprobante);
		
		$pathpdf= $dir.$empresa.'/documentos/'.$cliente.'/'.crearNombreFichero($xmlComprobante,'pdf');
		$pathxml= $dir.$empresa.'/documentos/'.$cliente.'/'.crearNombreFichero($xmlComprobante,'xml');
		
		
		$configCorreo=$xmlComprobante->configCorreo;
		$mail_smtp_server    = $configCorreo->correoHost;       
		$mail_smtp_user      = $configCorreo->correoRemitente;
		$mail_smtp_pass      = $configCorreo->correoPass;
		$mail_from           = $configCorreo->correoRemitente;
		//$mail_to             = $correo_destino;
		$mail_subject        = $configCorreo->correoAsunto;
		$mail_message        = crearMensajeCorreo($xmlComprobante,$configCorreo->rutaLogo); 
		$mail_format         = 'H';//format: (T)ext or (H)tml
		$mail_copies		 = '';	
		$mail_tp_copies		 = '';
		$mail_port           = $configCorreo->correoPort;
		$mail_tp_connection  = 'S';
		$mail_atachment		 = array();
		$mail_atachment[0]= $pathpdf;
		$mail_atachment[1]= $pathxml;
		
		try {
    		$destinatarios = explode(",", $correo_destino);
			foreach( $destinatarios as $destinatario){

				$mail_to= trim($destinatario);

				sc_mail_send($mail_smtp_server,
						 $mail_smtp_user,
						 $mail_smtp_pass,
						 $mail_from,
						 $mail_to,
						 $mail_subject,
						 $mail_message,
						 $mail_format,
						 $mail_copies,
						 $mail_tp_copies,
						 $mail_port,
						 $mail_tp_connection,
						 $mail_atachment);	

			}
			if ({sc_mail_ok}){
				return true;
			}else{
				return false;
			}
		} catch (Exception $e) {
			//echo 'Excepción capturada: ',  $e->getMessage(), "\n";
			return false;
		}
	
	}	

	function autorizar_factura ($fac_numero, $i_autorizar, $i_log=false){	
		if($i_log){
			var_dump($fac_numero);
			echo '<br>';
		}	
		$correo_enviado='';
		$check_sql = "SELECT sp_busca_parametro ('RUTA_FIRMADOR','http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $ruta_firmador={rs[0][0]};
		}
		$procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
		$configApp = new \configAplicacion();
		$configCorreo = new \configCorreo();
		$factura = new factura();
		$check_sql = "SELECT sp_busca_parametro ('RUTA_EMPRESA','D:/Desarrollos/')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir={rs[0][0]};
		}
		$check_sql = "SELECT sp_busca_parametro ('RUTA_IREPORT','D:/Desarrollo/IReport')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir_ireport={rs[0][0]};
		}
		$check_sql="select fac_empresa,fac_establecimiento from del_factura where fac_numero =".$fac_numero;
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
			$var_empresa={rs[0][0]};
			$var_establecimiento={rs[0][1]};
		}

		$check_sql = "SELECT
							emp_ruc,
							emp_logo,
							emp_firma,
							emp_clave_firma,
							emp_razon_social,
							emp_nombre_comercial,
							emp_direccion_matriz,
							emp_obligado_contabilidad,
							emp_ambiente_sri,
							emp_tipo_emision,
							emp_contribuyente_especial,
							fil_id,
							csmtp_servidor,
							csmtp_contrasenia,
							csmtp_puerto,
							csmtp_usuario,
							emp_es_op_transporte,
							emp_regimen_especial,
							emp_agente_retencion,
							emp_calificacion_artesanal,
							csmtp_ruta_imagenes
						FROM
							v_del_datos_empresa_sri
						WHERE emp_ruc='".$var_empresa."'
						AND est_id=".$var_establecimiento.";";
		sc_lookup(rs_empresa, $check_sql);
		if (isset({rs_empresa[0][0]})){
			$configApp->dirAutorizados = $dir.{rs_empresa[0][0]}."/documentos/";
			$configApp->dirLogo =  $dir.{rs_empresa[0][0]}."/logo/".{rs_empresa[0][1]};
			$configApp->dirFirma =  $dir.{rs_empresa[0][0]}."/firma/".{rs_empresa[0][2]};
			$configApp->passFirma = {rs_empresa[0][3]};
			$configApp->dirIreport=$dir_ireport;
			$factura->configAplicacion = $configApp;

			$configCorreo->correoAsunto = "Nueva Factura";
			$configCorreo->correoHost = {rs_empresa[0][12]};
			$configCorreo->correoPass = {rs_empresa[0][13]};
			$configCorreo->correoPort = {rs_empresa[0][14]};
			$configCorreo->correoRemitente = {rs_empresa[0][15]};
			$configCorreo->sslHabilitado = false;
			$configCorreo->rutaLogo={rs_empresa[0][20]}.'logo.jpg';
			$factura->configCorreo = $configCorreo;

			$factura->ruc = {rs_empresa[0][0]};
			$factura->razonSocial = {rs_empresa[0][4]};
			$factura->nombreComercial = {rs_empresa[0][5]}; 
			$factura->dirMatriz = {rs_empresa[0][6]}; 
			$factura->obligadoContabilidad ={rs_empresa[0][7]}; 
			$factura->tipoEmision = {rs_empresa[0][9]};
			if ({rs_empresa[0][10]}!=''){
				$factura->contribuyenteEspecial = {rs_empresa[0][10]};
			}	
			$factura->padronMicroempresa={rs_empresa[0][17]};
			$factura->padronAgenteRetencion={rs_empresa[0][18]};
			if({rs_empresa[0][18]}=='S'){
				$check_sql = "SELECT sp_busca_parametro ('NUMERORESOAR','1')";
				sc_lookup(rs, $check_sql);
				if (isset({rs[0][0]})){
				   $factura->numeroResolucion={rs[0][0]};   
				}
			}
			$factura->artesanoCalificado={rs_empresa[0][19]};
		}

		$check_sql="SELECT
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
						WHERE fac_numero=".$fac_numero ;
		sc_lookup(rs_factura, $check_sql);

		if (isset({rs_factura[0][0]})){
			$correo_enviado={rs_factura[0][39]};
			$factura->padronRimpe={rs_factura[0][35]};
			$factura->leyendaRimpe={rs_factura[0][37]};
			$factura->ambiente = {rs_factura[0][1]};
			$factura->codDoc = {rs_factura[0][2]};
			$factura->fechaEmision = {rs_factura[0][3]};
			$factura->dirEstablecimiento = {rs_factura[0][4]};
			$factura->establecimiento = {rs_factura[0][5]}; 
			$factura->ptoEmision = {rs_factura[0][6]}; 
			$factura->secuencial = {rs_factura[0][7]};
			$factura->tipoIdentificacionComprador = {rs_factura[0][8]};
			if({rs_factura[0][25]}<>''){
				$factura->guiaRemision={rs_factura[0][25]};
			}
			$factura->razonSocialComprador = {rs_factura[0][9]}; 
			$factura->identificacionComprador = {rs_factura[0][10]};
			$factura->direccionComprador={rs_factura[0][11]};
			$factura->totalSinImpuestos = {rs_factura[0][14]}; 
			if({rs_factura[0][36]}>0){
				$factura->totalSubsidio={rs_factura[0][36]};
			}
			$factura->totalDescuento = {rs_factura[0][15]}; 
			$total_Impuestos=array();
			$i=0;
				if({rs_factura[0][16]}>0){
					$sql_ivas_cobrados="SELECT df_porcentaje_iva,
												sum(df_base_iva),
												round(sum(df_base_iva) *iva_porcentaje/100,2)
										FROM  del_detalle_factura 
										inner join sri_tarifa_iva on iva_codigo=df_porcentaje_iva
										WHERE df_factura=".$fac_numero."
										and df_porcentaje_iva in (2,3,8)
										group by df_porcentaje_iva,iva_porcentaje";
					sc_select(rs_ivas, $sql_ivas_cobrados); 
					if ({rs_ivas}  === false){
						echo "Error al acceder a del_detalle_factura";
					}else{
						while (!{rs_ivas}->EOF){
							$totalImpuesto = new totalImpuesto();
							$totalImpuesto->codigo =2; 
							$totalImpuesto->codigoPorcentaje = {rs_ivas}->fields[0]; 
							$totalImpuesto->baseImponible = {rs_ivas}->fields[1];
							$totalImpuesto->valor = {rs_ivas}->fields[2];
							$total_Impuestos[$i]=$totalImpuesto;
							$i+=1;
							{rs_ivas}->MoveNext();
						}
						{rs_ivas}->Close();
					}	
				}	
				if({rs_factura[0][18]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '0'; 
					$totalImpuesto->baseImponible = {rs_factura[0][18]}; 
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_factura[0][19]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '6'; 
					$totalImpuesto->baseImponible = {rs_factura[0][19]}; 
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_factura[0][20]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo ='2'; 
					$totalImpuesto->codigoPorcentaje = '7'; 
					$totalImpuesto->baseImponible = {rs_factura[0][20]}; 
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_factura[0][21]}>0){	
					$check_sql="SELECT  '3' as impuesto,
										 ice_codigo,
										sum(df_subtotal),
										sum(df_valor_ice) 
								FROM del_detalle_factura 
								join sri_tarifa_ice on df_porcentaje_ice::integer=ice_id
								WHERE df_porcentaje_ice<>'0' 
								and df_factura=".$fac_numero."
								group by df_porcentaje_ice,ice_codigo";
					sc_select(rs_impuesto, $check_sql); 
					if ({rs_impuesto}  === false){
						echo "Error al acceder a del_detalle_factura";
					}else{
						while (!{rs_impuesto}->EOF){
							$totalImpuesto = new totalImpuesto();
							$totalImpuesto->codigo ={rs_impuesto}->fields[0]; 
							$totalImpuesto->codigoPorcentaje = {rs_impuesto}->fields[1]; 
							$totalImpuesto->baseImponible = {rs_impuesto}->fields[2];
							$totalImpuesto->valor = {rs_impuesto}->fields[3];
							$total_Impuestos[$i]=$totalImpuesto;
							$i+=1;
							{rs_impuesto}->MoveNext();
						}
						{rs_impuesto}->Close();
					}	
				}
				if({rs_factura[0][22]}>0){	
					$check_sql="SELECT  '5' as impuesto,
										df_porcentaje_irbpnr,
										sum(df_base_irbpnr*df_cantidad),
										sum(df_valor_irbpnr) 
								FROM del_detalle_factura 
								WHERE df_porcentaje_irbpnr<>'0' 
								and df_factura=".$fac_numero ."
								group by df_porcentaje_irbpnr";
					sc_select(rs_impuesto, $check_sql); 
					if ({rs_impuesto}  === false){
						echo "Error al acceder a del_detalle_factura";
					}else{
						while (!{rs_impuesto}->EOF){
							$totalImpuesto = new totalImpuesto();
							$totalImpuesto->codigo ={rs_impuesto}->fields[0]; 
							$totalImpuesto->codigoPorcentaje = {rs_impuesto}->fields[1];
							$totalImpuesto->baseImponible = {rs_impuesto}->fields[2]; 
							$totalImpuesto->valor = {rs_impuesto}->fields[3];
							$total_Impuestos[$i]=$totalImpuesto;
							$i+=1;
							{rs_impuesto}->MoveNext();
						}
						{rs_impuesto}->Close();
					}	
				}
			$factura->totalConImpuesto = $total_Impuestos;
			$factura->propina = {rs_factura[0][23]}; 
			$factura->importeTotal = {rs_factura[0][24]}; 
			$factura->moneda = {rs_factura[0][27]};
			//aqui van los detalles
			$check_sql="SELECT
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
								round(df_subtotal,2),
								ice_tarifa,
								df_valor_ice,
								pro_irbpnr,
								df_porcentaje_irbpnr,
								irbpnr_tarifa,
								round(df_base_irbpnr*df_cantidad,2),
								df_valor_irbpnr,
								df_descripcion,
								df_precio_sin_subsidio
							FROM
								v_del_detalle_factura_sri
							where df_factura=".$fac_numero."
							order by df_id" ;

			sc_select(rs_detalles, $check_sql);   
			if ({rs_detalles}  === false){
				echo "Error al acceder al detalle de la factura";
			}else{
				$detalles_factura = array();
				$i=0;
				while (!{rs_detalles}->EOF){
					$detalleFactura = new detalleFactura();
					$detalleFactura->codigoPrincipal = {rs_detalles}->fields[1];
					$detalleFactura->codigoAuxiliar = {rs_detalles}->fields[2]; 
					$detalleFactura->descripcion = {rs_detalles}->fields[3]; 
					$detalleFactura->cantidad = {rs_detalles}->fields[4]; 
					$detalleFactura->precioUnitario = {rs_detalles}->fields[5]; 
					$detalleFactura->descuento = {rs_detalles}->fields[6]; 
					$detalleFactura->precioTotalSinImpuesto = {rs_detalles}->fields[7]; 
					$detalleFactura->precioSinSubsidio={rs_detalles}->fields[24];

					if ({rs_detalles}->fields[23] <>""){
						$informacion_adicional=array();
						$detalle_adicional= new detalleAdicional();
						$detalle_adicional->nombre = 'Adicional';
						$detalle_adicional->valor = {rs_detalles}->fields[23] ;
						$informacion_adicional[0]=$detalle_adicional;							 
						$detalleFactura->detalleAdicional = $informacion_adicional;	
					}

						$impuestos_det=array();
						$j=0;
						$impuesto = new impuesto();
						$impuesto->codigo = {rs_detalles}->fields[8];
						$impuesto->codigoPorcentaje = {rs_detalles}->fields[9]; 
						$impuesto->tarifa = {rs_detalles}->fields[10]; 
						$impuesto->baseImponible = {rs_detalles}->fields[11]; 
						$impuesto->valor = {rs_detalles}->fields[12];
						$impuestos_det[$j]=$impuesto;
						$j+=1;
						if({rs_detalles}->fields[14]<>'0'){
							$impuesto = new impuesto();
							$impuesto->codigo = {rs_detalles}->fields[13];
							$impuesto->codigoPorcentaje = {rs_detalles}->fields[14]; 
							//$impuesto->tarifa = {rs_detalles}->fields[16];  
							$impuesto->tarifa = "0.00";  
							$impuesto->baseImponible = {rs_detalles}->fields[15]; 
							$impuesto->valor = {rs_detalles}->fields[17];
							$impuestos_det[$j]=$impuesto;
							$j+=1;
						}
						if({rs_detalles}->fields[19]<>'0'){
							$impuesto = new impuesto();
							$impuesto->codigo = {rs_detalles}->fields[18];
							$impuesto->codigoPorcentaje = {rs_detalles}->fields[19]; 
							$impuesto->tarifa = {rs_detalles}->fields[20]; // 
							$impuesto->baseImponible = {rs_detalles}->fields[21]; 
							$impuesto->valor = {rs_detalles}->fields[22];
							$impuestos_det[$j]=$impuesto;
							$j+=1;
						}
					$detalleFactura->impuestos = $impuestos_det;
					$detalles_factura[$i]=$detalleFactura;
					{rs_detalles}->MoveNext();
					$i+=1;
				}
				{rs_detalles}->Close();
				$factura->detalles = $detalles_factura;
			}
			$pagos = array();
			$check_sql="SELECT 	a.fp_id,
								sri_forma_pago.fp_codigo,
								a.fp_valor,
								coalesce(a.fp_plazo,0),
								coalesce(a.fp_unidad_tiempo,'DIAS') 
						FROM del_forma_pago_factura a 
						inner join del_forma_pago b on a.fp_forma_pago=b.fp_id	
						inner join sri_forma_pago on b.fp_sri=sri_forma_pago.fp_codigo
						where a.fp_factura=".$fac_numero ;
			sc_select(rs_pagos, $check_sql); 
			if ({rs_pagos}  === false){
				echo "Error al acceder a las formas de pago";
			}else{
				$i=0;
				while (!{rs_pagos}->EOF){
					$pago = new pagos();
					$pago->formaPago ={rs_pagos}->fields[1];
					$pago->total = {rs_pagos}->fields[2];
					$pago->plazo = {rs_pagos}->fields[3];
					$pago->unidadTiempo={rs_pagos}->fields[4];
					$pagos[$i]=$pago;
					$i+=1;
					{rs_pagos}->MoveNext();
				}
				{rs_pagos}->Close();
			}	
			$factura->pagos = $pagos;
			$camposAdicionales = array();
			$i=0;

			if({rs_empresa[0][16]}=='S'){
				if({rs_factura[0][29]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Ruc";
					$campoAdicional->valor = {rs_factura[0][29]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
				if({rs_factura[0][28]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Razon Social";
					$campoAdicional->valor = {rs_factura[0][28]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
				if({rs_factura[0][32]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Placa";
					$campoAdicional->valor = {rs_factura[0][32]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
				if({rs_factura[0][6]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Punto Emision";
					$campoAdicional->valor = {rs_factura[0][6]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}

				if({rs_factura[0][33]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Contribuyente";
					$campoAdicional->valor = {rs_factura[0][33]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}

				if({rs_factura[0][30]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Telefono Socio";
					$campoAdicional->valor = {rs_factura[0][30]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
				if({rs_factura[0][38]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Direccion Emisor";
					$campoAdicional->valor = {rs_factura[0][38]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
			}

			if({rs_empresa[0][19]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "artesanoCalificado";
				$campoAdicional->valor = 'Nro. '.{rs_empresa[0][19]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			if({rs_factura[0][12]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Telefono";
				$campoAdicional->valor = {rs_factura[0][12]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			if({rs_factura[0][13]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Email";
					$campoAdicional->valor = {rs_factura[0][13]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
			if({rs_factura[0][26]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Comentario";
				$campoAdicional->valor = {rs_factura[0][26]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			if({rs_factura[0][34]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Diagnostico";
				$campoAdicional->valor = {rs_factura[0][34]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			$factura->infoAdicional = $camposAdicionales;

			try{
				$procesarComprobante = new procesarComprobante();
				$procesarComprobante->comprobante = $factura;
				$procesarComprobante->envioSRI = false; //El sistema si es true //1-Crea XML en el directorio de autorizado 
				$res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
				if($i_log){
					var_dump($factura);
					echo '<br>';
					var_dump($res);
					echo '<br>';
				}	
			}catch(Throwable $e){
				echo $e->getMessage();	
			}
				
			
			if($i_autorizar=='S'){
				if ($res->return->estadoComprobante == "FIRMADO") {
					$procesarComprobante = new procesarComprobante();
					$procesarComprobante->comprobante = $factura;
					$procesarComprobante->envioSRI = true; //El sistema si es false 
					$res=$procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
					if($i_log){
						var_dump($res);
						echo '<br>';
					}	
				}else{
					if($res->return->estadoComprobante == "PROCESANDOSE"){
						$comprobantePendiente = new \comprobantePendiente();
						$comprobantePendiente->configAplicacion = $configApp;
						$comprobantePendiente->configCorreo = $configCorreo;
						$comprobantePendiente->ambiente = {rs_factura[0][1]};
						$comprobantePendiente->codDoc = {rs_factura[0][2]};
						$comprobantePendiente->establecimiento = {rs_factura[0][5]};
						$comprobantePendiente->fechaEmision = {rs_factura[0][3]};
						$comprobantePendiente->ptoEmision = {rs_factura[0][6]};
						$comprobantePendiente->ruc = {rs_empresa[0][0]};
						$comprobantePendiente->secuencial = {rs_factura[0][7]};
						$comprobantePendiente->tipoEmision = {rs_empresa[0][9]};
						$comprobantePendiente->padronMicroempresa = {rs_empresa[0][17]};
						$comprobantePendiente->padronAgenteRetencion = {rs_empresa[0][18]};
						$comprobantePendiente->padronRimpe = {rs_empresa[0][21]};
						$procesarComprobantePendiente = new \procesarComprobantePendiente();
						$procesarComprobantePendiente->comprobantePendiente = $comprobantePendiente;
						$res = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
						if ($res->return->estadoComprobante == "PROCESANDOSE") {
							$res->return->estadoComprobante = "ERROR";
						}
					}	
				}
			}

			$mensaje_final=	$res->return->estadoComprobante."<br>";
			//echo $res->return->estadoComprobante;
			if ($res->return->estadoComprobante == 'ERROR'){
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}
			if ($res->return->estadoComprobante == "FIRMADO") {
				$update_sql =  "UPDATE del_factura 
								SET  fac_estado_sri='".$res->return->estadoComprobante."'
								WHERE fac_numero=".$fac_numero ;
				sc_exec_sql($update_sql);
			}
			if($res->return->estadoComprobante=='AUTORIZADO'){
				
				if($correo_enviado=="NO"){
					if(enviarCorreo($factura,{rs_factura[0][13]})){
						$correo_enviado="SI";
					}else{
						$correo_enviado="NO";
					}
				}
				$update_sql = "UPDATE del_factura 
								SET  fac_estado_sri='".$res->return->estadoComprobante."',
									 fac_clave='".$res->return->claveAcceso."',   
									 fac_autorizacion='".$res->return->numeroAutorizacion."',
									 fac_fecha_autorizacion='".$res->return->fechaAutorizacion."',
									 fac_error_sri='',
									 fac_correo_enviado='".$correo_enviado."'
								WHERE fac_numero=".$fac_numero ;
				sc_exec_sql($update_sql);
			}
			if($res->return->estadoComprobante=='DEVUELTA'){
				if($res->return->mensajes->mensaje=='CLAVE ACCESO REGISTRADA'){
					if($correo_enviado=="NO"){
						if(enviarCorreo($factura,{rs_factura[0][13]})){
							$correo_enviado="SI";
						}else{
							$correo_enviado="NO";
						}
					}
					$update_sql = "UPDATE del_factura 
								SET  fac_estado_sri='AUTORIZADO',
									 fac_clave='".$res->return->claveAcceso."',   
									 fac_autorizacion='".$res->return->claveAcceso."',
									 fac_correo_enviado='".$correo_enviado."'
								WHERE fac_numero=".$fac_numero ;
				}else{
					$update_sql = "UPDATE del_factura 
								SET  fac_estado_sri='".$res->return->estadoComprobante."',
									 fac_clave='".$res->return->claveAcceso."',   
									 fac_error_sri='".$res->return->mensajes->mensaje."'
								WHERE fac_numero=".$fac_numero ;
				}

				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}
			if($res->return->estadoComprobante=='NO AUTORIZADO'){
				$update_sql = "UPDATE del_factura 
								SET  fac_estado_sri='".$res->return->estadoComprobante."',
									 fac_clave='".$res->return->claveAcceso."',   
									 fac_error_sri='".$res->return->mensajes->mensaje."',
									 fac_fecha_autorizacion='".$res->return->fechaAutorizacion."'
								WHERE fac_numero=".$fac_numero ;
				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}

			return $mensaje_final;
		}
	}	

	function autorizar_liquidacion ($liq_numero,$i_autorizar,$i_log=false){
		$check_sql = "SELECT sp_busca_parametro ('RUTA_FIRMADOR','http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $ruta_firmador={rs[0][0]};
		}
		$correo_enviado='';
		$procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
		$configApp = new \configAplicacion();
		$configCorreo = new \configCorreo();
		$liquidacion = new \liquidacionCompra();
		$check_sql = "SELECT sp_busca_parametro ('RUTA_EMPRESA','D:/Desarrollo/')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir={rs[0][0]};
		}
		$check_sql = "SELECT sp_busca_parametro ('RUTA_IREPORT','D:/Desarrollo/IReport')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir_ireport={rs[0][0]};
		}
		$check_sql="SELECT fc_empresa,liq_establecimiento FROM del_factura_compra where fc_id=".$liq_numero;
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
			$var_empresa={rs[0][0]};
			$var_establecimiento={rs[0][1]};	
		}
		$check_sql = "SELECT
							emp_ruc,
							emp_logo,
							emp_firma,
							emp_clave_firma,
							emp_razon_social,
							emp_nombre_comercial,
							emp_direccion_matriz,
							emp_obligado_contabilidad,
							emp_ambiente_sri,
							emp_tipo_emision,
							emp_contribuyente_especial,
							fil_id,
							csmtp_servidor,
							csmtp_contrasenia,
							csmtp_puerto,
							csmtp_usuario,
							emp_es_op_transporte,
							emp_regimen_especial,
							emp_agente_retencion,
							emp_calificacion_artesanal,
							csmtp_ruta_imagenes
						FROM
							v_del_datos_empresa_sri
						WHERE emp_ruc='".$var_empresa."'
						AND est_id=".$var_establecimiento.";";
		sc_lookup(rs_empresa, $check_sql);

		if (isset({rs_empresa[0][0]})){
			$configApp->dirAutorizados = $dir.{rs_empresa[0][0]}."/documentos/";
			$configApp->dirLogo =  $dir.{rs_empresa[0][0]}."/logo/".{rs_empresa[0][1]};
			$configApp->dirFirma =  $dir.{rs_empresa[0][0]}."/firma/".{rs_empresa[0][2]};
			$configApp->passFirma = {rs_empresa[0][3]};
			$configApp->dirIreport=$dir_ireport;
			$liquidacion->configAplicacion = $configApp;

			$configCorreo->correoAsunto = "Nueva Liquidacion de Compras";
			$configCorreo->correoHost = {rs_empresa[0][12]};
			$configCorreo->correoPass = {rs_empresa[0][13]};
			$configCorreo->correoPort = {rs_empresa[0][14]};
			$configCorreo->correoRemitente = {rs_empresa[0][15]};
			$configCorreo->sslHabilitado = false;
			$configCorreo->rutaLogo={rs_empresa[0][20]}.'logo.jpg';
			$liquidacion->configCorreo = $configCorreo;

			$liquidacion->ruc = {rs_empresa[0][0]};
			$liquidacion->razonSocial = {rs_empresa[0][4]};
			$liquidacion->nombreComercial = {rs_empresa[0][5]}; 
			$liquidacion->dirMatriz = {rs_empresa[0][6]}; 
			$liquidacion->obligadoContabilidad ={rs_empresa[0][7]}; 
			$liquidacion->tipoEmision = {rs_empresa[0][9]};

			if ({rs_empresa[0][10]}!=''){
				$liquidacion->contribuyenteEspecial = {rs_empresa[0][10]};
			}
			$liquidacion->padronMicroempresa={rs_empresa[0][17]};
			$liquidacion->padronAgenteRetencion={rs_empresa[0][18]};
			if({rs_empresa[0][18]}=='S'){
				$check_sql = "SELECT sp_busca_parametro ('NUMERORESOAR','1')";
				sc_lookup(rs, $check_sql);
				if (isset({rs[0][0]})){
				   $liquidacion->numeroResolucion={rs[0][0]};
				}
			}
			$liquidacion->artesanoCalificado={rs_empresa[0][19]};

		}

		$check_sql="SELECT
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
							fc_total-fc_propina,
							est_padronrimpe,
							est_leyenda_rimpe,
							fc_correo_enviado
					FROM    v_del_datos_liquidacion_sri
					WHERE fc_id=".$liq_numero ;

		sc_lookup(rs_liquidacion, $check_sql);

		if (isset({rs_liquidacion[0][0]})){
			$correo_enviado={rs_liquidacion[0][31]};
			$liquidacion->padronRimpe={rs_liquidacion[0][29]};
			$liquidacion->leyendaRimpe={rs_liquidacion[0][30]};
			$liquidacion->ambiente = {rs_liquidacion[0][1]};
			$liquidacion->codDoc = {rs_liquidacion[0][2]};
			$liquidacion->fechaEmision = {rs_liquidacion[0][3]};
			$liquidacion->dirEstablecimiento = {rs_liquidacion[0][4]};
			$liquidacion->establecimiento = {rs_liquidacion[0][5]}; 
			$liquidacion->ptoEmision = {rs_liquidacion[0][6]}; 
			$liquidacion->secuencial = {rs_liquidacion[0][7]};
			$liquidacion->tipoIdentificacionProveedor = {rs_liquidacion[0][8]};
			if({rs_liquidacion[0][25]}<>''){
				$liquidacion->guiaRemision={rs_liquidacion[0][25]};
			}
			$liquidacion->razonSocialProveedor = {rs_liquidacion[0][9]}; 
			$liquidacion->identificacionProveedor = {rs_liquidacion[0][10]};
			$liquidacion->direccionProveedor= {rs_liquidacion[0][11]};
			$liquidacion->totalSinImpuestos = {rs_liquidacion[0][14]}; 
			$liquidacion->totalDescuento = {rs_liquidacion[0][15]}; 
			$total_Impuestos=array();
			$i=0;
				if({rs_liquidacion[0][16]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '2'; 
					$totalImpuesto->baseImponible = {rs_liquidacion[0][16]}; 
					$totalImpuesto->valor = {rs_liquidacion[0][17]};
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_liquidacion[0][18]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '0'; 
					$totalImpuesto->baseImponible = {rs_liquidacion[0][18]}; 
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_liquidacion[0][19]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '6'; 
					$totalImpuesto->baseImponible = {rs_liquidacion[0][19]}; 
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_liquidacion[0][20]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo ='2'; 
					$totalImpuesto->codigoPorcentaje = '7'; 
					$totalImpuesto->baseImponible = {rs_liquidacion[0][20]}; 
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_liquidacion[0][21]}>0){	
					$check_sql="SELECT  '3' as impuesto,
										 dfc_porcentaje_ice,
										sum(dfc_base_ice),
										sum(dfc_valor_ice) 
								FROM del_detalle_factura_compra 
								WHERE dfc_porcentaje_ice<>'0'
								and dfc_factura=".$liq_numero."
								group by dfc_porcentaje_ice";
					sc_select(rs_impuesto, $check_sql); 
					if ({rs_impuesto}  === false){
						echo "Error al acceder a del_detalle_factura";
					}else{
						while (!{rs_impuesto}->EOF){
							$totalImpuesto = new totalImpuesto();
							$totalImpuesto->codigo ={rs_impuesto}->fields[0]; 
							$totalImpuesto->codigoPorcentaje = {rs_impuesto}->fields[1]; 
							$totalImpuesto->baseImponible = {rs_impuesto}->fields[2];
							$totalImpuesto->valor = {rs_impuesto}->fields[3];
							$total_Impuestos[$i]=$totalImpuesto;
							$i+=1;
							{rs_impuesto}->MoveNext();
						}
						{rs_impuesto}->Close();
					}	
				}
				if({rs_liquidacion[0][22]}>0){	
					$check_sql="SELECT  '5' as impuesto,
										 dfc_porcentaje_irbpnr,
										sum(dfc_base_irbpnr),
										sum(dfc_valor_irbpnr) 
								FROM del_detalle_factura_compra 
								WHERE dfc_porcentaje_irbpnr<>'0'
								and dfc_factura=".$liq_numero."
								group by dfc_porcentaje_irbpnr";
					sc_select(rs_impuesto, $check_sql); 
					if ({rs_impuesto}  === false){
						echo "Error al acceder a del_detalle_factura";
					}else{
						while (!{rs_impuesto}->EOF){
							$totalImpuesto = new totalImpuesto();
							$totalImpuesto->codigo ={rs_impuesto}->fields[0]; 
							$totalImpuesto->codigoPorcentaje = {rs_impuesto}->fields[1];
							$totalImpuesto->baseImponible = {rs_impuesto}->fields[2]; 
							$totalImpuesto->valor = {rs_impuesto}->fields[3];
							$total_Impuestos[$i]=$totalImpuesto;
							$i+=1;
							{rs_impuesto}->MoveNext();
						}
						{rs_impuesto}->Close();
					}	
				}
			$liquidacion->totalConImpuesto = $total_Impuestos;
			$liquidacion->propina = {rs_liquidacion[0][23]}; 
			$liquidacion->importeTotal = {rs_liquidacion[0][28]}; 
			$liquidacion->moneda = {rs_liquidacion[0][27]};
			//aqui van los detalles
			$check_sql="SELECT
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
							where dfc_factura=".$liq_numero."
							order by dfc_id" ;

			sc_select(rs_detalles, $check_sql);   
			if ({rs_detalles}  === false){
				echo "Error al acceder al detalle de la factura";
			}else{
				$detalles_factura = array();
				$i=0;
				while (!{rs_detalles}->EOF){
					$detalleFactura = new detalleFactura();
					$detalleFactura->codigoPrincipal = {rs_detalles}->fields[1];
					$detalleFactura->codigoAuxiliar = {rs_detalles}->fields[2]; 
					$detalleFactura->descripcion = {rs_detalles}->fields[3]; 
					$detalleFactura->cantidad = {rs_detalles}->fields[4]; 
					$detalleFactura->precioUnitario = {rs_detalles}->fields[5]; 
					$detalleFactura->descuento = {rs_detalles}->fields[6]; 
					$detalleFactura->precioTotalSinImpuesto = {rs_detalles}->fields[7]; 
					if ({rs_detalles}->fields[23] <>""){
						$informacion_adicional=array();
						$detalle_adicional= new detalleAdicional();
						$detalle_adicional->nombre = 'Adicional';
						$detalle_adicional->valor = {rs_detalles}->fields[23] ;
						$informacion_adicional[0]=$detalle_adicional;							 
						$detalleFactura->detalleAdicional = $informacion_adicional;	
					}
						$impuestos_det=array();
						$j=0;
						$impuesto = new impuesto();
						$impuesto->codigo = {rs_detalles}->fields[8];
						$impuesto->codigoPorcentaje = {rs_detalles}->fields[9]; 
						$impuesto->tarifa = {rs_detalles}->fields[10]; 
						$impuesto->baseImponible = {rs_detalles}->fields[11]; 
						$impuesto->valor = {rs_detalles}->fields[12];
						$impuestos_det[$j]=$impuesto;
						$j+=1;
						if({rs_detalles}->fields[14]<>'0'){
							$impuesto = new impuesto();
							$impuesto->codigo = {rs_detalles}->fields[13];
							$impuesto->codigoPorcentaje = {rs_detalles}->fields[14]; 
							$impuesto->tarifa = {rs_detalles}->fields[16];  
							$impuesto->baseImponible = {rs_detalles}->fields[15]; 
							$impuesto->valor = {rs_detalles}->fields[17];
							$impuestos_det[$j]=$impuesto;
							$j+=1;
						}
						if({rs_detalles}->fields[19]<>'0'){
							$impuesto = new impuesto();
							$impuesto->codigo = {rs_detalles}->fields[18];
							$impuesto->codigoPorcentaje = {rs_detalles}->fields[19]; 
							$impuesto->tarifa = {rs_detalles}->fields[20]; // 
							$impuesto->baseImponible = {rs_detalles}->fields[21]; 
							$impuesto->valor = {rs_detalles}->fields[22];
							$impuestos_det[$j]=$impuesto;
							$j+=1;
						}
					$detalleFactura->impuestos = $impuestos_det;
					$detalles_factura[$i]=$detalleFactura;
					{rs_detalles}->MoveNext();
					$i+=1;
				}
				{rs_detalles}->Close();
				$liquidacion->detalles = $detalles_factura;
			}
			$pagos = array();
			$check_sql="SELECT 	a.fp_id,
								sri_forma_pago.fp_codigo,
								a.fp_valor,
								coalesce(a.fp_plazo,0),
								coalesce(a.fp_unidad_tiempo,'DIAS') 
						FROM del_forma_pago_factura_compra a 
						inner join del_forma_pago b on a.fp_forma_pago=b.fp_id	
						inner join sri_forma_pago on b.fp_sri=sri_forma_pago.fp_codigo
						where a.fp_factura=".$liq_numero ;
			sc_select(rs_pagos, $check_sql); 
			if ({rs_pagos}  === false){
				echo "Error al acceder a las formas de pago";
			}else{
				$i=0;
				while (!{rs_pagos}->EOF){
					$pago = new pagos();
					$pago->formaPago ={rs_pagos}->fields[1];
					$pago->total = {rs_pagos}->fields[2];
					$pago->plazo = {rs_pagos}->fields[3];
					$pago->unidadTiempo={rs_pagos}->fields[4];
					$pagos[$i]=$pago;
					$i+=1;
					{rs_pagos}->MoveNext();
				}
				{rs_pagos}->Close();
			}	
			$liquidacion->pagos = $pagos;
			$camposAdicionales = array();
			$i=0;

			if({rs_empresa[0][19]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "artesanoCalificado";
				$campoAdicional->valor = 'Nro. '.{rs_empresa[0][19]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			if({rs_liquidacion[0][12]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Telefono";
				$campoAdicional->valor = {rs_liquidacion[0][12]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			if({rs_liquidacion[0][13]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Email";
				$campoAdicional->valor = {rs_liquidacion[0][13]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			if({rs_liquidacion[0][26]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Comentario";
				$campoAdicional->valor = {rs_liquidacion[0][26]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			$liquidacion->infoAdicional = $camposAdicionales;


			$procesarComprobante = new procesarComprobante();
			$procesarComprobante->comprobante = $liquidacion;
			$procesarComprobante->envioSRI = false; 
			$res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
			if($i_log){
				var_dump($liquidacion);
				echo '<br>';
				var_dump($res);
				echo '<br>';
			}	
			if($i_autorizar=='S'){
				if ($res->return->estadoComprobante == "FIRMADO") {
					$procesarComprobante = new procesarComprobante();
					$procesarComprobante->comprobante = $liquidacion;
					$procesarComprobante->envioSRI = true; 
					$res=$procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
					if($i_log){
						var_dump($res);
						echo '<br>';
					}	
				}else{
					if($res->return->estadoComprobante == "PROCESANDOSE"){
						$comprobantePendiente = new \comprobantePendiente();
						$comprobantePendiente->configAplicacion = $configApp;
						$comprobantePendiente->configCorreo = $configCorreo;
						$comprobantePendiente->ambiente = {rs_liquidacion[0][1]};
						$comprobantePendiente->codDoc = {rs_liquidacion[0][2]};
						$comprobantePendiente->establecimiento = {rs_liquidacion[0][5]};
						$comprobantePendiente->fechaEmision = {rs_liquidacion[0][3]};
						$comprobantePendiente->ptoEmision = {rs_liquidacion[0][6]};
						$comprobantePendiente->ruc = {rs_empresa[0][0]};
						$comprobantePendiente->secuencial = {rs_liquidacion[0][7]};
						$comprobantePendiente->tipoEmision = {rs_empresa[0][9]};
						$comprobantePendiente->padronMicroempresa = {rs_empresa[0][17]};
						$comprobantePendiente->padronAgenteRetencion = {rs_empresa[0][18]};
						$comprobantePendiente->padronRimpe = {rs_empresa[0][21]};
						$procesarComprobantePendiente = new \procesarComprobantePendiente();
						$procesarComprobantePendiente->comprobantePendiente = $comprobantePendiente;
						$res = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
						if ($res->return->estadoComprobante == "PROCESANDOSE") {
							$res->return->estadoComprobante = "ERROR";
						}
					}	
				}
			}	
			$mensaje_final=	$res->return->estadoComprobante."<br>";
			if ($res->return->estadoComprobante == 'ERROR'){
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}

			if ($res->return->estadoComprobante == "FIRMADO") {
				$update_sql =  "UPDATE del_factura_compra 
								SET  liq_estado_sri='".$res->return->estadoComprobante."'
								WHERE fc_id=".$liq_numero ;
				sc_exec_sql($update_sql);
			}
			if($res->return->estadoComprobante=='AUTORIZADO'){
				if($correo_enviado=="NO"){
					if(enviarCorreo($liquidacion,{rs_liquidacion[0][13]})){
						$correo_enviado="SI";
					}else{
						$correo_enviado="NO";
					}
				}
				
				$update_sql = "UPDATE del_factura_compra 
								SET  liq_estado_sri='".$res->return->estadoComprobante."',
									 liq_clave='".$res->return->claveAcceso."',   
									 fc_autorizacion='".$res->return->numeroAutorizacion."',
									 liq_fecha_autorizacion='".$res->return->fechaAutorizacion."',
									 fc_correo_enviado='".$correo_enviado."'
								WHERE fc_id=".$liq_numero ;
				sc_exec_sql($update_sql);
			}
			if($res->return->estadoComprobante=='DEVUELTA'){
				if($res->return->mensajes->mensaje=='CLAVE ACCESO REGISTRADA'){
					if($correo_enviado=="NO"){
						if(enviarCorreo($liquidacion,{rs_liquidacion[0][13]})){
							$correo_enviado="SI";
						}else{
							$correo_enviado="NO";
						}
					}
					
					$update_sql = "UPDATE del_factura_compra 
								SET  liq_estado_sri='AUTORIZADO',
									 liq_clave='".$res->return->claveAcceso."',   
									 fc_autorizacion='".$res->return->claveAcceso."',
									  fc_correo_enviado='".$correo_enviado."'
								WHERE fc_id=".$liq_numero ;
				}else{
					$update_sql = "UPDATE del_factura_compra 
								SET  liq_estado_sri='".$res->return->estadoComprobante."',
									 liq_clave='".$res->return->claveAcceso."',   
									 liq_error_sri='".$res->return->mensajes->mensaje."'
								WHERE fc_id=".$liq_numero ;
				}
				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}
			if($res->return->estadoComprobante=='NO AUTORIZADO'){
				$update_sql = "UPDATE del_factura_compra 
								SET  liq_estado_sri='".$res->return->estadoComprobante."',
									 liq_clave='".$res->return->claveAcceso."',   
									 liq_error_sri='".$res->return->mensajes->mensaje."',
									 liq_fecha_autorizacion='".$res->return->fechaAutorizacion."'
								WHERE fc_id=".$liq_numero ;
				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}
			return $mensaje_final;
		}

	}

	function autorizar_nc ($nc_numero,$i_autorizar,$i_log=false){
		$check_sql = "SELECT sp_busca_parametro ('RUTA_FIRMADOR','http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $ruta_firmador={rs[0][0]};
		}
		$correo_enviado='';
		$procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
		$configApp = new \configAplicacion();
		$configCorreo = new \configCorreo();
		$notaCredito = new notaCredito();
		$check_sql = "SELECT sp_busca_parametro ('RUTA_EMPRESA','D:/Desarrollos/')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir={rs[0][0]};
		}
		$check_sql = "SELECT sp_busca_parametro ('RUTA_IREPORT','D:/Desarrollo/IReport')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir_ireport={rs[0][0]};
		}
		$check_sql="SELECT nc_empresa,nc_establecimiento FROM del_nota_credito WHERE nc_numero=".$nc_numero;
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
			$var_empresa={rs[0][0]};
			$var_establecimiento={rs[0][1]};	
		}
		$check_sql = "SELECT
							emp_ruc,
							emp_logo,
							emp_firma,
							emp_clave_firma,
							emp_razon_social,
							emp_nombre_comercial,
							emp_direccion_matriz,
							emp_obligado_contabilidad,
							emp_ambiente_sri,
							emp_tipo_emision,
							emp_contribuyente_especial,
							fil_id,
							csmtp_servidor,
							csmtp_contrasenia,
							csmtp_puerto,
							csmtp_usuario,
							emp_es_op_transporte,
							emp_regimen_especial,
							emp_agente_retencion,
							emp_calificacion_artesanal,
							csmtp_ruta_imagenes
						FROM
							v_del_datos_empresa_sri
						WHERE emp_ruc='".$var_empresa."'
						AND est_id=".$var_establecimiento.";";
		sc_lookup(rs_empresa, $check_sql);

		if (isset({rs_empresa[0][0]})){
			$configApp->dirAutorizados = $dir.{rs_empresa[0][0]}."/documentos/";
			$configApp->dirLogo =  $dir.{rs_empresa[0][0]}."/logo/".{rs_empresa[0][1]};
			$configApp->dirFirma =  $dir.{rs_empresa[0][0]}."/firma/".{rs_empresa[0][2]};
			$configApp->passFirma = {rs_empresa[0][3]};
			$configApp->dirIreport=$dir_ireport;
			$notaCredito->configAplicacion = $configApp;

			$configCorreo->correoAsunto = "Nueva Nota de Credito";
			$configCorreo->correoHost = {rs_empresa[0][12]};
			$configCorreo->correoPass = {rs_empresa[0][13]};
			$configCorreo->correoPort = {rs_empresa[0][14]};
			$configCorreo->correoRemitente = {rs_empresa[0][15]};
			$configCorreo->sslHabilitado = false;
			$configCorreo->rutaLogo={rs_empresa[0][20]}.'logo.jpg';
			$notaCredito->configCorreo = $configCorreo;

			$notaCredito->ruc = {rs_empresa[0][0]};
			$notaCredito->razonSocial = {rs_empresa[0][4]};
			$notaCredito->nombreComercial = {rs_empresa[0][5]}; 
			$notaCredito->dirMatriz = {rs_empresa[0][6]}; 
			$notaCredito->obligadoContabilidad ={rs_empresa[0][7]}; 
			$notaCredito->tipoEmision = {rs_empresa[0][9]};
			if ({rs_empresa[0][10]}!=''){
				$notaCredito->contribuyenteEspecial = {rs_empresa[0][10]};
			}
			$notaCredito->padronMicroempresa={rs_empresa[0][17]};
			$notaCredito->padronAgenteRetencion={rs_empresa[0][18]};
			if({rs_empresa[0][18]}=='S'){
				$check_sql = "SELECT sp_busca_parametro ('NUMERORESOAR','1')";
				sc_lookup(rs, $check_sql);
				if (isset({rs[0][0]})){
				   $notaCredito->numeroResolucion={rs[0][0]};
				}
			}
			$notaCredito->artesanoCalificado={rs_empresa[0][19]};

		}

		$check_sql="SELECT
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
					WHERE nc_numero=".$nc_numero;
		sc_lookup(rs_notaCredito, $check_sql);

		if (isset({rs_notaCredito[0][0]})){
			$correo_enviado={rs_notaCredito[0][37]};
			$notaCredito->padronRimpe={rs_notaCredito[0][35]};
			$notaCredito->leyendaRimpe={rs_notaCredito[0][36]};
			$notaCredito->ambiente = {rs_notaCredito[0][1]};
			$notaCredito->codDoc = {rs_notaCredito[0][2]};
			$notaCredito->fechaEmision = {rs_notaCredito[0][3]};
			$notaCredito->dirEstablecimiento = {rs_notaCredito[0][4]};
			$notaCredito->establecimiento = {rs_notaCredito[0][5]}; 
			$notaCredito->ptoEmision = {rs_notaCredito[0][6]}; 
			$notaCredito->secuencial = {rs_notaCredito[0][7]};
			$notaCredito->tipoIdentificacionComprador = {rs_notaCredito[0][8]};
			$notaCredito->razonSocialComprador = {rs_notaCredito[0][9]}; 
			$notaCredito->identificacionComprador = {rs_notaCredito[0][10]};
			$notaCredito->codDocModificado = {rs_notaCredito[0][11]};
			$notaCredito->numDocModificado = {rs_notaCredito[0][12]};
			$notaCredito->fechaEmisionDocSustento = {rs_notaCredito[0][13]};
			$notaCredito->totalSinImpuestos = {rs_notaCredito[0][14]}; 
			$notaCredito->totalDescuento = {rs_notaCredito[0][15]}; 
			$total_Impuestos=array();
			$i=0;
				if({rs_notaCredito[0][16]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '2'; 
					$totalImpuesto->baseImponible = {rs_notaCredito[0][16]}; 
					$totalImpuesto->valor = {rs_notaCredito[0][17]};
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_notaCredito[0][18]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '0'; 
					$totalImpuesto->baseImponible = {rs_notaCredito[0][18]}; 
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_notaCredito[0][19]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '6'; 
					$totalImpuesto->baseImponible = {rs_notaCredito[0][19]}; 
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_notaCredito[0][20]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo ='2'; 
					$totalImpuesto->codigoPorcentaje = '7'; 
					$totalImpuesto->baseImponible = {rs_notaCredito[0][20]}; 
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_notaCredito[0][21]}>0){	
					$check_sql="SELECT  '3' as impuesto,
										 dnc_porcentaje_ice,
										sum(dnc_base_ice),
										sum(dnc_valor_ice) 
								FROM del_detalle_nota_credito 
								WHERE dnc_porcentaje_ice<>'0' 
								and dnc_nota_credito=".$nc_numero."
								group by dnc_porcentaje_ice";
					sc_select(rs_impuesto, $check_sql); 
					if ({rs_impuesto}  === false){
						echo "Error al acceder a del_detalle_notaCredito";
					}else{
						while (!{rs_impuesto}->EOF){
							$totalImpuesto = new totalImpuesto();
							$totalImpuesto->codigo ={rs_impuesto}->fields[0]; 
							$totalImpuesto->codigoPorcentaje = {rs_impuesto}->fields[1]; 
							$totalImpuesto->baseImponible = {rs_impuesto}->fields[2];
							$totalImpuesto->valor = {rs_impuesto}->fields[3];
							$total_Impuestos[$i]=$totalImpuesto;
							$i+=1;
							{rs_impuesto}->MoveNext();
						}
						{rs_impuesto}->Close();
					}	
				}
				if({rs_notaCredito[0][22]}>0){	
					$check_sql="SELECT  '5' as impuesto,
										dnc_porcentaje_irbpnr,
										sum(dnc_base_irbpnr),
										sum(dnc_valor_irbpnr) 
								FROM del_detalle_nota_credito 
								WHERE dnc_porcentaje_irbpnr<>'0' 
								and dnc_nota_credito=".$nc_numero ."
								group by dnc_porcentaje_irbpnr";
					sc_select(rs_impuesto, $check_sql); 
					if ({rs_impuesto}  === false){
						echo "Error al acceder a del_detalle_notaCredito";
					}else{
						while (!{rs_impuesto}->EOF){
							$totalImpuesto = new totalImpuesto();
							$totalImpuesto->codigo ={rs_impuesto}->fields[0]; 
							$totalImpuesto->codigoPorcentaje = {rs_impuesto}->fields[1];
							$totalImpuesto->baseImponible = {rs_impuesto}->fields[2]; 
							$totalImpuesto->valor = {rs_impuesto}->fields[3];
							$total_Impuestos[$i]=$totalImpuesto;
							$i+=1;
							{rs_impuesto}->MoveNext();
						}
						{rs_impuesto}->Close();
					}	
				}
			$notaCredito->totalConImpuesto = $total_Impuestos;
			$notaCredito->valorModificacion = {rs_notaCredito[0][23]};
			$notaCredito->motivo = {rs_notaCredito[0][24]};
			$notaCredito->moneda = {rs_notaCredito[0][25]};

			//aqui van los detalles
			$check_sql="SELECT
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
							where dnc_nota_credito=".$nc_numero."
							order by dnc_id" ;

			sc_select(rs_detalles, $check_sql);   
			if ({rs_detalles}  === false){
				echo "Error al acceder al detalle de la notaCredito";
			}else{
				$detalles_notaCredito = array();
				$i=0;
				while (!{rs_detalles}->EOF){
					$detallenotaCredito = new detalleNotaCredito();
					$detallenotaCredito->codigoInterno = {rs_detalles}->fields[1];
					$detallenotaCredito->codigoAdicional = {rs_detalles}->fields[2]; 
					$detallenotaCredito->descripcion = {rs_detalles}->fields[3]; 
					$detallenotaCredito->cantidad = {rs_detalles}->fields[4]; 
					$detallenotaCredito->precioUnitario = {rs_detalles}->fields[5]; 
					$detallenotaCredito->descuento = {rs_detalles}->fields[6]; 
					$detallenotaCredito->precioTotalSinImpuesto = {rs_detalles}->fields[7]; 
					if ({rs_detalles}->fields[23] <>""){
						$informacion_adicional=array();
						$detalle_adicional= new detalleAdicional();
						$detalle_adicional->nombre = 'Adicional';
						$detalle_adicional->valor = {rs_detalles}->fields[23] ;
						$informacion_adicional[0]=$detalle_adicional;							 
						$detallenotaCredito->detallesAdicionales = $informacion_adicional;	
					}
						$impuestos_det=array();
						$j=0;
						$impuesto = new impuesto();
						$impuesto->codigo = {rs_detalles}->fields[8];
						$impuesto->codigoPorcentaje = {rs_detalles}->fields[9]; 
						$impuesto->tarifa = {rs_detalles}->fields[10]; 
						$impuesto->baseImponible = {rs_detalles}->fields[11]; 
						$impuesto->valor = {rs_detalles}->fields[12];
						$impuestos_det[$j]=$impuesto;
						$j+=1;
						if({rs_detalles}->fields[14]<>'0'){
							$impuesto = new impuesto();
							$impuesto->codigo = {rs_detalles}->fields[13];
							$impuesto->codigoPorcentaje = {rs_detalles}->fields[14]; 
							$impuesto->tarifa = {rs_detalles}->fields[16];  
							$impuesto->baseImponible = {rs_detalles}->fields[15]; 
							$impuesto->valor = {rs_detalles}->fields[17];
							$impuestos_det[$j]=$impuesto;
							$j+=1;
						}
						if({rs_detalles}->fields[19]<>'0'){
							$impuesto = new impuesto();
							$impuesto->codigo = {rs_detalles}->fields[18];
							$impuesto->codigoPorcentaje = {rs_detalles}->fields[19]; 
							$impuesto->tarifa = {rs_detalles}->fields[20]; // 
							$impuesto->baseImponible = {rs_detalles}->fields[21]; 
							$impuesto->valor = {rs_detalles}->fields[22];
							$impuestos_det[$j]=$impuesto;
							$j+=1;
						}
					$detallenotaCredito->impuestos = $impuestos_det;
					$detalles_notaCredito[$i]=$detallenotaCredito;
					{rs_detalles}->MoveNext();
					$i+=1;
				}
				{rs_detalles}->Close();
				$notaCredito->detalles = $detalles_notaCredito;
			}

			$camposAdicionales = array();
			$i=0;

			if({rs_empresa[0][16]}=='S'){
				if({rs_notaCredito[0][6]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Punto Emision";
					$campoAdicional->valor = {rs_notaCredito[0][6]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
				if({rs_notaCredito[0][29]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Razon Social";
					$campoAdicional->valor = {rs_notaCredito[0][29]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
				if({rs_notaCredito[0][30]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Ruc";
					$campoAdicional->valor = {rs_notaCredito[0][30]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
				if({rs_notaCredito[0][31]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Telefono Socio";
					$campoAdicional->valor = {rs_notaCredito[0][31]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
				if({rs_notaCredito[0][33]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Placa";
					$campoAdicional->valor = {rs_notaCredito[0][33]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
				if({rs_notaCredito[0][34]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Contribuyente";
					$campoAdicional->valor = {rs_notaCredito[0][34]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}

			}


			if({rs_empresa[0][19]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "artesanoCalificado";
				$campoAdicional->valor = 'Nro. '.{rs_empresa[0][19]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}

			if({rs_notaCredito[0][28]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Telefono";
				$campoAdicional->valor = {rs_notaCredito[0][28]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			if({rs_notaCredito[0][27]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Email";
				$campoAdicional->valor = {rs_notaCredito[0][27]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}

			if({rs_notaCredito[0][26]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Direccion";
				$campoAdicional->valor = {rs_notaCredito[0][26]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}

			$notaCredito->infoAdicional = $camposAdicionales;

			$procesarComprobante = new procesarComprobante();
			$procesarComprobante->comprobante = $notaCredito;
			$procesarComprobante->envioSRI = false; //El sistema si es true //1-Crea XML en el directorio de autorizado 
			$res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
			if($i_log){
				var_dump($notaCredito);
				echo '<br>';
				var_dump($res);
				echo '<br>';
			}	
			if($i_autorizar=='S'){
				if ($res->return->estadoComprobante == "FIRMADO") {
					$procesarComprobante = new procesarComprobante();
					$procesarComprobante->comprobante = $notaCredito;
					$procesarComprobante->envioSRI = true; //El sistema si es false 
					$res=$procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
					if($i_log){
						var_dump($res);
						echo '<br>';
					}	
				}else{
					if($res->return->estadoComprobante == "PROCESANDOSE"){
						$comprobantePendiente = new \comprobantePendiente();
						$comprobantePendiente->configAplicacion = $configApp;
						$comprobantePendiente->configCorreo = $configCorreo;
						$comprobantePendiente->ambiente = {rs_notaCredito[0][1]};
						$comprobantePendiente->codDoc = {rs_notaCredito[0][2]};
						$comprobantePendiente->establecimiento = {rs_notaCredito[0][5]};
						$comprobantePendiente->fechaEmision = {rs_notaCredito[0][3]};
						$comprobantePendiente->ptoEmision = {rs_notaCredito[0][6]};
						$comprobantePendiente->ruc = {rs_empresa[0][0]};
						$comprobantePendiente->secuencial = {rs_notaCredito[0][7]};
						$comprobantePendiente->tipoEmision = {rs_empresa[0][9]};
						$comprobantePendiente->padronMicroempresa = {rs_empresa[0][17]};
						$comprobantePendiente->padronAgenteRetencion = {rs_empresa[0][18]};
						$comprobantePendiente->padronRimpe = {rs_empresa[0][21]};
						$procesarComprobantePendiente = new \procesarComprobantePendiente();
						$procesarComprobantePendiente->comprobantePendiente = $comprobantePendiente;
						$res = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
						if ($res->return->estadoComprobante == "PROCESANDOSE") {
							$res->return->estadoComprobante = "ERROR";
						}
					}	
				}
			}

			$mensaje_final=	$res->return->estadoComprobante."<br>";
			//echo $res->return->estadoComprobante;
			if ($res->return->estadoComprobante == 'ERROR'){
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}

			if ($res->return->estadoComprobante == "FIRMADO") {
				$update_sql =  "UPDATE del_nota_credito 
								SET  nc_estado_sri='".$res->return->estadoComprobante."'
								WHERE nc_numero=".$nc_numero ;
				sc_exec_sql($update_sql);
			}
			if($res->return->estadoComprobante=='AUTORIZADO'){
				if($correo_enviado=="NO"){
						if(enviarCorreo($notaCredito,{rs_notaCredito[0][27]})){
							$correo_enviado="SI";
						}else{
							$correo_enviado="NO";
						}
					}
				
				$update_sql = "UPDATE del_nota_credito 
								SET  nc_estado_sri='".$res->return->estadoComprobante."',
									 nc_clave='".$res->return->claveAcceso."',   
									 nc_autorizacion='".$res->return->numeroAutorizacion."',
									 nc_fecha_autorizacion='".$res->return->fechaAutorizacion."',
									 nc_error_sri='',
									 nc_correo_enviado='".$correo_enviado."'
								WHERE nc_numero=".$nc_numero ;
				sc_exec_sql($update_sql);
			}
			if($res->return->estadoComprobante=='DEVUELTA'){
				if($res->return->mensajes->mensaje=='CLAVE ACCESO REGISTRADA'){
					if($correo_enviado=="NO"){
						if(enviarCorreo($notaCredito,{rs_notaCredito[0][27]})){
							$correo_enviado="SI";
						}else{
							$correo_enviado="NO";
						}
					}
					$update_sql = "UPDATE del_nota_credito 
								SET  nc_estado_sri='AUTORIZADO',
									 nc_clave='".$res->return->claveAcceso."',   
									 nc_autorizacion='".$res->return->claveAcceso."',
									 nc_correo_enviado='".$correo_enviado."'
								WHERE nc_numero=".$nc_numero ;
				}else{
					$update_sql = "UPDATE del_nota_credito 
								SET  nc_estado_sri='".$res->return->estadoComprobante."',
									 nc_clave='".$res->return->claveAcceso."',   
									 nc_error_sri='".$res->return->mensajes->mensaje."'
								WHERE nc_numero=".$nc_numero ;
				}


				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}
			if($res->return->estadoComprobante=='NO AUTORIZADO'){
				$update_sql = "UPDATE del_nota_credito 
								SET  nc_estado_sri='".$res->return->estadoComprobante."',
									 nc_clave='".$res->return->claveAcceso."',   
									 nc_error_sri='".$res->return->mensajes->mensaje."',
									 nc_fecha_autorizacion='".$res->return->fechaAutorizacion."'
								WHERE nc_numero=".$nc_numero ;
				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}

			return $mensaje_final;
		}
	}

	function autorizar_guia	($gr_numero,$i_autorizar,$i_log=false){
		$check_sql = "SELECT sp_busca_parametro ('RUTA_FIRMADOR','http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $ruta_firmador={rs[0][0]};
		}
		$correo_enviado='';
		$procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
		$configApp = new \configAplicacion();
		$configCorreo = new \configCorreo();
		$guiaRemision = new guiaRemision();
		$check_sql = "SELECT sp_busca_parametro ('RUTA_EMPRESA','D:/Desarrollos/')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir={rs[0][0]};
		}
		$check_sql = "SELECT sp_busca_parametro ('RUTA_IREPORT','D:/Desarrollo/IReport')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir_ireport={rs[0][0]};
		}
		$check_sql="SELECT gr_empresa,gr_establecimiento FROM del_guia_remision WHERE gr_numero=".$gr_numero;
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
			$var_empresa={rs[0][0]};
			$var_establecimiento={rs[0][1]};	
		}
		$check_sql = "SELECT
							emp_ruc,
							emp_logo,
							emp_firma,
							emp_clave_firma,
							emp_razon_social,
							emp_nombre_comercial,
							emp_direccion_matriz,
							emp_obligado_contabilidad,
							emp_ambiente_sri,
							emp_tipo_emision,
							emp_contribuyente_especial,
							fil_id,
							csmtp_servidor,
							csmtp_contrasenia,
							csmtp_puerto,
							csmtp_usuario,
							emp_es_op_transporte,
							emp_regimen_especial,
							emp_agente_retencion,
							emp_calificacion_artesanal,
							csmtp_ruta_imagenes
						FROM
							v_del_datos_empresa_sri
						WHERE emp_ruc='".$var_empresa."'
						AND est_id=".$var_establecimiento.";";
		sc_lookup(rs_empresa, $check_sql);

		if (isset({rs_empresa[0][0]})){
			$configApp->dirAutorizados = $dir.{rs_empresa[0][0]}."/documentos/";
			$configApp->dirLogo =  $dir.{rs_empresa[0][0]}."/logo/".{rs_empresa[0][1]};
			$configApp->dirFirma =  $dir.{rs_empresa[0][0]}."/firma/".{rs_empresa[0][2]};
			$configApp->passFirma = {rs_empresa[0][3]};
			$configApp->dirIreport=$dir_ireport;
			$guiaRemision->configAplicacion = $configApp;

			$configCorreo->correoAsunto = "Nueva Guia de Remision";
			$configCorreo->correoHost = {rs_empresa[0][12]};
			$configCorreo->correoPass = {rs_empresa[0][13]};
			$configCorreo->correoPort = {rs_empresa[0][14]};
			$configCorreo->correoRemitente = {rs_empresa[0][15]};
			$configCorreo->sslHabilitado = false;
			$configCorreo->rutaLogo={rs_empresa[0][20]}.'logo.jpg';
			$guiaRemision->configCorreo = $configCorreo;

			$guiaRemision->ruc = {rs_empresa[0][0]};
			$guiaRemision->razonSocial = {rs_empresa[0][4]};
			$guiaRemision->nombreComercial = {rs_empresa[0][5]}; 
			$guiaRemision->dirMatriz = {rs_empresa[0][6]}; 
			$guiaRemision->obligadoContabilidad ={rs_empresa[0][7]}; 
			$guiaRemision->tipoEmision = {rs_empresa[0][9]};
			if ({rs_empresa[0][10]}!=''){
				$guiaRemision->contribuyenteEspecial = {rs_empresa[0][10]};
			}
			$guiaRemision->padronMicroempresa={rs_empresa[0][17]};
			$guiaRemision->padronAgenteRetencion={rs_empresa[0][18]};
			if({rs_empresa[0][18]}=='S'){
				$check_sql = "SELECT sp_busca_parametro ('NUMERORESOAR','1')";
				sc_lookup(rs, $check_sql);
				if (isset({rs[0][0]})){
				   $guiaRemision->numeroResolucion={rs[0][0]};
				}
			}
			$guiaRemision->artesanoCalificado={rs_empresa[0][19]};

		}

		$check_sql="SELECT  gr_numero,
							gr_ambiente,
							gr_tipo_comprobante,
							est_codigo,
							pen_serie,
							gr_secuencial,
							est_direccion,
							gr_direccion_partida,
							tr_nombre,
							tr_tipo_identificacion,
							tr_identificacion,
							fecha_inicio,
							fecha_fin,
							tr_placa,
							tr_email,
							est_padronrimpe,
							est_leyenda_rimpe,
							gr_correo_enviado
					FROM v_del_datos_guia_remision
					WHERE gr_numero=".$gr_numero;
		sc_lookup(rs_guiaRemision, $check_sql);

		if (isset({rs_guiaRemision[0][0]})){
			$correo_enviado={rs_guiaRemision[0][17]};
			$guiaRemision->padronRimpe={rs_guiaRemision[0][15]};
			$guiaRemision->leyendaRimpe={rs_guiaRemision[0][16]};
			$guiaRemision->ambiente = {rs_guiaRemision[0][1]};
			$guiaRemision->codDoc = {rs_guiaRemision[0][2]};
			$guiaRemision->establecimiento = {rs_guiaRemision[0][3]};
			$guiaRemision->ptoEmision = {rs_guiaRemision[0][4]}; 
			$guiaRemision->secuencial = {rs_guiaRemision[0][5]};
			$guiaRemision->dirEstablecimiento = {rs_guiaRemision[0][6]};
			$guiaRemision->dirPartida = {rs_guiaRemision[0][7]};
			$guiaRemision->razonSocialTransportista = {rs_guiaRemision[0][8]};
			$guiaRemision->tipoIdentificacionTransportista = {rs_guiaRemision[0][9]};
			$guiaRemision->rucTransportista ={rs_guiaRemision[0][10]};
			$guiaRemision->rise = "RISE";
			$guiaRemision->fechaIniTransporte = {rs_guiaRemision[0][11]};
			$guiaRemision->fechaFinTransporte = {rs_guiaRemision[0][12]};
			$guiaRemision->placa = {rs_guiaRemision[0][13]};

			//aqui van los destinatarios
			$check_sql="SELECT dg_guia,
								dg_identificacion_destinatario,
								dg_nombre_destinatario,
								dg_direccion_destino,
								dg_motivo_traslado,
								dg_documento_aduanero,
								coalesce(est_codigo,''),
								dg_ruta,
								dg_coddoc_sustento,
								dg_documento_sustento,
								dg_autorizacion_sustento,
								coalesce(dg_fecha_sustento,''),
								dg_id
							FROM v_del_destinatario_guia_sri
						where dg_guia=".$gr_numero."
						order by dg_id" ;

			sc_select(rs_destinatarios, $check_sql);   
			if ({rs_destinatarios}  === false){
				echo "Error al acceder al detalle de la guiaRemision";
			}else{
				$destinatarios_guiaRemision = array();
				$i=0;
				while (!{rs_destinatarios}->EOF){
					$destinatario = new Destinatario();
					$destinatario->identificacionDestinatario = {rs_destinatarios}->fields[1];
					$destinatario->razonSocialDestinatario = {rs_destinatarios}->fields[2];
					$destinatario->dirDestinatario ={rs_destinatarios}->fields[3];
					$destinatario->motivoTraslado = {rs_destinatarios}->fields[4];
					$destinatario->docAduaneroUnico = {rs_destinatarios}->fields[5];
					$destinatario->codEstabDestino = {rs_destinatarios}->fields[6];
					$destinatario->ruta ={rs_destinatarios}->fields[7];
					$destinatario->codDocSustento = {rs_destinatarios}->fields[8];
					$destinatario->numDocSustento = {rs_destinatarios}->fields[9];
					$destinatario->numAutDocSustento = {rs_destinatarios}->fields[10];
					$destinatario->fechaEmisionDocSustento = {rs_destinatarios}->fields[11];

					$check_sql="SELECT 
										dgd_destinatario,
										pro_codigo,
										pro_codigo_aux,
										pro_descripcion,
										dgd_cantidad 
								FROM v_del_productos_destinatario_guia_sri
								WHERE dgd_destinatario=".{rs_destinatarios}->fields[12]."
								order by dgd_id";

					sc_select(rs_detalles, $check_sql);  

					if ({rs_detalles}  === false){
						echo "Error al acceder al detalle de la guiaRemision";
					}else{
						$detalles = array();
						$j=0;
						while (!{rs_detalles}->EOF){
							$detalle = new DetalleGuiaRemision();
							$detalle->codigoInterno = {rs_detalles}->fields[1];
							$detalle->codigoAdicional = {rs_detalles}->fields[2];
							$detalle->descripcion = {rs_detalles}->fields[3];
							$detalle->cantidad = {rs_detalles}->fields[4];
							$detalles[$j] = $detalle;
							$j+=1;
							{rs_detalles}->MoveNext();
						}	
						$destinatario->detalles = $detalles;
					}
					$destinatarios_guiaRemision[$i]=$destinatario;
					{rs_destinatarios}->MoveNext();
					$i+=1;
				}
				{rs_destinatarios}->Close();
				$guiaRemision->destinatarios = $destinatarios_guiaRemision;
			}

			$camposAdicionales = array();
			$campoAdicional = new campoAdicional();
			$campoAdicional->nombre = "Email";
			$campoAdicional->valor = {rs_guiaRemision[0][14]};
			$camposAdicionales[0] = $campoAdicional;
			$campoAdicional = new campoAdicional();
			$campoAdicional->nombre = "Direccion";
			$campoAdicional->valor = {rs_empresa[0][6]};
			$camposAdicionales[1] = $campoAdicional;
			$guiaRemision->infoAdicional = $camposAdicionales;

			$procesarComprobante = new procesarComprobante();
			$procesarComprobante->comprobante = $guiaRemision;
			$procesarComprobante->envioSRI = false; //El sistema si es true //1-Crea XML en el directorio de autorizado 
			$res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
			if($i_log){
				var_dump($guiaRemision);
				echo '<br>';
				var_dump($res);
				echo '<br>';
			}
			if($i_autorizar=='S'){
				if ($res->return->estadoComprobante == "FIRMADO") {
					$procesarComprobante = new procesarComprobante();
					$procesarComprobante->comprobante = $guiaRemision;
					$procesarComprobante->envioSRI = true; //El sistema si es false 
					$res=$procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
					if($i_log){
						var_dump($res);
						echo '<br>';
					}	
				}else{
					if($res->return->estadoComprobante == "PROCESANDOSE"){
						$comprobantePendiente = new \comprobantePendiente();
						$comprobantePendiente->configAplicacion = $configApp;
						$comprobantePendiente->configCorreo = $configCorreo;
						$comprobantePendiente->ambiente = {rs_guiaRemision[0][1]};
						$comprobantePendiente->codDoc = {rs_guiaRemision[0][2]};
						$comprobantePendiente->establecimiento = {rs_guiaRemision[0][5]};
						$comprobantePendiente->fechaEmision = {rs_guiaRemision[0][3]};
						$comprobantePendiente->ptoEmision = {rs_guiaRemision[0][6]};
						$comprobantePendiente->ruc = {rs_empresa[0][0]};
						$comprobantePendiente->secuencial = {rs_guiaRemision[0][7]};
						$comprobantePendiente->tipoEmision = {rs_empresa[0][9]};
						$comprobantePendiente->padronMicroempresa = {rs_empresa[0][17]};
						$comprobantePendiente->padronAgenteRetencion = {rs_empresa[0][18]};
						$comprobantePendiente->padronRimpe = {rs_empresa[0][21]};
						$procesarComprobantePendiente = new \procesarComprobantePendiente();
						$procesarComprobantePendiente->comprobantePendiente = $comprobantePendiente;
						$res = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
						if ($res->return->estadoComprobante == "PROCESANDOSE") {
							$res->return->estadoComprobante = "ERROR";
						}
					}	
				}
			}

			$mensaje_final=	$res->return->estadoComprobante."<br>";
			//echo $res->return->estadoComprobante;
			if ($res->return->estadoComprobante == 'ERROR'){
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}

			if ($res->return->estadoComprobante == "FIRMADO") {
				$update_sql =  "UPDATE del_guia_remision 
								SET  gr_estado_sri='".$res->return->estadoComprobante."'
								WHERE gr_numero=".$gr_numero ;
				sc_exec_sql($update_sql);
			}

			if($res->return->estadoComprobante=='AUTORIZADO'){
				if($correo_enviado=="NO"){
					if(enviarCorreo($guiaRemision,{rs_guiaRemision[0][14]})){
						$correo_enviado="SI";
					}else{
						$correo_enviado="NO";
					}
				}
				
				$update_sql = "UPDATE del_guia_remision 
								SET  gr_estado_sri='".$res->return->estadoComprobante."',
									 gr_clave='".$res->return->claveAcceso."',   
									 gr_autorizacion='".$res->return->numeroAutorizacion."',
									 gr_fecha_autorizacion='".$res->return->fechaAutorizacion."',
									 gr_error_sri='',
									 gr_correo_enviado='".$correo_enviado."'
								WHERE gr_numero=".$gr_numero ;
				sc_exec_sql($update_sql);
			}
			if($res->return->estadoComprobante=='DEVUELTA'){
				if($res->return->mensajes->mensaje=='CLAVE ACCESO REGISTRADA'){
					if($correo_enviado=="NO"){
						if(enviarCorreo($guiaRemision,{rs_guiaRemision[0][14]})){
							$correo_enviado="SI";
						}else{
							$correo_enviado="NO";
						}
					}
					$update_sql = "UPDATE del_guia_remision 
								SET  gr_estado_sri='AUTORIZADO',
									 gr_clave='".$res->return->claveAcceso."',   
									 gr_autorizacion='".$res->return->claveAcceso."',
									 gr_correo_enviado='".$correo_enviado."'
								WHERE gr_numero=".$gr_numero ;
				}else{
					$update_sql = "UPDATE del_guia_remision 
								SET  gr_estado_sri='".$res->return->estadoComprobante."',
									 gr_clave='".$res->return->claveAcceso."',   
									 gr_error_sri='".$res->return->mensajes->mensaje."'
								WHERE gr_numero=".$gr_numero ;
				}


				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}
			if($res->return->estadoComprobante=='NO AUTORIZADO'){
				$update_sql = "UPDATE del_guia_remision 
								SET  gr_estado_sri='".$res->return->estadoComprobante."',
									 gr_clave='".$res->return->claveAcceso."',   
									 gr_error_sri='".$res->return->mensajes->mensaje."',
									 gr_fecha_autorizacion='".$res->return->fechaAutorizacion."'
								WHERE gr_numero=".$gr_numero ;
				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}

			return $mensaje_final;
		}

	}

	function autorizar_retencion ($ret_numero,$i_autorizar,$i_log=false){
		$check_sql = "SELECT sp_busca_parametro ('RUTA_FIRMADOR','http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $ruta_firmador={rs[0][0]};
		}
		$correo_enviado="";
		$procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
		$configApp = new \configAplicacion();
		$configCorreo = new \configCorreo();
		$retencion = new comprobanteRetencion();
		$check_sql = "SELECT sp_busca_parametro ('RUTA_EMPRESA','E:/Desarrollos/')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir={rs[0][0]};
		}
		$check_sql = "SELECT sp_busca_parametro ('RUTA_IREPORT','D:/Desarrollo/IReport')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir_ireport={rs[0][0]};
		}
		$check_sql="SELECT ret_empresa,ret_establecimiento FROM del_retencion WHERE ret_numero=".$ret_numero;
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
			$var_empresa={rs[0][0]};
			$var_establecimiento={rs[0][1]};	
		}
		$check_sql = "SELECT
							emp_ruc,
							emp_logo,
							emp_firma,
							emp_clave_firma,
							emp_razon_social,
							emp_nombre_comercial,
							emp_direccion_matriz,
							emp_obligado_contabilidad,
							emp_ambiente_sri,
							emp_tipo_emision,
							emp_contribuyente_especial,
							fil_id,
							csmtp_servidor,
							csmtp_contrasenia,
							csmtp_puerto,
							csmtp_usuario,
							emp_es_op_transporte,
							emp_regimen_especial,
							emp_agente_retencion,
							emp_calificacion_artesanal,
							csmtp_ruta_imagenes
						FROM
							v_del_datos_empresa_sri
						WHERE emp_ruc='".$var_empresa."'
						AND est_id=".$var_establecimiento.";";
		sc_lookup(rs_empresa, $check_sql);

		if (isset({rs_empresa[0][0]})){
			$configApp->dirAutorizados = $dir.{rs_empresa[0][0]}."/documentos/";
			$configApp->dirLogo =  $dir.{rs_empresa[0][0]}."/logo/".{rs_empresa[0][1]};
			$configApp->dirFirma =  $dir.{rs_empresa[0][0]}."/firma/".{rs_empresa[0][2]};
			$configApp->passFirma = {rs_empresa[0][3]};
			$configApp->dirIreport=$dir_ireport;
			$retencion->configAplicacion = $configApp;

			$configCorreo->correoAsunto = "Nueva Retencion";
			$configCorreo->correoHost = {rs_empresa[0][12]};
			$configCorreo->correoPass = {rs_empresa[0][13]};
			$configCorreo->correoPort = {rs_empresa[0][14]};
			$configCorreo->correoRemitente = {rs_empresa[0][15]};
			$configCorreo->sslHabilitado = false;
			$configCorreo->rutaLogo={rs_empresa[0][20]}.'logo.jpg';
			$retencion->configCorreo = $configCorreo;

			$retencion->ruc = {rs_empresa[0][0]};
			$retencion->razonSocial = {rs_empresa[0][4]};
			$retencion->nombreComercial = {rs_empresa[0][5]}; 
			$retencion->dirMatriz = {rs_empresa[0][6]}; 
			$retencion->obligadoContabilidad ={rs_empresa[0][7]}; 
			$retencion->tipoEmision = {rs_empresa[0][9]};
			if ({rs_empresa[0][10]}!=''){
				$retencion->contribuyenteEspecial = {rs_empresa[0][10]};
			}	
			$retencion->padronMicroempresa={rs_empresa[0][17]};
			$retencion->padronAgenteRetencion={rs_empresa[0][18]};
			if({rs_empresa[0][18]}=='S'){
				$check_sql = "SELECT sp_busca_parametro ('NUMERORESOAR','1')";
				sc_lookup(rs, $check_sql);
				if (isset({rs[0][0]})){
				   $retencion->numeroResolucion={rs[0][0]};
				}
			}
			$retencion->artesanoCalificado={rs_empresa[0][19]};

		}
		//cargamos datos de la factura generada
		$check_sql="SELECT	ret_numero,
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
							ret_correo_enviado
						FROM
							v_del_datos_retencion_sri
						WHERE ret_numero=".$ret_numero;
		sc_lookup(rs_retencion, $check_sql);

		if (isset({rs_retencion[0][0]})){
			$correo_enviado={rs_retencion[0][18]};
			$retencion->padronRimpe={rs_retencion[0][16]};
			$retencion->leyendaRimpe={rs_retencion[0][17]};
			$retencion->ambiente = {rs_retencion[0][1]};
			$retencion->codDoc = {rs_retencion[0][2]};
			$retencion->fechaEmision = {rs_retencion[0][3]};
			$retencion->establecimiento = {rs_retencion[0][4]};
			$retencion->ptoEmision = {rs_retencion[0][5]};
			$retencion->secuencial = {rs_retencion[0][6]};
			$retencion->dirEstablecimiento = {rs_retencion[0][7]};
			$retencion->tipoIdentificacionSujetoRetenido = {rs_retencion[0][8]};
			$retencion->razonSocialSujetoRetenido = {rs_retencion[0][9]};
			$retencion->identificacionSujetoRetenido = {rs_retencion[0][10]};
			$retencion->periodoFiscal = {rs_retencion[0][11]};
			$camposAdicionales = array();
			$i=0;

			if({rs_empresa[0][19]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "artesanoCalificado";
				$campoAdicional->valor = 'Nro. '.{rs_empresa[0][19]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			if ({rs_retencion[0][14]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Telefono";
				$campoAdicional->valor = {rs_retencion[0][14]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}

			if ({rs_retencion[0][13]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Email";
				$campoAdicional->valor = {rs_retencion[0][13]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}	

			if ({rs_retencion[0][12]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Direccion";
				$campoAdicional->valor = {rs_retencion[0][12]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}	

			if ({rs_retencion[0][15]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Comentario";
				$campoAdicional->valor = {rs_retencion[0][15]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}	
			$retencion->infoAdicional = $camposAdicionales;

			$check_sql="SELECT	dr_retencion,
								dr_impuesto,
								pri_codigo,
								dr_base_imponible,
								dr_porcentaje_retencion,
								dr_valor_retenido,
								dr_cod_doc_sustento,
								doc_sustento,
								fecha 
							FROM
								v_del_detalle_retencion_sri
							where dr_retencion=".$ret_numero."
							order by dr_id";
			sc_select(rs_detalles, $check_sql);
			if ({rs_detalles} === false){
				echo "error al acceder a los detalles";
			}else{
				$impuestos = array();
				$i=0;
				while (!{rs_detalles}->EOF){
					$impuesto = new impuestoComprobanteRetencion();
					$impuesto->codigo = {rs_detalles}->fields[1];
					$impuesto->codigoRetencion = {rs_detalles}->fields[2];
					$impuesto->baseImponible = {rs_detalles}->fields[3];
					$impuesto->porcentajeRetener = {rs_detalles}->fields[4];
					$impuesto->valorRetenido = {rs_detalles}->fields[5];
					$impuesto->codDocSustento = {rs_detalles}->fields[6];
					$impuesto->numDocSustento = {rs_detalles}->fields[7];
					$impuesto->fechaEmisionDocSustento = {rs_detalles}->fields[8];
					$impuestos[$i] = $impuesto;
					{rs_detalles}->MoveNext();
					$i+=1;
				}
				{rs_detalles}->Close();
				$retencion->impuestos = $impuestos;
			}	
			$procesarComprobante = new procesarComprobante();
			$procesarComprobante->comprobante = $retencion;
			$procesarComprobante->envioSRI = false; 
			$res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
			if($i_log){
				var_dump($retencion);
				echo '<br>';
				var_dump($res);
				echo '<br>';
			}

			if($i_autorizar=='S'){
				if ($res->return->estadoComprobante == "FIRMADO") {
					$procesarComprobante = new procesarComprobante();
					$procesarComprobante->comprobante = $retencion;
					$procesarComprobante->envioSRI = true; 
					$res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
					if($i_log){
						var_dump($res);
						echo "<br>";
					}	
				}else{
					if($res->return->estadoComprobante == "PROCESANDOSE"){
						$retencionPendiente = new \comprobantePendiente();
						$retencionPendiente->configAplicacion = $configApp;
						$retencionPendiente->configCorreo = $configCorreo;
						$retencionPendiente->ambiente = {rs_retencion[0][1]};
						$retencionPendiente->codDoc = {rs_retencion[0][2]};
						$retencionPendiente->establecimiento = {rs_retencion[0][4]};
						$retencionPendiente->fechaEmision = {rs_retencion[0][3]};
						$retencionPendiente->ptoEmision = {rs_retencion[0][5]};
						$retencionPendiente->ruc = {rs_empresa[0][0]};
						$retencionPendiente->secuencial = {rs_retencion[0][6]};
						$retencionPendiente->tipoEmision = {rs_empresa[0][9]};
						$comprobantePendiente->padronMicroempresa = {rs_empresa[0][17]};
						$comprobantePendiente->padronAgenteRetencion = {rs_empresa[0][18]};
						$comprobantePendiente->padronRimpe = {rs_empresa[0][21]};
						$procesarComprobantePendiente = new \procesarComprobantePendiente();
						$procesarComprobantePendiente->comprobantePendiente = $retencionPendiente;
						$res = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
						//var_dump($res);
						//echo "<br>";
						if ($res->return->estadoComprobante == "PROCESANDOSE") {
							$res->return->estadoComprobante = "ERROR";
						}
					}	
				}
			}	
			$mensaje_final=	$res->return->estadoComprobante."<br>";
			if ($res->return->estadoComprobante == 'ERROR'){
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}

			if ($res->return->estadoComprobante == "FIRMADO") {
					$update_sql =  "UPDATE  del_retencion
									SET  ret_estado_sri='".$res->return->estadoComprobante."'
									WHERE ret_numero=".$ret_numero ;
					sc_exec_sql($update_sql);
				}
			if($res->return->estadoComprobante=='AUTORIZADO'){
				if($correo_enviado=="NO"){
					if(enviarCorreo($retencion,{rs_retencion[0][13]})){
						$correo_enviado="SI";
					}else{
						$correo_enviado="NO";
					}
				}
				
				$update_sql = "UPDATE del_retencion 
								SET  ret_estado_sri='".$res->return->estadoComprobante."',
									 ret_clave='".$res->return->claveAcceso."',   
									 ret_autorizacion='".$res->return->numeroAutorizacion."',
									 ret_fecha_autorizacion='".$res->return->fechaAutorizacion."',
									 ret_correo_enviado='".$correo_enviado."'
								WHERE ret_numero=".$ret_numero ;
				sc_exec_sql($update_sql);
			}
			if($res->return->estadoComprobante=='DEVUELTA'){
				if($res->return->mensajes->mensaje=='CLAVE ACCESO REGISTRADA'){
					if($correo_enviado=="NO"){
						if(enviarCorreo($retencion,{rs_retencion[0][13]})){
							$correo_enviado="SI";
						}else{
							$correo_enviado="NO";
						}
					}
					$update_sql = "UPDATE del_retencion 
								SET  ret_estado_sri='AUTORIZADO',
									 ret_clave='".$res->return->claveAcceso."',   
									 ret_autorizacion='".$res->return->claveAcceso."',
									 ret_correo_enviado='".$correo_enviado."'
								WHERE ret_numero=".$ret_numero ;
				}else{
					$update_sql = "UPDATE del_retencion 
								SET  ret_estado_sri='".$res->return->estadoComprobante."',
									 ret_clave='".$res->return->claveAcceso."',   
									 ret_error_sri='".$res->return->mensajes->mensaje."'
								WHERE ret_numero=".$ret_numero ;
				}


				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}
			if($res->return->estadoComprobante=='NO AUTORIZADO'){
				$update_sql = "UPDATE del_retencion 
								SET  ret_estado_sri='".$res->return->estadoComprobante."',
									 ret_clave='".$res->return->claveAcceso."',   
									 ret_error_sri='".$res->return->mensajes->mensaje."',
									 ret_fecha_autorizacion='".$res->return->fechaAutorizacion."'
								WHERE ret_numero=".$ret_numero ;
				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}
			return $mensaje_final;
		}
	}

	function autorizar_retencion_dos ($ret_numero,$i_autorizar,$i_log=false){
		$check_sql = "SELECT sp_busca_parametro ('RUTA_FIRMADOR','http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $ruta_firmador={rs[0][0]};
		}
		$correo_enviado="";
		$procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
		$configApp = new \configAplicacion();
		$configCorreo = new \configCorreo();
		$retencion = new comprobanteRetencionDos();
		$check_sql = "SELECT sp_busca_parametro ('RUTA_EMPRESA','E:/Desarrollos/')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir={rs[0][0]};
		}
		$check_sql = "SELECT sp_busca_parametro ('RUTA_IREPORT','D:/Desarrollo/IReport')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir_ireport={rs[0][0]};
		}
		$check_sql="SELECT ret_empresa,ret_establecimiento FROM del_retencion WHERE ret_numero=".$ret_numero;
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
			$var_empresa={rs[0][0]};
			$var_establecimiento={rs[0][1]};	
		}
		$check_sql = "SELECT
							emp_ruc,
							emp_logo,
							emp_firma,
							emp_clave_firma,
							emp_razon_social,
							emp_nombre_comercial,
							emp_direccion_matriz,
							emp_obligado_contabilidad,
							emp_ambiente_sri,
							emp_tipo_emision,
							emp_contribuyente_especial,
							fil_id,
							csmtp_servidor,
							csmtp_contrasenia,
							csmtp_puerto,
							csmtp_usuario,
							emp_es_op_transporte,
							emp_regimen_especial,
							emp_agente_retencion,
							emp_calificacion_artesanal,
							csmtp_ruta_imagenes
						FROM
							v_del_datos_empresa_sri
						WHERE emp_ruc='".$var_empresa."'
						AND est_id=".$var_establecimiento.";";
		sc_lookup(rs_empresa, $check_sql);

		if (isset({rs_empresa[0][0]})){
			//cargamos datos de la factura generada
			$check_sql="SELECT	ret_numero,
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
					WHERE ret_numero=".$ret_numero;
				sc_lookup(rs_retencion, $check_sql);

			if (isset({rs_retencion[0][0]})){
				$configApp->dirAutorizados = $dir.{rs_empresa[0][0]}."/documentos/";
				$configApp->dirLogo =  $dir.{rs_empresa[0][0]}."/logo/".{rs_empresa[0][1]};
				$configApp->dirFirma =  $dir.{rs_empresa[0][0]}."/firma/".{rs_empresa[0][2]};
				$configApp->passFirma = {rs_empresa[0][3]};
				$configApp->dirIreport=$dir_ireport;
				$retencion->configAplicacion = $configApp;

				$configCorreo->correoAsunto = "Nueva Retencion";
				$configCorreo->correoHost = {rs_empresa[0][12]};
				$configCorreo->correoPass = {rs_empresa[0][13]};
				$configCorreo->correoPort = {rs_empresa[0][14]};
				$configCorreo->correoRemitente = {rs_empresa[0][15]};
				$configCorreo->sslHabilitado = false;
				$configCorreo->rutaLogo={rs_empresa[0][20]}.'logo.jpg';
				$retencion->configCorreo = $configCorreo;

				$retencion->ambiente = {rs_retencion[0][1]};
				$retencion->tipoEmision = {rs_empresa[0][9]};
				$retencion->razonSocial = {rs_empresa[0][4]};
				$retencion->nombreComercial = {rs_empresa[0][5]}; 
				$retencion->ruc = {rs_empresa[0][0]};
				$retencion->codDoc = {rs_retencion[0][2]};
				$retencion->establecimiento = {rs_retencion[0][4]};
				$retencion->ptoEmision = {rs_retencion[0][5]};
				$retencion->secuencial = {rs_retencion[0][6]};
				$retencion->dirMatriz = {rs_empresa[0][6]}; 
				
				$retencion->padronMicroempresa={rs_empresa[0][17]};
				$retencion->padronAgenteRetencion={rs_empresa[0][18]};
				if({rs_empresa[0][18]}=='S'){
					$check_sql = "SELECT sp_busca_parametro ('NUMERORESOAR','1')";
					sc_lookup(rs, $check_sql);
					if (isset({rs[0][0]})){
					$retencion->numeroResolucion={rs[0][0]};
					}
				}
				$retencion->artesanoCalificado={rs_empresa[0][19]};
				$retencion->padronRimpe={rs_retencion[0][16]};
				$retencion->leyendaRimpe={rs_retencion[0][17]};
		
				$retencion->fechaEmision = {rs_retencion[0][3]};
				$retencion->dirEstablecimiento = {rs_retencion[0][7]};
				if ({rs_empresa[0][10]}!=''){
					$retencion->contribuyenteEspecial = {rs_empresa[0][10]};
				}
				$retencion->obligadoContabilidad ={rs_empresa[0][7]}; 
				$retencion->tipoIdentificacionSujetoRetenido = {rs_retencion[0][8]};
				if({rs_retencion[0][8]}=='06' or {rs_retencion[0][8]}=='08' ){
					$retencion->tipoSujetoRetenido={rs_retencion[0][19]};
				}
				$retencion->parteRel={rs_retencion[0][20]};
				$retencion->razonSocialSujetoRetenido = {rs_retencion[0][9]};
				$retencion->identificacionSujetoRetenido = {rs_retencion[0][10]};
				$retencion->periodoFiscal = {rs_retencion[0][11]};
				$correo_enviado={rs_retencion[0][18]};
				//cargamos los sustentos de la retencion
				$select_sql="SELECT
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
									sr_retencion=".$ret_numero;
				sc_select(rs_sustentos, $select_sql);
				$w_sustentos = array();
				$i=0;
				if ({rs_sustentos} === false){
					echo "error al acceder a los sustentos";
				}else{
					$w_sustento = new sustentoRetencion();
					$w_sustento->codSustento={rs_sustentos}->fields[1];
					$w_sustento->codDocSustento={rs_sustentos}->fields[2];
					$w_sustento->numDocSustento={rs_sustentos}->fields[3];
					$w_sustento->fechaEmisionDocSustento={rs_sustentos}->fields[4];
					$w_sustento->fechaRegistroContable={rs_sustentos}->fields[5];
					$w_sustento->numAutDocSustento={rs_sustentos}->fields[6];
					$w_sustento->pagoLocExt={rs_sustentos}->fields[7];
					if({rs_sustentos}->fields[7]=='02'){
						$w_sustento->tipoRegi={rs_sustentos}->fields[8];
						$w_sustento->paisEfecPago={rs_sustentos}->fields[9];
						$w_sustento->aplicConvDobTrib={rs_sustentos}->fields[10];
						$w_sustento->pagExtSujRetNorLeg={rs_sustentos}->fields[11];
						$w_sustento->pagoRegFis={rs_sustentos}->fields[12];
					}
					if(codDocSustento={rs_sustentos}->fields[2]=='41'){
						$w_sustento->totalComprobantesReembolso={rs_sustentos}->fields[13];
						$w_sustento->totalBaseImponibleReembolso={rs_sustentos}->fields[14];
						$w_sustento->totalImpuestoReembolso={rs_sustentos}->fields[15];
						//$w_sustento->reembolsos={rs_sustentos}->fields[];
					}
						
					$w_sustento->totalSinImpuestos={rs_sustentos}->fields[16];
					$w_sustento->importeTotal={rs_sustentos}->fields[17];
					
					$w_impuestos_sustento = array();
					$j=0;
					$select_sql="SELECT 
									isr_cod_impuesto,
									isr_cod_porcentaje,
									isr_base_imponible,
									ti_tarifa,
									isr_valor_impuesto 
								FROM v_del_impuestos_sustento_retencion_sri 
								WHERE isr_doc_sustento=".{rs_sustentos}->fields[0];
					sc_select(rs_impuestos_sustentos, $select_sql);
					if ({rs_impuestos_sustentos} === false){
						echo "error al acceder a los impuestos del sustento";
					}else{
						$w_impuesto_sustento = new impuestoDocSustento();
						$w_impuesto_sustento->codImpuestoDocSustento = {rs_impuestos_sustentos}->fields[0];
						$w_impuesto_sustento->codigoPorcentaje = {rs_impuestos_sustentos}->fields[1];
						$w_impuesto_sustento->baseImponible = {rs_impuestos_sustentos}->fields[2];
						$w_impuesto_sustento->tarifa = {rs_impuestos_sustentos}->fields[3];
						$w_impuesto_sustento->valorImpuesto = {rs_impuestos_sustentos}->fields[4];

						$w_impuestos_sustento[$j]=$w_impuesto_sustento;
						{rs_impuestos_sustentos}->MoveNext();
						$j+=1;
					}
					$w_sustento->impuestosDocSustento=$w_impuestos_sustento;

					$w_retenciones = array();
					$j=0;
					$select_sql="SELECT 
									codigo
									,codigo_retencion
									,irs_base_imponible
									,irs_porcentaje_retencion
									,irs_valor_retenido 
								FROM v_del_datos_retencion_sustento_sri 
								WHERE irs_sustento=".{rs_sustentos}->fields[0];
					sc_select(rs_retenciones,$select_sql);
					if({rs_retenciones}===false){
						echo "error al acceder a las retenciones del sustento";
					}else{
						$w_retencion_sustento = new impuestoComprobanteRetencionDos();
						$w_retencion_sustento->codigo = {rs_retenciones}->items[];
						$w_retencion_sustento->codigoRetencion = {rs_retenciones}->items[];
						$w_retencion_sustento->baseImponible = {rs_retenciones}->items[];
						$w_retencion_sustento->porcentajeRetener = {rs_retenciones}->items[];
						$w_retencion_sustento->valorRetenido = {rs_retenciones}->items[];
						$w_retencion_sustento->dividendos = {rs_retenciones}->items[];
						$w_retencion_sustento->compraCajaBanano = {rs_retenciones}->items[];
						$w_retenciones[$j] = $w_retencion_sustento; 
						{rs_retenciones}->MoveNext();
						$j+=1;
					}
					$w_sustento->retenciones=$w_retenciones;

					$w_sustento->pagos={rs_sustentos}->fields[];
					
					$w_sustentos[$i]=$w_sustento;
					{rs_sustentos}->MoveNext();
					$i+=1;
				}
				$retencion->docsSustento=$w_sustentos;
				
				$camposAdicionales = array();
				$i=0;
				if({rs_empresa[0][19]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "artesanoCalificado";
					$campoAdicional->valor = 'Nro. '.{rs_empresa[0][19]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}
				if ({rs_retencion[0][14]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Telefono";
					$campoAdicional->valor = {rs_retencion[0][14]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}

				if ({rs_retencion[0][13]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Email";
					$campoAdicional->valor = {rs_retencion[0][13]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}	

				if ({rs_retencion[0][12]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Direccion";
					$campoAdicional->valor = {rs_retencion[0][12]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
				}	

				if ({rs_retencion[0][15]}<>''){
					$campoAdicional = new campoAdicional();
					$campoAdicional->nombre = "Comentario";
					$campoAdicional->valor = {rs_retencion[0][15]};
					$camposAdicionales[$i] = $campoAdicional;
					$i+=1;
					}	
				$retencion->infoAdicional = $camposAdicionales;

				$check_sql="SELECT	dr_retencion,
									dr_impuesto,
									pri_codigo,
									dr_base_imponible,
									dr_porcentaje_retencion,
									dr_valor_retenido,
									dr_cod_doc_sustento,
									doc_sustento,
									fecha 
								FROM
									v_del_detalle_retencion_sri
								where dr_retencion=".$ret_numero."
								order by dr_id";
				sc_select(rs_detalles, $check_sql);
				if ({rs_detalles} === false){
					echo "error al acceder a los detalles";
				}else{
					$impuestos = array();
					$i=0;
					while (!{rs_detalles}->EOF){
						$impuesto = new impuestoComprobanteRetencion();
						$impuesto->codigo = {rs_detalles}->fields[1];
						$impuesto->codigoRetencion = {rs_detalles}->fields[2];
						$impuesto->baseImponible = {rs_detalles}->fields[3];
						$impuesto->porcentajeRetener = {rs_detalles}->fields[4];
						$impuesto->valorRetenido = {rs_detalles}->fields[5];
						$impuesto->codDocSustento = {rs_detalles}->fields[6];
						$impuesto->numDocSustento = {rs_detalles}->fields[7];
						$impuesto->fechaEmisionDocSustento = {rs_detalles}->fields[8];
						$impuestos[$i] = $impuesto;
						{rs_detalles}->MoveNext();
						$i+=1;
					}
					{rs_detalles}->Close();
					$retencion->impuestos = $impuestos;
				}	
				$procesarComprobante = new procesarComprobante();
				$procesarComprobante->comprobante = $retencion;
				$procesarComprobante->envioSRI = false; 
				$res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
				if($i_log){
					var_dump($retencion);
					echo '<br>';
					var_dump($res);
					echo '<br>';
				}

				if($i_autorizar=='S'){
					if ($res->return->estadoComprobante == "FIRMADO") {
						$procesarComprobante = new procesarComprobante();
						$procesarComprobante->comprobante = $retencion;
						$procesarComprobante->envioSRI = true; 
						$res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
						if($i_log){
							var_dump($res);
							echo "<br>";
						}	
					}else{
						if($res->return->estadoComprobante == "PROCESANDOSE"){
							$retencionPendiente = new \comprobantePendiente();
							$retencionPendiente->configAplicacion = $configApp;
							$retencionPendiente->configCorreo = $configCorreo;
							$retencionPendiente->ambiente = {rs_retencion[0][1]};
							$retencionPendiente->codDoc = {rs_retencion[0][2]};
							$retencionPendiente->establecimiento = {rs_retencion[0][4]};
							$retencionPendiente->fechaEmision = {rs_retencion[0][3]};
							$retencionPendiente->ptoEmision = {rs_retencion[0][5]};
							$retencionPendiente->ruc = {rs_empresa[0][0]};
							$retencionPendiente->secuencial = {rs_retencion[0][6]};
							$retencionPendiente->tipoEmision = {rs_empresa[0][9]};
							$comprobantePendiente->padronMicroempresa = {rs_empresa[0][17]};
							$comprobantePendiente->padronAgenteRetencion = {rs_empresa[0][18]};
							$comprobantePendiente->padronRimpe = {rs_empresa[0][21]};
							$procesarComprobantePendiente = new \procesarComprobantePendiente();
							$procesarComprobantePendiente->comprobantePendiente = $retencionPendiente;
							$res = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
							//var_dump($res);
							//echo "<br>";
							if ($res->return->estadoComprobante == "PROCESANDOSE") {
								$res->return->estadoComprobante = "ERROR";
							}
						}	
					}
				}	
				$mensaje_final=	$res->return->estadoComprobante."<br>";
				if ($res->return->estadoComprobante == 'ERROR'){
					$mensaje_final.=$res->return->mensajes->mensaje."<br>";
				}

				if ($res->return->estadoComprobante == "FIRMADO") {
						$update_sql =  "UPDATE  del_retencion
										SET  ret_estado_sri='".$res->return->estadoComprobante."'
										WHERE ret_numero=".$ret_numero ;
						sc_exec_sql($update_sql);
					}
				if($res->return->estadoComprobante=='AUTORIZADO'){
					if($correo_enviado=="NO"){
						if(enviarCorreo($retencion,{rs_retencion[0][13]})){
							$correo_enviado="SI";
						}else{
							$correo_enviado="NO";
						}
					}
					
					$update_sql = "UPDATE del_retencion 
									SET  ret_estado_sri='".$res->return->estadoComprobante."',
										ret_clave='".$res->return->claveAcceso."',   
										ret_autorizacion='".$res->return->numeroAutorizacion."',
										ret_fecha_autorizacion='".$res->return->fechaAutorizacion."',
										ret_correo_enviado='".$correo_enviado."'
									WHERE ret_numero=".$ret_numero ;
					sc_exec_sql($update_sql);
				}
				if($res->return->estadoComprobante=='DEVUELTA'){
					if($res->return->mensajes->mensaje=='CLAVE ACCESO REGISTRADA'){
						if($correo_enviado=="NO"){
							if(enviarCorreo($retencion,{rs_retencion[0][13]})){
								$correo_enviado="SI";
							}else{
								$correo_enviado="NO";
							}
						}
						$update_sql = "UPDATE del_retencion 
									SET  ret_estado_sri='AUTORIZADO',
										ret_clave='".$res->return->claveAcceso."',   
										ret_autorizacion='".$res->return->claveAcceso."',
										ret_correo_enviado='".$correo_enviado."'
									WHERE ret_numero=".$ret_numero ;
					}else{
						$update_sql = "UPDATE del_retencion 
									SET  ret_estado_sri='".$res->return->estadoComprobante."',
										ret_clave='".$res->return->claveAcceso."',   
										ret_error_sri='".$res->return->mensajes->mensaje."'
									WHERE ret_numero=".$ret_numero ;
					}


					sc_exec_sql($update_sql);
					$mensaje_final.=$res->return->mensajes->mensaje."<br>";
				}
				if($res->return->estadoComprobante=='NO AUTORIZADO'){
					$update_sql = "UPDATE del_retencion 
									SET  ret_estado_sri='".$res->return->estadoComprobante."',
										ret_clave='".$res->return->claveAcceso."',   
										ret_error_sri='".$res->return->mensajes->mensaje."',
										ret_fecha_autorizacion='".$res->return->fechaAutorizacion."'
									WHERE ret_numero=".$ret_numero ;
					sc_exec_sql($update_sql);
					$mensaje_final.=$res->return->mensajes->mensaje."<br>";
				}
				return $mensaje_final;
			}
		}
	}

	function autorizar_nd ($nd_numero,$i_autorizar,$i_log=false){
		$check_sql = "SELECT sp_busca_parametro ('RUTA_FIRMADOR','http://localhost:8085/MasterOffline/ProcesarComprobanteElectronico?wsdl')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $ruta_firmador={rs[0][0]};
		}
		$correo_enviado='';
		$procesarComprobanteElectronico = new ProcesarComprobanteElectronico($wsdl = $ruta_firmador);
		$configApp = new \configAplicacion();
		$configCorreo = new \configCorreo();
		$notaDebito = new notaDebito();
		$check_sql = "SELECT sp_busca_parametro ('RUTA_EMPRESA','E:/Desarrollos/')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir={rs[0][0]};
		}
		$check_sql = "SELECT sp_busca_parametro ('RUTA_IREPORT','D:/Desarrollo/IReport')";
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
		   $dir_ireport={rs[0][0]};
		}
		$check_sql="SELECT nd_empresa,nd_establecimiento FROM del_nota_debito WHERE nd_numero=".$nd_numero;
		sc_lookup(rs, $check_sql);
		if (isset({rs[0][0]})){
			$var_empresa={rs[0][0]};
			$var_establecimiento={rs[0][1]};	
		}
		$check_sql = "SELECT
							emp_ruc,
							emp_logo,
							emp_firma,
							emp_clave_firma,
							emp_razon_social,
							emp_nombre_comercial,
							emp_direccion_matriz,
							emp_obligado_contabilidad,
							emp_ambiente_sri,
							emp_tipo_emision,
							emp_contribuyente_especial,
							fil_id,
							csmtp_servidor,
							csmtp_contrasenia,
							csmtp_puerto,
							csmtp_usuario,
							emp_es_op_transporte,
							emp_regimen_especial,
							emp_agente_retencion,
							emp_calificacion_artesanal,
							csmtp_ruta_imagenes
						FROM
							v_del_datos_empresa_sri
						WHERE emp_ruc='".$var_empresa."'
						AND est_id=".$var_establecimiento.";";
		sc_lookup(rs_empresa, $check_sql);

		if (isset({rs_empresa[0][0]})){
			$configApp->dirAutorizados = $dir.{rs_empresa[0][0]}."/documentos/";
			$configApp->dirLogo =  $dir.{rs_empresa[0][0]}."/logo/".{rs_empresa[0][1]};
			$configApp->dirFirma =  $dir.{rs_empresa[0][0]}."/firma/".{rs_empresa[0][2]};
			$configApp->passFirma = {rs_empresa[0][3]};
			$configApp->dirIreport=$dir_ireport;
			$notaDebito->configAplicacion = $configApp;

			$configCorreo->correoAsunto = "Nueva Nota de Debito";
			$configCorreo->correoHost = {rs_empresa[0][12]};
			$configCorreo->correoPass = {rs_empresa[0][13]};
			$configCorreo->correoPort = {rs_empresa[0][14]};
			$configCorreo->correoRemitente = {rs_empresa[0][15]};
			$configCorreo->sslHabilitado = false;
			$configCorreo->rutaLogo={rs_empresa[0][20]}.'logo.jpg';
			$notaDebito->configCorreo = $configCorreo;

			$notaDebito->ruc = {rs_empresa[0][0]};
			$notaDebito->razonSocial = {rs_empresa[0][4]};
			$notaDebito->nombreComercial = {rs_empresa[0][5]}; 
			$notaDebito->dirMatriz = {rs_empresa[0][6]}; 
			$notaDebito->obligadoContabilidad ={rs_empresa[0][7]}; 
			$notaDebito->tipoEmision = {rs_empresa[0][9]};
			if ({rs_empresa[0][10]}!=''){
				$notaDebito->contribuyenteEspecial = {rs_empresa[0][10]};
			}
			$notaDebito->padronMicroempresa={rs_empresa[0][17]};
			$notaDebito->padronAgenteRetencion={rs_empresa[0][18]};
			if({rs_empresa[0][18]}=='S'){
				$check_sql = "SELECT sp_busca_parametro ('NUMERORESOAR','1')";
				sc_lookup(rs, $check_sql);
				if (isset({rs[0][0]})){
				   $notaDebito->numeroResolucion={rs[0][0]};
				}
			}
			$notaDebito->artesanoCalificado={rs_empresa[0][19]};

		}

		$check_sql="SELECT
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
							nd_serie_docmod||'-'||nd_secuencial_docmod,
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
					WHERE nd_numero=".$nd_numero ;
		sc_lookup(rs_notaDebito, $check_sql);

		if (isset({rs_notaDebito[0][0]})){
			$correo_enviado={rs_notaDebito[0][27]};
			$notaDebito->padronRimpe={rs_notaDebito[0][25]};
			$notaDebito->leyendaRimpe={rs_notaDebito[0][26]};
			$notaDebito->ambiente = {rs_notaDebito[0][1]};
			$notaDebito->codDoc = {rs_notaDebito[0][2]};
			$notaDebito->fechaEmision = {rs_notaDebito[0][3]};
			$notaDebito->dirEstablecimiento = {rs_notaDebito[0][4]};
			$notaDebito->establecimiento = {rs_notaDebito[0][5]}; 
			$notaDebito->ptoEmision = {rs_notaDebito[0][6]}; 
			$notaDebito->secuencial = {rs_notaDebito[0][7]};
			$notaDebito->tipoIdentificacionComprador = {rs_notaDebito[0][8]};
			$notaDebito->razonSocialComprador = {rs_notaDebito[0][9]}; 
			$notaDebito->identificacionComprador = {rs_notaDebito[0][10]};
			$notaDebito->codDocModificado = {rs_notaDebito[0][11]};
			$notaDebito->numDocModificado = {rs_notaDebito[0][12]};
			$notaDebito->fechaEmisionDocSustento = {rs_notaDebito[0][13]};
			$notaDebito->totalSinImpuestos = {rs_notaDebito[0][14]}; 
			$total_Impuestos=array();
			$i=0;
				if({rs_notaDebito[0][15]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '2'; 
					$totalImpuesto->baseImponible = {rs_notaDebito[0][15]};
					$totalImpuesto->tarifa='12';
					$totalImpuesto->valor = {rs_notaDebito[0][16]};
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_notaDebito[0][17]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '0'; 
					$totalImpuesto->baseImponible = {rs_notaDebito[0][17]};
					$totalImpuesto->tarifa='0';
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_notaDebito[0][18]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo = '2'; 
					$totalImpuesto->codigoPorcentaje = '6'; 
					$totalImpuesto->baseImponible = {rs_notaDebito[0][18]}; 
					$totalImpuesto->tarifa='0';
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_notaDebito[0][19]}>0){
					$totalImpuesto = new totalImpuesto();
					$totalImpuesto->codigo ='2'; 
					$totalImpuesto->codigoPorcentaje = '7'; 
					$totalImpuesto->baseImponible = {rs_notaDebito[0][19]}; 
					$totalImpuesto->tarifa='0';
					$totalImpuesto->valor = '0.00';
					$total_Impuestos[$i]=$totalImpuesto;
					$i+=1;
				}	
				if({rs_notaDebito[0][20]}>0){	
					$check_sql="SELECT  '3' as impuesto,
										 dnd_porcentaje_ice,
										sum(dnd_base_ice),
										sum(dnd_valor_ice) 
								FROM del_detalle_nota_debito 
								WHERE dnd_porcentaje_ice<>'0' 
								and dnd_nota_debito=".$nd_numero."
								group by dnd_porcentaje_ice";
					sc_select(rs_impuesto, $check_sql); 
					if ({rs_impuesto}  === false){
						echo "Error al acceder a del_detalle_notaDebito";
					}else{
						while (!{rs_impuesto}->EOF){
							$totalImpuesto = new totalImpuesto();
							$totalImpuesto->codigo ={rs_impuesto}->fields[0]; 
							$totalImpuesto->codigoPorcentaje = {rs_impuesto}->fields[1]; 
							$totalImpuesto->baseImponible = {rs_impuesto}->fields[2];
							$totalImpuesto->valor = {rs_impuesto}->fields[3];
							$total_Impuestos[$i]=$totalImpuesto;
							$i+=1;
							{rs_impuesto}->MoveNext();
						}
						{rs_impuesto}->Close();
					}	
				}

			$notaDebito->impuestos = $total_Impuestos;
			$notaDebito->valorTotal = {rs_notaDebito[0][21]}; 
			//pagos
			$pagos = array();
			$check_sql="SELECT 	a.fp_id,
								sri_forma_pago.fp_codigo,
								a.fp_valor,
								coalesce(a.fp_plazo,0),
								coalesce(a.fp_unidad_tiempo,'DIAS') 
						FROM del_forma_pago_nd a 
						inner join del_forma_pago b on a.fp_forma_pago=b.fp_id	
						inner join sri_forma_pago on b.fp_sri=sri_forma_pago.fp_codigo
						where a.fp_nota_debito=".$nd_numero ;
			sc_select(rs_pagos, $check_sql); 
			if ({rs_pagos}  === false){
				echo "Error al acceder a las formas de pago";
			}else{
				$i=0;
				while (!{rs_pagos}->EOF){
					$pago = new pagos();
					$pago->formaPago ={rs_pagos}->fields[1];
					$pago->total = {rs_pagos}->fields[2];
					$pago->plazo = {rs_pagos}->fields[3];
					$pago->unidadTiempo={rs_pagos}->fields[4];
					$pagos[$i]=$pago;
					$i+=1;
					{rs_pagos}->MoveNext();
				}
				{rs_pagos}->Close();
			}	
			$notaDebito->pagos = $pagos;
			//aqui van los detalles
			$check_sql="SELECT pro_descripcion,
							   dnd_precio_unitario 
						FROM v_del_detalle_nota_debito_sri
						WHERE dnd_nota_debito=".$nd_numero."
						order by dnd_id" ;

			sc_select(rs_motivos, $check_sql);   
			if ({rs_motivos}  === false){
				echo "Error al acceder al detalle de la notaDebito";
			}else{
				$motivos = array();
				$i=0;
				while (!{rs_motivos}->EOF){
					$motivo = new motivo();
					$motivo->razon = {rs_motivos}->fields[0];
					$motivo->valor = {rs_motivos}->fields[1];
					$motivos[$i] = $motivo;
					{rs_motivos}->MoveNext();
					$i+=1;
				}
				{rs_motivos}->Close();
				$notaDebito->motivos = $motivos;
			}

			$camposAdicionales = array();
			$i=0;
			if({rs_empresa[0][19]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "artesanoCalificado";
				$campoAdicional->valor = 'Nro. '.{rs_empresa[0][19]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			if({rs_notaDebito[0][24]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Telefono";
				$campoAdicional->valor = {rs_notaDebito[0][24]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			if({rs_notaDebito[0][23]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Email";
				$campoAdicional->valor = {rs_notaDebito[0][23]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}

			if({rs_notaDebito[0][22]}<>''){
				$campoAdicional = new campoAdicional();
				$campoAdicional->nombre = "Direccion";
				$campoAdicional->valor = {rs_notaDebito[0][22]};
				$camposAdicionales[$i] = $campoAdicional;
				$i+=1;
			}
			$notaDebito->infoAdicional = $camposAdicionales;

			$procesarComprobante = new procesarComprobante();
			$procesarComprobante->comprobante = $notaDebito;
			$procesarComprobante->envioSRI = false; //El sistema si es true //1-Crea XML en el directorio de autorizado 
			$res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
			if($i_log){
				var_dump($notaDebito);
				echo '<br>';
				var_dump($res);
				echo '<br>';
			}	
			if($i_autorizar=='S'){
				if ($res->return->estadoComprobante == "FIRMADO") {
					$procesarComprobante = new procesarComprobante();
					$procesarComprobante->comprobante = $notaDebito;
					$procesarComprobante->envioSRI = true; //El sistema si es false 
					$res=$procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
					if($i_log){
						var_dump($res);
						echo '<br>';
					}	
				}else{
					if($res->return->estadoComprobante == "PROCESANDOSE"){
						$comprobantePendiente = new \comprobantePendiente();
						$comprobantePendiente->configAplicacion = $configApp;
						$comprobantePendiente->configCorreo = $configCorreo;
						$comprobantePendiente->ambiente = {rs_notaDebito[0][1]};
						$comprobantePendiente->codDoc = {rs_notaDebito[0][2]};
						$comprobantePendiente->establecimiento = {rs_notaDebito[0][5]};
						$comprobantePendiente->fechaEmision = {rs_notaDebito[0][3]};
						$comprobantePendiente->ptoEmision = {rs_notaDebito[0][6]};
						$comprobantePendiente->ruc = {rs_empresa[0][0]};
						$comprobantePendiente->secuencial = {rs_notaDebito[0][7]};
						$comprobantePendiente->tipoEmision = {rs_empresa[0][9]};
						$comprobantePendiente->padronMicroempresa = {rs_empresa[0][17]};
						$comprobantePendiente->padronAgenteRetencion = {rs_empresa[0][18]};
						$comprobantePendiente->padronRimpe = {rs_empresa[0][21]};
						$procesarComprobantePendiente = new \procesarComprobantePendiente();
						$procesarComprobantePendiente->comprobantePendiente = $comprobantePendiente;
						$res = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
						if ($res->return->estadoComprobante == "PROCESANDOSE") {
							$res->return->estadoComprobante = "ERROR";
						}
					}	
				}
			}

			$mensaje_final=	$res->return->estadoComprobante."<br>";
			//echo $res->return->estadoComprobante;
			if ($res->return->estadoComprobante == 'ERROR'){
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}

			if ($res->return->estadoComprobante == "FIRMADO") {
				$update_sql =  "UPDATE del_nota_debito 
								SET  nd_estado_sri='".$res->return->estadoComprobante."'
								WHERE nd_numero=".$nd_numero ;
				sc_exec_sql($update_sql);
			}

			if($res->return->estadoComprobante=='AUTORIZADO'){
				if($correo_enviado=="NO"){
					if(enviarCorreo($notaDebito,{rs_notaDebito[0][23]})){
						$correo_enviado="SI";
					}else{
						$correo_enviado="NO";
					}
				}
				$update_sql = "UPDATE del_nota_debito 
								SET  nd_estado_sri='".$res->return->estadoComprobante."',
									 nd_clave='".$res->return->claveAcceso."',   
									 nd_autorizacion='".$res->return->numeroAutorizacion."',
									 nd_fecha_autorizacion='".$res->return->fechaAutorizacion."',
									 nd_error_sri='',
									 nd_correo_enviado='".$correo_enviado."'
								WHERE nd_numero=".$nd_numero ;
				sc_exec_sql($update_sql);
			}
			if($res->return->estadoComprobante=='DEVUELTA'){
				if($res->return->mensajes->mensaje=='CLAVE ACCESO REGISTRADA'){
					if($correo_enviado=="NO"){
						if(enviarCorreo($notaDebito,{rs_notaDebito[0][23]})){
							$correo_enviado="SI";
						}else{
							$correo_enviado="NO";
						}
					}
					$update_sql = "UPDATE del_nota_debito 
								SET  nd_estado_sri='AUTORIZADO',
									 nd_clave='".$res->return->claveAcceso."',   
									 nd_autorizacion='".$res->return->claveAcceso."',
									 nd_correo_enviado='".$correo_enviado."'
								WHERE nd_numero=".$nd_numero ;
				}else{
					$update_sql = "UPDATE del_nota_debito 
								SET  nd_estado_sri='".$res->return->estadoComprobante."',
									 nd_clave='".$res->return->claveAcceso."',   
									 nd_error_sri='".$res->return->mensajes->mensaje."'
								WHERE nd_numero=".$nd_numero ;
				}


				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}
			if($res->return->estadoComprobante=='NO AUTORIZADO'){
				$update_sql = "UPDATE del_nota_debito 
								SET  nd_estado_sri='".$res->return->estadoComprobante."',
									 nd_clave='".$res->return->claveAcceso."',   
									 nd_error_sri='".$res->return->mensajes->mensaje."',
									 nd_fecha_autorizacion='".$res->return->fechaAutorizacion."'
								WHERE nd_numero=".$nd_numero ;
				sc_exec_sql($update_sql);
				$mensaje_final.=$res->return->mensajes->mensaje."<br>";
			}

			return $mensaje_final;
		}
	}	

?>