<?php
require_once(dirname(__DIR__,2)."/modelo/facturacion/facturarM.php");
require_once(dirname(__DIR__,2)."/comprobantes/SRI/autorizar_sri.php");
require_once(dirname(__DIR__,3)."/lib/fpdf/cabecera_pdf.php");

$controlador = new facturarC();
if(isset($_GET['lineas_factura']))
{
	// $parametros = $_POST['parametros'];
	echo json_encode($controlador->lineas_facturas());
}
if(isset($_GET['DCMod']))
{
	//$parametros = $_POST['parametros'];
	echo json_encode($controlador->DCMod());
}
if(isset($_GET['DCLineas']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->DCLinea($parametros));
}
if(isset($_GET['DCTipoPago']))
{
	//$parametros = $_POST['parametros'];
	echo json_encode($controlador->DCTipoPago());
}
if(isset($_GET['DCBodega']))
{
	//$parametros = $_POST['parametros'];
	echo json_encode($controlador->DCBodega());
}
if(isset($_GET['DCMarca']))
{
	//$parametros = $_POST['parametros'];
	echo json_encode($controlador->DCMarca());
}
if(isset($_GET['DCMedico']))
{
	//$parametros = $_POST['parametros'];
	echo json_encode($controlador->DCMedico());
}
if(isset($_GET['PorCodigo']))
{
	//$parametros = $_POST['parametros'];
	echo json_encode($controlador->PorCodigo());
}
if(isset($_GET['Lineas_De_CxC']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->Lineas_De_CxC($parametros));
}
if(isset($_GET['CDesc1']))
{
	//$parametros = $_POST['parametros'];
	echo json_encode($controlador->CDesc1());
}

//-------modal suscripcion-------
if(isset($_GET['DGSuscripcion']))
{
   //$parametros = $_POST['parametros'];
   echo json_encode($controlador->DGSuscripcion());
}
if(isset($_GET['DCCtaVenta']))
{
   //$parametros = $_POST['parametros'];
   echo json_encode($controlador->DCCtaVenta());
}
if(isset($_GET['DCEjecutivoModal']))
{
   $query = '';
   if(isset($_GET['q']))
   {
      $query = $_GET['q'];
   }
   echo json_encode($controlador->DCEjecutivoModal($query));
}
if(isset($_GET['TextComision_LostFocus']))
{
   $parametros = $_POST;
   echo json_encode($controlador->TextComision_LostFocus($parametros));
}
if(isset($_GET['Command1']))
{
   $parametros = $_POST;
   echo json_encode($controlador->Command1_Click($parametros));
}
if(isset($_GET['delete_asientoP']))
{
   //$parametros = $_POST['parametros'];
   echo json_encode($controlador-> delete_asientoP());
}
//------fin modal suscripcion--------


if(isset($_GET['DCEjecutivo']))
{
	//$parametros = $_POST['parametros'];
	echo json_encode($controlador->DCEjecutivo());
}
//------------lista ordenes-------------
if(isset($_GET['Listar_Ordenes']))
{
   //$parametros = $_POST['parametros'];
   echo json_encode($controlador->Listar_Ordenes());
}
//----------------fin lista orden---------
//------------guia--------------

if(isset($_GET['DCCiudadF']))
{
   //$parametros = $_POST['parametros'];
   $query = '';
   if(isset($_GET['q']))
   {
      $query = $_GET['q'];
   }
   echo json_encode($controlador->DCCiudadF($query));
}
if(isset($_GET['DCCiudadI']))
{
   //$parametros = $_POST['parametros'];
   $query = '';
   if(isset($_GET['q']))
   {
      $query = $_GET['q'];
   }
   echo json_encode($controlador->DCCiudadI($query));
}

if(isset($_GET['AdoPersonas']))
{
   //$parametros = $_POST['parametros'];
   $query = '';
   if(isset($_GET['q']))
   {
      $query = $_GET['q'];
   }
   echo json_encode($controlador->AdoPersonas($query));
}

if(isset($_GET['DCEmpresaEntrega']))
{
   //$parametros = $_POST['parametros'];
   $query = '';
   if(isset($_GET['q']))
   {
      $query = $_GET['q'];
   }
   echo json_encode($controlador->DCEmpresaEntrega($query));
}
if(isset($_GET['MBoxFechaGRE_LostFocus']))
{
   $parametros = $_POST['parametros'];
   echo json_encode($controlador->MBoxFechaGRE_LostFocus($parametros));
}
if(isset($_GET['DCSerieGR_LostFocus']))
{
   $parametros = $_POST['parametros'];
   echo json_encode($controlador->DCSerieGR_LostFocus($parametros));
}


//--------fin guia-------------
if(isset($_GET['DCGrupo_No']))
{
	//$parametros = $_POST['parametros'];
	$query = '';
	if(isset($_GET['q']))
	{
		$query = $_GET['q'];
	}
	echo json_encode($controlador->DCGrupo_No($query));
}

if(isset($_GET['numero_factura']))
{
	//$parametros = $_POST['parametros'];
	echo json_encode($controlador->numero_factura());
}
if(isset($_GET['LstOrden']))
{
	//$parametros = $_POST['parametros'];
	echo json_encode($controlador->LstOrden());
}

if(isset($_GET['DCCliente']))
{
	//$parametros = $_POST['parametros'];
	$grupo = G_NINGUNO;
	$query = '';
	if(isset($_GET['q']))
	{
		$query = $_GET['q'];
	}
	if(isset($_GET['Grupo'])!='')
	{
		$grupo = $_GET['Grupo'];
	}
	echo json_encode($controlador->Listar_Tipo_Beneficiarios($query,$grupo));
}

