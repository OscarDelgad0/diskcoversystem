<?php 
require(dirname(__DIR__,2).'/modelo/empresa/niveles_seguriM.php');
require(dirname(__DIR__,3).'/lib/phpmailer/enviar_emails.php');
//require_once(dirname(__DIR__)."/modelo/facturacion/lista_facturasM.php");

/**
 * 
 */
$controlador = new niveles_seguriC();
if (isset($_GET['modulos'])) {
	echo json_encode($controlador->modulos($_POST['parametros']));
}
if (isset($_GET['empresas'])) {
	echo json_encode($controlador->empresas($_POST['entidad']));
}
if (isset($_GET['usuarios'])) {
	if(!isset($_GET['q']))
	{
		$_GET['q'] =''; 
	}
	$parametros = array('entidad'=>$_GET['entidad'],'query'=>$_GET['q']);
	echo json_encode($controlador->usuarios($parametros));
}
if (isset($_GET['entidades'])) {
	if(!isset($_GET['q']))
	{
		$_GET['q']='';
	}
	echo json_encode($controlador->entidades($_GET['q']));
}
if(isset($_GET['mod_activos']))
{
	$parametros=$_POST['parametros'];
	echo json_encode($controlador->mod_activos($parametros['entidad'],$parametros['empresa'],$parametros['usuario']));
}
if(isset($_GET['usuario_data']))
{
	$parametros=$_POST['parametros'];
	echo json_encode($controlador->data_usuario($parametros['entidad'],$parametros['usuario']));
}
if(isset($_GET['guardar_datos']))
{
	$parametros=$_POST['parametros'];
	echo json_encode($controlador->guardar_datos_modulo($parametros));
}
if(isset($_GET['bloqueado']))
{
	$parametros=$_POST['parametros'];
	echo json_encode($controlador->bloqueado_usurio($parametros));
}
if(isset($_GET['nuevo_usuario']))
{
	$parametros=$_POST['parametros'];
	echo json_encode($controlador->nuevo_usurio($parametros));
}
if(isset($_GET['buscar_ruc']))
{
	$parametros=$_POST['ruc'];
	echo json_encode($controlador->buscar_ruc($parametros));
}
if(isset($_GET['usuario_empresa']))
{
	$parametros=$_POST['parametros'];
	echo json_encode($controlador->modulos_usuario($parametros['entidad'],$parametros['usuario']));
	// echo json_encode($controlador->usuario_empresa($parametros['entidad'],$parametros['usuario']));
}

if(isset($_GET['acceso_todos']))
{
	$parametros=$_POST['parametros'];
	echo json_encode($controlador->accesos_todos($parametros));
	// echo json_encode($controlador->usuario_empresa($parametros['entidad'],$parametros['usuario']));
}
if(isset($_GET['enviar_email']))
{
	$parametros=$_POST['parametros'];
	echo json_encode($controlador->enviar_email($parametros));
	// echo json_encode($controlador->usuario_empresa($parametros['entidad'],$parametros['usuario']));
}
if(isset($_GET['confirmar_enviar_email']))
{
	$parametros=$_POST['parametros'];
	echo json_encode($controlador->confirmar_enviar_email($parametros));
	// echo json_encode($controlador->usuario_empresa($parametros['entidad'],$parametros['usuario']));
}

if(isset($_GET['enviar_email_masivo']))
{
	$parametros=$_POST['parametros'];
	echo json_encode($controlador->enviar_email_masivo($parametros));
	// echo json_encode($controlador->usuario_empresa($parametros['entidad'],$parametros['usuario']));
}

class niveles_seguriC
{
	private $modelo;
	private $email;
	
	function __construct()
	{
		$this->modelo = new niveles_seguriM();	
		$this->email = new enviar_emails();	
		//$this->modeloFac = new lista_facturasM();
		// $this->empresaGeneral = $this->modelo->Empresa_data();
	}
	function entidades($valor)
	{
		$entidades = $this->modelo->entidades($valor);
		echo json_encode($entidades);
		exit();
		return $entidades;

	}
	// function empresas($entidad)
	// {
	// 	 $this->acceso_modulos($entidad);
	// 	// $empresas = $this->modelo->empresas($entidad);
	// 	// $items = '';
	// 	// $linea = '';
	// 	// foreach ($empresas as $key => $value) {
	// 	// 	$linea.= $value['id'].',';
	// 	// 	$items .= '<label class="checkbox-inline" id="lbl_'.$value['id'].'"><input type="checkbox" name="empresas[]" id="emp_'.$value['id'].'" value="'.$value['id'].'" onclick="empresa_select(\''.$value['id'].'\')"><i class="fa fa-circle-o text-red" style="display:none" id="indice_'.$value['id'].'"></i><b>'.utf8_decode($value['text']).'</b></label><br>';
			
