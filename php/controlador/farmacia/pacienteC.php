<?php 
require(dirname(__DIR__,2).'/modelo/farmacia/pacienteM.php');
/**
 * 
 */
$controlador = new pacienteC();
if(isset($_GET['provincias']))
{
	$respuesta = $controlador->provincias();
	echo json_encode($respuesta);
}
if(isset($_GET['pacientes']))
{
	$parametros = $_POST['parametros'];
	$respuesta = $controlador->cargar_paciente($parametros);
	echo json_encode($respuesta);
}
if(isset($_GET['buscar_edi']))
{
	$respuesta = $controlador->buscar_ficha($_POST['parametros']);
	echo json_encode($respuesta);
}
if(isset($_GET['buscar_edi_solicitante']))
{
	$respuesta = $controlador->buscar_ficha_solicitante($_POST['parametros']);
	echo json_encode($respuesta);
}
if(isset($_GET['nuevo']))
{
	$respuesta = $controlador->insertar_paciente($_POST['parametros']);
	echo json_encode($respuesta);
}
if(isset($_GET['eliminar']))
{
	$respuesta = $controlador->eliminar_paciente($_POST['cli'],$_POST['ruc']);
	echo json_encode($respuesta);
}
if(isset($_GET['historial_existente']))
{
	$respuesta = $controlador->historial_existente($_POST['parametros']);
	echo json_encode($respuesta);
}
if(isset($_GET['paciente_existente']))
{
	$respuesta = $controlador->buscar_ficha($_POST['parametros']);
	echo json_encode($respuesta);
}
if(isset($_GET['validar_ci']))
{
	$ci =$_POST['num'] ;
	echo json_encode(digito_verificador_nuevo($ci));
}
class pacienteC
{
	private $modelo;
	function __construct()
	{
		$this->modelo = new pacienteM();
	}

	function cargar_paciente($parametros)
	{
		$datos = $this->modelo->cargar_paciente($parametros,$parametros['pag']);
		$paginacion = paginancion('Clientes',$parametros['fun'],$parametros['pag']);
		// print_r($paginacion);die();
		$tr = '';
		foreach ($datos as $key => $value) 
		{
			$d =  dimenciones_tabl(strlen($value['ID']));
			$d2 =  dimenciones_tabl(strlen($value['Codigo']));
			$d3 =  dimenciones_tabl(strlen($value['Cliente']));
			$d4 =  dimenciones_tabl(strlen($value['CI_RUC']));
			$d5 =  dimenciones_tabl(strlen($value['Telefono']));
			$tr.='<tr>
  					<td width="'.$d.'">'.$value['ID'].'</td>
  					<td width="'.$d2.'">'.$value['Matricula'].'</td>
  					<td width="'.$d3.'">'.$value['Cliente'].'</td>
  					<td width="'.$d4.'">'.$value['CI_RUC'].'</td>
  					<td width="'.$d5.'">'.$value['Telefono'].'</td>
  					<td width="100px">
  					    <a href="../vista/farmacia.php?mod='.$_SESSION['INGRESO']['modulo_'].'&acc=vis_descargos&acc1=Visualizar%20descargos&b=1&po=subcu&cod='.$value['Matricula'].'&ci='.$value['CI_RUC'].'#" class="btn btn-sm btn-default" title="Ver Historial"><span class="glyphicon glyphicon-th-large"></span></a>
  						<button class="btn btn-sm btn-primary" onclick="buscar_cod(\'E\',\''.$value['CI_RUC'].'\')" title="Editar paciente"><span class="glyphicon glyphicon-pencil"></span></button>  						
  						<button class="btn btn-sm btn-danger" title="Eliminar paciente"  onclick="eliminar(\''.$value['ID'].'\',\''.$value['CI_RUC'].'\')" ><span class="glyphicon glyphicon-trash"></span></button>
  					</td>
  				</tr>';
			
		}
		$tabla = array('tr'=>$tr,'pag'=>$paginacion);

		// print_r($tabla);die();
		return $tabla;
		
	}
	function buscar_ficha($parametros)
	{
		// print_r($parametros);die();
		$datos = $this->modelo->cargar_paciente_all($parametros,false,false);
		if(!empty($datos))
		{
			$ficha = array('id'=>$datos[0]['ID'],'nombre'=>$datos[0]['Cliente'],'ci'=>$datos[0]['CI_RUC'],'prov'=>$datos[0]['Prov'],'localidad'=>$datos[0]['Direccion'],'telefono'=>$datos[0]['Telefono'],'email'=>$datos[0]['Email'],'matricula'=>$datos[0]['Matricula'],'Codigo'=>$datos[0]['Codigo']);
			// $ficha = array('id'=>$datos[0]['ID'],'nombre'=>$datos[0]['Cliente'],'ci'=>$datos[0]['CI_RUC'],'prov'=>$datos[0]['Prov'],'localidd'=>$datos[0]['Direccion'],'telefono'=>$datos[0]['Telefono'],'email'=>$datos[0]['Email'],'matricula'=>$datos[0]['Matricula']);
			
			return $ficha;
		}else
		{
			return -1;
		}
		
	}