if(isset($_GET['DCArticulos']))
{
	//$parametros = $_POST['parametros'];
	$marca = G_NINGUNO;
	$codmarca = G_NINGUNO;
	$query = '';
	if(isset($_GET['q']))
	{
		$query = $_GET['q'];
	}
	if(isset($_GET['marca']) and $_GET['marca']!='')
	{
		$marca = $_GET['marca'];
	}
	if(isset($_GET['codMarca']) and $_GET['codMarca']!='')
	{
		$codmarca = $_GET['codMarca'];
	}
	echo json_encode($controlador->Listar_Productos($query,$codmarca,$marca));
}

if(isset($_GET['DCArticulo_LostFocus']))
{
	$parametros = $_POST['parametros'];	
	echo json_encode($controlador->DCArticulo_LostFocus($parametros));
}

if(isset($_GET['TextVUnit_LostFocus']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->TextVUnit_LostFocus($parametros));
}

if(isset($_GET['Tipo_De_Facturacion']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->Tipo_De_Facturacion($parametros));
}
if(isset($_GET['Eliminar_linea']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->delete_asientoF($parametros));
}
if(isset($_GET['numFactura']))
{
   $parametros = $_POST['parametros'];
   echo json_encode($controlador->numFactura($parametros));
}
if(isset($_GET['Grabar_Factura_Actual']))
{
   $FA =$_GET;
   $parametros = $_POST['parametros'];
   echo json_encode($controlador->Grabar_Factura_Actual($FA,$parametros));
}
if(isset($_GET['Autorizar_Factura_Actual']))
{
   $FA =$_GET;
   $parametros = $_POST['parametros'];
   echo json_encode($controlador->Autorizar_Factura_Actual($FA,$parametros));
}
if(isset($_GET['imprimir_factura']))
{
   $FA =$_GET;
   $parametros = $_POST['parametros'];
   echo json_encode($controlador->imprimir_factura($FA,$parametros));
}


class facturarC
{
	private $modelo;
   private $sri;
   private $pdf;

	public function __construct(){
        $this->modelo = new facturarM();
        $this->sri = new autorizacion_sri();	
        $this->pdf = new cabecera_pdf();
    }

    function lineas_facturas()
    {
    	// $codigoCliente = $parametro['codigoCliente'];
    	$datos = $this->modelo->lineas_factura($tabla=1);
    	 $TextFacturaNo= Leer_Campo_Empresa("Mod_Fact");
       $Mod_PVP = Leer_Campo_Empresa("Mod_PVP");
       $DCEjecutivo = Leer_Campo_Empresa("Comision_Ejecutivo");
      $totales = Calculos_Totales_Factura();
    	return array('tbl'=>$datos,'TextFacturaNo'=>$TextFacturaNo,'Mod_PVP'=>$Mod_PVP,'DCEjecutivo'=>$DCEjecutivo,'totales'=>$totales);
    }

    function  DCMod(){
    	$datos = $this->modelo->DCMod();
    	$lis = array();
    	foreach ($datos as $key => $value) {
    		$lis[] =array('codigo'=>$value['Codigo'],'nombre'=>$value['Detalle']);
    	}
    	return $lis;
    }  

    function DCLinea($parametros)
    {
    	$datos = $this->modelo->DCLinea($parametros['TC'],$parametros['Fecha']);
    	$lis = array();
    	foreach ($datos as $key => $value) {
    		$lis[] =array('codigo'=>$value['Codigo'],'nombre'=>$value['Concepto']);
    	}
    	return $lis;
    }
    function DCTipoPago()
    {
    	$datos = $this->modelo->DCTipoPago();
    	$lis = array();
    	foreach ($datos as $key => $value) {
    		$lis[] =array('codigo'=>$value['Codigo'],'nombre'=>$value['CTipoPago']);
    	}
    	return $lis;
    }      
    function DCGrupo_No($query)
    {
    	$datos = $this->modelo->DCGrupo_No($query);
    	$lis = array();
    	foreach ($datos as $key => $value) {
    		$lis[] =array('id'=>$value['Grupo'],'text'=>$value['Grupo']);
    	}
    	return $lis;
    }
    function Listar_Tipo_Beneficiarios($query,$grupo)
    {
    	$datos = $this->modelo->Listar_Tipo_Beneficiarios($query,$grupo);
    	$lis = array();
    	foreach ($datos as $key => $value) {         
         $datos = Leer_Datos_Clientes($value['Codigo']);
    		$lis[] =array('id'=>$value['Codigo'],'text'=>$value['Cliente'],'datos'=>$datos);
    	}
      // print_r($lis);die();
    	return $lis;
    }

    function DCBodega()
    {
    	$datos = $this->modelo->bodega();
    	$lis = array();
    	foreach ($datos as $key => $value) {
    		$lis[] =array('codigo'=>$value['CodBod'],'nombre'=>$value['Bodega']);
    	}
    	return $lis;
    }
    function DCMarca()
    {
    	$datos = $this->modelo->DCMarca();
    	$lis = array();
    	foreach ($datos as $key => $value) {
    		$lis[] =array('codigo'=>$value['CodMar'],'nombre'=>$value['Marca']);
    	}
    	return $lis;
    }

    function DCMedico()
    {
    	$datos = $this->modelo->DCMedico();
    	$lis = array();
    	foreach ($datos as $key => $value) {
    		$lis[] =array('codigo'=>$value['CI_RUC'].'-'.$value['TD'].'-'.$value['Codigo'],'nombre'=>$value['Cliente']);
    	}
    	return $lis;
    }
    function DCEjecutivo()
    {
    	$datos = $this->modelo->DCEjecutivo();
    	$lis = array();
    	foreach ($datos as $key => $value) {
    		$lis[] =array('codigo'=>$value['Codigo'],'nombre'=>$value['Cliente']);
    	}
    	return $lis;
    }
    function PorCodigo()
    {
    	$res= ReadSetDataNum("PorCodigo", True, False);
    	return $res;
    }
    function CDesc1()
    {
    	$datos = $this->modelo->CDesc1();
    	$lis[] = array('codigo'=>0,'nombre'=>'00.00');
    	foreach ($datos as $key => $value) {
    		$lis[] =array('codigo'=>$value['ID'],'nombre'=>number_format($value['Interes'],2));
    	}
    	return $lis;

    }

