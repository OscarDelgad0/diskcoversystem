<?php 
include(dirname(__DIR__,1).'/funciones/funciones.php');
require_once(dirname(__DIR__,2)."/lib/fpdf/reporte_de.php");
require_once("../db/db1.php");

/**
 * 
 */
class modalesM
{
	private $db;	
	function __construct()
	{
		$this->db = new db();
	}

	function buscar_cliente($ci=false,$nombre=false)
	{
		$sql="SELECT Cliente AS nombre, CI_RUC as id,TD, email,Direccion,Telefono,Codigo,Grupo,Ciudad,Prov,DirNumero,ID,FA
		    FROM Clientes  C
		    WHERE T <> '.' ";

		    if($nombre)
		    {
		    	$sql.=" AND  Cliente LIKE '%".$nombre."%' ";
		    }
		    if($ci)
		    {
		    	$sql.=" AND CI_RUC LIKE '".$ci."%' ";
		    }	
		$sql.=" ORDER BY Cliente OFFSET 0 ROWS FETCH NEXT 25 ROWS ONLY;";

		// print_r($sql);die();
		$datos = $this->db->datos($sql);
		return $datos;

	}


function DLGasto($SubCta,$query=false)
{ 
    $sql = "SELECT Codigo+'  .  '+Cuenta As Nombre_Cta, TC, Codigo
            FROM Catalogo_Cuentas
            WHERE Item = '" .$_SESSION['INGRESO']['item']."'
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
            AND DG = 'D' ";
            if($query)
            {
            	$sql.=" AND Cuenta LIKE '%".$query."%'";
            }
	  if($SubCta == "C"){ $sql.=" AND TC IN ('CC','I') "; }else{ $sql.=" AND TC IN ('CC','G') ";}
	  $sql.=" ORDER BY Codigo ";
	  $datos = $this->db->datos($sql);
	  return $datos;
 }

 function DLSubModulo($SubCta,$query=false)
 {  
   $sql= "SELECT Detalle, Codigo 
        FROM Catalogo_SubCtas 
        WHERE Item = '".$_SESSION['INGRESO']['item']."' 
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."'";
        if($query)
        {
        	$sql.=" AND Detalle LIKE '%".$query."%'";
        }
	  if($SubCta == "C" ){ $sql.=" AND TC IN ('CC','I') "; }else{ $sql.=" AND TC IN ('CC','G') ";}
	  $sql.=" ORDER BY Detalle ";
	  $datos = $this->db->datos($sql);
	  return $datos;
}

function DLCxCxP($SubCta,$query=false)
{	  
  $sql = "SELECT Codigo+'  .  '+Cuenta As Nombre_Cta, Codigo
       	 FROM Catalogo_Cuentas
       	 WHERE Item = '".$_SESSION['INGRESO']['item']."'
       	 AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
       	 AND DG = 'D'
       	 AND TC = '".$SubCta."'";
       	 if($query)
        {
        	$sql.=" AND Cuenta LIKE '%".$query."%'";
        }
       	 $sql.=" ORDER BY Codigo ";
       	   $datos = $this->db->datos($sql);
	  return $datos;
}


 function buscar_cta($cta)
 {
 	$sql = "SELECT * 
 			FROM Ctas_Proceso 
 			WHERE Detalle = '".$cta."' 
 			AND Periodo = '".$_SESSION['INGRESO']["periodo"]."' 
 			AND Item = '".$_SESSION['INGRESO']['item']."'";
 	$datos = $this->db->datos($sql);
	return $datos;
 }

     function LeerCta($CodigoCta)
	{

		$Cuenta = G_NINGUNO;
		$Codigo = G_NINGUNO;
		$TipoCta = "G";
		$SubCta = "N";
		$TipoPago = "01";
		$Moneda_US = False;
		$datos= array();
		if (strlen(substr($CodigoCta, 1, 1)) >= 1){
			$sql = "SELECT Codigo, Cuenta, TC, ME, DG, Tipo_Pago
              FROM Catalogo_Cuentas 
              WHERE Codigo = '" .$CodigoCta. "'
              AND Item = '".$_SESSION['INGRESO']['item']."' 
              AND Periodo = '".$_SESSION['INGRESO']['periodo']."'";
          $datos = $this->db->datos($sql);
		  return $datos;
       }

       return $datos;
    }
	function catalogo_Cxcxp($Codigo)
	{
		$sql="SELECT * FROM Catalogo_CxCxP WHERE Codigo = '".$Codigo."' AND TC='P' AND Item = '".$_SESSION['INGRESO']['item']."' AND Periodo = '".$_SESSION['INGRESO']['periodo']."'";
		
	   $datos = $this->db->datos($sql);
       return $datos;

	}

