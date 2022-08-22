<?php
include(dirname(__DIR__,2).'/funciones/funciones.php');
include(dirname(__DIR__,3).'/lib/fpdf/reporte_de.php');
@session_start(); 
/**
 * 
 */
class lista_facturasM
{
	private $conn;	
	private $db;
	function __construct()
	{
		$this->conn = cone_ajax();
		$this->db = new db();
	}

	function ingresar_update($datos,$tabla,$campoWhere=false)
	{
		// print_r($datos);die();
		if ($campoWhere) {
			$resp = update_generico($datos,$tabla,$campoWhere);			
		  return $resp;
			
		}else{
	      $resp = insert_generico($tabla,$datos);
	      return $resp;
	  }
	}

 
   function facturas_emitidas_excel($codigo,$reporte_Excel=false,$periodo=false)
   {
   	$cid = $this->conn;
		
		$sql ="SELECT T,TC,Serie,Autorizacion,Factura,Fecha,SubTotal,Con_IVA,IVA,Descuento+Descuento2 as Descuentos,Total_MN as Total,Saldo_MN as Saldo,RUC_CI,TB,Razon_Social  FROM Facturas 
       WHERE CodigoC ='".$codigo."'
      AND Item = '".$_SESSION['INGRESO']['item']."' ";
       if($periodo && $periodo!='.')
       {
       	 $sql.=" AND Periodo BEETWEN '01/01/".$periodo."' AND '31/12".$periodo."'";
       }else
       {
       	$sql.=" AND Periodo =  '".$_SESSION['INGRESO']['periodo']."' ";
       }

       $sql.="ORDER BY Fecha DESC"; 

      // print_r($sql);die();

       $stmt = sqlsrv_query($cid, $sql);
	   if( $stmt === false)  
	   {  
		 echo "Error en consulta PA.\n";  
		 return '';
		 die( print_r( sqlsrv_errors(), true));  
	   }

	   $result = array();	
	   while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) 
	   {
		$result[] = $row;
		//echo $row[0];
	   }
	   if($reporte_Excel==false)
	   {
	   	  return $result;
	   }else
	   {
	   	 $stmt1 = sqlsrv_query($cid, $sql);
	     exportar_excel_generico($stmt1,'Facturasemitidas',null,null);

	   }

   }

   function facturas_perido($codigo)
   {
   	    $sql="SELECT Periodo
			FROM  Facturas
			WHERE CodigoC = '".$codigo."'
			GROUP BY Periodo
			ORDER BY Periodo";
			return $this->db->datos($sql);

   }

    function facturas_emitidas_tabla($codigo,$periodo=false)
   {
   	$cid = $this->conn;
		
		$sql ="SELECT T,TC,Serie,Autorizacion,Factura,Fecha,SubTotal,Con_IVA,IVA,Descuento+Descuento2 as Descuentos,Total_MN as Total,Saldo_MN as Saldo,RUC_CI,TB,Razon_Social,CodigoC,ID 
		FROM Facturas 
		WHERE CodigoC ='".$codigo."' 
		 AND CodigoC <> ''
		AND Item = '".$_SESSION['INGRESO']['item']."'";
       if($periodo && $periodo!='.')
       {
       	 $sql.= " AND Periodo BETWEEN '01/01/".$periodo."' AND '31/12/".$periodo."'";
       }else
       {
       	 $sql.=" AND Periodo =  '".$_SESSION['INGRESO']['periodo']."'";
       }

       $sql.="ORDER BY Fecha DESC"; 

      // print_r($sql);die();
		  $stmt = sqlsrv_query($cid, $sql);
	   if( $stmt === false)  
	   {  
		 echo "Error en consulta PA.\n";  
		 return '';
		 die( print_r( sqlsrv_errors(), true));  
	   }
// print_r($sql);die();
	   $datos = array();	
	   while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) 
	   {
		$datos[] = $row;
		//echo $row[0];
	   }

      
       $botones[0] = array('boton'=>'Ver factura','icono'=>'<i class="fa fa-eye"></i>', 'tipo'=>'default', 'id'=>'Factura,Serie,CodigoC');
       // $botones[1] = array('boton'=>'Generar PDF','icono'=>'<i class="fa fa-file-pdf-o"></i>', 'tipo'=>'primary', 'id'=>'ID');
       // $botones[2] = array('boton'=>'Generar EXCEL','icono'=>'<i class="fa fa-file-excel-o"></i>', 'tipo'=>'info', 'id'=>'ID');

        $tbl = grilla_generica_new($sql,'Facturas',false,$titulo=false,$botones,$check=false,$imagen=false,1,1,1,400);
        
       // $tabla = grilla_generica($stmt,null,NULL,'1','2,4,clave');
       return array('datos'=>$datos,'tbl'=>$tbl);
   }

   function pdf_factura($cod,$ser,$ci,$periodo=false)
   {
   	$id='factura_'.$ci;
   	$cid = $this->conn;
   	$sql="SELECT * 
   	FROM Facturas 
   	WHERE Serie='".$ser."' 
   	AND Factura='".$cod."' 
   	AND CodigoC='".$ci."' 
   	AND Item = '".$_SESSION['INGRESO']['item']."' ";
   	if($periodo==false || $periodo =='.')
   	{
	   $sql.=" AND Periodo =  '".$_SESSION['INGRESO']['periodo']."' ";
	}else
	{
      	$sql.=" AND Periodo BETWEEN '01/01/".$periodo."' AND '31/12".$periodo."'";
	}

	// print_r($sql);die();
	$datos_fac = $this->db->datos($sql);

   	$sql1="SELECT * 
   	FROM Detalle_Factura 
   	WHERE Factura = '".$cod."' 
   	AND CodigoC='".$ci."' 
   	AND Item = '".$_SESSION['INGRESO']['item']."'
	AND Periodo =  '".$_SESSION['INGRESO']['periodo']."' ";	
	$detalle_fac = $this->db->datos($sql1);

	$sql2 = "SELECT * FROM lista_tipo_contribuyente WHERE RUC = '".$_SESSION['INGRESO']['RUC']."'";
	$tipo_con = $this->db->datos($sql2, 'MYSQL');
	if(count($datos_fac)>0 && count($tipo_con)>0)
	{
		$datos_fac['Tipo_contribuyente'] = $tipo_con;
	}
	// array_push($datos_fac, $tipo_con);


    $datos_cli_edu=$this->cliente_matri($ci);
	   if($datos_cli_edu != '' && !empty($datos_cli_edu))
	   {
	   		imprimirDocEle_fac($datos_fac,$detalle_fac,$datos_cli_edu,'matr',$id,null,'factura',null,null);
	   }else
	   {
		    $datos_cli_edu=$this->Cliente($ci);
		    imprimirDocEle_fac($datos_fac,$detalle_fac,$datos_cli_edu,$id,null,'factura',null,null);
	   }

   }

    function Cliente($cod,$grupo = false,$query=false,$clave=false)
   {
   	$cid=$this->conn;
	   $sql = "SELECT * from Clientes WHERE FA=1 ";
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
		

	   // print_r($sql);die();
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
   

   function cliente_matri($codigo)
   {
   	$cid=$this->conn;
	   $sql = "SELECT * FROM Clientes_Matriculas WHERE Item = '".$_SESSION['INGRESO']['item']."'
		AND Periodo =  '".$_SESSION['INGRESO']['periodo']."' and Codigo = '".$codigo."'";

		// print_r($sql);die();
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

   function grupos($query)
   {
   	 $cid=$this->conn;
	   $sql = "SELECT DISTINCT Grupo FROM Clientes WHERE FA = '1' AND Grupo <>'.' ";
	   if($query)
	   {
	   	 $sql.=' AND Grupo LIKE "%'.$query.'%" ';
	   }
		// print_r($sql);die();
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
	      }
	      // print_r($result);
	      return $result;
   }

    function Empresa_data()
   {
   	$cid=$this->conn;
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


  
}
?>