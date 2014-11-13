<?php
//include_once('./3lib/firebug/fb.php');
//ob_start();
//fb($GLOBALS);

require_once "Class_ModbusTcp.php";
	
/*
	Lectura variables finales terciario
	===================================

	pH_ao_terciario		INT	%MW214	pH agua osmotizada terciario - AIT09001
	Cond_ao_terciario	INT	%MW215	Cloro agua osmotizada terciario - AIT09002
	Cloro_ao_terciario	INT	%MW216	Conductividad agua osmotizada terciario - AIT09003
	Turb_ao_terciario	INT	%MW217	Turbidez agua osmotizada terciario - AIT09004
	Amonio_ao_terciario	INT	%MW218	Amonio agua osmotizada terciario - AIT09005
	Nivel_ao_terciario	INT	%MW219	Nivel deposito agua osmotizada terciario - LT08001

	FACTOR DECIMALES
	pH_ao_terciario			10
	Cond_ao_terciario		100
	Cloro_ao_terciario		100
	Turb_ao_terciario		1000
	Amonio_ao_terciario		1000
	Nivel_ao_terciario		100

*/

	$Variable = array("pH_ao_terciario","Cond_ao_terciario","Cloro_ao_terciario",
			"Turb_ao_terciario","Amonio_ao_terciario","Nivel_ao_terciario");
	$Unidades = array(""," Cond"," Cloro"," Turb"," Amonio"," m");
	$FactorDecimales = array(10,100,100,1000,1000,100);
	$Selector = array("#pH","#Conductividad","#Cloro","#Turbidez","#Amonio","#Nivel");

	$Plc = new ModbusTcp;
	$Plc->Debug = false;
	$Plc->TypeFloat = false;
	$Plc->Simulation = false;
	//$Plc->SetAdIpPLC ("192.168.101.15");// IP directa red Aitasa Clientes
	$Plc->SetAdIpPLC ("95.124.249.45");// IP Router Aitasa ACA --> M340 Aitasa 
	
	$valores = $Plc->ReadModbus( "400215", 6); // Lectura de n registros desde dirección inicial

	// poner la condicion del if a false para JSON, true para HTML
	if (false) {
		echo "Lectura variables Terciario Vilaseca" . "<BR>";
		echo "====================================" . "<BR>";
	}
	
	// para decidir si se envia html o json
	if (false) {
		
		// respuesta en html texto plano
		$i=0;
		foreach ($valores as $v) {
			echo $Variable[$i].' -> '. $v/$FactorDecimales[$i] . $Unidades[$i] . "<BR>";
			$i++;	
		}
	} else {

		// respuesta codificada en JSON
		$i=0;
		$respJSON="{"; // array();
		$flag=false;
		foreach ($valores as $v) {
			$key=$Selector[$i];
			$value=$v/$FactorDecimales[$i]; // . $Unidades[$i];
			if ($flag){$respJSON.=",";}
			$respJSON.= '"' . $key . '":"' . $value . '"';
			$i++;
			$flag=true;
		}
		$respJSON.="}";
	
		print json_encode( $respJSON );
	}
	
	$Plc->ModClose();

?>