	function reporte_retencion($numero,$TP,$retencion,$serie,$imp=0)
	{
		$datos = array();
		$detalle = array(); 
		$cliente = array();
		$TFA = array();


		$sql2 = "SELECT * FROM lista_tipo_contribuyente WHERE RUC = '".$_SESSION['INGRESO']['RUC']."'";
	    $tipo_con = $this->db->datos($sql2, 'MYSQL');
		

		$sql = "SELECT C.Cliente,C.CI_RUC,C.TD,C.Direccion,C.Email,C.Ciudad,C.DirNumero,C.Telefono,TC.* 
        FROM Trans_Compras As TC,Clientes As C 
        WHERE TC.Item = '".$_SESSION['INGRESO']['item']."'
        AND TC.Periodo = '".$_SESSION['INGRESO']['periodo']."'
        AND TC.Numero = ".$numero." 
        AND TC.TP = '".$TP."' 
        AND TC.SecRetencion = ".$retencion."
        AND TC.Serie_Retencion = '".$serie."' 
        AND TC.IdProv = C.Codigo 
        ORDER BY Cta_Servicio,Cta_Bienes";
	   $datos = $this->db->datos($sql);

	   // print_r($datos);die();
	   if(count($datos)>0)
	   {
	   	 $TFA['Fecha'] = $datos[0]["Fecha"];
         $TFA['Cliente'] = $datos[0]["Cliente"];
         $TFA['Razon_Social'] = $datos[0]["Cliente"];
         $TFA['CI_RUC'] = $datos[0]["CI_RUC"];
         $TFA['RUC_CI'] = $datos[0]["CI_RUC"];
         $TFA['DireccionC'] = $datos[0]["Direccion"];
        // 'TFA.Serie_R
        // 'TFA.Retencion
         $TFA['Fecha_Aut'] = $datos[0]["Fecha_Aut"];
         $TFA['Hora'] = $datos[0]["Hora_Aut"];
         $TFA['Autorizacion_R'] = $datos[0]["AutRetencion"];
         $TFA['ClaveAcceso'] = $datos[0]["Clave_Acceso"];
         $TFA['Serie'] = $datos[0]["Establecimiento"].$datos[0]["PuntoEmision"];
         $TFA['Factura'] = $datos[0]["Secuencial"];
         $TFA['Fecha'] = $datos[0]["Fecha"];
         $TFA['Tipo_Comp'] = strval($datos[0]["TipoComprobante"]);
         $FechaTexto = $datos[0]["Fecha"]->format('Y-m-d');
         $EjercicioFiscal = strval($datos[0]["Fecha"]->format('Y'));
         $Porc_IVA = Validar_Porc_IVA($datos[0]["Fecha"]->format('Y-m-d'));
         $ConsultarDetalle = True;
	   }
	  if(count($datos)>0 && count($tipo_con)>0)
	  {
	    $TFA['Tipo_contribuyente'] = $tipo_con;
	  }

	   // print_r($TFA);die();


   // 'Determinamos el Tipo de Comprobante
    $sql = "SELECT Tipo_Comprobante_Codigo, Descripcion 
        FROM Tipo_Comprobante 
        WHERE TC = 'TDC' 
        AND Tipo_Comprobante_Codigo = ".intval($TFA['Tipo_Comp']);
     $datos1 = $this->db->datos($sql);
     if(count($datos1)>0)
     {
     	$TFA['Tipo_Comp'] = $datos1[0]["Descripcion"];
     }
   

 	// print_r($TFA);die();
    // 'Listar las Retenciones de la Fuente
    $sql = "SELECT TIV.Concepto,R.* 
    	FROM Trans_Air As R,Tipo_Concepto_Retencion As TIV 
    	WHERE R.Item = '".$_SESSION['INGRESO']['item']."' 
    	AND R.Periodo = '".$_SESSION['INGRESO']['periodo']."' 
    	AND R.Numero = ".$numero." 
    	AND R.TP = '".$TP."' 
    	AND R.SecRetencion = ".$retencion." 
    	AND R.EstabRetencion = '".substr($serie, 0, 3)."' 
    	AND R.PtoEmiRetencion = '".substr($serie, 3, 6)."' 
    	AND R.Tipo_Trans IN ('C','I') 
    	AND TIV.Fecha_Inicio <= '".BuscarFecha($FechaTexto)."' 
    	AND TIV.Fecha_Final >= '".BuscarFecha($FechaTexto)."'
    	AND R.CodRet = TIV.Codigo 
    	ORDER BY R.Cta_Retencion ";
    	$datos2 = $this->db->datos($sql);
   // 'Encabezado Factura

 	// print_r($TFA);
 	// print_r($datos2);
 	// die();    

	  imprimirDocEle_ret($datos,$datos2,'Retencion',$imp);

	}	

	
}
?>