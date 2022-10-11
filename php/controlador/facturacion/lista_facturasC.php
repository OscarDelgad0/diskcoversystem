<?php
require_once(dirname(__DIR__,2)."/modelo/facturacion/lista_facturasM.php");
require(dirname(__DIR__,3).'/lib/fpdf/cabecera_pdf.php');
require(dirname(__DIR__,3).'/lib/phpmailer/enviar_emails.php');
require(dirname(__DIR__,2)."/comprobantes/SRI/autorizar_sri.php");

$controlador = new lista_facturasC();
if(isset($_GET['tabla']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->tabla_facturas($parametros));
}
if(isset($_GET['perido']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->factura_periodo($parametros));
}
if(isset($_GET['ver_fac']))
{
  $controlador->ver_fac_pdf($_GET['codigo'],$_GET['ser'],$_GET['ci'],$_GET['per']);
}
if(isset($_GET['imprimir_pdf']))
{
	$parametros= $_GET;
     $controlador->imprimir_pdf($parametros);
}
if(isset($_GET['imprimir_excel']))
{   
	$parametros= $_GET;
	$controlador->imprimir_excel($parametros);	
}
if(isset($_GET['grupos']))
{
	$query = '';
	if(isset($_GET['q']))
	{
		$query = $_GET['q'];
	}
	echo json_encode($controlador->grupos($query));
}
if(isset($_GET['clientes']))
{
	$query = '';
	$grupo = $_GET['g'];
	if(isset($_GET['q']))
	{
		$query = $_GET['q'];
	}
	echo json_encode($controlador->clientes_x_grupo($query,$grupo));
}
if(isset($_GET['clientes2']))
{
	$query = '';
	$grupo = $_GET['g'];
	if(isset($_GET['q']))
	{
		$query = $_GET['q'];
	}
	echo json_encode($controlador->clientes2_x_grupo($query,$grupo));
}
if(isset($_GET['clientes_datos']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->clientes_datos($parametros));
}

if(isset($_GET['validar']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->validar_cliente($parametros));
}

if(isset($_GET['enviar_mail']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->enviar_mail($parametros));
}
if(isset($_GET['re_autorizar']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->autorizar($parametros));
}
if(isset($_GET['Anular']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->anular($parametros));
}

class lista_facturasC
{
	private $modelo;
    private $email;
    private $pdf;
	public function __construct(){
    $this->modelo = new lista_facturasM();
		$this->pdf = new cabecera_pdf();
		$this->email = new enviar_emails();
		$this->empresaGeneral = $this->modelo->Empresa_data();
		$this->sri = new autorizacion_sri();
        //$this->modelo = new MesaModel();
    }


