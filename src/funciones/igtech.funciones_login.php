<?php
include_once ('src/funciones/igtech.funciones_token.php');
function verificarLogin($i_data){
    try{
        $Log=new IgtechLog ();
        $Log->Abrir();
        $Log->EscribirLog(' Verificar Login ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Datos: '.var_export($i_data,true));        
        $w_usuario  = 	$i_data['user'];
        $w_password = 	$i_data['pass'];
        $ws_conexion=ws_coneccion_bdd();
        $select_sql=   "SELECT usu_usuario 
                        FROM sis_usuario 
                        WHERE md5(usu_usuario)='".$w_usuario."'
                        AND usu_contrasenia='".$w_password."'
                        AND usu_estado='V' ;";
        $Log->EscribirLog(' Consulta: '.$select_sql);
        if($result = pg_query($ws_conexion, $select_sql)){
            if( pg_num_rows($result) > 0 ){
                $devolver = pg_fetch_object( $result, 0 );
                $w_respuesta=array('token'=>Auth::SignIn(['respuesta' => 0,'mensaje' => 'ok','usuario' => $devolver->usu_usuario]));
                $o_respuesta=array('error'=>'0','mensaje'=>'Login OK','datos'=>$w_respuesta);
                $close = pg_close($ws_conexion);
                registrarToken($w_respuesta['token'],'V');
            }else{
                $o_respuesta=array('error'=>'9996','mensaje'=>'No existe el Usuario');
            }
        }else{
            $o_respuesta=array('error'=>'9997','mensaje'=>pg_last_error($ws_conexion));
        }
    }catch(Throwable $e){
        $o_respuesta=array('error'=>'9999','mensaje'=>$e->getMessage());
    }
    $Log->EscribirLog(' Respuesta: '.var_export($o_respuesta,true));
    return $o_respuesta;
}

function verificarLoginUsuario($i_data){
    try{
        $Log=new IgtechLog ();
        $Log->Abrir();
        $Log->EscribirLog(' Verificar Login Usuario ');
        $Log->EscribirLog(' DATOS DE ENTRADA');
        $Log->EscribirLog(' Datos: '.var_export($i_data,true));
        $w_usuario  = 	$i_data['user'];
        $w_password = 	$i_data['pass'];
        $ws_conexion=ws_coneccion_bdd();
        $select_sql="SELECT usu_usuario 
                    FROM del_usuario 
                    WHERE usu_usuario='".$w_usuario."'
                    AND usu_contrasenia=md5('".$w_password."')
                    AND usu_estado='V';";
        $Log->EscribirLog(' Consulta: '.$select_sql);
        if($result = pg_query($ws_conexion, $select_sql)){
            if( pg_num_rows($result) > 0 ){
                $devolver = pg_fetch_object( $result, 0 );
                $w_respuesta=array('token'=>Auth::SignIn(['respuesta' => 0,'mensaje' => 'ok','usuario' => $devolver->usu_usuario]));
                $o_respuesta=array('error'=>'0','mensaje'=>'Login OK','datos'=>$w_respuesta);
            }else{
               $o_respuesta=array('error'=>'9996','mensaje'=>'No existe el Usuario');
            }
        }else{
            $o_respuesta=array('error'=>'9997','mensaje'=>pg_last_error($ws_conexion));
        }
        $result = null;
        $close = pg_close($ws_conexion);
        if(isset($w_respuesta['token']))
            registrarToken($w_respuesta['token'],'V');
    }catch(Throwable $e){
            $o_respuesta=array('error'=>'9999','mensaje'=>$e->getMessage());
    }
    $Log->EscribirLog(' Respuesta: '.var_export($o_respuesta,true));
    return $o_respuesta;
}

function logOut($i_data){
    $Log=new IgtechLog ();
    $Log->Abrir();
    $Log->EscribirLog(' LogOut ');
    $Log->EscribirLog(' DATOS DE ENTRADA');
    $Log->EscribirLog(' Datos: '.var_export($i_data,true));
    return actualizarToken($i_data,'N');
}
?>