<?php 
require_once(dirname(__DIR__,2)."/db/db1.php");
require_once(dirname(__DIR__,2)."/funciones/funciones.php");
	
/**
 * 
 */
class notas_creditoM
{
	private $db;
	function __construct()
	{		
      $this->db = new db();
	}



	function cargar_tabla($parametro,$tabla = false)
	{
		$sql = "SELECT *
        FROM Asiento_NC
        WHERE Item = '".$_SESSION['INGRESO']['item']."'
        AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
        ORDER BY A_No ";
        if($tabla)
        {
	        $medida = medida_pantalla($_SESSION['INGRESO']['Height_pantalla'])-170;
	        $botones[0] = array('boton'=>'eliminar', 'icono'=>'<i class="fa fa-trash"></i>', 'tipo'=>'danger', 'id'=>'CODIGO,A_No' );
			$tbl = grilla_generica_new($sql,'Transacciones As T,Comprobantes As C,Clientes As Cl','tbl_lib',false,$botones,$check=false,$imagen=false,$border=1,$sombreado=1,$head_fijo=1,$medida);
		}else
		{
			$tbl = $this->db->datos($sql);
		}
       return $tbl;
	}

	function Listar_Facturas_Pendientes_NC($codigo=false)
	{ 
		$sql = "SELECT C.Grupo, C.Codigo, C.Cliente, F.Cta_CxP, SUM(F.Total_MN) As TotFact 
       	FROM Clientes As C, Facturas As F 
       	WHERE F.Item = '".$_SESSION['INGRESO']['item']."' 
       	AND F.Periodo = '".$_SESSION['INGRESO']['periodo']."' 
       	AND NOT F.TC IN ('DO','OP') 
       	AND F.T <> 'A' 
       	AND F.Saldo_MN <> 0 
       	AND C.Codigo = F.CodigoC";
       	if($codigo)
       	{
       		$sql.=" AND C.Codigo = '".$codigo."' ";
       	} 
       	$sql.="GROUP BY C.Grupo, C.Codigo, C.Cliente, F.Cta_CxP 
       	ORDER BY C.Cliente ";
       	return $this->db->datos($sql);
   }

   function DClineas($MBoxFecha,$Cta_CxP)
   {
   	   $sql = "SELECT Codigo, Concepto, CxC 
       FROM Catalogo_Lineas 
       WHERE Fact = 'NC' 
       AND Item = '".$_SESSION['INGRESO']['item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
       AND Fecha <= '".BuscarFecha($MBoxFecha)."' 
       AND Vencimiento >= '".BuscarFecha($MBoxFecha)."' ";
       if(strlen($Cta_CxP) > 2 ){ $sql.=" AND '".$Cta_CxP."' IN (CxC,CxC_Anterior) ";}
	  	$sql.=" ORDER BY CxC, Concepto ";
	  	// print_r($sql);die();
	  	return $this->db->datos($sql);
   }

   function delete_asiento_nc()
   {
   	  $sql = "DELETE
	  FROM Asiento_NC 
	  WHERE Item = '".$_SESSION['INGRESO']['item']."' 
	  AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."' ";
	  return $this->db->String_Sql($sql);
   }

   function catalogo_bodega(){
       $sql = "SELECT * 
       FROM Catalogo_Bodegas 
       WHERE Item = '".$_SESSION['INGRESO']['item']."' 
       AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
       ORDER BY CodBod, Bodega ";
       return $this->db->datos($sql);
	}

	function catalogo_marca()
	{   
	  $sql = "SELECT * 
	  FROM Catalogo_Marcas 
	  WHERE Item = '".$_SESSION['INGRESO']['item']."' 
	  AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
	  ORDER BY Marca ";
	  return $this->db->datos($sql);
	}


	function Catalogo_Cuentas($query)
	{  
	  $sql = "SELECT Codigo,Codigo+SPACE(10)+Cuenta As NomCuenta 
	    FROM Catalogo_Cuentas 
	    WHERE SUBSTRING(Codigo,1,1) IN ('1','2','4','5') 
	    AND DG = 'D' 
	    AND Item = '".$_SESSION['INGRESO']['item']."' 
	    AND Periodo = '".$_SESSION['INGRESO']['periodo']."' ";
	    if($query)
	    {
	    	$sql.=" AND Cuenta like '%".$query."%'";
	    }
	    $sql.="  ORDER BY Codigo ";
	    return $this->db->datos($sql);
	}

	function Catalogo_Productos($query)
	{  
	  $sql = "SELECT Producto, Codigo_Inv, PVP, IVA, Cta_Inventario 
	    FROM Catalogo_Productos 
	    WHERE TC = 'P' 
	    AND Item = '".$_SESSION['INGRESO']['item']."' 
	    AND Periodo = '".$_SESSION['INGRESO']['periodo']."'";
	    if($query)
	    {
	    	$sql.=" AND Producto like '%".$query."%'";
	    } 
	    $sql.=" ORDER BY Producto ";
	    return $this->db->datos($sql);
	}

