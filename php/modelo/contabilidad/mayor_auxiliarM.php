<?php
error_reporting(-1);
//include(dirname(__DIR__).'/funciones/funciones.php');//
include(dirname(__DIR__,2).'/db/variables_globales.php');//
 class mayor_auxiliarM
 {
 	private $conn;
 	function __construct()
 	{

		// $this->conn = cone_ajax();
    $this->conn = new db();

 	}
  function cuentas_($ini,$fin)
  {
  		
  	$sql= "SELECT Codigo, Codigo+'    '+Cuenta As Nombre_Cta 
          FROM Catalogo_Cuentas
          WHERE DG = 'D'";
          if(!empty($ini) && !empty($fin))
          {
          $sql.=" and Codigo BETWEEN '".$ini."' and '".$fin."' ";
          } 
          $sql.= "AND Cuenta <> '".G_NINGUNO."' 
          AND Item = '".$_SESSION['INGRESO']['item']."' 
          AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
          ORDER BY Codigo ";

          $result = $this->conn->datos($sql);
           return $result;

  }

   function cuentas_filtrado($ini,$fin)
  {
  		
  		if($ini =='')
  		{
  			$ini = 1;
  		}
  		if($fin == '')
  		{
  			$fin = $ini;
  		}
  		$sql ="SELECT Codigo, Codigo+'    '+Cuenta As Nombre_Cta 
       FROM Catalogo_Cuentas 
       WHERE DG = 'D'
        AND Cuenta <> '".G_NINGUNO."' 
        AND Item = '".$_SESSION['INGRESO']['item']."' 
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        ORDER BY Codigo ";
        // print($sql);
        $result = $this->conn->datos($sql);
           return $result;

  }


 function consultar_cuentas_($OpcUno,$PorConceptos,$cuentaini,$cuentafin,$desde,$hasta,$DCCtas,$CheckAgencia,$DCAgencia,$Checkusu,$DCUsuario)
 {

 	
 	$totales = $this->consulta_totales($OpcUno,$PorConceptos,$cuentaini,$cuentafin,$desde,$hasta,$DCCtas,$CheckAgencia,$DCAgencia,$Checkusu,$DCUsuario);
 	//print_r($PorConceptos);
 	if($cuentaini=='')
 	{
 		$cuentaini = 1;
 	}
 	if($cuentafin == '')
 	{
 		$cuentafin = 9;
 	}

 	if($PorConceptos=='true')
 	{
 		$sql =  "SELECT T.Fecha,T.TP,T.Numero,Cl.Cliente,T.Detalle As Concepto,T.Cheq_Dep,T.Debe,T.Haber,T.Saldo,
          T.Parcial_ME,T.Saldo_ME,T.ID,T.Cta,T.Item FROM Transacciones As T,Clientes As Cl ";
 	}else
 	{
 		 $sql = "SELECT T.Fecha,T.TP,T.Numero,Cl.Cliente,C.Concepto,T.Cheq_Dep,Debe,Haber,Saldo,
          Parcial_ME,Saldo_ME,T.ID,T.Cta,T.Item FROM Transacciones As T,Comprobantes As C,Clientes As Cl ";

 	}
 	
 	$sql.="WHERE T.Fecha BETWEEN '".$desde."' and '".$hasta."' AND T.T = '".G_NORMAL."' AND T.Periodo = '".$_SESSION['INGRESO']['periodo']."' ";

    if($OpcUno == 'true' && $cuentaini =='' && $cuentafin=='')
    {
       $sql.=" AND T.Cta = '".$DCCtas."'";
    }else
    {
    	$sql.= "AND T.Cta BETWEEN '".$cuentaini."' AND '".$cuentafin."' ";
    }
    if($CheckAgencia == 'true')
   {
   	 $sql.= " AND T.Item = '".$DCAgencia."' ";
   }else
   {
   	$sql.= "AND T.Item = '".$_SESSION['INGRESO']['item']."' ";
   }
 if($PorConceptos == 'true')
  {
  	$sql .= "AND T.Codigo_C = Cl.Codigo ";
  }else
  {
  	if($Checkusu == 'true')
  	{
  		$sql.=  "AND C.CodigoU = '".$DCUsuario."' AND C.Codigo_B = Cl.Codigo AND T.TP = C.TP AND T.Numero = C.Numero AND T.Periodo = C.Periodo AND T.Fecha = C.Fecha AND T.Item = C.Item ";
  	}else
  	{
  		 $sql.=  "AND C.Codigo_B = Cl.Codigo AND T.TP = C.TP AND T.Numero = C.Numero AND T.Periodo = C.Periodo AND T.Fecha = C.Fecha AND T.Item = C.Item ";

  	}
  }
  $sql.= "ORDER BY T.Cta,T.Fecha,T.TP,T.Numero,Debe DESC,Haber,T.ID ";
 // print_r($DCAgencia);print_r($CheckAgencia);
  // print_r($sql);
// die();

    $medida = medida_pantalla($_SESSION['INGRESO']['Height_pantalla'])-144;
    $tbl = grilla_generica_new($sql,'Transacciones As T,Comprobantes As C,Clientes As Cl ','tbl_may',false,$botones=false,$check=false,$imagen=false,$border=1,$sombreado=1,$head_fijo=1,$medida);

       return $tbl;
  
 }

function consultar_cuentas_datos($OpcUno,$PorConceptos,$cuentaini,$cuentafin,$desde,$hasta,$DCCtas,$CheckAgencia,$DCAgencia,$Checkusu,$DCUsuario)
 {

 	
 	$totales = $this->consulta_totales($OpcUno,$PorConceptos,$cuentaini,$cuentafin,$desde,$hasta,$DCCtas,$CheckAgencia,$DCAgencia,$Checkusu,$DCUsuario);
 	//print_r($PorConceptos);
 	if($cuentaini=='')
 	{
 		$cuentaini = 1;
 	}
 	if($cuentafin == '')
 	{
 		$cuentafin = 9;
 	}

 	if($PorConceptos=='true')
 	{
 		$sql =  "SELECT T.Fecha,T.TP,T.Numero,Cl.Cliente,T.Detalle As Concepto,T.Cheq_Dep,T.Debe,T.Haber,T.Saldo,
          T.Parcial_ME,T.Saldo_ME,T.ID,T.Cta,T.Item FROM Transacciones As T,Clientes As Cl ";
 	}else
 	{
 		 $sql = "SELECT T.Fecha,T.TP,T.Numero,Cl.Cliente,C.Concepto,T.Cheq_Dep,Debe,Haber,Saldo,
          Parcial_ME,Saldo_ME,T.ID,T.Cta,T.Item FROM Transacciones As T,Comprobantes As C,Clientes As Cl ";

 	}
 	
 	$sql.="WHERE T.Fecha BETWEEN '".$desde."' and '".$hasta."' AND T.T = '".G_NORMAL."' AND T.Periodo = '".$_SESSION['INGRESO']['periodo']."' ";

    if($OpcUno == 'true' && $cuentaini == '' && $cuentafin=='')
    {
       $sql.=" AND T.Cta = '".$DCCtas."'";
    }else
    {
    	$sql.= "AND T.Cta BETWEEN '".$cuentaini."' AND '".$cuentafin."' ";
    }
    if($CheckAgencia == 'true')
   {
   	 $sql.= " AND T.Item = '".$DCAgencia."' ";
   }else
   {
   	$sql.= "AND T.Item = '".$_SESSION['INGRESO']['item']."' ";
   }
 if($PorConceptos == 'true')
  {
  	$sql .= "AND T.Codigo_C = Cl.Codigo ";
  }else
  {
  	if($Checkusu == 'true')
  	{
  		$sql.=  "AND C.CodigoU = '".$DCUsuario."' AND C.Codigo_B = Cl.Codigo AND T.TP = C.TP AND T.Numero = C.Numero AND T.Periodo = C.Periodo AND T.Fecha = C.Fecha AND T.Item = C.Item ";
  	}else
  	{
  		 $sql.=  "AND C.Codigo_B = Cl.Codigo AND T.TP = C.TP AND T.Numero = C.Numero AND T.Periodo = C.Periodo AND T.Fecha = C.Fecha AND T.Item = C.Item ";

  	}
  }
  $sql.= "ORDER BY T.Cta,T.Fecha,T.TP,T.Numero,Debe DESC,Haber,T.ID DESC";
 // print_r($DCAgencia);print_r($CheckAgencia);
//   print_r($sql);
// die();
   $result = $this->conn->datos($sql);
           return $result;
  
 }

 function consultatr_submodulos($FechaIni,$FechaFin,$CheckAgencia,$DCAgencia,$CheckUsuario,$DCUsuario)
 {
 	 
    $sql = "SELECT T.Fecha,T.TP,T.Numero,C.Cliente,T.Cta,T.TC,T.Factura,T.Debitos,T.Creditos,T.Prima
        FROM Trans_SubCtas As T,Clientes As C 
        WHERE T.Fecha BETWEEN '".$FechaIni."' and '".$FechaFin."' 
       AND T.Periodo = '".$_SESSION['INGRESO']['periodo']."' AND T.TC='P' ";

   if($CheckAgencia=='true')
  {
    if($DCAgencia=='')
    {     
      $sql.="AND T.Item = '".$_SESSION['INGRESO']['item']."' ";
    }else
    {
      $sql.= "AND T.Item = '".$DCAgencia."' ";
    }

  }else
  {
    $sql.="AND T.Item = '".$_SESSION['INGRESO']['item']."' ";
  }

  
   if($CheckUsuario=='true')
  {
    $sql.= "AND T.CodigoU = '".$DCUsuario."' ";
  }
  
  $sql .="AND T.Codigo = C.Codigo
       UNION
       SELECT T.Fecha,T.TP,T.Numero,Detalle As Cliente,T.Cta,T.TC,T.Factura,T.Debitos,T.Creditos,T.Prima
       FROM Trans_SubCtas As T,Catalogo_SubCtas As C 
        WHERE T.Fecha BETWEEN '".$FechaIni."' and '".$FechaFin."' AND T.TC='P' 
       AND T.Periodo = '".$_SESSION['INGRESO']['periodo']."' ";

    if($CheckAgencia=='true')
  {
    if($DCAgencia=='')
    {     
      $sql.="AND T.Item = '".$_SESSION['INGRESO']['item']."' ";
    }else
    {
      $sql.= "AND T.Item = '".$DCAgencia."' ";
    }

  }else
  {
    $sql.="AND T.Item = '".$_SESSION['INGRESO']['item']."' ";
  }

    if($CheckUsuario=='true')
  {
    $sql.= "AND T.CodigoU = '".$DCUsuario."' ";
  }
 
  $sql.= "AND T.Item = C.Item 
       AND T.Periodo = C.Periodo 
       AND T.Codigo = C.Codigo 
       ORDER BY T.Fecha,T.TP,T.Numero,T.Cta,T.Factura ";
       // print_r($sql);die();

      $result = $this->conn->datos($sql);
           return $result;
 }
 function consulta_totales($OpcUno,$PorConceptos,$cuentaini,$cuentafin,$desde,$hasta,$DCCtas,$CheckAgencia,$DCAgencia,$Checkusu,$DCUsuario)
 {
 	
  $SumaDebe = 0; $SumaHaber = 0; $Suma_ME = 0; $SaldoTotal = 0;
 	$sql = "SELECT T.Cta,SUM(T.Debe) As TDebe, SUM(T.Haber) As THaber, SUM(T.Parcial_ME) As TParcial_ME ";
 	if($PorConceptos=='true')
 	{
 		 $sql.="FROM Transacciones As T,Clientes As Cl ";
 	}else
 	{
 		$sql.="FROM Transacciones As T,Comprobantes As C,Clientes As Cl ";
 	}

 	$sql.="WHERE T.Fecha BETWEEN '".$desde."' AND '".$hasta."' AND T.T = '".G_NORMAL."' AND T.Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
 	if($OpcUno == 'true')
    {
       $sql.=" AND T.Cta = '".$DCCtas."'";
    }else
    {
    	$sql.= "AND T.Cta BETWEEN '".$cuentaini."' AND '".$cuentafin."' ";
    }
     if($CheckAgencia == 'true')
   {
   	 $sql.= " AND T.Item = '".$DCAgencia."' ";
   }else
   {
   	$sql.= "AND T.Item = '".$_SESSION['INGRESO']['item']."' ";
   }
   if($PorConceptos == 'true')
  {
  	$sql .= "AND T.Codigo_C = Cl.Codigo ";
  }else
  {
  	if($Checkusu == 'true')
  	{
  		$sql.=  "AND C.CodigoU = '".$DCUsuario."' AND C.Codigo_B = Cl.Codigo AND T.TP = C.TP AND T.Numero = C.Numero AND T.Periodo = C.Periodo AND T.Fecha = C.Fecha AND T.Item = C.Item ";
  	}else
  	{
  		 $sql.=  "AND C.Codigo_B = Cl.Codigo AND T.TP = C.TP AND T.Numero = C.Numero AND T.Periodo = C.Periodo AND T.Fecha = C.Fecha AND T.Item = C.Item ";

  	}
  }
  $sql.="GROUP BY T.Cta ORDER BY T.Cta ";
// print_r($sql);
 //die();
	$result = $this->conn->datos($sql);
           return $result;
 }

 function exportar_excel($parametros,$sub)
 {
    $result = array();$submodulo=array();
     $desde = str_replace('-','',$parametros['desde']);
     $hasta = str_replace('-','',$parametros['hasta']);
     $result = $this->consultar_cuentas_datos($parametros['OpcUno'],$parametros['PorConceptos'],$parametros['txt_CtaI'],$parametros['txt_CtaF'],$desde,$hasta,$parametros['DCCtas'],$parametros['CheckAgencia'],$parametros['DCAgencia'],$parametros['CheckUsu'],$parametros['DCUsuario']);
     if($sub != 'false')
       {        
        $submodulo = $this->consultatr_submodulos($desde,$hasta,$parametros['CheckAgencia'],$parametros['DCAgencia'],$parametros['CheckUsu'],$parametros['DCUsuario']);
       }

       // print_r($sub);die();

     $b = 1;
     $titulo='MAYOR AUXILIAR NORMAL';
     $tablaHTML =array();
     $tablaHTML[0]['medidas']=array(20,10,20,40,18,18,18,18);
     $tablaHTML[0]['datos']=array('FECHA','TD','NUMERO','CONCEPTO','PARCIAL_ME','DEBE','HABER','SALDO');
     $tablaHTML[0]['tipo'] ='C';
     $pos = 1;
     $debe=0;$haber = 0;$saldo=0; $mes='';$mesAc='';
    foreach ($result as $key => $value) {
          $mes = $value['Fecha']->format('n');
          // print_r($mes);die();
          if($mes!=$mesAc)
          { 
               if($pos!=1)
              {           
                 $tablaHTML[$pos]['medidas']=$tablaHTML[0]['medidas'];
                  $tablaHTML[$pos]['datos']=array('Fin de:'.mes_X_nombre($mesAc),'Total','','','',$desde,$haber,$saldo);
                  $tablaHTML[$pos]['tipo'] ='SUBR';          
                  $pos+=1;
                  $debe= 0;
                  $haber= 0;
                  $saldo= 0;          
              }
             
               $tablaHTML[$pos]['medidas']=$tablaHTML[0]['medidas'];
                $tablaHTML[$pos]['datos']=array('Inicio de:'.mes_X_nombre($mes),'','','','','','','');
                $tablaHTML[$pos]['tipo'] ='SUB';          
                $pos+=1;  
                $mesAc = $mes;
          }

          $tablaHTML[$pos]['medidas']=$tablaHTML[0]['medidas'];
          $tablaHTML[$pos]['datos']=array($value['Fecha']->format('Y-m-d'),$value['TP'],$value['Numero'],$value['Cliente'],'','','','');
          $tablaHTML[$pos]['tipo'] ='N';          
          $pos+=1;

          $tablaHTML[$pos]['medidas']=$tablaHTML[0]['medidas'];
          $tablaHTML[$pos]['datos']=array('','','',$value['Concepto'],$value['Parcial_ME'],$value['Debe'],$value['Haber'],$value['Saldo']);
          $tablaHTML[$pos]['tipo'] ='N';          
          $pos+=1;
          foreach ($submodulo as $key2 => $value2) {
            if($value2['Numero']==$value['Numero'])
             {
                  if($value2['Debitos'] == 0)
                  {
                    $parcial = $value2['Creditos'];
                  }else
                  {
                    $parcial = $value2['Debitos'];
                  }

                  $tablaHTML[$pos]['medidas']=$tablaHTML[0]['medidas'];
                  $tablaHTML[$pos]['datos']=array('','','','*'.$value2['Cliente'],$parcial,'','','');
                  $tablaHTML[$pos]['tipo'] ='N';          
                  $pos+=1;
              }
          }
        $debe+= $value['Debe'];
        $haber+=$value['Haber'];
        $saldo+=$value['Saldo']; 
        $pos+=1;
         

    }
      excel_generico($titulo,$tablaHTML);  



 //  	$desde = str_replace('-','',$parametros['desde']);
	// $hasta = str_replace('-','',$parametros['hasta']);
	// $result = $this->consultar_cuentas_datos($parametros['OpcUno'],$parametros['PorConceptos'],$parametros['txt_CtaI'],$parametros['txt_CtaF'],$desde,$hasta,$parametros['DCCtas'],$parametros['CheckAgencia'],$parametros['DCAgencia'],$parametros['CheckUsu'],$parametros['DCUsuario']);
	//  if($sub != 'false')
 //       {       	
  		// $submodulo = $this->consultatr_submodulos($desde,$hasta,$parametros['CheckAgencia'],$parametros['DCAgencia'],$parametros['CheckUsu'],$parametros['DCUsuario']);
 //  	   }else
 //  	   {
 //  	   	$submodulo=array();
 //  	   }
  	   
	// //print_r($result);
 // 	// exportar_excel_mayor_auxi($result,$submodulo,'Mayor Auxiliar',null,null,null);  
 //  excel_file_mayor_auxi($result,$submodulo,'Mayor Auxiliar',null,null,null);  
  }
 

 } 
?>