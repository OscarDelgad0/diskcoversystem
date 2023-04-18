<?php

require_once(dirname(__DIR__,2)."/db/db1.php");
require_once(dirname(__DIR__,2)."/funciones/funciones.php");
require_once(dirname(__DIR__,3)."/lib/fpdf/reporte_de.php");
require_once(dirname(__DIR__,3)."/lib/phpmailer/enviar_emails.php");

class punto_ventaM
{
	private $db;
  private $email;

	public function __construct(){

      $this->db = new db();
      $this->email = new enviar_emails(); 
      $this->pdf = new cabecera_pdf(); 
  }

  function Listar_Clientes_PV($query)
  {
  	  $sql= "SELECT TOP 100 Cliente,Codigo,CI_RUC,TD,Grupo,Email,T 
        FROM Clientes 
        WHERE Cliente <> '.' 
        AND FA <> 0 ";
        if(!is_numeric($query))
        {
          $sql.=" AND Cliente LIKE '%".$query."%'";
        }else
        {
          $sql.=" AND CI_RUC LIKE '".$query."%'";
        } 
        // $sql.=" UNION 
        // SELECT Cliente,Codigo,CI_RUC,TD,Grupo,Email,T 
        // FROM Clientes 
        // WHERE Codigo = '9999999999' 
        // ORDER BY Cliente ";
        // print_r($sql);die();

     return $this->db->datos($sql);
  }
  function Listar_Clientes_PV_exacto($query)
  {
      $sql= "SELECT TOP 100 Cliente,Codigo,CI_RUC,TD,Grupo,Email,T 
        FROM Clientes 
        WHERE Cliente <> '.' 
        AND FA <> 0 ";
        if(!is_numeric($query))
        {
          $sql.=" AND Cliente LIKE '%".$query."%'";
        }else
        {
          $sql.=" AND CI_RUC = '".$query."'";
        } // print_r($sql);die();

     return $this->db->datos($sql);
  }

  function DCBodega()
  {
  	$sql = "SELECT *
        FROM Catalogo_Bodegas
        WHERE Item = '".$_SESSION['INGRESO']['item']."'
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
        ORDER BY CodBod ";
    return $this->db->datos($sql);
  }
  function DCBanco($query)
  {
  	 $sql= "SELECT Codigo +Space(2)+Cuenta As NomCuenta,Codigo 
       FROM Catalogo_Cuentas 
       WHERE TC IN ('BA','CJ','CP','C','P') 
       AND DG = 'D' 
       AND Item = '".$_SESSION['INGRESO']['item']."'
       AND Periodo =  '".$_SESSION['INGRESO']['periodo']."' ";
       if($query)
       	{
       		$sql.=" AND Cuenta LIKE '%".$query."%'";
       	}
       	$sql.=" ORDER BY Codigo ";
    return $this->db->datos($sql);

  }

  function DCArticulos($Grupo_Inv,$TipoFactura,$query)
  {
  	 $sql = "SELECT Producto,Codigo_Inv,Codigo_Barra 
        FROM Catalogo_Productos 
        WHERE Item = '".$_SESSION['INGRESO']['item']."'
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
        AND TC = 'P' ";
	  if(strlen($Grupo_Inv) > 1){ $sql.="AND MidStrg(Codigo_Inv,1,2) = '".$Grupo_Inv."' ";}
	  // if($TipoFactura == "CP"){
	  //    $sql.=" AND Cta_Inventario = '0' ";
	  // }else{
	  //    $sql.=" AND LEN(Cta_Inventario) > 1 ";
	  // }
	  if($query)
	  {
	  	$sql.=" AND Producto like '%".$query."%'";
	  }
	  $sql.=" ORDER BY Producto,Codigo_Inv "; 

    // print_r($sql);die();
	  return $this->db->datos($sql);
  }