    function Lineas_De_CxC($parametros)
    {
      // print_r($parametros);die();
    	 $resp = Lineas_De_CxC($parametros);
       // print_r($resp);die();
       $factura = ReadSetDataNum($parametros['TC']."_SERIE_".$resp['TFA']['Serie'],True, False);
       $resp['TFA']['NoFactura'] = $factura;
       // print_r($resp);die();
    	 return $resp;
    }
    

    function Listar_Productos($query,$codmarca,$marca)
    {

    	$datos = $this->modelo->Listar_Productos($codmarca,$OpcServicio=false,$query,$marca);
    	foreach ($datos as $key => $value) {
    		$lis[] =array('id'=>$value['Codigo_Inv'],'text'=>$value['Producto']);
    	}
    	return $lis;
    }
    function DCArticulo_LostFocus($parametros)
    {
    	// print_r($_SESSION['INGRESO']);die();
    	$respuesta = Leer_Codigo_Inv($parametros['codigo'],$parametros['fecha'],$parametros['bodega'],$parametros['marca']);
    	if($respuesta['respueta']==1)
    	{
    		if(count($respuesta['datos']) > 0 ){
		        $Producto = $respuesta['datos']["Producto"];
		        $Cta_Ventas = $respuesta['datos']["Cta_Ventas"];		       
		        $TextVUnit= number_format($respuesta['datos']["PVP"],4,'.','');
		        $NumStrg = $TextVUnit;
		       if($respuesta['datos']["IVA"]){ $NumStrg = number_format($respuesta['datos']["PVP"] + ($respuesta['datos']["PVP"] * $_SESSION['INGRESO']["porc"]),$_SESSION['INGRESO']['Dec_Costo'],'.','');}
		       $LabelStockArt = " P R O D U C T O 	--- ".$_SESSION['INGRESO']['S_M']."   ".$NumStrg;
		       $VUnitAnterior = $respuesta['datos']["PVP"];
		       $LabelStock = $respuesta['datos']["Stock"];
		       $Codigos = $respuesta['datos']["Codigo_Inv"];
		       $CodigoInv1 = $respuesta['datos']["Codigo_Barra"];
		       $BanIVA = $respuesta['datos']["IVA"];
		       if($parametros["tipoFactura"] == "NV"){$BanIVA = False;}
		       $DCArticulo = $Producto;
		       $TextComEjec = "0";
		       // 'TxtDetalle.SetFocus
		       $TxtDetalle = $Producto;
		       if(strlen($respuesta['datos']["Detalle"]) > 3){ $TxtDetalle = $TxtDetalle.' '.$respuesta['datos']["Detalle"];}
		          $EsNumero = False;
		          if(is_numeric($respuesta['datos']["Codigo_Barra"])) {
		             if(intval($respuesta['datos']["Codigo_Barra"]) > 0){$EsNumero = True;}
		          }
		          if(strlen($respuesta['datos']["Codigo_Barra"]) > 1 && $EsNumero ){ $TxtDetalle = $TxtDetalle."S/N: ".$respuesta['datos']["Codigo_Barra"];}
		          $TxtDetalle_Visible = True;
		          // 'TxtDetalle.SetFocus
      		}
      		return $respuesta = array('codigos'=>$Codigos,'producto'=>$Producto,'cta_venta'=>$Cta_Ventas,'labelstock'=>$LabelStock,'baniva'=>$BanIVA,'TextVUnit'=>$TextVUnit,'VUnitAnterior'=>$VUnitAnterior,'CodigoInv1'=>$CodigoInv1,'LabelStockArt'=>$LabelStockArt,'TextComEjec'=>$TextComEjec,'TxtDetalle'=>$TxtDetalle);

    	}else
    	{
    		return $respuesta;
    	}
    }

 function LstOrden()
 {
 	$datos = $this->modelo->LstOrden();
 	$op = '';
 	if(count($datos)>0)
 	{
 		foreach ($datos as $key => $value) {
 			$op.="<option>Lote No. ".$value['Lote_No']."</option>";
 		}
 	}
 	return $op;
 }