	// 	// }
	// 	// $linea = substr($linea,0,-1);
	// 	// return  array('items' => $items,'linea'=>$linea);

	// }
	function usuarios($parametros)
	{
		$parametros['entidad'] = '';
		$usuarios = $this->modelo->usuarios($parametros['entidad'],$parametros['query']);
		// print_r($usuarios);die();
		return $usuarios;
	}
	function modulos($parametros)
	{
		// print_r($parametros);die();
		$conjunto_empresa = substr($parametros['empresa'],0,-1);
		$empresa_selec =explode(',', $conjunto_empresa);
		$items = '';
		$tabs = '';
		foreach ($empresa_selec as $key => $value) {
			$datos_empresas = $this->modelo->empresas_datos($parametros['entidad'],$value);
			if(count($datos_empresas)>0)
			{
				if($key==0)
				{
					$tabs.='<li class="active" id="tab_'.$value.'" onclick="activo(\''.$value.'\')"><a data-toggle="tab" href="#'.$datos_empresas[0]['Item'].'">'.$datos_empresas[0]['text'].'</a></li>';

				}else
				{
					$tabs.='<li><a data-toggle="tab" href="#'.$datos_empresas[0]['id'].'">'.$datos_empresas[0]['text'].'</a></li>';
				}
				 $modulos = $this->modelo->modulos_todo();
				 if(count($datos_empresas)>0)
				 {				 	
				 	if($key==0)
				 	{
				 		$items.='<div id="'.$datos_empresas[0]['Item'].'" class="tab-pane fade in active">';
				 	}else
				 	{
				 		$items.='<div id="'.$datos_empresas[0]['Item'].'" class="tab-pane fade">';
				 	}
				 	$items.='<form id="form_'.$value.'">';
				 	$mod = $this->modelo->acceso_empresas($parametros['entidad'],$value,$parametros['usu']);
				 	$existente = 0;
				 		// print_r($mod);die();
				 	foreach ($modulos as $key1 => $value1) {
				 		if(count($mod)>0)
				 		{
				 			foreach ($mod as $key2 => $value2) {
				 				if ($value2['Modulo'] == $value1['modulo']) {
				 					$existente = 1;
				 					break;
				 				}
				 			}
				 			if($existente == 1)
				 			 {
				 			 	$items .= '<label class="checkbox-inline"><input type="checkbox" name="modulos_'.$value.'_'.$value1['modulo'].'" id="modulos_'.$value.'_'.$value1['modulo'].'" value="'.$value1['modulo'].'" checked><b>'.$value1['aplicacion'].'</b></label><br>';				 				
				 			 }else
				 			 {
				 			 	$items .= '<label class="checkbox-inline"><input type="checkbox" name="modulos_'.$value.'_'.$value1['modulo'].'" id="modulos_'.$value.'_'.$value1['modulo'].'" value="'.$value1['modulo'].'"><b>'.$value1['aplicacion'].'</b></label><br>';
				 			 }
				 			 $existente = 0;
				 		}else
				 		{
				 			$items .= '<label class="checkbox-inline"><input type="checkbox" name="modulos_'.$value.'_'.$value1['modulo'].'" id="modulos_'.$value.'_'.$value1['modulo'].'" value="'.$value1['modulo'].'"><b>'.$value1['aplicacion'].'</b></label><br>';
				 		}
				 	}
				 	$items.='</form></div>';
				 }

			}			
		}
		$contenido = array('header'=>$tabs,'body'=>'<div class="tab-content" id="tab-content">'.$items.'</div>');
		// print_r($contenido);die();		
		return $contenido;

	}
	function mod_activos($entidad,$empresa,$usuario)
	{
		$mod = $this->modelo->acceso_empresas($entidad,$empresa,$usuario);
		return $mod;

	}
	function data_usuario($entidad,$usuario)
	{
		$data = $this->modelo->datos_usuario($entidad,$usuario);
		return $data;
	}
	function guardar_datos_modulo($parametros)
	{
		$r = $this->modelo->existe_en_SQLSERVER($parametros);
		// print_r($r);die();
		if($r==1)
		{		

		$niveles = array('1'=>$parametros['n1'],'2'=>$parametros['n2'],'3'=>$parametros['n3'],'4'=>$parametros['n4'],'5'=>$parametros['n5'],'6'=>$parametros['n6'],'7'=>$parametros['n7'],'super'=>$parametros['super']);

		// $insert = $this->modelo->guardar_acceso_empresa($modulos,$parametros['entidad'],$empresa,$parametros['CI_usuario']);


		$update = $this->modelo->update_acceso_usuario($niveles,$parametros['usuario'],$parametros['pass'],$parametros['entidad'],$parametros['CI_usuario'],$parametros['email']);
		if($update == 1)
		{
			return 1;
		}else
		{
			return -1 ;
		}
	}else
	{
		return -2;
	}


	}
	function bloqueado_usurio($parametros)
	{
		$rest = $this->modelo->bloquear_usuario($parametros['entidad'],$parametros['usuario']);
		return $rest;

	}
	function nuevo_usurio($parametros)
	{
		// print_r($parametros);die();
		$parametros['n1'] = 0;
		$parametros['n2'] = 0;
		$parametros['n3'] = 0;
		$parametros['n4'] = 0;
		$parametros['n5'] = 0;
		$parametros['n6'] = 0;
		$parametros['n7'] = 0;
		$parametros['super'] = 0;
		$parametros['email'] = '.';
		$existe = $this->modelo->usuario_existente($parametros['usu'],$parametros['cla'],$parametros['ent']);
		if($existe == 1)
		{
			return -2;
		}else
		{
			$op = $this->modelo->nuevo_usuario($parametros);
			if($op==1)
			{
				return 1;
			}else if($op == -3)
			{
				return -3;
			}
			else
			{
				return -1;
			}			
		}
		// $rest = $this->modelo->nuevo_usuario();
		// return $rest;

	}