  function DGAsientoF($grilla=false)
  {
  	 $sql= "SELECT * 
       FROM Asiento_F 
       WHERE Item = '".$_SESSION['INGRESO']['item']."'
       AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
       ORDER BY A_No Asc";
       $datos =  $this->db->datos($sql);
       $ln = count($datos);
       $tbl='';
       if($grilla)
       {
       	$botones[0] = array('boton'=>'Eliminar','icono'=>'<i class="fa fa-trash"></i>', 'tipo'=>'danger', 'id'=>'A_No,CODIGO');
       	$tbl = grilla_generica_new($sql,'Asiento_F',false,$titulo=false,$botones,$check=false,$imagen=false,1,1,1,270);
       }
       return array('datos'=>$datos,'tbl'=>$tbl,'ln'=>$ln);
  }

  function catalogo_lineas($TC,$SerieFactura,$emision,$vencimiento,$electronico=false)
  {
  	$sql = "SELECT *
         FROM Catalogo_Lineas
         WHERE Item = '".$_SESSION['INGRESO']['item']."'
         AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
         AND Fact = '".$TC."'
         AND Serie = '".$SerieFactura."'
         AND TL <> 0
         AND CONVERT(DATE,Fecha) <= '".$emision."'
         AND CONVERT(DATE,Vencimiento) >= '".$vencimiento."'";
         if($electronico)
         {
           $sql.=" AND len(Autorizacion)=13";
         }
         $sql.=" ORDER BY Codigo ";
         // print_r($sql);die();
	  return $this->db->datos($sql);
  }

  function catalogo_lineas_($TC,$SerieFactura)
  {
    $sql = "SELECT *
         FROM Catalogo_Lineas
         WHERE Item = '".$_SESSION['INGRESO']['item']."'
         AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
         AND Fact = '".$TC."'
         AND Serie = '".$SerieFactura."'
         AND Autorizacion = '".$_SESSION['INGRESO']['RUC']."'
         AND TL <> 0
         ORDER BY Codigo ";
         // print_r($sql);die();
    return $this->db->datos($sql);

  }

  function ELIMINAR_ASIENTOF($codigo =false,$A_no=false)
  {
  	$sql= "DELETE
        FROM Asiento_F 
        WHERE Item = '".$_SESSION['INGRESO']['item']."'
        AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'";
        if($codigo)
        {
        	$sql.=" AND CODIGO ='".$codigo."'";
        }
        if($A_no)
        {
        	$sql.=" AND A_No ='".$A_no."'";
        }
        // print_r($sql);die();

        return $this->db->String_Sql($sql);
  }

  function delete_factura($TipoFactura,$Factura_No)
  {
  	 $sql = "DELETE
        FROM Detalle_Factura 
        WHERE Factura = ".$Factura_No." 
        AND Item = '".$_SESSION['INGRESO']['item']."' 
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        AND TC = '".$TipoFactura."'; ";
    
     $sql.="DELETE
        FROM Facturas 
        WHERE Factura = ".$Factura_No." 
        AND Item = '".$_SESSION['INGRESO']['item']."' 
        AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        AND TC = '".$TipoFactura."';";
     $this->db->String_Sql($sql);
  }

