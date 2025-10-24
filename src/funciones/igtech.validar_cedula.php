<?php
function esCedula($ced) {
	$dig = array ();
	if (strlen ( trim ( $ced ) ) == 10) {
		for($i = 0; $i <= 9; $i ++) {
			$dig [$i] = intval ( substr ( $ced, $i, 1 ) );
		}
		$sum = 0;
		for($i = 0; $i <= 8; $i ++) {
			$par = 0;
			if ($i % 2 != 0)
				$sum = $sum + $dig [$i];
			else {
				if (2 * $dig [$i] > 9)
					$par = 2 * $dig [$i] - 9;
				else
					$par = 2 * $dig [$i];
				$sum = $sum + $par;
			}
		}
		$i = 10;
		while ( $i < $sum ) {
			$i = $i + 10;
		}
		if (substr ( $ced, 0, 2 ) != "00") {
			if ($i - $sum == $dig [9])
				return 1;
			else
				return 0;
		} else
			return 0;
	}else{
		return 0;
	}	
}

function EsRUC_natural($identificacion) {
	$dig = array ();
	
	if (strlen ( trim ( $identificacion ) ) == 13) {
		for($i = 0; $i <= 9; $i ++) {
			$dig [$i] = intval ( substr ( $identificacion, $i, 1 ) );
		}
		$sum = 0;
		for($i = 0; $i <= 8; $i ++) {
			$par = 0;
			if ($i % 2 != 0)
				$sum = $sum + $dig [$i];
			else {
				if (2 * $dig [$i] > 9)
					$par = 2 * $dig [$i] - 9;
				else
					$par = 2 * $dig [$i];
				$sum = $sum + $par;
			}
		}
		$i = 10;
		while ( $i < $sum ) {
			$i = $i + 10;
		}
		if (substr ( $identificacion, 0, 2 ) != "00" and substr ( $identificacion, 10, 3 ) == "001") {
			if ($i - $sum == $dig [9])
				return 1;
			else
				return 0;
		} else
			return 0;
	} else
		return 0;
}

function EsRUC_juridico($identificacion) {
	// $Dig = array ();
	// $Prod = array ();
	// $Residuo = 0;
	// $digito = 0;
	
	// if (strlen ( trim ( $identificacion ) ) == 13) {
	// 	for($i = 1; $i <= 10; $i ++)
	// 		$Dig [$i] = intval ( substr ( $identificacion, $i - 1, 1 ) );
		
	// 	$Prod [1] = $Dig [1] * 4;
	// 	$Prod [2] = $Dig [2] * 3;
	// 	$Prod [3] = $Dig [3] * 2;
	// 	$Prod [4] = $Dig [4] * 7;
	// 	$Prod [5] = $Dig [5] * 6;
	// 	$Prod [6] = $Dig [6] * 5;
	// 	$Prod [7] = $Dig [7] * 4;
	// 	$Prod [8] = $Dig [8] * 3;
	// 	$Prod [9] = $Dig [9] * 2;
	// 	$Sum = 0;
	// 	for($i = 1; $i <= 9; $i ++)
	// 		$Sum = $Sum + $Prod [$i];
	// 	$Residuo = $Sum % 11;
	// 	$digito = 11 - $Residuo;
	// 	if ($Residuo == 0)
	// 		$digito = 0;
		
	// 	if (substr ( $identificacion, 0, 2 ) != "00" and substr ( $identificacion, 10, 3 ) == "001" ) {
	// 		if ($digito == $Dig [10])
	// 			return 1;
	// 		else
	// 			return 0;
	// 	} else
	// 		return 0;
	// } else
	// 	return 0;
		return 1;
}

function EsRUC_Publica($identificacion) {
	$Dig = array ();
	$Prod = array ();
	$Residuo = 0;
	$digito = 0;
	
	if (strlen ( trim ( $identificacion ) ) == 13) {
		for($i = 1; $i <= 10; $i ++)
			$Dig [$i] = intval ( substr ( $identificacion, $i - 1, 1 ) );
		
		$Prod [1] = $Dig [1] * 3;
		$Prod [2] = $Dig [2] * 2;
		$Prod [3] = $Dig [3] * 7;
		$Prod [4] = $Dig [4] * 6;
		$Prod [5] = $Dig [5] * 5;
		$Prod [6] = $Dig [6] * 4;
		$Prod [7] = $Dig [7] * 3;
		$Prod [8] = $Dig [8] * 2;
		$Prod [9] = $Dig [9] * 1;
		$Sum = 0;
		for($i = 1; $i <= 9; $i ++)
			$Sum = $Sum + $Prod [$i];
		$Residuo = $Sum % 11;
		$digito = 11 - $Residuo;
		if ($Residuo == 0)
			$digito = 0;
		
		if (substr ( $identificacion, 0, 2 ) != "00" and substr ( $identificacion, 10, 3 ) == "001") {
			if ($digito == $Dig [10])
				return 1;
			else
				return 0;
		} else
			return 0;
	} else
		return 0;
}

function validar_CedulaRuc($ced,$tipo){
	$validador=0;
	if (strlen ( trim ( $ced ) ) == 10 and $tipo=='CEDULA'){
		$validador=esCedula($ced);
	}
	if(strlen ( trim ( $ced ) ) == 13 and $tipo=='RUC'){
		if(intval ( substr ( $ced, 2, 1 ) )< 6){
			$validador=EsRUC_natural($ced);	
		}else{
			if(intval ( substr ( $ced, 2, 1 ) )== 6){
				$validador=EsRUC_Publica($ced);
				if($validador==0){
					$validador=EsRUC_natural($ced);
				}	
			}else{
				if(intval ( substr ( $ced, 2, 1 ) )== 9){
					$validador=EsRUC_juridico($ced);	
				}else{
					$validador=0;
				}	
			}
		}
	}	
	return $validador;
}
?>