 function numero_factura()
 {
 	// print_r($_SESSION['INGRESO']);die();
 	$TextFacturaNo = Leer_Campo_Empresa("Mod_Fact");
   $Mod_PVP = Leer_Campo_Empresa("Mod_PVP");
   $CheqSP =  Leer_Campo_Empresa("SP");
    if(Leer_Campo_Empresa("Comision_Ejecutivo")){$CheqEjec = True; }else{ $CheqEjec= False;}
    if($_SESSION['INGRESO']['Nombre'] == "Administrador de Red"){
      $command4 = True;
      $TextFacturaNo = True;
   }else{
      $command4 = false;
   }
   $Total_Desc = 0;
   $Ln_No = 0;
   return array('TextFacturaNo'=>$TextFacturaNo,'Mod_PVP'=>$Mod_PVP,'CheqEjec'=>$CheqEjec,'Command4'=>$command4,'Total_Desc'=>$Total_Desc,'Ln_No'=>$Ln_No,'CheqSP'=>$CheqSP);
 }

function TextVUnit_LostFocus($parametros)
{
   // print_r($parametros);die();
   if($parametros['Mod_PVP']==0){$TextVUnit = $parametro['TextVUnit'];}
   if($parametros['DatInv_Serie_No']== ""){$DatInv_Serie_No = G_NINGUNO;}

   $Factura_No =$parametros['TextFacturaNo'];
   $TextVUnit = TextoValido($parametros['TextVUnit'],true,false,$_SESSION['INGRESO']['Dec_PVP']);
   $TextCant = TextoValido($parametros['TextCant'],true);

   $SubTotal = 0; $SubTotalDescuento = 0; $SubTotalIVA = 0; $SubTotalPorcComision = 0;
   $NumMeses = 0; $VUnitTemp = 0; $Interes = 0;
   $datosL = $this->modelo->lineas_factura();

   if(count($datosL)<=$parametros['Cant_Item_FA'])
   {
   	  if($parametros['TxtDetalle'] <> G_NINGUNO){$Producto = $parametros['TxtDetalle'];}
        if(intval($parametros['TextComision']) > 0){$SubTotalPorcComision = number_format(intval($TextComision) / 100, 2,'.','');}
       // 'SubTotal por producto
        $SubTotal = number_format(floatval($parametros['TextCant']) * floatval($parametros['TextVUnit']), 2,'.','');
        if($VUnitTemp > 0){ $SubTotal = number_format($VUnitTemp, 2,'.','');}
       // 'Descuento
        $SubTotalDescuento = number_format($SubTotal * (number_format(intval($parametros['CDesc1']), 2,'.','') / 100), 2,'.','');
       // 'IVA = SubTotal - Descuento
        if($parametros['BanIVA'] && $parametros['tipoFactura'] <> "NV"){$SubTotalIVA = number_format(($SubTotal - $SubTotalDescuento) * $_SESSION['INGRESO']['porc'], 4,'.','');}

       // 'If TipoFactura = "OP" Then SubTotalIVA = 0
        if(floatval($parametros['TextVUnit']) == 0){$SubTotalIVA = 0;}
       
        $Ln_No=count($datosL)+1; 

   	if(strlen($parametros['codigo']) > 1 )
   	{
   		$DatInv = $this->modelo->Listar_Productos_all($PatronDeBusqueda=false,$parametros['codigo']);

         SetAdoAddNew("Asiento_F");
         SetAdoFields("CODIGO", $parametros['codigo']);
         SetAdoFields("CODIGO_L", $parametros['CodigoL']);
         SetAdoFields("PRODUCTO", $Producto);
         SetAdoFields("REP", 0);
         SetAdoFields("CANT", $parametros['TextCant']);
         SetAdoFields("PRECIO", number_format($parametros['TextVUnit'], $_SESSION['INGRESO']['Dec_PVP'], '.', ''));
         SetAdoFields("TOTAL", $SubTotal);
         SetAdoFields("VALOR_TOTAL", $SubTotal - $SubTotalDescuento + $SubTotalIVA);
         SetAdoFields("Total_Desc", $SubTotalDescuento);
         SetAdoFields("Total_IVA", $SubTotalIVA);
         SetAdoFields("Cta", $DatInv[0]['Cta_Ventas']);
         SetAdoFields("Cta_SubMod", $parametros['SubCta']);
         SetAdoFields("CodBod", $parametros['bodega']);
         SetAdoFields("CodMar", $parametros['marca']);
         SetAdoFields("COD_BAR", $DatInv[0]['Codigo_Barra']);
         SetAdoFields("Item", $_SESSION['INGRESO']['item']);
         SetAdoFields("CodigoU", $_SESSION['INGRESO']['CodigoU']);
         SetAdoFields("CORTE", $VUnitTemp);
         SetAdoFields("A_No", $Ln_No);
         SetAdoFields("Fecha_V", $parametros['fechaVGR']);
         SetAdoFields("Cod_Ejec", $parametros['Cod_Ejec']);
         SetAdoFields("Porc_C", $SubTotalPorcComision);
         SetAdoFields("Serie_No", $DatInv_Serie_No);
         SetAdoFields("COSTO", $DatInv[0]['Costo']);
         SetAdoFields("Codigo_Cliente", $parametros['Cliente']);

         if (strlen($parametros['TextComEjec']) > 1) {
             SetAdoFields("RUTA", $parametros['TextComEjec']);
         }

         if ($DatInv[0]['Por_Reservas']) {
             SetAdoFields("Fecha_IN", $parametros['MBFechaIn']);
             SetAdoFields("Fecha_OUT", $parametros['MBFechaOut']);
             SetAdoFields("Cant_Hab", $parametros['TxtCantRooms']);
             SetAdoFields("Tipo_Hab", $parametros['TxtTipoRooms']);
         }

         if (strlen($parametros['LstOrden']) > 1) {
             SetAdoFields("Lote_No", $parametros['LstOrden']);
             SetAdoFields("Fecha_Fab", $DatInv[0]['Fecha_Fab']);
             SetAdoFields("Fecha_Exp", $DatInv[0]['Fecha_Exp']);
             SetAdoFields("Reg_Sanitario", $DatInv[0]['Reg_Sanitario']);
             SetAdoFields("Procedencia", $DatInv[0]['Procedencia']);
             SetAdoFields("Modelo", $DatInv[0]['Modelo']);
             SetAdoFields("SP", 0);
             if ($parametros['Sec_Public'] == true) {
                 SetAdoFields("SP", 1);
             }
         }

         if ($DatInv[0]['Costo'] > 0) {
             SetAdoFields("Cta_Inv", $DatInv[0]['Cta_Inventario']);
             SetAdoFields("Cta_Costo", $DatInv[0]['Cta_Costo_Venta']);
         }

         return SetAdoUpdate();
      }
      else{

          return $MsgBox = "No ha seleccionado el codigo correcto, vuelva a ingresar";
      }
    }else{
       return $MsgBox = "Ya no se puede ingresar más datos.";
    }
   if (number_format($TextVUnit,$_SESSION['INGRESO']['Dec_Costo'],'.','') < $DatInv[0]['Costo'] && $DatInv[0]['Costo'] > 0 && strlen($DatInv[0]['Cta_Inventario']) > 3) {
      return "Usted esta vendiendo por debajo del Costo de Produccion";
   }
}

function delete_asientoF($parametros)
{
	$ln_No = $parametros['ln_No'];
	return $this->modelo->delete_asientoF($ln_No);
}