  function cargar_pedidos_factura($orden,$cliente=false)
  {
    // 'LISTA DE CODIGO DE ANEXOS
     $sql = "SELECT D.*,P.Producto 
     FROM Detalle_Factura  D ,Catalogo_Productos P
     WHERE D.Item = '".$_SESSION['INGRESO']['item']."'
      AND D.Periodo = '".$_SESSION['INGRESO']['periodo']."'
     AND D.Factura = '".$orden."' 
     AND D.CodigoC = '".$cliente."'
     AND D.Item = P.Item
     AND D.Periodo = P.Periodo
    AND D.Codigo = P.Codigo_Inv";
     
     $sql.=" ORDER BY D.ID DESC";
     // print_r($sql);die();

     return $this->db->datos($sql);
       
  }
  function lista_hijos_id($query)
  {
      // $cid = $this->conn;
    // 'LISTA DE CODIGO DE ANEXOS
      $sql = "SELECT CP.Codigo_Inv,CP.Producto,CP.TC,TK.Costo as 'Valor_Total',CP.Unidad, SUM(Entrada-Salida) As Stock_Actual ,CP.Cta_Inventario
        FROM Catalogo_Productos As CP, Trans_Kardex AS TK 
        WHERE CP.Periodo = '".$_SESSION['INGRESO']['periodo']."' 
        AND CP.Item = '".$_SESSION['INGRESO']['item']."'
        AND LEN(CP.Cta_Inventario)>3 
        AND CP.Codigo_Inv LIKE '".$query."' 
        AND TK.T<> 'A' 
        AND CP.Periodo = TK.Periodo 
        AND CP.Item = TK.Item 
        AND CP.Codigo_Inv = TK.Codigo_Inv 
        group by CP.Codigo_Inv,CP.Producto,CP.TC,CP.Valor_Total,CP.Unidad,TK.Costo,CP.Cta_Inventario having SUM(TK.Entrada-TK.Salida) <> 0
        order by CP.Codigo_Inv,CP.Producto,CP.TC,CP.Valor_Total,CP.Unidad,CP.Cta_Inventario";
   
     // print_r($sql);die();
     $datos1 =  $this->db->datos($sql);
     $datos = array();
     foreach ($datos1 as $key => $value) {
        $datos[]=array('id'=>$value['Codigo_Inv'].','.$value['Unidad'].','.$value['Stock_Actual'],'text'=>$value['Producto']);    
     }
       return $datos;
  }

