<?php

function buscarParametro($i_parametro,$i_valor_defecto){
    try{
        $ws_conexion=ws_coneccion_bdd();
        $select_sql="SELECT sp_busca_parametro ('".$i_parametro."','".$i_valor_defecto."')";
        
        if($result = pg_query($ws_conexion, $select_sql)){
            if( pg_num_rows($result) > 0 ){
                $row = pg_fetch_object( $result, 0 ); 
                $o_respuesta=array('error'=>'0','mensaje'=>'ok','datos'=>$row->sp_busca_parametro);
            }
        }else{
            $o_respuesta=array('error'=>'0','mensaje'=>'ok','datos'=>$i_valor_defecto);
        }
        $close = pg_close($ws_conexion);        
    }catch(Throwable $e){
        $o_respuesta=array('error'=>'9999','mensaje'=>$e->getMessage());
    }
    return $o_respuesta;
}

function crearNombreFicheroSinExtension($xmlComprobante){
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
    return $nombre . $establecimiento . "-" . $ptoEmision . "-" . $secuencial_doc ;
}

function seleccionarDatosEmpresaSRI($i_empresa,$i_establecimiento){
    try{
        // var_dump($i_empresa,$i_establecimiento);
        $Log=new IgtechLog ();
        $Log->Abrir();
	    $Log->EscribirLog(' SELECCIONAR DATOS EMPRESA SRI');
	    $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Empresa: '.$i_empresa);
        $Log->EscribirLog(' Establecimiento: '.$i_establecimiento);
        
        $ws_conexion=ws_coneccion_bdd();
        $select_sql="SELECT
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
                            csmtp_ruta_imagenes,
							csmtp_tipo_conexion
                        FROM
                            v_del_datos_empresa_sri
                        WHERE emp_ruc='".$i_empresa."'
                        AND est_id=".$i_establecimiento;
        $Log->EscribirLog(' Consulta: '.$select_sql);                
        if($result = pg_query($ws_conexion, $select_sql)){
            $w_respuesta = array(); //creamos un array
            if( pg_num_rows($result) > 0 ){
                $row = pg_fetch_object($result, 0 );
                $w_respuesta = array(
                    'ruc'=>$row->emp_ruc,
                    'logo'=>$row->emp_logo,
                    'firma'=>$row->emp_firma,
                    'passFirma'=>$row->emp_clave_firma,
                    'razonSocial'=>$row->emp_razon_social,
                    'nombreComercial'=>$row->emp_nombre_comercial,
                    'dirMatriz'=>$row->emp_direccion_matriz,
                    'obligadoContabilidad'=>$row->emp_obligado_contabilidad,
                    'ambiente'=>$row->emp_ambiente_sri,
                    'tipoEmision'=>$row->emp_tipo_emision,
                    'contribuyenteEspecial'=>$row->emp_contribuyente_especial,
                    'filial'=>$row->fil_id,
                    'correoHost'=>$row->csmtp_servidor,
                    'correoPass'=>$row->csmtp_contrasenia,
                    'correoPort'=>$row->csmtp_puerto,
                    'correoRemitente'=>$row->csmtp_usuario,
                    'esOpTransporte'=>$row->emp_es_op_transporte,
                    'padronMicroempresa'=>$row->emp_regimen_especial,
                    'padronAgenteRetencion'=>$row->emp_agente_retencion,
                    'artesanoCalificado'=>$row->emp_calificacion_artesanal,
                    'rutaLogo'=>$row->csmtp_ruta_imagenes,
                    'tipoConexion'=>$row->csmtp_tipo_conexion,
                );
            }
            $o_respuesta=array('error'=>'0','mensaje'=>'ok','datos'=>$w_respuesta);
        }else{
            $o_respuesta=array('error'=>'9997','mensaje'=>pg_last_error($ws_conexion));
        }
        $close = pg_close($ws_conexion);
    }catch(Throwable $e){
        $o_respuesta=array('error'=>'9999','mensaje'=>$e->getMessage());
    }
    $Log->EscribirLog(' Respuesta: '.var_export($o_respuesta,true));
    return $o_respuesta;
}
?>