	function buscar_ficha_solicitante($parametros)
	{

		// print_r($parametros);die();
		$datos = $this->modelo->cargar_paciente($parametros);
		if(!empty($datos))
		{
			$ficha = array('id'=>$datos[0]['ID'],'nombre'=>$datos[0]['Cliente'],'ci'=>$datos[0]['CI_RUC'],'prov'=>$datos[0]['Prov'],'localidad'=>$datos[0]['Direccion'],'telefono'=>$datos[0]['Telefono'],'email'=>$datos[0]['Email'],'matricula'=>$datos[0]['Matricula'],'Codigo'=>$datos[0]['Codigo']);
			// $ficha = array('id'=>$datos[0]['ID'],'nombre'=>$datos[0]['Cliente'],'ci'=>$datos[0]['CI_RUC'],'prov'=>$datos[0]['Prov'],'localidd'=>$datos[0]['Direccion'],'telefono'=>$datos[0]['Telefono'],'email'=>$datos[0]['Email'],'matricula'=>$datos[0]['Matricula']);
			
			return $ficha;
		}else
		{
			return -1;
		}
		
	}

	function insertar_paciente($parametros)
	{
		$datos[0]['campo']='Cliente';
		$datos[0]['dato']=$parametros['nom'];
		$datos[1]['campo']='CI_RUC';
		$datos[1]['dato']=$parametros['ruc'];
		$datos[2]['campo']='Prov';
		$datos[2]['dato']=$parametros['pro'];
		$datos[3]['campo']='Ciudad';
		$datos[3]['dato']=$parametros['loc'];
		$datos[4]['campo']='Telefono';
		$datos[4]['dato']=$parametros['tel'];
		$datos[5]['campo']='Email';
		$datos[5]['dato']=$parametros['ema'];
		$datos[6]['campo']='Matricula';
		$datos[6]['dato']=$parametros['cod'];

		if($parametros['tip']=='E')
		{
		
		$codig = digito_verificador_nuevo($parametros['ruc']);
		// print_r($codig);die();
		if($codig['Tipo']!='C')
		{
			return -2;
		}
		$datos[7]['campo'] = 'Codigo';
		$datos[7]['dato']=$codig['Codigo'];
		$datos[8]['campo'] = 'TD';
		$datos[8]['dato']=$codig['Tipo'];

			$campoWhere[0]['campo']='ID';
			$campoWhere[0]['valor']=$parametros['id'];
			
			return  $this->modelo->insertar_paciente($datos,$campoWhere,$parametros['tip']);
		}else
		{
		
		$codig = digito_verificador_nuevo($parametros['ruc']);
		// print_r($codig);die();
		if($codig['Tipo']!='C')
		{
			return -2;
		}
		$datos[7]['campo'] = 'T';
		$datos[7]['dato']='N';
		$datos[8]['campo'] = 'Codigo';
		$datos[8]['dato']=$codig['Codigo'];
		$datos[9]['campo'] = 'TD';
		$datos[9]['dato']=$codig['Tipo'];
			return  $this->modelo->insertar_paciente($datos,false,$parametros['tip']);
		}

	}
	function eliminar_paciente($cli,$ruc)
	{
		$num_c = strlen($ruc);
		if($num_c>10)
		{
			$ruc = substr($ruc,0,-3);
			$resp = $this->modelo->existe_transacciones_subcuenta_abonos_air_compras($ruc);
			if($resp ==1)
			{
				return -1;
			}else
			{
				return 1;//funcion eliminar falta
			}
		}else
		{
			// $ruc = substr($ruc,0,-3);

			// print_r('ssssss');die();
			$resp = $this->modelo->existe_transacciones_subcuenta_abonos_air_compras($ruc);
			if($resp ==1)
			{
				return -1;
			}else
			{
				return $this->modelo->eliminar_paciente($cli);
				//return 1;//funcion eliminar falta
			}

		}
		

	}

	function historial_existente($parametros)
	{
		$resp = $this->modelo->cargar_paciente($parametros);
		if(!empty($resp))
		{
			return -1;
		}else
		{
			return 1;
		}
	}

	function imprimir_paciente()
	{

	}
	function provincias()
	{
		$prov = $this->modelo->provincias();
		return $prov;
	}
}

?>