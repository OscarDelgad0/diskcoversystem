<?php
//Llamada al modelo
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../modelo/usuario_model.php");
require(dirname(__DIR__,2).'/lib/phpmailer/enviar_emails.php');
$login = new login_controller();
if(isset($_GET['Entidad']))
{
	$entidad = $_POST['entidad'];
	$_SESSION['INGRESO']['Height_pantalla'] = $_GET['pantalla'];
	echo json_encode($login->validar_entidad($entidad));
}

if(isset($_GET['Cartera_Entidad']))
{
	$entidad = $_POST['entidad'];
	$_SESSION['INGRESO']['Height_pantalla'] = $_GET['pantalla'];
	echo json_encode($login->validar_entidad_cartera($entidad));
} 
if(isset($_GET['Usuario']))
{
	$parametro = $_POST['parametros'];
	echo json_encode($login->validar_usuario($parametro));
}
if(isset($_GET['Ingresar']))
{
	$parametro = $_POST['parametros'];
	echo json_encode($login->login($parametro));
}
if(isset($_GET['Ingresar_cartera']))
{
	$parametro = $_POST['parametros'];
	echo json_encode($login->login_cartera($parametro));
}
if(isset($_GET['recuperar']))
{
	$parametro = $_POST['parametros'];
	echo json_encode($login->recuperar_clave($parametro));
}
if(isset($_GET['logout']))
{
	echo json_encode($login->logout());
}

/**
 * 
 */
class login_controller
{
	private $modelo;
	private $email;
	function __construct()
	{
		$this->modelo = new  usuario_model();
		$this->email = new enviar_emails();	
	}

	function validar_entidad($entidad)
	{
		$datos = $this->modelo->ValidarEntidad1($entidad);
		// $datos['cartera'] = 0;
		// if($datos['respuesta']==-1)
		// {
		// 	$datos = $this->modelo->empresa_cartera($entidad);
		// 	// print_r($datos);die();
		// 	if(count($datos)>0)
		// 	{
		// 		$datos['cartera'] = 1;
		// 		$datos['cartera_usu'] = 'Cartera';
		// 		$datos['cartera_pass'] = '999999';
		// 		$datos['respuesta'] = 1;
		// 		$datos['entidad'] = $datos[0]['ID_Empresa'];
		// 		$datos['Nombre'] = $datos[0]['Empresa'];
		// 		$datos['Item'] = $datos[0]['Item'];
		// 		$_SESSION['INGRESO']['CARTERA_ITEM'] = $datos[0]['Item'];
		// 	}else
		// 	{
		// 		//retorna -1 cuando no se encuentra la empresa 			
		// 		$datos['respuesta'] = -1;
		// 		$datos['entidad'] = '';
		// 		$datos['Nombre'] = '';
		// 	}
			
		// }
		return $datos;
		// print_r($datos);die();
	}

	function validar_entidad_cartera($entidad)
	{
		
		$datos = $this->modelo->empresa_cartera($entidad);
			// print_r($datos);die();
			if(count($datos)>0)
			{
				
				$datos1['respuesta'] = 1;
				$datos1['entidad'] = $datos[0]['ID_Empresa'];
				$datos1['Nombre'] = $datos[0]['Empresa'];
				$datos1['Razon_Social'] = $datos[0]['Razon_Social'];
				$datos1['Item'] = $datos[0]['Item'];

				 $url = '../../img/jpg/logo.jpg'; 
	            $tipo_img = array('jpg','gif','png','jpeg');
	              foreach ($tipo_img as $key => $value) {
	                if(file_exists( dirname(__DIR__,2). '/img/logotipos/'.$datos[0]['Logo_Tipo'].'.'.$value))
	                {                   
	                  $url='../../img/logotipos/'.$datos[0]['Logo_Tipo'].'.'.$value;
	                  break;
	                }
	              }
		 
				$datos1['Logo'] = $url;
				$_SESSION['INGRESO']['CARTERA_ITEM'] = $datos[0]['Item'];
			}else
			{
				//retorna -1 cuando no se encuentra la empresa 			
				$datos1['respuesta'] = -1;
				$datos1['entidad'] = '';
				$datos1['Nombre'] = '';
			}
			
		return $datos1;
		// print_r($datos);die();
	}