    function tabla_facturas($parametros)
    {

    	// print_r($parametros);die();
    	$codigo = $parametros['ci'];
    	$tbl = $this->modelo->facturas_emitidas_tabla($codigo,$parametros['per'],$parametros['desde'],$parametros['hasta']);
    	$tr='';
    	foreach ($tbl as $key => $value) {
    		 $exis = $this->modelo->catalogo_lineas($value['TC'],$value['Serie']);
    		 $autorizar = '';$anular = '';
    		 if(count($exis)>0 && strlen($value['Autorizacion'])==13 && $parametros['tipo']!='')
    		 {
    		 	$autorizar = '<button type="button" class="btn btn-xs btn-primary" onclick="autorizar(\''.$value['TC'].'\',\''.$value['Factura'].'\',\''.$value['Serie'].'\',\''.$value['Fecha']->format('Y-m-d').'\')" title="Autorizar"><i class="fa fa-paper-plane"></i></button>';
    		 }
    		 if($value['T']!='A' && $parametros['tipo']!='')
    		 {
    		 	$anular = '<button type="button" class="btn btn-xs btn-danger" onclick="anular_factura(\''.$value['Factura'].'\',\''.$value['Serie'].'\',\''.$value['CodigoC'].'\')" title="Anular factura"><i class="fa fa-times-circle"></i></button>';
    		 }
    		$tr.='<tr>
            <td><button type="button" class="btn btn-xs btn-default" onclick="Ver_factura(\''.$value['Factura'].'\',\''.$value['Serie'].'\',\''.$value['CodigoC'].'\')" title="Ver factura"><i class="fa fa-eye"></i></button>'.$autorizar.$anular.'</td>
            <td>'.$value['T'].'</td>
            <td>'.$value['Razon_Social'].'</td>
            <td>'.$value['TC'].'</td>
            <td>'.$value['Serie'].'</td>
            <td>'.$value['Autorizacion'].'</td>
            <td>'.$value['Factura'].'</td>
            <td>'.$value['Fecha']->format('Y-m-d').'</td>
            <td class="text-right">'.$value['SubTotal'].'</td>
            <td class="text-right">'.$value['Con_IVA'].'</td>
            <td class="text-right">'.$value['IVA'].'</td>
            <td class="text-right">'.$value['Descuentos'].'</td>
            <td class="text-right">'.$value['Total'].'</td>
            <td class="text-right">'.$value['Saldo'].'</td>
            <td>'.$value['RUC_CI'].'</td>
            <td>'.$value['TB'].'</td>
          </tr>';
    	}

    	// print_r($tbl);die();

    	return $tr;
    }
    function factura_periodo($parametros)
    {
    	$datos = $this->modelo->facturas_perido($parametros['codigo']);
    	$opcion = '';
    	foreach ($datos as $key => $value) {  
    		$year = '.';
    		if($value['Periodo']!='.')
    		{
    	   $year = explode('/',$value['Periodo']);
    	   $year = $year[2];
    	  }
    		$opcion.='<option value="'.$year.'">'.$year.'</option>';
    	}
    	return $opcion;
    }
    function ver_fac_pdf($cod,$ser,$ci,$per)
    {
    	// print_r($cod);die();
    	$this->modelo->pdf_factura($cod,$ser,$ci,$per);
    }
    function imprimir_pdf($parametros)
    {
    	// print_r($parametros);die();
    	$codigo = $parametros['ddl_cliente'];
    	$tbl = $this->modelo->facturas_emitidas_tabla($codigo,$parametros['ddl_periodo']);

  // 	    $desde = str_replace('-','',$parametros['txt_desde']);
		// $hasta = str_replace('-','',$parametros['txt_hasta']);
		// $empresa = explode('_', $parametros['ddl_entidad']);
		// $parametros['ddl_entidad'] = $empresa[0];

		// print_r($parametros);die();

		// $datos = $this->modelo->pedido_paciente_distintos(false,$parametros['rbl_buscar'],$parametros['txt_query'],$parametros['txt_desde'],$parametros['txt_hasta'],$parametros['txt_tipo_filtro']);


		$titulo = 'L I S T A  D E  F A C T U R A S';
		$sizetable =7;
		$mostrar = TRUE;
		// $Fechaini = $parametros['txt_desde'] ;//str_replace('-','',$parametros['Fechaini']);
		// $Fechafin = $parametros['txt_hasta']; //str_replace('-','',$parametros['Fechafin']);
		$tablaHTML= array();		
		$pos = 0;
		$borde = 1;
		// print_r($datos);die();
		$pos=1;
		$tablaHTML[0]['medidas']=array(7,10,15,50,15,20,15,15,15,15,15,20,7,50,15);
		$tablaHTML[0]['alineado']=array('L','L','L','L','L','L','R','R','R','R','R','L','L','L','L');
		$tablaHTML[0]['datos']=array('T','TC','Serie','Autorizacion','Factura','Fecha','SubTotal','Con Iva','IVA','Total','Saldo','Ruc','TB','Razon social','ID');
		$tablaHTML[0]['borde'] =$borde;
		$tablaHTML[0]['estilo'] ='b';

		$datos = $tbl['datos'];
		
		foreach ($datos as $key => $value) {			

		    $tablaHTML[$pos]['medidas']=$tablaHTML[0]['medidas'];
		    $tablaHTML[$pos]['alineado']=$tablaHTML[0]['alineado'];
		    $tablaHTML[$pos]['datos']=array($value['T'],$value['TC'],$value['Serie'],$value['Autorizacion'].' ',$value['Factura'],$value['Fecha']->format('Y-m-d'),$value['SubTotal'],$value['Con_IVA'],$value['IVA'],$value['Total'],$value['Saldo'],$value['RUC_CI'],$value['TB'],$value['Razon_Social'],$value['ID']);
		    $tablaHTML[$pos]['borde'] =$borde;
			$pos+=1;
		}
	   
		$this->pdf->cabecera_reporte_MC($titulo,$tablaHTML,$contenido=false,$image=false,$Fechaini=false,$Fechafin=false,$sizetable,$mostrar,15,'H');
  }