 //--------------- modal suscripcion-----------------
  function DGSuscripcion()
   {
      $tabla = true;
      $datos = $this->modelo->DGSuscripcion($tabla);
      return $datos;
   }
   function DCCtaVenta()
   {
      $datos = $this->modelo->DCCtaVenta();
      $list = array();
      foreach ($datos as $key => $value) {
         $list[] = array('codigo'=>$value['Cta_Ventas'],'nombre'=>$value['Cuenta']);
      }
      return $list;
   }

   function DCEjecutivoModal($query)
   {
      $datos = $this->modelo->DCEjecutivoModal($query);
      $list = array();
      foreach ($datos as $key => $value) {
         $list[] = array('id'=>$value['Codigo'],'text'=>$value['Cliente']);
      }
      return $list;
   }
   function delete_asientoP()
   {
      return $this->modelo->delete_asientoP();

   }
   function TextComision_LostFocus($parametros)
   {
      $this->modelo->delete_asientoP();
      $fecha1= new DateTime("1899-12-30");
      $fecha2= new DateTime($parametros['MBDesde']);
      $fecha3= new DateTime($parametros['MBHasta']);
      $Saldo = 0; $Diferencia = 0; $Cuota_No = 1;$NumMeses=0;
      $Opcion = intval(@$parametros['TxtHasta']);
      // print_r($Opcion);die();
      $Mifecha = $parametros['MBDesde'];
      $I = $fecha1->diff($fecha2)->days;
      $J = $fecha1->diff($fecha3)->days;

        if($parametros['opc']=='OpcMensual'){$NumMeses = number_format(($J - $I) / 31);}
        if($parametros['opc']=='OpcQuincenal'){$NumMeses = number_format(($J - $I) / 15);}
        if($parametros['opc']=='OpcSemanal'){$NumMeses = number_format(($J - $I) / 7);}
        if($parametros['opc']=='OpcAnual'){$NumMeses = 1;}
        if($parametros['opc']=='OpcTrimestral'){$NumMeses = 4;}
        if($parametros['opc']=='OpcSemestral'){$NumMeses = 2;}
        if($NumMeses <= 0){$NumMeses = 1;}
        $Saldo = number_format(floatval($parametros['TextValor'])/$NumMeses, 2,'.','');
        for ($i=$Cuota_No; $i <= intval($NumMeses); $i++) { 
             if($parametros['opc']==' OpcMensual'){$Mifecha = date("d-m-Y",strtotime($Mifecha."+ 1 month")); }
             if($parametros['opc']==' OpcQuincenal'){$Mifecha = date("d-m-Y",strtotime($Mifecha."+ 15 days"));}
             if($parametros['opc']==' OpcSemanal'){$Mifecha =date("d-m-Y",strtotime($Mifecha."+ 7 days"));}
             if($parametros['opc']==' OpcAnual'){$Mifecha = date("d-m-Y",strtotime($Mifecha."+ 366 days"));}
             if($parametros['opc']==' OpcTrimestral'){$Mifecha = date("d-m-Y",strtotime($Mifecha."+ 91 days"));}
             if($parametros['opc']==' OpcSemestral'){$Mifecha = date("d-m-Y",strtotime($Mifecha."+ 182 days"));}
             if($Opcion < $Cuota_No){$Si_No = false; }Else{$Si_No = True;}
             $Trans_No = 250;

             SetAdoAddNew("Asiento_P");
             SetAdoFields("Sector", $parametros['TextSector']);
             SetAdoFields("Ejemplar", $i);
             SetAdoFields("Fecha", $Mifecha);
             SetAdoFields("Entregado", $Si_No);
             SetAdoFields("Comision", 0);
             SetAdoFields("Capital", $Saldo);
             SetAdoFields("T_No", $Trans_No);
             SetAdoFields("Item", $_SESSION['INGRESO']['item']);
             SetAdoFields("CodigoU", $_SESSION['INGRESO']['CodigoU']);
             SetAdoFields("Cuotas", $i);
             SetAdoUpdate();
        }
      return $NumMeses;
   }

  function Command1_Click($parametros)
  {

  if($parametros['opc2']=='OpcN'){$TipoProc = "N";}else{$TipoProc = "R";}
  $Opcion = intval($parametros['TxtHasta']);
  if($parametros['opc']=='OpcMensual'){$TipoDoc = "MENS";}
  if($parametros['opc']=='OpcQuincenal'){$TipoDoc = "QUNC";}
  if($parametros['opc']=='OpcSemanal'){$TipoDoc = "SEMA";}
  if($parametros['opc']=='OpcAnual'){$TipoDoc = "ANUA";}
  if($parametros['opc']=='OpcTrimestral'){$TipoDoc = "TRIM";}
  if($parametros['opc']=='OpcSemestral'){$TipoDoc = "SEME";}

  $Credito_No = $parametros['TextTipo']."-".generaCeros(intval($parametros['TextContrato']),7);
  $this->modelo->delete_command1($TipoDoc,$Credito_No);

  $Suscripcion = $this->modelo->DGSuscripcion();
  if(count($Suscripcion)>0)
  {
      SetAdoAddNew("Prestamos");
      SetAdoFields("T", $TipoProc);
      SetAdoFields("Sector", $parametros['TextSector']);
      SetAdoFields("TP", $TipoDoc);
      SetAdoFields("Credito_No", $Credito_No);
      SetAdoFields("No_Venc", $Opcion);
      SetAdoFields("Cuenta_No", $parametros['LblClienteCod']);
      SetAdoFields("Meses", $parametros['txtperiodo']);
      SetAdoFields("Fecha", $parametros['MBDesde']);
      SetAdoFields("Fecha_C", $parametros['MBHasta']);
      SetAdoFields("Capital", $parametros['TextValor']);
      SetAdoFields("Encaje", number_format(floatval($parametros['TextValor']) * (intval($parametros['TextComisionModal']) / 100), 4, '.', ''));
      SetAdoFields("Numero", $parametros['TextFact']);
      SetAdoFields("Atencion", $parametros['TxtAtencion']);
      SetAdoFields("Cta", $parametros['DCCtaVenta']);
      SetAdoFields("Pagos", number_format($Suscripcion[0]["Capital"], 2, '.', ''));
      SetAdoFields("Item", $_SESSION['INGRESO']['item']);
      SetAdoFields("CodigoE", $parametros['DCEjecutivoModal']);
      SetAdoFields("CodigoU", $_SESSION['INGRESO']['CodigoU']);
      SetAdoUpdate();
   }

 // // 'Detalle
   if(count($Suscripcion)>0)
   {
      foreach ($Suscripcion as $key => $value) {
         SetAdoAddNew("Trans_Suscripciones");
         SetAdoFields("T", $TipoProc);
         SetAdoFields("AC", 0);
         SetAdoFields("TP", $TipoDoc);
         SetAdoFields("Contrato_No", $Credito_No);
         SetAdoFields("Ent_No", $value["Ejemplar"]);
         SetAdoFields("Fecha", $value["Fecha"]->format('Y-m-d'));
         SetAdoFields("E", $value["Entregado"]);
         SetAdoFields("Item", $_SESSION['INGRESO']['item']);
         SetAdoUpdate();
      }
   }
   return '1';
 }
//------------------fin modal suscripcion----------------
 //----------------guia--------------------