  function pdf_guia_remision_elec($TFA,$nombre_archivo,$periodo=false,$aprobado=false,$descargar=false)
   {
    $res = 1;    
        $sql = "SELECT DF.*,CP.Reg_Sanitario,CP.Marca
        FROM Detalle_Factura As DF, Catalogo_Productos As CP
        WHERE DF.Item = '".$_SESSION['INGRESO']['item']."'
        AND DF.Periodo =  '".$_SESSION['INGRESO']['periodo']."'
        AND DF.TC = '".$TFA['TC']."'
        AND DF.Serie = '".$TFA['Serie']."'
        AND DF.Autorizacion = '".$TFA['Autorizacion']."'
        AND DF.Factura = ".$TFA['Factura']."
        AND LEN(DF.Autorizacion) >= 13
        AND DF.T <> 'A'
        AND DF.Item = CP.Item
        AND DF.Periodo = CP.Periodo
        AND DF.Codigo = CP.Codigo_Inv
        ORDER BY DF.ID,DF.Codigo ";
        // print_r($sql);die();
      $AdoDBDet = $this->db->datos($sql);
      
   // 'Encabezado de la Guia de Remision
      $sql2 = "SELECT F.*,GR.Remision,GR.Comercial,GR.CIRUC_Comercial,GR.Entrega,GR.CIRUC_Entrega,GR.CiudadGRI,GR.CiudadGRF,
        GR.Placa_Vehiculo,GR.FechaGRE,GR.FechaGRI,GR.FechaGRF,GR.Pedido,GR.Zona,GR.Serie_GR,GR.Autorizacion_GR,
        GR.Clave_Acceso_GR,GR.Hora_Aut_GR,GR.Estado_SRI_GR,GR.Error_FA_SRI,GR.Fecha_Aut_GR,GR.Lugar_Entrega 
        FROM Facturas As F, Facturas_Auxiliares As GR 
        WHERE F.Item = '".$_SESSION['INGRESO']['item']."'
        AND F.Periodo =  '".$_SESSION['INGRESO']['periodo']."'
        AND F.TC = '".$TFA['TC']."' 
        AND F.Serie = '".$TFA['Serie']."' 
        AND F.Autorizacion = '".$TFA['Autorizacion']."' 
        AND F.Factura = ".$TFA['Factura']." 
        AND LEN(GR.Autorizacion_GR) >= 13 
        AND GR.Remision > 0 
        AND F.T <> 'A' 
        AND F.Item = GR.Item 
        AND F.Periodo = GR.Periodo 
        AND F.TC = GR.TC
        AND F.Serie = GR.Serie 
        AND F.Autorizacion = GR.Autorizacion 
        AND F.Factura = GR.Factura ";
        // print_r($sql2);die();
      $AdoDBFA = $this->db->datos($sql2);
      // print_r($AdoDBFA);die();

      $tipo_con = Tipo_Contribuyente_SP_MYSQL($_SESSION['INGRESO']['RUC']);

  if(count($AdoDBFA)>0 && count($tipo_con)>0)
  {
    $AdoDBFA['Tipo_contribuyente'] = $tipo_con;
  }
  // array_push($datos_fac, $tipo_con);
    $datos_cli_edu=$this->Cliente($TFA['CodigoC']);
    $archivos = array('0'=>$nombre_archivo.'.pdf','1'=>$TFA['Autorizacion_GR'].'.xml');
    $to_correo = '';
    if(count($datos_cli_edu)>0)
    {
      if($datos_cli_edu[0]['Email']!='.' && $datos_cli_edu[0]['Email']!='')
      {
        $to_correo.= $datos_cli_edu[0]['Email'].',';
      }
      if($datos_cli_edu[0]['Email2']!='.' && $datos_cli_edu[0]['Email2']!='')
      {
        $to_correo.= $datos_cli_edu[0]['Email2'].',';
      }
      if($datos_cli_edu[0]['EmailR']!='.' && $datos_cli_edu[0]['EmailR']!='')
      {
        $to_correo.= $datos_cli_edu[0]['EmailR'].',';
      }
      // $to_correo = substr($to_correo, 0,-1);
    }
    $sucursal = $this->catalogo_lineas_('GR',$TFA['Serie']);
    $forma_pago = $this->DCTipoPago($AdoDBFA[0]['Tipo_Pago']);

    if(count($forma_pago)>0)
    {
      $AdoDBFA[0]['Tipo_Pago'] = $forma_pago[0]['CTipoPago'];
    }

    imprimirDocEle_guia($AdoDBFA,$AdoDBDet,$datos_cli_edu,$nombre_archivo,null,'factura',null,null,$imp=$descargar,$sucursal);
    if($to_correo!='')
    {
      $titulo_correo = 'comprobantes electronicos';
      $cuerpo_correo = 'comprobantes electronico';
      if($aprobado)
      {
        $r = $this->email->enviar_email($archivos,$to_correo,$cuerpo_correo,$titulo_correo,$HTML=false);

        // print_r($r);die();
        return $r;
      }
      // print_r($r);
    }
    return $res;
   }