  function imprimir_excel($parametros)
  {
		$empresa = explode('_', $parametros['ddl_entidad']);
		$parametros['ddl_entidad'] = $empresa[0];
  	$datos = $this->modelo->tabla_registros($parametros['ddl_entidad'],$parametros['ddl_empresa'],$parametros['ddl_usuario'],$parametros['ddl_modulos'],$parametros['txt_desde'],$parametros['txt_hasta'],$parametros['ddl_num_reg']);
  	$reg = array();
  	foreach ($datos as $key => $value) {
			 $ent = $this->modelo->entidades(false,$value['RUC']);
			 $ent = explode('_', $ent[0]['id']);
			 $empresas = $this->modelo->empresas($ent[1],false,$value['Item']);
  		$reg[] = array('Fecha'=>$value['Fecha'],'Hora'=>$value['Hora'],'Entidad'=>$value['enti'],'IP_Acceso'=>$value['IP_Acceso'],'Aplicacion'=>$value['Aplicacion'],'Tarea'=>$value['Tarea'],'Empresa'=>$empresas[0]['text'],'Usuario'=>$value['nom']); 
  	}
	 $this->modelo->imprimir_excel($reg);
  }

  function grupos($query)
  {
  	$datos = $this->modelo->grupos($query);
  	$res[] = array('id'=>'.','text'=>'TODOS');
  	foreach ($datos as $key => $value) {
  		$res[] = array('id'=>$value['Grupo'],'text'=>$value['Grupo']);
  	}
  	return $res;
  }

  function clientes_x_grupo($query,$grupo)
  {
  	if($grupo=='.'){$grupo= '';}
  	$cod ='';
  	$datos = $this->modelo->Cliente_facturas($cod,$grupo,$query);
  	$res = array();
  	foreach ($datos as $key => $value) {
  		$res[] = array('id'=>$value['Codigo'],'text'=>$value['Cliente'].'  CI:'.$value['CI_RUC'],'email'=>$value['Email'],'data'=>$value);
  	}
  	return $res;
  }

  function clientes2_x_grupo($query,$grupo)
  {
  	if($grupo=='.'){$grupo= '';}
  	$cod ='';
  	$datos = $this->modelo->Cliente_facturas($cod,$grupo,$query);
  	$res[0] = array('id'=>'T','text'=>'Todos','email'=>'','data'=>'');
  	foreach ($datos as $key => $value) {
  		$res[] = array('id'=>$value['Codigo'],'text'=>$value['Cliente'].'  CI:'.$value['CI_RUC'],'email'=>$value['Email'],'data'=>$value);
  	}
  	return $res;
  }
  function validar_cliente($parametros)
  {
  	// print_r($parametros);die();
  	if($parametros['tip']=='2')
  	{
  		$parametros['cla']=false;
  		if($parametros['cli']=='T')
  		{
  			 return 1;
  		}
  	}
  	$dato = $this->modelo->Cliente_facturas($parametros['cli'],false,false,$parametros['cla']);
  	if(empty($dato))
  	{
  		return -1;
  	}else
  	{
  		return 1;
  	}

  } 

   function clientes_datos($parametros)
  {
    $grupo='';
  	if($parametros['gru']!='.'){$grupo= $parametros['gru'];}
  	$query ='';
  	$datos = $this->modelo->Cliente($parametros['ci'],$grupo,$query);
  	return $datos;
  }
  function enviar_mail($parametros)
  {
    $empresaGeneral = array_map(array($this, 'encode1'), $this->empresaGeneral);

  	$nueva_Clave = generate_clave(8);
  	$datos[0]['campo']='Clave';
  	$datos[0]['dato']=$nueva_Clave;

  	$where[0]['campo'] = 'Codigo';
  	$where[0]['valor'] = $parametros['ci'];
  	$where[0]['tipo'] = 'string';

  	$email_conexion = $empresaGeneral[0]['Email_Conexion'];
    $email_pass =  $empresaGeneral[0]['Email_Contraseña'];
    // print_r($empresaGeneral[0]);die();
  	$correo_apooyo="info@diskcoversystem.com"; //correo que saldra ala do del emisor
  	$cuerpo_correo = 'Se a generado una clave temporar para que usted pueda ingresar:'. $nueva_Clave;
  	$titulo_correo = 'EMAIL DE RECUPERACION DE CLAVE';
  	$archivos = false;
  	$correo = $parametros['ema'];
  	// print_r($correo);die();
  	$resp = $this->modelo->ingresar_update($datos,'Clientes',$where);  	
  	
  	if($resp==1)
  	{
  		if($this->email->recuperar_clave($archivos,$correo,$cuerpo_correo,$titulo_correo,$correo_apooyo,'Email de recuperacion',$email_conexion,$email_pass)==1){
  			return 1;
  		}else
  		{
  			return -1;
  		}
  	}else
  	{
  		return -1;
  	}
  }