  function DCCiudadI($query)
  {  
     $datos = $this->modelo->DCCiudad($query);
     $lista[] =array();
     foreach ($datos as $key => $value) {
          $lista[] = array('id'=>$value['Descripcion_Rubro'],'text'=>$value['Descripcion_Rubro']);
       }  
       return $lista;
  }
  function DCCiudadF($query)
  { 
    $datos = $this->modelo->DCCiudad($query);
    $lista[] =array();
     foreach ($datos as $key => $value) {
          $lista[] = array('id'=>$value['Descripcion_Rubro'],'text'=>$value['Descripcion_Rubro']);
       }     
       return $lista;
     
  }
  function AdoPersonas($query)
  {  
    $datos = $this->modelo->AdoPersonas($query);
    $lista[] =array();
     foreach ($datos as $key => $value) {
          $lista[] = array('id'=>$value['CI_RUC'].'_'.$value['Direccion'],'text'=>$value['Cliente']);
       }     
       return $lista;
     
  }
  function DCEmpresaEntrega($query)
  {  
    $datos = $this->modelo->AdoPersonas($query);
    $lista[] =array();
     foreach ($datos as $key => $value) {
          $lista[] = array('id'=>$value['Codigo'],'text'=>$value['Cliente']);
       }     
       return $lista;
     
  }
  function MBoxFechaGRE_LostFocus($parametros)
  {
    $datos = $this->modelo->MBoxFechaGRE_LostFocus($parametros['MBoxFechaGRE']);
    $lis = array();
    foreach ($datos as $key => $value) {
       $lis[] = array('codigo'=>$value['Codigo'].'_'.$value['Serie'],'nombre'=>$value['Concepto']);
    }
    return $lis;
  }

  function DCSerieGR_LostFocus($parametros)
  {
   $DCSerieGR= $parametros['DCSerieGR'];
   $LblGuiaR = ReadSetDataNum("GR_SERIE_".$DCSerieGR, True, False);
   $datos = $this->modelo->MBoxFechaGRE_LostFocus($parametros['MBoxFechaGRE'],$DCSerieGR);
   $Autorizacion = '';
   if(count($datos)>0)
   {
      $Autorizacion = $datos[0]['Autorizacion'];
   }
   return array('Auto'=>$Autorizacion,'Guia'=>$LblGuiaR);
  }
 //------------------fin guia------------

  // ------------------Listar_Ordenes------------
  function Listar_Ordenes()
  {
    $datos = $this->modelo->Listar_Ordenes();
    $lista = array();
    if(count($datos)>0)
    {
      foreach ($datos as $key => $value) {       
       $lista[] = array("Orden No. ".generaCeros($value["Factura"],9)." - ".$value["Cliente"]);
      }
    }

    return $lista;
  }
  // ----------------fin Listar_Ordenes---------