	function buscar_ruc($parametros)
	{
		// print_r($parametros);die();
		$existe = $this->modelo->buscar_ruc($parametros);
		if(count($existe)>0)
		{
			return $existe;
		}else
		{
			return -1;
		}

	}

	// function usuario_empresa($entidad,$usuario)
	// {
	// 	$emp = $this->modelo->usuario_empresas($entidad,$usuario);
	// 	$linea = '';
	// 	foreach ($emp as $key => $value) {
	// 		$linea.=$value['Item'].',';
	// 	}

	// 	// print_r($linea);die();
	// 	return $linea;
	// }


	function empresas($entidad)
	{
		$tbl2 = '';
		// $tbl='<table class="table table-hover table-bordered"><thead><tr style="height:70px" class="bg-info"><th style="width:250px"></th><th style="width: 50px;">Todos</th>';
		$modulos = $this->modelo->modulos_todo();
		// // print_r($modulos);die();
		// foreach ($modulos as $key => $value) {
		// 	$tbl.='<th style="width: 50px; text-align: center; transform: rotate(-45deg);">'.$value['aplicacion'].'</th>';
		// }
		// $tbl.='</tr></thead><tbody>';		
		$empresas = $this->modelo->empresas($entidad);
		$usuarios_reg = $this->modelo->usuarios_registrados_entidad($entidad);
		// //print_r($empresas);die();
		// foreach ($empresas as $key1 => $value1) {
		// 	$tbl.='<tr><td style="width: 250px;"><i class="fa fa-circle-o text-red" style="display:none" id="indice_'.$value1['id'].'"></i><b>'.$value1['text'].'</b></td><td style="width: 50px; class="text-center" style="border: solid 1px;"><input type="checkbox" name="rbl_'.$value1['id'].'_T" id="rbl_'.$value1['id'].'_T" onclick="marcar_all(\''.$value1['id'].'\')" ></td>';
		// 	foreach ($modulos as $key2 => $value2) {				
		// 		$tbl.='<td style="width: 50px; class="text-center" style="border: solid 1px;"><input type="checkbox" name="rbl_'.$value2['modulo'].'_'.$value1['id'].'" id="rbl_'.$value2['modulo'].'_'.$value1['id'].'" title="'.$value2['aplicacion'].'" onclick="marcar_acceso(\''.$value1['id'].'\',\''.$value2['modulo'].'\')" ></td>';
		// 	}
		// 	$tbl.='</tr>';	
		// }
		// $tbl.='</tbody></table>';
          // $tbl2.='';

			foreach ($empresas as $key1 => $value1) {
				$tbl2.='<div class="row">
								<div class=" col-xs-2 col-sm-3 col-lg-3" style="background-color:#e2fbff;">
								
										<b>'.$value1['text'].'</b> 
								</div>
            				<div class="col-xs-10 col-lg-9" style="overflow-x:scroll;">
              					<div class="row"><div class="col-sm-12">';


			// $tbl.='<tr><td style="width: 250px;"><i class="fa fa-circle-o text-red" style="display:none" id="indice_'.$value1['id'].'"></i><b>'.$value1['text'].'</b></td><td style="width: 50px; class="text-center" style="border: solid 1px;"><input type="checkbox" name="rbl_'.$value1['id'].'_T" id="rbl_'.$value1['id'].'_T" onclick="marcar_all(\''.$value1['id'].'\')" ></td>';
			   $tbl2.=' <table class="table-sm" style="margin-bottom:0px;font-size:11px"><tr>';
			foreach ($modulos as $key2 => $value2) {				
				$tbl2.='<td class="text-center" style="border: solid 1px; width: 50px;">
								'.$value2['aplicacion'].'</br>
				            <input type="checkbox" name="rbl_'.$value2['modulo'].'_'.$value1['id'].'" id="rbl_'.$value2['modulo'].'_'.$value1['id'].'" title="'.$value2['aplicacion'].'" onclick="marcar_acceso(\''.$value1['id'].'\',\''.$value2['modulo'].'\')" >
				        </td>';
			}
			$tbl2.='</tr></table></div></div></div></div></br>';	

			// print_r($tbl2);die();
		}



		$usuarios = '';
		foreach ($usuarios_reg as $key => $value) {
			$usuarios.='<tr><td><b>'.$value['CI_NIC'].'</b></td><td>'.$value['Nombre_Usuario'].'</td><td>'.$value['Email'].'</td></tr>';
		}
		// print_r($tbl);die();
		// return utf8_decode($tbl);
		$tbl = array('tbl'=>$tbl2,'usuarios'=>$usuarios);
		return $tbl;
	}

