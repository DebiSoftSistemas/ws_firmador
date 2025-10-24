<?php 
function ws_coneccion_bdd(){
	$server = "host=localhost";
	$puerto = "port=5432";
	$user = "user=postgres";
	$pass = "password=062603102";
	$bd = "dbname=bd_debifact";
	$ws_conexion = pg_connect("$server $puerto $bd $user $pass");
	if(!$ws_conexion){
		echo "Error: ".pg_last_error($ws_conexion ?? NULL);
	}else{
		return $ws_conexion;
	}
}

function my_pg_query( $conexion=null, $sql ){
	 $rs = @pg_query( $conexion, $sql );
	if( $rs == false )
		 throw new Exception( "Error PostgreSQL ".pg_last_error($conexion) );
	return $rs;
}

function array_to_xml($array, &$xml_user_info) {
    foreach($array as $key => $value) {
        if(is_array($value)) {
            if(!is_numeric($key)){
                $subnode = $xml_user_info->addChild("$key");
                array_to_xml($value, $subnode);
            }else{
                $subnode = $xml_user_info->addChild("item$key");
                array_to_xml($value, $subnode);
            }
        }else {
            $xml_user_info->addChild("$key",htmlspecialchars("$value"));
        }
    }
}

?>