  function Grabar_Factura_Actual($FA,$parametros)
  {

   $asientoF = $this->modelo->lineas_factura();
   if(count($asientoF)>0)
   {
     $TFA = Calculos_Totales_Factura();
     foreach ($TFA as $key => $value) {
       $FA[$key]=$value;
     }
         
     $TextObs  = TextoValido($parametros['TextObs']);
     $TextNota = TextoValido($parametros['TextNota']);
     $TxtPedido= TextoValido($parametros['TxtPedido']);
     $TxtZona = TextoValido($parametros['TxtZona'],false , True);
     $TxtLugarEntrega = TextoValido($parametros['TxtLugarEntrega'],false , True);
     $TextComision =  TextoValido($parametros['TextComision'],false , True);
     $TxtCompra =  TextoValido($parametros['TxtCompra'], True, false, 0);
     $MBoxFechaV = $parametros['MBoxFechaV'];
     $TextoFormaPago = G_PAGOCRED;
     if($parametros['Check1'] != 'false'){$Moneda_US = 1;}
        $Moneda_US = 0; //false
        $Total_FacturaME = 0;
     
     $FA['T'] = G_PENDIENTE;
     $FA['Orden_Compra'] = 0;
     $FA['SubCta'] = G_NINGUNO;
     $FA['SP'] = 0; //false
     $FA['Porc_IVA'] = $_SESSION['INGRESO']['porc'];
     $FA['Total_MN'] = $parametros['Total'];

     

     $FA['Tipo_Pago'] = $parametros['DCTipoPago'];
     $FA['Forma_Pago'] = $TextoFormaPago;
     $FA['Observacion'] = $TextObs;
     $FA['Nota'] = $TextNota;
     $FA['Pedido'] = $TxtPedido;
    
    // // 'MsgBox Val(TxtCompra)
     if(is_numeric($TxtCompra)){ $FA['Orden_Compra'] = intval($TxtCompra);}
     $Adomod = $this->modelo->DCMod();
     if(count($Adomod)>0){ $FA['SubCta'] = $parametros['DCMod'];}
     if( intval($FA['Tipo_Pago']) <= 0){$FA['Tipo_Pago'] = "01";}
     if($parametros['CheqSP'] == 'true'){$FA['SP'] = 1;}
     
     $FA['ME_'] = $Moneda_US;
     $FA['Saldo_MN'] = $FA['Total_MN'];

    //  RatonNormal
    
         $FA['Nuevo_Doc'] = True;
         $FA['Factura'] = intval($parametros['TextFacturaNo']);

         // print_r($FA);
         // print_r($parametros);die();
        if(Existe_Factura($FA)){
          if($parametros['Reprocesar']==1)
          {
            $FA['Numero_Doc'] = 0;
          }else
          {
            return  array('res'=>-2,'men'=>"Ya existe ".$FA['TC']." No. ".$FA['Serie']."-".generaCeros($FA['Factura'],9)." Desea Reprocesarla");
          }
          // if( BoxMensaje = vbYes Then FA.Nuevo_Doc = False Else GoTo NoGrabarFA
        }else{         
           $Factura_No = ReadSetDataNum($FA['TC']."_SERIE_".$FA['Serie'], True, False);
           if($FA['Factura'] <> $Factura_No){
            if($parametros['Reprocesar']==1)
             {
               $FA['Numero_Doc'] = 0;
             }else
             {
               return  array('res'=>-3,'men'=>"La ".$FA['TC']." No. ".$FA['Serie']."-".generaCeros($FA['Factura'],9).", no esta Procesada, desea Procesarla?");
             }
           }
        }

        if($FA['Nuevo_Doc']){$FA['Factura'] = ReadSetDataNum($FA['TC']."_SERIE_".$FA['Serie'], True, True);}        
        if(strlen($FA['Autorizacion_GR']) == 13){
           $GuiaRemision = ReadSetDataNum("GR_SERIE_".$FA['Serie_GR'], True, False);
           if($FA['Remision'] == $GuiaRemision){$FA['Remision'] = ReadSetDataNum("GR_SERIE_".$FA['Serie_GR'], True, True);}
        }
       
        
        $Comision = number_format($TextComision/100, 4,'.','');
        $Total_SubTotal=0;
        $Total_Comision = number_format(($Total_SubTotal*$Comision), 2,'.','');
       
       // 'Datos del Encabezado y totales de la factura
        $cliente = Leer_Datos_Clientes($parametros['Cliente'],$Por_Codigo=true,$Por_CIRUC=false,$Por_Cliente=false);
        $FA['Cliente'] = $cliente['Cliente'];
        $FA['TextCI'] = $cliente['CI_RUC'];
        $FA['TxtEmail'] = $cliente['Email'];
        $FA['codigoCliente'] = $cliente['Codigo'];
        $FA['FacturaNo'] = $parametros['TextFacturaNo'];
        $FA['me'] = $parametros['TextFacturaNo'];  //cambiar viene de abonos
        $FA['Total'] = $FA['Total_MN'];
        $FA['Total_Abonos'] = 0;
    
        // print_r($cliente);
        $FA['Porc_C'] = $Comision;
        $FA['Comision'] = $Total_Comision;

         // print_r($FA);
         // print_r($parametros);
         // die();
       // 'Grabamos el numero de factura
        // print_r(Grabar_Factura($FA));
         if(Grabar_Factura($FA)==2)
         {
            return  array('res'=>1,'men'=>"");
         }else
         {            
             array('res'=>-1,'men'=>"Algo salio mal");;
         }
        
        
       // 'Grabamos Abonos del numero de factura
        $Bandera = False;
        $Evaluar = True;
        // $FechaTexto = MBoxFecha
        $Factura_No = $FA['Factura'];
        $Numero = $Factura_No;

   }else
   {
      return -1;
   }
  }


