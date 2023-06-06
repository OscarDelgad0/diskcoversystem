<?php
/**
 * Autor: Diskcover System.
 * Mail:  diskcover@msn.com
 * web:   www.diskcoversystem.com
 * distribuidor: PrismaNet Profesional S.A.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
if(!isset($_SESSION)) 
	{ 		
			@session_start();
	}
//require_once("../../lib/excel/plantilla.php");
require_once(dirname(__DIR__,2)."/lib/excel/plantilla.php");
require_once(dirname(__DIR__,1)."/db/db1.php");
require_once(dirname(__DIR__,1)."/db/variables_globales.php");
require_once(dirname(__DIR__,1)."/comprobantes/SRI/autorizar_sri.php");


if(isset($_POST['RUC']) AND !isset($_POST['submitweb'])) 
{
	$pag=$_POST['vista'];
	$ruc=$_POST['RUC'];
	$idMen=$_POST['idMen'];
	$item=$_POST['item'];
  Digito_verificador($ruc);
}

function ip()
{
  // print_r($_SESSION);die();
   $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('HTTP_X_REAL_IP'))
        $ip_address = getenv('HTTP_X_REAL_IP');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
  return $ipaddress;

}

function Empresa_data()
 {
  $conn = new db();
  $cid= $conn->conexion();
   $sql = "SELECT * FROM Empresas where Item='".$_SESSION['INGRESO']['item']."'";
   $stmt = sqlsrv_query($cid, $sql);
    if( $stmt === false)  
      {  
       echo "Error en consulta PA.\n";  
       return '';
       die( print_r( sqlsrv_errors(), true));  
      }

    $result = array();  
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) 
      {
      $result[] = $row;
      //echo $row[0];
      }

     // $result =  encode($result);
      // print_r($result);
      return $result;
 }

 function Cliente($cod,$grupo = false,$query=false,$clave=false)
 {
   $conn = new db();
   $cid= $conn->conexion();
   $sql = "SELECT * from Clientes WHERE 1=1";
   if($cod){
    $sql.=" and Codigo= '".$cod."'";
   }
   if($grupo)
   {
    $sql.=" and Grupo= '".$grupo."'";
   }
   if($query)
   {
    $sql.=" and Cliente +' '+ CI_RUC like '%".$query."%'";
   }
   if($clave)
   {
    $sql.=" and Clave= '".$clave."'";
   }

   $sql.=" ORDER BY ID OFFSET 0 ROWS FETCH NEXT 25 ROWS ONLY;";
  

   // print_r($sql);
   $stmt = sqlsrv_query($cid, $sql);
    if( $stmt === false)  
      {  
       echo "Error en consulta PA.\n";  
       return '';
       die( print_r( sqlsrv_errors(), true));  
      }

    $result = array();  
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) 
      {
      $result[] = $row;
      //echo $row[0];
      }

     // $result =  encode($result);
      // print_r($result);
      return $result;
 }




// clave aleatoria 
function generate_clave($strength = 16) {
  $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($permitted_chars);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
 
    return $random_string;
}



//----------------------------------- fin funciones en duda--------------------------- 
//Configuración del algoritmo de encriptación

//Debes cambiar esta cadena, debe ser larga y unica
//nadie mas debe conocerla
$clave  = 'Una cadena, muy, muy larga para mejorar la encriptacion';

//Metodo de encriptación
$method = 'aes-256-cbc';

// Puedes generar una diferente usando la funcion $getIV()
$iv = base64_decode("C9fBxl1EWtYTL1/M8jfstw==");

 /*
 Encripta el contenido de la variable, enviada como parametro.
  */
 $encriptar = function ($valor) use ($method, $clave, $iv) {
     return openssl_encrypt ($valor, $method, $clave, false, $iv);
 };

 /*
 Desencripta el texto recibido
 */
 $desencriptar = function ($valor) use ($method, $clave, $iv) {
     $encrypted_data = base64_decode($valor);
     return openssl_decrypt($valor, $method, $clave, false, $iv);
 };

 /*
 Genera un valor para IV
 */
 $getIV = function () use ($method) {
     return base64_encode(openssl_random_pseudo_bytes(openssl_cipher_iv_length($method)));
 };

//----------------------------------- fin funciones en duda--------------------------- 

function Eliminar_Empresa_SP($Item, $NombreEmpresa=false)
{
  $conn = new db();
  $parametros = array(
    array(&$Item, SQLSRV_PARAM_IN),
  );
  // print_r('...'.$parametros);die();
  $sql = "EXEC sp_Eliminar_Empresa @Item= ?";
  $res = $conn->ejecutar_procesos_almacenados($sql,$parametros,$tipo=false);
  //print_r($res);die();
  return $res;
}

function Tipo_Contribuyente_SP_MYSQL($CI_RUC)
{
  $agente = 'Agente';
  $micro = 'micro';
  $conn = new db();
  $parametros = array(
    array($CI_RUC,'IN'),
    array($agente,'OUT'),
    array($micro,'OUT'),
  );
  $sql = "CALL sp_tipo_contribuyente";
  $res = $conn->ejecutar_procesos_almacenados($sql,$parametros,$respuesta='1',$tipo='MYSQL');
  //print_r($res);die();
  return $res;
}

function Actualizar_Datos_ATS_SP($Items,$MBFechaI,$MBFechaF,$Numero) //-------------optimizado javier farinango
{

    $conn = new db();
    $respuesta = 1;
    $conn = new db();
    $FechaIni = $MBFechaI;
    $FechaFin = $MBFechaF;
    $parametros = array(
      array(&$Items, SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$FechaIni, SQLSRV_PARAM_IN),
      array(&$FechaFin, SQLSRV_PARAM_IN),
      array(&$Numero, SQLSRV_PARAM_IN)
    );
    // print_r($parametros);die();
    $sql = "EXEC sp_Actualizar_Datos_ATS @Item= ?,@Periodo=?,@FechaDesde=?,@FechaHasta=?,@Numero=?";
    $res = $conn->ejecutar_procesos_almacenados($sql,$parametros);
    // print_r($res);die();
    return $res;
}

function sp_Reporte_Cartera_Clientes($CodigoCliente,$desde,$hasta)
{
    $desde = str_replace('-','',$desde);
    $hasta = str_replace('-','',$hasta);
    $conn = new db();
    $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['CodigoU'], SQLSRV_PARAM_IN),
      array(&$CodigoCliente, SQLSRV_PARAM_IN),
      array(&$desde, SQLSRV_PARAM_IN),
      array(&$hasta, SQLSRV_PARAM_IN)

    );
    $sql = "EXEC  sp_Reporte_Cartera_Clientes @Item= ?,@Periodo=?,@CodigoUsuario=?,@CodigoCliente=?,@FechaInicio=?,@FechaCorte=?";
    return $conn->ejecutar_procesos_almacenados($sql,$parametros,$tipo=false);
}

function Leer_Datos_Cliente_SP($BuscarCodigo)
{

    $conn = new db();
    $BuscarCodigo1 = '';
   $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$BuscarCodigo, SQLSRV_PARAM_IN),
      array(&$BuscarCodigo1, SQLSRV_PARAM_INOUT)
    );
    $sql = "EXEC  sp_Leer_Datos_Cliente @Item= ?,@Periodo=?,@Codigo_CIRUC_Cliente=?,@Codigo_Encontrado=?";
    return $conn->ejecutar_procesos_almacenados($sql,$parametros,$tipo=false);
}

function sp_Actualizar_Saldos_Facturas($TC,$serie,$factura)
{

    $conn = new db();
   $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$TC, SQLSRV_PARAM_IN),
      array(&$serie,SQLSRV_PARAM_IN),
      array(&$factura,SQLSRV_PARAM_IN),
    );
    $sql = "EXEC  sp_Actualizar_Saldos_Facturas @Item= ?,@Periodo=?,@TC=?,@Serie=?,@Factura=?";
    return $conn->ejecutar_procesos_almacenados($sql,$parametros,$tipo=false);
}



function sp_Reporte_CxCxP_x_Meses($cta,$fecha)
{

  $conn = new db();
   $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$cta, SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['CodigoU'], SQLSRV_PARAM_IN),
      array(&$fecha, SQLSRV_PARAM_IN)
    );

   // print_r($parametros);die();
    $sql = "EXEC  sp_Reporte_CxCxP_x_Meses @Item=?,@Periodo=?,@Cta=?,@CodigoUsuario=?,@FechaCorte=?";
    $conn->ejecutar_procesos_almacenados($sql,$parametros);
}


function Leer_Codigo_Inv_SP($BuscarCodigo,$FechaInventario,$CodBodega,$CodMarca,$CodigoDeInv)
{

  //devuelve datos de sp
    $conn = new db();
    $FechaKardex = $FechaInventario;
    $CodigoDeInv = G_NINGUNO;
    // Iniciar_Stored_Procedure "", MiSQL, MiCmd, MiReg
    // MiCmd.CommandText = "sp_Leer_Codigo_Inv"

     $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$BuscarCodigo, SQLSRV_PARAM_IN),
      array(&$FechaKardex, SQLSRV_PARAM_IN),
      array(&$CodBodega, SQLSRV_PARAM_IN),
      array(&$CodMarca, SQLSRV_PARAM_IN),
      array(&$CodigoDeInv, SQLSRV_PARAM_INOUT)
      );     
     $sql="EXEC sp_Leer_Codigo_Inv @Item=?, @Periodo=?, @BuscarCodigo=?, @FechaInventario=?, @CodBodega=?, @CodMarca=?, @CodigoDeInv=? ";
     // print_r($_SESSION['INGRESO']);die();}
      $respuesta = $conn->ejecutar_procesos_almacenados($sql,$parametros);
      if($respuesta==1)
      {
        return $CodigoDeInv;
      }
      return $respuesta;   

}


function Fecha_Del_AT($ATMes, $ATAno)
{
$fechas_ats = array();
$FechaInicial='';$FechaMitad='';$FechaFinal='';
if($ATMes == 'Todos')
{
  $FechaInicial='01/01/'.$ATAno;
  $FechaMitad = '15/01/'.$ATAno;
  $FechaFinal =date('dd/mm/yyy');
  // $fechas_ats = array('FechaIni'=>$FechaInicial,'FechaMit'=>$FechaMitad,'FechaFin'=>$FechaFin);
}else
{
  
     $FechaInicial='01/'.$ATMes.'/'.$ATAno;
     $FechaMitad = '15/'.$ATMes.'/'.$ATAno;
     $FechaFinal = date("d",(mktime(0,0,0,$ATMes+1,1,$ATAno)-1)).'/'.$ATMes.'/'.$ATAno;     
     // $fechas_ats = array('FechaIni'=>$FechaInicial,'FechaMit'=>$FechaMitad,'FechaFin'=>$FechaFinal);
 }
 if($_SESSION['INGRESO']['Tipo_Base'] == 'SQL SERVER')
  {
    $mitad = DateTime::createFromFormat('d/m/Y', $FechaMitad);
    $final = DateTime::createFromFormat('d/m/Y', $FechaFinal);
    $inicial = DateTime::createFromFormat('d/m/Y',$FechaInicial);

       $FechaInicial=$inicial->format('Ymd');
       $FechaMitad=$mitad->format('Ymd');
       $FechaFinal=$final->format('Ymd');


  }else
  {
    $FechaInicial=date('Y-m-d',strtotime($FechaInicial));
    $FechaMitad = date('Y-m-d',strtotime($FechaMitad));
    $FechaFinal = date('Y-m-d',strtotime($FechaFinal));

  }
  $fechas_ats = array('FechaIni'=>$FechaInicial,'FechaMit'=>$FechaMitad,'FechaFin'=>$FechaFinal);
  return $fechas_ats;
}

function copiar_tabla_empresa($NombreTabla,$OldItemEmpresa,$PeriodoCopy,$si_periodo,$AdoStrCnnCopy=false,$NoBorrarTabla=false) //optimizado
{

 $conn = new db();

 $NombreTabla = trim($NombreTabla);
 $campos_db = dimenciones_tabla($NombreTabla);

 // 'Borramos datos si existen en la empresa nueva'
 if($NoBorrarTabla)
 {
   $sqld = "DELETE  FROM ".$NombreTabla." WHERE Item = '".$_SESSION['INGRESO']['item']."' ";
   if($si_periodo)
   {
    if($PeriodoCopy !='.')
    {
       $sqld .= " AND Periodo = '".$PeriodoCopy."' ";
    }else
    {
      $sqld .=" AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
    }
   }
 }

 $conn->String_Sql($sql);

  if($PeriodoCopy == '.')
  {
    $PeriodoCopy = date('Y');
  }

 
$tabla_sistema = '';
foreach ($campos_db as $key => $value) {
  if($value->COLUMN_NAME !='ID' && $value->COLUMN_NAME !='Periodo' &&$value->COLUMN_NAME !='Item')
  {
    $tabla_sistema.=$value->COLUMN_NAME.',';
  }
}
$tabla_sistema = substr($tabla_sistema,0,-1);


   $sql1 = "select  '".$_SESSION['INGRESO']['item']."','".$_SESSION['INGRESO']['periodo']."',".$tabla_sistema." FROM ".$NombreTabla."  WHERE Item = '".$OldItemEmpresa."'";
   $sql = "INSERT INTO ".$NombreTabla." (Item,Periodo,".$tabla_sistema.") ";
         if($si_periodo)
          {
            if(checkdate('12',$PeriodoCopy, '31'))
              {
                $sql1 .= " AND Periodo = '".$PeriodoCopy."' ";
              }
            else
              {
                $sql1 .=" AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
              }
          }else
          {
             $sql1 = "select '".$_SESSION['INGRESO']['item']."',".$tabla_sistema." FROM ".$NombreTabla."  WHERE Item = '".$OldItemEmpresa."'";
            $sql = "INSERT INTO ".$NombreTabla." (Item,".$tabla_sistema.") ";
          }
           $sql = $sql.$sql1;
         return  $conn->String_Sql($sql);

}

function Rubro_Rol_Pago($Detalle_Rol)
{


	$Rubro_Rol_Pago = '';
	$cod = array();
	$Det_Rol = str_replace( ".", "",$Detalle_Rol);
    $Det_Rol = str_replace( "/", "",$Det_Rol);
    $Det_Rol = str_replace( "Á", "A",$Det_Rol);
    $Det_Rol = str_replace( "É", "E",$Det_Rol);
    $Det_Rol = str_replace( "Í", "I",$Det_Rol);
    $Det_Rol = str_replace( "Ó", "O",$Det_Rol);
    $Det_Rol = str_replace( "Ú", "U",$Det_Rol);
    $Det_Rol = str_replace( "Ñ", "N",$Det_Rol);
    $Det_Rol = str_replace( "á", "a",$Det_Rol);
    $Det_Rol = str_replace( "é", "e",$Det_Rol);
    $Det_Rol = str_replace( "í", "i",$Det_Rol);
    $Det_Rol = str_replace( "ó", "o",$Det_Rol);
    $Det_Rol = str_replace( "ú", "u",$Det_Rol);
    $Det_Rol = str_replace( "ñ", "n",$Det_Rol);

    $cod = explode(' ', $Det_Rol);

    // $cod[0] = trim($Det_Rol);
    // $Det_Rol =substr($Det_Rol,strlen($cod[0])+1,strlen($Det_Rol));
    // $cod[1] = trim($Det_Rol);
    // $Det_Rol =substr($Det_Rol,strlen($cod[1])+1,strlen($Det_Rol));
    // $cod[2] = trim($Det_Rol);
    // $Det_Rol =substr($Det_Rol,strlen($cod[2])+1,strlen($Det_Rol));
    // $cod[3] = trim($Det_Rol);


    $Det_Rol = '';
    // if(strlen(trim($cod[0]))>=2)
    // {
    // 	$Det_Rol = $Det_Rol.''.trim(substr($cod[0],0,3)).'_';
    // }      
    foreach ($cod as $key => $value) {
    	if(strlen(trim($value))>=2)
    	{
    		if($key == 0)
    		{
    			$Det_Rol .=trim(substr($value, 0, 3))."_";
    		}else
    		{
    			$Det_Rol .=trim(substr($value, 0, 2))."_";
    		}   		
    		 
    	}

    }     $Det_Rol = trim(substr($Det_Rol, 0,-1));
    // $Rubro_Rol_Pago = $Det_Rol;
   $Rubro_Rol_Pago = $Det_Rol;
    return $Rubro_Rol_Pago;

}



function ReadSetDataNum($SQLs,$ParaEmpresa =false,$Incrementar = false) // optimizado por javier farinango // pendiente a revicion repetida
{
  $result = '';
  $NumCodigo = 0;
  $NuevoNumero = False;
  $FechaComp = '';
  $Si_MesComp = false;
  if(strlen($FechaComp) < 10 || $FechaComp == '00/00/0000')
  {
  	$FechaComp =date('Y-m-d');
  }

  if($ParaEmpresa)
  {
  	$NumEmpA = $_SESSION['INGRESO']['item'];
  }else
  {
  	$NumEmpresa = '000';
  }

  $Num_Meses_CI=true;
  $Num_Meses_CD=true;  
  $Num_Meses_CE=true;
  $Num_Meses_ND=true;
  $Num_Meses_NC=true;
    
// print_r($FechaComp);die();
  if($SQLs != '')
  {
    $MesComp = '';
    if(strlen($FechaComp) >= 10)
    {
    	$MesComp = date('m');;
    }
    if($MesComp == '')
    {
    	$MesComp = '01';
    }
    if($Num_Meses_CD and $SQLs == 'Diario')
    {
    	$SQLs = $MesComp.''.$SQLs;
      $Si_MesComp = True;
    }
    if($Num_Meses_CI and $SQLs == 'Ingresos')
    {
    	$SQLs = $MesComp.''.$SQLs;
      $Si_MesComp = True;
    }
    if($Num_Meses_CE and $SQLs == 'Egresos')
    {
    	$SQLs = $MesComp.''.$SQLs;
      $Si_MesComp = True;
    }
    if($Num_Meses_ND and $SQLs == 'NotaDebito')
    {
    	$SQLs = $MesComp.''.$SQLs;
      $Si_MesComp = True;
    }
    if($Num_Meses_NC and $SQLs == 'NotaCredito')
    {
    	$SQLs = $MesComp.''.$SQLs;
      $Si_MesComp = True;
    }
    // print_r($SQLs);die();
  }
  if($SQLs !='')
  {
    $MesComp = "";
    if(strlen($FechaComp) >= 10)
    {
    	$MesComp = date('m');
    }
    if($MesComp == '')
    {
    	$MesComp = '01';
    }
    $conn = new db();
    $sql = "SELECT Numero, ID FROM Codigos
            WHERE Concepto = '".$SQLs. "' 
            AND Periodo = '".$_SESSION['INGRESO']['periodo']. "'
            AND Item = '".$_SESSION['INGRESO']['item']."'" ;
    // print_r($sql);die();
		$result = $conn->datos($sql);
	  if(count($result)>0)
	  {
	    $NumCodigo = $result[0]["Numero"];

	  }else
	  {
	    $NuevoNumero = True;
      $NumCodigo = 1;
      if($Num_Meses_CD && $Si_MesComp){$NumCodigo = intval($MesComp.''.'000001');}
      if($Num_Meses_CI && $Si_MesComp){$NumCodigo = intval($MesComp.''.'000001');}
      if($Num_Meses_CE && $Si_MesComp){$NumCodigo = intval($MesComp.''.'000001');}
      if($Num_Meses_ND && $Si_MesComp){$NumCodigo = intval($MesComp.''.'000001');}
      if($Num_Meses_NC && $Si_MesComp){$NumCodigo = intval($MesComp.''.'000001');}
	  }
	  
    if($NumCodigo > 0)
	  {
	    if($NuevoNumero)
	    {
	    	$Strgs = "INSERT INTO Codigos (Periodo,Item,Concepto,x,Numero)
                VALUES ('".$_SESSION['INGRESO']['periodo']."','".$_SESSION['INGRESO']['item']."','".$SQLs."','.',".$NumCodigo.") ";
                //faltra ejecutar
                 $conn->String_Sql($Strgs);
	    }
      // print_r($NumCodigo);die();
	    if($Incrementar)
	    {
	    	$Strgs = "UPDATE Codigos 
                SET Numero = Numero+1 
                WHERE Concepto = '".$SQLs."'
                AND Periodo = '" .$_SESSION['INGRESO']['periodo']."' 
                AND Item = '".$_SESSION['INGRESO']['item']. "' ";
                // print_r($Strgs);
        $conn->String_Sql($Strgs);
	    }
	  }
  }
  return $NumCodigo;
}


function paginancion($tabla,$function,$pag=false,$where=false) //optimizado
{
  $num_index = 6;

  $ini=0;
  $fin = 6;

  $sql = 'SELECT count(*) as total FROM '.$tabla.' WHERE 1=1 ';
  $conn = new db();  
  // print_r($sql);die();
   $result = $conn->datos($sql);
  $total = $result[0]['total'];
  // print_r($total);die();
  $partes = $total/25;
  $html='<div class="row text-right" id="paginacion"><ul class="pagination">
  <li class="paginate_button" onclick="$(\'#txt_pag\').val(this.value);'.$function.'();" value="0"><a href="#">Inicio</a></li>';

  $index_actual = ($pag/25)+1;
  if($index_actual % $num_index == 0)
  {
    $ini = $index_actual-1;
    $fin = $index_actual+6;
  }else
  {
    $secc = intval(($pag/25)/6);
    // print_r($secc);die();
    $i=0;
    while ($secc>$i) {
      // print_r('expression');
      $ini = $fin-1;
      $fin = $fin+6;
      $i++;
    }
  }

  

  for($i=$ini;$i<$fin;$i++)
  {
    $valor =$i*25;
    $index = $i+1;

    if($valor==$pag)
    {
      $html.='<li class="paginate_button active" onclick="$(\'#txt_pag\').val(this.value);'.$function.'();" value="'.$valor.'"><a href="#">'.$index.'</a></li>';
    }else
    {
      $html.='<li class="paginate_button " onclick="$(\'#txt_pag\').val(this.value);'.$function.'();" value="'.$valor.'"><a href="#">'.$index.'</a></li>';
    }
  }
  $final =  intval(($total-25));
  $html.=' <li class="paginate_button" onclick="$(\'#txt_pag\').val(this.value);'.$function.'();" value="'.$final.'"><a href="#">Fin</a></li></ul></div>';

  // print_r($html);die();
       return $html;       

}

// texto valido
function TextoValido($texto,$numero=false,$Mayusculas=false,$NumeroDecimales=false)
{
	$result = '';
	if($Mayusculas)
	{
		$result = strtoupper($texto);
	}
	if($numero)
	{
		if($texto == '')
		{
			$texto = 0;
		}
		if(is_numeric($texto))
		{
			$result = round($texto, 2, PHP_ROUND_HALF_DOWN);
			switch ($NumeroDecimales) {
				case 0:
				$result = round($texto, 2, PHP_ROUND_HALF_DOWN);
				break;
				
				case $NumeroDecimales > 2:
				$result = round($texto, $NumeroDecimales, PHP_ROUND_HALF_DOWN);
				break;
			}
		}
	}else
	{
		if($texto == '')
		{
			$result = G_NINGUNO;
		}else
		{
			$result = $texto;
		}

	}
  //print_r($result);
	return $result;
}

//string de tipo de cuenta
function TiposCtaStrg($cuenta) {
	$Resultado='NINGUNA';
   switch ($cuenta){
   	case 'value':
   		# code...
   		break;
   	case "1":  
   	$Resultado = "ACTIVO";
   	break;
    case "2":  
    $Resultado = "PASIVO";
    break;
    case "3":  
    $Resultado = "CAPITAL";
    break;
    case "4":  
    $Resultado = "INGRESO";
    break;
    case "5":  
    $Resultado = "EGRESO";
    break;
   }
   return $Resultado;
}

//enviar emails
  function enviar_email($archivos=false,$to_correo="",$cuerpo_correo="",$titulo_correo="",$correo_apooyo="",$nombre="",$EMAIL_CONEXION="",$EMAIL_CONTRASEÑA="")
  {

  	$respuesta=true;
  	//$correo='ejfc19omoshiroi@gmail.com,ejfc_omoshiroi@hotmail.com';
  	//$to =explode(',', $correo);
  	$to =explode(',', $to_correo);
  
   foreach ($to as $key => $value) {
 //  	print_r($value);
  		 $mail = new PHPMailer();
       $mail->isSMTP();
	     $mail->SMTPDebug = 0;
	     $mail->Host = "smtp.gmail.com";
	     $mail->Port =  465;
	     //$mail->SMTPSecure = "none";
	     $mail->SMTPAuth = true;
	     $mail->SMTPSecure = 'ssl';
	     $mail->Username = $EMAIL_CONEXION;  //EMAIL_CONEXION DE TABLA EMPRESA
	     $mail->Password = $EMAIL_CONTRASEÑA; //EMAIL_CONTRASEÑA DE LA TABLA EMPRESA
	     $mail->setFrom($correo_apooyo,$nombre);

         $mail->addAddress($value);
         $mail->Subject = $titulo_correo;
         $mail->Body = $cuerpo_correo; // Mensaje a enviar


         if($archivos)
         {
          foreach ($archivos as $key => $value) {
           if(file_exists('../vista/TEMP/'.$value))
            {
          //		print_r('../vista/TEMP/'.$value);
          
         	  $mail->AddAttachment('../vista/TEMP/'.$value);
             }          
          }         
        }
          if (!$mail->send()) 
          {
          	$respuesta = false;
     	  }
    }
    print_r($mail->ErrorInfo);die();
    return $respuesta;
  }

  function mes_X_nombre($num)
  {
  	//print_r($num);
  	$monthNameSpanish='';
  	switch($num)
  	 {   
       case 1:
       $monthNameSpanish = "Enero";
       break;

       case 2:
       $monthNameSpanish = "Febrero";
       break;

       case 3:
       $monthNameSpanish = "Marzo";
       break;

       case 4:
       $monthNameSpanish = "Abril";
       break;

       case 5:
       $monthNameSpanish = "Mayo";
       break;

       case 6:
       $monthNameSpanish = "Junio";
       break;

       case 7:
       $monthNameSpanish = "Julio";
       break;

       case 8:
       $monthNameSpanish = "Agosto";
       break;

       case 9:
       $monthNameSpanish = "Septiembre";
       break;

        case 10:
       $monthNameSpanish = "Octubre";
       break;

       case 11:
       $monthNameSpanish = "Noviembre";
       break;

       case 12:
       $monthNameSpanish = "Diciembre";
       break;
    }

return $monthNameSpanish;

  }
   function nombre_X_mes($num)
  {
    //print_r($num);
    $monthNameSpanish='';
    switch($num)
     {   
       case ($num =='Enero') || ($num == 'enero'):
       $monthNameSpanish = "01";
       break;

       case ($num =='Febrero') || ($num =='febrero'):
       $monthNameSpanish = "02";
       break;

       case ($num =='Marzo') || ($num =='marzo'):
       $monthNameSpanish = "03";
       break;

       case ($num =='Abril') || ($num =='abril'):
       $monthNameSpanish = "04";
       break;

       case ($num =='Mayo') || ($num =='mayo'):
       $monthNameSpanish = "05";
       break;

       case ($num =='Junio') || ($num =='junio'):
       $monthNameSpanish = "06";
       break;

       case ($num =='Julio') || ($num =='julio'):
       $monthNameSpanish = "07";
       break;

       case ($num =='Agosto') || ($num =='agosto'):
       $monthNameSpanish = "08";
       break;

       case ($num =='Septiembre') || ($num =='septiembre'):
       $monthNameSpanish = "09";
       break;

        case ($num =='Octubre') || ($num =='octubre'):
       $monthNameSpanish = "10";
       break;

       case ($num =='Noviembre') || ($num =='noviembre'):
       $monthNameSpanish = "11";
       break;

       case ($num =='Diciembre') || ($num =='diciembre'):
       $monthNameSpanish = "12";
       break;
    }

return $monthNameSpanish;

  }

 // funcion para enviar todos los meses del año

  function meses_del_anio()
  {
  	$mese = array(
  		array('mes'=>'Enero','num'=>'01','acro'=>'ENE'),
  		array('mes'=>'Febrero','num'=>'02','acro'=>'FEB'),
  		array('mes'=>'Marzo','num'=>'03','acro'=>'MAR'),
  		array('mes'=>'Abril','num'=>'04','acro'=>'ABR'),
  		array('mes'=>'Mayo','num'=>'05','acro'=>'MAY'),
  		array('mes'=>'Junio','num'=>'06','acro'=>'JUN'),
  		array('mes'=>'Julio','num'=>'07','acro'=>'JUL'),
  		array('mes'=>'Agosto','num'=>'08','acro'=>'AGO'),
  		array('mes'=>'Septiembre','num'=>'09','acro'=>'SEP'),
  		array('mes'=>'Octubre','num'=>'10','acro'=>'OCT'),
  		array('mes'=>'Noviembre','num'=>'11','acro'=>'NOV'),
  		array('mes'=>'Diciembre','num'=>'12','acro'=>'DIC'),
  	);

  	return $mese;
  }

//verificar si tiene sucursales

  function existe_sucursales()  //--------------- optimizado javier farinango
  {
  	$conn = new db();
    $sql = "SELECT * FROM Acceso_Sucursales where Item='".$_SESSION['INGRESO']['item']."'";
    $result = $conn->datos($sql);		  
	  if(count($result) == 0)
    {
      return -1;
    }else
    {
      return 1;
    }
  }

//año bisiesto

function naciones_todas()  // optimizado
{
  $conn = new db();
    $sql = "SELECT CPais,Descripcion_Rubro
    FROM Tabla_Naciones
    WHERE TR = 'N'
    AND Descripcion_Rubro <> 'OTRO'
    ORDER BY Descripcion_Rubro,CPais ";
    $datos = $conn->datos($sql);
    $result = array();  
    foreach ($datos as $key => $value) {
      $result[] =array('Codigo'=>$value['CPais'],'Descripcion_Rubro'=>$value['Descripcion_Rubro']);
    }
   return $result;
       //print_r($result);
}


function provincia_todas($Cpais=false)  // optimizado
{
  if (!$Cpais) {
    $Cpais = '593';
  }
	  $conn = new db();
    $sql = "SELECT * FROM Tabla_Naciones WHERE CPais = '".$Cpais."' AND TR ='P' ORDER BY CProvincia";
    // print_r($sql);die();
		$datos = $conn->datos($sql);
    $result = array();  
    foreach ($datos as $key => $value) {
      $result[] =array('Codigo'=>$value['CProvincia'],'Descripcion_Rubro'=>$value['Descripcion_Rubro']);
    }
	 return $result;
	     //print_r($result);
}

function todas_ciudad($idpro) //otimizado
{
	$conn = new db();
    $sql = "SELECT * FROM Tabla_Naciones WHERE CPais = '593' AND TR ='C' AND CProvincia='".$idpro."' ORDER BY CCiudad";
		$datos = $conn->datos($sql);
    $result = array();  
    foreach ($datos as $key => $value) {
      $result[] =array('Codigo'=>$value['Codigo'],'Descripcion_Rubro'=>$value['Descripcion_Rubro']);
    }
   return $result;
	     //print_r($result);

}
function esBisiesto($year=NULL) 
{
    $year = ($year==NULL)? date('Y'):$year;
    return ( ($year%4 == 0 && $year%100 != 0) || $year%400 == 0 );
}
//para devolver la url basica
function url($pag=null,$idMen=null)
{
	//directorio adicional en caso de tener uno
	$direc=$pag;
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
			$uri = 'https://';
		}else{
			$uri = 'http://';
		}
		$uri .= $_SERVER['HTTP_HOST'].$direc;
	return $uri;
}
function redireccion($pag=null,$idMen=null)
{
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	}else{
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	//Aqui modificar si el pag de aministracion esta 
	//en un subdirectorio
	// "<script type=\"text/javascript\">
	// window.location=\"".$uri."/wp-admin/admin.php\";
	// </script>";
	echo "<script type='text/javascript'>window.location='".$uri."/php/vista/".$pag.".php'</script>";
}
//agregar ceros a cadena a la izquierda $tam= tamaño de la cadena
function generaCeros($numero,$tam=null){
	 //obtengop el largo del numero
	 $largo_numero = strlen($numero);
	 //especifico el largo maximo de la cadena
	 if($tam==null)
	 {
	 	$largo_maximo = 7;
	 }
	 else
	 {
	 	 $largo_maximo =$tam;
	 }
	
	 //tomo la cantidad de ceros a agregar
	 $agregar = $largo_maximo - $largo_numero;
	 //agrego los ceros
	 for($i =0; $i<$agregar; $i++){
	 $numero = "0".$numero;
	 }
	 //retorno el valor con ceros
	 return $numero;
 }
//convertir digitos a letras  ABCDEFGHIJ 0123456789
function convertirnumle($digito=null)
{
	$letra='';
	if($digito!=null)
	{
		if($digito==0)
		{
			$letra='A';
		}
		if($digito==1)
		{
			$letra='B';
		}
		if($digito==2)
		{
			$letra='C';
		}
		if($digito==3)
		{
			$letra='D';
		}
		if($digito==4)
		{
			$letra='E';
		}
		if($digito==5)
		{
			$letra='F';
		}
		if($digito==6)
		{
			$letra='G';
		}
		if($digito==7)
		{
			$letra='H';
		}
		if($digito==8)
		{
			$letra='I';
		}
		if($digito==9)
		{
			$letra='J';
		}
	}
	return $letra;
}

function Digito_verificador($CI_RUC)
{
  $sri = new autorizacion_sri();
  $CI_RUC = $sri->quitar_carac($CI_RUC);
  // 'SP que determinar que tipo de contribuyente es y el codigo si es pasaporte
   $datos = Digito_Verificador_SP($CI_RUC);
   // print_r($datos);die();
   if($datos['Tipo_Beneficiario'] <> "R" && strlen($datos['RUC_CI']) == 13){
      if(GetUrlSource("https://srienlinea.sri.gob.ec/sri-catastro-sujeto-servicio-internet/rest/ConsolidadoContribuyente/existePorNumeroRuc?numeroRuc=".$datos['RUC_CI'])== true){
        // print_r('expression');die();
         $datos['Tipo_Beneficiario'] = "R";
         $datos['Codigo_RUC_CI'] = substr($datos['RUC_CI'], 0, 10);
         $datos['Digito_Verificador'] = substr($datos['RUC_CI'], 10, 1);
      }
   }
   $TipoBenef = $datos['Tipo_Beneficiario'];
   if($datos['Tipo_Beneficiario'] == "R")
    { 
       $datos1 = Tipo_Contribuyente_SP_MySQL($datos['RUC_CI']);
       $datos['MicroEmpresa'] = $datos1['@micro'];
       $datos['AgenteRetencion'] = $datos1['@Agente'];
    }

    return $datos;
   // RatonNormal
   // Digito_Verificador = Tipo_RUC_CI.Digito_Verificador
}

function ping($ip)
{
  $output = shell_exec("ping $ip");   
  if (strpos($output, "recibidos = 0")) {
      return -1;
  } else {
     return 1;
  }
}
function GetUrlSource($url)
{
  $res = file_get_contents($url);
  return $res;
}

function Digito_Verificador_SP($NumeroRUC)
{
  $conn = new db();
  $RUCCI = "";
  $CodigoRUCCI = "";
  $DigitoVerificador = "";
  $TipoBeneficiario = "";
  $RUCNatural = false;

  //Determinamos que tipo de RUC/CI es
  $Tipo_Beneficiario = "P";
  $Codigo_RUC_CI = $_SESSION['INGRESO']['item']. "0000001";
  $Digito_Verificador = "-";
  $RUC_CI = $NumeroRUC;
  $RUC_Natural = 0;
  $TipoSRI_Existe = 0;
  $parametros = array(
    array(&$NumeroRUC, SQLSRV_PARAM_IN),
    array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),

    array(&$RUC_CI, SQLSRV_PARAM_INOUT),
    array(&$Codigo_RUC_CI, SQLSRV_PARAM_INOUT),
    array(&$Digito_Verificador, SQLSRV_PARAM_INOUT),
    array(&$Tipo_Beneficiario, SQLSRV_PARAM_INOUT),
    array(&$RUC_Natural, SQLSRV_PARAM_INOUT),
  );
  $sql = "EXEC sp_Digito_Verificador @NumeroRUC=?, @Item=?, @RUCCI=?, @CodigoRUCCI=?, @DigitoVerificador=?, @TipoBeneficiario=?, @RUCNatural=?";
  $exec = $conn->ejecutar_procesos_almacenados($sql,$parametros);

  if($Tipo_Beneficiario != "R") {$TipoSRI_Existe  = false; };

  return compact('RUC_CI', 'Codigo_RUC_CI', 'Digito_Verificador', 'Tipo_Beneficiario', 'RUC_Natural', 'TipoSRI_Existe'); 
}


//excel
function exportar_excel_generico($stmt,$ti=null,$camne=null,$b=null,$base=null)
{
  excel_file($stmt,$ti,$camne,$b,$base); 
}
function exportar_excel_descargos($stmt,$ti=null,$camne=null,$b=null,$base=null)
{
	excel_file_descargos($stmt,$ti,$camne,$b,$base); 
}
function exportar_excel_auditoria($stmt,$ti=null,$camne=null,$b=null,$base=null)
{
  excel_file_auditoria($stmt,$ti,$camne,$b,$base); 
}
function exportar_excel_comp($stmt,$ti=null,$camne=null,$b=null,$base=null)
{
  excel_file_comp($stmt,$ti,$camne,$b,$base); 
}
function exportar_excel_diario_g($re,$ti=null,$camne=null,$b=null,$base=null)
{
	excel_file_diario($re,$ti,$camne,$b,$base); 
}
function exportar_excel_libro_g($re,$stmt,$ti=null,$camne=null,$b=null,$base=null)
{
	excel_file_libro($re,$stmt,$ti,$camne,$b,$base); 
}
// function exportar_excel_mayor_auxi($re,$sub,$ti=null,$camne=null,$b=null,$base=null)
// {
// 	excel_file_mayor_auxi($re,$sub,$ti,$camne,$b,$base); 
// }
function exportar_excel_libro_banco($re,$ti=null,$camne=null,$b=null,$base=null)
{
	excel_file_libro_banco($re,$ti,$camne,$b,$base); 
}
//impimir xml txt etc $va en caso de ser pdf se evalua si lee 0 desde variable si lee 1 desde archivo xml
//$imp variable para descargar o no archivo
function ImprimirDoc($stmt,$id=null,$formato=null,$va=null,$imp=null,$ruta=null)
{
	if($ruta==null)
	{
		require_once("../../lib/fpdf/reporte_de.php");
	}
	$nombre_archivo = "TEMP/".$id.".".$formato; 
	if($formato=='xml')
	{
		if($imp==0)
		{
			$nombre_archivo = "TEMP/".$id.".xml"; 
		}
		if(file_exists($nombre_archivo))
		{
			//$mensaje = "El Archivo $nombre_archivo se ha modificado";
		}
	 
		else
		{
			//$mensaje = "El Archivo $nombre_archivo se ha creado";
		}
		
		//if($archivo = fopen($nombre_archivo, "a"))
		if($archivo = fopen($nombre_archivo, "w+b"))
		{
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
			{
				$row[0] = str_replace("ï»¿", "", $row[0]);
				if(fwrite($archivo, $row[0]))
				{
					echo "Se ha ejecutado correctamente";
				}
				else
				{
					echo "Ha habido un problema al crear el archivo";
				}
			}
		   
	 
			fclose($archivo);
		}
		if($imp==null or $imp==1)
		{
			if (file_exists($nombre_archivo)) {
				$downloadfilename = $downloadfilename !== null ? $downloadfilename : basename($nombre_archivo);
				
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename=' . $downloadfilename);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($nombre_archivo));
				
				ob_clean();
				flush();
				readfile($nombre_archivo);
				
				exit;
			}
		}
	}
	if($formato=='pdf')
	{
		$nombre_archivo = "TEMP/".$id.".xml"; 
		//desde archivo
		if($va==1)
		{
			//echo "asas";
			if(file_exists($nombre_archivo))
			{
				//$mensaje = "El Archivo $nombre_archivo se ha modificado";
			}
		 
			else
			{
				//$mensaje = "El Archivo $nombre_archivo se ha creado";
			}
		 
			//if($archivo = fopen($nombre_archivo, "a"))
			if($archivo = fopen($nombre_archivo, "w+b"))
			{
				
				while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
				{
					$row[0] = str_replace("ï»¿", "", $row[0]);
					$stmt1=$row[0];
					$ti=$row[1];
					if(fwrite($archivo, $row[0]))
					{
						//echo "Se ha ejecutado correctamente";
					}
					else
					{
						echo "Ha habido un problema al crear el archivo";
					}
				}
			   
		 
				fclose($archivo);
			}
		}
		else
		{
			//echo "dddd";
			//desde variable
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
			{
				//echo $row[0];
				$row[0] = str_replace("ï»¿", "", $row[0]);
				$stmt1=$row[0];
				$ti=$row[1];
			}
		}
		//die();
		//echo $ti;
		//die();
		if($ti=='FA')
		{
			imprimirDocEl($stmt1,$id,$formato,$nombre_archivo,$va,$imp);
		}
		if($ti=='NC')
		{
			imprimirDocElNC($stmt1,$id,$formato,$nombre_archivo,$va,$imp);
		}
		if($ti=='RE')
		{
			imprimirDocElRE($stmt1,$id,$formato,$nombre_archivo,$va,$imp);
		}
		if($ti=='GR' OR $ti=='XX')
		{
			imprimirDocElGR($stmt1,$id,$formato,$nombre_archivo,$va,$imp);
		}
		if($ti=='NV')
		{
			imprimirDocElNV($stmt1,$id,$formato,$nombre_archivo,$va,$imp);
		}
		if($ti=='ND')
		{
			imprimirDocElND($stmt1,$id,$formato,$nombre_archivo,$va,$imp);
		}
		
	}
}
function ImprimirDocError($stmt,$id=null,$formato=null,$va=null,$imp=null)
{
	//para errores de mayorizacion
	if($id=='macom')
	{
		require_once("../../lib/fpdf/reporte_comp.php");
		//echo " entrooo ";
		//die();
		$nombre_archivo = "TEMP/".$id.".xml"; 
		imprimirDocERRORPDF($stmt,$id,$formato,$nombre_archivo,$va,$imp);
	}
}
//conseguir un valor en una etiqueta xml
function etiqueta_xml($xml,$eti)
{
	//validar que etiqueta sea unica
	$cont=substr_count($xml,$eti);
	if( $cont <= 1 and $cont<>0 )
	{
		$resul1 = explode($eti, $xml);
		$cont1=substr_count($eti,">");
		$eti1 = str_replace("<", "</", $eti);
		//sin atributos
		if($cont1==1)
		{
			$resul2 = explode($eti1, $resul1[1]);
		}
		else
		{
			//con atributos
			$resul3 = explode(">", $resul1[1]);
			$resul2 = explode($eti1, $resul3[1]);
		}
		if($eti=='<baseImponible')
		{
			//echo $resul2[0].' ssssssssssssssssssss<br>';
		}
		//$resul2 = explode($eti1, $resul1[1]);
		//echo $resul2[0].' <br>';
		return $resul2[0]; 
		
	}
	else
	{
		if( $cont > 1  )
		{
			//echo " vvv ".$cont;
			$resul1 = explode($eti, $xml);
			//$eti1 = str_replace("<", "</", $eti);
			//$resul2 = explode($eti1, $resul1[1]);
			$j=0;
			$resul4=array();
			
			for($i=0;$i<count($resul1);$i++)
			{
				if($i>=1)
				{
					$resul3 = explode(">", $resul1[$i]);
					$eti1 = str_replace("<", "</", $eti);
					$resul2 = explode($eti1, $resul3[1]);
					$resul4[$j]=$resul2[0];
					//echo $resul2[0].' <br>';
					//echo " segunda opc".' <br>';
					//echo $j.' <br>';
					if($eti=='<baseImponible')
					{
						//echo $resul1[$i].' ssssssssssssssssssss<br>';
					}
					$j++;
				}
			}
			return $resul4;
		}
		else
		{
			return '';
		}
	}
}
//tomar solo porcion de etiqueta xml
function porcion_xml($xml,$eti,$etf)
{
	$resul1 = explode($eti, $xml);
	$resul4=array();
	$j=0;
	for($i=0;$i<count($resul1);$i++)
	{
		if($i>=1)
		{
			$resul2 = explode($etf, $resul1[$i]);
			$resul4[$j]=$resul2[0];
			//echo $resul2[0];
			$j++;
		}
	}
	return $resul4;
}
//crear select option
function select_option_aj($tabla,$value,$mostrar,$filtro=null,$sel=null)//------------------------------por revisar //////
{
	//realizamos conexion
	if(isset($_SESSION['INGRESO']['IP_VPN_RUTA'])) 
	{
		$database=$_SESSION['INGRESO']['Base_Datos'];
		//$server=$_SESSION['INGRESO']['IP_VPN_RUTA'];
		$server=''.$_SESSION['INGRESO']['IP_VPN_RUTA'].', '.$_SESSION['INGRESO']['Puerto'];
		$user=$_SESSION['INGRESO']['Usuario_DB'];
		$password=$_SESSION['INGRESO']['Password_DB'];
	}
	else
	{
		$database="DiskCover_Prismanet";
		$server="tcp:mysql.diskcoversystem.com, 11433";
		$user="sa";
		$password="disk2017Cover";
	}
	/*$database="DiskCover_Prismanet";
	$server="mysql.diskcoversystem.com";
	$user="sa";
	$password="disk2017Cover";*/
	if(isset($_SESSION['INGRESO']['IP_VPN_RUTA']) and $_SESSION['INGRESO']['Tipo_Base']=='SQL SERVER') 
	{
		$connectionInfo = array("Database"=>$database, "UID" => $user, "PWD" => $password);

		$cid = sqlsrv_connect($server, $connectionInfo); //returns false
		if( $cid === false )
		{
			echo "fallo conecion sql server";
		}
		$sql = "SELECT ".$value.",".$mostrar." FROM ".$tabla;
		if($filtro!=null and $filtro!='')
		{
			$sql =  $sql." WHERE ".$filtro." ";
		}
	}
	$value1 = explode(",", $value);
	if(count($value1)==1)
	{
		$val1=0;
	}
	else
	{
		$val1=1;
	}
	$mostrar1 = explode(",", $mostrar);
	if(count($mostrar1)==1)
	{
		$cam1=0;
	}
	else
	{
		$cam1=1;
	}
	//echo $sql;
	$stmt = sqlsrv_query( $cid, $sql);
	if( $stmt === false)  
	{  
		 echo "Error en consulta.\n";  
		 die( print_r( sqlsrv_errors(), true));  
	}  
	$i=0;
	$selc='';
	while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
	{
		$selc='';
		if($sel==$row[0])
		{
			$selc='selected';
		}
		if($val1==0)
		{
		?>	
			<option value='<?php echo $row[0]; ?>' <?php echo $selc; ?> >
		<?php
		}
		if($val1==1)
		{
		?>	
			<option value='<?php echo $row[0].'-'.$row[1]; ?>' <?php echo $selc; ?> >
		<?php
		}
				if($cam1==0)
				{
					if($val1==0)
					{
						echo $row[1];
					}
					if($val1==1)
					{
						echo $row[2];
					}
				}
				else
				{
					if($val1==0)
					{
						echo $row[1].'  '.$row[2];
					}
					if($val1==1)
					{
						echo $row[3].'  '.$row[4];
					}
				}
			?></option>
		<?php
	}
	sqlsrv_close( $cid );
}
//crear select option
function select_option($tabla,$value,$mostrar,$filtro=null,$click=null,$id_html=null)
{
	//realizamos conexion
	if(isset($_SESSION['INGRESO']['IP_VPN_RUTA'])) 
	{
		$database=$_SESSION['INGRESO']['Base_Datos'];
		//$server=$_SESSION['INGRESO']['IP_VPN_RUTA'];
		$server=''.$_SESSION['INGRESO']['IP_VPN_RUTA'].', '.$_SESSION['INGRESO']['Puerto'];
		$user=$_SESSION['INGRESO']['Usuario_DB'];
		$password=$_SESSION['INGRESO']['Password_DB'];
	}
	else
	{
		$database="DiskCover_Prismanet";
		$server="tcp:mysql.diskcoversystem.com, 11433";
		$user="sa";
		$password="disk2017Cover";
	}
	/*$database="DiskCover_Prismanet";
	$server="mysql.diskcoversystem.com";
	$user="sa";
	$password="disk2017Cover";*/
	if(isset($_SESSION['INGRESO']['IP_VPN_RUTA']) and $_SESSION['INGRESO']['Tipo_Base']=='SQL SERVER') 
	{
		$connectionInfo = array("Database"=>$database, "UID" => $user, "PWD" => $password);

		$cid = sqlsrv_connect($server, $connectionInfo); //returns false
		if( $cid === false )
		{
			echo "fallo conecion sql server";
		}
		$sql = "SELECT ".$value.",".$mostrar." FROM ".$tabla;
		if($filtro!=null and $filtro!='')
		{
			$sql =  $sql." WHERE ".$filtro." ";
		}
	}
	$mostrar1 = explode(",", $mostrar);
	if(count($mostrar1)==1)
	{
		$cam1=0;
	}
	else
	{
		$cam1=1;
	}
	// echo $sql;
	$stmt = sqlsrv_query( $cid, $sql);
	if( $stmt === false)  
	{  
		 echo "Error en consulta.\n";  
		 die( print_r( sqlsrv_errors(), true));  
	}  
	$i=0;
	$click1='';
	if($click!=null)
	{
		if($id_html!=null)
		{
			$click1=$click;
			$click1=$click1."('".$id_html."')";
			//onclick=" echo $click1; "
		}
	}
  $op='';
	while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
	{	
		$op = "<option value='".$row[0]."'>";
			
				if($cam1==0)
				{
					$op.=$row[1]; 
				}
				else
				{
					 $op.=$row[1].'-'.$row[2]; 
				}
		$op.="</option>";

	}
	sqlsrv_close( $cid );
  return $op;
}

//crear select option para mysql
function select_option_mysql($tabla,$value,$mostrar,$filtro=null)
{
	require_once("../db/db.php");
	$cid = Conectar::conexion('MYSQL');;
	
	$sql = "SELECT ".$value.",".$mostrar." FROM ".$tabla;
	
	if($filtro!=null and $filtro!='')
	{
		$sql =  $sql." WHERE ".$filtro." ";
	}
	//echo $sql;
	$consulta=$cid->query($sql) or die($cid->error);
	//$stmt = sqlsrv_query( $cid, $sql);
	//saber si hay mas campos amostrar
	$mostrar1 = explode(",", $mostrar);
	if(count($mostrar1)==1)
	{
		$cam1=0;
	}
	else
	{
		$cam1=1;
	}
	
	if( $consulta === false)  
	{  
		 echo "Error en consulta.\n";  
		 $return = array('success' => false);
		 //die( print_r( sqlsrv_errors(), true));  
	}
	else
	{	
		while($filas=$consulta->fetch_assoc())
		{
			?>	
			<option value='<?php echo $filas[$value]; ?>'>
				<?php 
					if($cam1==0)
					{
						echo $filas[$mostrar];
					}
					else
					{
						$mos1=$mostrar1[0];
						$mos2=$mostrar1[1];
						echo $filas[$mos1].'-'.$filas[$mos2];
					}
				?>
			</option>
			<?php
			
		}
		
	}
	$cid->close();
}

//consulta menus del sistema
function select_menu_mysql()
{
  $cid = new db();
  // $sql = "SELECT * FROM menu_modulos";
  // $datos = $cid->datos($sql,'MYSQL');
  
  //verificar codigo del menu que se necesita
  // while ($menu_item = $consulta->fetch_assoc()) {
  //   if (strtolower($_GET['mod']) == strtolower($menu_item['descripcionMenu'])) {
  //     $codMenu = $menu_item['codMenu'];
  //   }
  // }

  // foreach ($datos as $key => $value) {
     // if (strtolower($_GET['mod']) == strtolower($value['descripcionMenu'])) {
       $codMenu = $_GET['mod'];
    // }
  // }

  //seleccionar todos los items del menu
  $sql = "SELECT * FROM menu_modulos WHERE codMenu LIKE '".$codMenu."%' ORDER BY codMenu ASC";
  $submenu = $cid->datos($sql,'MYSQL');

  // $submenu=$cid->query($sql) or die($cid->error);
  $array_menu = array();
  $i = 0;

  foreach ($submenu as $key => $value) {
    $array_menu[$key]['codMenu'] = $value['codMenu'];
    $array_menu[$key]['descripcionMenu'] = $value['descripcionMenu'];
    $array_menu[$key]['accesoRapido'] = $value['accesoRapido'];
    $array_menu[$key]['rutaProceso'] = $value['rutaProceso'];
    
  }
  // while ($menu_item = $submenu->fetch_assoc()) {
  //   //echo $menu_item['codMenu']." ".$menu_item['descripcionMenu']."<br>";
  //   $array_menu[$i]['codMenu'] = $menu_item['codMenu'];
  //   $array_menu[$i]['descripcionMenu'] = $menu_item['descripcionMenu'];
  //   $array_menu[$i]['accesoRapido'] = $menu_item['accesoRapido'];
  //   $array_menu[$i]['rutaProceso'] = $menu_item['rutaProceso'];
  //   $i++;
  // }
  return $array_menu;
  // $cid->close();
  // exit();
}


//consulta menus del sistema
function pagina_acceso_hijos($usuario,$entidad,$item,$codMenu=false)
{
  // print_r($_SESSION['INGRESO']);die();
  if(!$codMenu)
  {
    $codMenu = $_GET['mod'];
  }
  $cid = new db();
  // $usuario = $_SESSION['INGRESO']['CodigoU'];
  // $id_entidad = $_SESSION['INGRESO']['IDEntidad'];
  // $item = $_SESSION['INGRESO']['item'];
  //seleccionar todos los items del menu
  $sql  = "SELECT * 
           FROM acceso_empresas AE
           INNER JOIN menu_modulos MM ON AE.Pagina = MM.ID 
           WHERE ID_Empresa = '".$entidad."' 
           AND CI_NIC = '".$usuario."' 
           AND codMenu LIKE '".$codMenu."%' 
           AND Item = '".$item."' 
           AND Pagina != '.' 
           AND Pagina != ''
           ORDER BY CodMenu ASC";

 // print_r($sql);
  $submenu = $cid->datos($sql,'MYSQL');
  // $submenu=$cid->query($sql) or die($cid->error);
  $array_menu = array();
  $i = 0;

  foreach ($submenu as $key => $value) {
    $array_menu[$key]['codMenu'] = $value['codMenu'];
    $array_menu[$key]['descripcionMenu'] = $value['descripcionMenu'];
    $array_menu[$key]['accesoRapido'] = $value['accesoRapido'];
    $array_menu[$key]['rutaProceso'] = $value['rutaProceso'];
    $array_menu[$key]['Pagina'] = $value['Pagina'];
    
  }
 
  return $array_menu;
  
}

function pagina_acceso($codMenu,$usuario,$entidad,$item)
{
  // print_r($_SESSION['INGRESO']);die();
  $cid = new db();
  //seleccionar todos los items del menu
  $sql  = "SELECT * 
           FROM acceso_empresas AE
           INNER JOIN menu_modulos MM ON AE.Pagina = MM.ID 
           WHERE ID_Empresa = '".$entidad."' 
           AND CI_NIC = '".$usuario."' 
           AND codMenu LIKE '".$codMenu."' 
           AND Item = '".$item."' 
           AND Pagina != '.' 
           AND Pagina != ''
           ORDER BY CodMenu ASC";

 // print_r($sql);
  $submenu = $cid->datos($sql,'MYSQL');
  // $submenu=$cid->query($sql) or die($cid->error);
  $array_menu = array();
  $i = 0;

  foreach ($submenu as $key => $value) {
    $array_menu[$key]['codMenu'] = $value['codMenu'];
    $array_menu[$key]['descripcionMenu'] = $value['descripcionMenu'];
    $array_menu[$key]['accesoRapido'] = $value['accesoRapido'];
    $array_menu[$key]['rutaProceso'] = $value['rutaProceso'];
    
  }
 
  return $array_menu;
  
}


//consulta niveles del menu
function select_nivel_menu_mysql($padre) // otimizado
{
  //require_once("../db/db.php");
  $conn  =  new db(); 
  //seleccionar los niveles del menu
  $sql = "SELECT * FROM menu_modulos WHERE codMenu LIKE '".$padre.".%' ORDER BY codMenu ASC";
  $datos = $conn->datos($sql,'MY SQL');
  $array_menu = array();
  foreach ($datos as $key => $value) {
    $array_menu[$key]['codMenu'] = $value['codMenu'];
    $array_menu[$key]['descripcionMenu'] = $value['descripcionMenu'];
    $array_menu[$key]['accesoRapido'] = $value['accesoRapido'];
    $array_menu[$key]['rutaProceso'] = $value['rutaProceso'];
  } 
  return $array_menu;
}

//contar registros se usa para determinar tamaños de ventanas
function contar_option($tabla,$value,$mostrar,$filtro=null)  ///------------------------revicion para optimizar
{
	//realizamos conexion
	if(isset($_SESSION['INGRESO']['IP_VPN_RUTA'])) 
	{
		$database=$_SESSION['INGRESO']['Base_Datos'];
		//$server=$_SESSION['INGRESO']['IP_VPN_RUTA'];
		$server=''.$_SESSION['INGRESO']['IP_VPN_RUTA'].', '.$_SESSION['INGRESO']['Puerto'];
		$user=$_SESSION['INGRESO']['Usuario_DB'];
		$password=$_SESSION['INGRESO']['Password_DB'];
	}
	else
	{
		$database="DiskCover_Prismanet";
		$server="tcp:mysql.diskcoversystem.com, 11433";
		$user="sa";
		$password="disk2017Cover";
	}
	/*$database="DiskCover_Prismanet";
	$server="mysql.diskcoversystem.com";
	$user="sa";
	$password="disk2017Cover";*/
	if(isset($_SESSION['INGRESO']['IP_VPN_RUTA']) and $_SESSION['INGRESO']['Tipo_Base']=='SQL SERVER') 
	{
		$connectionInfo = array("Database"=>$database, "UID" => $user, "PWD" => $password);

		$cid = sqlsrv_connect($server, $connectionInfo); //returns false
		if( $cid === false )
		{
			echo "fallo conecion sql server";
		}
		$sql = "SELECT ".$value." FROM ".$tabla;
		if($filtro!=null and $filtro!='')
		{
			$sql =  $sql." WHERE ".$filtro." ";
		}
	}
	//echo $sql;
	//die();
	$stmt = sqlsrv_query( $cid, $sql);
	if( $stmt === false)  
	{  
		 echo "Error en consulta.\n";  
		 die( print_r( sqlsrv_errors(), true));  
	}  
	$i=0;
	//div inicial	
	$cont=array();
	while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
	{
		$cont[$i]=$row[0];		
		$i++;
	}
	sqlsrv_close( $cid );
	return $cont;
}

//crear select option
function cone_ajax() //optimizado
{
   $conn = new db();
   $cid = $conn->conexion();
	 return $cid;
}
//cerrar sesion caso de usar funciones para hacer consultas rapidas fuera del MVC
function cerrarSQLSERVERFUN($cid)
{
	sqlsrv_close( $cid );
}
//para devolver columna de impuesto en reporte pdf
function impuesto_re($codigo)
{
	$resul4='';
	if($codigo==1)
	{
		$resul4='RENTA';
	}
	if($codigo==2)
	{
		$resul4='IVA';
	}
	if($codigo==6)
	{
		$resul4='ISD';
	}
	return $resul4;
}
function concepto_re($codigo)  //optimizado
{
  $conn = new db();
	$resul4='';
	$sql="select Concepto from Tipo_Concepto_Retencion where '".date('Y-m-d')."'  BETWEEN Fecha_Inicio AND Fecha_Final;";
  $resul4 = $conn->datos($sql);
  $resul4 = $resul4[0];
	return $resul4;
}
//caso guia de remision buscar el cliente
function buscar_cli($serie,$factura) // optimizado - revision
{
	$resul4=array();
	//conectamos  
  $conn = new db();
	$sql="select Razon_Social,RUC_CI from Facturas where
	TC='FA' and serie='".$serie."' and factura=".$factura." and periodo='".$_SESSION['INGRESO']['periodo']."';";

  $datos = $conn->datos($sql);
  foreach ($datos as $key => $value) {
    $resul4[0] = $value[0];
    $resul4[1] = $value[1];    
  }
	// $stmt = sqlsrv_query( $cid, $sql);
	// if( $stmt === false)  
	// {  
	// 	 echo "Error en consulta PA.\n";  
	// 	 die( print_r( sqlsrv_errors(), true));  
	// }
	
	// while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
	// {
	// 	$resul4[0] = $row[0];
	// 	$resul4[1] = $row[1];
	// 	//echo $row[0];
	// }
	// //cerramos
	// cerrarSQLSERVERFUN($cid);
	return $resul4;
}
//generica para contar registros $stmt= consulta generada
function contar_registros($stmt)
{
	$i=0;
	while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
	{
		$i++;
	}
	return $i;
}

//contar registros caso paginador por ejemplo (sql server y MYSQL) 
function cantidaREGSQL_AJAX($tabla,$filtro=null,$base=null)  // revision
{
	//echo $filtro.' gg ';
	if($base==null or $base=='SQL SEVER')
	{
		$cid = Conectar::conexion('SQL SERVER');
		if($filtro!=null AND $filtro!='')
		{
			$sql = "SELECT count(*) as regis FROM ".$tabla." WHERE ".$filtro." ";
		}
		else
		{
			$sql = "SELECT count(*) as regis FROM ".$tabla;
		}
		//echo $sql;
		$stmt = sqlsrv_query( $cid, $sql);
		if( $stmt === false)  
		{  
			 echo "Error en consulta PA.\n";  
			 die( print_r( sqlsrv_errors(), true));  
		}
		$row_count=0;
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
		{
			$row_count = $row[0];
			//echo $row[0];
		}
		cerrarSQLSERVERFUN($cid);
	}
	else
	{
		if($base=='MYSQL')
		{
			$cid = Conectar::conexion('MYSQL');
			
			if($filtro!=null AND $filtro!='')
			{
				$sql = "SELECT count(*) as regis FROM ".$tabla." WHERE ".$filtro." ";
			}
			else
			{
				$sql = "SELECT count(*) as regis FROM ".$tabla;
			}
			//echo $sql;
			$consulta=$cid->query($sql) or die($cid->error);
			$row_count=0;
			while($row=$consulta->fetch_assoc())
			{
				$row_count = $row['regis'];
				//echo $row[0];
			}
			$cid->close();
		}
	}
	//numero de columnas
	//$row_count = sqlsrv_num_rows( $stmt );
	return $row_count;
}
function paginador($tabla,$filtro=null,$link=null) // revision
{
	//saber si hay paginador
	$pag=1;
	$start_from=null; 
	$record_per_page=null;
	if($pag==1) 
	{
		//obtenemos los valores
		$record_per_page = 10;
		$pagina = '';
		if(isset($_GET["pagina"]))
		{
		 $pagina = $_GET["pagina"];
		}
		else
		{
		 $pagina = 1;
		}
		$start_from = ($pagina-1)*$record_per_page;
		
		//buscamos cantidad de registros
		$filtros=" Item = '".$_SESSION['INGRESO']['item']."'  
		AND ( Periodo='".$_SESSION['INGRESO']['periodo']."' ) ";
		//hacemos los filtros
		if(isset($_POST['tipo']))
		{
			if($_POST['tipo']!='seleccione')
			{
				$filtros=$filtros." AND TD='".$_POST['tipo']."' ";
				$_SESSION['FILTRO']['cam1']=$_POST['tipo'];
			}
			else
			{
				unset($_SESSION['FILTRO']['cam1']);
			}
		}
		else
		{
			//si ya existe un filtro caso paginador
			if(isset($_SESSION['FILTRO']['cam1']))
			{
				$filtros=$filtros." AND TD='".$_SESSION['FILTRO']['cam1']."' ";
			}
		}
		if(isset($_POST['fechai']) and isset($_POST['fechaf']))
		{
			//echo $_POST['fechai'];
			if($_POST['fechai']!='' AND $_POST['fechaf']!='')
			{
				$fei = explode("/", $_POST['fechai']);
				$fef = explode("/", $_POST['fechaf']);
				if(strlen($fei[2])==2 AND strlen($fef[2])==2)
				{
					$filtros=$filtros." AND convert(datetime,(SUBSTRING(Clave_Acceso, 5, 4)+'/'
					+SUBSTRING(Clave_Acceso, 3, 2)+'/'+SUBSTRING(Clave_Acceso, 1, 2)+' 00:00:00.000 AM'))
					BETWEEN '".$fei[0].$fei[1].$fei[2]."' AND '".$fef[0].$fef[1].$fef[2]."' ";
					$_SESSION['FILTRO']['cam2']=$fei[0].'/'.$fei[1].'/'.$fei[2];
					$_SESSION['FILTRO']['cam3']=$fef[0].'/'.$fef[1].'/'.$fef[2];
				}
				else
				{
					$filtros=$filtros." AND convert(datetime,(SUBSTRING(Clave_Acceso, 5, 4)+'/'
					+SUBSTRING(Clave_Acceso, 3, 2)+'/'+SUBSTRING(Clave_Acceso, 1, 2)+' 00:00:00.000 AM'))
					BETWEEN '".$fei[2].$fei[0].$fei[1]."' AND '".$fef[2].$fef[0].$fef[1]."' ";
					$_SESSION['FILTRO']['cam2']=$fei[2].'/'.$fei[0].'/'.$fei[1];
					$_SESSION['FILTRO']['cam3']=$fef[2].'/'.$fef[0].'/'.$fef[1];
				}
				//echo $fei[0].' '.$fei[1].' '.$fei[2].' ';
				
				
			}
		}
		else
		{
			//si ya existe un filtro caso paginador
			if(isset($_SESSION['FILTRO']['cam2']) AND isset($_SESSION['FILTRO']['cam3']))
			{
				$fei = explode("/", $_SESSION['FILTRO']['cam2']);
				$fef = explode("/", $_SESSION['FILTRO']['cam3']);
				
				$filtros=$filtros." AND convert(datetime,(SUBSTRING(Clave_Acceso, 5, 4)+'/'
				+SUBSTRING(Clave_Acceso, 3, 2)+'/'+SUBSTRING(Clave_Acceso, 1, 2)+' 00:00:00.000 AM'))
				BETWEEN '".$fei[0].$fei[1].$fei[2]."' AND '".$fef[0].$fef[1].$fef[2]."' ";
			}
		}
			//$_POST['fechai']; 
		$total_records=cantidaREGSQL_AJAX($tabla,$filtro,'MYSQL');
		//echo ' ddd '.$total_records;
		//die();
		if($total_records>0)
		{
			$total_pages = ceil($total_records/$record_per_page);
		}
		else
		{
			$total_pages = 0;
		}
		//echo '  '.$total_pages;
		$start_loop = $pagina;
		$diferencia = $total_pages - $pagina;
		if(isset($_SESSION['INGRESO']['Tipo_Base']) and $_SESSION['INGRESO']['Tipo_Base']=='SQL SERVER') 
		{
			$record_per_page = $start_from+10;
		}
		if($total_pages>0)
		{
			$start_loop1=$start_loop;
			if($diferencia <= 5)
			{
				$start_loop = $total_pages - 5;
				$start_loop1=$start_loop;
				if($start_loop < 0)
				{
					//$total_pages=$total_pages+$start_loop;
					$start_loop1=$start_loop;
					$start_loop=1;
				}
				if($start_loop == 0)
				{
					$start_loop=1;
				}
			}
			$end_loop = $start_loop1 + 4;
		}
		else
		{
			$start_loop=0;
			$end_loop=0;
		}
	}
	if($link==null)
	{
	?>
	<div class="box-footer clearfix">
		<ul class="pagination pagination-sm no-margin pull-right">
				<?php
			if($pag==1) 
			{
				if($pagina == 1)
				{
					//echo "<a class='pagina' href='pagina.php?pagina=1'>Primera</a>";
					//echo "<a class='pagina' href='pagina.php?pagina=".($pagina - 1)."'><<</a>";
					?>
					
				 <?php
				}
				if($pagina > 1)
				{
					//echo "<a class='pagina' href='pagina.php?pagina=1'>Primera</a>";
					//echo "<a class='pagina' href='pagina.php?pagina=".($pagina - 1)."'><<</a>";
					?>
					<li><a href="rde.php?mod=contabilidad&acc=rde&acc1=Reporte Doc. Electronico&ti=
				&Opcb=6&Opcen=0&b=0&pagina=1">1</a></li>
					<li><a href="rde.php?mod=contabilidad&acc=rde&acc1=Reporte Doc. Electronico&ti=
				&Opcb=6&Opcen=0&b=0&pagina=<?php echo ($pagina-1); ?>">&laquo;</a></li>
				 <?php
				}
				//echo $start_loop.' '.$end_loop;
				for($i=$start_loop; $i<=$end_loop; $i++)
				{     
					//echo "<a class='pagina' href='pagina.php?pagina=".$i."'>".$i."</a>";
						?>
					<li><a href="rde.php?mod=contabilidad&acc=rde&acc1=Reporte Doc. Electronico&ti=
				&Opcb=6&Opcen=0&b=0&pagina=<?php echo $i; ?>"><?php echo $i; ?></a></li>
					 <?php
				}
				if($pagina <= $end_loop)
				{
					//echo "<a class='pagina' href='pagina.php?pagina=".($pagina + 1)."'>>></a>";
					//echo "<a class='pagina' href='pagina.php?pagina=".$total_pages."'>Última</a>";
					?>
					<li><a href="rde.php?mod=contabilidad&acc=rde&acc1=Reporte Doc. Electronico&ti=
				&Opcb=6&Opcen=0&b=0&pagina=<?php echo $pagina+1; ?>">&raquo;</a></li>
					<li><a href="rde.php?mod=contabilidad&acc=rde&acc1=Reporte Doc. Electronico&ti=
				&Opcb=6&Opcen=0&b=0&pagina=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a></li>
					
				 <?php
				}
			
			}
		?>		
		</ul>
	</div>
	<?php
	}
	else
	{
		?>
	<div class="box-footer clearfix">
		<ul class="pagination pagination-sm no-margin pull-right">
				<?php
			if($pag==1) 
			{
				if($pagina == 1)
				{
					//echo "<a class='pagina' href='pagina.php?pagina=1'>Primera</a>";
					//echo "<a class='pagina' href='pagina.php?pagina=".($pagina - 1)."'><<</a>";
					?>
					
				 <?php
				}
				if($pagina > 1)
				{
					//echo "<a class='pagina' href='pagina.php?pagina=1'>Primera</a>";
					//echo "<a class='pagina' href='pagina.php?pagina=".($pagina - 1)."'><<</a>";
					?>
					<li><a href="<?php echo $link; ?>&pagina=1">1</a></li>
					<li><a href="<?php echo $link; ?>&pagina=<?php echo ($pagina-1); ?>">&laquo;</a></li>
				 <?php
				}
				//echo $start_loop.' '.$end_loop;
				for($i=$start_loop; $i<=$end_loop; $i++)
				{     
					//echo "<a class='pagina' href='pagina.php?pagina=".$i."'>".$i."</a>";
						?>
					<li><a href="rde.php?<?php echo $link; ?>&pagina=<?php echo $i; ?>"><?php echo $i; ?></a></li>
					 <?php
				}
				if($pagina <= $end_loop)
				{
					//echo "<a class='pagina' href='pagina.php?pagina=".($pagina + 1)."'>>></a>";
					//echo "<a class='pagina' href='pagina.php?pagina=".$total_pages."'>Última</a>";
					?>
					<li><a href="<?php echo $link; ?>&pagina=<?php echo $pagina+1; ?>">&raquo;</a></li>
					<li><a href="<?php echo $link; ?>&pagina=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a></li>
					
				 <?php
				}
			
			}
		?>		
		</ul>
	</div>
	<?php
	}
}
//grilla generica para mostrar en caso de usar ajax
//$tabla caso donde sean necesaria varias grillas
function grilla_generica($stmt,$ti=null,$camne=null,$b=null,$ch=null,$tabla=null,$base=null,$estilo=false,$button=false)
{
	if($base==null or $base=='SQL SERVER')
	{
		//cantidad de campos
		$cant=0;
		//guardamos los campos
		$campo='';
		//obtenemos los campos 
		foreach( sqlsrv_field_metadata( $stmt ) as $fieldMetadata ) {
			foreach( $fieldMetadata as $name => $value) {
				if(!is_numeric($value))
				{
					if($value!='')
					{
						$cant++;
					}
				}
			}
		}
		if($ch!=null)
		{
			$ch1 = explode(",", $ch);
			$cant++;
		}
    if($button)
    {
      $cant++;
    }
		//si lleva o no border
		$bor='';
    $bor1='';
    $bor2='';
		if($b!=null and $b!='0')
		{
			$bor='style="border: #b2b2b2 1px solid;"';
      $bor1='border: #b2b2b2 1px solid;';
      $bor2 = 'border';
			//style="border-top: 1px solid #bce8f1;"
		}
		//colocar cero a tabla en caso de no existir definida ninguna
		if($tabla==null OR $tabla=='0' OR $tabla=='')
		{
			$tabla=0;
		}
		?>
 <?php if($estilo)
 { echo  ' <style type="text/css">
      #datos_t table {
  border-collapse: collapse;
 
}

#datos_t table, th, td {
  /*border: solid 1px black;*/
  padding: 2px;
}

#datos_t tbody tr:nth-child(even) {
  background:#fffff;
}

#datos_t tbody tr:nth-child(odd) {
  background: #e2fbff;;
}

#datos_t tbody tr:nth-child(even):hover {
  background: #DDB;
}

#datos_t tbody tr:nth-child(odd):hover {
  background: #DDA;
}


.sombra {
  width: 99%;
  box-shadow: 10px 10px 6px rgba(0, 0, 0, 0.6);
}
 </style>';
 }
 ?>

		<div class="sombra" style="">
            <table <?php echo $bor2; ?> class="table table-striped table-hover" id='datos_t'>
				<?php
				if($ti!='' or $ti!=null)
				{
			?>
					<tr>
						<th  <?php echo $bor; ?> colspan='<?php echo $cant; ?>' style='text-align: center;background-color: #0086c7;color: #FFFFFF;' ><?php echo $ti; ?></th>
					</tr>
			<?php
				}
			?>
                <!-- <tr> -->
                  <thead>
					<?php
					//cantidad campos
					$cant=0;
					//guardamos los campos
					$campo='';
					//tipo de campos
					$tipo_campo=array();
					//guardamos posicion de un campo ejemplo fecha
					$cam_fech=array();
					//contador para fechas
					$cont_fecha=0;
					//obtenemos los campos 
					//en caso de tener check
					if($ch!=null)
					{
						echo "<th style='text-align: left;' id='tit_sel'>SEL</th>";
					}
          if($button)
          {
            echo "<th style='text-align: left;' id='tit_sel'></th>";
          }
          /*
          datetime = 93;

        */
					foreach( sqlsrv_field_metadata( $stmt ) as $fieldMetadata ) {
						//$camp='';
						$i=0;
						//tipo de campo
						$ban=0;
						//texto
						if($fieldMetadata['Type']==-9)
						{
							$tipo_campo[($cant)]="style='text-align: left;'";
							$ban=1;
						}
						//numero
						if($fieldMetadata['Type']==3)
						{
							//number_format($item_i['nombre'],2, ',', '.')
							$tipo_campo[($cant)]="style='text-align: right;'";
							$ban=1;
						}
						// echo $fieldMetadata['Type'].' ccc <br>';
						// echo $fieldMetadata['Name'].' ccc <br>';
						//caso fecha
						if($fieldMetadata['Type']==93)
						{
							$tipo_campo[($cant)]="style='text-align: left; width:80px;'";
							$ban=1;
							$cam_fech[$cont_fecha]=$cant;
							//contador para fechas
							$cont_fecha++;
						}
						//caso bit
						if($fieldMetadata['Type']==-7)
						{
							$tipo_campo[($cant)]="style='text-align: left;'";
							$ban=1;
						}
						//caso int
						if($fieldMetadata['Type']==4)
						{
							$tipo_campo[($cant)]=" style='text-align: right;'";
							$ban=1;
						}
						//caso tinyint
						if($fieldMetadata['Type']==-6)
						{
							$tipo_campo[($cant)]="style='text-align: right;'";
							$ban=1;
						}
						//caso smallint
						if($fieldMetadata['Type']==5)
						{
							$tipo_campo[($cant)]="style='text-align: right;'";
							$ban=1;
						}
						//caso real
						if($fieldMetadata['Type']==7)
						{
							$tipo_campo[($cant)]="style='text-align: right;'";
							$ban=1;
						}
						//caso float
						if($fieldMetadata['Type']==6)
						{
							$tipo_campo[($cant)]="style='text-align: right;'";
							$ban=1;
						}
						//uniqueidentifier
						if($fieldMetadata['Type']==-11)
						{
							$tipo_campo[($cant)]="style='text-align: right;'";
							$ban=1;
						}
						//ntext
						if($fieldMetadata['Type']==-10)
						{
							$tipo_campo[($cant)]="style='text-align: left; width:40px;'";
							$ban=1;
						}
						//rownum
						if($fieldMetadata['Type']==-5)
						{
							//echo " dddd ";
							$tipo_campo[($cant)]="style='text-align: left;'";
							$ban=1;
						}
						//ntext
						if($fieldMetadata['Type']==12)
						{
							$tipo_campo[($cant)]="style='text-align: left;  width:40px;'";
							$ban=1;
						}
						if($ban==0)
						{
							echo ' no existe tipo '.$value.' '.$fieldMetadata['Name'].' '.$fieldMetadata['Type'];
						}
						foreach( $fieldMetadata as $name => $value) {
							
							if(!is_numeric($value))
							{
								if($value!='')
								{
									echo "<th  ".$bor." id='id_$cant' onclick='orde($cant)' ".$tipo_campo[$cant].">".$value."</th>";
									$camp=$value;
									$campo[$cant]=$camp;
									//echo ' dd '.$campo[$cant];
									$cant++;
									//echo $value.' cc '.$cant.' ';
								}
							}
						   //echo "$name: $value<br />";
						}
						
						  //echo "<br />";
					}
					/*for($i=0;$i<$cant;$i++)
					{
						echo $i.' gfggf '.$tipo_campo[$i];
					}*/
					?>
				<!-- </tr> -->
				</thead>
                 
					<?php
					//echo $cant.' fffff ';
					//obtener la configuracion para celdas personalizadas
					//campos a evaluar
					$campoe=array();
					//valor a verificar
					$campov=array();
					//campo a afectar 
					$campoaf=array();
					//adicional
					$adicional=array();
					//signos para comparar
					$signo=array();
					//titulo de proceso
					$tit=array();
					//indice de registros a comparar con datos
					$ind=0;
					//obtener valor en caso de mas de una condicion
					$con_in=0;
					if($camne!=null)
					{
						for($i=0;$i<count($camne['TITULO']);$i++)
						{
							if($camne['TITULO'][$i]=='color_fila')
							{	
								$tit[$ind]=$camne['TITULO'][$i];
								//temporar para indice
								//$temi=$i;
								//buscamos campos a evaluar
								$camneva = explode(",", $camne['CAMPOE'][$i]);
								//si solo es un campo
								if(count($camneva)==1)
								{
									$camneva1 = explode("=", $camneva[0]);
									$campoe[$ind]=$camneva1[0];
									$campov[$ind]=$camneva1[1];
									//echo ' pp '.$campoe[$ind].' '.$campov[$ind];
								}
								else
								{
									//hacer bucle
								}
								//para los campos a afectar
								if(count($camne['CAMPOA'])==1 AND $i==0)
								{
									if($camne['CAMPOA'][$i]=='TODOS' OR $camne['CAMPOA'][$i]='')
									{
										$campoaf[$ind]='TODOS';
									}
									else
									{
										//otras opciones
									}
								}
								else
								{
									//bucle
									if(!empty($camne['CAMPOA'][$i]))
									{
										if($camne['CAMPOA'][$i]=='TODOS' OR $camne['CAMPOA'][$i]='')
										{
											$campoaf[$ind]='TODOS';
										}
										else
										{
											//otras opciones
										}
									}
								}
								//valor adicional en este caso color
								if(count($camne['ADICIONAL'])==1 AND $i==0)
								{
									$adicional[$ind]=$camne['ADICIONAL'][$i];
								}
								else
								{
									//bucle
									if(!empty($camne['ADICIONAL'][$i]))
									{
										$adicional[$ind]=$camne['ADICIONAL'][$i];
									}
								}
								//signo de comparacion
								if(count($camne['SIGNO'])==1 AND $i==0)
								{
									$signo[$ind]=$camne['SIGNO'][$i];
								}
								else
								{
									//bucle
									if(!empty($camne['SIGNO'][$i]))
									{
										$signo[$ind]=$camne['SIGNO'][$i];
									}
								}
								$ind++;
								//echo ' pp '.count($camneva);
							}
							//caso de indentar columna
						
							//caso italica, subrayar, indentar
							if($camne['TITULO'][$i]=='italica' OR $camne['TITULO'][$i]=='subrayar' OR $camne['TITULO'][$i]=='indentar')
							{
								$tit[$ind]=$camne['TITULO'][$i];
									//buscamos campos a evaluar
								if(!is_array($camne['CAMPOE'][$i]))
								{
									$camneva = explode(",", $camne['CAMPOE'][$i]);
									//si solo es un campo
									if(count($camneva)==1)
									{
										$camneva1 = explode("=", $camneva[0]);
										$campoe[$ind]=$camneva1[0];
										$campov[$ind]=$camneva1[1];
										//echo ' pp '.$campoe[$ind].' '.$campov[$ind];
									}
									else
									{
										//hacer bucle
									}
								}
								else
								{
									//es mas de un campo
									$con_in = count($camne['CAMPOE'][$i]);
									//recorremos registros
									for($j=0;$j<$con_in;$j++)
									{
										//echo $camne['CAMPOE'][$i][$j].' ';
										$camneva = explode(",", $camne['CAMPOE'][$i][$j]);
										//si solo es un campo
										if(count($camneva)==1)
										{
											$camneva1 = explode("=", $camneva[0]);
											$campoe[$ind][$j]=$camneva1[0];
											$campov[$ind][$j]=$camneva1[1];
											//echo ' pp '.$campoe[$ind][$j].' '.$campov[$ind][$j];
										}
									}
								}
								//para los campos a afectar
								if(!is_array($camne['CAMPOA'][$i]))
								{
									if(count($camne['CAMPOA'])==1 AND $i==0)
									{
										$campoaf[$ind]=$camne['CAMPOA'][$i];
									}
									else
									{
										//bucle
										if(!empty($camne['CAMPOA'][$i]))
										{
											//otras opciones
											$campoaf[$ind]=$camne['CAMPOA'][$i];
										}
									}
								}
								else
								{
									//recorremos el ciclo
									//es mas de un campo
									$con_in = count($camne['CAMPOA'][$i]);
									//recorremos registros
									for($j=0;$j<$con_in;$j++)
									{
										$campoaf[$ind][$j]=$camne['CAMPOA'][$i][$j];
										//echo ' pp '.$campoaf[$ind][$j];
									}
								}
								//valor adicional en este caso color
								
									if(count($camne['ADICIONAL'])==1 AND $i==0)
									{
										$adicional[$ind]=$camne['ADICIONAL'][$i];
									}
									else
									{
										//bucle
										if(!empty($camne['ADICIONAL'][$i]))
										{
											//es mas de un campo
											$con_in = count($camne['ADICIONAL'][$i]);
											for($j=0;$j<$con_in;$j++)
											{
												$adicional[$ind][$j]=$camne['ADICIONAL'][$i][$j];
												//echo ' pp '.$adicional[$ind][$j];
											}
										}
									}
								
								
								//signo de comparacion
								if(!is_array($camne['SIGNO'][$i]))
								{
									if(count($camne['SIGNO'])==1 AND $i==0)
									{
										$signo[$ind]=$camne['SIGNO'][$i];
									}
									else
									{
										//bucle
										if(!empty($camne['SIGNO'][$i]))
										{
											$signo[$ind]=$camne['SIGNO'][$i];
										}
									}
								}
								else
								{
									//es mas de un campo
									$con_in = count($camne['SIGNO'][$i]);
									for($j=0;$j<$con_in;$j++)
									{
										$signo[$ind][$j]=$camne['SIGNO'][$i][$j];
										//echo ' pp '.$signo[$ind][$j];
									}
								}
								$ind++;
							}
						}
					}
					$i=0;
					while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
							//para colocar identificador unicode_decode
							if($ch!=null)
							{
								if(count($ch1)==2)
								{
									$cch=$ch1[0];
									?>
										<tr <?php echo "id=ta_".$row[$cch]."";?> >
									<?php
								}
								else
								{
									//casos con mas id
									$cch='';
									$camch='';
									//no manda fechas se debe colocar $row[$i]->format('Y-m-d');
									for($ca=0;$ca<count($ch1);$ca++)
									{
										if($ca<(count($ch1)-1))
										{
											$cch=$ch1[$ca];
                      if(is_object($row[$cch]))
                      {
                        $camch=$camch.$row[$cch]->format('Y-m-d').'--';
                      }else
                      {
                         $camch=$camch.$row[$cch].'--';
                      }
										}
									}
									$ca=$ca-1;
									?>
										<tr <?php echo "id=ta_".$camch."";?> >
									<?php
								}
							}
							else
							{
								?>
								<tr >
								<?php
							}
							if($ch!=null)
							{
								if(count($ch1)==2)
								{
									$cch=$ch1[0];
									echo "<td style='text-align: left; ".$bor1."'><input type='checkbox' id='id_".$row[$cch]."[]' name='".$ch1[1]."' value='".$row[$cch]."'
									onclick=\"validarc('id_".$row[$cch]."','".$tabla."')\"></td>";
								}
								else
								{
									//casos con mas id
									$cch='';
									$camch='';
									//no manda fechas se debe colocar $row[$i]->format('Y-m-d');
									for($ca=0;$ca<count($ch1);$ca++)
									{
										if($ca<(count($ch1)-1))
										{
											$cch=$ch1[$ca];
                      if(is_object($row[$cch]))
                      {
                        $camch=$camch.$row[$cch]->format('Y-m-d').'--';
                      }else
                      {
                         $camch=$camch.$row[$cch].'--';
                      }
										}
									}
									$ca=$ca-1;
									echo "<td style='text-align: left; ".$bor."'><input type='checkbox' id='id_".$camch."' name='".$ch1[$ca]."[]' value='".$camch."'
									onclick=\"validarc('id_".$camch."','".$tabla."')\"></td>";
									//die();
								}
							}
              if($button)
              {
                foreach ($button as $key => $value) {
                $nombre = str_replace(' ','_',$value['nombre']);
                $icono = $value['icon'];
                $tipo = $value['tipo'];
                $id = '';
                $datos = explode(',',$value['dato'][0]);
                foreach ($datos as $key2 => $value2) {
                  if(is_numeric($value2))
                    {
                      $id.= '\''.$row[$value2].'\',';
                    }else
                    {
                      $id.= '\''.$value2.'\',';
                    }
                }
                $id=substr($id,0,-1);
               
                 echo '<td><button class="btn btn-'.$tipo.' btn-sm"  type="button" onclick="'.$nombre.'('.$id.')"><i class ="'.$icono.'"></i></button></td>';             
                }
                }

              // print_r($button);die();
							//comparamos con los valores de los array para personalizar las celdas
							//para titulo color fila
							$cfila1='';
							$cfila2='';
							//indentar
							$inden='';
							$indencam=array();
							$indencam1=array();
							//contador para caso indentar
							$conin=0;
							//contador caso para saber si cumple varias condiciones ejemplo italica TC=P OR TC=C
							$ca_it=0;
							//variable para colocar italica
							$ita1='';
							$ita2='';
							//contador para caso italicas
							$conita=0;
							//valores de campo a afectar
							$itacam1=array();
							//variables para subrayar
							//valores de campo a afectar en caso subrayar
							$subcam1=array();
							//contador caso subrayar
							$consub=0;
							//contador caso para saber si cumple varias condiciones ejemplo subrayar TC=P OR TC=C
							$ca_sub=0;
							//variable para colocar subrayar
							$sub1='';
							$sub2='';
							for($i=0;$i<$ind;$i++)
							{
								if($tit[$i]=='color_fila')
								{
									if(!is_array($campoe[$i]))
									{
										//campo a comparar
										$tin=$campoe[$i];
										//comparamos valor
										if($signo[$i]=='=')
										{
											if($row[$tin]==$campov[$i])
											{
												if($adicional[$i]=='black')
												{
													//activa condicion
													$cfila1='<B>';
													$cfila2='</B>';
												}
											}
										}
									}
								}
								if($tit[$i]=='indentar')
								{	
									if(!is_array($campoe[$i]))
									{
										//campo a comparar
										$tin=$campoe[$i];
										//comparamos valor
										if($signo[$i]=='=')
										{
											if($campov[$i]=='contar')
											{
												$inden1 = explode(".", $row[$tin]);
												//echo ' '.count($inden1);
												//hacemos los espacios
												//$inden=str_repeat("&nbsp;&nbsp;", count($inden1));
												if(count($inden1)>1)
												{
													$indencam1[$conin]=str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", (count($inden1)-1));
												}
												else
												{
													$indencam1[$conin]="";
												}
												/*if(count($inden1)==1)
												{
													$inden='';
												}
												if(count($inden1)==2)
												{
													$inden='&nbsp;';
												}
												if(count($inden1)==3)
												{
													$inden='&nbsp;&nbsp;';
												}
												if(count($inden1)==3)
												{
													$inden='&nbsp;&nbsp;&nbsp;';
												}*/
											}
											$indencam[$conin]=$campoaf[$i];
											//echo $indencam[$conin].' dd ';
											$conin++;
										}
									}
								}
								if($tit[$i]=='italica')
								{	
									if(!is_array($campoe[$i]))
									{
										
									}
									else
									{
										//es mas de un campo
										$con_in = count($campoe[$i]);
										$ca_it=0;
										for($j=0;$j<$con_in;$j++)
										{
											$tin=$campoe[$i][$j];
											//echo ' pp '.$tin[$i][$j];
											//comparamos valor
											if($signo[$i][$j]=='=')
											{
												//echo $row[$tin].' wwww '.$campov[$i][$j].'<br/>';
												if($row[$tin]==$campov[$i][$j])
												{
													$ca_it++;
												}
											}
											//si es diferente
											if($signo[$i][$j]=='<>')
											{
												//echo $row[$tin].' wwww '.$campov[$i][$j].'<br/>';
												if($row[$tin]<>$campov[$i][$j])
												{
													$ca_it++;
												}
											}
											
										}
										$con_in = count($campoaf[$i]);
										for($j=0;$j<$con_in;$j++)
										{
											$itacam1[$conita]=$campoaf[$i][$j];
											//echo $itacam1[$conita].' ';
											$conita++;
										}
										//echo $ca_it.' cdcd '.count($campoe[$i]).'<br/>';
										if($ca_it==count($campoe[$i]))
										{
											$ita1='<em>';
											$ita2='</em>';
										}
										else
										{
											$ita1='';
											$ita2='';
										}
									}
									
								}
								if($tit[$i]=='subrayar')
								{	
									if(!is_array($campoe[$i]))
									{
										
									}
									else
									{
										//es mas de un campo
										$con_in = count($campoe[$i]);
										$ca_sub=0;
										$ca_sub1=0;
										for($j=0;$j<$con_in;$j++)
										{
											$tin=$campoe[$i][$j];
											//echo ' pp '.$tin[$i][$j];
											//comparamos valor
											if($signo[$i][$j]=='=')
											{
												//echo $row[$tin].' wwww '.$campov[$i][$j].'<br/>';
												if($row[$tin]==$campov[$i][$j])
												{
													$ca_sub++;
													$ca_sub1++;
												}
											}
											//si es diferente
											if($signo[$i][$j]=='<>')
											{
												//echo $row[$tin].' wwww '.$campov[$i][$j].'<br/>';
												if($row[$tin]<>$campov[$i][$j])
												{
													$ca_sub++;
												}
											}
											
										}
										$con_in = count($campoaf[$i]);
										for($j=0;$j<$con_in;$j++)
										{
											$subcam1[$consub]=$campoaf[$i][$j];
											//echo $subcam1[$consub].' ';
											$consub++;
										}
										//echo $ca_it.' cdcd '.count($campoe[$i]).'<br/>';
										$sub1='';
										$sub2='';
										//condicion para verificar si signo es "=" o no
										if($ca_sub1==0)
										{
											//condicion en caso de distintos
											if($ca_sub==count($campoe[$i]))
											{
												$sub1='<u>';
												$sub2='</u>';
											}
											else
											{
												$sub1='';
												$sub2='';
											}
										}
										else
										{
											$sub1='<u>';
											$sub2='</u>';
										}
									}
								}
							}
							//para check box
						
						for($i=0;$i<$cant;$i++)
						{
							//caso indentar
							for($j=0;$j<count($indencam);$j++)
							{
								if($indencam[$j]==$i)
								{
									$inden=$indencam1[$j];
								}
								else
								{
									$inden='';
								}
							}
							//caso italica
							$ita3="";
							$ita4="";
							for($j=0;$j<count($itacam1);$j++)
							{
								//echo $itacam1[$j].' ssscc '.$i;
								if($itacam1[$j]==$i)
								{
									$ita3=$ita1;
									$ita4=$ita2;
								}
								
							}
							//caso subrayado
							$sub3="";
							$sub4="";
							for($j=0;$j<count($subcam1);$j++)
							{
								//echo $itacam1[$j].' ssscc '.$i;
								if($subcam1[$j]==$i)
								{
									$sub3=$sub1;
									$sub4=$sub2;
								}
								
							}
							//caso de campos fechas
							for($j=0;$j<count($cam_fech);$j++)
							{
								//echo $itacam1[$j].' ssscc '.$i;
								if($cam_fech[$j]==$i)
								{
									//$row[$i]=$row[$i]->format('Y-m-d H:i:s');
                  if(is_object($row[$i]))
                      {
                        $row[$i]=$row[$i]->format('Y-m-d');
                      }else
                      {
                         $row[$i]=$row[$i];
                      }
									// $row[$i]=$row[$i]->format('Y-m-d');
								}
								
							}
							//echo "<br/>";
							//formateamos texto si es decimal
							if($tipo_campo[$i]=="style='text-align: right;'")
							{
								//si es cero colocar -
								//1.1.02.03.01.001 2017
								if(number_format($row[$i],2, ',', '.')==0.00 OR number_format($row[$i],2, ',', '.')=='0,00')
								{
									if($row[$i]>0)
									{
										echo "<td ".$tipo_campo[$i]." ".$bor.">".$cfila1.$ita3.$sub3.$inden.number_format($row[$i],2, ',', '.').$sub4.$ita4.$cfila2."</td>";
									}
									else
									{
										echo "<td ".$tipo_campo[$i]." ".$bor.">".$cfila1.$ita3.$sub3.$inden."-".$sub4.$ita4.$cfila2."</td>";
									}
									//echo "<td ".$tipo_campo[$i].">".$cfila1.$ita3.$sub3.$inden."-".$sub4.$ita4.$cfila2."</td>";
								}
								else
								{
									//si es negativo colocar rojo
									if($row[$i]<0)
									{
										//reemplazo una parte de la cadena por otra
										$longitud_cad = strlen($tipo_campo[$i]); 
										$cam2 = substr_replace($tipo_campo[$i],"color: red;'",$longitud_cad-1,1); 
										echo "<td ".$cam2." ".$bor."> ".$cfila1.$ita3.$inden.$sub3."".number_format($row[$i],2, '.', ',')."".$sub4.$ita4.$cfila2."</td>";
									}
									else
									{
										echo "<td ".$tipo_campo[$i]." ".$bor.">".$cfila1.$ita3.$inden.$sub3."".number_format($row[$i],2, '.', ',')."".$sub4.$ita4.$cfila2."</td>";
									}
								}
								
							}
							else
							{
								if(strlen($row[$i])<=50)
								{
									echo "<td ".$tipo_campo[$i]." ".$bor.">".$cfila1.$ita3.$inden.$sub3."".$row[$i]."".$sub4.$ita4.$cfila2."</td>";
								}
								else
								{
									$resultado = substr($row[$i], 0, 50);
									//echo $resultado; // imprime "ue"
									echo "<td ".$bor." ".$tipo_campo[$i]." data-toggle='tooltip' data-placement='left' title='".$row[$i]."'>".$cfila1.$ita3.$inden.$sub3."".$resultado."...".$sub4.$ita4.$cfila2."</td>";
								}
							}
						}
						/*$cam=$campo[$i];
						echo "<td>".$row['DG']."</td>";
						echo "<td>".$row['Codigo']."</td>";
						echo "<td>".$row['Cuenta']."</td>";
						echo "<td>".$row['Saldo_Anterior']."</td>";
						echo "<td>".$row['Debitos']."</td>";
						echo "<td>".$row['Creditos']."</td>";
						echo "<td>".$row['Saldo_Total']."</td>";
						echo "<td>".$row['TC']."</td>";*/
						 ?>
						  </tr>
						  <?php
						
						//$campo
						  //echo $row[$i].", <br />";
						  $i++;
						  if($cant==($i))
						  {
							  
							  //echo $cant.' ddddd '.$i;
							  $i=0;
							 
						  }
					}
		 ?>
			</table>
		</div>
		  <?php
	}
	else
	{
		if($base=='MYSQL')
		{
			$info_campo = $stmt->fetch_fields();
			$cant=0;
			//guardamos los campos
			$campo='';
			foreach ($info_campo as $valor) 
			{
				$cant++;
			}
			if($ch!=null)
			{
				$ch1 = explode(",", $ch);
				$cant++;
			}
			//si lleva o no border
			$bor='';
			if($b!=null and $b!='0')
			{
				$bor='table-bordered1';
				//style="border-top: 1px solid #bce8f1;"
			}
			//colocar cero a tabla en caso de no existir definida ninguna
			if($tabla==null OR $tabla=='0' OR $tabla=='')
			{
				$tabla=0;
			}
					//si lleva o no border
		$bor='';
		$bor1='';
		$bor2='';
		if($b!=null and $b!='0')
		{
			$bor='style="border: #b2b2b2 1px solid;"';
			$bor1='border: #b2b2b2 1px solid;';
			$bor2 = 'border';
			//style="border-top: 1px solid #bce8f1;"
		}
		//colocar cero a tabla en caso de no existir definida ninguna
		if($tabla==null OR $tabla=='0' OR $tabla=='')
		{
			$tabla=0;
		}
		?>
	<?php if($estilo)
	 { 
		echo  ' <style type="text/css">
				#datos_t table {
				border-collapse: collapse;
			}

			#datos_t table, th, td {
			  /*border: solid 1px black;*/
			  padding: 2px;
			}

			#datos_t tbody tr:nth-child(even) {
			  background:#fffff;
			}

			#datos_t tbody tr:nth-child(odd) {
			  background: #e2fbff;;
			}

			#datos_t tbody tr:nth-child(even):hover {
			  background: #DDB;
			}

			#datos_t tbody tr:nth-child(odd):hover {
			  background: #DDA;
			}

			.sombra {
			  width: 99%;
			  box-shadow: 10px 10px 6px rgba(0, 0, 0, 0.6);
			}
		</style>';
	 }
			?>

				<div class="sombra" style="">
				<!--<div class="box-body no-padding">-->
					<table <?php echo $bor2; ?> class="table table-striped table-hover" id='datos_t' >
						<?php
						if($ti!='' or $ti!=null)
						{
					?>
							<tr>
								<th  colspan='<?php echo $cant; ?>' style='text-align: center;background-color: #0086c7;color: #FFFFFF;' ><?php echo $ti; ?></th>
							</tr>
					<?php
						}
					?>
						<tr>
							<th colspan='<?php echo $cant; ?>' style='text-align: center;background-color: #0086c7;color: #FFFFFF;' ><?php echo $ti; ?></th>
						</tr>
						<thead>
							<?php
							//cantidad campos
							$cant=0;
							//guardamos los campos
							$campo='';
							//tipo de campos
							$tipo_campo=array();
							//guardamos posicion de un campo ejemplo fecha
							$cam_fech=array();
							//contador para fechas
							$cont_fecha=0;
							//obtenemos los campos 
							//en caso de tener check
							if($ch!=null)
							{
								echo "<th style='text-align: left;' id='tit_sel'>SEL</th>";
							}
							foreach ($info_campo as $valor) 
							{
								//$camp='';
								$i=0;
								//tipo de campo
								/*
								tinyint_    1   boolean_    1   smallint_    2 int_        3
								float_      4   double_     5   real_        5 timestamp_    7
								bigint_     8   serial      8   mediumint_    9 date_        10
								time_       11  datetime_   12  year_        13 bit_        16
								decimal_    246 text_       252 tinytext_    252 mediumtext_    252
								longtext_   252 tinyblob_   252 mediumblob_    252 blob_        252
								longblob_   252 varchar_    253 varbinary_    253 char_        254
								binary_     254
								*/
								$ban=0;
								//texto
								if( $valor->type==7 OR $valor->type==8 OR $valor->type==10
								 OR $valor->type==11 OR $valor->type==12 OR $valor->type==13 OR $valor->type==16 
								 OR $valor->type==252 OR $valor->type==253 OR $valor->type==254 )
								{
									$tipo_campo[($cant)]="style='text-align: left; width:40px;'";
									$ban=1;
								}
								if( $valor->type==10 OR $valor->type==11 OR $valor->type==12  )
								{
									$tipo_campo[($cant)]="style='text-align: left; width:80px;'";
									$ban=1;
								}
								//numero
								if($valor->type==3 OR $valor->type==2 OR $valor->type==4 OR $valor->type==5
								 OR isset($valor->type['Type'])==8  OR ($valor->type)==8  OR $valor->type==9 OR $valor->type==246)
								{
									//number_format($item_i['nombre'],2, ',', '.')
									$tipo_campo[($cant)]="style='text-align: right;'";
									$ban=1;
								}
								if($ban==0)
								{
									echo ' no existe tipo '.$valor->type.' '.$valor->name.' '.$valor->table;
								}
								echo "<th ".$tipo_campo[$cant].">".$valor->name."</th>";
											$camp=$valor->name;
											$campo[$cant]=$camp;
											//echo ' dd '.$campo[$cant];
											$cant++;
							}
							?>
						</thead>
						<?php
						//echo $cant.' fffff ';
							//obtener la configuracion para celdas personalizadas
							//campos a evaluar
							$campoe=array();
							//valor a verificar
							$campov=array();
							//campo a afectar 
							$campoaf=array();
							//adicional
							$adicional=array();
							//signos para comparar
							$signo=array();
							//titulo de proceso
							$tit=array();
							//indice de registros a comparar con datos
							$ind=0;
							//obtener valor en caso de mas de una condicion
							$con_in=0;
							if($camne!=null)
							{
								for($i=0;$i<count($camne['TITULO']);$i++)
								{
									if($camne['TITULO'][$i]=='color_fila')
									{	
										$tit[$ind]=$camne['TITULO'][$i];
										//temporar para indice
										//$temi=$i;
										//buscamos campos a evaluar
										$camneva = explode(",", $camne['CAMPOE'][$i]);
										//si solo es un campo
										if(count($camneva)==1)
										{
											$camneva1 = explode("=", $camneva[0]);
											$campoe[$ind]=$camneva1[0];
											$campov[$ind]=$camneva1[1];
											//echo ' pp '.$campoe[$ind].' '.$campov[$ind];
										}
										else
										{
											//hacer bucle
										}
										//para los campos a afectar
										if(count($camne['CAMPOA'])==1 AND $i==0)
										{
											if($camne['CAMPOA'][$i]=='TODOS' OR $camne['CAMPOA'][$i]='')
											{
												$campoaf[$ind]='TODOS';
											}
											else
											{
												//otras opciones
											}
										}
										else
										{
											//bucle
											if(!empty($camne['CAMPOA'][$i]))
											{
												if($camne['CAMPOA'][$i]=='TODOS' OR $camne['CAMPOA'][$i]='')
												{
													$campoaf[$ind]='TODOS';
												}
												else
												{
													//otras opciones
												}
											}
										}
										//valor adicional en este caso color
										if(count($camne['ADICIONAL'])==1 AND $i==0)
										{
											$adicional[$ind]=$camne['ADICIONAL'][$i];
										}
										else
										{
											//bucle
											if(!empty($camne['ADICIONAL'][$i]))
											{
												$adicional[$ind]=$camne['ADICIONAL'][$i];
											}
										}
										//signo de comparacion
										if(count($camne['SIGNO'])==1 AND $i==0)
										{
											$signo[$ind]=$camne['SIGNO'][$i];
										}
										else
										{
											//bucle
											if(!empty($camne['SIGNO'][$i]))
											{
												$signo[$ind]=$camne['SIGNO'][$i];
											}
										}
										$ind++;
										//echo ' pp '.count($camneva);
									}
									//caso italica, subrayar, indentar
									if($camne['TITULO'][$i]=='italica' OR $camne['TITULO'][$i]=='subrayar' OR $camne['TITULO'][$i]=='indentar')
									{
										$tit[$ind]=$camne['TITULO'][$i];
											//buscamos campos a evaluar
										if(!is_array($camne['CAMPOE'][$i]))
										{
											$camneva = explode(",", $camne['CAMPOE'][$i]);
											//si solo es un campo
											if(count($camneva)==1)
											{
												$camneva1 = explode("=", $camneva[0]);
												$campoe[$ind]=$camneva1[0];
												$campov[$ind]=$camneva1[1];
												//echo ' pp '.$campoe[$ind].' '.$campov[$ind];
											}
											else
											{
												//hacer bucle
											}
										}
										else
										{
											//es mas de un campo
											$con_in = count($camne['CAMPOE'][$i]);
											//recorremos registros
											for($j=0;$j<$con_in;$j++)
											{
												//echo $camne['CAMPOE'][$i][$j].' ';
												$camneva = explode(",", $camne['CAMPOE'][$i][$j]);
												//si solo es un campo
												if(count($camneva)==1)
												{
													$camneva1 = explode("=", $camneva[0]);
													$campoe[$ind][$j]=$camneva1[0];
													$campov[$ind][$j]=$camneva1[1];
													//echo ' pp '.$campoe[$ind][$j].' '.$campov[$ind][$j];
												}
											}
										}
										//para los campos a afectar
										if(!is_array($camne['CAMPOA'][$i]))
										{
											if(count($camne['CAMPOA'])==1 AND $i==0)
											{
												$campoaf[$ind]=$camne['CAMPOA'][$i];
											}
											else
											{
												//bucle
												if(!empty($camne['CAMPOA'][$i]))
												{
													//otras opciones
													$campoaf[$ind]=$camne['CAMPOA'][$i];
												}
											}
										}
										else
										{
											//recorremos el ciclo
											//es mas de un campo
											$con_in = count($camne['CAMPOA'][$i]);
											//recorremos registros
											for($j=0;$j<$con_in;$j++)
											{
												$campoaf[$ind][$j]=$camne['CAMPOA'][$i][$j];
												//echo ' pp '.$campoaf[$ind][$j];
											}
										}
										//valor adicional en este caso color
										
											if(count($camne['ADICIONAL'])==1 AND $i==0)
											{
												$adicional[$ind]=$camne['ADICIONAL'][$i];
											}
											else
											{
												//bucle
												if(!empty($camne['ADICIONAL'][$i]))
												{
													//es mas de un campo
													$con_in = count($camne['ADICIONAL'][$i]);
													for($j=0;$j<$con_in;$j++)
													{
														$adicional[$ind][$j]=$camne['ADICIONAL'][$i][$j];
														//echo ' pp '.$adicional[$ind][$j];
													}
												}
											}
										
										
										//signo de comparacion
										if(!is_array($camne['SIGNO'][$i]))
										{
											if(count($camne['SIGNO'])==1 AND $i==0)
											{
												$signo[$ind]=$camne['SIGNO'][$i];
											}
											else
											{
												//bucle
												if(!empty($camne['SIGNO'][$i]))
												{
													$signo[$ind]=$camne['SIGNO'][$i];
												}
											}
										}
										else
										{
											//es mas de un campo
											$con_in = count($camne['SIGNO'][$i]);
											for($j=0;$j<$con_in;$j++)
											{
												$signo[$ind][$j]=$camne['SIGNO'][$i][$j];
												//echo ' pp '.$signo[$ind][$j];
											}
										}
										$ind++;
									}
								}
							}
							$i=0;
							while ($row = $stmt->fetch_row()) 
							//while($row=$stmt->fetch_array())
							//while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
							{
								//para colocar identificador unicode_decode
								if($ch!=null)
								{
									if(count($ch1)==2)
									{
										$cch=$ch1[0];
										?>
											<tr <?php echo "id=ta_".$row[$cch]."";?> >
										<?php
									}
									else
									{
										//casos con mas id
										$cch='';
										$camch='';
										//no manda fechas se debe colocar $row[$i]->format('Y-m-d');
										for($ca=0;$ca<count($ch1);$ca++)
										{
											if($ca<(count($ch1)-1))
											{
												$cch=$ch1[$ca];
												$camch=$camch.$row[$cch].'--';
											}
										}
										$ca=$ca-1;
										?>
											<tr <?php echo "id=ta_".$camch."";?> >
										<?php
									}
								}
								else
								{
									?>
									<tr >
									<?php
								}
								if($ch!=null)
								{
									if(count($ch1)==2)
									{
										$cch=$ch1[0];
										echo "<td style='text-align: left;".$bor1."'><input type='checkbox' id='id_".$row[$cch]."' name='".$ch1[1]."[]' value='".$row[$cch]."'
										onclick=\"validarc('id_".$row[$cch]."','".$tabla."')\"></td>";
									}
									else
									{
										//casos con mas id
										$cch='';
										$camch='';
										//no manda fechas se debe colocar $row[$i]->format('Y-m-d');
										for($ca=0;$ca<count($ch1);$ca++)
										{
											if($ca<(count($ch1)-1))
											{
												$cch=$ch1[$ca];
												$camch=$camch.$row[$cch].'--';
											}
										}
										$ca=$ca-1;
										echo "<td style='text-align: left;' ".$bor."><input type='checkbox' id='id_".$camch."' name='".$ch1[$ca]."[]' value='".$camch."'
										onclick=\"validarc('id_".$camch."','".$tabla."')\"></td>";
										//die();
									}
								}
								//comparamos con los valores de los array para personalizar las celdas
								//para titulo color fila
								$cfila1='';
								$cfila2='';
								//indentar
								$inden='';
								$indencam=array();
								$indencam1=array();
								//contador para caso indentar
								$conin=0;
								//contador caso para saber si cumple varias condiciones ejemplo italica TC=P OR TC=C
								$ca_it=0;
								//variable para colocar italica
								$ita1='';
								$ita2='';
								//contador para caso italicas
								$conita=0;
								//valores de campo a afectar
								$itacam1=array();
								//variables para subrayar
								//valores de campo a afectar en caso subrayar
								$subcam1=array();
								//contador caso subrayar
								$consub=0;
								//contador caso para saber si cumple varias condiciones ejemplo subrayar TC=P OR TC=C
								$ca_sub=0;
								//variable para colocar subrayar
								$sub1='';
								$sub2='';
								for($i=0;$i<$ind;$i++)
								{
									if($tit[$i]=='color_fila')
									{
										if(!is_array($campoe[$i]))
										{
											//campo a comparar
											$tin=$campoe[$i];
											//comparamos valor
											if($signo[$i]=='=')
											{
												if($row[$tin]==$campov[$i])
												{
													if($adicional[$i]=='black')
													{
														//activa condicion
														$cfila1='<B>';
														$cfila2='</B>';
													}
												}
											}
										}
									}
									if($tit[$i]=='indentar')
									{	
										if(!is_array($campoe[$i]))
										{
											//campo a comparar
											$tin=$campoe[$i];
											//comparamos valor
											if($signo[$i]=='=')
											{
												if($campov[$i]=='contar')
												{
													$inden1 = explode(".", $row[$tin]);
													//echo ' '.count($inden1);
													//hacemos los espacios
													//$inden=str_repeat("&nbsp;&nbsp;", count($inden1));
													if(count($inden1)>1)
													{
														$indencam1[$conin]=str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", (count($inden1)-1));
													}
													else
													{
														$indencam1[$conin]="";
													}
												}
												$indencam[$conin]=$campoaf[$i];
												//echo $indencam[$conin].' dd ';
												$conin++;
											}
										}
									}
									if($tit[$i]=='italica')
									{	
										if(!is_array($campoe[$i]))
										{
											
										}
										else
										{
											//es mas de un campo
											$con_in = count($campoe[$i]);
											$ca_it=0;
											for($j=0;$j<$con_in;$j++)
											{
												$tin=$campoe[$i][$j];
												//echo ' pp '.$tin[$i][$j];
												//comparamos valor
												if($signo[$i][$j]=='=')
												{
													//echo $row[$tin].' wwww '.$campov[$i][$j].'<br/>';
													if($row[$tin]==$campov[$i][$j])
													{
														$ca_it++;
													}
												}
												//si es diferente
												if($signo[$i][$j]=='<>')
												{
													//echo $row[$tin].' wwww '.$campov[$i][$j].'<br/>';
													if($row[$tin]<>$campov[$i][$j])
													{
														$ca_it++;
													}
												}
												
											}
											$con_in = count($campoaf[$i]);
											for($j=0;$j<$con_in;$j++)
											{
												$itacam1[$conita]=$campoaf[$i][$j];
												//echo $itacam1[$conita].' ';
												$conita++;
											}
											//echo $ca_it.' cdcd '.count($campoe[$i]).'<br/>';
											if($ca_it==count($campoe[$i]))
											{
												$ita1='<em>';
												$ita2='</em>';
											}
											else
											{
												$ita1='';
												$ita2='';
											}
										}
										
									}
									if($tit[$i]=='subrayar')
									{	
										if(!is_array($campoe[$i]))
										{
											
										}
										else
										{
											//es mas de un campo
											$con_in = count($campoe[$i]);
											$ca_sub=0;
											$ca_sub1=0;
											for($j=0;$j<$con_in;$j++)
											{
												$tin=$campoe[$i][$j];
												//echo ' pp '.$tin[$i][$j];
												//comparamos valor
												if($signo[$i][$j]=='=')
												{
													//echo $row[$tin].' wwww '.$campov[$i][$j].'<br/>';
													if($row[$tin]==$campov[$i][$j])
													{
														$ca_sub++;
														$ca_sub1++;
													}
												}
												//si es diferente
												if($signo[$i][$j]=='<>')
												{
													//echo $row[$tin].' wwww '.$campov[$i][$j].'<br/>';
													if($row[$tin]<>$campov[$i][$j])
													{
														$ca_sub++;
													}
												}
												
											}
											$con_in = count($campoaf[$i]);
											for($j=0;$j<$con_in;$j++)
											{
												$subcam1[$consub]=$campoaf[$i][$j];
												//echo $subcam1[$consub].' ';
												$consub++;
											}
											//echo $ca_it.' cdcd '.count($campoe[$i]).'<br/>';
											$sub1='';
											$sub2='';
											//condicion para verificar si signo es "=" o no
											if($ca_sub1==0)
											{
												//condicion en caso de distintos
												if($ca_sub==count($campoe[$i]))
												{
													$sub1='<u>';
													$sub2='</u>';
												}
												else
												{
													$sub1='';
													$sub2='';
												}
											}
											else
											{
												$sub1='<u>';
												$sub2='</u>';
											}
										}
									}
								}
								//para check box
							
								for($i=0;$i<$cant;$i++)
								{
									//caso indentar
									for($j=0;$j<count($indencam);$j++)
									{
										if($indencam[$j]==$i)
										{
											$inden=$indencam1[$j];
										}
										else
										{
											$inden='';
										}
									}
									//caso italica
									$ita3="";
									$ita4="";
									for($j=0;$j<count($itacam1);$j++)
									{
										//echo $itacam1[$j].' ssscc '.$i;
										if($itacam1[$j]==$i)
										{
											$ita3=$ita1;
											$ita4=$ita2;
										}
										
									}
									//caso subrayado
									$sub3="";
									$sub4="";
									for($j=0;$j<count($subcam1);$j++)
									{
										//echo $itacam1[$j].' ssscc '.$i;
										if($subcam1[$j]==$i)
										{
											$sub3=$sub1;
											$sub4=$sub2;
										}
										
									}
									//caso de campos fechas
									for($j=0;$j<count($cam_fech);$j++)
									{
										//echo $itacam1[$j].' ssscc '.$i;
										if($cam_fech[$j]==$i)
										{
											//$row[$i]=$row[$i]->format('Y-m-d H:i:s');
											$row[$i]=$row[$i]->format('Y-m-d');
										}
										
									}
									//echo "<br/>";
									//formateamos texto si es decimal
									if($tipo_campo[$i]=="style='text-align: right;'")
									{
										//si es cero colocar -
										if(number_format($row[$i],2, '.', ',')==0 OR number_format($row[$i],2, '.', ',')=='0,00')
										{
											echo "<td ".$bor." ".$tipo_campo[$i].">".$cfila1.$ita3.$sub3.$inden."-".$sub4.$ita4.$cfila2."</td>";
										}
										else
										{
											//si es negativo colocar rojo
											if($row[$i]<0)
											{
												//reemplazo una parte de la cadena por otra
												$longitud_cad = strlen($tipo_campo[$i]); 
												$cam2 = substr_replace($tipo_campo[$i],"color: red;'",$longitud_cad-1,1); 
												echo "<td ".$bor." ".$cam2." > ".$cfila1.$ita3.$inden.$sub3."".number_format($row[$i],2, '.', ',')."".$sub4.$ita4.$cfila2."</td>";
											}
											else
											{
												echo "<td ".$bor." ".$tipo_campo[$i].">".$cfila1.$ita3.$inden.$sub3."".number_format($row[$i],2, '.', ',')."".$sub4.$ita4.$cfila2."</td>";
											}
										}
										
									}
									else
									{
										if(strlen($row[$i])<=50)
										{
											echo "<td ".$bor." ".$tipo_campo[$i].">".$cfila1.$ita3.$inden.$sub3."".$row[$i]."".$sub4.$ita4.$cfila2."</td>";
										}
										else
										{
											$resultado = substr($row[$i], 0, 50);
											//echo $resultado; // imprime "ue"
											echo "<td ".$bor." ".$tipo_campo[$i]." data-toggle='tooltip' data-placement='left' title='".$row[$i]."'>".$cfila1.$ita3.$inden.$sub3."".$resultado."...".$sub4.$ita4.$cfila2."</td>";
										}
									}
								}
								/*$cam=$campo[$i];
								echo "<td>".$row['DG']."</td>";
								echo "<td>".$row['Codigo']."</td>";
								echo "<td>".$row['Cuenta']."</td>";
								echo "<td>".$row['Saldo_Anterior']."</td>";
								echo "<td>".$row['Debitos']."</td>";
								echo "<td>".$row['Creditos']."</td>";
								echo "<td>".$row['Saldo_Total']."</td>";
								echo "<td>".$row['TC']."</td>";*/
								 ?>
								  </tr>
								  <?php
								
								//$campo
								  //echo $row[$i].", <br />";
								  $i++;
								  if($cant==($i))
								  {
									  
									  //echo $cant.' ddddd '.$i;
									  $i=0;
									 
								  }
							}
						?>
					</table>
				</div>
				<?php
		}
	}
}	


//FUNCION PARA ADCTUALIZAR GENERICA
function update_generico($datos,$tabla,$campoWhere) // optimizado javier farinango
{
	$campos_db = dimenciones_tabla($tabla);
	$conn = new db();
	$wherelist ='';
   	$sql = 'UPDATE '.$tabla.' SET '; 
   	 $set='';
     // print_r($campos_db);die();
   	foreach ($datos as $key => $value) {
   		foreach ($campos_db as $key => $value1) 
   		{
   			if($value1['COLUMN_NAME']==$value['campo'])
   				{
            // print_r($value1);die();
   					if($value1['CHARACTER_MAXIMUM_LENGTH'] != '' && $value1['CHARACTER_MAXIMUM_LENGTH'] != null)
   						{
   							$set .=$value['campo']."='".substr($value['dato'],0,$value1['CHARACTER_MAXIMUM_LENGTH'])."',";
   				    }else
   				    {
   				      $set .=$value['campo']."='".$value['dato']."',";
   				    }
   	       }

   		}
   		//print_r($value['campo']);
   	}
   	$set = substr($set,0,-1);
   	foreach ($campoWhere as $key => $value) {
   		//print_r($value['valor']);
   		if(is_numeric($value['valor']))
   		{
        if(isset($value['tipo']) && $value['tipo'] =='string')
        {

          $wherelist.= $value['campo']."='".$value['valor']."' AND ";
        }else
        {
          $wherelist.= $value['campo'].'='.$value['valor'].' AND ';
        }
   		}else{
   		  $wherelist.= $value['campo']."='".$value['valor']."' AND ";
   	    }
   	}
   	$wherelist = substr($wherelist,0,-5);
   	$where = " WHERE ".$wherelist;   
   	$sql = $sql.$set.$where;
     // print_r($sql);die();
   	return $conn->String_Sql($sql);
}


//FUNCION DE INSERTAR GENERICO
function insert_generico($tabla=null,$datos=null) // optimizado pero falta 
{
	$conn = new db();
  $cid = $conn->conexion();
	$sql = "SELECT * from Information_Schema.Tables where TABLE_TYPE = 'BASE TABLE' AND TABLE_NAME='".$tabla."' ORDER BY TABLE_NAME";
	// $stmt = sqlsrv_query( $cid, $sql);
  $datos1 = $conn->datos($sql);
  // print_r($sql);die();
  $tabla_ = $datos1[0]['TABLE_NAME'];
	if($tabla_!='')
	{
		//buscamos los campos
		$sql="SELECT        TOP (1) sys.sysindexes.rows
		FROM   sys.sysindexes INNER JOIN
		sys.sysobjects ON sys.sysindexes.id = sys.sysobjects.id
		WHERE   (sys.sysobjects.xtype = 'U') AND (sys.sysobjects.name = '".$tabla_."')
		ORDER BY sys.sysindexes.indid";
    $tabla_cc=0;
    $datos1 = $conn->datos($sql);
    $tabla_cc=$datos1[0]['rows'];
		
		$sql="SELECT COLUMN_NAME,DATA_TYPE,IS_NULLABLE,CHARACTER_MAXIMUM_LENGTH
		FROM Information_Schema.Columns
		WHERE TABLE_NAME = '".$tabla_."'";
		
		$stmt = sqlsrv_query( $cid, $sql);
		if( $stmt === false)  
		{  
			 echo "Error en consulta.\n";  
			 die( print_r( sqlsrv_errors(), true));  
		} 
		//consulta sql
		$sql_="INSERT INTO ".$tabla_."
			  (";
		$sql_v=" VALUES 
		(";
		$fecha_actual = date("Y-m-d"); 
		while( $obj = sqlsrv_fetch_object( $stmt)) 
		{
			if($obj->COLUMN_NAME!='ID')
			{
				$sql_=$sql_.$obj->COLUMN_NAME.",";
			}
		
    // print_r($obj);
			//recorremos los datos
      // print_r($datos);die();
			$ban=0;
			for($i=0;$i<count($datos);$i++)
			{
				if($obj->COLUMN_NAME==$datos[$i]['campo'])
				{
					if($obj->CHARACTER_MAXIMUM_LENGTH != '' && $obj->CHARACTER_MAXIMUM_LENGTH != null && $obj->CHARACTER_MAXIMUM_LENGTH != -1 && $obj->CHARACTER_MAXIMUM_LENGTH != 'null' && $obj->CHARACTER_MAXIMUM_LENGTH != 0)
					{
						$datos[$i]['dato'] =substr($datos[$i]['dato'],0, $obj->CHARACTER_MAXIMUM_LENGTH);
			    }
			       
					if($obj->DATA_TYPE=='int identity')
					{
						$sql_v=$sql_v."".$datos[$i]['dato'].",";
					}
					if($obj->DATA_TYPE=='nvarchar')
					{
						$sql_v=$sql_v."'".$datos[$i]['dato']."',";
					}
					if($obj->DATA_TYPE=='ntext')
					{
						$sql_v=$sql_v."'".$datos[$i]['dato']."',";
					}
					if($obj->DATA_TYPE=='tinyint')
					{
						$sql_v=$sql_v."".$datos[$i]['dato'].",";
					}
					if($obj->DATA_TYPE=='real')
					{
						$sql_v=$sql_v."".$datos[$i]['dato'].",";
					}
					if($obj->DATA_TYPE=='bit')
					{
						$sql_v=$sql_v."".$datos[$i]['dato'].",";
					}
					if($obj->DATA_TYPE=='smalldatetime' OR $obj->DATA_TYPE=='datetime')
					{
            if(!is_array($datos[$i]['dato'])) {              
            $sql_v=$sql_v."'".$datos[$i]['dato']."',";
            }else{
             $sql_v=$sql_v."'".$datos[$i]['dato']->format('Y-m-d')."',";
            }
					}
					if($obj->DATA_TYPE=='money')
					{
            if($datos[$i]['dato']!='.' && $datos[$i]['dato']!=''){
            $sql_v=$sql_v."".$datos[$i]['dato'].",";
            }else
            {
            $sql_v=$sql_v."0,";              
            }
					}
					if($obj->DATA_TYPE=='int')
					{
            if($datos[$i]['dato']!='.' && $datos[$i]['dato']!=''){
						$sql_v=$sql_v."".$datos[$i]['dato'].",";
            }else
            {
            $sql_v=$sql_v."0,";              
            }
					}
					if($obj->DATA_TYPE=='float')
					{
						$sql_v=$sql_v."".$datos[$i]['dato'].",";
					}
					if($obj->DATA_TYPE=='smallint')
					{
						$sql_v=$sql_v."".$datos[$i]['dato'].",";
					}
					if($obj->DATA_TYPE=='uniqueidentifier')
					{
						$sql_v=$sql_v."".$datos[$i]['dato'].",";
					}
					$ban=1;
				}
			}
			//por defaul
			if($ban==0)
			{
				if($obj->DATA_TYPE=='int identity')
				{
					$sql_v=$sql_v."0,";
				}
				if($obj->DATA_TYPE=='nvarchar')
				{
					$sql_v=$sql_v."'.',";
				}
				if($obj->DATA_TYPE=='ntext')
				{
					$sql_v=$sql_v."'.',";
				}
				if($obj->DATA_TYPE=='tinyint')
				{
					$sql_v=$sql_v."0,";
				}
				if($obj->DATA_TYPE=='real')
				{
					$sql_v=$sql_v."0,";
				}
				if($obj->DATA_TYPE=='bit')
				{
					$sql_v=$sql_v."0,";
				}
				if($obj->DATA_TYPE=='smalldatetime' OR $obj->DATA_TYPE=='datetime')
				{
					$sql_v=$sql_v."'".$fecha_actual."',";
				}
				if($obj->DATA_TYPE=='money')
				{
					$sql_v=$sql_v."0,";
				}
				if($obj->DATA_TYPE=='int')
				{
					if($obj->COLUMN_NAME=='ID')
					{
						$sql_v=$sql_v."";
					}
					else
					{
						$sql_v=$sql_v."0,";
					}
				}
				if($obj->DATA_TYPE=='float')
				{
					$sql_v=$sql_v."0,";
				}
				if($obj->DATA_TYPE=='smallint')
				{
					$sql_v=$sql_v."0,";
				}
				if($obj->DATA_TYPE=='uniqueidentifier')
				{
					$sql_v=$sql_v."0,";
				}
			}
		}
		$longitud_cad = strlen($sql_); 
		$cam2 = substr_replace($sql_,")",$longitud_cad-1,1); 
		$longitud_cad = strlen($sql_v); 
		$v2 = substr_replace($sql_v,")",$longitud_cad-1,1);

   // print_r($cam2.$v2);
   // die();
     $res = $conn->String_Sql($cam2.$v2);
     if($res==1)
     {
       return null;
     }
		
	}
}


function dimenciones_tabla($tabla) //---------optimizado por javier farinango
{
	$conn = new db();
  $tabla_="";
	$sql = "SELECT * from Information_Schema.Tables where TABLE_TYPE = 'BASE TABLE' AND TABLE_NAME='".$tabla."' ORDER BY TABLE_NAME";
  $datos = $conn->datos($sql);
  // print_r($datos);die();
  $tabla_ = $datos[0]['TABLE_NAME'];
	if($tabla_ != '')
	{
		$sql="SELECT COLUMN_NAME,DATA_TYPE,IS_NULLABLE,CHARACTER_MAXIMUM_LENGTH
		FROM Information_Schema.Columns
		WHERE TABLE_NAME = '".$tabla_."'";
		$campos = $conn->datos($sql);
		return $campos;
	}
}

function cabecera_tabla($tabla)
{
  $conn = new db();
  $sql = "SELECT COLUMN_NAME,DATA_TYPE,IS_NULLABLE,CHARACTER_MAXIMUM_LENGTH
    FROM Information_Schema.Columns
    WHERE TABLE_NAME ='".$tabla."'";
    $campos = $conn->datos($sql);
    return $campos;
}

function dimenciones_tabl($len)
{
  $px = 8;
  if($len > 60)
  {
    $val = 60*8;
    return $val.'px';
  }elseif ($len==1) {
     $val = ($len+3)*8;
    return $val.'px';
  }elseif ($len >= 10 && $len<=13){
     $val = ($len+3)*8;
    return $val.'px';
  }elseif ($len==10){
     $val = ($len+3)*8;
    return $val.'px';
  }elseif ($len>3 && $len <6) {
     $val = ($len+3)*8;
    return $val.'px';
  }elseif ($len==3){
     $val = ($len+3)*8;
    return $val.'px';
  }elseif ($len>13 && $len<60) {
     $val = ($len+3)*8;
    return $val.'px';
  }else
  {
     $val = ($len+3)*8;
    return $val.'px';
  }
}

  function numero_comprobante1($query,$empresa,$incrementa,$FechaComp)
  {
    $NumCodigo = 0;
    $NuevoNumero = False;
    if(strlen($FechaComp)<10)
    {
      $FechaComp = date('Y-m-d');
    }
    if($FechaComp == '00/00/0000')
    {
      $FechaComp = date('Y-m-d');
    }
    $Si_MesComp = false;
    if($empresa)
    {
      $NumEmpA = $_SESSION['INGRESO']['item'];
    }else
    {
      $NumEmpA = '000';
    }

    if ($query<>'') {
      $MesComp = '';
      if(strlen($FechaComp)>=10)
      {
        $MesComp = date('m', strtotime($FechaComp)); 
      }
      if($MesComp=='')
      {
        $MesComp = '01';
      }

      if($_SESSION['INGRESO']['Num_CD'] and $query=='Diario')
      {
        $query = $MesComp.''.$query;
      }
       if($_SESSION['INGRESO']['Num_CI'] and $query=='Ingresos')
      {
        $query = $MesComp.''.$query;
      }
       if($_SESSION['INGRESO']['Num_CE'] and $query=='Egresos')
      {
        $query = $MesComp.''.$query;
      }
       if($_SESSION['INGRESO']['Num_ND'] and $query=='NotaDebito')
      {
        $query = $MesComp.''.$query;
      }
       if($_SESSION['INGRESO']['Num_NC'] and $query=='NotaCredito')
      {
        $query = $MesComp.''.$query;
      }       

    }

    $conn = new db(); 
    $Result = array();
    $sql = "SELECT Numero, ID 
           FROM Codigos 
           WHERE Concepto = '".$query."' 
           AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
           AND Item = '".$_SESSION['INGRESO']['item']."' ";
    $Result = $conn->datos($sql);
      if(count($Result)>0)
      {
        $NumCodigo = $Result[0]['Numero'];
      }else
      {
        $NuevoNumero = true;
        $NumCodigo = 1;
        if($Num_Meses_CD && $Si_MesComp){$NumCodigo= $MesComp.'000001';}
        if($Num_Meses_CI && $Si_MesComp){$NumCodigo= $MesComp.'000001';}
        if($Num_Meses_CE && $Si_MesComp){$NumCodigo= $MesComp.'000001';}
        if($Num_Meses_ND && $Si_MesComp){$NumCodigo= $MesComp.'000001';}
        if($Num_Meses_NC && $Si_MesComp){$NumCodigo= $MesComp.'000001';}
      }

      if($NumCodigo > 0)
      {
        if($NuevoNumero)
        {
          $sql = "INSERT INTO Codigos (Periodo,Item,Concepto,Numero) 
                VALUES ('".$_SESSION['INGRESO']['periodo']."','".$_SESSION['INGRESO']['item']."','".$query."',".$NumCodigo.") ";
          $conn->String_Sql($sql);
        }
        if($incrementa)
        {
           $sql = "UPDATE Codigos
                SET Numero = Numero + 1
                WHERE Concepto = '".$query."'
                AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
                AND Item = '".$_SESSION['INGRESO']['item']."' ";
                $conn->String_Sql($sql);
        }
      }
      return generaCeros($NumCodigo,8);
  
  }

  function numero_comprobante($parametros) // por revisar repetida
  {
    $conn = new Conectar();
    $cid=$conn->conexion();
    if(isset($parametros['fecha']))
    {
      if($parametros['fecha']=='')
      {
        $fecha_actual = date("Y-m-d"); 
      }
      else
      {
        $fecha_actual = $parametros['fecha']; 
      }
    }
    else
    {
      $fecha_actual = date("Y-m-d"); 
    }
    $ot = explode("-",$fecha_actual);
    if($parametros['tip']=='CD')
    {
      if($_SESSION['INGRESO']['Num_CD']==1)
      {
        $sql ="SELECT        Periodo, Item, Concepto, Numero, ID
        FROM            Codigos
        WHERE        (Item = '".$_SESSION['INGRESO']['item']."') 
        AND (Periodo = '".$_SESSION['INGRESO']['periodo']."') 
        AND (Concepto = '".$ot[1]."Diario')";
        $stmt = sqlsrv_query( $cid, $sql);
        if( $stmt === false)  
        {  
           echo "Error en consulta PA.\n";  
           die( print_r( sqlsrv_errors(), true));  
        }
        $row_count=0;
        $i=0;
        $Result = array();
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
        {
          
          $Result[$i]['Numero'] = $row[3];
          
          //echo $Result[$i]['nombre'];
          $i++;
        }
        $codigo=$Result[0]['Numero']++;

        if($i==0)
        {
          return -1;
        }else
        {
          return "Comprobante de Ingreso No. ".$ot[0].'-'.$codigo;
        }
      }
    }
    if($parametros['tip']=='CI')
    {
      if($_SESSION['INGRESO']['Num_CI']==1)
      {
        $sql ="SELECT        Periodo, Item, Concepto, Numero, ID
        FROM            Codigos
        WHERE        (Item = '".$_SESSION['INGRESO']['item']."') 
        AND (Periodo = '".$_SESSION['INGRESO']['periodo']."') 
        AND (Concepto = '".$ot[1]."Ingresos')";
        
        $stmt = sqlsrv_query( $cid, $sql);
        if( $stmt === false)  
        {  
           echo "Error en consulta PA.\n";  
           die( print_r( sqlsrv_errors(), true));  
        }
        $row_count=0;
        $i=0;
        $Result = array();
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
        {
          
          $Result[$i]['Numero'] = $row[3];
          
          //echo $Result[$i]['nombre'];
          $i++;
        }
        $codigo=$Result[0]['Numero']++;
        echo "Comprobante de Ingreso No. ".$ot[0].'-'.$codigo;
        if($i==0)
        {
          echo 'no existe registro';
          //echo json_encode($Result);
        }
      }
    }
    if($parametros['tip']=='CE')
    {
      if($_SESSION['INGRESO']['Num_CE']==1)
      {
        $sql ="SELECT        Periodo, Item, Concepto, Numero, ID
        FROM            Codigos
        WHERE        (Item = '".$_SESSION['INGRESO']['item']."') 
        AND (Periodo = '".$_SESSION['INGRESO']['periodo']."') 
        AND (Concepto = '".$ot[1]."Egresos')";
        
        $stmt = sqlsrv_query( $cid, $sql);
        if( $stmt === false)  
        {  
           echo "Error en consulta PA.\n";  
           die( print_r( sqlsrv_errors(), true));  
        }
        $row_count=0;
        $i=0;
        $Result = array();
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
        {
          
          $Result[$i]['Numero'] = $row[3];
          
          //echo $Result[$i]['nombre'];
          $i++;
        }
        $codigo=$Result[0]['Numero']++;
        echo "Comprobante de Egreso No. ".$ot[0].'-'.$codigo;
        if($i==0)
        {
          echo 'no existe registro';
          //echo json_encode($Result);
        }
      }
    }
    if($parametros['tip']=='NC')
    {
      if($_SESSION['INGRESO']['Num_NC']==1)
      {
        $sql ="SELECT        Periodo, Item, Concepto, Numero, ID
        FROM            Codigos
        WHERE        (Item = '".$_SESSION['INGRESO']['item']."') 
        AND (Periodo = '".$_SESSION['INGRESO']['periodo']."') 
        AND (Concepto = '".$ot[1]."NotaCredito')";
        
        $stmt = sqlsrv_query( $cid, $sql);
        if( $stmt === false)  
        {  
           echo "Error en consulta PA.\n";  
           die( print_r( sqlsrv_errors(), true));  
        }
        $row_count=0;
        $i=0;
        $Result = array();
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
        {
          
          $Result[$i]['Numero'] = $row[3];
          
          //echo $Result[$i]['nombre'];
          $i++;
        }
        $codigo=$Result[0]['Numero']++;
        echo "Comprobante de Nota de Credito No. ".$ot[0].'-'.$codigo;
        if($i==0)
        {
          echo 'no existe registro';
          //echo json_encode($Result);
        }
      }
    }
    if($parametros['tip']=='ND')
    {
      if($_SESSION['INGRESO']['Num_ND']==1)
      {
        $sql ="SELECT        Periodo, Item, Concepto, Numero, ID
        FROM            Codigos
        WHERE        (Item = '".$_SESSION['INGRESO']['item']."') 
        AND (Periodo = '".$_SESSION['INGRESO']['periodo']."') 
        AND (Concepto = '".$ot[1]."NotaDebito')";
        
        $stmt = sqlsrv_query( $cid, $sql);
        if( $stmt === false)  
        {  
           echo "Error en consulta PA.\n";  
           die( print_r( sqlsrv_errors(), true));  
        }
        $row_count=0;
        $i=0;
        $Result = array();
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
        {
          
          $Result[$i]['Numero'] = $row[3];
          
          //echo $Result[$i]['nombre'];
          $i++;
        }
        $codigo=$Result[0]['Numero']++;
        echo "Comprobante de Nota de Debito No. ".$ot[0].'-'.$codigo;
        if($i==0)
        {
          echo 'no existe registro';
          //echo json_encode($Result);
        }
      }
    } 
  }

function ingresar_asientos_SC($parametros)  //revision parece repetida
{
  $conn = new db();
  $cid=$conn->conexion();
  //   $conn = new Conectar();
  //   $cid=$conn->conexion(); 
    $cod=$parametros['sub'];    
    if($parametros['t']=='P' OR $parametros['t']=='C')
    {
      $sql=" SELECT codigo FROM clientes WHERE CI_RUC='".$parametros['sub']."' ";
      $stmt = sqlsrv_query( $cid, $sql);
      if( $stmt === false)  
      {  
         echo "Error en consulta PA.\n";  
         die( print_r( sqlsrv_errors(), true));  
      }
      //echo $sql;
      $row_count=0;
      $i=0;
      $Result = array();
      while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
      {
        $cod=$row[0];
      }
    }
    else
    {
      //echo ' nnnn ';
      $cod=$parametros['sub'];
    }
    //verificamos valor
    $SC_No=0;
    $sql=" SELECT MAX(SC_No) AS Expr1 FROM  Asiento_SC 
    where CodigoU ='".$_SESSION['INGRESO']['CodigoU']."' 
    AND item='".$_SESSION['INGRESO']['item']."'";
    $stmt = sqlsrv_query( $cid, $sql);
    if( $stmt === false)  
    {  
       echo "Error en consulta PA.\n";  
       die( print_r( sqlsrv_errors(), true));  
    }
    //echo $sql;
    $row_count=0;
    $i=0;
    $Result = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
    {
      $SC_No=$row[0];
    }
    if($SC_No==null)
    {
      $SC_No=1;
    }
    else
    {
      $SC_No++;
    }
    $fecha_actual=$parametros['fecha_sc'];
    if($parametros['fac2']==0)
    {
      if($_SESSION['INGRESO']['modulo_']!='1')
      {
      $ot = explode("-",$fecha_actual);
      $fact2=$ot[0].$ot[1].$ot[2];
      }else
      {
        $fact2=$parametros['fac2'];
      }
      
    }
    else
    {
      $fact2=$parametros['fac2'];
      
    }
    if($parametros['mes']==0)
    {
      $sql="INSERT INTO Asiento_SC(Codigo ,Beneficiario,Factura ,Prima,DH,Valor,Valor_ME
           ,Detalle_SubCta,FECHA_V,TC,Cta,TM,T_No,SC_No
           ,Fecha_D ,Fecha_H,Bloquear,Item,CodigoU)
      VALUES
           ('".$cod."'
           ,'".substr($parametros['sub2'],0,60)."'
           ,'".$fact2."'
           ,0
           ,'".$parametros['tic']."'
           ,".$parametros['valorn']."
           ,0
           ,'".$parametros['Trans']."'
           ,'".$fecha_actual."'
           ,'".$parametros['t']."'
           ,'".$parametros['co']."'
           ,".$parametros['moneda']."
           ,".$parametros['T_N']."
           ,".$SC_No."
           ,null
           ,null
           ,0
           ,'".$_SESSION['INGRESO']['item']."'
           ,'".$_SESSION['INGRESO']['CodigoU']."')";
       $stmt = sqlsrv_query( $cid, $sql);
       //echo $sql;
      if( $stmt === false)  
      {  
         echo "Error en consulta PA.\n";  
         die( print_r( sqlsrv_errors(), true));  
      }
    }
    else
    {
      $sql="INSERT INTO Asiento_SC(Codigo ,Beneficiario,Factura ,Prima,DH,Valor,Valor_ME
      ,Detalle_SubCta,FECHA_V,TC,Cta,TM,T_No,SC_No
      ,Fecha_D ,Fecha_H,Bloquear,Item,CodigoU)
      VALUES
      ";
      $dia=0;
      for ($i=0;$i<$parametros['mes'];$i++)
      {
       
         $ot = explode("-",$fecha_actual);
         if($ot[1]=='01')
         {
            if($ot[2]>=28)
            {
             $dia=$ot[2];
              $year=esBisiesto_ajax($ot[0]);
            if($year==1)
            {
              $fecha_actual = date("Y-m-d",strtotime($ot[0].'-02-29')); 
              if($parametros['fac2']==0)
              {
                $fact2 = date("Ymd",strtotime($ot[0].'0229')); 
              }
              //$fact2 = $ot[0].'0229'; 
            }
            else
            {
              $fecha_actual = date("Y-m-d",strtotime($ot[0].'-02-28')); 
               if($parametros['fac2']==0)
              {
                $fact2 = date("Ymd",strtotime($ot[0].'0228')); 
              }
            }
            }
          else
          {
            $fecha_actual = date("Y-m-d",strtotime($fecha_actual."+ 1 month")); 
             if($parametros['fac2']==0)
            {
              $fact2 = date("Ymd",strtotime($fact2."+ 1 month")); 
            }
          }
           
         }
         else
         {
          
            if( $dia>=28)
            {
              $ot = explode("-",$fecha_actual);
              if($ot[1]=='02')
              {
                $fecha_actual = date("Y-m-d",strtotime($ot[0].'-03-31')); 
                if($parametros['fac2']==0)
                {
                  $fact2 = date("Ymd",strtotime($ot[0].'0331')); 
                }
              }
              if($ot[1]=='03')
              {
                $fecha_actual = date("Y-m-d",strtotime($ot[0].'-04-30')); 
                if($parametros['fac2']==0)
                {
                  $fact2 = date("Ymd",strtotime($ot[0].'0430')); 
                }
              }
              if($ot[1]=='04')
              {
                $fecha_actual = date("Y-m-d",strtotime($ot[0].'-05-31')); 
                if($parametros['fac2']==0)
                {
                  $fact2 = date("Ymd",strtotime($ot[0].'0531')); 
                }
              }
              if($ot[1]=='05')
              {
                $fecha_actual = date("Y-m-d",strtotime($ot[0].'-06-30')); 
                if($parametros['fac2']==0)
                {
                  $fact2 = date("Ymd",strtotime($ot[0].'0630')); 
                }
              }
              if($ot[1]=='06')
              {
                $fecha_actual = date("Y-m-d",strtotime($ot[0].'-07-31')); 
                if($parametros['fac2']==0)
                {
                  $fact2 = date("Ymd",strtotime($ot[0].'0731')); 
                }
              }
              if($ot[1]=='07')
              {
                $fecha_actual = date("Y-m-d",strtotime($ot[0].'-08-31')); 
                if($parametros['fac2']==0)
                {
                  $fact2 = date("Ymd",strtotime($ot[0].'0831')); 
                }
              }
              if($ot[1]=='08')
              {
                $fecha_actual = date("Y-m-d",strtotime($ot[0].'-09-30')); 
                if($parametros['fac2']==0)
                {
                  $fact2 = date("Ymd",strtotime($ot[0].'0930')); 
                }
              }
              if($ot[1]=='09')
              {
                $fecha_actual = date("Y-m-d",strtotime($ot[0].'-10-31')); 
                if($parametros['fac2']==0)
                {
                  $fact2 = date("Ymd",strtotime($ot[0].'1031')); 
                }
              }
              if($ot[1]=='10')
              {
                $fecha_actual = date("Y-m-d",strtotime($ot[0].'-11-30')); 
                if($parametros['fac2']==0)
                {
                  $fact2 = date("Ymd",strtotime($ot[0].'1130')); 
                }
              }
              if($ot[1]=='11')
              {
                $fecha_actual = date("Y-m-d",strtotime($ot[0].'-12-31')); 
                if($parametros['fac2']==0)
                {
                  $fact2 = date("Ymd",strtotime($ot[0].'1231')); 
                }
              }
            }
            else
            {

            // print_r($fecha_actual);
               $fecha_actual = date("Y-m-d",strtotime($fecha_actual)); 
               $mes = date("m",strtotime($fecha_actual)); 
               $y = date("y",strtotime($fecha_actual)); 
               $d = date("d",strtotime($fecha_actual));
               $m = $i+1;
               if($m<10)
               {
                 $m = '0'.$m;
               } 


            // print_r($fecha_actual);die();
              if($parametros['fac2']==0)
              {
                $fact2 = $y.$mes.$d.$m;
                // $fact2 = date("Ymd",strtotime($fact2."+ 1 month")); 
              }

            // print_r($fact2);die();
            }
           //}
           //}
          }
        // echo $fecha_actual.' <br>';
           $sql=$sql."('".$cod."'
         ,'".$parametros['sub2']."'
         ,'".$fact2."'
         ,0
         ,'".$parametros['tic']."'
         ,".$parametros['valorn']."
         ,0
         ,'".$parametros['Trans']."'
         ,'".$fecha_actual."'
         ,'".$parametros['t']."'
         ,'".$parametros['co']."'
         ,".$parametros['moneda']."
         ,".$parametros['T_N']."
         ,".$SC_No."
         ,null
         ,null
         ,0
         ,'".$_SESSION['INGRESO']['item']."'
         ,'".$_SESSION['INGRESO']['CodigoU']."'),";
         $SC_No++;

      //      if($i==1)
      // {

      //   print_r($sql);die();
      // }
      }
      //reemplazo una parte de la cadena por otra
      $longitud_cad = strlen($sql); 
      $cam2 = substr_replace($sql,"",$longitud_cad-1,1);
     
      $stmt = sqlsrv_query( $cid, $cam2);
        //echo $sql;
      if( $stmt === false)  
      {  
         echo "Error en consulta PA.\n";  
         die( print_r( sqlsrv_errors(), true));  
      }
      //echo $cam2;
    }
      $sql="SELECT Codigo, Beneficiario, Factura, Prima, DH, Valor, Valor_ME, Detalle_SubCta,T_No, SC_No,Item, CodigoU
      FROM Asiento_SC
      WHERE 
        Item = '".$_SESSION['INGRESO']['item']."' 
        AND CodigoU = '".$_SESSION['INGRESO']['Id']."' ";
      $stmt = sqlsrv_query( $cid, $sql);
      if( $stmt === false)  
      {  
         echo "Error en consulta PA.\n";  
         die( print_r( sqlsrv_errors(), true));  
      }
      else
      {
        $camne=array();
      }

}


function ingresar_asientos($parametros) //revision parece repetida
{

    $conn = new db();
    $cid=$conn->conexion();
    $va = $parametros['va'];
    $dconcepto1 = $parametros['dconcepto1'];
    $codigo = $parametros['codigo'];
    $cuenta = $parametros['cuenta'];
    $tc ='';
    if(isset($parametros['tc']))
    {
      $tc = $parametros['tc'];
    }
    if(isset($parametros['t_no']))
    {
      $t_no = $parametros['t_no'];
    }
    else
    {
      $t_no = 1;
    }
    if(isset($parametros['efectivo_as']))
    {
      $efectivo_as = $parametros['efectivo_as'];
    }
    else
    {
      $efectivo_as = '';
    }
    if(isset($parametros['chq_as']))
    {
      $chq_as = $parametros['chq_as'];
    }
    else
    {
      $chq_as = '';
    }
    
    $moneda = $parametros['moneda'];
    $tipo_cue = $parametros['tipo_cue'];
    
    if($efectivo_as=='' or $efectivo_as==null)
    {
      $efectivo_as=$fecha;
    }
    if($chq_as=='' or $chq_as==null)
    {
      $chq_as='.';
    }
    $parcial = 0;
    if($moneda==2)
    {
      $cotizacion = $parametros['cotizacion'];
      $con = $parametros['con'];
      if($tipo_cue==1)
      {
        if($con=='/')
        {
          $debe=$va/$cotizacion;
        }
        else
        {
          $debe=$va*$cotizacion;
        }
        $parcial = $va;
        $haber=0;
      }
      if($tipo_cue==2)
      {
        if($con=='/')
        {
          $haber=$va/$cotizacion;
        }
        else
        {
          $haber=$va*$cotizacion;
        }
        $parcial = $va;
        $debe=0;
      }
    }
    else
    {
      if($tipo_cue==1)
      {
        $debe=$va;
        $haber=0;
      }
      if($tipo_cue==2)
      {
        $debe=0;
        $haber=$va;
      }
    }
    //verificar si ya existe en ese modulo ese registro
    $sql="SELECT CODIGO, CUENTA
    FROM Asiento
    WHERE CODIGO = '".$codigo."' AND Item = '".$_SESSION['INGRESO']['item']."' 
    AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'  
    AND T_No=".$_SESSION['INGRESO']['modulo_'];
    if($tipo_cue==1)
    {
      $sql.=" AND DEBE = ".$va;
    }else
    {
      $sql.=" AND HABER = ".$va;
    }
    $sql.=" ORDER BY A_No ASC ";
    //print_r($sql);die();
    $stmt = sqlsrv_query( $cid, $sql);
    if( $stmt === false)  
    {  
       echo "Error en consulta PA.\n";  
       die( print_r( sqlsrv_errors(), true));  
    }
   
    // print_r(contar_registros($stmt));die();
    //para contar registro
    $i=0;
    $i=contar_registros($stmt);
    if($t_no == '60')
    {
      $i=0;
    }
    //echo $i.' -- '.$sql;
    //seleccionamos el valor siguiente
    $sql="SELECT TOP 1 A_No FROM Asiento
    WHERE (Item = '".$_SESSION['INGRESO']['item']."')
    ORDER BY A_No DESC";
    $A_No=0;
    $stmt = sqlsrv_query( $cid, $sql);
    if( $stmt === false)  
    {  
       echo "Error en consulta PA.\n";  
       die( print_r( sqlsrv_errors(), true));  
    }
    else
    {
      $ii=0;
      while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
      {
        $A_No = $row[0];
        $ii++;
      }
      
      if($ii==0)
      {
        $A_No++;
      }
      else
      {
        $A_No++;
      }
    }
    //si no existe guardamos
    if($i==0)
    {
      // print_r($va);print_r($haber);print_r($debe);die();
        $sql="INSERT INTO Asiento
        (CODIGO,CUENTA,PARCIAL_ME,DEBE,HABER,CHEQ_DEP,DETALLE,EFECTIVIZAR,CODIGO_C,CODIGO_CC
        ,ME,T_No,Item,CodigoU,A_No,TC)
        VALUES
        ('".$codigo."','".$cuenta."',".$parcial.",".$debe.",".$haber.",'".$chq_as."','".$dconcepto1."',
        '".$efectivo_as."','.','.',0,".$t_no.",'".$_SESSION['INGRESO']['item']."','".$_SESSION['INGRESO']['CodigoU']."',".$A_No.",'".$tc."')";
       $stmt = sqlsrv_query( $cid, $sql);
      if( $stmt === false)  
      {  
         echo "Error en consulta PA.\n";  
         die( print_r( sqlsrv_errors(), true));  
      }
      else
      {
        $sql="SELECT A_No,CODIGO,CUENTA,PARCIAL_ME,DEBE,HABER,CHEQ_DEP,DETALLE
        FROM Asiento
        WHERE 
          T_No=".$_SESSION['INGRESO']['modulo_']." AND
          Item = '".$_SESSION['INGRESO']['item']."' 
          AND CodigoU = '".$_SESSION['INGRESO']['Id']."' 
          ORDER BY A_No ASC ";
        $stmt = sqlsrv_query( $cid, $sql);
        if( $stmt === false)  
        {  
           echo "Error en consulta PA.\n";  
           die( print_r( sqlsrv_errors(), true));  
        }
        else
        {
          $camne=array();
          return 1;
        }
      }
    }
    else
    {
      //echo " ENTROOO ";
      echo "<script>
            Swal.fire({
              type: 'error',
              title: 'No se pudo guardar registro',
              text: 'Ya existe un registro con estos datos',
              footer: ''
          })
      </script>";
      $sql="SELECT A_No,CODIGO,CUENTA,PARCIAL_ME,DEBE,HABER,CHEQ_DEP,DETALLE
        FROM Asiento
        WHERE 
          T_No=".$_SESSION['INGRESO']['modulo_']." AND
          Item = '".$_SESSION['INGRESO']['item']."' 
          AND CodigoU = '".$_SESSION['INGRESO']['Id']."' 
          ORDER BY A_No ASC ";
        $stmt = sqlsrv_query( $cid, $sql);
        if( $stmt === false)  
        {  
           echo "Error en consulta PA.\n";  
           die( print_r( sqlsrv_errors(), true));  
        }
        else
        {
          return 1;
        }
    }    
}

function generar_comprobantes($parametros) //revision parece repetida
  {
    $conn = new db();
    $cid=$conn->conexion();
    if(isset($parametros['cotizacion']))
    {
      if($parametros['cotizacion']=='' or $parametros['cotizacion']==null)
      {
        $parametros['cotizacion']=0;
      }
    }
    else
    {
      $parametros['cotizacion']=0;
    }
    $codigo_b='';
    //echo $_POST['ru'].'<br>';
    if($parametros['ru']=='000000000')
    {
      $codigo_b='.';
    }
    else
    {
      //buscamos codigo
      $sql="  SELECT Codigo
          FROM Clientes
          WHERE((CI_RUC = '".$parametros['ru']."')) ";
          // print_r($sql);die();
      $stmt = sqlsrv_query( $cid, $sql);
      if( $stmt === false)  
      {  
         echo "Error en consulta PA.\n";  
         die( print_r( sqlsrv_errors(), true));  
      }
      else
      {
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
        {
          $codigo_b=$row[0];
        }
      }
      //caso en donde se necesite guardar el codigo de usuario como codigo beneficiario de comprobante
      if($codigo_b =='' or $codigo_b==null)
      {
        $codigo_b =$parametros['ru'];
      }
      //$codigo_b=$_POST['ru'];
    }
    //buscamos total
    if($parametros['tip']=='CE' or $parametros['tip']=='CI')
    {
      $sql="SELECT        SUM( DEBE) AS db, SUM(HABER) AS ha
      FROM            Asiento
      where T_No=".$_SESSION['INGRESO']['modulo_']." AND
          Item = '".$_SESSION['INGRESO']['item']."' 
          AND CodigoU = '".$_SESSION['INGRESO']['Id']."'  AND CUENTA 
      in (select Cuenta FROM  Catalogo_Cuentas 
      where Catalogo_Cuentas.Cuenta=Asiento.CUENTA AND (Catalogo_Cuentas.TC='CJ' OR Catalogo_Cuentas.TC='BA'))";
      
      $stmt = sqlsrv_query( $cid, $sql);
      $totald=0;
      $totalh=0;
      if( $stmt === false)  
      {  
         echo "Error en consulta PA.\n";  
         die( print_r( sqlsrv_errors(), true));  
      }
      else
      {
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
        {
          $totald=$row[0];
          $totalh=$row[1];
        }
      }
      if($parametros['tip']=='CE')
      {
        $parametros['totalh']=$totalh;
      }
      if($parametros['tip']=='CI')
      {
        $parametros['totalh']=$totald;
      }
    }
    if($parametros['concepto']=='')
    {
      $parametros['concepto']='.';
    }
    $num_com = explode("-", $parametros['num_com']);
    //verificamos que no se coloque fecha erronea
    $ot = explode("-",$parametros['fecha1']);
    $num_com1 = explode(".", $num_com[0]);
    $parametros['fecha1']=trim($num_com1[1]).'-'.$ot[1].'-'.$ot[2];
    
    //echo $_POST['fecha1'];
    //die();

    $sql="INSERT INTO Comprobantes
           (Periodo ,Item,T ,TP,Numero ,Fecha ,Codigo_B,Presupuesto,Concepto,Cotizacion,Efectivo,Monto_Total
           ,CodigoU ,Autorizado,Si_Existe ,Hora,CEj,X)
       VALUES
           ('".$_SESSION['INGRESO']['periodo']."'
           ,'".$_SESSION['INGRESO']['item']."'
           ,'N'
           ,'".$parametros['tip']."'
           ,".$num_com[1]."
           ,'".$parametros['fecha1']."'
           ,'".$codigo_b."'
           ,0
           ,'".$parametros['concepto']."'
           ,'".$parametros['cotizacion']."'
           ,0
           ,'".((is_numeric($parametros['totalh']))?$parametros['totalh']:0)."'
           ,'".$_SESSION['INGRESO']['CodigoU']."'
           ,'.'
           ,0
           ,'".date('h:i:s')."'
           ,'.'
           ,'.')";
        // echo $sql.'<br>';
           // print_r($sql);die();
        $stmt = sqlsrv_query( $cid, $sql);
      if( $stmt === false)  
      {  
         echo "Error en consulta PA.\n";  
         die( print_r( sqlsrv_errors(), true));  
      }

       //consultamos transacciones
       $sql="SELECT CODIGO,CUENTA,PARCIAL_ME  ,DEBE ,HABER ,CHEQ_DEP ,DETALLE ,EFECTIVIZAR,CODIGO_C,CODIGO_CC
        ,ME,T_No,Item,CodigoU ,A_No,TC
        FROM Asiento
        WHERE 
          T_No='".$_SESSION['INGRESO']['modulo_']."' AND
          Item = '".$_SESSION['INGRESO']['item']."' 
          AND CodigoU = '".$_SESSION['INGRESO']['Id']."' ";
      
      $sql=$sql." ORDER BY A_No ";
      $stmt = sqlsrv_query( $cid, $sql);
      if( $stmt === false)  
      {  
         echo "Error en consulta PA.\n";  
         die( print_r( sqlsrv_errors(), true));  
      }
      else
      {
        $i=0;
        $ii=0;
        $Result = array();
        $fecha_actual = date("Y-m-d"); 
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
        {
          $Result[$i]['CODIGO']=$row[0];
          $Result[$i]['CHEQ_DEP']=$row[5];
          $Result[$i]['DEBE']=$row[3];
          $Result[$i]['HABER']=$row[4];
          $Result[$i]['PARCIAL_ME']=$row[2];
          $Result[$i]['EFECTIVIZAR']=$row[7]->format('Y-m-d');
          $Result[$i]['CODIGO_C']=$row[8];
          $Result[$i]['DETALLE']=$row[6];
          
          $sql=" INSERT INTO Transacciones
            (Periodo ,T,C ,Cta,Fecha,TP ,Numero,Cheq_Dep,Debe ,Haber,Saldo ,Parcial_ME ,Saldo_ME ,Fecha_Efec ,Item ,X ,Detalle
            ,Codigo_C,Procesado,Pagar,C_Costo)
           VALUES
            ('".$_SESSION['INGRESO']['periodo']."'
            ,'N'
            ,0
            ,'".$Result[$i]['CODIGO']."'
            ,'".$parametros['fecha1']."'
            ,'".$parametros['tip']."'
            ,".$num_com[1]."
            ,'".$Result[$i]['CHEQ_DEP']."'
            ,".$Result[$i]['DEBE']."
            ,".$Result[$i]['HABER']."
            ,0
            ,".$Result[$i]['PARCIAL_ME']."
            ,0
            ,'".$Result[$i]['EFECTIVIZAR']."'
            ,'".$_SESSION['INGRESO']['item']."'
            ,'.'
            ,'".$Result[$i]['DETALLE']."'
            ,'".$Result[$i]['CODIGO_C']."'
            ,0
            ,0
            ,'.');";
           // echo $sql.'<br>';

           // print_r($sql);
          $stmt1 = sqlsrv_query( $cid, $sql);
          if( $stmt1 === false)  
          {  
             echo "Error en consulta PA.\n";  
             die( print_r( sqlsrv_errors(), true));  
          }
          $i++;
        }
        $sql="SELECT  Codigo,Beneficiario,Factura,Prima,DH,Valor ,Valor_ME,Detalle_SubCta,FECHA_V ,TC,Cta,TM
        ,T_No,SC_No,Fecha_D,Fecha_H,Bloquear,Item,CodigoU
        FROM Asiento_SC
        WHERE 
          Item = '".$_SESSION['INGRESO']['item']."' 
          AND CodigoU = '".$_SESSION['INGRESO']['Id']."' ";

        //echo $sql;
        $stmt = sqlsrv_query(   $cid, $sql);
        if( $stmt === false)  
        {  
           echo "Error en consulta PA.\n";  
           die( print_r( sqlsrv_errors(), true));  
        }
        else
        {
          $i=0;
          $Result = array();
          $fecha_actual = date("Y-m-d"); 
          while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) 
          {
            $Result[$i]['TC']=$row[9];
            $Result[$i]['Cta']=$row[10];
            $Result[$i]['FECHA_V']=$row[8]->format('Y-m-d');
            $Result[$i]['Codigo']=$row[0];
            $Result[$i]['Factura']=$row[2];
            $Result[$i]['Prima']=$row[3];
            $Result[$i]['DH']=$row[4];
            if($Result[$i]['DH']==1)
            {
              $Result[$i]['DEBITO']=$row[5];
              $Result[$i]['HABER']=0;
            }
            if($Result[$i]['DH']==2)
            {
              $Result[$i]['DEBITO']=0;
              $Result[$i]['HABER']=$row[5];
            }
            $sql="INSERT INTO Trans_SubCtas
                 (Periodo ,T,TC,Cta,Fecha,Fecha_V,Codigo ,TP,Numero ,Factura ,Prima ,Debitos ,Creditos ,Saldo_MN,Parcial_ME
                 ,Saldo_ME,Item,Saldo ,CodigoU,X,Comp_No,Autorizacion,Serie,Detalle_SubCta,Procesado)
             VALUES
                 ('".$_SESSION['INGRESO']['periodo']."'
                 ,'N'
                 ,'".$Result[$i]['TC']."'
                 ,'".$Result[$i]['Cta']."'
                 ,'".$parametros['fecha1']."'
                 ,'".$Result[$i]['FECHA_V']."'
                 ,'".$Result[$i]['Codigo']."'
                 ,'".$parametros['tip']."'
                 ,".$num_com[1]."
                 ,".$Result[$i]['Factura']."
                 ,".$Result[$i]['Prima']."
                 ,".$Result[$i]['DEBITO']."
                 ,".$Result[$i]['HABER']."
                 ,0
                 ,0
                 ,0
                 ,'".$_SESSION['INGRESO']['item']."'
                 ,0
                 ,'".$_SESSION['INGRESO']['CodigoU']."'
                 ,'.'
                 ,0
                 ,'.'
                 ,'.'
                 ,'.'
                 ,0)";
            //echo $sql.'<br>';

           // print_r($sql);die();
            $stmt1 = sqlsrv_query( $cid, $sql);
            if( $stmt1 === false)  
            {  
               echo "Error en consulta PA.\n";  
               die( print_r( sqlsrv_errors(), true));  
            }
          }
        }
        //incrementamos el secuencial
        if($_SESSION['INGRESO']['Num_CD']==1)
        {
          //para variable en html
          $num1=$num_com[1];
          $num_com[1]=$num_com[1]+1;
          //echo $num_com[1].'<br>'.$_POST['tip'].'<br>';
          if(isset($parametros['fecha1']))
          {
            //echo $_POST['fecha'];
            $fecha_actual = $parametros['fecha1']; 
          }
          else
          {
            $fecha_actual = date("Y-m-d"); 
          }
          $ot = explode("-",$fecha_actual);
          if($parametros['tip']=='CD')
          {
            $sql ="UPDATE Codigos set Numero=".$num_com[1]."
            WHERE        (Item = '".$_SESSION['INGRESO']['item']."') 
            AND (Periodo = '".$_SESSION['INGRESO']['periodo']."') 
            AND (Concepto = '".$ot[1]."Diario')";
          }
          if($parametros['tip']=='CI')
          {
            $sql ="UPDATE Codigos set Numero=".$num_com[1]."
            WHERE        (Item = '".$_SESSION['INGRESO']['item']."') 
            AND (Periodo = '".$_SESSION['INGRESO']['periodo']."') 
            AND (Concepto = '".$ot[1]."Ingresos')";
          }
          if($parametros['tip']=='CE')
          {
            $sql ="UPDATE Codigos set Numero=".$num_com[1]."
            WHERE        (Item = '".$_SESSION['INGRESO']['item']."') 
            AND (Periodo = '".$_SESSION['INGRESO']['periodo']."') 
            AND (Concepto = '".$ot[1]."Egresos')";
          }
          if($parametros['tip']=='ND')
          {
            $sql ="UPDATE Codigos set Numero=".$num_com[1]."
            WHERE        (Item = '".$_SESSION['INGRESO']['item']."') 
            AND (Periodo = '".$_SESSION['INGRESO']['periodo']."') 
            AND (Concepto = '".$ot[1]."NotaDebito')";
          }
          if($parametros['tip']=='NC')
          {
            $sql ="UPDATE Codigos set Numero=".$num_com[1]."
            WHERE        (Item = '".$_SESSION['INGRESO']['item']."') 
            AND (Periodo = '".$_SESSION['INGRESO']['periodo']."') 
            AND (Concepto = '".$ot[1]."NotaCredito')";
          }
          $stmt = sqlsrv_query( $cid, $sql);
          if( $stmt === false)  
          {  
             echo "Error en consulta PA.\n";  
             die( print_r( sqlsrv_errors(), true));  
          }
          //borramos temporales asientos
          $sql="DELETE FROM Asiento
          WHERE 
          T_No=".$_SESSION['INGRESO']['modulo_']." AND
          Item = '".$_SESSION['INGRESO']['item']."' 
          AND CodigoU = '".$_SESSION['INGRESO']['Id']."' ";
          // echo  $sql;
          $stmt = sqlsrv_query( $cid, $sql);
          if( $stmt === false)  
          {  
             echo "Error en consulta PA.\n";  
             die( print_r( sqlsrv_errors(), true));  
          }
          //borramos temporales asientos bancos
          
          $sql="DELETE FROM Asiento_B
          WHERE 
          Item = '".$_SESSION['INGRESO']['item']."' 
          AND CodigoU = '".$_SESSION['INGRESO']['Id']."' ";
          $stmt = sqlsrv_query( $cid, $sql);
          if( $stmt === false)  
          {  
             echo "Error en consulta PA.\n";  
             die( print_r( sqlsrv_errors(), true));  
          }
          //echo $sql;
          $stmt = sqlsrv_query( $cid, $sql);
          if( $stmt === false)  
          {  
             echo "Error en consulta PA.\n";  
             die( print_r( sqlsrv_errors(), true));  
          }
          //borramos asiento subcuenta
          $sql="DELETE FROM Asiento_SC
          WHERE 
            Item = '".$_SESSION['INGRESO']['item']."' 
            AND CodigoU = '".$_SESSION['INGRESO']['Id']."' ";
          $stmt = sqlsrv_query(   $cid, $sql);
          if( $stmt === false)  
          {  
             echo "Error en consulta PA.\n";  
             die( print_r( sqlsrv_errors(), true));  
          }
          //generamos comprobante
          //reporte_com($num1);
          return $num1;
        }
      }
  }


  //  function mayorizar_inventario_sp3() // optimizado
  // {

  //   $db = new db();
  //   $conn  = $db->conexion();
   
  //   $query ="{call [dbo].[sp_Mayorizar_Inventario] ('016','.','1723875561','01','4','7','2022-02-24','.')}";
  //   $result = sqlsrv_query($conn, $query);
  //   if( $result === false ) {
  //        print_r( sqlsrv_errors(), true);
  //        die();
  //   }
  //   echo $result ; 
  // }


  // function mayorizar_inventario_sp2() // optimizado
  // {
  //   // set_time_limit(300);
  //   // print_r($_SESSION['INGRESO']['CodigoU']);die();
  //   $db = new db();
  //   $conn  = $db->conexion();
  //   $params = array (
  //   '016',
  //   '.',
  //   '1723875561',
  //   '01',
  //   '4',
  //   '7',
  //   '2022-02-24',
  //   '.',
  //   );
  //    $query="EXEC DiskCover_CSB.dbo.sp_Mayorizar_Inventario @Item=?,@Periodo=?, @Usuario=?, @NumModulo=?, @DecPVP=?, @DecCosto=?,@FechaCorte=?,@TipoKardex=?";
  //   $result = sqlsrv_query($conn, $query, $params);
  //   if( $result === false ) {
  //        print_r( sqlsrv_errors(), true);
  //        die();
  //   }
  //   echo $result ;
  //   // set_time_limit(1024);
  //   // ini_set("memory_limit", "-1");
  //   // $desde = '2019/10/28';
  //   // $hasta = '2019/11/29';
  //    //  $TipoKardex = '';
  //    //  $fecha = date('Y-m-d');
  //    //  $_SESSION['INGRESO']['modulo_']='01';
  //    //  $conn = new db();
  //    //  $parametros = array(
  //    //  array(&,$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
  //    //  array(&,$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
  //    //  array(&,$_SESSION['INGRESO']['CodigoU'], SQLSRV_PARAM_IN),
  //    //  array(&,$_SESSION['INGRESO']['modulo_'], SQLSRV_PARAM_IN),
  //    //  array(&,$_SESSION['INGRESO']['Dec_PVP'], SQLSRV_PARAM_IN),
  //    //  array(&,$_SESSION['INGRESO']['Dec_Costo'], SQLSRV_PARAM_IN),
  //    //  array(&,$fecha, SQLSRV_PARAM_IN),
  //    //  array(&,$TipoKardex, SQLSRV_PARAM_INOUT),
  //    //  );     
  //    // $sql=" {CALL sp_Mayorizar_Inventario values(?,?,?,?,?,?,?,?)}";
  //    // // print_r($parametros);die();
  //    //  $respuesta = $conn->ejecutar_procesos_almacenados($sql,$parametros);
  //    //  return $respuesta;   
  // }
 
  function mayorizar_inventario_sp($fecha=false) // optimizado
  {
    // set_time_limit(1024);
    // ini_set("memory_limit", "-1");
    // $desde = '2019/10/28';
    // $hasta = '2019/11/29';
      $TipoKardex = '';      
      $fecha_corte = date('Y-m-d');
      if($fecha)
      {
        $fecha_corte = $fecha;  
      }
      $_SESSION['INGRESO']['modulo_']='01';
      $conn = new db();
      $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['CodigoU'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['modulo_'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['Dec_PVP'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['Dec_Costo'], SQLSRV_PARAM_IN),
      array(&$fecha_corte, SQLSRV_PARAM_IN),
      array(&$TipoKardex, SQLSRV_PARAM_INOUT),
      );     
     $sql="EXEC sp_Mayorizar_Inventario @Item=?,@Periodo=?, @Usuario=?, @NumModulo=?, @DecPVP=?, @DecCosto=?,@FechaCorte=?,@TipoKardex=?";
     // print_r($parametros);die();
      $respuesta = $conn->ejecutar_procesos_almacenados($sql,$parametros);
      // if($respuesta==1)
      // {
      //    print_r($TipoKardex);die();
      // }
      return $respuesta;   
  }

  function sp_Reindexar_Periodo() //optimizado
  {
    // set_time_limit(1024);
    // ini_set("memory_limit", "-1");
    // $desde = '2019/10/28';
    // $hasta = '2019/11/29';
    $_SESSION['INGRESO']['modulo_']='01';
    $conn = new db();
      $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN)
      );     
     $sql="EXEC sp_Reindexar_Periodo @Item=?, @Periodo=?";
     // print_r($_SESSION['INGRESO']);die();

      $respuesta = $conn->ejecutar_procesos_almacenados($sql,$parametros);
      return $respuesta;   
  }

  function Leer_Campo_Empresa($query)//  optimizado
  {
     $conn = new db();
    $sql = "SELECT ".$query." 
            FROM Empresas 
            WHERE Item = '".$_SESSION['INGRESO']['item']."'";
    $datos = $conn->datos($sql);
    // print_r($datos);die();
    return $datos[0][$query];

  }

function buscar_cta_iva_inventario()//  optimizado
  {
    $conn = new db();
    $sql = "SELECT * FROM Ctas_Proceso WHERE Periodo = '".$_SESSION['INGRESO']['periodo']."' AND Item='".$_SESSION['INGRESO']['item']."' AND Detalle = 'Cta_Iva_Inventario'";
    // print_r($sql); die();
    $datos = $conn->datos($sql);
     if(count($datos)>0)
     {
       return $datos[0]['Codigo'];
     }else
     {
       return -1;
     }

  }

  function buscar_en_ctas_proceso($query)//  optimizado
  {
    $conn = new db();
    $sql = "SELECT * FROM Ctas_Proceso WHERE Periodo = '".$_SESSION['INGRESO']['periodo']."' AND Item='".$_SESSION['INGRESO']['item']."' AND Detalle = '".$query."'";
    // print_r($sql); die();
    $datos = $conn->datos($sql);
     if(count($datos)>0)
     {
       return $datos[0]['Codigo'];
     }else
     {
       return -1;
     }

  }

function LeerCta($CodigoCta ) //optimizado
{
  $conn = new db();
  $Cuenta = G_NINGUNO;
  $Codigo = G_NINGUNO;
  $TipoCta = "G";
  $SubCta = "N";
  $TipoPago = "01";
  $Moneda_US = False;
  if(strlen(substr($CodigoCta, 1, 1)) >= 1){

     $sql = "SELECT Codigo, Cuenta, TC, ME, DG, Tipo_Pago
             FROM Catalogo_Cuentas
             WHERE Codigo = '".$CodigoCta."'
             AND Item = '".$_SESSION['INGRESO']['item']."'
             AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
    $datos = $conn->datos($sql);
    $datoscta = array();
     if (count($datos)>0) {
       foreach ($datos as $key => $value) {
         
         if (intval($value['Tipo_Pago']) <= 0){ $tipo= "01";}
         $datoscta[] = array( 'Codigo' =>$value["Codigo"],'Cuenta'=>$value["Cuenta"],'SubCta'=>$value["TC"],'Moneda_US'=>$value["ME"],'TipoCta'=>$value["DG"],'TipoPago'=> $tipo);
       }
     }
     return $datoscta;

  }
}

function costo_venta($codigo_inv)  // optimizado
  {
    $conn = new db();
    $sql = "SELECT  SUM(Entrada-Salida) as 'Existencia' 
    FROM Trans_Kardex
    WHERE Fecha <= '".date('Y-m-d')."'
    AND Codigo_Inv = '".$codigo_inv."'
    AND Item = '".$_SESSION['INGRESO']['item']."'
    AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
    AND T <> 'A'";
    // print_r($sql);die();
    $datos = $conn->datos($sql);
    return $datos;

  }
  
  function Leer_Seteos_Ctas($Det_Cta = "") // optimizado
  {
    //conexion
    $conn = new db();
    $RatonReloj;
    $Cta_Ret_Aux = "0";
    $sql = "SELECT * 
               FROM Ctas_Proceso 
               WHERE Item = '".$_SESSION['INGRESO']['item']."'
               AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
               AND Detalle = '".$Det_Cta."' ";
    $datos = $conn->datos($sql);
    
    return $datos[0]['Codigo'];
  }

 
  function SinEspaciosDer($texto = ""){
    $resultado = explode(" ", $texto);
    return $resultado[1];
  }

  function SinEspaciosIzq($texto = ""){
    $resultado = explode(" ", $texto);
    return $resultado[0];
  }

function grilla_generica_new($sql,$tabla,$id_tabla=false,$titulo=false,$botones=false,$check=false,$imagen=false,$border=1,$sombreado=1,$head_fijo=1,$tamaño_tabla=300,$num_decimales=2,$num_reg=false,$paginacion_view= false,$estilo=1)
{  
  $conn = new db();

  $ddl_reg = '';
  $val_pagina = '';
  $fun_pagina = '';
  $total_registros =0;
  $cid2=$conn->conexion();
  if($id_tabla=='' || $id_tabla == false)
  {
    $id_tabla = 'datos_t';
  }

    // print_r($sql);die();
  $pos = strpos($sql,'UNION');
if ($pos === false) {
  $unit = explode(',', $tabla);

    // print_r($sql);die();
    $sql2 = " SELECT COUNT_BIG(*) as 'reg' FROM ".$unit[0];
    // print_r($sql2);die();
    $datos2 =  $conn->datos($sql2);
    $total_registros = $datos2[0]['reg']; 
} else {
    $sql2 = $sql;
    // print_r($sql2);die();
    $datos2 =  $conn->datos($sql2);
    $tot_reg = count($datos2);
    $total_registros = $tot_reg;

}

  // $sql2 = " SELECT COUNT(*) as 'reg' FROM ".$tabla;
  

  if($num_reg && count($num_reg)>1)
  {
    $ddl_reg = $num_reg[1];
    $val_pagina = $num_reg[0];
    $fun_pagina = $num_reg[2];
    $sql.= " OFFSET ".$num_reg[0]." ROWS FETCH NEXT ".$num_reg[1]." ROWS ONLY;";
  }else
  {
    $ddl_reg = '15';
    $val_pagina = '0';
    //$fun_pagina = $num_reg[2];
    $paginacion = array('0','15');
    //$sql.= " OFFSET ".$paginacion[0]." ROWS FETCH NEXT ".$paginacion[1]." ROWS ONLY;";
  }

  // print_r($sql);die();

  $cid=$conn->conexion();
  $cid1=$conn->conexion();

  $datos = $conn->datos($sql);
  // $stmt = sqlsrv_query($cid, $sql);
  $columnas = sqlsrv_query($cid1, $sql);
  $columnas = sqlsrv_field_metadata($columnas);
  // print_r($tabla);
  // print_r($columnas);die();


  $columnas_uti = array();
  $uti = datos_tabla($tabla);
  $existe = false;
  foreach ($columnas as $key => $value) {
    // aplica cuando la consulta sql tiene mas tablas incluidas
    if(count($uti)>0){
        foreach ($uti as $key1 => $value1) {    
          if($value1['COLUMN_NAME']==$value['Name'])
          {
            array_push($columnas_uti, $value1['COLUMN_NAME']);
            $existe = false;
            break;
          }else
          {
            $existe = true;
            break;
          }
        }
        if($existe)
        {
          array_push($columnas_uti,$value['Name']);
         // print_r($columnas_uti);die();
          $existe = false;
        }
      }else
      {
        array_push($columnas_uti,$value['Name']);
      }
      
  }

  // print_r($datos);
  // print_r($uti);
  // print_r($columnas);
  // print_r($columnas_uti);die();
  $medida_body =array();
  $alinea_body =array();
  
  $tbl='';
  if($estilo==1)
  {
 $tbl.=' <style type="text/css" id="estilo_tabla">';
  
    $tbl.='
  #'.$id_tabla.' tbody tr:nth-child(even) { background:#fffff;}
  #'.$id_tabla.' tbody tr:nth-child(odd) { background: #e2fbff;}
  #'.$id_tabla.' tbody tr:nth-child(even):hover {  background: #DDB;}
  #'.$id_tabla.' thead { background: #afd6e2; }
  #'.$id_tabla.' tbody tr:nth-child(odd):hover {  background: #DDA;}
 ';

 if($border)
 {
  $tbl.=' #'.$id_tabla.' table {border-collapse: collapse;}
  #'.$id_tabla.' table, th, td {  border: solid 1px #aba0a0;  padding: 2px;  }'; 
 }

 if($sombreado)
 {
  $tbl.='#'.$id_tabla.' tbody { box-shadow: 10px 10px 6px rgba(0, 0, 0, 0.6); }
   #'.$id_tabla.' thead { background: #afd6e2;  box-shadow: 10px 0px 6px rgba(0, 0, 0, 0.6);} ';
 }

 if($head_fijo)
 {
 $tbl.='#'.$id_tabla.' tbody { display:block; height:'.$tamaño_tabla.'px; width:auto; overflow-y:scroll;}
  #'.$id_tabla.' thead,tbody tr {    display:table;  width:100%;  table-layout:fixed; } 
  #'.$id_tabla.' thead { width: calc( 100% - 1.2em )/* scrollbar is average 1em/16px width, remove it from thead width */}
  /*thead tr {    display:table;  width:98.5%;  table-layout:fixed;  }*/ ';
 } 

 $tbl.="</style>";

 }

// print_r($tbl);die();
if($titulo)
 {
  // $num = count($columnas_uti);
   $tbl.="<div class='text-center'><b>".$titulo."</b></div>";
 }

 $tbl.= '<div class="table-responsive" style="overflow-x: scroll;">
 <div style="width:fit-content;padding-right:20px">';
 // ''paginado
 $funcion_e ='';
 if($fun_pagina!='')
 {
   $funcion_e = $fun_pagina.'()';
 }
 if($paginacion_view)
 {
  $tbl.= '
<select id="ddl_reg" onChange="'.$funcion_e.'"><option value="15">15</option><option value="25">25</option><option value="50">50</option></select>
  <nav aria-label="...">
  <input type="hidden" value="0" id="pag">
  <ul class="pagination" style="margin:0px">
  <li class="page-item" onclick="paginacion(0);'.$funcion_e.'">
      <span class="page-link">Inicio</span>
     </li>';
    if($fun_pagina==''){
      for ($i=1; $i <= 10; $i++) {
       $pa = $ddl_reg*($i-1); 
       if($val_pagina==$pa)
        {
          $tbl.=' <li class="page-item  active" id="pag_'.$pa.'" onclick="paginacion(\''.$pa.'\')"><a class="page-link" href="#">'.$i.'</a></li>';
        }else
        {
           $tbl.=' <li class="page-item" id="pag_'.$pa.'" onclick="paginacion(\''.$pa.'\')"><a class="page-link" href="#">'.$i.'</a></li>';
        }
      }
    }else
    {
      $tab = ($val_pagina/$ddl_reg);
      $inicio = 1;
      $tab_paginas = 10;
      $co = 0;
      // print_r($tab);die();
      while (($tab+1)>=$tab_paginas) {
        $inicio = $tab_paginas;
        $tab_paginas = $tab_paginas+10;
        // $co = $co+1;
      }

      for ($i=$inicio; $i <= $tab_paginas; $i++) {
       $pa = $ddl_reg*($i-1); 
       if($val_pagina==$pa)
        {
          $tbl.=' <li class="page-item  active" id="pag_'.$pa.'" onclick="paginacion(\''.$pa.'\';'.$fun_pagina.'()"><a class="page-link" href="#">'.$i.'</a></li>';
        }else
        {
           $tbl.=' <li class="page-item" id="pag_'.$pa.'" onclick="paginacion(\''.$pa.'\');'.$fun_pagina.'()"><a class="page-link" href="#">'.$i.'</a></li>';
        }
      }

    }
    $tbl.='<li class="page-item">
      <a class="page-link" href="#">Ultimo</a>
    </li>
  </ul>
</nav>';
}
 $tbl.='<table class="table-sm" id="'.$id_tabla.'"><thead>';
  //cabecera de la consulta sql//
 if($botones)
  {
    $med_b = count($botones)*42;
    $tbl.='<th style="width:'.$med_b.'px"></th>';
  }
  if($check)
  {
     $label = false;
     if(isset($check[0]['text_visible']))
      {
        $label = $check[0]['text_visible'];
      }
      if($label==true)
      {
        $tbl.='<th width="'.dimenciones_tabl(strlen($check[0]['boton'])).'" class="text-center">'.$check[0]['boton'].'</th>';
      }else
      {
        $tbl.='<th width="30px" class="text-center"></th>';
      }
  }
  // print_r($datos);
  // print_r($uti);
   //print_r($columnas_uti);die();
  foreach ($columnas_uti as $key => $value) {
    //calcula dimenciones de cada columna 
    if(is_array($value))
    {
    if($value['CHARACTER_MAXIMUM_LENGTH']!='')
    {
      if($value['CHARACTER_MAXIMUM_LENGTH']>=60)
      {
        $medida = '300px';
      }else{
        if(($value['CHARACTER_MAXIMUM_LENGTH']<=11 && strlen($value['COLUMN_NAME'])>2 && $value['COLUMN_NAME']!='Codigo' && $value['COLUMN_NAME']!='CodigoU' && $value['CHARACTER_MAXIMUM_LENGTH']!=-1))
        {        
          $medida = dimenciones_tabl(strlen($value['COLUMN_NAME']));       
        }else if($value['COLUMN_NAME']=='Codigo' || $value['COLUMN_NAME']=='CodigoU'){

          $medida = '100px'; 

        // print_r($medida);die();      
        }else
        {
          if($value['CHARACTER_MAXIMUM_LENGTH']!=-1)
          {
            // print_r('expression');die()
            $med_nom = str_replace('px','', dimenciones_tabl(strlen($value['COLUMN_NAME'])));
            $medida = str_replace('px','',dimenciones_tabl($value['CHARACTER_MAXIMUM_LENGTH']));
            if($medida<$med_nom)
            {
              $medida = dimenciones_tabl(strlen($value['COLUMN_NAME']));
            }else
            {
               $medida = dimenciones_tabl($value['CHARACTER_MAXIMUM_LENGTH']);
               // print_r($medida);die();
            }
          }else
          {
             $medida ='250px';
          }
        }
      }
   }else
   {
    if($value['DATA_TYPE']=='datetime')
        {
          $medida = '100px';

        }else if($value['DATA_TYPE']=='int')
        {
          $medida ='70px';
        }else if($value['DATA_TYPE']=='money')
        {
          $medida ='75px';
        }
        else{
        $medida = dimenciones_tabl(strlen($value['COLUMN_NAME']));
       }
    // print_r('expression');die();
     // $medida = dimenciones_tabl(strlen($value['COLUMN_NAME']));
   }
   //fin de dimenciones
   //alinea dependiendo el tipo de dato que sea
   $alineado = 'text-left'; 
     switch ($value['DATA_TYPE']) 
     {
        case 'nvarchar':
            $alineado = 'text-left'; 
          break;                
        case 'int':            
        case 'money':            
        case 'real':                             
            $alineado = 'text-right';  
          break;
        case 'bit':       
            $alineado = 'text-left'; 
          break;
        case 'datetime':       
          $alineado = 'text-left'; 
          // $medida = dimenciones_tabl(strlen($value['COLUMN_NAME']));
          // $medida ='100px';
        break;
      } 
  //fin de alineacion        

    $tbl.='<th class="'.$alineado.'" style="width:'.$medida.'">'.$value['COLUMN_NAME'].'</th>'; 
    // print_r($tbl);die();
    array_push($medida_body, $medida);
    array_push($alinea_body, $alineado);
  }else
  {
  //   if($tabla==' Trans_SubCtas As T,Clientes As C '){
     // print_r($columnas);die();
  // }
    foreach ($columnas as $key6 => $value6) {
      if($value == $value6['Name'])
      {
         $medida = '300px';
         $alineado = 'text-left';
         if($value6['Size']<60)
          {
            if($value6['Size']!='')
            {
              $medida = determinaAnchoTipos($value6['Type'],$EsCorto=false,$value6['Size'],$value6['Name']); 
              // dimenciones_tabl($value6['Size']);
            }else
            {
              $medida = determinaAnchoTipos($value6['Type'],$EsCorto=false,false,$value6['Name']);
              // dimenciones_tabl(strlen($value6['Name']));
            }
          }
           switch ($value6['Type']) 
            {
               case '-9':// campo nvarchar 
                   $alineado = 'text-left'; 
                 break;                
               case '4':  //campo int          
               case '7':  //campo real          
               case '3':  //campo money                         
                   $alineado = 'text-right';  
                 break;
               case '-7':      //campo bit 
                   $alineado = 'text-left'; 
                 break;
               case '93':       // campo date
                 $alineado = 'text-left'; 
               break;
             }
           // $medida1 = explode('p',$medida);
           // $medida1  = ($medida1[0]-6).'px';
           // // $medida1 = $medida1.'px'; 
           $tbl.='<th class="'.$alineado.'" style="width:'.$medida.'">'.$value6['Name'].'</th>'; 
           array_push($medida_body, $medida);
           array_push($alinea_body, $alineado);
           break;
      }
    }
   
    // $tbl.='<th class="'.$alineado.'" style="width:'.$medida.'">'.$value['COLUMN_NAME'].'</th>'; 
    // array_push($medida_body, $medida);
  }
  }
  //fin de cabecera
  $tbl.='</thead><tbody>';

//cuerpo de la consulta
  $colum = 0;
  // print_r($datos);die();
  if(count($datos)>0)
  {
  foreach ($datos as $key => $value) {
     $tbl.='<tr>';
     //crea botones
       if($botones)
        {
          $med_b = count($botones)*42;
          $tbl.='<td style="width:'.$med_b.'px">';
          foreach ($botones as $key3 => $value3) {
            $valor = '';
            $tipo = 'default';
            $icono = '<i class="far fa-circle nav-icon"></i>';
            if(isset($value3['tipo']))
            {
              $tipo = $value3['tipo'];
            }
            if(isset($value3['icono']))
            {
              $icono = $value3['icono'];
            }
            $k = explode(',', $value3['id']);
            foreach ($k as $key4 => $value4) {
              // print_r($value);die();
              if(isset($value[$value4]))
              {
                $valor.="'".$value[$value4]."',";
              }else
              {
                $valor.="'".$value4."',";
              }
            }
            if($valor!='')
            {
              $valor = substr($valor,0,-1);
            }
            $funcion = str_replace(' ','_', $value3['boton']);
            $tbl.='<button type="button" class="btn btn-xs btn-'.$tipo.'" onclick="'.$funcion.'('.$valor.')" title="'.$value3['boton'].'">'.$icono.'</button>';
          }
          $tbl.='</td>';
        }
        //fin de crea botones
        //crea los check
        if($check)
        {
           $label = false;
           $med_ch ='30px';
           if(isset($check[0]['text_visible']))
            {
              $label = $check[0]['text_visible'];
            }
            if($label)
            {
              $med_ch = dimenciones_tabl(strlen($check[0]['boton']));
            }
          $tbl.='<td width="'.$med_ch.'" class="text-center">';
          foreach ($check as $key3 => $value3) {
            $valor = '';
            $k = explode(',', $value3['id']);
            foreach ($k as $key4 => $value4) {
              // print_r($value);die();
              $valor.="'".$value[$value4]."',";
            }
            if($valor!='')
            {
              $valor = substr($valor,0,-1);
            }
            $funcion = str_replace(' ','_', $value3['boton']);
            
            $tbl.='<label><input type="checkbox" onclick="'.$funcion.'('.$valor.')" title="'.$value3['boton'].'"></label>';
            
          }
          $tbl.='</td>';
        }
        //fin de creacion de checks

        // print_r($medida_body);die();
        // print_r($value);die();
     foreach ($value as $key1 => $value1) { 
             $medida = $medida_body[$colum]; 
             $alineado = $alinea_body[$colum]; 
             if(is_object($value1))
             {
               $tbl.='<td style="width:'.$medida.'">'.$value1->format('Y-m-d').'</td>';              
             }
             else
             {
              if($alineado=='text-left')
              {                
                  $tbl.='<td style="width:'.$medida.'" class="'.$alineado.'">'.$value1.'</td>';  
                  // $tbl.='<td style="width:'.$medida.'" class="'.$alineado.'">'.$value1.'</td>';  
              }else
              {
                if(is_int($value1))
                {

                 $tbl.='<td style="width:'.$medida.'" class="'.$alineado.'">'.$value1.'</td>';
                }else
                {  
                    $color = 'black';                
                  if($value1<0)
                  {
                    $color = 'red';
                  }
                 $tbl.='<td style="width:'.$medida.'; color:'.$color.'" class="'.$alineado.'">'.number_format(floatval($value1),$num_decimales,'.','').'</td>'; 
                }
              }    
             }
            $colum+=1;    
         }

         $colum=0;  
      }
    }else
    {      
    }          
          
  $tbl.='</tbody>
      </table>
      </div>
      <script>
      $("#ddl_reg").val('.$ddl_reg.');
      function paginacion(p)
      {
        $("#pag").val(p);
      }
  </script>
      
    </div>';
    // print_r($tbl);die();


    return $tbl;

}

function determinaAnchoTipos($tipo,$EsCorto=false,$size=false,$namecol=false)
{
   $valor = 0;
   $pixel = 8;
   if($tipo!='' && $size!=false && $size>17 ){ $valor = $size*$pixel;}

   if($EsCorto){

     //boolean
    if($tipo=='-7'){ $valor = strlen("Si_")*4;}
    //date
    if($tipo=='93'){ $valor = strlen("dd/MM/aaaa ");}
    //time
     // if ($tipo==) { $valor = strlen("hh:mm "); }
     //byte
     // if ($tipo==) { $valor = strlen("999 ");  }
     //integer
     if ($tipo=='4') { $valor = strlen("9999999999"); }
     //long
     // if ($tipo==) { $valor = strlen("999999 ");  }
     //single
     // if ($tipo==) { $valor = strlen("999.99% ");}
     //double
     if ($tipo=='7') { $valor = strlen("9,999.99 ");  }
     //money
     if ($tipo=='3') { $valor = strlen("9,999,999.99 "); }

     if ($tipo=='-9') { $valor = strlen("999")*3; }

  }else{
    //boolean
    if($tipo=='-7'){ $valor = strlen("Yes_")*$pixel;}
    //date
    if($tipo=='93')
    { 
      $valor = strlen("dd/mm/yyyy")*$pixel;
     if(strlen($namecol)>10){ $valor = strlen($namecol)*$pixel; /*print_r(strlen($namecol).'-'.$namecol);*/ }
    }
    //time
     // if ($tipo==) { $valor = strlen("HH:MM:SS "); }
     // integer
     if ($tipo=='4') { $valor = strlen("+9999999999999999")*$pixel; }
     //float
     if ($tipo=='6') { $valor = strlen("9,999.99")*$pixel; /*print_r('expression');*/}
     //tiyinit     
     if ($tipo=='-6') 
      {         
         $valor = strlen("9,999.99")*$pixel; 
        if(strlen($namecol)>8){ $valor = strlen($namecol)*$pixel; }
      }
     //long
     // if ($tipo==) { $valor = strlen("+99999999 ");  }
     //single
     // if ($tipo==) { $valor = strlen("+999.99% ");}
     //double o  real
     if ($tipo=='7') 
     {
        $valor = strlen("+99,999,999.99")*$pixel; 
       // if(strlen($namecol)>14){ $valor = strlen($namecol)*$pixel; } 
     }
     //bit
     if ($tipo=='-7') 
     {
        $valor = strlen("YES")*$pixel; 
        if(strlen($namecol)>3){ $valor = strlen($namecol)*$pixel; } 
     }
     //money
     if ($tipo=='3') 
     { 
      $valor = strlen("+9,999,999,999")*$pixel; 
      if(strlen($namecol)>14){ $valor = strlen($namecol)*$pixel; }
     }
     //nvarchar
     if ($tipo=='-9' )
     {
       if($size<18 && $size>=5 ){ $valor = strlen("99999999999999999")*$pixel; }
       if($size<5 && strlen($namecol)<5){ $valor = strlen("99999")*$pixel; }
       if($size<5 && strlen($namecol)>5){ $valor = strlen($namecol)*$pixel; }
       //if($size<5 && strlen($namecol)>11){ $valor = strlen($namecol)*$pixel; }
     }
     //smallint
     if ($tipo=='5')
     {
       $valor = strlen("99999 ")*$pixel;
       if(strlen($namecol)>5){ $valor = strlen($namecol)*$pixel; }   
     }
  }

     return $valor.'px';    
}

function datos_tabla($tabla,$campo=false)
{
    $conn = new db();
    $cid=$conn->conexion();
    $sql="SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH
    FROM Information_Schema.Columns
    WHERE TABLE_NAME = '".$tabla."' ";
    if($campo){
      $sql.=" AND COLUMN_NAME = '".$campo."'";
    }
    $datos = $conn->datos($sql);
     return $datos;
}


  function Leer_Cta_Catalogo($CodigoCta = ""){
    
    //conexion
    $conn = new db();
    $cid=$conn->conexion();

    //RatonReloj
    $NoEncontroCta = true;
    $cuenta = [];
    $cuenta['Codigo_Catalogo'] =  G_NINGUNO;
    $cuenta['Cuenta'] = G_NINGUNO;
    $cuenta['SubCta'] ="N";
    $cuenta['Moneda_US'] =  false;
    $cuenta['TipoCta'] = "G";
    $cuenta['TipoPago'] = "01";
    $auxCodigoCta = intval(substr($CodigoCta, 0,1));
    if ($auxCodigoCta >= 1) {
      $sql = "SELECT Codigo, Cuenta, TC, ME, DG, Tipo_Pago
            FROM Catalogo_Cuentas
            WHERE '".$CodigoCta."' IN (Codigo,Codigo_Ext) 
            AND Item = '".$_SESSION['INGRESO']['item']."'
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."'";
      $datos = sqlsrv_query( $cid, $sql);
      $cuenta['TipoPago'] = 0;
      while ($value = sqlsrv_fetch_array( $datos, SQLSRV_FETCH_ASSOC)) {
        $cuenta['Codigo_Catalogo'] = $value['Codigo'];
        $cuenta['Cuenta'] = $value['Cuenta'];
        $cuenta['SubCta'] = $value['TC'];
        $cuenta['Moneda_US'] = $value['ME'];
        $cuenta['TipoCta'] = $value['DG'];
        $cuenta['TipoPago'] = $value['Tipo_Pago'];
      }
      if (intval($cuenta['TipoPago']) <= 0) {
        $cuenta['TipoPago'] = "01";
        $NoEncontroCta = false;
      }
    }
    return $cuenta;
  }

  function Calculos_Totales_Factura($codigoCliente=false){
    //conexion
     $conn = new db();
    $cid=$conn->conexion();

    $TFA['SubTotal'] = 0;
    $TFA['Con_IVA'] = 0;
    $TFA['Sin_IVA'] = 0;
    $TFA['Descuento'] = 0;
    $TFA['Total_IVA'] = 0;
    $TFA['Total_MN'] = 0;
    $TFA['Total_ME'] = 0;
    $TFA['Descuento2'] = 0;
    $TFA['Descuento_0'] = 0;
    $TFA['Descuento_X'] = 0;
    $TFA['Servicio'] = 0;

    //Miramos de cuanto es la factura para los calculos de los totales
    $Total_Desc_ME = 0;
    $sql = "SELECT *
          FROM Asiento_F 
          WHERE Item = '".$_SESSION['INGRESO']['item']."'
          AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' ";

          // print_r($sql);die();
    $datos = sqlsrv_query( $cid, $sql);
    while ($value = sqlsrv_fetch_array( $datos, SQLSRV_FETCH_ASSOC)) {
      $TFA['Descuento'] += $value['Total_Desc'];
      $TFA['Descuento2'] += $value['Total_Desc2'];
      $TFA['Total_IVA'] += $value['Total_IVA'];
      $TFA['Servicio']+= floatval($value['SERVICIO']);
      if (number_format($value['Total_IVA'],2)!=0) {
        $TFA['Con_IVA'] += $value['TOTAL'];
        $TFA['Descuento_X'] = $TFA['Descuento_X'] + $TFA['Descuento'] + $TFA['Descuento2'];
      }else{
        $TFA['Sin_IVA'] += $value['TOTAL'];
        $TFA['Descuento_0'] = $TFA['Descuento_0'] + $TFA['Descuento'] + $TFA['Descuento2'];
      }
      //print_r($value);die();
    }

    $TFA['Total_IVA'] = round($TFA['Total_IVA'],2);
    $TFA['Con_IVA'] = round($TFA['Con_IVA'],2);
    $TFA['Sin_IVA'] = round($TFA['Sin_IVA'],2);
    $TFA['SubTotal'] = $TFA['Sin_IVA'] + $TFA['Con_IVA'] - $TFA['Descuento'] - $TFA['Descuento2'];
    $TFA['Total_MN'] = $TFA['Sin_IVA'] + $TFA['Con_IVA'] - $TFA['Descuento'] - $TFA['Descuento2'] + $TFA['Total_IVA'] + $TFA['Servicio'];
    // print_r($TFA);die();

    return $TFA;
  }

  function Existe_Factura($TFA){
    //conexion
   $conn = new db();

    $Respuesta = false;
    //Consultamos si exista la factura
    $sql = "SELECT TC, Serie, Factura 
            FROM Facturas 
            WHERE Factura = ".$TFA['Factura']."
            AND TC = '".$TFA['TC']."' 
            AND Serie = '".$TFA['Serie']."' 
            AND Item = '".$_SESSION['INGRESO']['item']."' 
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."'";
    //print_r($sql);exit();
   $numero = $conn->existe_registro($sql);
  return $numero;
  }

function factura_numero($ser)
{
  $conn = new db();
  $numero='';
  $sql="SELECT Item, Concepto, Numero, Periodo, ID
    FROM Codigos
    WHERE (Item = '".$_SESSION['INGRESO']['item']."') AND 
    (Periodo = '".$_SESSION['INGRESO']['periodo']."') AND 
    (Concepto = 'FA_SERIE_".$ser."')"; 
    $numero = $conn->datos($sql);
  return $numero;
}


// 'Facturas            : RUC_CI, TB, Razon_Social
// 'Clientes_Matriculas : Cedula_R, TD, Representante
function  Leer_Datos_Cliente_FA($Codigo_CIRUC_Cliente)
{
    $conn = new db();
    $TFA = array();    
    if(strlen($Codigo_CIRUC_Cliente) <= 0){$Codigo_CIRUC_Cliente = G_NINGUNO;}
    Leer_Datos_Cliente_SP($Codigo_CIRUC_Cliente);
    $TFA['CodigoC'] = $Codigo_CIRUC_Cliente;

   // 'Verificamos la informacion del Clienete
     if( $TFA['CodigoC'] <> ".")
     {  
         $sql = "SELECT Cliente,CI_RUC,TD,Email,EmailR,Direccion,DireccionT,Ciudad,Telefono,Telefono_R,Grupo,Representante,CI_RUC_R,TD_R 
          FROM Clientes 
          WHERE Codigo = '".$TFA['CodigoC']."' ";
          $datos = $conn->datos($sql);

         if(count($datos) > 0)
          {
           $TFA['Cliente'] = $datos[0]["Cliente"];
           $TFA['CI_RUC'] = $datos[0]["CI_RUC"];
           $TFA['TD'] = $datos[0]["TD"];
           $TFA['EmailC'] = $datos[0]["Email"];
           $TFA['EmailR'] = $datos[0]["EmailR"];
           $TFA['TelefonoC'] = $datos[0]["Telefono"];
           $TFA['DireccionC'] = $datos[0]["Direccion"];
           $TFA['Curso'] = $datos[0]["Direccion"];
           $TFA['CiudadC'] = $datos[0]["Ciudad"];
           $TFA['Grupo'] = $datos[0]["Grupo"];
           
           $TFA['Razon_Social'] = "CONSUMIDOR FINAL";
           $TFA['RUC_CI'] = "9999999999999";
           $TFA['TB'] = "R";
            if( strlen($datos[0]["Representante"]) > 1 And strlen($datos[0]["CI_RUC_R"]) > 1)
            {
              $TFA['TB'] = $datos[0]["TD_R"];
              switch ($TFA['TB']) {
                case 'C':
                case 'R':
                case 'P':
                     $TFA['Razon_Social'] = $datos[0]["Representante"];
                     $TFA['RUC_CI'] = $datos[0]["CI_RUC_R"];
                     $TFA['TelefonoC'] = $datos[0]["Telefono_R"];
                     $TFA['DireccionC'] = $datos[0]["DireccionT"];
                  break;
              }               

            }else{

              switch ($TFA['TD']) {
                case 'C':
                case 'R':
                case 'P':
                     $TFA['Razon_Social'] = $datos[0]["Cliente"];
                     $TFA['RUC_CI'] = $datos[0]["CI_RUC"];
                     $TFA['TB'] = $datos[0]["TD"];
                  break;
              }
            }               
            if(strlen($TFA['TelefonoC']) <= 1 ){$TFA['TelefonoC'] ='';}
            return $TFA;
          }
    }

}


function Grabar_Factura1($TFA,$VerFactura = false, $NoRegTrans = false)
{
  $LCxC = Lineas_De_CxC($TFA);
  if(isset($LCxC['TFA'])){
    $clavesFaltantes = array_diff_key($LCxC['TFA'], $TFA);
    $TFA = array_merge($TFA, $clavesFaltantes);
  }

   // print_r($TFA);die();
   $FA = variables_tipo_factura();
   $TFA = array_merge($FA,$TFA);
    //RatonReloj
   //'Averiguamos si la Factura esta a nombre del Representante

  // print_r($TFA);die();
  $conn = new db();
  $cliente = Leer_Datos_Cliente_FA($TFA['CodigoC']);
  $TFA = array_merge($TFA,$cliente);

  // print_r($TFA);die();
  
  $Orden_No = 0;
  $Total_Desc_ME = 0;
  if(strlen($TFA['Tipo_Pago']) <= 1){ $TFA['Tipo_Pago'] = "01";}
  $TFA['T'] = G_PENDIENTE;
  $TFA['SubTotal'] = 0;
  $TFA['Con_IVA'] = 0;
  $TFA['Sin_IVA'] = 0;
  $TFA['Total_IVA'] = 0;
  $TFA['Total_MN'] = 0;
  $TFA['Total_ME'] = 0;
  $TFA['Descuento'] = 0;
  $TFA['Descuento2'] = 0;
  $TFA['Descuento_0'] = 0;
  $TFA['Descuento_X'] = 0;
  $TFA['Servicio'] = 0;
  if(!isset($TFA['Cta_CxP_Anterior'])){
    $TFA['Cta_CxP_Anterior'] = '0';
  }
  
  if(strlen($TFA['Autorizacion']) >= 13){  $TMail['TipoDeEnvio'] = "CE";}
  // if($TFA['DireccionC'] <> $FA['DireccionS'] And strlen($TFA['DireccionS']) > 1 ){$TFA['DireccionC'] = $FA['DireccionS'];}
  if($TFA['TC'] =="PV")
  {
     $sql = "DELETE 
        FROM Trans_Ticket
        WHERE Ticket = ".$TFA['Factura']."
        AND TC = '".$TFA['TC']."'
        AND Item = '".$_SESSION['INGRESO']['item']."'
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
        $conn->String_Sql($sql);
  }else{
     $sql = "DELETE 
        FROM Detalle_Factura 
        WHERE Factura = ".$TFA['Factura']." 
        AND TC = '".$TFA['TC']."' 
        AND Serie = '".$TFA['Serie']."' 
        AND Autorizacion = '".$TFA['Autorizacion']."' 
        AND Item = '".$_SESSION['INGRESO']['item']."' 
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
        $conn->String_Sql($sql);

     $sql = "DELETE 
        FROM Facturas
        WHERE Factura = ".$TFA['Factura']."
        AND TC = '".$TFA['TC']."'
        AND Serie = '".$TFA['Serie']."'
        AND Autorizacion = '".$TFA['Autorizacion']."'
        AND Item = '".$_SESSION['INGRESO']['item']."'
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."' "; 
        $conn->String_Sql($sql);

     $sql = "DELETE 
        FROM Trans_Abonos 
        WHERE Factura = ".$TFA['Factura']." 
        AND TP = '".$TFA['TC']."' 
        AND Serie = '".$TFA['Serie']."' 
        AND Autorizacion = '".$TFA['Autorizacion']."' 
        AND Item = '".$_SESSION['INGRESO']['item']."' 
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
        $conn->String_Sql($sql);
    
     $sql = "DELETE 
        FROM Facturas_Auxiliares 
        WHERE Factura = ".$TFA['Factura']." 
        AND TC = '".$TFA['TC']."' 
        AND Serie = '".$TFA['Serie']."' 
        AND Autorizacion = '".$TFA['Autorizacion']."' 
        AND Item = '".$_SESSION['INGRESO']['item']."' 
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
        $conn->String_Sql($sql);
    
     $sql = "DELETE 
        FROM Trans_Kardex 
        WHERE Item = '".$_SESSION['INGRESO']['item']."' 
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        AND TC = '".$TFA['TC']."' 
        AND Serie = '".$TFA['Serie']."' 
        AND Factura = ".$TFA['Factura']." 
        AND SUBSTRING(Detalle, 1, 3) = 'FA:' ";
        $conn->String_Sql($sql);
  }
  
  $sql = "SELECT * 
    FROM Asiento_F 
    WHERE Item = '".$_SESSION['INGRESO']['item']."' 
    AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' 
    ORDER BY A_No ";
  $datos = $conn->datos($sql);

   if(count($datos) > 0 )
   {
       foreach ($datos as $key => $value) 
       {
          if($value["Total_IVA"] > 0 ){
             $TFA['Descuento_X'] = $TFA['Descuento_X'] + $value["Total_Desc"] + $value["Total_Desc2"];
             $TFA['Con_IVA'] = $TFA['Con_IVA'] + $value["TOTAL"];
          }else{
             $TFA['Descuento_0'] = $TFA['Descuento_0'] + $value["Total_Desc"] + $value["Total_Desc2"];
             $TFA['Sin_IVA'] = $TFA['Sin_IVA'] + $value["TOTAL"];
          }
          $TFA['Total_IVA'] = $TFA['Total_IVA'] + $value["Total_IVA"];
          $TFA['Descuento'] = $TFA['Descuento'] + $value["Total_Desc"];
          $TFA['Descuento2'] = $TFA['Descuento2'] + $value["Total_Desc2"];
          $TFA['Servicio'] = $TFA['Servicio'] + $value["SERVICIO"];
          
          if($value["HABIT"] <> G_NINGUNO)
          {
              $SQLHab = "DELETE
                    FROM Trans_Pedidos 
                    WHERE Item = '".$_SESSION['INGRESO']['item']."' 
                    AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
                    AND No_Hab = '".$value["HABIT"]."' ";
                    $conn->String_Sql($SQLHab);
          }  
          if($value["Numero"] <> 0){
              $SQLHab = "DELETE 
                    FROM Trans_Kardex
                    WHERE Item = '".$_SESSION['INGRESO']['item']."' 
                    AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
                    AND TC = 'OP'
                    AND Factura = ".$value["Numero"]." ";
                    $conn->String_Sql($SQLHab);
              $Orden_No = $value["Numero"];
          }    
        }
       
       // print_r($TFA);die();   
      // 'If Total_Desc_ME > 0 Then Total_Desc = Total_Desc_ME
       $TFA['Total_IVA'] = number_format($TFA['Total_IVA'], 2,'.','');
       $TFA['Con_IVA'] = number_format($TFA['Con_IVA'], 2,'.','');
       $TFA['Sin_IVA'] = number_format($TFA['Sin_IVA'], 2,'.','');
       $TFA['Servicio'] = number_format($TFA['Servicio'], 2,'.','');
       $TFA['SubTotal'] = $TFA['Sin_IVA'] + $TFA['Con_IVA'] - $TFA['Descuento'] - $TFA['Descuento2'];
       $TFA['Total_MN'] = $TFA['Sin_IVA'] + $TFA['Con_IVA'] - $TFA['Descuento'] - $TFA['Descuento2'] + $TFA['Total_IVA'] + $TFA['Servicio'];
       $TFA['Saldo_MN'] = $TFA['Total_MN'];
      // 'Averiguamos si tenemos facturas de años anteriores
       if($TFA['Cta_CxP'] <> $TFA['Cta_CxP_Anterior']){
          foreach ($datos as $key => $value) {
            if(is_numeric($value['TICKET']))
            {
              $year = date("Y", strtotime($TFA['Fecha']));
              if(intval($value['TICKET'])<>$year)
              {
                $TFA['Cta_CxP'] = $TFA['Cta_CxP_Anterior'];
              }
            }
            
          }
       }
       $Cta_Cobrar = $TFA['Cta_CxP'];
       $TFA['Hora'] =  date('h:i:s a', time());  
       
      // 'Totales de la Factura/Nota de Venta
       if($TFA['TC'] == "PV")
       {
             $Ln_No = 1;
          foreach ($datos as $key => $value) {

          SetAdoAddNew("Trans_Ticket");          
          SetAdoFields("TC",  $TFA['TC']);
          SetAdoFields("Ticket", $TFA['Factura']);
          SetAdoFields("CodigoC",  $TFA['CodigoC']);
          SetAdoFields("Fecha",  $TFA['Fecha']);
          SetAdoFields("Efectivo",  $TFA['Efectivo']);
          SetAdoFields("Codigo_Inv",  $TFA['CODIGO']);
          SetAdoFields("Cantidad",  $TFA['CANT']);
          SetAdoFields("Precio",  number_format($TFA['PRECIO'],$_SESSION['INGRESO']['Dec_PVP'],'.',''));
          SetAdoFields("Total",  $TFA['TOTAL']);
          SetAdoFields("Descuento",  $value[ "Total_Desc"] + $value["Total_Desc2"]);
          SetAdoFields("Producto",  substr($value["PRODUCTO"], 1, 40));
          SetAdoFields("CodigoU", $_SESSION['INGRESO']['CodigoU']);
          SetAdoFields("Periodo",  $_SESSION['INGRESO']['periodo']);
          SetAdoFields("Hora",  $TFA['Hora']);
          SetAdoFields("Item",  $_SESSION['INGRESO']['item']);

          SetAdoUpdate(); 
            
          }
            
       }else{
         // 'Grabamos el numero de factura
          
          SetAdoAddNew("Facturas");          
          SetAdoFields("T",$TFA['T']);
          SetAdoFields("TC",$TFA['TC']);
          SetAdoFields("Serie",$TFA['Serie']);
          SetAdoFields("Factura",$TFA['Factura']);
          SetAdoFields("Autorizacion",$TFA['Autorizacion']);
          
          SetAdoFields("ME",$TFA['ME_']);
          SetAdoFields("SP",$TFA['SP']);
          SetAdoFields("Porc_IVA",$TFA['Porc_IVA']);
          SetAdoFields("CodigoC",$TFA['CodigoC']);
          SetAdoFields("CodigoB",$TFA['CodigoB']);
          SetAdoFields("CodigoA",$TFA['CodigoA']);
          SetAdoFields("CodigoDr",$TFA['CodigoDr']);
          SetAdoFields("Cod_Ejec",$TFA['Cod_Ejec']);
          SetAdoFields("Fecha",$TFA['Fecha']);
          SetAdoFields("Fecha_C",$TFA['Fecha_C']);
          SetAdoFields("Fecha_V",$TFA['Fecha_V']);
          SetAdoFields("Cod_CxC",$TFA['Cod_CxC']);
          SetAdoFields("Forma_Pago",$TFA['Forma_Pago']);
          SetAdoFields("Servicio",$TFA['Servicio']);
          SetAdoFields("Sin_IVA",$TFA['Sin_IVA']);
          SetAdoFields("Con_IVA",$TFA['Con_IVA']);
          SetAdoFields("SubTotal",$TFA['Sin_IVA'] + $TFA['Con_IVA']);
          SetAdoFields("Descuento",$TFA['Descuento']);
          SetAdoFields("Descuento2",$TFA['Descuento2']);
          SetAdoFields("Desc_0",$TFA['Descuento_0']);   // Descuentos por el detalle de la factura
          SetAdoFields("Desc_X",$TFA['Descuento_X']);
          SetAdoFields("IVA",$TFA['Total_IVA']);
          SetAdoFields("Total_MN",$TFA['Total_MN']);
          SetAdoFields("Total_ME",$TFA['Total_ME']);
          SetAdoFields("Saldo_MN",$TFA['Saldo_MN']);
          SetAdoFields("Saldo_ME",$TFA['Saldo_ME']);
          SetAdoFields("Porc_C",$TFA['Porc_C']);
          SetAdoFields("Comision",$TFA['Comision']);
          SetAdoFields("SubCta",$TFA['SubCta']);
          SetAdoFields("Tipo_Pago",$TFA['Tipo_Pago']);
          SetAdoFields("Propina",$TFA['Propina']);
          SetAdoFields("Efectivo",$TFA['Efectivo']);
          SetAdoFields("Cotizacion",$TFA['Cotizacion']);
          SetAdoFields("Observacion",$TFA['Observacion']);
          SetAdoFields("Nota",$TFA['Nota']);
          SetAdoFields("Clave_Acceso",$TFA['ClaveAcceso']);
          SetAdoFields("Cta_CxP",$TFA['Cta_CxP']);
          SetAdoFields("Cta_Venta",$TFA['Cta_Venta']);
          SetAdoFields("Hora",$TFA['Hora']);
          SetAdoFields("Vencimiento",$TFA['Vencimiento']);
          SetAdoFields("Imp_Mes",$TFA['Imp_Mes']);
          SetAdoFields("Orden_Compra",$TFA['Orden_Compra']);
          SetAdoFields("Gavetas",$TFA['Gavetas']);
          SetAdoFields("CodigoU",$_SESSION['INGRESO']['CodigoU']);
          SetAdoFields("Periodo",$_SESSION['INGRESO']['periodo']);
          SetAdoFields("Item",$_SESSION['INGRESO']['item']);
          
         // 'MsgBox TFA.Razon_Social
          SetAdoFields("Razon_Social",$TFA['Razon_Social']);
          SetAdoFields("RUC_CI",$TFA['RUC_CI']);
          SetAdoFields("TB",$TFA['TB']);
          SetAdoFields("Telefono_RS",$TFA['TelefonoC']);
          SetAdoFields("Direccion_RS",$TFA['DireccionC']);

          SetAdoUpdate(); 

         // 'MsgBox TFA.Fecha & "-" & TFA.TC & "-" & TFA.Serie & "-" & TFA.Factura
         // 'Datos de la Guia de Remision
          if($TFA['Remision'] > 0 ){
             // SetAdoAddNew "Facturas_Auxiliares"
             SetAdoAddNew("Facturas_Auxiliares");  

             SetAdoFields("TC",$TFA['TC']);
             SetAdoFields("Serie",$TFA['Serie']);
             SetAdoFields("Factura",$TFA['Factura']);
             SetAdoFields("Autorizacion",$TFA['Autorizacion']);
             SetAdoFields("Fecha",$TFA['Fecha']);
             SetAdoFields("CodigoC",$TFA['CodigoC']);
             SetAdoFields("Remision",$TFA['Remision']);
             SetAdoFields("Comercial",$TFA['Comercial']);
             SetAdoFields("CIRUC_Comercial",$TFA['CIRUCComercial']);
             SetAdoFields("Entrega",$TFA['Entrega']);
             SetAdoFields("CIRUC_Entrega",$TFA['CIRUCEntrega']);
             SetAdoFields("CiudadGRI",$TFA['CiudadGRI']);
             SetAdoFields("CiudadGRF",$TFA['CiudadGRF']);
             SetAdoFields("Placa_Vehiculo",$TFA['Placa_Vehiculo']);
             SetAdoFields("FechaGRE",$TFA['FechaGRE']);
             SetAdoFields("FechaGRI",$TFA['FechaGRI']);
             SetAdoFields("FechaGRF",$TFA['FechaGRF']);
             SetAdoFields("Pedido",$TFA['Pedido']);
             SetAdoFields("Zona",$TFA['Zona']);
             SetAdoFields("Orden_Compra",$TFA['Orden_Compra']);
             SetAdoFields("Serie_GR",$TFA['Serie_GR']);
             SetAdoFields("Autorizacion_GR",$TFA['Autorizacion_GR']);
             SetAdoFields("Lugar_Entrega",$TFA['Lugar_Entrega']);
             SetAdoFields("CodigoU", $_SESSION['INGRESO']['CodigoU']);
             SetAdoFields("Periodo", $_SESSION['INGRESO']['periodo']);
             SetAdoFields("Item",$_SESSION['INGRESO']['item']);

             SetAdoUpdate(); 
          }
         // 'Detalle de la Factura/Nota de Venta
          $Habitacion_No = G_NINGUNO;
          foreach ($datos as $key => $value) 
          {           
            $No_Mes = 0;
            $Mes = "";
            if($value["Mes"] == G_NINGUNO){ 
              $No_Mes = date("m", strtotime($TFA['Fecha'])); 
              $Mes = MesesLetras($No_Mes); 
            }else{ 
              $Mes = $value["Mes"]; 
              $No_Mes = nombre_X_mes($value["Mes"]); 
            }

             // SetAdoAddNew "Detalle_Factura"
             SetAdoAddNew("Detalle_Factura");  
             SetAdoFields("T",$TFA['T']);
             SetAdoFields("TC",$TFA['TC']);
             SetAdoFields("SP",$TFA['SP']);
             SetAdoFields("Porc_IVA",$TFA['Porc_IVA']);
             SetAdoFields("Factura",$TFA['Factura']);
             SetAdoFields("CodigoC",$TFA['CodigoC']);
             SetAdoFields("CodigoB",$TFA['CodigoB']);
             SetAdoFields("CodigoA",$TFA['CodigoA']);
             SetAdoFields("Fecha",$TFA['Fecha']);
             SetAdoFields("CodigoL",$TFA['Cod_CxC']);
             SetAdoFields("Serie",$TFA['Serie']);
             SetAdoFields("Autorizacion",$TFA['Autorizacion']);
             SetAdoFields("No_Hab",$value["HABIT"]);
             SetAdoFields("Codigo",$value["CODIGO"]);
             SetAdoFields("Cantidad",$value["CANT"]);
             SetAdoFields("Reposicion",$value["REP"]);
             SetAdoFields("Precio",$value["PRECIO"]);
             SetAdoFields("Precio2",$value["PRECIO2"]);
             SetAdoFields("Total",$value["TOTAL"]);
             SetAdoFields("Total_Desc",$value["Total_Desc"]);
             SetAdoFields("Total_Desc2",$value["Total_Desc2"]);
             SetAdoFields("Total_IVA",$value["Total_IVA"]);
             SetAdoFields("Producto",$value["PRODUCTO"]);
             SetAdoFields("Cod_Ejec",$value["Cod_Ejec"]);
             SetAdoFields("Porc_C",$value["Porc_C"]);
             SetAdoFields("Ruta",$value["RUTA"]);
             SetAdoFields("Corte",$value["CORTE"]);
             SetAdoFields("Mes",$Mes);
             SetAdoFields("Mes_No",$No_Mes);
             SetAdoFields("Ticket",$value["TICKET"]);
             SetAdoFields("CodBodega",$value["CodBod"]);
             SetAdoFields("CodMarca",$value["CodMar"]);
             SetAdoFields("Codigo_Barra",$value["COD_BAR"]);
             SetAdoFields("Orden_No",$value["Numero"]);
             SetAdoFields("CodigoU",$_SESSION['INGRESO']['CodigoU']);
             SetAdoFields("Periodo",$_SESSION['INGRESO']['periodo']);
             SetAdoFields("Item",$_SESSION['INGRESO']['item']);
             SetAdoFields("Fecha_IN",$value["Fecha_IN"]->format('Y-m-d'));
             SetAdoFields("Fecha_OUT",$value["Fecha_OUT"]->format('Y-m-d'));
             SetAdoFields("Cant_Hab",$value["Cant_Hab"]);
             SetAdoFields("Tipo_Hab",$value["Tipo_Hab"]);
             SetAdoFields("Fecha_V",$value["Fecha_V"]->format('Y-m-d'));
             SetAdoFields("Lote_No",$value["Lote_No"]);
             SetAdoFields("Fecha_Fab",$value["Fecha_Fab"]->format('Y-m-d'));
             SetAdoFields("Fecha_Exp",$value["Fecha_Exp"]->format('Y-m-d'));
             //'$datosD[0]['campo'] =  "Reg_Sanitario"; $datosDET[0]['dato'] = $value["Reg_Sanitario")
             SetAdoFields("Procedencia",$value["Procedencia"]);
             SetAdoFields("Modelo",$value["Modelo"]);
             SetAdoFields("Serie_No",$value["Serie_No"]);
             SetAdoFields("Costo",$value["COSTO"]);

             SetAdoUpdate(); 
                             
            // 'Grabamos el submodulo de ingreso
             if($value["TOTAL"] > 0 And $value["Cta_SubMod"] <> G_NINGUNO)
             {
                 // SetAdoAddNew "Trans_SubCtas"
               SetAdoAddNew("Trans_SubCtas");  

               SetAdoFields("T",G_NORMAL);
               SetAdoFields("TP",G_NINGUNO);
               SetAdoFields("Numero",0);
               SetAdoFields("Fecha",$TFA['Fecha']);
               SetAdoFields("Item",$_SESSION['INGRESO']['item']);
               SetAdoFields("CodigoU",$_SESSION['INGRESO']['CodigoU']);
               SetAdoFields("TC","I");
               SetAdoFields("Cta",$value["Cta"]);
               SetAdoFields("Codigo",$value["Cta_SubMod"]);
               SetAdoFields("Fecha_V",$TFA['Fecha_V']);
               SetAdoFields("Factura",$TFA['Factura']);
               SetAdoFields("Creditos",$value["TOTAL"]);

               SetAdoUpdate(); 
                
             }

            // 'Grabamos en el Kardex la factura
             if($value["COSTO"] > 0)
             {
                 // SetAdoAddNew "Trans_Kardex"

               SetAdoAddNew("Trans_Kardex");  
                 SetAdoFields("T",G_NORMAL);
                 SetAdoFields("TC",$TFA['TC']);
                 SetAdoFields("Serie",$TFA['Serie']);
                 SetAdoFields("Fecha",$TFA['Fecha']);
                 SetAdoFields("Factura",$TFA['Factura']);
                 SetAdoFields("Codigo_P",$TFA['CodigoC']);
                 SetAdoFields("CodBodega",$value["CodBod"]);
                 SetAdoFields("CodMarca",$value["CodMar"]);
                 SetAdoFields("Codigo_Inv",$value["CODIGO"]);
                 SetAdoFields("CodigoL",$TFA['Cod_CxC']);
                 SetAdoFields("Lote_No",$value["Lote_No"]);
                 SetAdoFields("Fecha_Fab",$value["Fecha_Fab"]->format('Y-m-d'));
                 SetAdoFields("Fecha_Exp",$value["Fecha_Exp"]->format('Y-m-d'));
                 SetAdoFields("Procedencia",$value["Procedencia"]);
                 SetAdoFields("Modelo",$value["Modelo"]);
                 SetAdoFields("Serie_No",$value["Serie_No"]);
                 SetAdoFields("Total_IVA",$value["Total_IVA"]);
                 SetAdoFields("Porc_C",$value["Porc_C"]);
                 SetAdoFields("Salida",$value["CANT"]);
                 SetAdoFields("PVP",$value["PRECIO"]);
                 SetAdoFields("Valor_Unitario",$value["PRECIO"]);
                 SetAdoFields("Costo",$value["COSTO"]);
                 SetAdoFields("Valor_Total", number_format($value["CANT"] * $value["PRECIO"],2,'.',''));
                 SetAdoFields("Total", number_format($value["CANT"] * $value["COSTO"],2,'.',''));
                 SetAdoFields("Detalle", "FA: ".substr($TFA['Cliente'],0,96));
                 SetAdoFields("Codigo_Barra",$value["COD_BAR"]);
                 SetAdoFields("Orden_No",$value["Numero"]);
                 SetAdoFields("Cta_Inv",$value["Cta_Inv"]);
                 SetAdoFields("Contra_Cta",$value["Cta_Costo"]);
                 SetAdoFields("Item", $_SESSION['INGRESO']['item']);
                 SetAdoFields("Periodo", $_SESSION['INGRESO']['periodo']);
                 SetAdoFields("CodigoU", $_SESSION['INGRESO']['CodigoU']);   

                 SetAdoUpdate();               
              }            

              // 'Salida si es por recetas y ademas el producto es por servicios
             // 'caso contrario hay que acondicionar desde el Modulo de Inventario
             if(strlen($value["Cta_Inv"]) == 1 && strlen($value["Cta_Costo"]) == 1)
             {
                $sql = "SELECT Codigo_Receta, Cantidad, Costo, ID 
                     FROM Catalogo_Recetas 
                     WHERE Item = '".$_SESSION['INGRESO']['item']."' 
                     AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
                     AND Codigo_PP = '".$value["CODIGO"]."' 
                     AND TC = 'P' 
                     ORDER BY Codigo_Receta ";
                $AdoDBReceta = $conn->datos($sql);

                if(count($AdoDBReceta)> 0)
                {
                  $FechaSistema = date('Y-m-d');
                   foreach ($AdoDBReceta as $key => $valueAdo) {

                     $DatInv = Leer_Codigo_Inv($AdoDBReceta[0]["Codigo_Receta"],$FechaSistema, $value("CodBod"), $value["CodMar"]);                    
                      if(count($codigo_inve)>0)
                      {
                         if($DatInv['Costo'] > 0 )
                         {
                            
                            $CantidadAnt = $value["CANT"] * $valueAdo["Cantidad"];
                            $ValorTotal = number_format($CantidadAnt * $DatInv['Costo'], 2,'.','');
                            SetAdoAddNew("Trans_Kardex");
                            SetAdoFields("T", G_NORMAL);
                            SetAdoFields("TC", $TFA['TC']);
                            SetAdoFields("Serie", $TFA['Serie']);
                            SetAdoFields("Fecha", $TFA['Fecha']);
                            SetAdoFields("Factura", $TFA['Factura']);
                            SetAdoFields("Codigo_P", $TFA['CodigoC']);
                            SetAdoFields("CodBodega", $value["CodBod"]);
                            SetAdoFields("CodMarca", $value["CodMar"]);
                            SetAdoFields("Codigo_Inv", $valueAdo["Codigo_Receta"]);
                            SetAdoFields("CodigoL", $TFA['Cod_CxC']);
                            SetAdoFields("Lote_No", $value["Lote_No"]);
                            SetAdoFields("Fecha_Fab", $value["Fecha_Fab"]);
                            SetAdoFields("Fecha_Exp", $value["Fecha_Exp"]);
                            SetAdoFields("Procedencia", $value["Procedencia"]);
                            SetAdoFields("Modelo", $value["Modelo"]);
                            SetAdoFields("Serie_No", $value["Serie_No"]);
                            SetAdoFields("Porc_C", $value["Porc_C"]);
                            SetAdoFields("PVP", $DatInv['Costo']);
                            SetAdoFields("Valor_Unitario", $DatInv['Costo']);
                            SetAdoFields("Salida", $CantidadAnt);
                            SetAdoFields("Valor_Total", $ValorTotal);
                            SetAdoFields("Costo", $DatInv['Costo']);
                            SetAdoFields("Total", $ValorTotal);
                            SetAdoFields("Detalle", substr("FA: RE-".$TFA['Cliente'], 1, 100));
                            SetAdoFields("Codigo_Barra", $value["COD_BAR"]);
                            SetAdoFields("Orden_No", $value["Numero"]);
                            SetAdoFields("Cta_Inv", $DatInv['Cta_Inventario']);
                            SetAdoFields("Contra_Cta", $DatInv['Cta_Costo_Venta']);
                            SetAdoFields("Item", $_SESSION['INGRESO']['item']);
                            SetAdoFields("Periodo",$_SESSION['INGRESO']['periodo']);
                            SetAdoFields("CodigoU",$_SESSION['INGRESO']['CodigoU']);
                            SetAdoUpdate();
                         }
                    }
                  }
                }
              }

          }
        }

       $sql = "UPDATE Trans_Fletes 
        SET T = 'P' 
        WHERE Item = '".$_SESSION['INGRESO']['item']."' 
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        AND Ok <> 0  
        AND CodigoC = '".$TFA['CodigoC']."' ";
        $conn->String_Sql($sql);
        // '''   sSQL = "UPDATE Trans_Comision " _
        // '''   & "SET Factura = " & TFA.Factura & " " _
        // '''   & "WHERE Factura = 0 " _
        // '''   & "AND Item = '" & NumEmpresa & "' " _
        // '''   & "AND Periodo = '" & Periodo_Contable & "' " _
        // '''   & "AND CodigoU = '" & CodigoUsuario & "' "
        // '''   Ejecutar_SQL_SP sSQL

       $sql = "DELETE
          FROM Clientes_Facturacion
          WHERE Item = '".$_SESSION['INGRESO']['item']."'
          AND Valor <= 0
          AND Num_Mes >= 0 ";
          $conn->String_Sql($sql);
      
       if($Orden_No > 0)
       {
          $Orden_No = $datos[0]["Numero"];
          foreach ($datos as $key => $value) 
          {            
             if($Orden_No <> $value["Numero"])
             {
                $sql = "UPDATE Facturas 
                  SET T = 'A' 
                  WHERE Item = '".$_SESSION['INGRESO']['item']."' 
                  AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
                  AND Factura = ".$Orden_No." 
                  AND TC = 'OP' ";
                  $conn->String_Sql($sql);
                // Ejecutar_SQL_SP sSQL
                
                $sql = "UPDATE Detalle_Factura 
                  SET T = 'A' 
                  WHERE Item = '".$_SESSION['INGRESO']['item']."' 
                  AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
                  AND Factura = ".$Orden_No." 
                  AND TC = 'OP' ";
                  $conn->String_Sql($sql);
                
                $sql = "UPDATE Trans_Pedidos 
                  SET Factura = ".$TFA['Factura'].",Serie = '".$TFA['Serie']."',Autorizacion = '".$TFA['Autorizacion']."' 
                  WHERE Item = '".$_SESSION['INGRESO']['item']."' 
                  AND Orden_No = ".$Orden_No." 
                  AND TC = 'OP' ";
                  $conn->String_Sql($sql);
                $Orden_No = $value["Numero"];
             }            
          }
           $sql = "UPDATE Facturas 
               SET T = 'A' 
               WHERE Item = '".$_SESSION['INGRESO']['item']."' 
               AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
               AND Factura = ".$Orden_No." 
               AND TC = 'OP' ";
                $conn->String_Sql($sql);
               // Ejecutar_SQL_SP sSQL
        
           $sql = "UPDATE Detalle_Factura 
               SET T = 'A' 
               WHERE Item = '".$_SESSION['INGRESO']['item']."' 
               AND Periodo = '".$_SESSION['INGRESO']['item']."' 
               AND Factura = ".$Orden_No." 
               AND TC = 'OP' ";
                $conn->String_Sql($sql);
               // Ejecutar_SQL_SP sSQL
            
           $sql = "UPDATE Trans_Pedidos 
               SET Factura = ".$TFA['Factura'].",Serie = '".$TFA['Serie']."',Autorizacion = '".$TFA['Autorizacion']."' 
               WHERE Item = '".$_SESSION['INGRESO']['item']."' 
               AND Orden_No = ".$Orden_No." 
               AND TC = 'OP' ";
                $conn->String_Sql($sql);
               // Ejecutar_SQL_SP sSQL
        }
       
       $sql = "DELETE 
          FROM Asiento_F 
          WHERE Item = '".$_SESSION['INGRESO']['item']."' 
          AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' ";
           $conn->String_Sql($sql);
          // Ejecutar_SQL_SP sSQL
                   
         if($NoRegTrans)
         {
            Actualiza_Procesado_Kardex_Factura($TFA);
            // Control_Procesos "G", "Grabar " & TFA.TC & " No. " & TFA.Serie & "-" & Format$(TFA.Factura, "000000000") & " [" & TFA.Hora & "]"
         }

         return 1;
    }else{
       return "No se puede grabar el documento, falta datos.";
   }
  }

function Actualiza_Procesado_Kardex_Factura($TFA)
{

    $conn = new db();
    $SQLKardex = "UPDATE Trans_Kardex 
        SET Procesado = 0 
        FROM Trans_Kardex As TK, Detalle_Factura As DF 
        WHERE DF.Item = '".$_SESSION['INGRESO']['item']."' 
        AND DF.Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        AND DF.TC = '".$TFA['TC']."' 
        AND DF.Serie = '".$TFA['Serie']."' 
        AND DF.Factura = ".$TFA['Factura']." 
        AND TK.Item = DF.Item 
        AND TK.Periodo = DF.Periodo 
        AND TK.Codigo_Inv = DF.Codigo ";
        $conn->String_Sql($SQLKardex);
    
    $SQLKardex = "UPDATE Trans_Kardex 
        SET Procesado = 0 
        FROM Trans_Kardex As TK, Asiento_NC As ANC 
        WHERE TK.Item = '".$_SESSION['INGRESO']['item']."' 
        AND TK.Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        AND ANC.CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' 
        AND TK.Item = ANC.Item 
        AND TK.Codigo_Inv = ANC.CODIGO ";
        $conn->String_Sql($SQLKardex);
    
    $SQLKardex = "UPDATE Trans_Kardex 
        SET Procesado = 0 
        FROM Trans_Kardex As TK, Asiento_F As AF 
        WHERE TK.Item = '".$_SESSION['INGRESO']['item']."' 
        AND TK.Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        AND AF.CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' 
        AND TK.Item = AF.Item 
        AND TK.Codigo_Inv = AF.CODIGO ";        
        $conn->String_Sql($SQLKardex);
}


  function Grabar_Factura($datos1)
  {
    //conexion
    $conn = new db();
    $cid=$conn->conexion();
    // $conn = new Conectar();
    // $cid=$conn->conexion();
    $nombrec= $datos1['Cliente'];
    $ruc= $datos1['TextCI'];
    $email= $datos1['TxtEmail'];
    $ser= $datos1['Serie'];
    $ser1=explode("_", $ser);
    $n_fac= $datos1['FacturaNo'];
    $me = '.';
    if(isset($datos1['me']))
    {
      $me= $datos1['me'];
    }
    $total_total_= $datos1['Total'];
    $total_abono= $datos1['Total_Abonos']; 
    $fecha_actual = date("Y-m-d"); 
    $hora = date("H:i:s");
    $fechaEntera = strtotime($fecha_actual);
    $anio = date("Y", $fechaEntera);
    $mes = date("m", $fechaEntera);
    $total_iva=0;
    $imp=0;
    if(isset($datos1['imprimir']))
    {
      $imp=$datos1['imprimir'];
    }
    if($imp==0)
    {
      //$mes=$mes+1;
      //consultamos clientes
      $sql="SELECT * FROM Clientes WHERE CI_RUC= '".$ruc."' ";
      $row = $conn->datos($sql);
      $codigo = $row[0]['Codigo'];

      //consultamos catalogo linea
      $sql="SELECT   Codigo, CxC
      FROM   Catalogo_Lineas
      WHERE   Periodo = '".$_SESSION['INGRESO']['periodo']."' 
      AND Item = '".$_SESSION['INGRESO']['item']."' 
      AND Serie = '".$ser."' 
      AND Fact = '".$datos1['TC']."'";

      // print_r($sql);die();

       //aparecen vario registros verificar eso
       $row = $conn->datos($sql);
       $cxc=$row[0]['CxC'];
       $cod_linea=$row[0]['Codigo'];

      //verificamos que no exista la factura
      $sql="SELECT TOP (1) Factura
            FROM Detalle_Factura
            WHERE Factura = '".$n_fac."' 
            AND Serie = '".$ser."' 
            AND Item = '".$_SESSION['INGRESO']['item']."' 
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
            AND TC = '".$datos1['TC']."'";
         $ii = $conn->existe_registro($sql);
     // print_r($ii);die();
      if($ii==0)
      {
        $total_coniva=0;
        $total_siniva=0;
        //agregamos detalle factura
        $sql="SELECT * ". 
            "FROM Asiento_F
             WHERE  (Item = '".$_SESSION['INGRESO']['item']."')
             AND CodigoU = '". $_SESSION['INGRESO']['CodigoU'] ."' 
             AND Codigo_Cliente = '". $datos1['codigoCliente'] ."' 
             ORDER BY CODIGO";
             // print_r($sql);die();
         $datos = $conn->datos($sql);
         foreach ($datos as $key => $value) {
              SetAdoAddNew("Detalle_Factura");
              SetAdoFields('T', 'C');
              SetAdoFields('TC', $datos1['TC']);
              SetAdoFields('CodigoC', $codigo);
              SetAdoFields('Factura', $n_fac);
              SetAdoFields('Fecha', $fecha_actual);
              SetAdoFields('Codigo', $value['CODIGO']);
              SetAdoFields('CodigoL', $cod_linea);
              SetAdoFields('Producto', $value['PRODUCTO']);
              SetAdoFields('Cantidad', number_format($value['CANT'], 2, '.', ''));
              SetAdoFields('Precio', number_format($value['PRECIO'], 6, '.', ''));
              SetAdoFields('Total', number_format($value['TOTAL'], 2, '.', ''));
              SetAdoFields('Total_IVA', number_format($value['Total_IVA'], 2, '.', ''));
              SetAdoFields('Item', $_SESSION['INGRESO']['item']);
              SetAdoFields('CodigoU', $_SESSION['INGRESO']['CodigoU']);
              SetAdoFields('Periodo', $_SESSION['INGRESO']['periodo']);
              SetAdoFields('Serie', $ser);
              SetAdoFields('Mes_No', $mes);
              SetAdoFields('Porc_IVA', $_SESSION['INGRESO']['porc']);
              SetAdoFields('Autorizacion', $datos1['Autorizacion']);
              SetAdoFields('Precio2', $value['PRECIO2']);
              SetAdoUpdate();
              $total_iva=$total_iva+$value['Total_IVA'];
              if($value['Total_IVA']==0)
              {
                $total_siniva=$value['TOTAL']+$value['Total_IVA']+$total_siniva;
              }
              else
              {
                $total_coniva=$value['TOTAL']+$total_coniva;
              }
         }
         if($total_siniva!=0)
         {       
           $total_total_ = $total_siniva+$total_coniva;
         }

        //agregamos abono
        $sql="SELECT * 
        FROM Asiento_Abonos
         WHERE  HABIT= '".$me."' 
         AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
         AND Item = '".$_SESSION['INGRESO']['item']."'";
         $datos = $conn->datos($sql);
        
        $cod_cue='.';
        $TC='.';
        $cuenta='.';
        $tipo_pago='.';
        foreach ($datos as $key => $value) {           
            //datos de la cuenta
            $sql="SELECT TC,Codigo,Cuenta,Tipo_Pago FROM Catalogo_Cuentas 
              WHERE TC IN ('BA','CJ','CP','C','P','TJ','CF','CI','CB') 
              AND DG = 'D' AND Item = '".$_SESSION['INGRESO']['item']."' 
              AND Periodo = '".$_SESSION['INGRESO']['periodo']."' AND Codigo='".$value['Cta']."' ";
              //echo $sql.'<br>';
              $row1 = $conn->datos($sql);
              foreach ($row1 as $key1 => $value1) {
                 $cod_cue=$value1['Codigo'];
                 $TC=$value1['TC'];
                $cuenta=$value1['Cuenta'];
                if($value1['Tipo_Pago']!='.')
                {
                  if($tipo_pago=='.')
                  {
                    $tipo_pago=$value1['Tipo_Pago'];
                  }
                  else
                  {
                    if($tipo_pago<$value1['Tipo_Pago'])
                    {
                      $tipo_pago=$value1['Tipo_Pago'];
                    }
                  }
                }                
              }
              SetAdoAddNew("Trans_Abonos");
              SetAdoFields('T', 'C');
              SetAdoFields('TP', $datos1['TC']);
              SetAdoFields('CodigoC', $codigo);
              SetAdoFields('Factura', $n_fac);
              SetAdoFields('Fecha', $fecha_actual);
              SetAdoFields('Cta', $cod_cue);
              SetAdoFields('Cta_CxP', $cod_linea);
              SetAdoFields('Recibo_No', '0000000000');
              SetAdoFields('Comprobante', '.');
              SetAdoFields('Abono', $total_total_);
              SetAdoFields('Total', $total_total_);
              SetAdoFields('Cheque', $value['Comprobante']);
              SetAdoFields('Fecha_Aut_NC', $fecha_actual);
              SetAdoFields('Item', $_SESSION['INGRESO']['item']);
              SetAdoFields('CodigoU', $_SESSION['INGRESO']['CodigoU']);
              SetAdoFields('Periodo', $_SESSION['INGRESO']['periodo']);
              SetAdoFields('Serie', $ser);
              SetAdoFields('Fecha_Aut', $fecha_actual);
              SetAdoFields('C', 0);
              SetAdoFields('Tipo_Cta', $TC);
              SetAdoFields('Banco', $cuenta);
              SetAdoFields('Autorizacion', $datos1['Autorizacion']);
              SetAdoUpdate();
        }

        $dato = [];
        if($tipo_pago=='.')
        {
          $tipo_pago ='01';
        }
        //exit();
        $propina_a = 0;

        SetAdoAddNew("Facturas");
        SetAdoFields('C', 1);
        SetAdoFields('T', $datos1['T']);
        SetAdoFields('TC', $datos1['TC']);
        SetAdoFields('ME', 0);
        SetAdoFields('Factura', $n_fac);
        SetAdoFields('CodigoC', $codigo);
        SetAdoFields('Fecha', $fecha_actual);
        SetAdoFields('Fecha_C', $fecha_actual);
        SetAdoFields('Fecha_V', $fecha_actual);
        SetAdoFields('SubTotal', number_format(($total_total_-$total_iva),2,'.',''));
        SetAdoFields('Con_IVA', number_format(($total_coniva-$total_iva),2,'.',''));
        SetAdoFields('Sin_IVA', number_format($total_siniva,2,'.',''));
        SetAdoFields('IVA', number_format($total_iva,2,'.',''));
        SetAdoFields('Total_MN', number_format($total_total_,2,'.',''));
        SetAdoFields('Cta_CxP', $cxc);
        SetAdoFields('Cta_Venta', '0');
        SetAdoFields('Item', $_SESSION['INGRESO']['item']);
        SetAdoFields('CodigoU', $_SESSION['INGRESO']['CodigoU']);
        SetAdoFields('Periodo', $_SESSION['INGRESO']['periodo']);
        SetAdoFields('Cod_CxC', $cod_linea);
        SetAdoFields('Com_Pag', 0);
        SetAdoFields('Hora', $hora);
        SetAdoFields('X', 'X');
        SetAdoFields('Serie', $ser);
        SetAdoFields('Vencimiento', $fecha_actual);
        SetAdoFields('P', 0);
        SetAdoFields('Fecha_Aut', $fecha_actual);
        SetAdoFields('RUC_CI', $ruc);
        SetAdoFields('TB', 'R');
        SetAdoFields('Razon_Social', $nombrec);
        SetAdoFields('Total_Efectivo', $total_total_);
        SetAdoFields('Total_Banco', 0);
        SetAdoFields('Otros_Abonos', 0);
        SetAdoFields('Total_Abonos', $total_total_);
        SetAdoFields('Abonos_MN', $total_total_);
        SetAdoFields('Tipo_Pago', $tipo_pago);
        SetAdoFields('Porc_IVA', $_SESSION['INGRESO']['porc']);
        SetAdoFields('Propina', $propina_a);
        SetAdoFields('Autorizacion', $datos1['Autorizacion']);
        SetAdoFields('Saldo_MN', $datos1['Saldo_MN']);
        SetAdoUpdate();
        $n_fac++;
        //incrementar contador de facturas
        $sql="UPDATE Codigos set Numero='".$n_fac."'
        WHERE  Concepto = '".$datos1['TC']."_SERIE_".$ser."'
        AND Item = '".$_SESSION['INGRESO']['item']."'
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."'";
        $conn->String_Sql($sql);
       
        //eliminar campos temporales asiento_f
        $sql="DELETE ". 
          "FROM Asiento_F
          WHERE  Item = '".$_SESSION['INGRESO']['item']."'
          AND  HABIT='".$me."' ";
          $conn->String_Sql($sql);
        //echo $sql;
       
        //eliminar catalogo lineas
        $sql = "DELETE 
              FROM Clientes_Facturacion 
              WHERE Item = '" .$_SESSION['INGRESO']['item']. "' 
              AND Valor <= 0 
              AND Num_Mes >= 0 ";       
          $conn->String_Sql($sql);
        //eliminar abono
        $sql="DELETE FROM Asiento_Abonos 
        WHERE  HABIT= '".$me."' AND 
        Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        AND Item = '".$_SESSION['INGRESO']['item']."'";
        //echo $sql;       
          $conn->String_Sql($sql);
          //campo que informar imprimir pdf automatico
          return 2;
      }
      else
      {
        return 0;
      }
    }
    else
    {
      //liberar mesa 
      $this->liberar($me);
      //datos para el pdf
      $param=array();
      $param[0]['nombrec']=$nombrec;
      //echo $param[0]['nombrec'].' -- ';
      $param[0]['ruc']=$ruc;
      $param[0]['mesa']=$me;
      $param[0]['PFA']='F';
      $param[0]['serie']=$ser1[2];
      $param[0]['factura']=($n_fac-1);
      imprimirDocElPF(null,$me,null,null,null,0,$param,'F',$cid);
      //imprimir factura despues de autorizar 
      return 2;
    }
  }

  function Grabar_Abonos($TA)
  {
    //conexion
    if($TA['Abono']!=0)
    {
    $DiarioCaja = ReadSetDataNum("Recibo_No", True, True);
    if ($TA['Abono'] == '') {
      $TA['Abono'] = 0;
    }
    
    if ($TA['T'] == "" || $TA['T'] == G_NINGUNO || $TA['T'] == "A") {
      $TA['T'] = G_NORMAL;
    }
    if ($TA['Cta_CxP'] == "" || $TA['Cta_CxP'] == G_NINGUNO) {
      $TA['Cta_CxP'] = $TA['Cta_CxP'];
    }
    if(!isset($TA['CodigoC']))
    {
      $TA['CodigoC'] = '';
    }
    if ($TA['CodigoC'] == "" || $TA['CodigoC'] == G_NINGUNO) {
      $TA['CodigoC'] = $TA['CodigoC'];
    }
    if(!isset($TA['Comprobante']))
    {
      $TA['Comprobante'] = '';
    }
    if ($TA['Comprobante'] == "") {
      $TA['Comprobante'] = G_NINGUNO;
    }
    if(isset($TA['Codigo_Inv']))
    {
      if ($TA['Codigo_Inv'] == "") {
        $TA['Codigo_Inv'] = G_NINGUNO;
      }
    }else
    {
       $TA['Codigo_Inv'] = G_NINGUNO;
    }
    if ($TA['Fecha'] == G_NINGUNO) {
      $TA['Fecha'] = date('Y-m-d');
    }
    if ($TA['Serie'] == G_NINGUNO) {
      $TA['Serie'] = "001001";
    }
    if ($TA['Autorizacion'] == G_NINGUNO) {
      $TA['Autorizacion'] = "1234567890";
    }
    if ($TA['Cheque'] == G_NINGUNO && $DiarioCaja > 0 ) {
      $TA['Cheque'] = str_pad($DiarioCaja,7,"0", STR_PAD_LEFT);
    }
    if ($DiarioCaja > 0 ) {
      $TA['Recibo_No'] = str_pad($DiarioCaja,10,"0", STR_PAD_LEFT); 
    }else{
      $TA['Recibo_No'] = "0000000000";
    }
    $cta = '.';
    $Tipo_Cta = Leer_Cta_Catalogo($TA['Cta']);
    // print_r($TA['Cta']);
    if(count($Tipo_Cta)>0)
    {
      $cta = $Tipo_Cta['Codigo_Catalogo'];
      $Tipo_Cta = $Tipo_Cta['SubCta'];
    }else
    {
      $Tipo_Cta='.';      
    }
    // print_r('dasdasasd');die();

    SetAdoAddNew("Trans_Abonos"); 

    SetAdoFields('T',$TA['T']);
    SetAdoFields('TP',$TA['TP']);
    SetAdoFields('Fecha',$TA['Fecha']);
    SetAdoFields('Recibo_No',$TA['Recibo_No']);
    SetAdoFields('Tipo_Cta',$Tipo_Cta);
    SetAdoFields('Cta',$TA['Cta']);
    SetAdoFields('Cta_CxP',$TA['Cta_CxP']);
    SetAdoFields('Factura',$TA['Factura']);
    SetAdoFields('CodigoC',$TA['CodigoC']);
    SetAdoFields('Abono',$TA['Abono']);
    SetAdoFields('Banco',$TA['Banco']);
    SetAdoFields('Cheque',$TA['Cheque']);
    SetAdoFields('Codigo_Inv',$TA['Codigo_Inv']);
    SetAdoFields('Comprobante',$TA['Comprobante']);
    SetAdoFields('Serie',$TA['Serie']);
    SetAdoFields('Autorizacion',$TA['Autorizacion']);
    SetAdoFields('Item',$_SESSION['INGRESO']['item']);
    SetAdoFields('CodigoU',$_SESSION['INGRESO']['CodigoU']);
    if(!isset($TA['Vendedor']) || $TA['Vendedor']==''){
      SetAdoFields('Cod_Ejec',$_SESSION['INGRESO']['CodigoU']);
    }else
    {
      SetAdoFields('Cod_Ejec',$TA['Vendedor']);
    }
   if ($TA['Banco'] == "NOTA DE CREDITO") {
      SetAdoFields('Serie_NC',$TA['Serie_NC']);
      SetAdoFields('Autorizacion_NC',$TA['Autorizacion_NC']);
      SetAdoFields('Secuencial_NC',$TA['Nota_Credito']);
    }
    if(isset($TA['Total']))
    {
      SetAdoFields('Total',$TA['Total']);
    }

    $resp = null;
    if($TA['Abono']>0 && strlen($cta) > 1 && $ipoCta = "D" )
    {
       return SetAdoUpdate();  
    }else
    {
      return -1;
    }
    // print_r($resp);die();
    // if($resp==1)
    // {
    //   echo "<script type='text/javascript'>
    //       Swal.fire({
    //         //position: 'top-end',
    //         type: 'success',
    //         title: 'abono agregado con exito!',
    //         showConfirmButton: true
    //         //timer: 2500
    //       });
    //     </script>";
    // }
   }
  }

  function CodigoCuentaSup($CodigoCta){
    $LongCta =0;
    $Bandera = true;
    $CadAux = "";
    $CadAux = $CodigoCta;
    $LongCta = strlen($CadAux);
    while($LongCta >= 0 && $Bandera){
      if (substr($CadAux, $LongCta-1,1) == ".") {
        $Bandera = false;
      }
      $LongCta--;
    }
    if ($LongCta < 1){
      $CadAux = "0";
    }else{
      $CadAux = substr($CadAux,0,$LongCta);
    }
    return $CadAux;
  }

function Leer_Codigo_Inv($CodigoDeInv,$FechaInventario,$CodBodega='',$CodMarca='')
{
 // 'Datos por default
  if($CodBodega == "" ){$CodBodega = G_NINGUNO;}
  if($CodMarca == ""){ $CodMarca = G_NINGUNO;}
  $Codigo_Ok = False;
  $Con_Kardex = False;
  $DatInv =array();
  $DatInv["Stock"] = 0;
  $DatInv["Costo"] = 0;
  $DatInv["Codigo_Barra"] = G_NINGUNO;
  $DatInv["Tipo_SubMod"] = G_NINGUNO;
  $DatInv["Cta_Inventario"] = G_NINGUNO;
  $DatInv["Cta_Costo_Venta"] = G_NINGUNO;
  $DatInv["Codigo_Inv"] = $CodigoDeInv;
  $DatInv["Fecha_Stock"] = $FechaInventario;
  $DatInv["TC"]='';
  $DatInv['Con_Kardex'] = false;

  $f = explode('-',$FechaInventario);
  // print_r($f);die();
  
 // 'Validacion de datos correctos
  if(checkdate($f[1],$f[2],$f[0]) ) { $DatInv["Fecha_Stock"] = date('Y-m-d');}
  if(strlen($DatInv["TC"]) <= 1){$DatInv["TC"] = "FA";}
  $BuscarCodigoInv = $CodigoDeInv;
  $CodigoDeInv = Leer_Codigo_Inv_SP($BuscarCodigoInv,$DatInv["Fecha_Stock"],$CodBodega,$CodMarca,$DatInv["Codigo_Inv"]);
  
 // '-----------------------------------------------------------------
 // 'Si existe el producto pasamos a recolectar los datos del producto
 // '-----------------------------------------------------------------
  if($DatInv["Codigo_Inv"] <> G_NINGUNO)
  {

     $conn = new db();
     $sql = "SELECT Producto, Detalle, Codigo_Barra_K, Unidad, Minimo, Maximo, Cta_Inventario, Cta_Costo_Venta, Cta_Ventas, Cta_Ventas_0, Cta_Venta_Anticipada, 
          Utilidad, Div, PVP_2, Por_Reservas, Reg_Sanitario, IVA, PVP, Tipo_SubMod, Stock, Costo, Valor_Unit,Con_Kardex  
          FROM Catalogo_Productos 
          WHERE Item = '".$_SESSION['INGRESO']['item']."' 
          AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
          AND Codigo_Inv = '".$DatInv["Codigo_Inv"]."' ";
      $datos = $conn->datos($sql);
      // print_r($datos);die();
      if(count($datos)>0)
      {
          $DatInv["Producto"]= $datos[0]["Producto"];
          $DatInv["Detalle"] = $datos[0]["Detalle"];
          $DatInv["Codigo_Barra"] = $datos[0]["Codigo_Barra_K"];
          $DatInv["Unidad"]= $datos[0]["Unidad"];
          $DatInv["Minimo"] = $datos[0]["Minimo"];
          $DatInv["Maximo"] = $datos[0]["Maximo"];
          $DatInv["Cta_Inventario"] = $datos[0]["Cta_Inventario"];
          $DatInv["Cta_Costo_Venta"]= $datos[0]["Cta_Costo_Venta"];
          $DatInv["Cta_Ventas"] = $datos[0]["Cta_Ventas"];
          $DatInv["Cta_Ventas_0"] = $datos[0]["Cta_Ventas_0"];
          $DatInv["Cta_Venta_Anticipada"] = $datos[0]["Cta_Venta_Anticipada"];
          $DatInv["Utilidad"] = $datos[0]["Utilidad"];
          $DatInv["Div"] = $datos[0]["Div"];
          $DatInv["PVP2"] = $datos[0]["PVP_2"];
          $DatInv["Por_Reservas"] = $datos[0]["Por_Reservas"];
          $DatInv["Reg_Sanitario"] = $datos[0]["Reg_Sanitario"];
          $DatInv["Stock"] = $datos[0]["Stock"];
          $DatInv["Costo"] = number_format($datos[0]["Costo"], $_SESSION['INGRESO']['Dec_Costo'],'.','');
          $DatInv["Valor_Unit"] = number_format($datos[0]["Valor_Unit"], $_SESSION['INGRESO']['Dec_Costo'],'.','');
          $DatInv["Tipo_SubMod"] = $datos[0]["Tipo_SubMod"];
          $DatInv['Con_Kardex'] = $datos[0]["Con_Kardex"];
          switch ($DatInv["TC"]) {
            case 'NV':
            case 'PV':
                if($datos[0]["IVA"]){$DatInv["PVP"] = $datos[0]["PVP"]*(1 + $Porc_IVA); }else{ $DatInv["PVP"] = $datos[0]["PVP"];}
                 $DatInv["IVA"] = False;
              break;            
            default:
                 $DatInv["PVP"] = $datos[0]["PVP"];
                 $DatInv["IVA"] = $datos[0]["IVA"];
              break;
          }

          //revisar por si hay que comentar---
          if(strlen($DatInv["Cta_Ventas"])>1 && strlen($DatInv["Cta_Inventario"])<=1)
          {
            $DatInv["Stock"] =99999999;
          }      
          //----------------------------------------------------------------------   
          $Codigo_Ok = True;     

      }else
      {
        $respuesta  ="Producto no Asignado";
        return array('respueta'=>-1,'datos'=>$respuesta);
      }
    }else
    {
      $respuesta = "No existen datos";
      return array('respueta'=>-1,'datos'=>$respuesta);
    }

 return $Leer_Codigo_Inv = array('respueta'=>$Codigo_Ok,'datos'=>$DatInv);

}

function BuscarFecha($FechaStr)
{
  if(is_numeric($FechaStr)){
     if($_SESSION['INGRESO']['Tipo_Base']=='SQLSERVER' || $_SESSION['INGRESO']['Tipo_Base']== 'SQL SERVER'){

      $newDate = date("Ymd", strtotime($FechaStr));
        return $newDate;       
     
     }else{
        $newDate = date("Y-m-d", strtotime($FechaStr));
        return $newDate; 
     }
     // 'MsgBox "Fecha Incorrecta"
  }else{

     if($_SESSION['INGRESO']['Tipo_Base']=='SQLSERVER' || $_SESSION['INGRESO']['Tipo_Base']== 'SQL SERVER'){
      if(is_object($FechaStr))
      {
        $FechaStr = $FechaStr->format('Y-m-d');
      }
      $newDate = date("Ymd", strtotime($FechaStr));

        return $newDate;     
     }else{
        $newDate = date("Y-m-d", strtotime($FechaStr));
        return $newDate; 
     }
  }
}

function Lineas_De_CxC($TFA)
{
  $conn = new db();
  $Cta_CajaG=1;//crear funcion para setear esto
  $Cta_CajaGE=1;//crear funcion para setear esto
  $Cta_CajaBA=1;//crear funcion para setear esto
  $TFA['Vencimiento'] = date('Y-m-d');
  $TFA['Porc_IVA'] = $_SESSION['INGRESO']['porc'];
  $TFA['Porc_Serv'] = $_SESSION['INGRESO']['Porc_Serv'];
  if($TFA['Vencimiento']!='')
  {
   $FA['Vencimiento'] = $TFA['Vencimiento'];
  }

  $Cant_Item_FA = 1;  $TFA['Cant_Item_FA']=$Cant_Item_FA ;
  $Cant_Item_PV = 1;  
  $TFA['Cta_CxP'] = G_NINGUNO;
  $TFA['Cta_Venta'] = G_NINGUNO;
  if($TFA['Fecha']==''){$TFA['Fecha']=date('Y-m-d');}
  if(!isset($TFA['Fecha_NC']) || $TFA['Fecha_NC']==''){$TFA['Fecha_NC']=date('Y-m-d');}
  // 'MsgBox LineaCxC
  $sSQL = "SELECT Concepto, Logo_Factura, Largo, Ancho, Espacios, Pos_Factura, Fact_Pag, Pos_Y_Fact, Serie, Autorizacion, Vencimiento, Fecha, Secuencial, " .
      "ItemsxFA, Codigo, Fact, CxC, Cta_Venta, CxC_Anterior, Imp_Mes, Nombre_Establecimiento, Direccion_Establecimiento, Telefono_Estab, Logo_Tipo_Estab " .
      "FROM Catalogo_Lineas " .
      "WHERE Item = '" . $_SESSION['INGRESO']['item'] . "' " .
      "AND Periodo = '" . $_SESSION['INGRESO']['periodo'] . "' ";
  if (strlen($TFA['TC']) == 2) $sSQL .= "AND Fact = '" . $TFA['TC'] . "' ";
  if (strlen($TFA['Cod_CxC']) > 1) {
    $sSQL .= "AND '" . $TFA['Cod_CxC'] . "' IN (Concepto, Codigo, CxC) ";
  } elseif (strlen($TFA['Serie']) == 6) {
    $sSQL .= "AND Serie = '" . $TFA['Serie'] . "' ";
  } elseif (strlen($TFA['Autorizacion']) >= 6) {
    $sSQL .= "AND Autorizacion = '" . $TFA['Autorizacion'] . "' ";
  }

  if($TFA['TC'] == "NC"){
     $sSQL.= " AND Fecha <= '".BuscarFecha($TFA['Fecha_NC'])."' 
           AND Vencimiento >= '".BuscarFecha($TFA['Fecha_NC'])."'";
  }else{
     $sSQL.= " AND Fecha <= '".BuscarFecha($TFA['Fecha'])."' 
           AND Vencimiento >= '".BuscarFecha($TFA['Fecha'])."' ";
  }
  $sSQL.=' ORDER BY Codigo';

  $datos = $conn->datos($sSQL);
   // print_r($sSQL);die();
  if(count($datos)>0)
  {   
      $TFA['CxC_Clientes'] = $datos[0]["Concepto"];
      $TFA['LogoFactura'] = $datos[0]["Logo_Factura"];
      $TFA['AltoFactura'] = $datos[0]["Largo"];
      $TFA['AnchoFactura'] = $datos[0]["Ancho"];
      $TFA['EspacioFactura'] = $datos[0]["Espacios"];
      $TFA['Pos_Factura'] = $datos[0]["Pos_Factura"];
      $TFA['Pos_Copia'] = $datos[0]["Pos_Y_Fact"];
      $TFA['CantFact'] = $datos[0]["Fact_Pag"];
      
     // 'Datos para grabar automaticamente
      $TFA['TC'] = trim(strtoupper($datos[0]["Fact"]));
      $TFA['Serie'] = $datos[0]["Serie"];
      $TFA['Autorizacion'] = $datos[0]["Autorizacion"];
      $TFA['Fecha_Aut'] = $datos[0]["Fecha"];
      $TFA['Vencimiento'] = $datos[0]["Vencimiento"];
      $TFA['Cta_CxP'] = $datos[0]["CxC"];
      $TFA['Cta_Venta'] = $datos[0]["Cta_Venta"];
      $TFA['Cta_CxP_Anterior'] = $datos[0]["CxC_Anterior"];
      $TFA['Cod_CxC'] = $datos[0]["Codigo"];
      $TFA['Imp_Mes'] = $datos[0]["Imp_Mes"];
      $TFA['DireccionEstab'] = $datos[0]["Direccion_Establecimiento"];
      $TFA['NombreEstab'] = $datos[0]["Nombre_Establecimiento"];
      $TFA['TelefonoEstab'] = $datos[0]["Telefono_Estab"];
      $TFA['LogoTipoEstab']= '../../img/logotipos/'.$datos[0]["Logo_Tipo_Estab"].".jpg";
      if($TFA['TC'] == "NC"){
         $TFA['Serie_NC'] = $datos[0]["Serie"];
         $TFA['Autorizacion_NC'] = $datos[0]["Autorizacion"];
      }else{
         $TFA['Cant_Item_FA'] = $datos[0]["ItemsxFA"];
         $Cant_Item_FA = $datos[0]["ItemsxFA"];
         $Cant_Item_PV = $datos[0]["ItemsxFA"];
         $Cta_Cobrar = $datos[0]["CxC"];
         $Cta_Ventas = $datos[0]["Cta_Venta"];
         $CodigoL = $datos[0]["Codigo"];
         $TipoFactura = $datos[0]["Fact"];
      }

  }else
  {
       $MsgBox = "Codigos No Asignados o fuera de fecha";
  }

  // print_r($TFA);die();

   // 'MsgBox $TFA['Cta_CxP']
  if($TFA['Cta_CxP'] <> G_NINGUNO ){
     $ExisteCtas = array();
     $ExisteCtas[0] = $TFA['Cta_CxP'];
     $ExisteCtas[1] = $Cta_CajaG;
     $ExisteCtas[2] = $Cta_CajaGE;
     $ExisteCtas[3] = $Cta_CajaBA;
     $resp = VerSiExisteCta($ExisteCtas);
  }
 // 'AdoLineaDB.Close
  if($Cant_Item_FA <= 0){$Cant_Item_FA = 15; $TFA['Cant_Item_FA'] = $Cant_Item_FA;}
  if($Cant_Item_PV <= 0 ){$Cant_Item_PV = 15;}
  $Cadena = "Esta Ingresando Comprobantes Caducados La Fecha Tope de emision es: ".$FA['Vencimiento'];

  // print_r($TFA);die();
  $fecha1 = strtotime($TFA['Fecha']);
  $fecha2 = strtotime($TFA['Vencimiento']->format('Y-m-d'));

  if($fecha1 > $fecha2 ){ return array('respuesta'=>-1,'TFA'=>$TFA,'mensaje'=>$Cadena);}
   if($resp!=1)
    {
       return array('respuesta'=>2,'TFA'=>$TFA,'mensaje'=>$resp);
    } 
  return array('respuesta'=>1,'TFA'=>$TFA,'mensaje'=>'');

}


function VerSiExisteCta($ExisteCtas)
{
  // print_r($ExisteCtas);die();
  $ListCtas[0] = G_NINGUNO;
  $NCtas = "";
  foreach ($ExisteCtas as $key => $value) {
    if(strlen($value) > 0 ){$NCtas.="'".$value."',";}
  }
  $NCtas = substr($NCtas, 0,- 1);
  if(strlen($NCtas) >= 1){

    $conn = new db();
    $sql = "SELECT Codigo,Cuenta 
            FROM Catalogo_Cuentas 
            WHERE Codigo IN (".$NCtas.") 
            AND Item = '".$_SESSION['INGRESO']['item']."' 
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."'";
            // print_r($sql);die();
    $datos = $conn->datos($sql);
    if(count($datos)>0)
    {
      foreach ($datos as $key => $value) {
        $ListCtas[$key] = $value['Codigo'];
      }
    }
  }
  $NCtas = "";
  // $nombreCuenta = "";
  // $ExisteCtasV = count($ExisteCtas)-1;
  // $ListCtasV = count($ListCtas);
  $d='';
  if(count($ExisteCtas) > 0){
    foreach ($ExisteCtas as $key => $value) {   
         $NoExiste = 1;
         foreach ($ListCtas as $key1 => $value1) {
          if($value==$value1)
            {
              $NoExiste = 0;
              break;
            }           
         }      
         if($NoExiste==1){$NCtas = $NCtas."'".$value."',";}
    }
  }
  if(strlen($NCtas) > 1){
     return "Falta de setear la(s) cuenta(s) siguiente(s):".$NCtas." no existe en el 'Catalogo de Lineas CxC'
           Debe Setearla en: Linea de CxC de: FA, NV, NC, LC y GR";
  }
  return 1;
 }



function Leer_Datos_Clientes($Codigo_CIRUC_Cliente,$Por_Codigo=true,$Por_CIRUC=false,$Por_Cliente=false)
{

    $conn = new db();
    // $Por_Codigo = False;
    // $Por_CIRUC = False;
    // $Por_Cliente = False;
    if(strlen($Codigo_CIRUC_Cliente) <= 0){ $Codigo_CIRUC_Cliente = G_NINGUNO;}
    
    // Leer_Datos_Cliente_SP($Codigo_CIRUC_Cliente);
    $TBenef_Codigo = $Codigo_CIRUC_Cliente;
        
   // 'Verificamos la informacion del Clienete
    if($TBenef_Codigo <> "." ){
  
        $sql = "SELECT T,FA,Cliente,Codigo,Descuento,CI_RUC,TD,Fecha,Fecha_N,Sexo,Email,Email2,EmailR,Direccion,DireccionT,DirNumero,Ciudad,Prov,Pais,Profesion,
          Telefono,Telefono_R,TelefonoT,Grupo,Contacto,Calificacion,Plan_Afiliado,Actividad,Credito,Representante,CI_RUC_R,TD_R,Tipo_Cta,Cod_Banco,
          Cta_Numero,Fecha_Cad,Asignar_Dr,Saldo_Pendiente 
          FROM Clientes 
          WHERE 1=1 ";
          if($Por_Codigo)
          {
            $sql.="AND Codigo = '".$TBenef_Codigo. "' ";
          }
          if($Por_CIRUC)
          {
            $sql.="AND CI_RUC = '".$TBenef_Codigo. "' ";
          }
          if($Por_Cliente)
          {

            $sql.="AND Cliente = '".$TBenef_Codigo. "' ";
          }         
          // print_r($sql);die();
          $datos = $conn->datos($sql);
            if(count($datos) > 0 ){
              return $datos[0];
             // '.Salario = 0
            }
    }
    // Leer_Datos_Clientes = TBenef
}

function  Grabar_Abonos_Retenciones($FTA)
{
  control_procesos("P",$FTA['Banco']." ".$FTA['TP']." No. ".$FTA['Serie']."-".str_pad($FTA['Factura'], 7, '0', STR_PAD_LEFT) ,'Por: '.number_format($FTA['Abono'], 2, ',', '.'));
  if(count($FTA))
  {
    if($FTA['Abono'] > 0){
      if($FTA['T']== "" || $FTA['T'] == G_NINGUNO){ $FTA['T'] = G_NORMAL;}
      $cuenta = buscar_en_ctas_proceso('Cta_Cobrar');
      if($FTA['Cta_CxP'] == "" || $FTA['Cta_CxP'] == G_NINGUNO){$FTA['Cta_CxP'] = $cuenta;}
      if($FTA['CodigoC'] == "" || $FTA['CodigoC'] == G_NINGUNO){$FTA['CodigoC'] = '999999999';}
      if($FTA['Comprobante'] == ""){$FTA['Comprobante'] = G_NINGUNO;}
      if($FTA['Codigo_Inv'] == ""){$FTA['Codigo_Inv'] = G_NINGUNO;}
      if($FTA['Fecha'] == G_NINGUNO){$FTA['Fecha'] = date('Y-m-d');}
      if($FTA['Serie'] == G_NINGUNO){$FTA['Serie'] = "001001";}
      if($FTA['Autorizacion'] == G_NINGUNO){$FTA['Autorizacion'] = "1234567890";}
      if($FTA['Cheque'] == G_NINGUNO && $FTA['DiarioCaja'] > 0 ){ $FTA['Cheque'] = generaCeros($FTA['DiarioCaja'],8);}
      if($FTA['DiarioCaja'] > 0){$FTA['Recibo_No'] = generaCeros($FTA['DiarioCaja'],10);}else{$FTA['Recibo_No'] = "0000000000";}
      $cuenta = Leer_Cta_Catalogo($FTA['Cta']);
      $FTA['Tipo_Cta']  = '.';
      if(isset( $cuenta['SubCta']))
      {
        $FTA['Tipo_Cta'] = $cuenta['SubCta'];
      }

      SetAdoAddNew("Trans_Abonos");
      SetAdoFields("T", $FTA['T']);
      SetAdoFields("TP", $FTA['TP']);
      SetAdoFields("Fecha", $FTA['Fecha']);
      SetAdoFields("Recibo_No", $FTA['Recibo_No']);
      SetAdoFields("Tipo_Cta", $FTA['Tipo_Cta']);
      SetAdoFields("Cta", $FTA['Cta']);
      SetAdoFields("Cta_CxP", $FTA['Cta_CxP']);
      SetAdoFields("Factura", $FTA['Factura']);
      SetAdoFields("CodigoC", $FTA['CodigoC']);
      SetAdoFields("Abono", $FTA['Abono']);
      SetAdoFields("Banco", $FTA['Banco']);
      SetAdoFields("Cheque", $FTA['Cheque']);
      SetAdoFields("Codigo_Inv", $FTA['Codigo_Inv']);
      SetAdoFields("Comprobante", $FTA['AutorizacionR']);
      SetAdoFields("EstabRetencion", $FTA['Establecimiento']);
      SetAdoFields("PtoEmiRetencion", $FTA['Emision']);
      SetAdoFields("Porc", $FTA['Porcentaje']);
      SetAdoFields("Serie", $FTA['Serie']);
      SetAdoFields("Autorizacion", $FTA['Autorizacion']);
      SetAdoFields("Autorizacion_R", $FTA['AutorizacionR']);
      SetAdoFields("CodigoU", $_SESSION['INGRESO']['CodigoU']);
      SetAdoFields("Item", $_SESSION['INGRESO']['item']);
      SetAdoUpdate();
    }
  }
  Actualiza_Estado_Factura($FTA);
}


function Actualiza_Estado_Factura($FTA)
{
  $conn = new db();
 // 'MsgBox FTA.Factura & vbCrLf & FTA.Serie & vbCrLf & FTA.Autorizacion & vbCrLf & FTA.TP
   if(count($FTA)>0){
       $AbonoTP = 0;
       $sql_Abono = "SELECT Factura, SUM(Abono) As Total_Abonos
                  FROM Trans_Abonos
                  WHERE TP = '".$FTA['TP']."'
                  AND Serie = '".$FTA['Serie']."'
                  AND Factura = ".$FTA['Factura']."
                  AND Autorizacion = '".$FTA['Autorizacion']."'
                  AND CodigoC = '".$FTA['CodigoC']."'
                  AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
                  AND Item = '".$_SESSION['INGRESO']['item']."'
                  GROUP BY Factura ";

        $datos = $conn->datos($sql_Abono);
        if(count($datos)>0)
        {
          $AbonoTP = number_format($datos[0]['Total_Abonos'],2,'.','');
        }
       if($AbonoTP > 0 ){
          $sqlAbono = "UPDATE Facturas 
                  SET Saldo_MN = Total_MN - ".$AbonoTP." 
                  WHERE Factura = ".$FTA['Factura']." 
                  AND Serie = '".$FTA['Serie']."' 
                  AND Autorizacion = '".$FTA['Autorizacion']."' 
                  AND TC = '".$FTA['TP']."' 
                  AND CodigoC = '".$FTA['CodigoC']."' 
                  AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
                  AND Item = '".$_SESSION['INGRESO']['item']."' ";
                  $conn->String_Sql($sqlAbono);
          
          $sqlAbono = "UPDATE Facturas 
                    SET T = 'C' 
                    WHERE Factura = ".$FTA['Factura']." 
                    AND Serie = '".$FTA['Serie']."' 
                    AND Autorizacion = '".$FTA['Autorizacion']."' 
                    AND TC = '".$FTA['TP']."' 
                    AND CodigoC = '".$FTA['CodigoC']."' 
                    AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
                    AND Item = '".$_SESSION['INGRESO']['item']."' 
                    AND Saldo_MN <= 0 
                    AND T <> 'A' ";
                    $conn->String_Sql($sqlAbono);
          
          if($_SESSION['INGRESO']['Tipo_Base']=='SQL SERVER'){
             $sqlAbono = "UPDATE Detalle_Factura
                       SET T = F.T 
                       FROM Detalle_Factura As DF, Facturas As F ";
          }else{
             $sqlAbono = "UPDATE Detalle_Factura As DF, Facturas As F
                       SET DF.T = F.T ";
          }
          $sqlAbono.= "WHERE F.Factura = ".$FTA['Factura']." 
                    AND F.Serie = '".$FTA['Serie']."' 
                    AND F.Autorizacion = '".$FTA['Autorizacion']."' 
                    AND F.TC = '".$FTA['TP']."' 
                    AND F.CodigoC = '".$FTA['CodigoC']."' 
                    AND F.Item = '".$_SESSION['INGRESO']['item']."' 
                    AND F.Periodo = '".$_SESSION['INGRESO']['periodo']."' 
                    AND F.Item = DF.Item 
                    AND F.Periodo = DF.Periodo 
                    AND F.Factura = DF.Factura 
                    AND F.CodigoC = DF.CodigoC 
                    AND F.Autorizacion = DF.Autorizacion 
                    AND F.Serie = DF.Serie 
                    AND F.TC = DF.TC ";

                    // print_r($sqlAbono);die();
                    $conn->String_Sql($sqlAbono);
       
          if($_SESSION['INGRESO']['Tipo_Base']=='SQL SERVER'){
             $sqlAbono = "UPDATE Trans_Abonos
                       SET T = F.T 
                       FROM Trans_Abonos As DF, Facturas As F ";
          }else{
             $sqlAbono = "UPDATE Trans_Abonos As DF, Facturas As F;
                       SET DF.T = F.T ";
          }
          $sqlAbono.= "WHERE F.Factura = ".$FTA['Factura']." 
                    AND F.Serie = '".$FTA['Serie']."' 
                    AND F.Autorizacion = '".$FTA['Autorizacion']."' 
                    AND F.TC = '".$FTA['TP']."' 
                    AND F.CodigoC = '".$FTA['CodigoC']."' 
                    AND F.Item = '".$_SESSION['INGRESO']['item']."' 
                    AND F.Periodo = '".$_SESSION['INGRESO']['periodo']."' 
                    AND F.Item = DF.Item 
                    AND F.Periodo = DF.Periodo 
                    AND F.Factura = DF.Factura 
                    AND F.CodigoC = DF.CodigoC 
                    AND F.Autorizacion = DF.Autorizacion 
                    AND F.Serie = DF.Serie 
                    AND F.TC = DF.TP ";
                    $conn->String_Sql($sqlAbono);

      }
  }
}


function Imprimir_Comprobante_Caja($FTA)
{
  $comm = new db();  
  $Saldo_P ='';
  if(Leer_Campo_Empresa("Imp_Recibo_Caja"))
  {
     $TRecibo['Tipo_Recibo'] = "I";
     $Saldo_P = 0;
     $sql= "SELECT Factura,Saldo_MN
          FROM Facturas
          WHERE Item = '".$_SESSION['INGRESO']['item']."'
          AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
          AND TC = '" .$FTA['TP']. "'
          AND Serie = '" .$FTA['Serie']. "'
          AND Autorizacion = '" .$FTA['Autorizacion']. "'
          AND Factura = " .$FTA['Factura']. "
          AND CodigoC = '" .$FTA['CodigoC']. "' ";
      $datos = $comm->datos($sql);
     if(count($datos) > 0){$Saldo_P = $datos[0]["Saldo_MN"];}
     
     $sql1 = "SELECT Factura,Mes,Ticket,Codigo,Producto 
          FROM Detalle_Factura 
          WHERE Item = '".$_SESSION['INGRESO']['item']."'
          AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
          AND TC = '".$FTA['TP']."' 
          AND Serie = '".$FTA['Serie']."' 
          AND Autorizacion = '".$FTA['Autorizacion']."' 
          AND Factura = ".$FTA['Factura']." 
          AND CodigoC = '".$FTA['CodigoC']."' 
          ORDER BY Codigo,Ticket,Mes ";
      $datos1 = $comm->datos($sql1);    
      $TRecibo['Concepto'] = "";
      if(count($datos1))
      {
          $Codigo_M = $datos1[0]["Mes"];
          $Codigo_A = $datos1[0]["Ticket"];
          $Codigo_P = $datos1[0]["Codigo"];
          if($datos1[0]["Ticket"] <> G_NINGUNO){
              $Codigo_S = $datos1[0]["Ticket"].", ".$datos1[0]["Producto"]." ";
          }else{
              $Codigo_S = $datos1[0]["Producto"]." ";
          }
          foreach ($datos1 as $key => $value) 
          {           
             if($Codigo_A <> $value["Ticket"])
             {
                $TRecibo['Concepto'] = $TRecibo['Concepto'].$Codigo_S;
                $Codigo_P = $value["Codigo"];
                if($value["Ticket"] <> G_NINGUNO){
                    $Codigo_S = $value["Ticket"].", ".$value["Producto"]." ";
                }else{
                    $Codigo_S = ", ".$value["Producto"]." ";
                }
                $Codigo_A = $value["Ticket"];
             }
             if($Codigo_P <> $value["Codigo"])
             {
                $TRecibo['Concepto'] = $TRecibo['Concepto'].$Codigo_S;
                $Codigo_P = $value["Codigo"];
                if($value["Ticket"] <> G_NINGUNO)
                {
                    $Codigo_S = $value["Ticket"].", ".$value["Producto"]." ";
                }else{
                    $Codigo_S = ", ".$value["Producto"]." ";
                }
              }
             if($value["Mes"] <> G_NINGUNO){ $Codigo_S = $Codigo_S." ".$value["Mes"];}
             $TRecibo['Concepto'] = $TRecibo['Concepto'].$Codigo_S;
          }
        }
     
     $sql3 = "SELECT Serie,Factura,Abono,Cheque,Banco,Cta 
          FROM Trans_Abonos 
          WHERE Item = '".$_SESSION['INGRESO']['item']."'
          AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
          AND TP = '".$FTA['TP']."' 
          AND Fecha = '".BuscarFecha($FTA['Fecha'])."' 
          AND Serie = '".$FTA['Serie']."' 
          AND Autorizacion = '".$FTA['Autorizacion']."' 
          AND Factura = ".$FTA['Factura']." 
          AND CodigoC = '".$FTA['CodigoC']."' ";
     $datos3 = $comm->datos($sql3);
     if(count($datos3))
     {    
        $TRecibo['Cobrado_a'] = $FTA['Recibi_de'];
        $TRecibo['Recibo_No'] = $FTA['Recibo_No'];
        $TRecibo['Fecha'] = $FTA['Fecha'];
        $TRecibo['Total'] = 0;
      foreach ($datos3 as $key => $value) {        
           $TRecibo['Total'] = $TRecibo['Total'] + $value["Abono"];
           $Valor_Str = number_format($value['Abono'],2,'.',',');
           $TRecibo['Concepto'] = $TRecibo['Concepto'].
                   "CI/RUC/Codigo: ".$FTA['CI_RUC_Cli'].", 
                    Factura No. ".$value["Serie"]."-".generaCeros($value["Factura"],9).", "
                   .$value["Cheque"]
                   .$value["Banco"].", "
                   .$value["Cta"].", USD "
                   .number_format($value["Abono"],2,'.',',');
         }
        if($Saldo_P <> 0){ $TRecibo['Concepto'] = $TRecibo['Concepto']."Saldo Pendiente USD ".number_format($Saldo_P,2,'.',',');}

    }
    // print_r($TRecibo);die();    
    return  Imprimir_Recibo_Caja($TRecibo);
  } 
  return 1;
}



function Imprimir_Recibo_Caja($TRecibo)
{
  $pdf = new cabecera_pdf();

}

function UltimoDiaMes($FechaStr)
{
  $vFechaStr = $FechaStr;
  $f = explode('/', $vFechaStr);
  if(checkdate($f[0], $f[1], $f[2])==false) {$vFechaStr = date('Y-m-d');}
  if($vFechaStr == "00/00/0000"){$vFechaStr = date('Y-m-d');}
  $Vmes = date("m",  strtotime($vFechaStr));
  $Vanio = date("Y", strtotime( $vFechaStr));
  $vDia = 31;
  switch ($Vmes) {
    case '4':
    case '6':
    case '9':
    case '11':
       $vDia = 30;
      break;
    case '2':
       $vDia = 28;
         if ($Vanio % 4 == 0){$vDia = 29;}
      break;
  }
 
  return $UltimoDiaMes = generaCeros($vDia,2)."/".generaCeros($Vmes,2)."/".generaCeros($Vanio,4);
}

function UltimoDiaMes2($FechaStr, $formato_salida='d/m/Y', $formato_entrada='d/m/Y')
{
  $d = date_create_from_format($formato_entrada, $FechaStr);
  $fecha = date_format($d,'Y-m-d');
  $newFecha = new datetime($fecha);
  $newFecha->modify('last day of this month'); 
  return $newFecha->format($formato_salida);
}

function PrimerDiaMes($fecha, $formato_salida ="d/m/Y")
{
  $d = new datetime($fecha);
  $d->modify('first day of this month'); 
  return $d->format($formato_salida);
}

function PrimerDiaSeguienteMes($fecha, $formato_salida ="d/m/Y")
{
  $d = date_create_from_format($formato_salida, $fecha);
  $fecha = date_format($d,'Y-m-d');
  $d = new datetime($fecha);
  $fecha = $d->modify('next month');
  $newFecha= new datetime($fecha->format('Y-m-d'));
  $newFecha->modify('first day of this month'); 
  return $newFecha->format($formato_salida);
}

function ObtenerMesFecha($fecha, $formato_entrada ="d/m/Y")
{
  $d = date_create_from_format($formato_entrada, $fecha);
  return date_format($d,'m');
}

function ObtenerAnioFecha($fecha, $formato_entrada ="d/m/Y")
{
  $d = date_create_from_format($formato_entrada, $fecha);
  return date_format($d,'Y');
}

function MesesLetras($Mes,$Mayuscula=false)
{
   $SMes = "";
   switch ($Mes) {
     case '1':
       $SMes = 'Enero';
       break;
    case '2':
       $SMes = 'Febrero';
       break;
    case '3':
       $SMes = 'Marzo';
       break;
    case '4':
       $SMes = 'Abril';
       break;
    case '5':
       $SMes = 'Mayo';
       break;
    case '6':
       $SMes = 'Junio';
       break;
    case '7':
       $SMes = 'Julio';
       break;
    case '8':
       $SMes = 'Agosto';
       break;
    case '9':
       $SMes = 'Septiembre';
       break;
    case '10':
       $SMes = 'Octubre';
       break;
    case '11':
       $SMes = 'Noviembre';
       break;
    case '12':
       $SMes = 'Diciembre';
       break;    
   }
   if($Mayuscula){$SMes = strtoupper($SMes);}
   return $SMes;
}

function FechaStrg($Fechas,$FormatoFechas = "dd/mm/yyyy") {
  $Fechas = date($FormatoFechas, strtotime($Fechas)); // convierte la fecha a formato dd/mm/yyyy
  $dd = date("d", strtotime($Fechas));
  $MM = date("n", strtotime($Fechas));
  $AA = date("Y", strtotime($Fechas));
  if ($AA < 1) $AA = date("Y"); // si el año es menor que 1, utiliza el año actual
  if (($MM < 1) || ($MM > 12)) $MM = date("n"); // si el mes es menor que 1 o mayor que 12, utiliza el mes actual
  if (($dd < 1) || ($dd > cal_days_in_month(CAL_GREGORIAN, $MM, $AA))) $dd = cal_days_in_month(CAL_GREGORIAN, $MM, $AA); // si el día es menor que 1 o mayor que los días del mes, utiliza los días del mes actual
  return $dd . " de " . MesesLetras($MM) . " del " . $AA;
}

function  Imprimir_Facturas($TFA){
// Dim AdoDBDetalle As ADODB.Recordset
// Dim CadenaMoneda As String
// Dim Numero_Letras As String
// Dim NombUusuario As String
// Dim Cad_Tipo_Pago As String
// Dim PVP_Desc As Currency
// Dim Orden_No_S As String
// Dim PFilT As Single
// Dim PFilTemp As Single
// Dim Desc_Sin_IVA As Currency
// Dim Desc_Con_IVA As Currency

// 'Establecemos Espacios y seteos de impresion
// On Error GoTo Errorhandler
//   'MsgBox TipoFact
//    CEConLineas = ProcesarSeteos(TFA.TC)
// CantFils = 0
// Mensajes = "Imprmir Factura No. " & TFA.Factura
// Titulo = "IMPRESION"
// Bandera = False
// SetPrinters.Show 1
// If PonImpresoraDefecto(SetNombrePRN) Then
   
//    tPrint.TipoImpresion = Es_Printer
//    tPrint.NombreArchivo = TFA.TC & "-" & TFA.Serie & "-" & Format(TFA.Factura, "000000000")
//    tPrint.TituloArchivo = TFA.TC & "-" & TFA.Serie & "-" & Format(TFA.Factura, "000000000")
//    tPrint.TipoLetra = TipoCourier ' TipoArialNarrow
//    tPrint.OrientacionPagina = Orientacion_Pagina
//    tPrint.PaginaA4 = True
//    tPrint.EsCampoCorto = False
//    tPrint.VerDocumento = True
//    Set cPrint = New cImpresion
//    cPrint.iniciaImpresion
//    RatonReloj
//    Orden_No_S = ""
//    cPrint.tipoDeLetra = TipoCourier ' TipoArialNarrow
//    cPrint.tipoNegrilla = True
//    Leer_Datos_FA_NV TFA
//    Set AdoDBDetalle = Leer_Datos_FA_NV_Detalle(TFA)
//    With AdoDBDetalle
//     If .RecordCount > 0 Then
//         Orden_No = .Fields("Orden_No")
//         Do While (Not .EOF)
//            If Orden_No <> TFA.Orden_Compra And TFA.Orden_Compra > 0 Then
//               Orden_No_S = Orden_No_S & CStr(Orden_No) & " "
//               Orden_No = TFA.Orden_Compra
//            End If
//           .MoveNext
//         Loop
//         Orden_No_S = Orden_No_S & CStr(Orden_No) & " "
//        .MoveFirst
//     End If
//    End With
//   'MsgBox FA.Si_Existe_Doc
//    If FA.Si_Existe_Doc Then
//      '--------------------------------------------------------------------------------------------------------
//      'Encabezado de la factura
//      '--------------------------------------------------------------------------------------------------------
//      'Imagen de la factura
//       If TFA.LogoFactura <> "NINGUNO" And TFA.AnchoFactura > 0 And TFA.AltoFactura > 0 Then
//          If SetD(1).PosX > 0 And SetD(1).PosY > 0 Then
//             Codigo4 = Format$(TFA.Factura, "000000000")
//             If TFA.LogoFactura = "MATRICIA" Then
//                Imprimir_Formato_Propio "IF", SetD(1).PosX, SetD(1).PosY
//             Else
//                Cadena = RutaSistema & "\FORMATOS\" & TFA.LogoFactura & ".gif"
//                cPrint.printImagen Cadena, SetD(1).PosX, SetD(1).PosY, TFA.AnchoFactura, TFA.AltoFactura
//                cPrint.printImagen LogoTipo, SetD(34).PosX, SetD(34).PosY, 2.5, 1.25
//             End If
//          End If
//       End If
//       If SetD(2).PosX > 0 And SetD(2).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(2).Tamaño
//          cPrint.printTexto SetD(2).PosX, SetD(2).PosY, TFA.Serie & "-" & Format$(TFA.Factura, "000000000")
//       End If
//       If SetD(8).PosX > 0 And SetD(8).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(8).Tamaño
//          If TFA.Razon_Social = TFA.Cliente Then Cadena = TFA.Cliente Else Cadena = TFA.Razon_Social
//          cPrint.printTexto SetD(8).PosX, SetD(8).PosY, Cadena
//       End If
//       If SetD(64).PosX > 0 And SetD(64).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(64).Tamaño
//          If TFA.Razon_Social <> TFA.Cliente Then
//             cPrint.printTexto SetD(64).PosX, SetD(64).PosY, TFA.Cliente
//          End If
//       End If
//       If SetD(11).PosX > 0 And SetD(11).PosY > 0 Then
//          DireccionCli = TFA.DireccionC
//          If Len(TFA.DirNumero) > 1 And TFA.DirNumero <> Ninguno Then
//             If TFA.DirNumero <> "S/N" Then DireccionCli = DireccionCli & " (" & TFA.DirNumero & ")"
//          End If
//          cPrint.PorteDeLetra = SetD(11).Tamaño
//          cPrint.printTexto SetD(11).PosX, SetD(11).PosY, DireccionCli
//       End If
//       If SetD(10).PosX > 0 And SetD(10).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(10).Tamaño
//          cPrint.printTexto SetD(10).PosX, SetD(10).PosY, TFA.Grupo
//       End If
//      'Codigo abreviado del Usuario
//       If SetD(18).PosX > 0 And SetD(18).PosY > 0 Then
//          NombUusuario = TFA.Digitador
//          Cadena = Cambio_Usuario_Inicial(NombUusuario)
//          cPrint.PorteDeLetra = SetD(18).Tamaño
//          cPrint.printTexto SetD(18).PosX, SetD(18).PosY, Cadena
//       End If
//       If SetD(21).PosX > 0 And SetD(21).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(21).Tamaño
//          cPrint.printTexto SetD(21).PosX, SetD(21).PosY, TFA.Ejecutivo_Venta
//       End If
//       If SetD(3).PosX > 0 And SetD(3).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(3).Tamaño
//          cPrint.printTexto SetD(3).PosX, SetD(3).PosY, FechaStrgCorta(TFA.Fecha)
//       End If
//       If SetD(4).PosX > 0 And SetD(4).PosY > 0 Then
//          Cadena = FechaDia(TFA.Fecha) & Space(10) & FechaMes(TFA.Fecha) & Space(10) & FechaAnio(TFA.Fecha)
//          cPrint.PorteDeLetra = SetD(4).Tamaño
//          cPrint.printTexto SetD(4).PosX, SetD(4).PosY, Cadena
//       End If
//       If SetD(7).PosX > 0 And SetD(7).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(7).Tamaño
//          cPrint.printTexto SetD(7).PosX, SetD(7).PosY, FechaStrgCiudad(TFA.Fecha)
//       End If
//       If SetD(5).PosX > 0 And SetD(5).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(5).Tamaño
//          cPrint.printTexto SetD(5).PosX, SetD(5).PosY, FechaStrgCorta(TFA.Fecha_V)
//       End If
//       If SetD(6).PosX > 0 And SetD(6).PosY > 0 Then
//          Cadena = FechaDia(TFA.Fecha_V) & Space(10) & FechaMes(TFA.Fecha_V) & Space(10) & FechaAnio(TFA.Fecha_V)
//          cPrint.PorteDeLetra = SetD(6).Tamaño
//          cPrint.printTexto SetD(6).PosX, SetD(6).PosY, Cadena
//       End If
//       If SetD(14).PosX > 0 And SetD(14).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(14).Tamaño
//          cPrint.printTexto SetD(14).PosX, SetD(14).PosY, TFA.TelefonoC
//       End If
//       If SetD(12).PosX > 0 And SetD(12).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(12).Tamaño
//          cPrint.printTexto SetD(12).PosX, SetD(12).PosY, TFA.CiudadC
//       End If
//       If SetD(13).PosX > 0 And SetD(13).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(13).Tamaño
//          cPrint.printTexto SetD(13).PosX, SetD(13).PosY, TFA.CI_RUC
//       End If
//       If SetD(15).PosX > 0 And SetD(15).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(15).Tamaño
//          cPrint.printTexto SetD(15).PosX, SetD(15).PosY, TFA.EmailC
//       End If
//       If SetD(51).PosX > 0 And SetD(71).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(51).Tamaño
//          cPrint.printTexto SetD(51).PosX, SetD(51).PosY, TFA.DAU
//       End If
//       If SetD(52).PosX > 0 And SetD(52).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(52).Tamaño
//          cPrint.printTexto SetD(52).PosX, SetD(52).PosY, TFA.FUE
//       End If
//       If SetD(53).PosX > 0 And SetD(53).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(53).Tamaño
//          cPrint.printTexto SetD(53).PosX, SetD(53).PosY, TFA.Declaracion
//       End If
//       If SetD(54).PosX > 0 And SetD(54).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(54).Tamaño
//          cPrint.printTexto SetD(54).PosX, SetD(54).PosY, TFA.Remision
//       End If
//       If SetD(55).PosX > 0 And SetD(55).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(55).Tamaño
//          cPrint.printTexto SetD(55).PosX, SetD(55).PosY, TFA.Comercial
//       End If
//       If SetD(56).PosX > 0 And SetD(56).PosY > 0 Then
//         cPrint.PorteDeLetra = SetD(56).Tamaño
//         cPrint.printTexto SetD(56).PosX, SetD(56).PosY, TFA.Solicitud
//       End If
//       If SetD(57).PosX > 0 And SetD(57).PosY > 0 Then
//         cPrint.PorteDeLetra = SetD(57).Tamaño
//         cPrint.printTexto SetD(57).PosX, SetD(57).PosY, TFA.Cantidad
//       End If
//       If SetD(58).PosX > 0 And SetD(58).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(58).Tamaño
//          cPrint.printTexto SetD(58).PosX, SetD(58).PosY, TFA.Kilos
//       End If
//       If SetD(60).PosX > 0 And SetD(60).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(60).Tamaño
//          cPrint.printTexto SetD(60).PosX, SetD(60).PosY, Format$(Day(TFA.Fecha), "00")
//       End If
//       If SetD(61).PosX > 0 And SetD(61).PosY > 0 Then
//          Cadena = UCaseStrg(MidStrg(MesesLetras(CInt(Month(TFA.Fecha))), 1, 3))
//          cPrint.PorteDeLetra = SetD(61).Tamaño
//          cPrint.printTexto SetD(61).PosX, SetD(61).PosY, Cadena
//       End If
//       If SetD(62).PosX > 0 And SetD(62).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(62).Tamaño
//          cPrint.printTexto SetD(62).PosX, SetD(62).PosY, Format$(Year(TFA.Fecha), "0000")
//       End If
//       If SetD(67).PosX > 0 And SetD(67).PosY > 0 Then
//         cPrint.PorteDeLetra = SetD(67).Tamaño
//         cPrint.printTexto SetD(67).PosX, SetD(67).PosY, FechaStrgCorta(TFA.Fecha_C)
//       End If
//       If SetD(16).PosX > 0 And SetD(16).PosY > 0 Then
//         cPrint.PorteDeLetra = SetD(16).Tamaño
//         NumeroLineas = cPrint.printTextoMultiple(SetD(16).PosX, SetD(16).PosY, TFA.Observacion, SetD(26).PosX)
//       End If
//       If SetD(17).PosX > 0 And SetD(17).PosY > 0 Then
//         cPrint.PorteDeLetra = SetD(17).Tamaño
//         NumeroLineas = cPrint.printTextoMultiple(SetD(17).PosX, SetD(17).PosY, TFA.Nota, SetD(26).PosX)
//       End If
//      '--------------------------------------------------------------------------------------------------------
//      'Pie de factura
//      '--------------------------------------------------------------------------------------------------------
//       cPrint.tipoNegrilla = True
//       Total = Redondear(TFA.Total_MN, 2)
//       Total_ME = Redondear(TFA.Total_ME, 2)
//      'Porcentaje Con IVA
//       If SetD(39).PosX > 0 And SetD(39).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(39).Tamaño
//          cPrint.printTexto SetD(39).PosX, SetD(39).PosY, CStr(TFA.Porc_IVA * 100)
//       End If
//      'Sin IVA
//       If SetD(37).PosX > 0 And SetD(37).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(37).Tamaño
//          Diferencia = TFA.Sin_IVA - TFA.Descuento_0
//          cPrint.printVariable SetD(37).PosX, SetD(37).PosY, Diferencia
//       End If
//      'Con IVA
//       If SetD(38).PosX > 0 And SetD(38).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(38).Tamaño
//          Diferencia = TFA.Con_IVA - TFA.Descuento_X
//          cPrint.printVariable SetD(38).PosX, SetD(38).PosY, Diferencia
//       End If
//      'Descuento palabra
//       If SetD(43).PosX > 0 And SetD(43).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(43).Tamaño
//          cPrint.printTexto SetD(43).PosX, SetD(43).PosY, "Descuento"
//       End If
//      'Total Descuento
//       If SetD(42).PosX > 0 And SetD(42).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(42).Tamaño
//          cPrint.printVariable SetD(42).PosX, SetD(42).PosY, TFA.Total_Descuento
//       End If
//      'Total Comision
//       If SetD(48).PosX > 0 And SetD(48).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(48).Tamaño
//          cPrint.printVariable SetD(48).PosX, SetD(48).PosY, TFA.Comision
//       End If
//      'Total Servicio
//       If SetD(49).PosX > 0 And SetD(49).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(49).Tamaño
//          cPrint.printVariable SetD(49).PosX, SetD(49).PosY, TFA.Servicio
//       End If
//      'IVA Porcentaje
//       If SetD(41).PosX > 0 And SetD(41).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(41).Tamaño
//          cPrint.printTexto SetD(41).PosX, SetD(41).PosY, CStr(TFA.Porc_IVA * 100) & " "
//       End If
//      'Total IVA
//       If SetD(40).PosX > 0 And SetD(40).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(40).Tamaño
//          cPrint.printVariable SetD(40).PosX, SetD(40).PosY, TFA.Total_IVA
//       End If
//      'SubTotal
//       If SetD(36).PosX > 0 And SetD(36).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(36).Tamaño
//          cPrint.printVariable SetD(36).PosX, SetD(36).PosY, TFA.SubTotal
//       End If
//      'SubTotal - Descuentos
//       If SetD(66).PosX > 0 And SetD(66).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(66).Tamaño
//          cPrint.printVariable SetD(66).PosX, SetD(66).PosY, TFA.SubTotal - TFA.Total_Descuento
//       End If
//      'Total Facturado
//       If SetD(44).PosX > 0 And SetD(44).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(44).Tamaño
//          cPrint.printVariable SetD(44).PosX, SetD(44).PosY, TFA.Total_MN
//       End If
//      'Total Facturado en letras
//       If SetD(45).PosX > 0 And SetD(45).PosY > 0 Then
//          Numero_Letras = Cambio_Letras(TFA.Total_MN, 2)
//          cPrint.PorteDeLetra = SetD(45).Tamaño
//          PrinterLineas SetD(45).PosX, SetD(45).PosY, Numero_Letras, 11.5
//       End If
//      'CxC Clientes: Linea de Produccion
//       If SetD(50).PosX > 0 And SetD(50).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(50).Tamaño
//          cPrint.printTexto SetD(50).PosX, SetD(50).PosY, TFA.CxC_Clientes
//       End If
//      'Hora de Proceso
//       If SetD(63).PosX > 0 And SetD(63).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(63).Tamaño
//          cPrint.printTexto SetD(63).PosX, SetD(63).PosY, TFA.Hora
//       End If
//       If SetD(68).PosX > 0 And SetD(68).PosY > 0 Then
//          cPrint.PorteDeLetra = SetD(68).Tamaño
//          cPrint.printTexto SetD(68).PosX, SetD(68).PosY, TrimStrg(Orden_No_S)
//       End If
//      'Tipo_Pago
//       If Len(TFA.Tipo_Pago_Det) > 1 Then
//          If SetD(79).PosX > 0 And SetD(79).PosY > 0 Then
//             cPrint.PorteDeLetra = SetD(79).Tamaño
//             cPrint.printVariable SetD(79).PosX, SetD(79).PosY, TFA.Fecha_V
//          End If
//          If SetD(78).PosX > 0 And SetD(78).PosY > 0 Then
//             cPrint.PorteDeLetra = SetD(78).Tamaño
//             cPrint.printVariable SetD(78).PosX, SetD(78).PosY, TFA.Total_MN
//          End If
//          If SetD(77).PosX > 0 And SetD(77).PosY > 0 Then
//             RutaOrigen = RutaSistema & "\ICONOS\Vistofp.jpg"
//             cPrint.printImagen RutaOrigen, SetD(77).PosX, SetD(77).PosY, SetD(77).Tamaño, SetD(77).Tamaño
//          End If
//          If SetD(76).PosX > 0 And SetD(76).PosY > 0 Then
//             cPrint.PorteDeLetra = SetD(76).Tamaño
//             cPrint.printTexto SetD(76).PosX, SetD(76).PosY, "TIPO PAGO:"
//          End If
//          If SetD(75).PosX > 0 And SetD(75).PosY > 0 Then
//             cPrint.PorteDeLetra = SetD(75).Tamaño
//             cPrint.printTexto SetD(75).PosX, SetD(75).PosY, TrimStrg(MidStrg(TFA.Tipo_Pago_Det, 15, Len(TFA.Tipo_Pago_Det)))
//          End If
//       End If
//      'MsgBox Orden_No_S & " ...."
//    End If
//   'Printer.FontName = TipoConsola
//    SaldoInic = 0: SaldoFinal = 0
//    Orden_No_S = ""
//    cPrint.tipoNegrilla = False
//   'Comenzamos a recoger los detalles de la factura
//    sSQL = "SELECT DF.*,CP.Detalle,CP.Codigo_Barra,CP.Unidad,CP.Reg_Sanitario,CM.Marca " _
//         & "FROM Detalle_Factura As DF,Catalogo_Productos As CP,Catalogo_Marcas As CM " _
//         & "WHERE DF.Factura = " & TFA.Factura & " " _
//         & "AND DF.TC = '" & TFA.TC & "' " _
//         & "AND DF.Serie = '" & TFA.Serie & "' " _
//         & "AND DF.Item = '" & NumEmpresa & "' " _
//         & "AND DF.Periodo = '" & Periodo_Contable & "' " _
//         & "AND DF.Periodo = CP.Periodo " _
//         & "AND DF.Periodo = CM.Periodo " _
//         & "AND DF.Item = CP.Item " _
//         & "AND DF.Item = CM.Item " _
//         & "AND DF.Codigo = CP.Codigo_Inv " _
//         & "AND DF.CodMarca = CM.CodMar " _
//         & "ORDER BY DF.ID,DF.Codigo "
//    Select_AdoDB AdoDBDetalle, sSQL
//    With AdoDBDetalle
//     If .RecordCount > 0 Then
//         PFil = SetD(22).PosY
//         Orden_No = .Fields("Orden_No")
//         Do While (Not .EOF)
//           'MsgBox .RecordCount
//            SaldoInic = SaldoInic + .Fields("Cantidad")
//            SaldoFinal = SaldoFinal + .Fields("Tonelaje")
//            cPrint.PorteDeLetra = SetD(23).Tamaño
//            cPrint.printFields SetD(23).PosX, PFil, .Fields("Codigo")
//            cPrint.PorteDeLetra = SetD(30).Tamaño
//            cPrint.printFields SetD(30).PosX, PFil, .Fields("Codigo_Barra")
//            If .Fields("CodMarca") <> Ninguno Then
//                cPrint.PorteDeLetra = SetD(31).Tamaño
//                cPrint.printTexto SetD(31).PosX, PFil, "(" & TrimStrg(MidStrg(.Fields("Marca"), 1, 3)) & ")"
//            End If
//            cPrint.PorteDeLetra = SetD(33).Tamaño
//            cPrint.printFields SetD(33).PosX, PFil, .Fields("Ruta")
//            cPrint.PorteDeLetra = SetD(32).Tamaño
//            cPrint.printFields SetD(32).PosX, PFil, .Fields("Unidad")
//            If .Fields("Cant_Hab") > 0 And Len(.Fields("Tipo_Hab")) > 1 Then
//                cPrint.PorteDeLetra = SetD(70).Tamaño
//                cPrint.printFields SetD(70).PosX, PFil, .Fields("Fecha_IN")
//                cPrint.PorteDeLetra = SetD(71).Tamaño
//                cPrint.printFields SetD(71).PosX, PFil, .Fields("Fecha_OUT")
//                cPrint.PorteDeLetra = SetD(72).Tamaño
//                cPrint.printFields SetD(72).PosX, PFil, .Fields("Cant_Hab")
//                cPrint.PorteDeLetra = SetD(73).Tamaño
//                cPrint.printFields SetD(73).PosX, PFil, .Fields("Tipo_Hab")
//            End If
//            If SetD(24).PosX <= SetD(25).PosX Then
//               cPrint.PorteDeLetra = SetD(24).Tamaño
//               cPrint.printTexto SetD(24).PosX, PFil, " " & CStr(.Fields("Cantidad"))
//            End If
//            cPrint.PorteDeLetra = SetD(25).Tamaño
//            PFilTemp = PFil
//            PFilT = PrinterLineasTexto(SetD(25).PosX, PFil - 0.2, .Fields("Producto"), SetD(26).PosX)
//            If PVP_Al_Inicio Then PFil = PFilTemp Else PFil = PFilT
//            If Len(.Fields("Lote_No")) > 1 And Len(.Fields("Reg_Sanitario")) > 1 Then
//               PFil = PFil + Printer.TextHeight("H")
//               Cadena = "-LOTE No. " & .Fields("Lote_No") _
//                      & ", REG. SANITARIO: " & .Fields("Reg_Sanitario")
//               'cPrint.printTexto SetD(25).PosX, PFil, Cadena
//               PFilT = PrinterLineasTexto(SetD(25).PosX, PFil, Cadena, SetD(26).PosX)
//               PFil = PFil + Printer.TextHeight("H")
//               Cadena = UCaseStrg("-FAB: " & Format(.Fields("Fecha_Fab"), "MMM-yyyy") & ", EXP: " & Format(.Fields("Fecha_Exp"), "MMM-yyyy"))
//               'cPrint.printTexto SetD(25).PosX, PFil, Cadena
//               PFilT = PrinterLineasTexto(SetD(25).PosX, PFil, Cadena, SetD(26).PosX)
//            End If
//            PFil = PFil + 0.2
//            If SetD(24).PosX > SetD(25).PosX Then
//               cPrint.PorteDeLetra = SetD(24).Tamaño
//               cPrint.printTexto SetD(24).PosX, PFil, " " & CStr(.Fields("Cantidad"))
//            End If
//            cPrint.PorteDeLetra = SetD(29).Tamaño
//            cPrint.printFields SetD(29).PosX, PFil, .Fields("Total")
//            PVP_Desc = .Fields("Total") - .Fields("Total_Desc") - .Fields("Total_Desc2")
//            cPrint.PorteDeLetra = SetD(69).Tamaño
//            cPrint.printVariable SetD(69).PosX, PFil, PVP_Desc
//            cPrint.PorteDeLetra = SetD(28).Tamaño
//            cPrint.printFields SetD(28).PosX, PFil, .Fields("Precio"), , , , Dec_PVP
//            cPrint.PorteDeLetra = SetD(27).Tamaño
//            If .Fields("Precio") > 0 And .Fields("Cantidad") > 0 Then
//                cPrint.printTexto SetD(27).PosX, PFil, Format$(.Fields("Total_Desc") / (.Fields("Cantidad") * .Fields("Precio")), "00.00%")
//            End If
//            cPrint.PorteDeLetra = SetD(34).Tamaño
//            cPrint.printTexto SetD(34).PosX, PFil, Format$(.Fields("Tonelaje"), "#,##0.00")
//           'Descuentos en Ventas
//            If .Fields("Total_IVA") > 0 Then
//                Desc_Con_IVA = Desc_Con_IVA + .Fields("Total_Desc")
//            Else
//                Desc_Sin_IVA = Desc_Sin_IVA + .Fields("Total_Desc")
//            End If
//            If PVP_Al_Inicio Then PFil = PFilT
//            PFil = PFil + Printer.TextHeight("H")
//            If Orden_No <> TFA.Orden_Compra And TFA.Orden_Compra > 0 Then
//               Orden_No_S = Orden_No_S & CStr(Orden_No) & " "
//               Orden_No = TFA.Orden_Compra
//            End If
//           .MoveNext
//         Loop
//         Orden_No_S = Orden_No_S & CStr(Orden_No) & " "
//     End If
//    End With
//    cPrint.finalizaImpresion
// End If
// 'Printer.FontName = LetraAnterior
// MensajeEncabData = ""
// RatonNormal
// Exit Sub
// Errorhandler:
//     RatonNormal
//     ErrorDeImpresion
//     Exit Sub
// End Sub
}

function  Imprimir_Punto_Venta_Grafico_datos($TFA)
{

  // print_r($TFA);die();
   $conn = new db();
   $ContEspec = Leer_Campo_Empresa("Codigo_Contribuyente_Especial");
   $Obligado_Conta = Leer_Campo_Empresa("Obligado_Conta");
   $SetNombrePRN = Leer_Campo_Empresa("Impresora_Defecto");
   $Ambiente = '';
  
   // $SubTotal = 0: $Total = 0: $Total_IVA = 0: $Total_Desc = 0: $Cant_Ln = 0
   $sql='';
   if($TFA['TC'] == "PV"){
         $sql = "SELECT F.*,C.Cliente,C.CI_RUC,C.Telefono,C.Direccion,C.Ciudad,C.Grupo,C.Email 
           FROM Trans_Ticket As F,Clientes As C 
           WHERE F.Ticket = ".$TFA['Factura']." 
           AND F.TC = '".$TFA['TC']."' 
           AND F.Periodo = '".$_SESSION['INGRESO']['periodo']. "' 
           AND F.Item = '".$_SESSION['INGRESO']['item']."' 
           AND C.Codigo = F.CodigoC ";
   }else{
         $sql = "SELECT F.*,C.Cliente,C.CI_RUC,C.Telefono,C.Direccion,C.Ciudad,C.Grupo,C.Email
           FROM Facturas As F,Clientes As C
           WHERE F.Factura = ".$TFA['Factura']."
           AND F.TC = '".$TFA['TC']."'
           AND F.Serie = '".$TFA['Serie']."'
           AND F.Periodo = '".$_SESSION['INGRESO']['periodo']. "'
           AND F.Item = '".$_SESSION['INGRESO']['item']."'
           AND C.Codigo = F.CodigoC ";
   }

   // print_r($sql);
   $datos = $conn->datos($sql);
   // print_r($datos);die();
   if(is_numeric($TFA['Autorizacion'])){
      $Ambiente = substr($datos[0]['Clave_Acceso'], 23, 1);
     // 'Generacion Codigo de Barras
      // PathCodigoBarra = RutaSysBases & "\TEMP" & TFA.ClaveAcceso & ".jpg";
  }


 // 'Datos Iniciales
 // 'Comenzamos a recoger los detalles de la factura
  if($TFA['TC'] == "PV"){
     $sql = "SELECT DF.*,CP.Detalle,CP.Codigo_Barra
          FROM Trans_Ticket As DF,Catalogo_Productos As CP
          WHERE DF.Ticket = ".$TFA['Factura']."
          AND DF.TC = '".$TFA['TC']."'
          AND DF.Item = '".$_SESSION['INGRESO']['item']."'
          AND DF.Periodo = '".$_SESSION['INGRESO']['periodo']. "'
          AND DF.Item = CP.Item
          AND DF.Periodo = CP.Periodo
          AND DF.Codigo_Inv = CP.Codigo_Inv
          ORDER BY DF.ID ";
  }else{
     $sql = "SELECT DF.*,CP.Detalle,CP.Codigo_Barra 
        FROM Detalle_Factura As DF,Catalogo_Productos As CP 
        WHERE DF.Factura = ".$TFA['Factura']." 
        AND DF.TC = '".$TFA['TC']."' 
        AND DF.Serie = '".$TFA['Serie']."' 
        AND DF.Item = '".$_SESSION['INGRESO']['item']."' 
        AND DF.Periodo = '".$_SESSION['INGRESO']['periodo']. "' 
        AND DF.Item = CP.Item 
        AND DF.Periodo = CP.Periodo 
        AND DF.Codigo = CP.Codigo_Inv 
        ORDER BY DF.ID ";
  }
  $datos1 = $conn->datos($sql);

  return array('factura'=>$datos,'lineas'=>$datos1,'especial'=>$ContEspec,'conta'=>$Obligado_Conta,'ambiente'=>$Ambiente);

  // print_r($datos);
  // print_r($datos1);
  // print_r($ContEspec);
  // print_r($Obligado_Conta);
  // print_r($Ambiente);
  // die();
  
 
}

function CalculosSaldoAnt($TipoCod,$TDebe,$THaber,$TSaldo)
{

// print_r(substr($TipoCod ,1,1));die();
  // print_r($TipoCod);die();
  $OpcCoop = false;
  $TotSaldoAnt = 0;
  if($OpcCoop){
    switch (substr($TipoCod ,0,1)) {
      case '1':
      case '4':
      case '6':
      case '8':
         $TotSaldoAnt = number_format($TSaldo - $TDebe + $THaber, 2,'.','');
        break;      
      case '2':
      case '3':
      case '5':
      case '6':
      case '9':
          $TotSaldoAnt = number_format($TSaldo - $THaber + $TDebe, 2,'.','');
        break;
    }
    
  }else{

     switch (substr($TipoCod ,0,1)) {
      case '1':
      case '5':
      case '7':
      case '9':
         $TotSaldoAnt = number_format($TSaldo - $TDebe + $THaber, 2,'.','');
        break;      
      case '2':
      case '3':
      case '4':
      case '6':
      case '8':
           $TotSaldoAnt = number_format($TSaldo - $THaber + $TDebe, 2,'.','');
        break;
    }
  }
  return $TotSaldoAnt;
}

function medida_pantalla($medida)
{
  /* Extra small */
  if($medida<=600)
  {
    return '100';

  }
  /*small */
  if($medida>600 && $medida<768)
  {
    return '220';
  }

  /* medium */
  if($medida>=768 && $medida<992)
  {
    $medium = '484';
    if($medida>=800 && $medida<=900)
    {
      $medium = '600';
    }
    if($medida>900 && $medida<=992)
    {
      $medium = '600';
    }
    return $medium;

  }

  /* large */
  if($medida>=992)
  {

    return '600';
  }

}

function  AddNewCta($TipoTC,$Codigo,$Detalle)
{

// Dim SubInd As Integer
// Dim inserteKey As Boolean
  $h = '';
  if(strlen($Codigo) == 1){
       $h.='<li class="file" id="label_'.str_replace('.','_',$Codigo).'" title="Presione Suprimir para eliminar"><a href="">'.$Detalle.'</a></li>';
  }else{
    switch ($TipoTC) {
      case '':
        return 1;
        break;
      case 'A':
        return 2;
        break;
      case 'S':
        return 3;
        break;
      case 'T':
        return 4;
        break;
       case 'D':
        return 5;
        break;
    }     
    $Cta_Sup = CodigoCuentaSup($Codigo);
    $inserteKey = true;
    for ($i=1; $i < 10; $i++) { 
      
    }
     // For SubInd = 1 To TVCatalogo.Nodes.Count
     //     If TVCatalogo.Nodes(SubInd).key = Codigo Then inserteKey = false
     // Next SubInd
     if($inserteKey){
         $h.='<li class="file" id="label_'.str_replace('.','_',$Codigo).'" title="Presione Suprimir para eliminar"><a href="">'.$Detalle.'</a></li>';
        //' MidStrg(Codigo, 2, Len(Codigo))
     }
  }

  return $h;
}

function CambioCodigoCta($Codigo){
  $Bandera = True;
  $LongCta = strlen($Codigo);
   while($LongCta > 0 And $Bandera){
     if (substr($Codigo, $LongCta, 1) <> "." && substr($Codigo, $LongCta, 1) <> " "){ 
      $Bandera = False;
      $LongCta = $LongCta - 1;
    }
  }
  $LongCta = $LongCta + 1;
  if($LongCta < 1){$LongCta = 1;}
  $Codigo_Cta = substr($Codigo, 1, $LongCta);
  if($Codigo_Cta == ""){$Codigo_Cta = "0";}
  if($Codigo_Cta == " "){ $Codigo_Cta = "0";}
  if($Codigo_Cta ==G_NINGUNO){ $Codigo_Cta = "0";}

  print_r($Codigo_Cta);die();
  return $Codigo_Cta;
}

function CambioCodigoCtaSup($Codigo)
{
  $CadAux = ""; 
  $Bandera = True;
  $LongCta = strLen($Codigo);
  While (($LongCta > 0) And $Bandera)
  {
     if (substr($Codigo, $LongCta, 1) == "."){$Bandera = False;}
     $LongCta = $LongCta - 1;
  }
  if($LongCta < 1){ $CadAux = "0"; }else{ $CadAux = substr($Codigo, 1, $LongCta);}
  return  $CadAux;

}



function Validar_Porc_IVA($FechaIVA)
{
  $conn = new db();
   // 'Carga la Tabla de Porcentaje IVA
    $Porc_IVA = 0;
    if($FechaIVA == "00/00/0000"){$FechaIVA = date('Y-m-d');}
      $sql = "SELECT * 
      FROM Tabla_Por_ICE_IVA 
      WHERE IVA <> 0  
      AND Fecha_Inicio <= '".BuscarFecha($FechaIVA)."' 
      AND Fecha_Final >= '".BuscarFecha($FechaIVA)."' 
      ORDER BY Porc DESC ";
      $datos1 = $conn->datos($sql);

      if(count($datos1)>0)
      {
        $Porc_IVA = number_format($datos1[0]["Porc"] / 100,2,'.','');
      }

      return $Porc_IVA;
}

function filtra_datos_unico_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();
   
    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}

function ReadAdoCta($query) 
{  
  $conn = new db();
  $NumCodigo = G_NINGUNO;
  if($query <> "" )
  {
     $sql = "SELECT *
            FROM Ctas_Proceso
            WHERE Item = '" .$_SESSION['INGRESO']['item']."'
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
            AND Detalle = '".$query."' ";
      $datos = $conn->datos($sql); 

     if(count($datos)> 0){$NumCodigo = $datos[0]["Codigo"];}
   }
   return  $NumCodigo;
  }


function variables_tipo_factura()
{
   $FA = array(
     
    'T'=>'.',
    'TC'=>'.',
    'Porc_IVA_S' =>'0',
    'Tipo_PRN'   =>'.',
    'CodigoC'=>'.',
    'CodigoB'=>'.',
    'CodigoA'=>'.',
    'CodigoDr'=>'.',
    'Grupo'=>'.',
    'Curso'=>'.',
    'Cliente'=>'.',
    'Contacto'=>'.',
    'CI_RUC' =>'.', //Solo Clientes
    'TD'=>'.',
    'Razon_Social' =>'.',
    'RUC_CI' =>'.', //Clientes Matriculas
    'TB'=>'.',
    'DireccionC' =>'.',
    'DireccionS' =>'.',
    'CiudadC'=>'.',
    'DirNumero'  =>'.',
    'TelefonoC'  =>'.',
    'EmailC' =>'.',
    'EmailR' =>'.',
    'Forma_Pago' =>'.',
    'Ejecutivo_Venta'  =>'.',
    'Cta_CxP'=>'0',
    'Cta_CxP_Anterior' =>'0',
    'Cta_Venta'  =>'0',
    'Cod_Ejec'   =>'.',
    'Vendedor'   =>'.',
    'Afiliado'   =>'.',
    'Digitador'  =>'.',
    'Nivel'=>'.',
    'Nota'=>'.', 
    'Observacion'=>'.',
    'Definitivo' =>'.',
    'Codigo_T'   =>'.',
    'CodigoU'=>'.',
    'Declaracion'=>'.',
    'SubCta' =>'.',
    'Hora'=>date('H:i:s'),
    'Hora_FA'=>date('H:i:s'),
    'Hora_NC'=>date('H:i:s'),
    'Hora_GR'=>date('H:i:s'),
    'Hora_LC'=>date('H:i:s'),
    'Serie'=>'.',
    'Serie_R'=>'.',
    'Serie_NC'   =>'.',
    'Serie_GR'   =>'.',
    'Serie_LC'   =>'.',
    'Autorizacion' =>'.',
    'Autorizacion_R'   =>'.',
    'Autorizacion_NC'  =>'.',
    'Autorizacion_GR'  =>'.',
    'Autorizacion_LC'  =>'.',
    'Fecha_Tours'=>date('Y-m-d'),
    'ClaveAcceso'=>'.',
    'ClaveAcceso_NC'   =>'.',
    'ClaveAcceso_GR'   =>'.',
    'ClaveAcceso_LC'   =>'.',
    'Fecha'=>date('Y-m-d'),
    'Fecha_C'=>date('Y-m-d'),
    'Fecha_V'=>date('Y-m-d'),
    'Fecha_NC'   =>date('Y-m-d'),
    'Fecha_Aut'  =>date('Y-m-d'),
    'Fecha_Aut_NC' =>date('Y-m-d'),
    'Fecha_Aut_GR' =>date('Y-m-d'),
    'Fecha_Aut_LC' =>date('Y-m-d'),
    'Fecha_Corte'=>date('Y-m-d'),
    'Fecha_Desde'=>date('Y-m-d'),
    'Fecha_Hasta'=>date('Y-m-d'),
    'Vencimiento'=>date('Y-m-d'),
    'FechaGRE'   =>'.',
    'FechaGRI'   =>'.',
    'FechaGRF'   =>'.',
    'CiudadGRI'  =>'.',
    'CiudadGRF'  =>'.',
    'Comercial'  =>'.',
    'CIRUCComercial'   =>'.',
    'Entrega'=>'.',
    'CIRUCEntrega' =>'.',
    'Dir_PartidaGR'=>'.',
    'Dir_EntregaGR'=>'.',
    'Pedido' =>'.',
    'Zona'=>'.',
    'Placa_Vehiculo'=>'.',
    'Error_SRI'  =>'.',
    'Estado_SRI' =>'.',
    'Estado_SRI_NC'=>'.',
    'Estado_SRI_GR'=>'.',
    'Estado_SRI_LC'=>'.',
    'Lugar_Entrega'=>'.',
    'DireccionEstab'   =>'.',
    'NombreEstab'=>'.',
    'TelefonoEstab'=>'.',
    'LogoTipoEstab'=>'.',
    'TP'=>'.',
    'Tipo_Pago'  =>'.',
    'Tipo_Pago_Det'=>'.',
    'Tipo_Comp'  =>'.',
    'Cod_CxC'=>'.',
    'CxC_Clientes' =>'.',
    'LogoFactura'=>'.',
    'LogoNotaCredito'  =>'.',
    'PDF_ClaveAcceso'  =>'.',

    'C'=>'0',   
    'p'=>'0',
    'SP'=>'0',  
    'ME_'=>'0', 
    'Com_Pag'=>'0',
    'Educativo'  =>'0',
    'Imp_Mes'=>'0',
    'Si_Existe_Doc'=>'0',
    'Nuevo_Doc'  =>'0',
    'EsPorReembolso' =>'0',
    
    'Gavetas' =>'0',
    
    'CantFact' =>'.',
    
    'Factura'=>'.',
    'Desde'=>'.',
    'Hasta'=>'.',
    'DAU'=>'.',
    'FUE'=>'.',
    'Remision'   =>'0',
    'Solicitud'  =>'.',
    'Retencion'  =>'.',
    'Nota_Credito' =>'.',
    'Numero' =>'.',
    'Orden_Compra' =>'.',
    
    'Porc_C' =>'0',
    'Cotizacion' =>'0',
    'Porc_NC'=>'.',
    'Porc_IVA'   =>'0',
    'AltoFactura'=>'.',
    'AnchoFactura' =>'.',
    'EspacioFactura'   =>'.',
    'Pos_Factura'=>'.',
    'Pos_Copia'  =>'.',
    
    'SubTotal' =>'0',
    'SubTotal_NC'=>'0',
    'SubTotal_NCX'=>'0',
    'Sin_IVA'=>'0',
    'Con_IVA'=>'0',
    'Total_Sin_No_IVA'=>'0',
    'Total_Descuento'=>'0',
    'Total_IVA'  =>'0',
    'Total_IVA_NC'=>'0',
    'Total_Abonos'=>'0',
    'Descuento'  =>'0',
    'Descuento2' =>'0',
    'Descuento_0'=>'0',
    'Descuento_X'=>'0',
    'Descuento_NC'=>'0',
    'Comision'   =>'0',
    'Servicio'   =>'0',
    'Propina'    =>'0',
    'Total_MN'   =>'0',
    'Total_ME'   =>'0',
    'Saldo_MN'   =>'0',
    'Saldo_ME'   =>'0',
    'Cantidad'   =>'0',
    'Kilos'=>'0',
    'Saldo_Actual'=>'0',
    'Efectivo' =>'0',
    'Saldo_Pend' =>'0',
    'Saldo_Pend_MN' =>'0',
    'Saldo_Pend_ME' =>'0',
    'Ret_Fuente'=>'.',
    'Ret_IVA'=>'.',
    );

   return $FA;
}


function datos_Co()
{
    $Co['Item'] = $_SESSION['INGRESO']['item'];
    $Co['RetNueva'] = True;
    $Co['Ctas_Modificar'] = "";
    $Co['TipoContribuyente'] = "";
    $Co['RUC_CI'] = G_NINGUNO;
    $Co['CodigoB'] = G_NINGUNO;
    $Co['Beneficiario'] = G_NINGUNO;
    $Co['Email'] = G_NINGUNO;
    $Co['TD'] = G_NINGUNO;
    $Co['Direccion'] = G_NINGUNO;
    $Co['Telefono'] = G_NINGUNO;
    $Co['Grupo'] = G_NINGUNO;
    $Co['AgenteRetencion'] = G_NINGUNO;
    $Co['MicroEmpresa'] = G_NINGUNO;
    $Co['Estado'] = G_NINGUNO;
    $Co['Autorizacion_LC'] = G_NINGUNO;
    $Co['Autorizacion_R'] = G_NINGUNO;
    $Co['Autorizado'] = G_NINGUNO;
    $Co['Serie_R'] = G_NINGUNO;
    $Co['Retencion'] = G_NINGUNO;
    $Co['Cotizacion'] = 0;
    $Co['Concepto'] = "";
    $Co['Efectivo'] = 0;
    $Co['Total_Banco'] = 0;
    $Co['Monto_Total'] = 0;

    return $Co;
}


function Leer_Datos_FA_NV($TFA)
{
    $conn = new db();
    $TFA['Fecha_Aut_GR'] = date('Y-m-d');
    $TFA['Hora_GR'] = date('H:i:s');
    $TFA['Estado_SRI_GR'] = G_NINGUNO;
    $TFA['Serie_GR'] = G_NINGUNO;
    $TFA['ClaveAcceso_GR'] = G_NINGUNO;
    $TFA['Autorizacion_GR'] = G_NINGUNO;
    $TFA['Vendedor'] = G_NINGUNO;
    $TFA['Remision'] = 0;
    $TFA['Comercial'] = G_NINGUNO;
    $TFA['CIRUCComercial'] = G_NINGUNO;
    $TFA['CIRUCEntrega'] = G_NINGUNO;
    $TFA['Entrega'] = G_NINGUNO;
    $TFA['CiudadGRI'] = G_NINGUNO;
    $TFA['CiudadGRF'] = G_NINGUNO;
    $TFA['Serie_GR'] = G_NINGUNO;
    $TFA['FechaGRE'] = date('Y-m-d');
    $TFA['FechaGRI'] = date('Y-m-d');
    $TFA['FechaGRF'] = date('Y-m-d');
    $TFA['Pedido'] = G_NINGUNO;
    $TFA['Zona'] = G_NINGUNO;
    $TFA['Orden_Compra'] = 0;
    $TFA['Placa_Vehiculo'] = G_NINGUNO;
    $TFA['Lugar_Entrega'] = G_NINGUNO;
    $TFA['Descuento_X'] = 0;
    $TFA['Descuento_0'] = 0;
    $TFA['Gavetas'] = 0;
    $TFA['Servicio'] = 0;
    $TFA['EsPorReembolso'] = False;

    $sql = "SELECT F.*,C.Cliente,C.CI_RUC,C.TD,C.Grupo,C.Direccion,C.DireccionT,C.Celular,C.Codigo,C.Ciudad,C.Email,C.Email2,C.EmailR,
        C.Contacto,C.DirNumero,C.Fecha As Fecha_C 
        FROM Facturas As F, Clientes As C 
        WHERE F.Item = '".$_SESSION['INGRESO']['item']."' 
        AND F.Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        AND F.TC = '".$TFA['TC']. "' 
        AND F.Serie = '".$TFA['Serie']. "' 
        AND F.Autorizacion = '".$TFA['Autorizacion']. "' 
        AND F.Factura = ".$TFA['Factura']." 
        AND C.Codigo = F.CodigoC ";
    $AdoDBFac = $conn->datos($sql);
    // print_r($AdoDBFac);die();
    if(count($AdoDBFac) > 0)
    {
    
        // 'Datos del SRI
         $TFA['Si_Existe_Doc'] = True;
         $TFA['T'] = $AdoDBFac[0]["T"];
         $TFA['SP'] = $AdoDBFac[0]["SP"];
         $TFA['Porc_IVA'] = $AdoDBFac[0]["Porc_IVA"];
         $TFA['Porc_IVA_S'] = $AdoDBFac[0]["Porc_IVA"] * 100;
         $TFA['Cta_CxP'] = $AdoDBFac[0]["Cta_CxP"];
         $TFA['Cod_CxC'] = $AdoDBFac[0]["Cod_CxC"];
         $TFA['Estado_SRI'] = $AdoDBFac[0]["Estado_SRI"];
         $TFA['Error_SRI'] = $AdoDBFac[0]["Error_FA_SRI"];
         $TFA['ClaveAcceso'] = $AdoDBFac[0]["Clave_Acceso"];
         $TFA['CodigoU'] = $AdoDBFac[0]["CodigoU"];
                
        // 'Encabezado de Facturas
         $TFA['CodigoC'] = $AdoDBFac[0]["CodigoC"];
         $TFA['Contacto'] = $AdoDBFac[0]["Contacto"];
         $TFA['Cliente'] = $AdoDBFac[0]["Cliente"];
         $TFA['CI_RUC'] = $AdoDBFac[0]["CI_RUC"];
         $TFA['TD'] = $AdoDBFac[0]["TD"];
         $TFA['Razon_Social'] = $AdoDBFac[0]["Razon_Social"];
         $TFA['RUC_CI'] = $AdoDBFac[0]["RUC_CI"];
         $TFA['TB'] = $AdoDBFac[0]["TB"];
         $TFA['DireccionC'] = $AdoDBFac[0]["Direccion_RS"];
         $TFA['TelefonoC'] = $AdoDBFac[0]["Telefono_RS"];
         $TFA['DirNumero'] = $AdoDBFac[0]["DirNumero"];
         $TFA['Curso'] = $AdoDBFac[0]["Direccion"];
         $TFA['CiudadC'] = $AdoDBFac[0]["Ciudad"];
         $TFA['Grupo'] = $AdoDBFac[0]["Grupo"];
         $TFA['Cod_Ejec'] = $AdoDBFac[0]["Cod_Ejec"];
         $TFA['Imp_Mes'] = $AdoDBFac[0]["Imp_Mes"];
         $TFA['Fecha'] = $AdoDBFac[0]["Fecha"];
         $TFA['Fecha_V'] = $AdoDBFac[0]["Fecha_V"];
         $TFA['Fecha_C'] = $AdoDBFac[0]["Fecha_C"];
         $TFA['Fecha_Aut'] = $AdoDBFac[0]["Fecha_Aut"];
         $TFA['Hora'] = $AdoDBFac[0]["Hora"];
         $TFA['Tipo_Pago'] = $AdoDBFac[0]["Tipo_Pago"];
         $TFA['EmailC'] = $AdoDBFac[0]["Email"];
         $TFA['EmailR'] = $AdoDBFac[0]["EmailR"];
         $TFA['Observacion'] = $AdoDBFac[0]["Observacion"];
         $TFA['Nota'] = $AdoDBFac[0]["Nota"];
         $TFA['Orden_Compra'] = $AdoDBFac[0]["Orden_Compra"];
         $TFA['Gavetas'] = $AdoDBFac[0]["Gavetas"];
         if($TFA['EmailR'] == G_NINGUNO){
            //$TFA['EmailR'] = $_SESSION['INGRESO']['Email_Procesos'];
         }

        // 'SubTotales de la Factura
         $TFA['Descuento'] = $AdoDBFac[0]["Descuento"];
         $TFA['Descuento2'] = $AdoDBFac[0]["Descuento2"];
         $TFA['Descuento_0'] = $AdoDBFac[0]["Desc_0"];
         $TFA['Descuento_X'] = $AdoDBFac[0]["Desc_X"];
         $TFA['SubTotal'] = $AdoDBFac[0]["SubTotal"];
         $TFA['Total_IVA'] = $AdoDBFac[0]["IVA"];
         $TFA['Con_IVA'] = $AdoDBFac[0]["Con_IVA"];
         $TFA['Sin_IVA'] = $AdoDBFac[0]["Sin_IVA"];
         $TFA['Servicio'] = $AdoDBFac[0]["Servicio"];
         $TFA['Total_MN'] = $AdoDBFac[0]["Total_MN"];
         $TFA['Saldo_MN'] = $AdoDBFac[0]["Saldo_MN"];
         $TFA['Saldo_Actual'] = $AdoDBFac[0]["Saldo_MN"];
         $TFA['Total_Descuento'] = $TFA['Descuento'] + $TFA['Descuento2'];
    }
            
         $sql = "SELECT *
         FROM Facturas_Auxiliares
         WHERE Item = '".$_SESSION['INGRESO']['item']."' 
         AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
         AND Remision <> 0
         AND TC = '".$TFA['TC']."'
         AND Serie = '".$TFA['Serie']."'
         AND Autorizacion = '".$TFA['Autorizacion']."'
         AND Factura = ".$TFA['Factura']." ";

         $AdoDBFac = $conn->datos($sql);

         if(count($AdoDBFac) > 0)
         {
            // 'Guia de Remision
             $TFA['Fecha_Aut_GR'] = $AdoDBFac[0]["Fecha_Aut_GR"];
             $TFA['Hora_GR'] = $AdoDBFac[0]["Hora_Aut_GR"];
             $TFA['Estado_SRI_GR'] = $AdoDBFac[0]["Estado_SRI_GR"];
             $TFA['Serie_GR'] = $AdoDBFac[0]["Serie_GR"];
             $TFA['ClaveAcceso_GR ']= $AdoDBFac[0]["Clave_Acceso_GR"];
             $TFA['Autorizacion_GR'] = $AdoDBFac[0]["Autorizacion_GR"];
             $TFA['Remision'] = $AdoDBFac[0]["Remision"];
             $TFA['Comercial'] = $AdoDBFac[0]["Comercial"];
             $TFA['CIRUCComercial'] = $AdoDBFac[0]["CIRUC_Comercial"];
             $TFA['CIRUCEntrega ']= $AdoDBFac[0]["CIRUC_Entrega"];
             $TFA['Entrega'] = $AdoDBFac[0]["Entrega"];
             $TFA['CiudadGRI'] = $AdoDBFac[0]["CiudadGRI"];
             $TFA['CiudadGRF'] = $AdoDBFac[0]["CiudadGRF"];
             $TFA['Serie_GR'] = $AdoDBFac[0]["Serie_GR"];
             $TFA['FechaGRE'] = $AdoDBFac[0]["FechaGRE"];
             $TFA['FechaGRI'] = $AdoDBFac[0]["FechaGRI"];
             $TFA['FechaGRF'] = $AdoDBFac[0]["FechaGRF"];
             $TFA['Pedido'] = $AdoDBFac[0]["Pedido"];
             $TFA['Zona'] = $AdoDBFac[0]["Zona"];
             $TFA['Orden_Compra'] = $AdoDBFac[0]["Orden_Compra"];
             $TFA['Placa_Vehiculo'] = $AdoDBFac[0]["Placa_Vehiculo"];
             $TFA['Lugar_Entrega'] = $AdoDBFac[0]["Lugar_Entrega"];
         }
            
          $sql = "SELECT Direccion 
              FROM Clientes 
              WHERE CI_RUC = '".$TFA['CIRUCComercial']."' ";
            $AdoDBFac = $conn->datos($sql);

          if(count($AdoDBFac) > 0){ $TFA['Dir_PartidaGR'] = $AdoDBFac[0]["Direccion"];}
          
    
        $sql = "SELECT Direccion 
            FROM Clientes 
            WHERE CI_RUC = '".$TFA['CIRUCEntrega']."' ";
            $AdoDBFac = $conn->datos($sql);
          if(count($AdoDBFac) > 0){  $TFA['Dir_EntregaGR'] = $AdoDBFac[0]["Direccion"];}
    
        $sql = "SELECT Nombre_Completo
            FROM Accesos
            WHERE Codigo = '".$TFA['Cod_Ejec']."' ";
        $AdoDBFac = $conn->datos($sql);
        if(count($AdoDBFac) > 0){ $TFA['Ejecutivo_Venta'] = $AdoDBFac[0]["Nombre_Completo"];}
    
        $sql = "SELECT Nombre_Completo 
             FROM Accesos 
             WHERE Codigo = '".$TFA['CodigoU']."' ";
         $AdoDBFac = $conn->datos($sql);
         if(count($AdoDBFac) > 0){  $TFA['Digitador'] = $AdoDBFac[0]["Nombre_Completo"];}
       
    
        $sql = "SELECT Descripcion 
            FROM Tabla_Referenciales_SRI 
            WHERE Tipo_Referencia = 'FORMA DE PAGO' 
            AND Codigo = '".$TFA['Tipo_Pago']."' ";
        $AdoDBFac = $conn->datos($sql);
        if(count($AdoDBFac) > 0){  $TFA['Tipo_Pago_Det'] = "Forma de Pago: ".strtoupper($AdoDBFac[0]["Descripcion"]);}
        
    
        $sql = "SELECT * 
            FROM Facturas_Formatos 
            WHERE Item = '".$_SESSION['INGRESO']['item']."' 
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
            AND TC = '".$TFA['TC']."' 
            AND Serie = '".$TFA['Serie']."' 
            AND Cod_CxC = '".$TFA['Cod_CxC']."' 
            AND '".BuscarFecha($TFA['Fecha'])."' BETWEEN Fecha_Inicio and Fecha_Final 
            ORDER BY Cod_CxC ";
           $AdoDBFac = $conn->datos($sql);

          if(count($AdoDBFac) > 0){       
             $TFA['CxC_Clientes'] = $AdoDBFac[0]["Concepto"];
             $TFA['LogoFactura'] = $AdoDBFac[0]["Formato_Factura"];
             $TFA['AltoFactura'] = $AdoDBFac[0]["Largo"];
             $TFA['AnchoFactura'] = $AdoDBFac[0]["Ancho"];
             $TFA['EspacioFactura'] = $AdoDBFac[0]["Espacios"];
             $TFA['Pos_Factura'] = $AdoDBFac[0]["Pos_Factura"];
             $TFA['DireccionEstab'] = $AdoDBFac[0]["Direccion_Establecimiento"];
             $TFA['NombreEstab'] = $AdoDBFac[0]["Nombre_Establecimiento"];
             $TFA['TelefonoEstab'] = $AdoDBFac[0]["Telefono_Estab"];
             $TFA['Vencimiento'] = $AdoDBFac[0]["Fecha_Final"];
             $TFA['CantFact'] = $AdoDBFac[0]["Fact_Pag"];
             $TFA['LogoTipoEstab'] = dirname(__DIR__)."\LOGOS\"".$AdoDBFac[0]["Logo_Tipo_Estab"].".jpg";
          }
    
          $sql = "SELECT Codigo 
               FROM Detalle_Factura 
               WHERE Item = '".$_SESSION['INGRESO']['item']."' 
               AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
               AND Codigo = '99.41' 
               AND TC = '".$TFA['TC']."' 
               AND Serie = '".$TFA['Serie']."' 
               AND Factura = ".$TFA['Factura']." ";
         $AdoDBFac = $conn->datos($sql);
         if(count($AdoDBFac) > 0){  $TFA['EsPorReembolso'] = True;}

         return $TFA;
          

}


function Eliminar_Nulos_SP($NombreTabla)
{
  $conn = new db();
  $parametros = array(
    array(&$NombreTabla, SQLSRV_PARAM_IN),
  );
  $sql = "EXEC sp_Eliminar_Nulos @NombreTabla= ?";
  $res = $conn->ejecutar_procesos_almacenados($sql,$parametros,$tipo=false);
  return $res;
}

function BuscarArchivo_Foto_Estudiante($nombre){
  $imagen_url = BuscarImagen($nombre, __DIR__ ."/../img/img_estudiantes/");
  if($imagen_url==""){
    $imagen_url = BuscarImagen('SINFOTO', __DIR__ ."/../img/img_estudiantes/");
  }
  return $imagen_url;
}

function BuscarImagen($nombre, $ruta)
{
  $imagen ="";
  if (@getimagesize($ruta.$nombre.'.png')) 
  { 
    $imagen = $nombre.'.png';
  }else if (@getimagesize($ruta.$nombre.'.jpg')) 
  { 
    $imagen = $nombre.'.jpg';
  }else if (@getimagesize($ruta.$nombre.'.jpeg')) 
  { 
    $imagen = $nombre.'.jpeg';
  }else if (@getimagesize($ruta.$nombre.'.gif')) 
  { 
    $imagen = $nombre.'.gif'; 
  }
  return $imagen;
}

function CFechaLong($CFecha)
{
  if (strlen($CFecha) == 10) {
    if ($CFecha == "00/00/0000") $CFecha = date('Y-m-d');
  } else {
    $CFecha = date('Y-m-d');
  }
  $CFecha = date('Y-m-d', strtotime($CFecha));
  return strtotime($CFecha);
}

function setObjectOrArray($value)
{
  return json_decode(json_encode($value), false);
}

function valorPorDefectoSegunTipo($tipo)
{
  switch ($tipo) {
    case 'nvarchar':
    case 'ntext':
      $default = G_NINGUNO;
      break;
    case 'int':
    case 'int identity':
    case 'tinyint':
    case 'real':
    case 'bit':
    case 'money':
    case 'float':
    case 'decimal':
    case 'smallint':
    case 'uniqueidentifier':
      $default = "0";
      break;
    case 'date':
      $default = date('Ymd');
      break;
    case 'datetime':
    case 'datetime2':
    case 'datetimeoffset':
    case 'smalldatetime':
      $default = date('YmdHis');
      break;
    default:
      $default = G_NINGUNO;
    break;
  }
  return $default;
}

function Generar_File_SQL($nombreFile, $sqlQuery) {
  $NumFile = 0;
  if (strlen($nombreFile) > 1) {
    $datosFile = $sqlQuery;
    $datosFile = str_replace("FROM", "\nFROM", $datosFile);
    $datosFile = str_replace("WHERE", "\nWHERE", $datosFile);
    $datosFile = str_replace("AND", "\nAND", $datosFile);
    $datosFile = str_replace("OR ", "\nOR ", $datosFile);
    $datosFile = str_replace("SET", "\nSET", $datosFile);
    $datosFile = str_replace("GROUP BY", "\nGROUP BY", $datosFile);
    $datosFile = str_replace("ORDER BY", "\nORDER BY", $datosFile);
    $datosFile = str_replace("HAVING", "\nHAVING", $datosFile);
    $datosFile = str_replace("VALUES", "\nVALUES\n", $datosFile);
    $NumFile = fopen(dirname(__DIR__,2)."/TEMP/" . $nombreFile . ".sql", "w");
    fwrite($NumFile, $datosFile);
    fclose($NumFile);
  }
}

function CompilarSQL($CadSQL) {
  $StrSQL = $CadSQL;
  $Indc = 0;
  $Fecha_SQL = "";
  $Inic_Fecha = false;
  if (isset($_SESSION['INGRESO']['Tipo_Base']) and $_SESSION['INGRESO']['Tipo_Base']=='SQL SERVER') {
      if (strlen($CadSQL) > 0) {
          $StrSQL = "";
          for ($Indc = 0; $Indc < strlen($CadSQL); $Indc++) {
              if (substr($CadSQL, $Indc, 1) != "#") {
                  if (substr($CadSQL, $Indc, 1) == "&") {
                      $StrSQL = $StrSQL . "+";
                  } else {
                      $StrSQL = $StrSQL . substr($CadSQL, $Indc, 1);
                  }
              } elseif (substr($CadSQL, $Indc, 1) == "#") {
                  $StrSQL = $StrSQL . "'";
                  $Inic_Fecha = !$Inic_Fecha;
              }
              if ($Inic_Fecha) {
                  $Fecha_SQL = $Fecha_SQL . substr($CadSQL, $Indc, 1);
              }
          }
      } else {
          $StrSQL = "";
      }
      $CadSQL = $StrSQL;
      if (strtoupper(substr($CadSQL, 0, 6)) == "DELETE" && strlen($CadSQL) > 0) {
          $StrSQL = "";
          for ($Indc = 0; $Indc < strlen($CadSQL); $Indc++) {
              if (substr($CadSQL, $Indc, 1) != "*") {
                  $StrSQL = $StrSQL . substr($CadSQL, $Indc, 1);
              }
          }
      }
      $StrSQL = str_replace("MidStrg(", "SUBSTRING(", $StrSQL);
      $StrSQL = str_replace("UCaseStrg(", "UPPER(", $StrSQL);
      $StrSQL = str_replace("LeftStrg(", "LTRIM(", $StrSQL);
      $StrSQL = str_replace("RightStrg(", "RTRIM(", $StrSQL);
  } else {
      $StrSQL = str_replace("MidStrg(", "MidStrg(", $StrSQL);
      $StrSQL = str_replace("UCaseStrg(", "UCase$(", $StrSQL);
      $StrSQL = str_replace("LeftStrg(", "Ltrim$(", $StrSQL);
      $StrSQL = str_replace("RightStrg(", "RTrim$(", $StrSQL);
  }
  $StrSQL = str_replace("CSTR(", "STR(", $StrSQL);
  $StrSQL = str_replace("CStr(", "STR(", $StrSQL);
  $StrSQL = str_replace("False", "0", $StrSQL);
  $StrSQL = str_replace("True", "1", $StrSQL);
  $StrSQL = str_replace("false", "0", $StrSQL);
  $StrSQL = str_replace("true", "1", $StrSQL);
  if (isset($_SESSION['INGRESO']['Tipo_Base']) and $_SESSION['INGRESO']['Tipo_Base']=='SQL SERVER') {
      $StrSQL = str_replace("#", "'", $StrSQL);
  }
  return $StrSQL;
}

function Ejecutar_SQL_SP($SQL, $NoCompilar = false, $NombreFile ="")
{
  $conn = new db();
  if (!$NoCompilar) {  $SQL = CompilarSQL($SQL);}
  Generar_File_SQL($NombreFile, $SQL);
  $parametros = array(
    array(&$SQL, SQLSRV_PARAM_IN),
  );
  $sql = "EXEC sp_Ejecutar_SQL @sSQL= ?";
  return $conn->ejecutar_procesos_almacenados($sql,$parametros,$tipo=false);
}

function Full_Fields($NombreTabla) 
{
  $conn = new db();
  $SQLTable = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name = '$NombreTabla' ORDER BY ORDINAL_POSITION";
  $result =  $conn->datos($SQLTable);
  $listaCampos = '';
  foreach ($result as $row) {
    $listaCampos .= $row['COLUMN_NAME'] . ', ';
  }
  $listaCampos = trim($listaCampos);
  $listaCampos = substr($listaCampos, 0, -1);
  return $listaCampos;
}

function SetAdoAddNew($NombreTabla, $SinElItem = false) {
  $RegAdodc = [];
  $DatosSelect  ="";
  $IndDato = 0;

  $NombreTabla = trim($NombreTabla);
  $RegAdodc["fields"] = datos_tabla($NombreTabla);
  if(count($RegAdodc)>0){ 
    $RegAdodc = setObjectOrArray($RegAdodc);
    $DatosTabla = array();
    $DatosTabla[0]["Campo"] = $NombreTabla;
    $DatosTabla[0]["Ancho"] = count($RegAdodc->fields);
    $DatosTabla[0]["Valor"] = 0;
    $DatosTabla[0]["Tipo"] = 0;
    for ($IndDato = 0; $IndDato < count($RegAdodc->fields); $IndDato++) {
      $DatosTabla[$IndDato + 1]["Campo"] = $RegAdodc->fields[$IndDato]->COLUMN_NAME;
      $DatosTabla[$IndDato + 1]["Ancho"] = $RegAdodc->fields[$IndDato]->CHARACTER_MAXIMUM_LENGTH;
      $DatosTabla[$IndDato + 1]["Tipo"] = $RegAdodc->fields[$IndDato]->DATA_TYPE;
      $DatosTabla[$IndDato + 1]["Valor"] = valorPorDefectoSegunTipo($RegAdodc->fields[$IndDato]->DATA_TYPE);
      $DatosTabla[$IndDato + 1]["Update"] = false;
    }
    $_SESSION['SetAdoAddNew'][0] = $DatosTabla;
    //echo "<pre>";print_r($_SESSION['SetAdoAddNew'][0]);echo "</pre>";die();
  }
}

function SetAdoFields($NombCampo, $ValorCampo) {
  $DatosTabla = $_SESSION['SetAdoAddNew'][0];
  $NombCampo = trim($NombCampo);
  if (is_null($ValorCampo) || empty($ValorCampo)) $ValorCampo = null;
  for ($IndDato = 1; $IndDato <= $DatosTabla[0]['Ancho']; $IndDato++) {
    if ($DatosTabla[$IndDato]['Campo'] == $NombCampo) {
      switch ($DatosTabla[$IndDato]['Tipo']) {
        case 'nvarchar':
        case 'ntext':
          if (is_null($ValorCampo) || empty($ValorCampo)) $ValorCampo = '';
          $ValorCampo = str_replace("'", "`", $ValorCampo);
          $ValorCampo = str_replace("#", "No.", $ValorCampo);
          if ($DatosTabla[$IndDato]['Ancho'] != -1 && strlen($ValorCampo) > $DatosTabla[$IndDato]['Ancho']) {
            $ValorCampo = trim(substr($ValorCampo, 1, $DatosTabla[$IndDato]['Ancho']));
          }
          if (strlen($ValorCampo) == 0) $ValorCampo = G_NINGUNO;
        break;
        case 'datetime':
        case 'datetime2':
        case 'datetimeoffset':
        case 'smalldatetime':
          if($ValorCampo instanceof DateTime){
            $ValorCampo = $ValorCampo->format('YmdHis');
            break;
          }
          if ((is_int($ValorCampo) && $ValorCampo == 0) || (is_string($ValorCampo) && $ValorCampo == G_NINGUNO)){
            $ValorCampo = date('YmdHis'); break;
          } 
          if (is_null($ValorCampo)){ $ValorCampo = date('YmdHis'); break;} 
          if (!strtotime($ValorCampo)){$ValorCampo = date('YmdHis'); break;} 
        break;
        case 'date':
          if($ValorCampo instanceof DateTime){
            $ValorCampo = $ValorCampo->format('Ymd');
            break;
          }
          if ((is_int($ValorCampo) && $ValorCampo == 0) || (is_string($ValorCampo) && $ValorCampo == G_NINGUNO)){
            $ValorCampo = date('Ymd'); break;
          } 
          if (is_null($ValorCampo)){ $ValorCampo = date('Ymd'); break;} 
          if (!strtotime($ValorCampo)){$ValorCampo = date('Ymd'); break;}
        break;
        case 'bit':
          if (is_null($ValorCampo) || empty($ValorCampo)) $ValorCampo = false;
        break;
        case 'tinyint':
        if ($ValorCampo > 255) $ValorCampo = 0;
        break;
        case 'smallint':
        if ($ValorCampo > 32767) $ValorCampo = 0;
        break;
        case 'int':
        if ($ValorCampo > 2147483647) $ValorCampo = 0;
        break;
        case 'real':
        if ($ValorCampo > 9999999999.99) $ValorCampo = 0;
        break;
        case 'bigint':
        case 'float':
        case 'numeric':
        case 'money':
        case 'decimal':
        if ($ValorCampo > 999999999999.99) $ValorCampo = 0;
        break;
      }
      $DatosTabla[$IndDato]['Valor'] = $ValorCampo;
      $DatosTabla[$IndDato]['Update'] = true;
    }
  }
  $_SESSION['SetAdoAddNew'][0] = $DatosTabla;
}

function SetAdoUpdate(){
  $AdoCon1 = new db();
  $DatosTabla = $_SESSION['SetAdoAddNew'][0];
  $DatosSelect = "";
  $InsertarCampos = "";
  $InsertDato = 0;
  $IndDato = 0;
  $IdTime = 0;

  $InsertarCampos = "";

  $DatosSelect = "INSERT INTO ".$DatosTabla[0]['Campo']." (";
  for ($indDato = 1; $indDato <= $DatosTabla[0]['Ancho']; $indDato++) {
    if ($DatosTabla[$indDato]['Campo'] != 'ID') {
      $DatosSelect .= " [".$DatosTabla[$indDato]['Campo']."],";
    }
  }
  if (substr($DatosSelect, -1) == ',') {
    $DatosSelect = substr($DatosSelect, 0, -1);
  }
  $DatosSelect .= ") VALUES (";
  for ($IndDato = 1; $IndDato <= $DatosTabla[0]['Ancho']; $IndDato++) { 
    if ($DatosTabla[$IndDato]['Campo'] != 'ID') {
      switch ($DatosTabla[$IndDato]['Tipo']) {
        case 'bit':
          if (is_null($DatosTabla[$IndDato]['Valor'])) {
            $DatosTabla[$IndDato]['Valor'] = 0;
          }
          if (is_bool($DatosTabla[$IndDato]['Valor']) && $DatosTabla[$IndDato]['Valor']) {
            $DatosTabla[$IndDato]['Valor'] = 1;
          }
          if ($DatosTabla[$IndDato]['Valor'] == G_NINGUNO || $DatosTabla[$IndDato]['Valor'] == "") {
            $DatosTabla[$IndDato]['Valor'] = 0;
          }
          if ($DatosTabla[$IndDato]['Valor'] < 0) {
            $DatosTabla[$IndDato]['Valor'] = 1;
          }
          if ($DatosTabla[$IndDato]['Valor'] > 1) {
            $DatosTabla[$IndDato]['Valor'] = 1;
          }
          $DatosSelect .= (int)$DatosTabla[$IndDato]['Valor'];
          $InsertarCampos .= $DatosTabla[$IndDato]['Campo'] . " = " . (int)$DatosTabla[$IndDato]['Valor'] . "\n";
          break;
        case 'nvarchar':
        case 'ntext':
            if (is_null($DatosTabla[$IndDato]['Valor']) || empty($DatosTabla[$IndDato]['Valor'])) {
            $DatosTabla[$IndDato]['Valor'] = G_NINGUNO;
          }
          if ($DatosTabla[$IndDato]['Campo'] == "Periodo" && $DatosTabla[$IndDato]['Valor'] == G_NINGUNO) {
            $DatosTabla[$IndDato]['Valor'] = $_SESSION['INGRESO']['periodo'];
          }
          if ($DatosTabla[$IndDato]['Campo'] == "Item" && $DatosTabla[$IndDato]['Valor'] == G_NINGUNO) {
            $DatosTabla[$IndDato]['Valor'] = $_SESSION['INGRESO']['item'];
          }
          if ($DatosTabla[$IndDato]['Campo'] == "CodigoU" && $DatosTabla[$IndDato]['Valor'] == G_NINGUNO) {
            $DatosTabla[$IndDato]['Valor'] = $_SESSION['INGRESO']['CodigoU'];
          }
          $DatosSelect .= "'" . $DatosTabla[$IndDato]['Valor'] . "'";
          $InsertarCampos .= $DatosTabla[$IndDato]['Campo'] . " = '" . $DatosTabla[$IndDato]['Valor'] . "'\n";
          break;
        case 'date':
          if (is_null($DatosTabla[$IndDato]['Valor'])) {
            $DatosTabla[$IndDato]['Valor'] = date("Ymd");
          }
          $DatosSelect .= "#" . BuscarFecha((string)$DatosTabla[$IndDato]['Valor']) . "#";
          $InsertarCampos .= $DatosTabla[$IndDato]['Campo'] . " = #" . BuscarFecha((string)$DatosTabla[$IndDato]['Valor']) . "#\n";
          break;
        case 'datetime':
        case 'datetime2':
        case 'datetimeoffset':
        case 'smalldatetime':
          if (is_null($DatosTabla[$IndDato]['Valor'])) {
            $DatosTabla[$IndDato]['Valor'] = date("YmdHis");
          }
          $DatosSelect .= "#" . BuscarFecha((string)$DatosTabla[$IndDato]['Valor']) . "#";
          $InsertarCampos .= $DatosTabla[$IndDato]['Campo'] . " = #" . BuscarFecha((string)$DatosTabla[$IndDato]['Valor']) . "#\n";
          break;
        case 'tinyint':
        case 'int':
        case 'bigint':
        case 'smallint':
          if (is_null($DatosTabla[$IndDato]['Valor'])) {
              $DatosTabla[$IndDato]['Valor'] = 0;
          }
          $DatosSelect .= (int) $DatosTabla[$IndDato]['Valor'];
          $InsertarCampos .= $DatosTabla[$IndDato]['Campo'] . ' = ' . (int) $DatosTabla[$IndDato]['Valor'] . PHP_EOL;
          break;
        case 'real':
        case 'float':
        case 'numeric':
        case 'money':
        case 'decimal':
          if (is_null($DatosTabla[$IndDato]['Valor'])) {
            $DatosTabla[$IndDato]['Valor'] = 0;
          }
          $DatosSelect .= (float) $DatosTabla[$IndDato]['Valor'];
          $InsertarCampos .= $DatosTabla[$IndDato]['Campo'] . ' = ' . (float) $DatosTabla[$IndDato]['Valor'] . PHP_EOL;
          break;
      }
      $DatosSelect .= ",";
    }
  }
  $DatosSelect .=  ");";
  $DatosSelect = str_replace(",)", ")", $DatosSelect);
  $DatosSelect = CompilarSQL($DatosSelect);
  return Ejecutar_SQL_SP($DatosSelect);
}

function Reporte_Cartera_Clientes_SP($MBFechaInicial, $MBFechaFinal, $CodigoCliente)
{
  $conn = new db();
  $parametros = array(
    array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
    array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
    array(&$_SESSION['INGRESO']['CodigoU'], SQLSRV_PARAM_IN),
    array(&$CodigoCliente, SQLSRV_PARAM_IN),
    array(&$MBFechaInicial, SQLSRV_PARAM_IN),
    array(&$MBFechaFinal, SQLSRV_PARAM_IN)
  );
  $sql = "EXEC sp_Reporte_Cartera_Clientes @Item= ?,@Periodo=?,@CodigoUsuario=?,@CodigoCliente=?,@FechaInicio=?,@FechaCorte=?";
  return $conn->ejecutar_procesos_almacenados($sql,$parametros);
}

function Insertar_Mail($ListaMails, $InsertarMail) {
  if (strpos($ListaMails, $InsertarMail) === false && strpos($InsertarMail, '@') !== false && strlen($InsertarMail) > 3) {
      $ListaMails .= $InsertarMail . ';';
  } /*else {
      $TMail->ListaError .= $TMail->Destinatario . ': ' . $InsertarMail . "\n";
  }*/
  return $ListaMails;
}

function Leer_Datos_Clientes2($Codigo_CIRUC_Cliente, $NoActualizaSP = false) {
    $conn = new db();
    $AdoCliDB = [];
    $Por_Codigo = false;
    $Por_CIRUC = false;
    $Por_Cliente = false;

    $TBenef = array(
        "FA" => false,
        "Asignar_Dr" => false,
        "Codigo" => "",
        "Cliente" => "",
        "Tipo_Cta" => "",
        "Cta_Numero" => "",
        "Descuento" => false,
        "T" => "",
        "TP" => "",
        "CI_RUC" => "",
        "TD" => "",
        "Fecha" => "",
        "Fecha_A" => "",
        "Fecha_N" => "",
        "Sexo" => "",
        "Email1" => "",
        "Email2" => "",
        "Direccion" => "",
        "DirNumero" => "",
        "Telefono" => "",
        "Telefono1" => "",
        "TelefonoT" => "",
        "Celular" => "",
        "Ciudad" => "",
        "Prov" => "",
        "Pais" => "",
        "Profesion" => "",
        "Archivo_Foto" => "",
        "Representante" => "",
        "RUC_CI_Rep" => "",
        "TD_Rep" => "",
        "Direccion_Rep" => "SD",
        "Grupo_No" => "",
        "Contacto" => "",
        "Calificacion" => "",
        "Plan_Afiliado" => "",
        "Cte_Ahr_Otro" => "",
        "Cta_Transf" => "",
        "Cod_Banco" => 0,
        "Salario" => 0,
        "Saldo_Pendiente" => 0,
        "Total_Anticipo" => 0
    );

    if (strlen($Codigo_CIRUC_Cliente) <= 0) {
      $Codigo_CIRUC_Cliente = G_NINGUNO;
    }

    if (!$NoActualizaSP) {
      Leer_Datos_Cliente_SP($Codigo_CIRUC_Cliente);
    }

    $TBenef["Codigo"] = $Codigo_CIRUC_Cliente;

    //Verificamos la informacion del Cliente
    if ($TBenef["Codigo"] != G_NINGUNO) {
        $sSQL = "SELECT " . Full_Fields("Clientes") .
                  " FROM Clientes " .
                  " WHERE Codigo = '" . $TBenef["Codigo"] . "' ";
        $AdoCliDB = $conn->datos($sSQL);

        if (count($AdoCliDB) > 0) {
          $AdoCliDB = $AdoCliDB[0];
          $TBenef["FA"] = $AdoCliDB["FA"];
          $TBenef["Asignar_Dr"] = $AdoCliDB["Asignar_Dr"];
          $TBenef["Cliente"] = $AdoCliDB["Cliente"];
          $TBenef["Descuento"] = $AdoCliDB["Descuento"];
          $TBenef["T"] = $AdoCliDB["T"];
          $TBenef["CI_RUC"] = $AdoCliDB["CI_RUC"];
          $TBenef["TD"] = $AdoCliDB["TD"];
          $TBenef["Fecha"] = $AdoCliDB["Fecha"];
          $TBenef["Fecha_N"] = $AdoCliDB["Fecha_N"];
          $TBenef["Sexo"] = $AdoCliDB["Sexo"];
          $TBenef["Email1"] = $AdoCliDB["Email"];
          $TBenef["Email2"] = $AdoCliDB["Email2"];
          $TBenef["EmailR"] = $AdoCliDB["EmailR"];
          $TBenef["Direccion"] = $AdoCliDB["Direccion"];
          $TBenef["DirNumero"] = $AdoCliDB["DirNumero"];
          $TBenef["Telefono"] = $AdoCliDB["Telefono"];
          $TBenef["Telefono1"] = $AdoCliDB["Telefono_R"];
          $TBenef["TelefonoT"] = $AdoCliDB["TelefonoT"];
          $TBenef["Ciudad"] = $AdoCliDB["Ciudad"];
          $TBenef["Prov"] = $AdoCliDB["Prov"];
          $TBenef["Pais"] = $AdoCliDB["Pais"];
          $TBenef["Profesion"] = $AdoCliDB["Profesion"];
          $TBenef["Grupo_No"] = $AdoCliDB["Grupo"];
          $TBenef["Contacto"] = $AdoCliDB["Contacto"];
          $TBenef["Calificacion"] = $AdoCliDB["Calificacion"];
          $TBenef["Plan_Afiliado"] = $AdoCliDB["Plan_Afiliado"];
          $TBenef["Actividad"] = $AdoCliDB["Actividad"];
          $TBenef["Credito"] = $AdoCliDB["Credito"];

          $TBenef["Representante"] = str_replace("  ", " ", $AdoCliDB["Representante"]);
          $TBenef["RUC_CI_Rep"] = $AdoCliDB["CI_RUC_R"];
          $TBenef["TD_Rep"] = $AdoCliDB["TD_R"];
          $TBenef["Tipo_Cta"] = $AdoCliDB["Tipo_Cta"];
          $TBenef["Cod_Banco"] = $AdoCliDB["Cod_Banco"];
          $TBenef["Cta_Numero"] = $AdoCliDB["Cta_Numero"];
          $TBenef["Direccion_Rep"] = $AdoCliDB["DireccionT"];
          $TBenef["Fecha_Cad"] = $AdoCliDB["Fecha_Cad"];
          $TBenef["Saldo_Pendiente"] = $AdoCliDB["Saldo_Pendiente"];
          $TBenef["Archivo_Foto"] = $AdoCliDB["Archivo_Foto"];
      }
    }

    return $TBenef;
}

function Datos_Iniciales_Entidad_SP_MySQL($empresa, $usuario)
{
  global $Fecha_CO, $Fecha_CE, $Fecha_VPN, $Fecha_DB, $Fecha_P12, $AgenteRetencion, $MicroEmpresa, $EstadoEmpresa, $DescripcionEstado, $NombreEntidad, $RepresentanteLegal, $MensajeEmpresa, $ComunicadoEntidad, $SerieFE, $Cartera, $Cant_FA, $TipoPlan, $PCActivo, $EstadoUsuario, $ConexionConMySQL;

  $ConexionConMySQL = false;
  $PCActivo = true;
  $EstadoUsuario = true;
  $IDEntidad = 0;
  $DescripcionEstado = "OK";
  $EstadoEmpresa = G_NINGUNO;
  $Fecha_CE = date('Y-m-d H:i:s');
  $Fecha_CO = date('Y-m-d H:i:s');
  $Fecha_VPN = date('Y-m-d H:i:s');
  $Fecha_DB = date('Y-m-d H:i:s');
  $Fecha_P12 = date('Y-m-d H:i:s');
  $SerieFE = G_NINGUNO;
  $MicroEmpresa = G_NINGUNO;
  $AgenteRetencion = G_NINGUNO;
  $Cartera = 0;
  $Cant_FA = 0;
  $TipoPlan = 0;
  
  $CadenaParcial = "";
  $modulos = getModulosGeneral();
  foreach ($modulos as $key => $row) {
        $CadenaParcial .= $row["Modulo"] . "^" . $row["Item"] . "^" . $row["Codigo"] . "^~";
  }
  $ItemEmpresa = $empresa["Item"];
  $RUCEmpresa = $empresa["RUC"];
  $CodigoUsuario = $usuario['Codigo'];
  $NombreUsuario = $usuario['Nombre_Completo'];
  $IDEUsuario = $usuario['Usuario'];
  $PWRUsuario = $usuario['Clave'];
  $NombreEmpresa = $empresa["Empresa"];
  $RazonSocialEmpresa = $empresa["Razon_Social"];
  $NombreCiudad = $empresa["Ciudad"];
  $ContadorEmpresa = $empresa["Contador"];
  $ContadorRUC = $empresa["RUC_Contador"];
  $GerenteEmpresa = $empresa["Gerente"];
  $NLogoTipo = $empresa["Logo_Tipo"];
  $NMarcaAgua = $empresa["Marca_Agua"];
  $EmailUsuario = $usuario['EmailUsuario'];
  $NivelesDeAccesos = $CadenaParcial;
  $IP_Local = @$_SESSION['INGRESO']['IP_Local'];
  $IP_WAN = ip();
  $PC_Nombre = G_NINGUNO;
  $PC_MAC = G_NINGUNO;

  $conn = new db();
  //Enviamos los parametro de solo entrada al SP
  $parametros = array(
      array(&$ItemEmpresa, 'IN'),
      array(&$RUCEmpresa, 'IN'),
      array(&$CodigoUsuario, 'IN'),
      array(&$NombreUsuario, 'IN'),
      array(&$IDEUsuario, 'IN'),
      array(&$PWRUsuario, 'IN'),
      array(&$NombreEmpresa, 'IN'),
      array(&$RazonSocialEmpresa, 'IN'),
      array(&$NombreCiudad, 'IN'),
      array(&$ContadorEmpresa, 'IN'),
      array(&$ContadorRUC, 'IN'),
      array(&$GerenteEmpresa, 'IN'),
      array(&$NLogoTipo, 'IN'),
      array(&$NMarcaAgua, 'IN'),
      array(&$EmailUsuario, 'IN'),
      array(&$NivelesDeAccesos, 'IN'),
      array(&$IP_Local, 'IN'),
      array(&$IP_WAN, 'IN'),
      array(&$PC_Nombre, 'IN'),
      array(&$PC_MAC, 'IN'),
      array("FechaCO", 'OUT'),
      array("FechaCE", 'OUT'),
      array("FechaVPN", 'OUT'),
      array("FechaDB", 'OUT'),
      array("FechaP12", 'OUT'),
      array("AgenteRetencion", 'OUT'),
      array("MicroEmpresa", 'OUT'),
      array("EstadoEmpresa", 'OUT'),
      array("DescripcionEstado", 'OUT'),
      array("NombreEntidad", 'OUT'),
      array("Representante", 'OUT'),
      array("MensajeEmpresa", 'OUT'),
      array("ComunicadoEntidad", 'OUT'),
      array("SerieFA", 'OUT'),
      array("TotCartera", 'OUT'),
      array("CantFA", 'OUT'),
      array("TipoPlan", 'OUT'),
      array("pActivo", 'OUT'),
      array("EstadoUsuario", 'OUT'),
  );
  $sql = "Call sp_mysql_datos_iniciales";
  $rsMySQL =  $conn->ejecutar_procesos_almacenados($sql,$parametros, true,$tipo='MYSQL');
  $Fecha_CO = $rsMySQL["@FechaCO"];
  $Fecha_CE = $rsMySQL["@FechaCE"];
  $Fecha_VPN = $rsMySQL["@FechaVPN"];
  $Fecha_DB = $rsMySQL["@FechaDB"];
  $Fecha_P12 = $rsMySQL["@FechaP12"];
  $AgenteRetencion = $rsMySQL["@AgenteRetencion"];
  $MicroEmpresa = $rsMySQL["@MicroEmpresa"];
  $EstadoEmpresa = $rsMySQL["@EstadoEmpresa"];
  $DescripcionEstado = $rsMySQL["@DescripcionEstado"];
  $NombreEntidad = $rsMySQL["@NombreEntidad"];
  $RepresentanteLegal = $rsMySQL["@Representante"];
  $MensajeEmpresa = $rsMySQL["@MensajeEmpresa"];
  $ComunicadoEntidad = $rsMySQL["@ComunicadoEntidad"];
  $SerieFE = $rsMySQL["@SerieFA"];
  $Cartera = $rsMySQL["@TotCartera"];
  $Cant_FA = $rsMySQL["@CantFA"];
  $TipoPlan = $rsMySQL["@TipoPlan"];
  $PCActivo = $rsMySQL["@pActivo"];
  $EstadoUsuario = $rsMySQL["@EstadoUsuario"];
  $ConexionConMySQL = true;
}

function getModulosGeneral()
{
  $conn = new db();
  $sql = "SELECT Modulo, Item, Codigo 
             FROM Acceso_Empresa 
             WHERE Modulo <> '00'" ;
  return $conn->datos($sql);
}

function sp_Iniciar_Datos_Default($Item, $Periodo, $Cotizacion, $RUCEmpresa, $CodigoUsuario, $FechaC, $NumModulo)
{
  $conn = new db();
  $No_ATS = G_NINGUNO;
  $ListSucursales = G_NINGUNO;
  $NombreProvincia = G_NINGUNO;
  $ConSucursal = 0;
  $SiUnidadEducativa = 0;
  $PorcIVA = 0.0; //float
  $parametros = array(
    array(&$Item, SQLSRV_PARAM_IN),
    array(&$Periodo, SQLSRV_PARAM_IN),
    array(&$Cotizacion, SQLSRV_PARAM_IN),
    array(&$RUCEmpresa, SQLSRV_PARAM_IN),
    array(&$CodigoUsuario, SQLSRV_PARAM_IN),
    array(&$FechaC, SQLSRV_PARAM_IN),
    array(&$NumModulo, SQLSRV_PARAM_IN),

    array(&$No_ATS, SQLSRV_PARAM_INOUT),
    array(&$ListSucursales, SQLSRV_PARAM_INOUT),
    array(&$NombreProvincia, SQLSRV_PARAM_INOUT),
    array(&$ConSucursal, SQLSRV_PARAM_INOUT),
    array(&$SiUnidadEducativa, SQLSRV_PARAM_INOUT),
    array(&$PorcIVA, SQLSRV_PARAM_INOUT),
  );
  $sql = "EXEC sp_Iniciar_Datos_Default @Item=?, @Periodo=?, @Cotizacion=?, @RUCEmpresa=?, @CodigoUsuario=?, @FechaC=?, @NumModulo=? , @No_ATS=?, @ListSucursales=?, @NombreProvincia=?, @ConSucursal=?, @SiUnidadEducativa=?, @PorcIVA=?";
  $exec = $conn->ejecutar_procesos_almacenados($sql,$parametros);

    $_SESSION['INGRESO']['porc'] = $PorcIVA;
    $_SESSION['INGRESO']['No_ATS'] = $No_ATS;
    $_SESSION['INGRESO']['ListSucursales'] = $ListSucursales;
    $_SESSION['INGRESO']['NombreProvincia'] = $NombreProvincia;
    $_SESSION['INGRESO']['Sucursal'] = $ConSucursal;
    $_SESSION['INGRESO']['SiUnidadEducativa'] = $SiUnidadEducativa;

  if($exec){
    return compact("No_ATS","ListSucursales","NombreProvincia","ConSucursal","SiUnidadEducativa","PorcIVA");
  }else{
    return $exec;
  }
}

function Estado_Empresa_SP_MySQL()
{
  $conn = new db();

  $ItemEmpresa = $_SESSION['INGRESO']['item'];
  $CodigoUsuario = $_SESSION['INGRESO']['CodigoU'];
  $RUCEmpresa = $_SESSION['INGRESO']['RUC'];
  $IP_WAN = ip();
  $IP_Local = @$_SESSION['INGRESO']['IP_Local'];
  $PC_Nombre = G_NINGUNO;
  $PC_MAC = G_NINGUNO;
  //Parametros de entrada y de salida
  $parametros = array(
    array($ItemEmpresa,'IN'),
    array($RUCEmpresa,'IN'),
    array($CodigoUsuario,'IN'),
    array($IP_Local,'IN'), 
    array($IP_WAN,'IN'),
    array($PC_Nombre,'IN'),
    array($PC_MAC,'IN'),

    array('FechaCO','OUT'),
    array('FechaCE','OUT'),
    array('FechaVPN','OUT'),
    array('FechaDB','OUT'),
    array('FechaP12','OUT'),
    array('AgenteRetencion','OUT'),
    array('MicroEmpresa','OUT'),
    array('EstadoEmpresa','OUT'),
    array('DescripcionEstado','OUT'),
    array('NombreEntidad','OUT'),
    array('Representante','OUT'),
    array('MensajeEmpresa','OUT'),
    array('ComunicadoEntidad','OUT'),
    array('TotCartera','OUT'),
    array('CantFA','OUT'),
    array('TipoPlan','OUT'),
    array('SerieFA','OUT'),
    array('pActivo','OUT'),
    array('EstadoUsuario','OUT'),
  );
  $sql = "CALL sp_mysql_datos_estado_empresa";
  return $conn->ejecutar_procesos_almacenados($sql,$parametros,$respuesta='1',$tipo='MYSQL');
}

function Actualiza_Procesado_Kardex($CodigoInv)
{
    $conn = new db();
    if(strlen($CodigoInv) > 2 )
    {
       $SQLKardex = "UPDATE Trans_Kardex
                SET Procesado = 0
                WHERE Item = '".$_SESSION['INGRESO']['item']."'
                AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
                AND Codigo_Inv = '".$CodigoInv."' ";
       $conn->String_Sql($sql);
    }
}

function EliminarComprobantes($C1)
{

  $conn = new db();
  $sql = "DELETE 
       FROM Comprobantes 
       WHERE TP = '".$C1['TP']."' 
       AND Numero = ".$C1['Numero']." 
       AND Item = '".$C1['Item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $conn->String_Sql($sql);
  $sql = "DELETE 
       FROM Transacciones 
       WHERE TP = '".$C1['TP']."' 
       AND Numero = ".$C1['Numero']." 
       AND Item = '".$C1['Item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $conn->String_sql($sql);
  $sql = "DELETE 
       FROM Trans_SubCtas 
       WHERE TP = '".$C1['TP']."' 
       AND Numero = ".$C1['Numero']." 
       AND Item = '".$C1['Item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $conn->String_sql($sql);
  $sql = "DELETE  
       FROM Trans_Kardex 
       WHERE TP = '".$C1['TP']."' 
       AND Numero = ".$C1['Numero']." 
       AND Item = '".$C1['Item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
       AND LEN(TC) <= 1 ";
  $conn->String_sql($sql);
  $sql = "DELETE 
       FROM Trans_Compras 
       WHERE TP = '".$C1['TP']."' 
       AND Numero = ".$C1['Numero']." 
       AND Item = '".$C1['Item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $conn->String_sql($sql);
  $sql = "DELETE 
       FROM Trans_Air 
       WHERE TP = '".$C1['TP']."' 
       AND Numero = ".$C1['Numero']." 
       AND Item = '".$C1['Item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $conn->String_sql($sql);
  $sql = "DELETE 
       FROM Trans_Ventas 
       WHERE TP = '".$C1['TP']."' 
       AND Numero = ".$C1['Numero']." 
       AND Item = '".$C1['Item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $conn->String_sql($sql);
  $sql = "DELETE 
       FROM Trans_Exportaciones 
       WHERE TP = '".$C1['TP']."' 
       AND Numero = ".$C1['Numero']." 
       AND Item = '".$C1['Item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $conn->String_sql($sql);
  $sql = "DELETE  
       FROM Trans_Importaciones 
       WHERE TP = '".$C1['TP']."' 
       AND Numero = ".$C1['Numero']." 
       AND Item = '".$C1['Item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $conn->String_sql($sql);
  
  $sql = "DELETE  
       FROM Trans_Rol_Pagos 
       WHERE TP = '".$C1['TP']."' 
       AND Numero = ".$C1['Numero']." 
       AND Item = '".$C1['Item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $conn->String_sql($sql);
}

function GrabarComprobante($C1)
{

  $conn = new db();
  // Dim ConBodegas As Boolean
  // Dim NumTrans As Long
  // Dim CodSustento As String
  // Dim AdoTemp As ADODB.Recordset
  // Dim AdoRetAut As ADODB.Recordset

  // RatonReloj
  // ' Encabezado del Comprobante
   if(count($C1)==0){
       if($C1['T'] == ""){ $C1['T'] = G_NORMAL;}
       if($C1['Fecha'] == ''){ $C1['Fecha'] = date('Y-m-d');}
       if($C1['CodigoDr'] == ""){ $C1['CodigoDr'] = G_NINGUNO;}
       if($C1['Concepto'] == ""){ $C1['Concepto'] = G_NINGUNO;}
       if($C1['CodigoB'] == ""){ $C1['CodigoB'] = G_NINGUNO;}
       if($C1['RUC_CI'] == ""){ $C1['RUC_CI'] = "0000000000000";}
       $FechaComp =$C1['Fecha'];
       if($C1['Numero']== 0){
         if($C1['TP']=='CD'){ $Ci['Numero'] = ReadSetDataNum("Diario", True, True);}
         if($C1['TP']=='CI'){ $Ci['Numero'] = ReadSetDataNum("Ingresos", True, True);}
         if($C1['TP']=='CE'){ $Ci['Numero'] = ReadSetDataNum("Egresos", True, True);}
         if($C1['TP']=='ND'){ $Ci['Numero'] = ReadSetDataNum("NotaDebito", True, True);}
         if($C1['TP']=='NC'){ $Ci['Numero'] = ReadSetDataNum("NotaCredito", True, True);}
       }
  }
 // 'Grabamos los datos de la transaccion en la tabla definitiva de almacenamiento
  $TMail['TipoDeEnvio'] = G_NINGUNO;
  if(strlen($C1['Autorizacion_LC']) >= 13){
     $C1['Autorizacion_LC'] = ReadSetDataNum("LC_SERIE_".$C1['Serie_LC'], True, True);
     $TMail['TipoDeEnvio'] = "CE";
  }
  if(strlen($C1['Autorizacion_R']) >= 13){
     $sql = "SELECT * 
          FROM Asiento_Air 
          WHERE Item = '".$_SESSION['INGRESO']['item']."' 
          AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' 
          AND T_No = ".$C1['T_No']." 
          ORDER BY Tipo_Trans, A_No ";
    $AdoTemp = $conn->datos($sql);
    if(count($AdoTemp)>0 && $C1['RetNueva'] && $C1['RetSecuencial'])
    {    
       $C1['Retencion'] = ReadSetDataNum("RE_SERIE_".$C1['Serie_R'], True, True);
    }
     $TMail['TipoDeEnvio'] = "CE";
  }
  $FA['TP'] = $C1['TP'];
  $FA['Fecha'] = $C1['Fecha'];
  $FA['Numero'] = $C1['Numero'];
  $FA['ClaveAcceso'] = G_NINGUNO;
 // 'Actualizar las Ctas a mayoriazar
  $sql1 = "SELECT Codigo_Inv 
      FROM Trans_Kardex 
      WHERE TP = '".$C1['TP']."' 
      AND Numero = ".$C1['Numero']." 
      AND Item = '".$_SESSION['INGRESO']['item']."' 
      AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $AdoTemp = $conn->datos($sql1);
  if(count($AdoTemp)>0)
  {
  foreach ($AdoTemp as $key => $value) {
         Actualiza_Procesado_Kardex($value["Codigo_Inv"]);
      }    
  }

  // ' Borramos la informacion del comprobante si lo hubiera
  EliminarComprobantes($C1);
  // ' Por Bodegas
  $ConBodegas = false;
  $sql1 = "SELECT CodBod 
       FROM Catalogo_Bodegas 
       WHERE Item = '".$_SESSION['INGRESO']['item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
  $AdoTemp = $conn->datos($sql1);
  if(count($AdoTemp) > 1){ $ConBodegas = True;}

  //' Grabamos SubCtas
  $sql = "SELECT * 
      FROM Asiento_SC 
      WHERE Item = '".$C1['Item']."' 
      AND T_No = ".$C1['T_No']." 
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' 
      ORDER BY TC,Cta,Codigo ";
  $AdoTemp =$conn->datos($sql);
  if(count($AdoTemp) > 0)
  {
       foreach ($AdoTemp as $key => $value) 
       {         
          $Valor = $value["Valor"];
          $Valor_ME = $value["Valor_ME"];
          $Codigo = $value["Codigo"];
          $TipoCta = $value["TC"];
          $OpcDH = intval($value["DH"]);
          $Factura_No = $value["Factura"];
          $Fecha_Vence = $value["FECHA_V"]->format('Y-m-d');
          $Cta_Cobrar = trim($value["Cta"]);
          if($Valor <> 0 || $Valor_ME <> 0)
          {
             SetAdoAddNew("Trans_SubCtas");
             SetAdoFields("T", $C1['T']);
             SetAdoFields("TP", $C1['TP']);
             SetAdoFields("Numero", $C1['Numero']);
             SetAdoFields("Fecha", $C1['Fecha']);
             SetAdoFields("Item", $C1['Item']);
             SetAdoFields("TC", $TipoCta);
             SetAdoFields("Cta", $Cta_Cobrar);
             SetAdoFields("Codigo", $Codigo);
             SetAdoFields("Fecha_V", $Fecha_Vence);
             SetAdoFields("Factura", $Factura_No);
             SetAdoFields("Detalle_SubCta", $value["Detalle_SubCta"]);
             SetAdoFields("Prima", $value["Prima"]);
             if($OpcDH == 1)
             {
                SetAdoFields("Debitos", $Valor);
                SetAdoFields("Parcial_ME", $Valor_ME);
             }else{
                SetAdoFields("Creditos",$Valor);
                SetAdoFields("Parcial_ME",$Valor_ME * -1);
             }
             SetAdoUpdate();
             // $NumTrans = $NumTrans + 1;
          }
      }
  }
    
 // 'RETENCIONES COMPRAS
  $sql = "SELECT * 
      FROM Asiento_Compras 
      WHERE Item = '".$_SESSION['INGRESO']['item']."' 
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' 
      AND T_No = ".$C1['T_No']." 
      ORDER BY T_No ";
  $AdoTemp = $conn->datos($sql);
   if(count($AdoTemp)> 0)
   {
      // 'Generacion de la Retencion si es Electronica
    foreach ($AdoTemp as $key => $value) 
    {

    // print_r($AdoTemp);die();
       $FechaTexto = $value["FechaRegistro"];
       $CodSustento = generaCeros($value["CodSustento"],2);
       SetAdoAddNew("Trans_Compras");
       SetAdoFields("IdProv",$C1['CodigoB']);
       SetAdoFields("DevIva", $value["DevIva"]);
       SetAdoFields("CodSustento", $value["CodSustento"]);
       SetAdoFields("TipoComprobante", $value["TipoComprobante"]);
       SetAdoFields("Establecimiento", $value["Establecimiento"]);
       SetAdoFields("PuntoEmision", $value["PuntoEmision"]);
       SetAdoFields("Secuencial", $value["Secuencial"]);
       SetAdoFields("Autorizacion", $value["Autorizacion"]);
       SetAdoFields("FechaEmision", $value["FechaEmision"]->format('Y-m-d'));
       SetAdoFields("FechaRegistro", $value["FechaRegistro"]->format('Y-m-d'));
       SetAdoFields("FechaCaducidad", $value["FechaCaducidad"]->format('Y-m-d'));
       SetAdoFields("BaseNoObjIVA", $value["BaseNoObjIVA"]);
       SetAdoFields("BaseImponible", $value["BaseImponible"]);
       SetAdoFields("BaseImpGrav", $value["BaseImpGrav"]);
       SetAdoFields("PorcentajeIva", $value["PorcentajeIva"]);
       SetAdoFields("MontoIva", $value["MontoIva"]);
       SetAdoFields("BaseImpIce", $value["BaseImpIce"]);
       SetAdoFields("PorcentajeIce", $value["PorcentajeIce"]);
       SetAdoFields("MontoIce", $value["MontoIce"]);
       SetAdoFields("MontoIvaBienes", $value["MontoIvaBienes"]);
       SetAdoFields("PorRetBienes", $value["PorRetBienes"]);
       SetAdoFields("ValorRetBienes", $value["ValorRetBienes"]);
       SetAdoFields("MontoIvaServicios", $value["MontoIvaServicios"]);
       SetAdoFields("PorRetServicios", $value["PorRetServicios"]);
       SetAdoFields("ValorRetServicios", $value["ValorRetServicios"]);
       SetAdoFields("Porc_Bienes", $value["Porc_Bienes"]);
       SetAdoFields("Porc_Servicios", $value["Porc_Servicios"]);
       SetAdoFields("Cta_Servicio", $value["Cta_Servicio"]);
       SetAdoFields("Cta_Bienes", $value["Cta_Bienes"]);
       SetAdoFields("Linea_SRI", 0);
       SetAdoFields("DocModificado", $value["DocModificado"]);
       SetAdoFields("FechaEmiModificado", $value["FechaEmiModificado"]->format('Y-m-d'));
       SetAdoFields("EstabModificado", $value["EstabModificado"]);
       SetAdoFields("PtoEmiModificado", $value["PtoEmiModificado"]);
       SetAdoFields("SecModificado", $value["SecModificado"]);
       SetAdoFields("AutModificado", $value["AutModificado"]);
       SetAdoFields("ContratoPartidoPolitico", $value["ContratoPartidoPolitico"]);
       SetAdoFields("MontoTituloOneroso", $value["MontoTituloOneroso"]);
       SetAdoFields("MontoTituloGratuito", $value["MontoTituloGratuito"]);
       SetAdoFields("PagoLocExt", $value["PagoLocExt"]);
       SetAdoFields("PaisEfecPago", $value["PaisEfecPago"]);
       SetAdoFields("AplicConvDobTrib", $value["AplicConvDobTrib"]);
       SetAdoFields("PagExtSujRetNorLeg", $value["PagExtSujRetNorLeg"]);
       SetAdoFields("FormaPago", $value["FormaPago"]);
       SetAdoFields("Serie_Retencion", $C1['Serie_R']);
       SetAdoFields("SecRetencion", $C1['Retencion']);
       SetAdoFields("AutRetencion", $C1['Autorizacion_R']);
       SetAdoFields("Clave_Acceso", G_NINGUNO);
       SetAdoFields("T",G_NORMAL);
       SetAdoFields("TP", $C1['TP']);
       SetAdoFields("Numero", $C1['Numero']);
       SetAdoFields("Fecha", $C1['Fecha']);
       SetAdoUpdate();
     }
 }
  // print('arg');die();
  // ' RETENCIONES VENTAS
  $sql = "SELECT *
       FROM Asiento_Ventas
       WHERE Item = '".$_SESSION['INGRESO']['item']."'
       AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
       AND T_No = ".$C1['T_No']."
       ORDER BY T_No DESC ";
  $AdoTemp = $conn->datos($sql);
   if(count($AdoTemp)> 0)
   {
       $FechaTexto =$value["FechaRegistro"];
       SetAdoAddNew("Trans_Ventas");
       SetAdoFields("IdProv",$C1['CodigoB']);
       SetAdoFields("TipoComprobante",$value["TipoComprobante"]);
       SetAdoFields("FechaRegistro",$value["FechaRegistro"]->format('Y-m-d'));
       SetAdoFields("FechaEmision",$value["FechaEmision"]->format('Y-m-d'));
       SetAdoFields("Establecimiento",$value["Establecimiento"]);
       SetAdoFields("PuntoEmision",$value["PuntoEmision"]);
       SetAdoFields("Secuencial",$value["Secuencial"]);
       SetAdoFields("NumeroComprobantes",$value["NumeroComprobantes"]);
       SetAdoFields("BaseImponible",$value["BaseImponible"]);
       SetAdoFields("IvaPresuntivo",$value["IvaPresuntivo"]);
       SetAdoFields("BaseImpGrav",$value["BaseImpGrav"]);
       SetAdoFields("PorcentajeIva",$value["PorcentajeIva"]);
       SetAdoFields("MontoIva",$value["MontoIva"]);
       SetAdoFields("BaseImpIce",$value["BaseImpIce"]);
       SetAdoFields("PorcentajeIce",$value["PorcentajeIce"]);
       SetAdoFields("MontoIce",$value["MontoIce"]);
       SetAdoFields("MontoIvaBienes",$value["MontoIvaBienes"]);
       SetAdoFields("PorRetBienes",$value["PorRetBienes"]);
       SetAdoFields("ValorRetBienes",$value["ValorRetBienes"]);
       SetAdoFields("MontoIvaServicios",$value["MontoIvaServicios"]);
       SetAdoFields("PorRetServicios",$value["PorRetServicios"]);
       SetAdoFields("ValorRetServicios",$value["ValorRetServicios"]);
       SetAdoFields("RetPresuntiva",$value["RetPresuntiva"]);
       SetAdoFields("Porc_Bienes",$value["Porc_Bienes"]);
       SetAdoFields("Porc_Servicios",$value["Porc_Servicios"]);
       SetAdoFields("Cta_Servicio",$value["Cta_Servicio"]);
       SetAdoFields("Cta_Bienes",$value["Cta_Bienes"]);
       SetAdoFields("Tipo_Pago",$value["Tipo_Pago"]);
       SetAdoFields("Linea_SRI", 0);
       SetAdoFields("T", G_NORMAL);
       SetAdoFields("TP", $C1['TP']);
       SetAdoFields("Numero",$C1['Numero']);
       SetAdoFields("Fecha",$C1['Fecha']);
      // 'Razon Social
      // 'MsgBox C1.Beneficiario
       SetAdoFields("RUC_CI", $C1['RUC_CI']);
       SetAdoFields("IB", $C1['TD']);
       SetAdoFields("Razon_Social", $C1['Beneficiario']);
       SetAdoUpdate();    
  }

  // ' RETENCIONES EXPORTACION
    $sql = "SELECT * 
      FROM Asiento_Exportaciones 
      WHERE Item = '".$_SESSION['INGRESO']['item']."' 
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' 
      AND T_No = ".$C1['T_No']." 
      ORDER BY T_No DESC ";
    $AdoTemp = $conn->datos($sql);
    if(count($AdoTemp) > 0)
    {
       SetAdoAddNew("Trans_Exportaciones");
       SetAdoFields("Codigo",$value["Codigo"]);
       SetAdoFields("CtasxCobrar",$value["CtasxCobrar"]);
       SetAdoFields("ExportacionDe",$value["ExportacionDe"]);
       SetAdoFields("TipoComprobante",$value["TipoComprobante"]);
       SetAdoFields("FechaEmbarque",$value["FechaEmbarque"]->format('Y-m-d'));
       SetAdoFields("NumeroDctoTransporte",$value["NumeroDctoTransporte"]);
       SetAdoFields("IdFiscalProv", $C1['CodigoB']);
       SetAdoFields("ValorFOB",$value["ValorFOB"]);
       SetAdoFields("DevIva",$value["DevIva"]);
       SetAdoFields("FacturaExportacion",$value["FacturaExportacion"]);
       SetAdoFields("ValorFOBComprobante",$value["ValorFOBComprobante"]);
       SetAdoFields("DistAduanero",$value["DistAduanero"]);
       SetAdoFields("Anio",$value["Anio"]);
       SetAdoFields("Regimen",$value["Regimen"]);
       SetAdoFields("Correlativo",$value["Correlativo"]);
       SetAdoFields("Verificador",$value["Verificador"]);
       SetAdoFields("Establecimiento",$value["Establecimiento"]);
       SetAdoFields("PuntoEmision",$value["PuntoEmision"]);
       SetAdoFields("Secuencial",$value["Secuencial"]);
       SetAdoFields("Autorizacion",$value["Autorizacion"]);
       SetAdoFields("FechaEmision",$value["FechaEmision"]->format('Y-m-d'));
       SetAdoFields("FechaRegistro",$value["FechaRegistro"]->format('Y-m-d'));
       SetAdoFields("Linea_SRI", 0);
       SetAdoFields("T", G_NORMAL);
       SetAdoFields("TP", $C1['TP']);
       SetAdoFields("Numero", $C1['Numero']);
       SetAdoFields("Fecha", $C1['Fecha']);
       SetAdoUpdate();
    }
  // ' RETENCIONES IMPORTACIONES
  $sql = "SELECT * 
      FROM Asiento_Importaciones 
      WHERE Item = '".$_SESSION['INGRESO']['item']."' 
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' 
      AND T_No = ".$C1['T_No']." 
      ORDER BY T_No DESC ";
  $AdoTemp = $conn->datos($sql);
   if(count($AdoTemp) > 0 )
   {
    foreach ($AdoTemp as $key => $value) 
    {     
       $FechaTexto =$value["FechaLiquidacion"]->format('Y-m-d');
       SetAdoAddNew("Trans_Importaciones");
       SetAdoFields("CodSustento",$value["CodSustento"]);
       SetAdoFields("ImportacionDe",$value["ImportacionDe"]);
       SetAdoFields("FechaLiquidacion",$value["FechaLiquidacion"]->format('Y-m-d'));
       SetAdoFields("TipoComprobante",$value["TipoComprobante"]);
       SetAdoFields("DistAduanero",$value["DistAduanero"]);
       SetAdoFields("Anio",$value["Anio"]);
       SetAdoFields("Regimen",$value["Regimen"]);
       SetAdoFields("Correlativo",$value["Correlativo"]);
       SetAdoFields("Verificador",$value["Verificador"]);
       SetAdoFields("IdFiscalProv", $C1['CodigoB']);
       SetAdoFields("ValorCIF",$value["ValorCIF"]);
       SetAdoFields("BaseImponible",$value["BaseImponible"]);
       SetAdoFields("BaseImpGrav",$value["BaseImpGrav"]);
       SetAdoFields("PorcentajeIva",$value["PorcentajeIva"]);
       SetAdoFields("MontoIva",$value["MontoIva"]);
       SetAdoFields("BaseImpIce",$value["BaseImpIce"]);
       SetAdoFields("PorcentajeIce",$value["PorcentajeIce"]);
       SetAdoFields("MontoIce",$value["MontoIce"]);
       SetAdoFields("Linea_SRI", 0);
       SetAdoFields("T", G_NORMAL);
       SetAdoFields("TP", $C1['TP']);
       SetAdoFields("Numero", $C1['Numero']);
       SetAdoFields("Fecha", $C1['Fecha']);
       SetAdoUpdate(); 
        // code...
    } 
  }

  // ' RETENCIONES AIR
  $sql = "SELECT * 
      FROM Asiento_Air 
      WHERE Item = '".$_SESSION['INGRESO']['item']."' 
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' 
      AND T_No = ".$C1['T_No']." 
      ORDER BY Tipo_Trans,A_No ";
  $AdoTemp = $conn->datos($sql);
   if(count($AdoTemp) > 0)
   {
      foreach ($AdoTemp as $key => $value) 
      {      
          SetAdoAddNew("Trans_Air");
          SetAdoFields("CodRet",$value["CodRet"]);
          SetAdoFields("BaseImp",$value["BaseImp"]);
          SetAdoFields("Porcentaje",$value["Porcentaje"]);
          SetAdoFields("ValRet",$value["ValRet"]);
          SetAdoFields("EstabRetencion",$value["EstabRetencion"]);
          SetAdoFields("PtoEmiRetencion",$value["PtoEmiRetencion"]);
          SetAdoFields("Tipo_Trans",$value["Tipo_Trans"]);
          SetAdoFields("IdProv", $C1['CodigoB']);
          SetAdoFields("Cta_Retencion",$value["Cta_Retencion"]);
          SetAdoFields("EstabFactura",$value["EstabFactura"]);
          SetAdoFields("PuntoEmiFactura",$value["PuntoEmiFactura"]);
          SetAdoFields("Factura_No",$value["Factura_No"]);
          SetAdoFields("Linea_SRI", 0);
          SetAdoFields("T", G_NORMAL);
          SetAdoFields("TP", $C1['TP']);
          SetAdoFields("Numero", $C1['Numero']);
          SetAdoFields("Fecha", $C1['Fecha']);
          SetAdoFields("SecRetencion", $C1['Retencion']);
          SetAdoFields("AutRetencion", $C1['Autorizacion_R']);
          SetAdoUpdate();
          // $NumTrans = $NumTrans + 1;
      }
    }
  
  // ' Grabamos Retencion de Rol de Pagos
  $sql = "SELECT * 
      FROM Asiento_RP 
      WHERE Item = '".$C1['Item']."' 
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' 
      AND T_No = ".$C1['T_No']." 
      ORDER BY Codigo ";
   $AdoTempC = cabecera_tabla('Asiento_RP');
   $AdoTemp = $conn->datos($sql);

   if(count($AdoTemp)> 0)
   {
      foreach ($AdoTemp as $key => $value) 
      {       
          // ojo toca ver si se puede envia cabecera y el valor  
          SetAdoAddNew("Trans_Rol_Pagos");
          foreach ($AdoTempC as $key => $value2) 
          {
            SetAdoFields($value2['COLUMN_NAME'],$value[$value2['COLUMN_NAME']]);
          }
          //-----------------------------
          SetAdoFields("CodigoU",$_SESSION['INGRESO']['CodigoU']);
          SetAdoFields("Item",$C1['Item']);
          SetAdoFields("Fecha",$C1['Fecha']);
          SetAdoFields("T", G_NORMAL);
          SetAdoFields("TP",$C1['TP']);
          SetAdoFields("Numero",$C1['Numero']);
          SetAdoFields("Codigo",$C1['CodigoB']);
          SetAdoUpdate();
      }
    }
  
  // ' Grabamos Inventarios
  $sql = "SELECT * 
      FROM Asiento_K 
      WHERE Item = '".$C1['Item']."' 
      AND T_No = ".$C1['T_No']." 
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' ";
  $AdoTemp = $conn->datos($sql);
  if(count($AdoTemp) > 0)
  {
    foreach ($AdoTemp as $key => $value) {
      // ' Asiento de Inventario
        SetAdoAddNew("Trans_Kardex");
        SetAdoFields("T", G_NORMAL);
        SetAdoFields("TP", $C1['TP']);
        SetAdoFields("Numero", $C1['Numero']);
        SetAdoFields("Fecha", $C1['Fecha']);
        SetAdoFields("Codigo_Dr",$value["Codigo_Dr"]); //' C1.CodigoDr
        SetAdoFields("Codigo_Tra",$value["Codigo_Tra"]); //' C1.CodigoDr
        SetAdoFields("Codigo_Inv",$value["CODIGO_INV"]);
        SetAdoFields("Codigo_P",$value["Codigo_B"]);
        SetAdoFields("Descuento",$value["P_DESC"]);
        SetAdoFields("Descuento1",$value["P_DESC1"]);
        SetAdoFields("Valor_Total",$value["VALOR_TOTAL"]);
        SetAdoFields("Existencia",$value["CANTIDAD"]);
        SetAdoFields("Valor_Unitario",$value["VALOR_UNIT"]);
        SetAdoFields("Total",$value["SALDO"]);
        SetAdoFields("Cta_Inv",$value["CTA_INVENTARIO"]);
        SetAdoFields("Contra_Cta",$value["CONTRA_CTA"]);
        SetAdoFields("Orden_No",$value["ORDEN"]);
        SetAdoFields("CodBodega",$value["CodBod"]);
        SetAdoFields("CodMarca",$value["CodMar"]);
        SetAdoFields("Codigo_Barra",$value["COD_BAR"]);
        SetAdoFields("Costo",$value["VALOR_UNIT"]);
        SetAdoFields("PVP",$value["PVP"]);
        SetAdoFields("No_Refrendo",$value["No_Refrendo"]);
        SetAdoFields("Lote_No",$value["Lote_No"]);
        SetAdoFields("Fecha_Fab",$value["Fecha_Fab"]->format('Y-m-d'));
        SetAdoFields("Fecha_Exp",$value["Fecha_Exp"]->format('Y-m-d'));
        SetAdoFields("Modelo",$value["Modelo"]);
        SetAdoFields("Serie_No",$value["Serie_No"]);
        SetAdoFields("Procedencia",$value["Procedencia"]);
        SetAdoFields("CodigoL",$value["SUBCTA"]);
        if($Inv_Promedio)
        {
           $Cantidad =$value["CANTIDAD"];
           $Saldo =$value["SALDO"];
           if($Cantidad <= 0){$Cantidad = 1;}
           SetAdoFields("Costo", $Saldo / $Cantidad);
        }
        if($value["DH"] == 1){
           SetAdoFields("Entrada",$value["CANT_ES"]);
        }else{
           SetAdoFields("Salida",$value["CANT_ES"]);
           $Si_No = False;
        }
        SetAdoFields("CodigoU",$_SESSION['INGRESO']['CodigoU']);
        SetAdoFields("Item",$_SESSION['INGRESO']['item']);
        SetAdoUpdate();
        $NumTrans = $NumTrans + 1;
    }
  }
  // ' Grabamos Prestamos
  $sql = "SELECT * 
      FROM Asiento_P 
      WHERE Item = '".$C1['Item']."' 
      AND T_No = ".$C1['T_No']." 
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' ";
  $AdoTemp = $conn->datos($sql);
  if(count($AdoTemp) > 0)
  {
     $TotalCapital = 0;
     $TotalInteres = 0;
     foreach ($AdoTemp as $key => $value) 
     {       
        if($value["Cuotas"] > 0)
        {
           SetAdoAddNew("Trans_Prestamos");
           SetAdoFields("T", "P");
           SetAdoFields("Fecha",$value["Fecha"]->format('Y-m-d'));
           SetAdoFields("TP", $C1['TP']);
           SetAdoFields("Credito_No", $_SESSION['INGRESO']['item'].generaCeros($C1['Numero'],7));
           SetAdoFields("Cta", $Cta);
           SetAdoFields("Cuenta_No", $C1['CodigoB']);
           SetAdoFields("Cuota_No",$value["Cuotas"]);
           SetAdoFields("Interes",$value["Interes"]);
           SetAdoFields("Capital",$value["Capital"]);
           SetAdoFields("Pagos",$value["Pagos"]);
           SetAdoFields("Saldo",$value["Saldo"]);
           SetAdoFields("CodigoU",$value["CodigoU"]);
           SetAdoFields("Item", $C1['Item']);
           SetAdoUpdate();        
        }
        $TotalCapital = $TotalCapital +$value["Capital"];
        $TotalInteres = $TotalInteres +$value["Interes"];
        $TotalAbonos =$value["Pagos"];
        $Cta = $value["Cta"];
        $NumMeses =$value["Cuotas"];
     }
     SetAdoAddNew("Prestamos");
     SetAdoFields("T", "P");
     SetAdoFields("Fecha", $C1['Fecha']);
     SetAdoFields("TP", $C1['TP']);
     SetAdoFields("Credito_No", $_SESSION['INGRESO']['item'].generaCeros($C1['Numero'],7));
     SetAdoFields("Cta", $Cta);
     SetAdoFields("Cuenta_No", $C1['CodigoB']);
     SetAdoFields("Meses", $NumMeses);
     SetAdoFields("Tasa", number_format(($TotalInteres * 12) / ($TotalCapital * $NumMeses), 4));
     SetAdoFields("Interes", $TotalInteres);
     SetAdoFields("Capital", $TotalCapital);
     SetAdoFields("Pagos", $TotalAbonos);
     SetAdoFields("Saldo_Pendiente", $TotalCapital);
     SetAdoFields("Item", $C1['Item']);
     SetAdoUpdate();
  }

// ' Grabamos Comprobantes
  SetAdoAddNew("Comprobantes");
  SetAdoFields("Item", $C1["Item"]);
  SetAdoFields("T", $C1["T"]);
  SetAdoFields("Fecha", $C1["Fecha"]);
  SetAdoFields("TP", $C1["TP"]);
  SetAdoFields("Numero", $C1["Numero"]);
  SetAdoFields("Codigo_B", $C1["CodigoB"]);
  SetAdoFields("Monto_Total", number_format(floatval($C1["Monto_Total"]), 2,'.',''));
  SetAdoFields("Concepto", $C1["Concepto"]);
  SetAdoFields("Efectivo", $C1["Efectivo"]);
  SetAdoFields("Cotizacion", $C1["Cotizacion"]);
  SetAdoFields("CodigoU", $C1["Usuario"]);
  SetAdoFields("Autorizado",$C1["Autorizado"]);
  SetAdoUpdate();
// ' Grabamos Transacciones
  $sql = "SELECT *
      FROM Asiento
      WHERE Item = '".$C1["Item"]."'
      AND T_No = ".$C1['T_No']."
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      ORDER BY A_No,DEBE DESC,CODIGO ";
  $AdoTemp = $conn->datos($sql);
  if(count($AdoTemp) > 0)
  {
    // print_r($AdoTemp);die();
    foreach ($AdoTemp as $key => $value) 
    {  
      $Moneda_US =$value["ME"];
      $Cta = trim($value["CODIGO"]);
      $Debe = number_format($value["DEBE"], 2,'.','');
      $Haber = number_format($value["HABER"], 2,'.','');
      $Parcial = number_format($value["PARCIAL_ME"], 2,'.','');
      $NoCheque =$value["CHEQ_DEP"];
      $CodigoCC =$value["CODIGO_CC"];
      $Fecha_Vence =$value["EFECTIVIZAR"];
      $DetalleComp =$value["DETALLE"];
      $CodigoP =$value["CODIGO_C"];
      if($CodigoP = G_NINGUNO){ $CodigoP = $C1['CodigoB'];}
      // 'MsgBox C1.T_No & vbCrLf & C1.Concepto & vbCrLf & Debe & vbCrLf & Haber
      if(strpos($C1["Ctas_Modificar"], $Cta ) === false ){$C1["Ctas_Modificar"] = $C1["Ctas_Modificar"].$Cta.",";}
      if($Debe + $Haber > 0 )
      {
         SetAdoAddNew("Transacciones");
         SetAdoFields("T", $C1["T"]);
         SetAdoFields("Fecha", $C1["Fecha"]);
         SetAdoFields("TP", $C1["TP"]);
         SetAdoFields("Numero", $C1["Numero"]);
         SetAdoFields("Cta", $Cta);
         SetAdoFields("Parcial_ME", $Parcial);
         SetAdoFields("Debe", $Debe);
         SetAdoFields("Haber", $Haber);
         SetAdoFields("Parcial_ME", $Parcial);
         SetAdoFields("Cheq_Dep", $NoCheque);
         SetAdoFields("Fecha_Efec", $Fecha_Vence->format('Y-m-d'));
         SetAdoFields("Detalle", $DetalleComp);
         SetAdoFields("Codigo_C", $CodigoP);
         SetAdoFields("C_Costo", $CodigoCC);
         SetAdoFields("Item", $C1["Item"]);
        // 'SetAdoFields("C", True)
         SetAdoFields("Procesado", False);
         SetAdoUpdate();
         // $NumTrans = $NumTrans + 1;
      }
    }
  }
 // 'Pasamos a colocar las cuentas que se tienen que mayorizar despuesde grabar el comprobante

  // ojo poner en prueba esto no se sabe todavia bien
  if(strlen($C1["Ctas_Modificar"]) > 1 )
  {
     while(strlen($C1["Ctas_Modificar"]) > 1)
     {
        $I = strpos($C1["Ctas_Modificar"], ",");
        $Cta = trim(substr($C1["Ctas_Modificar"], 1, $I-1));
        $sql = "UPDATE Transacciones 
            SET Procesado = 0 
            WHERE Item = '".$_SESSION['INGRESO']['item']."' 
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
            AND Cta = '".$Cta."'";
        $conn->String_Sql($sql);
        $C1["Ctas_Modificar"] = substr($C1["Ctas_Modificar"], $I + 1, strlen($C1["Ctas_Modificar"]));
     }
  }
 // 'Actualiza el Email del beneficiario
  if(strlen($C1["Email"]) > 3)
  {
     $sql = "UPDATE Clientes
          SET Email = '".$C1["Email"]."'
          WHERE Codigo = '".$C1["CodigoB"]."' ";
        $conn->String_Sql($sql);
  }
 // 'Pasamos a Autorizar la retencion si es electronica
  $FA["Autorizacion_R"] = $C1["Autorizacion_R"];
  $FA["Retencion"] = $C1["Retencion"];
  $FA["Serie_R"] = $C1["Serie_R"];
  if(strlen($FA["Autorizacion_R"]) >= 13){ //Autorizar_retencion($FA); //SRI_Crear_Clave_Acceso_Retenciones($FA, True);
  }
 // 'Eliminamos Asientos contables
  $Trans_No = $C1["T_No"];
  // BorrarAsientos('',True);
  // Control_Procesos Normal, "Grabar Comprobante de: " & $C1["TP & " No. " & $C1["Numero"]
}



   function BorrarAsientos($Trans_No,$B_Asiento=false)
   {    
    $conn = new db();
    $sql='';
    if($Trans_No <= 0){$Trans_No = 1;}
      if($B_Asiento){
         $sql.= "DELETE
           FROM Asiento
           WHERE Item = '".$_SESSION['INGRESO']['item']."'
           AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
           AND T_No = ".$Trans_No." ";
      }
      $sql.= "DELETE
      FROM Asiento_SC
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";

    $sql.= "DELETE
      FROM Asiento_B
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";


    $sql.= "DELETE
      FROM Asiento_R
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";


    $sql.= "DELETE
      FROM Asiento_RP
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";


    $sql.= "DELETE
      FROM Asiento_K
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";

    $sql.= "DELETE
      FROM Asiento_P
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";

    $sql.= "DELETE
      FROM Asiento_Air
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";

    $sql.= "DELETE
      FROM Asiento_Compras
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";

    $sql.= "DELETE
      FROM Asiento_Exportaciones
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";

    $sql.= "DELETE
      FROM Asiento_Importaciones
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";

    $sql.= "DELETE
      FROM Asiento_Ventas
      WHERE Item = '".$_SESSION['INGRESO']['item']."'
      AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
      AND T_No = ".$Trans_No." ";
      // print_r($sql);die();
    $result = $conn->String_Sql($sql);
       return $result;
  }


  // INICIO CIERRE DE CAJA
  
  function Insertar_Ctas_Cierre_SP($InsCta, $Valor, $Trans_No)
  {
    if (strlen($InsCta) > 1 && $Valor != 0) {
      $conn = new db();
      $parametros = array(
        array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
        array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
        array(&$_SESSION['INGRESO']['modulo_'], SQLSRV_PARAM_IN),
        array(&$_SESSION['INGRESO']['CodigoU'], SQLSRV_PARAM_IN),
        array(&$InsCta, SQLSRV_PARAM_IN),
        array(&$Valor, SQLSRV_PARAM_IN),
        array(&$Trans_No, SQLSRV_PARAM_IN),
      );
      $sql = "EXEC sp_Insertar_Ctas_Cierre @Item=?, @Periodo=?, @NumModulo=?, @Usuario=?, @Codigo=?, @Valor=?, @TransNo=?";
      return $conn->ejecutar_procesos_almacenados($sql,$parametros);
    }
  }

  function Productos_Cierre_Caja_SP($FechaDesde, $FechaHasta)
  {
    $FechaIniSP = BuscarFecha($FechaDesde);
    $FechaFinSP = BuscarFecha($FechaHasta);
    $conn = new db();
    $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$FechaIniSP, SQLSRV_PARAM_IN),
      array(&$FechaFinSP, SQLSRV_PARAM_IN),
    );
    $sql = "EXEC sp_Productos_Cierre_Caja @Item=?, @Periodo=?,@FechaDesde=?, @FechaHasta=?";
    return $conn->ejecutar_procesos_almacenados($sql,$parametros);
  }

  function Actualizar_Abonos_Facturas_SP($TFA, $SaldoReal=false, $PorFecha = false)
  {
    $FechaCorte = $TFA['Fecha_Corte'];
    $FechaIni = $TFA['Fecha_Desde'];
    $FechaFin = $TFA['Fecha_Hasta'];
    $FechaSistema = date('Y-m-d');
    $FechaCorte = strtotime($FechaCorte) ? BuscarFecha($FechaCorte) : BuscarFecha($FechaSistema);
    $FechaIni = strtotime($FechaIni) ? BuscarFecha($FechaIni) : BuscarFecha($FechaSistema);
    $FechaFin = strtotime($FechaFin) ? BuscarFecha($FechaFin) : BuscarFecha($FechaSistema);
    $SaldoReal = ($FechaCorte == BuscarFecha($FechaSistema)) ? true : false;
    $ExisteErrores = 0;

    $conn = new db();
    $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['modulo_'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['CodigoU'], SQLSRV_PARAM_IN),
      array(&$TFA['TC'], SQLSRV_PARAM_IN),
      array(&$TFA['Serie'], SQLSRV_PARAM_IN),
      array(&$TFA['Factura'], SQLSRV_PARAM_IN),
      array(&$FechaCorte, SQLSRV_PARAM_IN),
      array(&$FechaIni, SQLSRV_PARAM_IN),
      array(&$FechaFin, SQLSRV_PARAM_IN),
      array(&$SaldoReal, SQLSRV_PARAM_IN),
      array(&$PorFecha, SQLSRV_PARAM_IN),
      array(&$ExisteErrores, SQLSRV_PARAM_INOUT),
    );
    $sql = "EXEC sp_Actualizar_Abonos_Facturas @Item=?, @Periodo=?,@NumModulo=?, @Usuario=?, @TC=?, @Serie=?, @Factura=?, @FechaCorte=?, @FechaDesde=?, @FechaHasta=?, @SaldoReal=?, @PorFecha=?, @ExisteErrores=?";
    $exec = $conn->ejecutar_procesos_almacenados($sql,$parametros);
    if($exec){
      return compact("ExisteErrores");
    }else{
      return $exec;
    }
  }

  function Actualizar_Datos_Representantes_SP($MasGrupos = false)
  {
    $conn = new db();
    $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$MasGrupos, SQLSRV_PARAM_IN),
    );
    $sql = "EXEC sp_Actualizar_Datos_Representantes @Item=?, @Periodo=?,@MasGrupos=?";
    return $conn->ejecutar_procesos_almacenados($sql,$parametros);
  }

  function FechaValida($NomBox, $ChequearCierreMes = false) {
    $conn = new db();
    $AdoCierre = [];
    $DiaV = 0;
    $MesV = 0;
    $AnioV = 0;
    $ErrorFecha = false;
    $NoMes = 0;
    $Anio = "";
    $sSQL1 = "";
    $FechaIni1 = "";
    $FechaFin1 = "";
    $FechaSistema = date('Y-m-d');
    $MsgBox = "";
    
    $Periodo_Contable = $_SESSION['INGRESO']['periodo'];
    $NumEmpresa = $_SESSION['INGRESO']['item'];

    //Empezamos a verificar la fecha ingresada
    $ErrorFecha = false;
    if ($NomBox == 'LimpiarFechas') {
        $NomBox = $FechaSistema;
    }
    $NomBox = date('d-m-Y', strtotime($NomBox));
    $DiaV = intval(substr($NomBox, 0, 2));
    $MesV = intval(substr($NomBox, 3, 2));
    $AnioV = intval(substr($NomBox, 6, 4));
    if ($AnioV <= 1900) {
        $ErrorFecha = true;   // AnioV = 2000
    }
    if ($AnioV >= date('Y') + 8) {
        $ErrorFecha = true;  // AnioV = 2000
    }
    //MsgBox AnioV
    $timestamp = strtotime($NomBox);
    if (!($timestamp !== false && checkdate(date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp)))) {
        $ErrorFecha = true;
    }

    //Resultado Final de la verificacion de la Fecha ingresada
    $Cadena = "";
    if ($ErrorFecha) {
        $Cadena = "ESTA INCORRECTA" . PHP_EOL;
    } else {
      //Averiguamos si esta cerrado el mes de procesamiento
      $Anio = date('Y', strtotime($NomBox));
      $FechaCierre = PrimerDiaMes($FechaSistema, "d/m/Y");
      $FechaFin1 = BuscarFecha($NomBox);
      $sSQL1 = "SELECT * " .
             "FROM Fechas_Balance " .
             "WHERE Periodo = '" . $Periodo_Contable . "' " .
           "AND Item = '" . $NumEmpresa . "' " .
           "AND Cerrado = 0 " .
           "AND Fecha_Inicial <= '" . $FechaFin1 . "' " .
           "AND Fecha_Final >= '" . $FechaFin1 . "' " .
           "AND MidStrg(Detalle,1,4) = '" . $Anio . "' " .
           "ORDER BY Fecha_Inicial ";
      $sSQL1 = CompilarSQL($sSQL1);
      $AdoCierre = $conn->datos($sSQL1);
      if(count($AdoCierre) > 0){       
        $FechaCierre = $AdoCierre[0]["Fecha_Inicial"];
      }

      //Chequea si es necesario cerrar el mes
      if ($ChequearCierreMes) {
        //Compara la fecha de NomBox con la fecha de cierre del mes
        if (strtotime($NomBox) < strtotime($FechaCierre)) {
          $ErrorFecha = true;
          $Cadena .= "ES INFERIOR A LA DEL CIERRE DEL MES" . PHP_EOL;
        }
      }

      if ($AnioV > 2050) {
          $Cadena .= "ES SUPERIOR A LA PERMITIDA POR EL SISTEMA" . PHP_EOL;
          $ErrorFecha = true;
      }
      //Carga la tabla de Porcentaje IVA
      $sSQL1 = "SELECT * " .
               "FROM Tabla_Por_ICE_IVA " .
               "WHERE IVA <> 0 " .
               "AND Fecha_Inicio <= '" . $FechaFin1 . "' " .
               "AND Fecha_Final >= '" . $FechaFin1 . "' " .
               "ORDER BY Porc DESC ";
      $sSQL1 = CompilarSQL($sSQL1);
      $AdoStrCnn = $conn->datos($sSQL1);
      if(count($AdoStrCnn) > 0){
        $_SESSION['INGRESO']['porc'] = number_format($AdoStrCnn[0]["Porc"] / 100, 2, '.', '');
      }
    }

    if ($ErrorFecha) {
      $MsgBox =  "LA FECHA QUE ESTA INTENTANDO INGRESAR" . PHP_EOL . PHP_EOL .
      $Cadena . PHP_EOL .
      "CONSULTE AL ADMINISTRADOR DEL SISTEMA" . PHP_EOL . PHP_EOL .
      "PARA SOLUCIONAR EL INCONVENIENTE";
    }

    return ['ErrorFecha' =>$ErrorFecha, 'MsgBox' =>$MsgBox];
  }

  function MidStrg($Cadena, $InicioStr, $CantStr = null) {
    if (strlen($Cadena) > 0 && $CantStr > 0) {
        if ($InicioStr > 0) {
            $Resultado = substr($Cadena, $InicioStr, $CantStr);
        } else {
            $Resultado = substr($Cadena, 0, $CantStr);
        }
    } else {
        $Resultado = "";
    }
    return $Resultado;
  }

  function Presenta_Errores_Facturacion_SP($FechaDesde, $FechaHasta)
  {
    $FechaIniSP = BuscarFecha($FechaDesde);
    $FechaFinSP = BuscarFecha($FechaHasta);
    $ExisteErrores = 0;
    $conn = new db();
    $parametros = array(
      array(&$_SESSION['INGRESO']['item'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['periodo'], SQLSRV_PARAM_IN),
      array(&$FechaIniSP, SQLSRV_PARAM_IN),
      array(&$FechaFinSP, SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['Dec_Costo'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['CodigoU'], SQLSRV_PARAM_IN),
      array(&$_SESSION['INGRESO']['modulo_'], SQLSRV_PARAM_IN),
      array(&$ExisteErrores, SQLSRV_PARAM_INOUT),
    );
    $sql = "EXEC sp_Presenta_Errores_Facturacion @Item=?, @Periodo=?,@FechaDesde=?, @FechaHasta=?, @DecCosto=?, @Usuario=?, @NumModulo=?, @ExisteErrores=?";
    $exec = $conn->ejecutar_procesos_almacenados($sql,$parametros);
    if($exec){
      return compact("ExisteErrores");
    }else{
      return $exec;
    }
  }
  // FIN CIERRE DE CAJA
  function ULCase($textoConversion) {
    $textoULCase = strtolower($textoConversion);
    if ($textoULCase == "") {
        $textoULCase = G_NINGUNO;
    }
    $cadAux = "";
    $mayusc = true;
    for ($ti = 0; $ti < strlen($textoULCase); $ti++) {
        $caracter = substr($textoULCase, $ti, 1);
        if ($mayusc) {
            $caracter = strtoupper($caracter);
            $mayusc = false;
        }
        $cadAux .= $caracter;
        if ($caracter == " ") {
            $mayusc = true;
        }
    }
    return $cadAux;
}

function SetAdoFieldsWhere($Campo, $Valor)
{
  $_SESSION['SetAdoAddNew']['SetAdoWhere'][$Campo] = $Valor;
}

function SetAdoUpdateGeneric(){
  if(!isset($_SESSION['SetAdoAddNew']['SetAdoWhere']) || count($_SESSION['SetAdoAddNew']['SetAdoWhere'])<1){
    echo 'No es posible hacer un Update sin un condicional';
    return false;
  }
  $DatosTabla = $_SESSION['SetAdoAddNew'][0];
  $IndDato = 0;

  $DatosSelect = "UPDATE  ".$DatosTabla[0]['Campo']." SET ";

  for ($IndDato = 1; $IndDato <= $DatosTabla[0]['Ancho']; $IndDato++) { 
    if ($DatosTabla[$IndDato]['Campo'] != 'ID' && $DatosTabla[$IndDato]['Update']) {
      $DatosSelect .= validateTypeFieldAssign($DatosTabla[$IndDato]).",";
    }
  }

  if (substr($DatosSelect, -1) === ',') {
    $DatosSelect = rtrim($DatosSelect, ',');
  }

  $DatosSelect .=" WHERE ";
  foreach ($_SESSION['SetAdoAddNew']['SetAdoWhere'] as $key => $value) {
    for ($IndDato = 1; $IndDato <= $DatosTabla[0]['Ancho']; $IndDato++) { 
      if ($DatosTabla[$IndDato]['Campo'] == $key) {
        $DatosTabla[$IndDato]['Valor'] = $value;
        $DatosSelect .= validateTypeFieldAssign($DatosTabla[$IndDato])." AND ";
        break;
      }
    }
  }

  if (substr($DatosSelect, -4) === 'AND ') {
    $DatosSelect = rtrim($DatosSelect, 'AND ');
  }

  $DatosSelect = CompilarSQL($DatosSelect);
  unset($_SESSION['SetAdoAddNew']['SetAdoWhere']);
  return Ejecutar_SQL_SP($DatosSelect);
}

function validateTypeFieldAssign($DatoTablaI)
{
  switch ($DatoTablaI['Tipo']) {
    case 'bit':
      if (is_null($DatoTablaI['Valor'])) {
        $DatoTablaI['Valor'] = 0;
      }
      if (is_bool($DatoTablaI['Valor']) && $DatoTablaI['Valor']) {
        $DatoTablaI['Valor'] = 1;
      }
      if ($DatoTablaI['Valor'] == G_NINGUNO || $DatoTablaI['Valor'] == "") {
        $DatoTablaI['Valor'] = 0;
      }
      if ($DatoTablaI['Valor'] < 0) {
        $DatoTablaI['Valor'] = 1;
      }
      if ($DatoTablaI['Valor'] > 1) {
        $DatoTablaI['Valor'] = 1;
      }
      $DatosSelect    = $DatoTablaI['Campo'] . " = " .(int)$DatoTablaI['Valor'];
      break;
    case 'nvarchar':
    case 'ntext':
        if (is_null($DatoTablaI['Valor']) || empty($DatoTablaI['Valor'])) {
        $DatoTablaI['Valor'] = G_NINGUNO;
      }
      if ($DatoTablaI['Campo'] == "Periodo" && $DatoTablaI['Valor'] == G_NINGUNO) {
        $DatoTablaI['Valor'] = $_SESSION['INGRESO']['periodo'];
      }
      if ($DatoTablaI['Campo'] == "Item" && $DatoTablaI['Valor'] == G_NINGUNO) {
        $DatoTablaI['Valor'] = $_SESSION['INGRESO']['item'];
      }
      if ($DatoTablaI['Campo'] == "CodigoU" && $DatoTablaI['Valor'] == G_NINGUNO) {
        $DatoTablaI['Valor'] = $_SESSION['INGRESO']['CodigoU'];
      }
      $DatosSelect = $DatoTablaI['Campo'] . " = '" . $DatoTablaI['Valor'] . "'";
      break;
    case 'date':
      if (is_null($DatoTablaI['Valor'])) {
        $DatoTablaI['Valor'] = date("Ymd");
      }
      $DatosSelect = $DatoTablaI['Campo'] . " = #" . BuscarFecha((string)$DatoTablaI['Valor']) . "#";
      break;
    case 'datetime':
    case 'datetime2':
    case 'datetimeoffset':
    case 'smalldatetime':
      if (is_null($DatoTablaI['Valor'])) {
        $DatoTablaI['Valor'] = date("YmdHis");
      }
      $DatosSelect = $DatoTablaI['Campo'] . " = #" . BuscarFecha((string)$DatoTablaI['Valor']) . "#";
      break;
    case 'tinyint':
    case 'int':
    case 'bigint':
    case 'smallint':
      if (is_null($DatoTablaI['Valor'])) {
          $DatoTablaI['Valor'] = 0;
      }
      $DatosSelect = $DatoTablaI['Campo'] . ' = ' . (int) $DatoTablaI['Valor'];
      break;
    case 'real':
    case 'float':
    case 'numeric':
    case 'money':
    case 'decimal':
      if (is_null($DatoTablaI['Valor'])) {
        $DatoTablaI['Valor'] = 0;
      }
      $DatosSelect = $DatoTablaI['Campo'] . ' = ' .(float) $DatoTablaI['Valor'];
      break;
  }

  return $DatosSelect;
}

function Cambio_De_Codigo($NombreTabla, $Campo, $CodOld, $CodNew) {
  if ($CodNew != $CodOld) {
    $sSQL = "UPDATE " . $NombreTabla . " " .
            "SET " . $Campo . " = '" . $CodNew . "' " .
            "WHERE " . $Campo . " = '" . $CodOld . "'";
    Ejecutar_SQL_SP($sSQL);
  }
}

function Procesar_Renumerar_CIRUC_JuntaAgua($CodigoCliente, $CI_RUC_Actual) {
  $conn = new db();
  $sSQL = "SELECT Codigo, CI_RUC, TD, Cliente, Direccion, Grupo " .
          "FROM Clientes " .
          "WHERE Codigo = '$CodigoCliente' ".
          "ORDER BY TD, Cliente, Grupo, Codigo ";
  $result = $conn->datos($sSQL);
  if (count($result) > 0) {
    foreach ($result as $key => $fields) {
      
      $Codigo1 = $fields["Codigo"];
      $CadenaDV = Digito_verificador($CI_RUC_Actual);

      if(!isset($CadenaDV['Codigo_RUC_CI']) || !isset($CadenaDV['Tipo_Beneficiario']) || !isset($CadenaDV['RUC_CI'] )){
        return array('rps' => false, "mensaje" =>"No fue posible validar el Digito Verificador");
      }
      $Codigo2 = $CadenaDV['Codigo_RUC_CI'];
      
      $sSQL = "UPDATE Clientes " .
               "SET TD = '" . $CadenaDV['Tipo_Beneficiario'] . "', 
               CI_RUC = '" . $CadenaDV['RUC_CI'] . "' ".
               "WHERE Codigo = '" . $Codigo1 . "'";
      Ejecutar_SQL_SP($sSQL);
                
      Cambio_De_Codigo("Acceso_Empresa", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Accesos", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Catalogo_CxCxP", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Catalogo_Rol_Pagos", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Catalogo_Rol_Rubros", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Catalogo_SubCtas", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Clientes", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Clientes_Datos_Extras", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Clientes_Facturacion", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Clientes_Matriculas", "Codigo", $Codigo1, $Codigo2);
        Cambio_De_Codigo("Clientes_Matriculas", "Cedula_R", $CI_RUC_Actual, $CadenaDV['RUC_CI']);
      Cambio_De_Codigo("Comprobantes", "Codigo_B", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Detalle_Factura", "CodigoC", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Facturas", "CodigoC", $Codigo1, $Codigo2);
        Cambio_De_Codigo("Facturas", "RUC_CI", $CI_RUC_Actual, $CadenaDV['RUC_CI']);
      Cambio_De_Codigo("Trans_Abonos", "CodigoC", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Actas", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Activos", "Codigo_R", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Aduanas", "CodigoC", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Air", "IdProv", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Asistencia", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Comision", "CodigoC", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Compras", "IdProv", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Cuotas", "CodigoC", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Exportaciones", "IdFiscalProv", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Fideicomiso", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Fletes", "CodigoC", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Gastos_Caja", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Importaciones", "IdFiscalProv", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Kardex", "Codigo_P", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Memos", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Memos", "CC1", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Memos", "CC2", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Notas", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Notas_Grado", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Pedidos", "CodigoC", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Promedios", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Rol_de_Pagos", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Rol_Horas", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Rol_Pagos", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_SubCtas", "Codigo", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Ticket", "CodigoC", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Trans_Ventas", "IdProv", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Transacciones", "Codigo_C", $Codigo1, $Codigo2);
      Cambio_De_Codigo("Prestamos", "Cuenta_No", $Codigo1, $Codigo2);

      return array('rps' => true, "mensaje" =>"Proceso Terminado", "codigoCliente" =>$Codigo2);
    }
  }
    
  return array('rps' => false, "mensaje" =>"Cliente no encontrado", "codigoCliente" =>$CodigoCliente);
}

function calcularValorRango($valor1, $valor2)
{
  return abs($valor1 - $valor2) + 1;
}

?>