 function encode1($arr) {
    $new = array(); 
    foreach($arr as $key => $value) {
      if(!is_object($value))
      {
      	if($key=='Archivo_Foto')
      		{
      			if (!file_exists('../../img/img_estudiantes/'.$value)) 
      				{
      					$value='';
      					//$new[utf8_encode($key)] = utf8_encode($value);
      					$new[$key] = $value;
      				}
      		} 
         if($value == '.')
         {
         	$new[$key] = '';
         }else{
         	//$new[utf8_encode($key)] = utf8_encode($value);
         	$new[$key] = $value;
         }
      }else
        {
          //print_r($value);
          $new[$key] = $value->format('Y-m-d');          
        }
     }
     return $new;
    }

    function autorizar($parametros)
    {
    	// $datos[0]['campo'] = 'Autorizacion';
    	// $datos[0]['dato'] = $_SESSION['INGRESO']['RUC'];
    	
    	// $campoWhere[0]['campo'] = 'Item';
    	// $campoWhere[0]['valor'] = $_SESSION['INGRESO']['item'];
    	// $campoWhere[1]['campo'] = 'Periodo';
    	// $campoWhere[1]['valor'] = $_SESSION['INGRESO']['periodo'];
    	// $campoWhere[2]['campo'] = 'TC';
    	// $campoWhere[2]['valor'] = $parametros['tc'];
    	// $campoWhere[3]['campo'] = 'Serie';
    	// $campoWhere[3]['valor'] = $parametros['serie'];
    	// $campoWhere[4]['campo'] = 'Factura';
    	// $campoWhere[4]['valor'] = $parametros['FacturaNo'];

    	 // $this->modelo->ingresar_update($datos,'Facturas',$campoWhere);
    	$res = $this->sri->Autorizar_factura_o_liquidacion($parametros);
    	return $res;
    }

    function anular($parametros)
    {
    	 // print_r($parametros);die();
    	 $datos[0]['campo'] = 'T';
    	 $datos[0]['dato'] = 'A';
    	 $datos[1]['campo'] = 'Nota';
    	 $datos[1]['dato'] = 'Anulción de Factura No.'.$parametros['factura'].'.';

    	 $campoWhere[0]['campo'] = 'Serie';
    	 $campoWhere[0]['valor'] = $parametros['serie'];
    	 $campoWhere[1]['campo'] = 'Factura';
    	 $campoWhere[1]['valor'] = $parametros['factura'];
    	 $campoWhere[2]['campo'] = 'CodigoC';
    	 $campoWhere[2]['valor'] = $parametros['codigo'];

    	 $tabla = 'Facturas';
    	 $this->modelo->ingresar_update($datos,$tabla,$campoWhere);


    	 $datos1[0]['campo'] = 'T';
    	 $datos1[0]['dato'] = 'A';
    	
    	 $campoWhere1[0]['campo'] = 'Serie';
    	 $campoWhere1[0]['valor'] = $parametros['serie'];
    	 $campoWhere1[1]['campo'] = 'Factura';
    	 $campoWhere1[1]['valor'] = $parametros['factura'];
    	 $campoWhere1[2]['campo'] = 'CodigoC';
    	 $campoWhere1[2]['valor'] = $parametros['codigo'];

    	 $tabla1 = 'Detalle_Factura';
    	 $this->modelo->ingresar_update($datos1,$tabla1,$campoWhere1);

    	 return $this->modelo->eliminar_abonos($parametros);




    }

        
}
?>