    function pdf_guia_remision_elec_sin_fac($TFA,$nombre_archivo,$periodo=false,$aprobado=false,$descargar=false)
   {
    $res = 1;    
        $sql = "SELECT DF.*,CP.Reg_Sanitario,CP.Marca
        FROM Detalle_Factura As DF, Catalogo_Productos As CP
        WHERE DF.Item = '".$_SESSION['INGRESO']['item']."'
        AND DF.Periodo =  '".$_SESSION['INGRESO']['periodo']."'
        AND DF.TC = '".$TFA['TC']."'
        AND DF.Serie = '".$TFA['Serie']."'
        AND DF.Autorizacion = '".$TFA['Autorizacion']."'
        AND DF.Factura = ".$TFA['Factura']."
        AND LEN(DF.Autorizacion) >= 13
        AND DF.T <> 'A'
        AND DF.Item = CP.Item
        AND DF.Periodo = CP.Periodo
        AND DF.Codigo = CP.Codigo_Inv
        ORDER BY DF.ID,DF.Codigo ";
        // print_r($sql);die();
      $AdoDBDet = $this->db->datos($sql);
      
   // 'Encabezado de la Guia de Remision
      $sql2 = "SELECT GR.Remision,GR.Comercial,GR.CIRUC_Comercial,GR.Entrega,GR.CIRUC_Entrega,GR.CiudadGRI,GR.CiudadGRF,
            GR.Placa_Vehiculo,GR.FechaGRE,GR.FechaGRI,GR.FechaGRF,GR.Pedido,GR.Zona,GR.Serie_GR,GR.Autorizacion_GR,
            GR.Clave_Acceso_GR,GR.Hora_Aut_GR,GR.Estado_SRI_GR,GR.Error_FA_SRI,GR.Fecha_Aut_GR,GR.Fecha,GR.FechaGRE as 'Fecha_Aut',Autorizacion,Clave_Acceso_GR as 'Clave_Acceso'
            FROM Facturas_Auxiliares As GR 
            WHERE GR.Item = '".$_SESSION['INGRESO']['item']."'
            AND GR.Periodo =  '".$_SESSION['INGRESO']['periodo']."'
            AND GR.TC = '".$TFA['TC']."' 
            AND GR.Serie = '".$TFA['Serie']."' 
            AND GR.Autorizacion = '".$TFA['Autorizacion']."' 
            AND GR.Factura =".$TFA['Factura']." 
            AND Remision = '".$TFA['Remision']."'
            AND LEN(GR.Autorizacion_GR) >= 13 
            AND GR.Remision > 0 ";
        // print_r($sql2);die();
      $AdoDBFA = $this->db->datos($sql2);
      // print_r($AdoDBFA);die();
       $AdoDBFA[0]['Serie']=$TFA['Serie'];
       $AdoDBFA[0]['Factura'] = $TFA['Factura'];
       $AdoDBFA[0]['Factura_Aut']=$TFA['Autorizacion']; 
       $AdoDBFA[0]['Razon_Social']= $TFA['Razon_Social']; 
       $AdoDBFA[0]['RUC_CI']= $TFA['RUC_CI']; 
       $AdoDBFA[0]['Lugar_Entrega']= $TFA['Entrega']; 
       $AdoDBFA[0]['Nota'] = '';
       $AdoDBFA[0]['Direccion_RS'] = $TFA['Direccion_RS'];
       $AdoDBFA[0]['Imp_Mes'] = '.';

      $tipo_con = Tipo_Contribuyente_SP_MYSQL($_SESSION['INGRESO']['RUC']);

  if(count($AdoDBFA)>0 && count($tipo_con)>0)
  {
    $AdoDBFA['Tipo_contribuyente'] = $tipo_con;
  }
  // array_push($datos_fac, $tipo_con);
    $datos_cli_edu=$this->Cliente($TFA['CodigoC']);
    $archivos = array('0'=>$nombre_archivo.'.pdf','1'=>$TFA['Autorizacion_GR'].'.xml');
    $to_correo = '';
    if(count($datos_cli_edu)>0)
    {
      if($datos_cli_edu[0]['Email']!='.' && $datos_cli_edu[0]['Email']!='')
      {
        $to_correo.= $datos_cli_edu[0]['Email'].',';
      }
      if($datos_cli_edu[0]['Email2']!='.' && $datos_cli_edu[0]['Email2']!='')
      {
        $to_correo.= $datos_cli_edu[0]['Email2'].',';
      }
      if($datos_cli_edu[0]['EmailR']!='.' && $datos_cli_edu[0]['EmailR']!='')
      {
        $to_correo.= $datos_cli_edu[0]['EmailR'].',';
      }
      // $to_correo = substr($to_correo, 0,-1);
    }
    $sucursal = $this->catalogo_lineas_('GR',$TFA['Serie']);
    $forma_pago = $this->DCTipoPago('01');

    if(count($forma_pago)>0)
    {
      $AdoDBFA[0]['Tipo_Pago'] = $forma_pago[0]['CTipoPago'];
    }

    imprimirDocEle_guia($AdoDBFA,$AdoDBDet,$datos_cli_edu,$nombre_archivo,null,'factura',null,null,$imp=$descargar,$sucursal);
    if($to_correo!='')
    {
      $titulo_correo = 'comprobantes electronicos';
      $cuerpo_correo = 'comprobantes electronico';
      if($aprobado)
      {
        $r = $this->email->enviar_email($archivos,$to_correo,$cuerpo_correo,$titulo_correo,$HTML=false);

        // print_r($r);die();
        return $r;
      }
      // print_r($r);
    }
    return $res;
   }

