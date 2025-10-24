<?php
class IgtechLog{
	var $fecha;
	var $fecha_log;	
	var $ArchivoLog;

	function Abrir(){	
		$this->fecha = getDate();
		$this->ArchivoLog = fopen('ws_debifact_'.$this->fecha['year']
							.str_pad($this->fecha['mon'], 2,'0',STR_PAD_LEFT).
							str_pad($this->fecha['mday'], 2,'0',STR_PAD_LEFT).'_log.txt','a');	
	}

	function EscribirLog($Datos){
		$this->fecha_log =$this->fecha['year'].'/'.str_pad($this->fecha['mon'], 2,'0',STR_PAD_LEFT).'/'.
						str_pad($this->fecha['mday'], 2,'0',STR_PAD_LEFT).' '.
						str_pad($this->fecha['hours'], 2,'0',STR_PAD_LEFT).':'.
						str_pad($this->fecha['minutes'], 2,'0',STR_PAD_LEFT).':'.
						str_pad($this->fecha['seconds'], 2,'0',STR_PAD_LEFT);
		fwrite($this->ArchivoLog, $this->fecha_log.': '.$Datos.chr(10).chr(13));
	}

	function GetArchivo(){
		return $this->ArchivoLog;
	}

	function Cerrar(){
		fclose($this->ArchivoLog);
	}

}
?>