  function Autorizar_Factura_Actual($FA,$parametros)
  {
   // print_r($parametros);die();
   $asientoF = $this->modelo->lineas_factura();
   if(count($asientoF)>0)
   {
     $TFA = Calculos_Totales_Factura();
     foreach ($TFA as $key => $value) {
       $FA[$key]=$value;
     }
         
     $TextObs  = TextoValido($parametros['TextObs']);
     $TextNota = TextoValido($parametros['TextNota']);
     $TxtPedido= TextoValido($parametros['TxtPedido']);
     $TxtZona = TextoValido($parametros['TxtZona'],false , True);
     $TxtLugarEntrega = TextoValido($parametros['TxtLugarEntrega'],false , True);
     $TextComision =  TextoValido($parametros['TextComision'],false , True);
     $TxtCompra =  TextoValido($parametros['TxtCompra'], True, false, 0);
     $MBoxFechaV = $parametros['MBoxFechaV'];
     $TextoFormaPago = G_PAGOCRED;
     if($parametros['Check1'] != 'false'){$Moneda_US = 1;}
        $Moneda_US = 0; //false
        $Total_FacturaME = 0;
     
     $FA['T'] = G_PENDIENTE;
     $FA['Orden_Compra'] = 0;
     $FA['SubCta'] = G_NINGUNO;
     $FA['SP'] = 0; //false
     $FA['Porc_IVA'] = $_SESSION['INGRESO']['porc'];

     

     $FA['Tipo_Pago'] = $parametros['DCTipoPago'];
     $FA['Forma_Pago'] = $TextoFormaPago;
     $FA['Observacion'] = $TextObs;
     $FA['Nota'] = $TextNota;
     $FA['Pedido'] = $TxtPedido;
    
    // // 'MsgBox Val(TxtCompra)
     if(is_numeric($TxtCompra)){ $FA['Orden_Compra'] = intval($TxtCompra);}
     $Adomod = $this->modelo->DCMod();
     if(count($Adomod)>0){ $FA['SubCta'] = $parametros['DCMod'];}
     if( intval($FA['Tipo_Pago']) <= 0){$FA['Tipo_Pago'] = "01";}
     if($parametros['CheqSP'] == 'true'){$FA['SP'] = 1;}
     
     $FA['ME_'] = $Moneda_US;
     $FA['Saldo_MN'] = $FA['Total_MN'];

    //  RatonNormal
    
         $FA['Nuevo_Doc'] = True;
         $FA['Factura'] = intval($parametros['TextFacturaNo']);

         // print_r($FA);
         // print_r($parametros);die();

        // if($FA['Nuevo_Doc']==false){$FA['Factura'] = ReadSetDataNum($FA['TC']."_SERIE_".$FA['Serie'], True, True);}        
        if(strlen($FA['Autorizacion_GR']) == 13){
           $GuiaRemision = ReadSetDataNum("GR_SERIE_".$FA['Serie_GR'], True, False);
           if($FA['Remision'] == $GuiaRemision){$FA['Remision'] = ReadSetDataNum("GR_SERIE_".$FA['Serie_GR'], True, True);}
        }
       
        
        $Comision = number_format($TextComision/100, 4,'.','');
        $Total_SubTotal=0;
        $Total_Comision = number_format(($Total_SubTotal*$Comision), 2,'.','');
       
       // 'Datos del Encabezado y totales de la factura
        $cliente = Leer_Datos_Clientes($parametros['Cliente'],$Por_Codigo=true,$Por_CIRUC=false,$Por_Cliente=false);
        $FA['Cliente'] = $cliente['Cliente'];
        $FA['TextCI'] = $cliente['CI_RUC'];
        $FA['TxtEmail'] = $cliente['Email'];
        $FA['codigoCliente'] = $cliente['Codigo'];
        $FA['FacturaNo'] = $parametros['TextFacturaNo'];
        $FA['me'] = $parametros['TextFacturaNo'];  //cambiar viene de abonos
        $FA['Total'] = $FA['Total_MN'];
        $FA['Total_Abonos'] = 0;
    
        // print_r($cliente);
        $FA['Porc_C'] = $Comision;
        $FA['Comision'] = $Total_Comision;

         // print_r($FA);
         // print_r($parametros);
         // die();
       // 'Grabamos el numero de factura
       
        
       // 'Grabamos Abonos del numero de factura
        $Bandera = False;
        $Evaluar = True;
        $FechaTexto = $FA['Fecha'];
        $Factura_No = $FA['Factura'];
        $Numero = $Factura_No;


        $respuseta = ''; 
        $respuesta2 = '';     
      
       
       // 'Autorizamos la factura y/o Guia de Remision
        if(strlen($FA['Autorizacion']) == 13){
         /* genera xml al sri*/
         $FAA['serie']= $FA['Serie'];
         $FAA['num_fac']= $FA['FacturaNo'];
         $FAA['tc']= $FA['TC'];
         $FAA['cod_doc']= '01';
         $respuesta = $this->sri->Autorizar($FAA); 
         // SRI_Crear_Clave_Acceso_Facturas($FA, False, True);
        //  
      }
         
        if(strlen($FA['Autorizacion_GR']) == 13){
           /* genera xml al sri*/ 
            $FAA['serie']= $FA['Serie'];
            $FAA['num_fac']= $FA['FacturaNo'];
            $FAA['tc']= $FA['TC'];
            $FAA['cod_doc']= '01';
           $respuesta2 =  $this->sri->Autorizar($FAA); 
           // SRI_Crear_Clave_Acceso_Guia_Remision FA, False, True
           if(strlen($FA['Autorizacion_GR']) > 13){
              $this->modelo->actualizar_Facturas_Auxiliares($FA);             
           }
        }
        // //    'MsgBox "Documento " & FA.TC & " No. " & FA.Serie & "-" & Format(FA.Factura, "000000000")
        $TA['TP'] = $FA['TC'];
        $TA['Serie'] = $FA['Serie'];
        $TA['Factura'] = $FA['Factura'];
        $TA['Autorizacion'] = $FA['Autorizacion'];
        $TA['CodigoC'] = $FA['codigoCliente'];
        Actualiza_Estado_Factura($TA);

        $Grafico_PV = Leer_Campo_Empresa("Grafico_PV");
        $imp = '';
         if($FA['TC'] <> "OP")
        {
          // 'MsgBox FA.Autorizacion & vbCrLf & FA.Autorizacion_GR
           if(strlen($FA['Autorizacion']) >= 13){
              if($Grafico_PV){ 
               $info = Imprimir_Punto_Venta_Grafico_datos($FA);
               $this->pdf->Imprimir_Punto_Venta_Grafico($info);
               $imp = $FA['Serie'].'-'.generaCeros($FA['Factura'],7);
            }else{ Imprimir_Punto_Venta($FA); }
           }else{
               return array('AU'=>'multiple');
           }
           $this->modelo->Facturas_Impresas($FA);
        }


        return array('AU'=>$respuesta,'GR'=>$respuesta2,'pdf'=>$imp);
   }else
   {
      return -1;
   }
  }


  function Imprimir_Punto_Venta_Grafico($FA)
  {


  }

  function imprimir_multiple($parametros)
  {

   print_r($parametros);die();
     // Titulo = "IMPRESION"
     //          Mensajes = "Facturacion Multiple"
     //          If BoxMensaje = vbYes Then
     //             Factura_Desde = FA.Factura
     //             Factura_Hasta = FA.Factura
     //             FA.Tipo_PRN = "FM"
     //             Imprimir_Facturas_CxC Facturas, FA, True
     //          Else
     //             $FA['Tipo_PRN'] = "FA";
     //             Imprimir_Facturas($FA);
     //          End If


  }

    
}
?>