<?php

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
    $w_parametros = buscarParametro('RUTA_SKINS', 'D:/Pruebas/');
    if ($w_parametros['error'] == '0') {
        $ruta_imagenes=$w_parametros['datos']."imagenes/";
        $nombre=$w_parametros['datos']."skin_email_documentos.html";
    }
    $codDoc = $xmlComprobante->codDoc;
    $w_establecimiento= $xmlComprobante->establecimiento;
    $ptoEmision = $xmlComprobante->ptoEmision;
    $secuencial_doc = $xmlComprobante->secuencial;
    
    $tipoComprobante = "";
    $dirigido = "";
    $w_datos_adicionales = "";
    if ($codDoc=="01") {
        $tipoComprobante = "FACTURA";
        $dirigido = $xmlComprobante->razonSocialComprador;
        $importeTotal = $xmlComprobante->importeTotal;
        $w_datos_adicionales = "<strong>Valor Total: </strong>".$importeTotal."<br /><br />";
    } else if ($codDoc=="03") {
        $tipoComprobante = "LIQUIDACIÓN DE COMPRA DE BIENES Y PRESTACIÓN DE SERVICIOS";
        $dirigido = $xmlComprobante->razonSocialProveedor;
        $importeTotal = $xmlComprobante->importeTotal;
        $w_datos_adicionales = "<strong>Valor Total: </strong>".$importeTotal."<br /><br />";
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
    if (file_exists($nombre)){ 
        $fp = fopen ($nombre,"r"); 
        $datos = fread($fp, filesize($nombre));
        fclose($fp);
        $datos=str_replace('$rutaLogo',$w_ruta_logo,$datos);
        $datos=str_replace('$razonSocial',$razonSocial,$datos);
        $datos=str_replace('$EMPRESA_CLIENTE',$razonSocialEmisor,$datos);
        $datos=str_replace('$tipoComprobante',$tipoComprobante,$datos);
        $datos=str_replace('$w_establecimiento',$w_establecimiento,$datos);
        $datos=str_replace('$ptoEmision',$ptoEmision,$datos);
        $datos=str_replace('$secuencial_doc',$secuencial_doc,$datos);
        $datos=str_replace('$w_datos_adicionales',$w_datos_adicionales,$datos);
        $datos=str_replace('imagenes/',$ruta_imagenes,$datos);
        
        return $datos;
    }else{
        return 'No se pudo cargar skin '.$nombre;
    }        
}	

function enviarCorreo($xmlComprobante,$correo_destino){
    
    $w_parametros = buscarParametro('RUTA_EMPRESA', 'D:/Desarrollos/');
        //var_dump($w_parametros);
    if ($w_parametros['error'] == '0') {
        $dir = $w_parametros['datos'];
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
    $mail_subject        = $configCorreo->correoAsunto;
    $mail_message        = crearMensajeCorreo($xmlComprobante,$configCorreo->rutaLogo); 
    $mail_format         = 'H';//format: (T)ext or (H)tml
    $mail_copies		 = '';	
    $mail_tp_copies		 = '';
    $mail_port           = $configCorreo->correoPort;
    $mail_tp_connection  = $configCorreo->sslHabilitado;
    $mail_atachment		 = array();
    $mail_atachment[0]= $pathpdf;
    $mail_atachment[1]= $pathxml;
    
    try {
        $destinatarios = explode(",", $correo_destino);
        foreach( $destinatarios as $destinatario){

            $mail_to= trim($destinatario);

            $correo_enviado=ig_mail_send(   $mail_smtp_server,
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
        return $correo_enviado;
    } catch (Exception $e) {
        //echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        return false;
    }

}	

function ig_mail_send(  $mail_smtp_server,
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
                        $mail_atachment){
    try {
        // Create the SMTP Transport
        //$sc_mail_port     = $mail_port;
        //$sc_mail_tp_port  = $mail_tp_connection;
        if ($mail_tp_connection == "S"){
            $sc_mail_port = (!empty($mail_port)) ? $mail_port : 465;
            $transport = (new Swift_SmtpTransport($mail_smtp_server, $sc_mail_port,'ssl'))
                ->setUsername( $mail_smtp_user)
                ->setPassword($mail_smtp_pass);
        }else if($mail_tp_connection == "T"){
            $sc_mail_port = !empty($mail_port) ? $mail_port : 587;    
            $transport = (new Swift_SmtpTransport($mail_smtp_server, $sc_mail_port,'tls'))
                ->setUsername( $mail_smtp_user)
                ->setPassword($mail_smtp_pass);
         
        }else{
            $sc_mail_port = (!empty($mail_port)) ? $mail_port : 25;
            $transport = (new Swift_SmtpTransport($mail_smtp_server, $sc_mail_port))
                ->setUsername( $mail_smtp_user)
                ->setPassword($mail_smtp_pass);
        }
        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);
        $message = new Swift_Message();
        $message->setSubject($mail_subject);
        $message->setFrom([$mail_from => 'Debisoft']);
        $message->addTo($mail_to,'');
     
        // Add "CC" address [Use setCc method for multiple recipients, argument should be array]
        //$message->addCc($mail_to, '');
     
        // Add "BCC" address [Use setBcc method for multiple recipients, argument should be array]
        //$message->addBcc('recipient@gmail.com', 'recipient name');
     
        // Add an "Attachment" (Also, the dynamic data can be attached)
        foreach ($mail_atachment as $adjunto){
            $attachment = Swift_Attachment::fromPath($adjunto);
        //$attachment->setFilename('report.xls');
            $message->attach($attachment);
        }
        
     
        // Add inline "Image"
        //$inline_attachment = Swift_Image::fromPath('nature.jpg');
        //$cid = $message->embed($inline_attachment);
     
        // Set the plain-text "Body"
        if($mail_format=='T'){
            $message->setBody( $mail_message);
        }else{    
        // Set a "Body"
            $message->addPart( $mail_message,'text/html');
        }    
        // Send the message
        $mailer->send($message);
        return true;
    } catch (Exception $e) {
      return false;
    }

}

?>