  function pdf_factura_elec_rodillo($cod,$ser,$ci,$nombre,$clave_acceso,$periodo=false,$aprobado=false,$descargar=false)
   {
    $res = 1;
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
    AND Serie='".$ser."' 
    AND Item = '".$_SESSION['INGRESO']['item']."'
    AND Periodo =  '".$_SESSION['INGRESO']['periodo']."' "; 
  $detalle_fac = $this->db->datos($sql1);

  // $sql2 = "SELECT * FROM lista_tipo_contribuyente WHERE RUC = '".$_SESSION['INGRESO']['RUC']."'";
  $tipo_con = Tipo_Contribuyente_SP_MYSQL($_SESSION['INGRESO']['RUC']);

  $cliente = 
   $sql2="SELECT * 
    FROM Trans_Abonos 
    WHERE Factura = '".$cod."' 
    AND CodigoC='".$ci."' 
    AND Item = '".$_SESSION['INGRESO']['item']."'
    AND Autorizacion = '".$clave_acceso."'
    AND Periodo =  '".$_SESSION['INGRESO']['periodo']."' "; 
  $detalle_abonos = $this->db->datos($sql2);

  if(count($datos_fac)>0 && count($tipo_con)>0)
  {
    $datos_fac['Tipo_contribuyente'] = $tipo_con;
  }
  // array_push($datos_fac, $tipo_con);
    $datos_cli_edu=$this->Cliente($ci);
    $archivos = array('0'=>$nombre.'.pdf','1'=>$clave_acceso.'.xml');
    $to_correo = '';
    if(count($datos_cli_edu)>0)
    {
      if($datos_cli_edu[0]['Email']!='.' && $datos_cli_edu[0]['Email']!='')
      {
        $to_correo.= $datos_cli_edu[0]['Email'].',';
      }
      if($datos_cli_edu[0]['Email2']!='.' && $datos_cli_edu[0]['Email2']!='')
      {
        $to_correo.= $datos_cli_edu[0]['Email2'].',';
      }
      if($datos_cli_edu[0]['EmailR']!='.' && $datos_cli_edu[0]['EmailR']!='')
      {
        $to_correo.= $datos_cli_edu[0]['EmailR'].',';
      }
      // $to_correo = substr($to_correo, 0,-1);
    }
    $sucursal = $this->catalogo_lineas_('FA',$ser);
    $forma_pago = $this->DCTipoPago($datos_fac[0]['Tipo_Pago']);

    if(count($forma_pago)>0)
    {
      $datos_fac[0]['Tipo_Pago'] = $forma_pago[0]['CTipoPago'];
    }

    $TFA['factura'] = $datos_fac;
    $TFA['lineas'] = $detalle_fac;
    $TFA['CLAVE'] = $datos_fac[0]['Autorizacion'];
    $TFA['factura'][0]['Telefono'] = $datos_cli_edu[0]['Telefono'];
    $TFA['factura'][0]['Email'] =  $datos_cli_edu[0]['Email'];

     $this->pdf->Imprimir_Punto_Venta_Grafico($TFA,0);
   }