	function validar_usuario($parametro)
	{
		$datos = $this->modelo->ValidarUser1($parametro['usuario'],$parametro['entidad']);
		$datos['cartera_usu'] = 'Cartera';
		$datos['cartera_pass'] = '999999';
		return $datos;
	}
	function login($parametro)
	{
		
		// print_r($parametro);
		// print_r($datos);
		// die();
		if($parametro['cartera']==1)
		{
			$datos = $this->modelo->Ingresar($parametro['cartera_usu'],$parametro['cartera_pass'],$parametro['entidad']);
			// validar cliente en cartera
			$empresa = $this->modelo->empresa_cartera($parametro['empresa'],$parametro['entidad']);
			// print_r($empresa);die();
			$cliente = $this->modelo->buscar_cliente_cartera($parametro['usuario'],$parametro['pass'],$empresa);
			if(count($cliente)==0)
			{
				return -2;
			}else
			{
				$_SESSION['INGRESO']['CARTERA_USUARIO'] = $parametro['usuario'];
				$_SESSION['INGRESO']['CARTERA_PASS'] = $parametro['pass'];
			}

			// print_r($cliente);die();
			// print_r($empresa);print_r($cliente);die();
		}else
		{
			$datos = $this->modelo->Ingresar($parametro['usuario'],$parametro['pass'],$parametro['entidad']);
		}


		return $datos;
	}
	function login_cartera($parametro)
	{
		$datos = $this->modelo->Ingresar($parametro['usuario'],$parametro['pass'],$parametro['entidad']);

		// print_r($parametro);
		// print_r($datos);
		// die();
		if($parametro['cartera']==1)
		{
			// validar cliente en cartera
			$empresa = $this->modelo->empresa_cartera($parametro['empresa'],$parametro['entidad']);
			// print_r($empresa);die();
			$cliente = $this->modelo->buscar_cliente_cartera($parametro['cartera_usu'],$parametro['cartera_pass'],$empresa);
			if(count($cliente)==0)
			{
				return -2;
			}else
			{
				$_SESSION['INGRESO']['CARTERA_USUARIO'] = $parametro['cartera_usu'];
				$_SESSION['INGRESO']['CARTERA_PASS'] = $parametro['cartera_pass'];
			}

			// print_r($cliente);die();
			// print_r($empresa);print_r($cliente);die();
		}


		return $datos;
	}

	function logout()
	{
		session_destroy(); 
		return 1;
	}


	function recuperar_clave($parametro)
	{
		// print_r($parametro);die();
		//entra a buscar en cartera
		if(is_numeric($parametro['usuario']))
		{
			$empresa = $this->modelo->empresa_cartera($parametro['empresa'],$parametro['entidad']);
			// print_r($empresa);die();
			$datos = $this->modelo->buscar_cliente_cartera($parametro['usuario'],false,$empresa);
			// print_r($datos);die();
			if(count($datos)>0)
			{
				$datos_email = array(
					 'nick'=>$datos[0]['CI_RUC'],
				    'clave'=>$datos[0]['Clave'],
				    'email'=>$datos[0]['Email'],
				    'entidad'=>$empresa[0]['Razon_Social'],
				    'ruc'=>$parametro['empresa'],
				    'usuario'=>$datos[0]['Cliente'], 
				    'CI_usuario'=>$datos[0]['CI_RUC'],
				    'cartera'=>1,
				);
				// print_r($datos_email);die();
				$rep = $this->enviar_email($datos_email);
				return array('respuesta'=>$rep,'email'=>$datos_email['email']);
			}else
			{
				return -1;
			}
		}else
		{
			//entra a buscar en usuarios de sistema
			// print_r($parametro);die();
			$datos = $this->modelo->datos_usuario_mysql($usuario=false,$entidad=false,$parametro['usuario']);
			// print_r($datos);die();
			if(count($datos)>0)
			{
				$datos_email = array(
					'nick'=>$datos[0]['Usuario'],
				    'clave'=>$datos[0]['Clave'],
				    'email'=>$datos[0]['Email'],
				    'entidad'=>'',
				    'ruc'=>$parametro['empresa'],
				    'usuario'=>$datos[0]['Nombre_Usuario'], 
				    'CI_usuario'=>$datos[0]['CI_NIC'],
				    'cartera'=>0,
				);
				// print_r($datos_email);die();
				$rep =  $this->enviar_email($datos_email);
				return array('respuesta'=>$rep,'email'=>$datos_email['email']);
			}else
			{
				return -1;
			}

			// print_r($datos);die();
		}

	}