	function modulos_usuario($entidad,$usuario)
	{
		$datos = $this->modelo->accesos_modulos($entidad,$usuario);
		$rbl = array();
		foreach ($datos as $key => $value) {
			// print_r($value);die();
			$rbl[] = 'rbl_'.$value['Modulo'].'_'.$value['Item'];
		}
		return $rbl;
	}
	function accesos_todos($parametros)
	{
		// print_r($parametros);die();
		if($parametros['item']!='' && $parametros['modulo']=='')
		{
			if( $parametros['check']=='true')
			{
			     $this->modelo->delete_modulos($parametros['entidad'],$parametros['item'],$parametros['usuario']);
			     $modulos = $this->modelo->modulos_todo();
			     $m = '';
			     foreach ($modulos as $key => $value) {
				     $m.=$value['modulo'].',';
			     }

			     $m = substr($m,0,-1);
			     $res = $this->modelo->guardar_acceso_empresa($m,$parametros['entidad'],$parametros['item'],$parametros['usuario']);
			     return $res;
		    }else
		    {
			   $resp =   $this->modelo->delete_modulos($parametros['entidad'],$parametros['item'],$parametros['usuario']);
			    return $resp;
		    }
		}else
		{
			if($parametros['check']=='true')
			{
				 $res = $this->modelo->guardar_acceso_empresa($parametros['modulo'],$parametros['entidad'],$parametros['item'],$parametros['usuario']);
				 return $res;
			}else
			{
			   $resp = 	$this->modelo->delete_modulos($parametros['entidad'],$parametros['item'],$parametros['usuario'],$parametros['modulo']);
			   return $resp;

			}

		}
	}