  function pdf_factura_elec($cod,$ser,$ci,$nombre,$clave_acceso,$periodo=false,$aprobado=false,$descargar=false)
   {
    $res = 1;
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
    AND Serie='".$ser."' 
    AND Item = '".$_SESSION['INGRESO']['item']."'
    AND Periodo =  '".$_SESSION['INGRESO']['periodo']."' "; 
  $detalle_fac = $this->db->datos($sql1);

  // $sql2 = "SELECT * FROM lista_tipo_contribuyente WHERE RUC = '".$_SESSION['INGRESO']['RUC']."'";
  $tipo_con = Tipo_Contribuyente_SP_MYSQL($_SESSION['INGRESO']['RUC']);

   $sql2="SELECT * 
    FROM Trans_Abonos 
    WHERE Factura = '".$cod."' 
    AND CodigoC='".$ci."' 
    AND Item = '".$_SESSION['INGRESO']['item']."'
    AND Autorizacion = '".$clave_acceso."'
    AND Periodo =  '".$_SESSION['INGRESO']['periodo']."' "; 
  $detalle_abonos = $this->db->datos($sql2);

  if(count($datos_fac)>0 && count($tipo_con)>0)
  {
    $datos_fac['Tipo_contribuyente'] = $tipo_con;
  }
  // array_push($datos_fac, $tipo_con);
    $datos_cli_edu=$this->Cliente($ci);
    $archivos = array('0'=>$nombre.'.pdf','1'=>$clave_acceso.'.xml');
    $to_correo = '';
    if(count($datos_cli_edu)>0)
    {
      if($datos_cli_edu[0]['Email']!='.' && $datos_cli_edu[0]['Email']!='')
      {
        $to_correo.= $datos_cli_edu[0]['Email'].',';
      }
      if($datos_cli_edu[0]['Email2']!='.' && $datos_cli_edu[0]['Email2']!='')
      {
        $to_correo.= $datos_cli_edu[0]['Email2'].',';
      }
      if($datos_cli_edu[0]['EmailR']!='.' && $datos_cli_edu[0]['EmailR']!='')
      {
        $to_correo.= $datos_cli_edu[0]['EmailR'].',';
      }
      // $to_correo = substr($to_correo, 0,-1);
    }
    $sucursal = $this->catalogo_lineas_('FA',$ser);
    $forma_pago = $this->DCTipoPago($datos_fac[0]['Tipo_Pago']);

    if(count($forma_pago)>0)
    {
      $datos_fac[0]['Tipo_Pago'] = $forma_pago[0]['CTipoPago'];
    }

    imprimirDocEle_fac($datos_fac,$detalle_fac,$datos_cli_edu,$nombre,null,'factura',null,null,$imp=$descargar,$detalle_abonos,$sucursal);
    if($to_correo!='')
    {
      $titulo_correo = 'comprobantes electronicos';
      $cuerpo_correo = 'comprobantes electronico';
      if($aprobado)
      {
        $r = $this->email->enviar_email($archivos,$to_correo,$cuerpo_correo,$titulo_correo,$HTML=false);

        // print_r($r);die();
        return $r;
      }
      // print_r($r);
    }
    return $res;
   }

  function Cliente($cod,$grupo = false,$query=false,$clave=false)
   {
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

     $result = $this->db->datos($sql);

       // $result =  encode($result);
        // print_r($result);
        return $result;
   }

  function getSerieUsuario($codigoU){
      $sql="SELECT * FROM Accesos WHERE Codigo = '".$codigoU."'";
      // print_r($sql);die();
      $stmt = $this->db->datos($sql);
      return $stmt;
    }
  function getCatalogoLineas13($fecha,$vencimiento){
    $sql="  SELECT * FROM Catalogo_Lineas 
            WHERE Item = '".$_SESSION['INGRESO']['item']."' 
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
            AND Fact = 'FA'
            AND CONVERT(DATE,Fecha) <= '".$fecha."'
            AND CONVERT(DATE,Vencimiento) >= '".$vencimiento."'
            AND len(Autorizacion)>=13
            ORDER BY Codigo";
            $stmt = $this->db->datos($sql);
            return $stmt;
    }

  function DCTipoPago($cod=false)
   {
      $sql = "SELECT Codigo,(Codigo + ' ' + Descripcion) As CTipoPago
         FROM Tabla_Referenciales_SRI
         WHERE Tipo_Referencia = 'FORMA DE PAGO' ";
         if($cod)
         {
          $sql.=" AND Codigo = '".$cod."'";
         }
         $sql.=" ORDER BY Codigo ";
         // print_r($sql);die();
          $stmt = $this->db->datos($sql);
      return $stmt;
   }

   

  
}

?>