	function DCTC($CodigoC){
  	// 'MsgBox sSQL
	  $sql = "SELECT TC 
	       FROM Facturas 
	       WHERE Item = '".$_SESSION['INGRESO']['item']."' 
	       AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
	       AND CodigoC = '".$CodigoC."'
	       AND T = '".G_PENDIENTE."' 
	       AND TC <> 'OP' 
	       GROUP BY TC 
	       ORDER BY TC ";
	    return $this->db->datos($sql);
	  // SelectDB_Combo DCTC, AdoTC, sSQL, "TC"
	  // If AdoTC.Recordset.RecordCount <= 0 Then MsgBox "Este Cliente no ha empezado a generar facturas"
	}
  	
  	function DCSerie($TC,$CodigoC)
  	{
	   $sql = "SELECT Serie 
	      FROM Facturas 
	      WHERE Item = '".$_SESSION['INGRESO']['item']."' 
	      AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
	      AND CodigoC = '".$CodigoC."' 
	      AND TC = '".$TC."' 
	      AND T = '".G_PENDIENTE."' 
	      GROUP BY Serie 
	      ORDER BY Serie ";
	      return $this->db->datos($sql);
  	}

  	function DCFactura($Serie,$TC,$CodigoC)
  	{
	   $sql = "SELECT Factura 
	     FROM Facturas 
	     WHERE Item = '".$_SESSION['INGRESO']['item']."' 
	     AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
	     AND CodigoC = '".$CodigoC."' 
	     AND TC = '".$TC."' 
	     AND Serie = '".$Serie."' 
	     AND T = '".G_PENDIENTE."' 
	     AND Saldo_MN > 0 
	     GROUP BY Factura 
	     ORDER BY Factura ";
	     return $this->db->datos($sql);

	}

	function Factura_detalle($Factura,$Serie,$TC,$CodigoC)
	{
	  $sql = "SELECT T,Fecha,Cta_CxP,Cod_CxC,Porc_IVA,Total_MN,Saldo_MN,IVA,Autorizacion 
	     FROM Facturas 
	     WHERE Item = '".$_SESSION['INGRESO']['item']."' 
	     AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
	     AND TC = '".$TC."' 
	     AND Serie = '".$Serie."' 
	     AND Factura = ".$Factura." 
	     AND T <> '".G_ANULADO."' 
	     AND Saldo_MN > 0 
	     ORDER BY Autorizacion ";
	     // print_r($sql);die();
	     return $this->db->datos($sql);
	}

	function lineas_factura($Factura,$Serie,$TC,$Autorizacion)
	{
		$sql = "SELECT Codigo,Cantidad,Precio,Producto,Total,Total_Desc,Total_Desc2,Total_IVA,CodBodega,CodMarca,Cod_Ejec,Porc_C,Porc_IVA,Mes_No,Mes,Ticket 
          	FROM Detalle_Factura 
          	WHERE Item = '".$_SESSION['INGRESO']['item']."' 
          	AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
          	AND TC = '".$TC."' 
          	AND Serie = '".$Serie."' 
          	AND Factura = ".$Factura." 
          	AND Autorizacion = '".$Autorizacion."' 
          	ORDER BY ID ";
        return $this->db->datos($sql);
	}

	function delete_Detalle_Nota_Credito($serieNC,$Nota_Credito)
	{
	  $sql = "DELETE 
	         FROM Detalle_Nota_Credito 
	         WHERE Item ='".$_SESSION['INGRESO']['item']."' 
	         AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
	         AND Serie = '".$serieNC."' 
	         AND Secuencial = ".$Nota_Credito; 
	  	return $this->db->String_Sql($sql);
	}

	function Actualizar_facturas_trans_abonos($TxtConcepto,$FA)
	{
	    $sql = "UPDATE Facturas
        	SET Nota = '".$TxtConcepto."'
        	WHERE Item = '".$_SESSION['INGRESO']['item']."' 
        	AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
        	AND Factura = ".$FA['Factura']."
        	AND TC = '".$FA['TC']."'
        	AND Serie = '".$FA['Serie']."'
            AND Autorizacion = '".$FA['Autorizacion']."';";
	            
        $sql2 = "UPDATE Trans_Abonos
            SET Serie_NC = '".$FA['Serie_NC']."',
            Autorizacion_NC = '".$FA['Autorizacion_NC']."',
            Secuencial_NC = '".$FA['Nota_Credito']."',
            Clave_Acceso_NC = '".G_NINGUNO."',
            Estado_SRI_NC = 'CG'
            WHERE Item = '".$_SESSION['INGRESO']['item']."' 
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
            AND Factura = ".$FA['Factura']."
            AND TP = '".$FA['TC']."'
            AND Serie = '".$FA['Serie']."'
            AND Autorizacion = '".$FA['Autorizacion']."'; ";
	  	return $this->db->String_Sql($sql.$sql2);
	}


	function delete_asientonNC($codigo=False,$A_no=false)
	{
		$sql = "DELETE 
        FROM Asiento_NC
        WHERE Item =  '".$_SESSION['INGRESO']['item']."' 
        AND CodigoU =  '".$_SESSION['INGRESO']['CodigoU']."'";
        if($codigo)
        {
        	$sql.=" AND CODIGO = '".$codigo."'";
        }
        if($A_no)
        {
         $sql.=" AND A_No = '".$A_no."'";
     	}
	  	return $this->db->String_Sql($sql);
	}

}
?>