  	function enviar_email($parametros)
  	{
  		$empresaGeneral = array_map(array($this, 'encode1'), $this->modelo->Empresa_data());
	  	$this->modelo->actualizar_correo($parametros['email'],$parametros['CI_usuario']);
	    $datos = $this->modelo->entidades_usuario($parametros['CI_usuario']);

	    // print_r($datos);die();

	  	$email_conexion = 'info@diskcoversystem.com'; //$empresaGeneral[0]['Email_Conexion'];
	    $email_pass =  'info2021DiskCover'; //$empresaGeneral[0]['Email_Contraseña'];
	    // print_r($empresaGeneral[0]);die();
	    //$Nombre_Usuario
	  	$correo_apooyo="credenciales@diskcoversystem.com"; //correo que saldra ala do del emisor
	  	$cuerpo_correo = '
<pre>
Este correo electronico fue generado automaticamente del Sistema Administrativo Financiero Contable DiskCover System a usted porque figura como correo electronico alternativo en nuestra base de datos.

A solicitud de El(a) Sr(a) '.$parametros['usuario'].' se envian sus credenciales de acceso:
</pre> 
<br>
<table>
<tr><td><b>Link:</b></td><td>https://erp.diskcoversystem.com</td></tr>
<tr><td><b>Nombre Usuario:</b></td><td>'.$datos[0]['Nombre_Usuario'].'</td></tr>
<tr><td><b>Usuario:</b></td><td>'.$datos[0]['Usuario'].'</td></tr>
<tr><td><b>Clave:</b></td><td>'.$datos[0]['Clave'].'</td></tr>
<tr><td><b>Email:</b></td><td>'.$datos[0]['Email'].'</td></tr>
</table>
A este usuario se asigno las siguientes Entidades: 
<br>
<table>
<tr><td width="30%"><b>Codigo</b></td><td width="50%"><b>Entidad</b></td></tr>
';
foreach ($datos as $value) {
	$cuerpo_correo .= '<tr><td>'.$value['id'].'</td><td>'.$value['text'].'</td></tr>';
}
$cuerpo_correo.='</table><br>';
$cuerpo_correo .= ' 
<pre>
Nosotros respetamos su privacidad y solamente se utiliza este correo electronico para mantenerlo informado sobre nuestras ofertas, promociones, claves de acceso y comunicados. No compartimos, publicamos o vendemos su informacion personal fuera de nuestra empresa.

Esta direccion de correo electronico no admite respuestas. En caso de requerir atencion personalizada por parte de un asesor de servicio al cliente; Usted podra solicitar ayuda mediante los canales de atencion al cliente oficiales que detallamos a continuacion:

Telefonos: (+593) 098-652-4396/099-965-4196/098-910-5300.
Emails: recepcion@diskcoversystem.com o prisma_net@hotmail.es
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

  	function confirmar_enviar_email($parametros)
  	{
  		$empresaGeneral = array_map(array($this, 'encode1'), $this->modelo->Empresa_data());
	  	$this->modelo->actualizar_correo($parametros['email'],$parametros['CI_usuario']);
	    $datos = $this->modelo->entidades_usuario($parametros['CI_usuario']);

	    // print_r($datos);die();

	  	$email_conexion = 'info@diskcoversystem.com'; //$empresaGeneral[0]['Email_Conexion'];
	    $email_pass =  'info2021DiskCover'; //$empresaGeneral[0]['Email_Contraseña'];
	    // print_r($empresaGeneral[0]);die();
	    //$Nombre_Usuario
	  	$correo_apooyo="credenciales@diskcoversystem.com"; //correo que saldra ala do del emisor
	  	$cuerpo_correo = '
<pre>
Este correo electronico fue generado automaticamente del Sistema Administrativo Financiero Contable DiskCover System a usted porque figura como correo electronico alternativo en nuestra base de datos.

A solicitud de El(a) Sr(a) '.$parametros['usuario'].' se envian sus credenciales de acceso:
</pre> 
<br>
<table>
<tr><td><b>Link:</b></td><td>https://erp.diskcoversystem.com</td></tr>
<tr><td><b>Nombre Usuario:</b></td><td>'.$datos[0]['Nombre_Usuario'].'</td></tr>
<tr><td><b>Usuario:</b></td><td>'.$datos[0]['Usuario'].'</td></tr>
<tr><td><b>Clave:</b></td><td>'.$datos[0]['Clave'].'</td></tr>
<tr><td><b>Email:</b></td><td>'.$datos[0]['Email'].'</td></tr>
</table>
A este usuario se asigno las siguientes Entidades: 
<br>
<table>
<tr><td width="30%"><b>Codigo</b></td><td width="50%"><b>Entidad</b></td></tr>
';
foreach ($datos as $value) {
	$cuerpo_correo .= '<tr><td>'.$value['id'].'</td><td>'.$value['text'].'</td></tr>';
}
$cuerpo_correo.='</table><br>';
$cuerpo_correo .= ' 
<pre>
Nosotros respetamos su privacidad y solamente se utiliza este correo electronico para mantenerlo informado sobre nuestras ofertas, promociones, claves de acceso y comunicados. No compartimos, publicamos o vendemos su informacion personal fuera de nuestra empresa.

Esta direccion de correo electronico no admite respuestas. En caso de requerir atencion personalizada por parte de un asesor de servicio al cliente; Usted podra solicitar ayuda mediante los canales de atencion al cliente oficiales que detallamos a continuacion:

Telefonos: (+593) 098-652-4396/099-965-4196/098-910-5300.
Emails: recepcion@diskcoversystem.com o prisma_net@hotmail.es
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

	  	return $cuerpo_correo;
  	}


  	function enviar_email_masivo($parametros)
  	{
  		$empresaGeneral = array_map(array($this, 'encode1'), $this->modelo->Empresa_data());
	  	$fallo = false;
	    $usuarios = $this->modelo->entidades_usuarios($parametros['ruc']);

	    // print_r($usuarios);die();
	    foreach ($usuarios as $datos) {
  			$datos0 = $this->modelo->entidades_usuario($datos['CI_NIC']);
		  	$email_conexion = 'info@diskcoversystem.com';
		    $email_pass =  'info2021DiskCover';
		  	$correo_apooyo="credenciales@diskcoversystem.com";
		  	$cuerpo_correo = '
<pre>
Este correo electronico fue generado automaticamente del Sistema Administrativo Financiero Contable DiskCover System a usted porque figura como correo electronico alternativo en nuestra base de datos.

A solicitud de El(a) Sr(a) '.$datos['Nombre_Usuario'].' se envian sus credenciales de acceso:
</pre> 
<br>
<table>
<tr><td><b>Link:</b></td><td>https://erp.diskcoversystem.com</td></tr>
<tr><td><b>Nombre Usuario:</b></td><td>'.$datos['Nombre_Usuario'].'</td></tr>
<tr><td><b>Usuario:</b></td><td>'.$datos['Usuario'].'</td></tr>
<tr><td><b>Clave:</b></td><td>'.$datos['Clave'].'</td></tr>
<tr><td><b>Email:</b></td><td>'.$datos['Email'].'</td></tr>
</table>
A este usuario se asigno las siguientes Entidades: 
<br>
<table>
<tr><td width="30%"><b>Codigo</b></td><td width="50%"><b>Entidad</b></td></tr>
';
foreach ($datos0 as $value) {
	$cuerpo_correo .= '<tr><td>'.$value['id'].'</td><td>'.utf8_decode($value['text']).'</td></tr>';
}
$cuerpo_correo.='</table><br>';
$cuerpo_correo .= ' 
<pre>
Nosotros respetamos su privacidad y solamente se utiliza este correo electronico para mantenerlo informado sobre nuestras ofertas, promociones, claves de acceso y comunicados. No compartimos, publicamos o vendemos su informacion personal fuera de nuestra empresa.

Esta direccion de correo electronico no admite respuestas. En caso de requerir atencion personalizada por parte de un asesor de servicio al cliente; Usted podra solicitar ayuda mediante los canales de atencion al cliente oficiales que detallamos a continuacion:

Telefonos: (+593) 098-652-4396/099-965-4196/098-910-5300.
Emails: recepcion@diskcoversystem.com o prisma_net@hotmail.es
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
		  	$correo = $datos['Email'];
		  	$resp=1;

		  	if($correo!='.'){
		  	$resp = $this->email->enviar_credenciales($archivos,$correo,$cuerpo_correo,$titulo_correo,$correo_apooyo,'Credenciales de acceso al sistema DiskCover System',$email_conexion,$email_pass,$html=1,$empresaGeneral);
		     }
		    if($resp!=1)
		    {
		    	$fallo = true;
		    }
	    }

	    if($fallo==true)
	    {
	    	return -2;
	    }else
	    {
	    	return 1;
	    }
		//echo json_encode(1);
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

}
?>