	function enviar_email($parametros)
  	{
  		$empresaGeneral = $this->modelo->Empresa_data($parametros['ruc']);
  		// print_r($empresaGeneral);die();
  		// print_r($empresaGeneral);die();
	    $datos = $this->modelo->entidades_usuario($parametros['CI_usuario']);
	    if($parametros['cartera']==1)
	    {
	    	$datos[0]['Nombre_Usuario'] = $parametros['usuario'];
	    	$datos[0]['Usuario'] = $parametros['nick'];
	    	$datos[0]['Clave'] = $parametros['clave']; 
	    	$datos[0]['Email'] = $parametros['email'];
	    }

	    // print_r($datos);die();

	  	$email_conexion = 'info@diskcoversystem.com'; 
	    $email_pass =  'info2021DiskCover'; 	   
	  	$correo_apooyo="credenciales@diskcoversystem.com"; //correo que saldra ala do del emisor
	  	$cuerpo_correo = '
<pre>
Este correo electronico fue generado automaticamente del Sistema Administrativo Financiero Contable DiskCover System a usted porque figura como correo electronico alternativo en nuestra base de datos.

A solicitud de El(a) Sr(a) '.$parametros['usuario'].' se envian sus credenciales de acceso:
</pre> 
<br>
<table>
<tr><td><b>Link:</b></td><td>https://erp.diskcoversystem.com</td></tr>';
if($parametros['cartera']==1)
{
	$cuerpo_correo.= '<tr><td><b>Entidad:</b></td><td>'.$parametros['entidad'].'</td></tr>
	<tr><td><b>Ruc:</b></td><td>'.$parametros['ruc'].'</td></tr>';
}
$cuerpo_correo.='<tr><td><b>Nombre Usuario:</b></td><td>'.$datos[0]['Nombre_Usuario'].'</td></tr>
<tr><td><b>Usuario:</b></td><td>'.$datos[0]['Usuario'].'</td></tr>
<tr><td><b>Clave:</b></td><td>'.$datos[0]['Clave'].'</td></tr>
<tr><td><b>Email:</b></td><td>'.$datos[0]['Email'].'</td></tr>
</table>
A este usuario se asigno las siguientes Entidades: 
<br>';
if($parametros['cartera']==0)
{
	$cuerpo_correo.='<table> <tr><td width="30%"><b>Codigo</b></td><td width="50%"><b>Entidad</b></td></tr>';
	foreach ($datos as $value) {
		$cuerpo_correo .= '<tr><td>'.$value['id'].'</td><td>'.$value['text'].'</td></tr>';
	}
}
$cuerpo_correo.='</table><br>';
$cuerpo_correo .= ' 
<pre>
Nosotros respetamos su privacidad y solamente se utiliza este correo electronico para mantenerlo informado sobre nuestras ofertas, promociones, claves de acceso y comunicados. No compartimos, publicamos o vendemos su informacion personal fuera de nuestra empresa.

Esta direccion de correo electronico no admite respuestas. En caso de requerir atencion personalizada por parte de un asesor de servicio al cliente; Usted podra solicitar ayuda mediante los canales de atencion al cliente oficiales que detallamos a continuacion:
</pre>
<table width="100%">
<tr>
 <td align="center">
 <hr>
    SERVIRLES ES NUESTRO COMPROMISO, DISFRUTARLO ES EL SUYO
<hr>
    </td>
    </tr>
    <tr>   
 <td align="center">
    www.diskcoversystem.com
    </td>
    </tr>
     <tr>   
 <td align="center">
        QUITO - ECUADOR
    </td>
    </tr>
  </table>
';

	  	$titulo_correo = 'Credenciales de acceso al sistema DiskCover System';
	  	$archivos = false;
	  	$correo = $parametros['email'];
	  	// print_r($correo);die();
	  	// $resp = $this->modelo->ingresar_update($datos,'Clientes',$where);  	
	  	
	  	// if($resp==1)
	  	// {
	  	if($this->email->enviar_credenciales($archivos,$correo,$cuerpo_correo,$titulo_correo,$correo_apooyo,'Credenciales de acceso al sistema DiskCover System',$email_conexion,$email_pass,$html=1,$empresaGeneral)==1){
	  		return 1;
	  	}else{
	  		// echo json_encode(-1);
	  		return -1;
	  	}
	  	// }else
	  	// {
	  		// return -1;
	  	// }
  	}


}


?>
