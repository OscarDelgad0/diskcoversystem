<?php
/**
 * Autor: JAVIER FARINANGO.
 * Mail:  
 * web:   www.diskcoversystem.com
 */

@session_start();
// print_r($_SESSION['INGRESO']['modulo_']);

$_SESSION['INGRESO']['modulo_']='01';
// chequea que esten con sesion
require_once("../db/chequear_seguridad.php");
//llamo la cabecera
require_once("../headers/header.php");

// chequea si hay una base de datos asignada
if(isset($_SESSION['INGRESO']['IP_VPN_RUTA']) && $_SESSION['INGRESO']['Tipo_Base'] =='SQL SERVER') 
{
	$permiso=getAccesoEmpresas();
}else
{
	echo "<script>
			Swal.fire({
			  type: 'error',
			   title: 'Comuniquese con el Administrador del Sistema, Para Activar el acceso a su base de dato de la nube',
			  text: 'Asegurese de tener credeciales de SQLSERVER',
			  allowOutsideClick:false,
			}).then((result) => {
			  if (result.value) {
				location.href='modulos.php';
			  } 
			});
		</script>";
}
?>

  <div class="content-wrapper">
    <!-- <section class="content-header">
      <h1>
        <small>Panel de control</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section> -->

<?php 
    //llamamos a los parciales
	if (isset($_SESSION['INGRESO']['accion'])) 
	{
		echo '<section class="content-header">
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>'.$_GET['mod'].'</a></li>
        <li class="active">'.$_SESSION['INGRESO']['accion1'].'</li>
      </ol>
    </section>';

    echo '<section class="content">';

		//cambio de clave
		if ($_SESSION['INGRESO']['accion']=='cambioc') 
		{
			require_once("contabilidad/cambioc.php");
		}
		//ingreso catalogo de cuenta
		if ($_SESSION['INGRESO']['accion']=='incc') 
		{
			require_once("contabilidad/inccu.php");
		}
		//Mayorización
		if ($_SESSION['INGRESO']['accion']=='macom') 
		{
			require_once("contabilidad/macom.php");
		}
		//Balance de Comprobacion/Situación/General
		if ($_SESSION['INGRESO']['accion']=='bacsg') 
		{
			require_once("contabilidad/bacsg1.php");
		}
		//herramientas conexion oracle
		if ($_SESSION['INGRESO']['accion']=='hco') 
		{
			require_once("contabilidad/hco.php");
		}
		//comprobantes procesados
		if ($_SESSION['INGRESO']['accion']=='compro') 
		{
			require_once("contabilidad/compro.php");
		}
		//cambio de periodo
		if ($_SESSION['INGRESO']['accion']=='campe') 
		{
			require_once("contabilidad/campe.php");
		}
		//Ingresar Comprobantes (Crtl+f5)
		if ($_SESSION['INGRESO']['accion']=='incom') 
		{
			require_once("contabilidad/incom.php");
		}
		//saldo de factura submodulo
		if ($_SESSION['INGRESO']['accion']=='saldo_fac_submodulo') 
		{
			require_once("contabilidad/saldo_fac_submodulo.php");
		}
		if ($_SESSION['INGRESO']['accion']=='catalogo_cuentas') 
		{
			include("contabilidad/catalogoCta.php");
		}
		if ($_SESSION['INGRESO']['accion']=='diario_general') 
		{

			include("contabilidad/diario_general.php");
		}
		if ($_SESSION['INGRESO']['accion']=='mayor_auxiliar') 
		{			
			require_once("contabilidad/mayor_auxiliar.php");
		}
		if ($_SESSION['INGRESO']['accion']=='libro_banco') 
		{
			require_once("contabilidad/libro_banco.php");
		}
		if ($_SESSION['INGRESO']['accion']=='ctaOperaciones') 
		{
			require_once("contabilidad/ctaOperaciones.php");
		}
		if ($_SESSION['INGRESO']['accion']=='anexos_trans') 
		{
			require_once("contabilidad/anexos_trans.php");
		}
		if ($_SESSION['INGRESO']['accion']=='bamup') 
		{
			require_once("contabilidad/bamup.php");
		}
		if ($_SESSION['INGRESO']['accion']=='reportes') 
		{
			require_once("contabilidad/resumen_retenciones.php");
		}
		if ($_SESSION['INGRESO']['accion']=='Clientes') 
		{
			include("contabilidad/FCliente.php");
		}
		if ($_SESSION['INGRESO']['accion']=='subcta_proyectos') 
		{
			require_once("contabilidad/Subcta_proyectos.php");
		}
		if ($_SESSION['INGRESO']['accion']=='cierre_mes') 
		{
			require_once("contabilidad/cierre_mes.php");
		}
		if ($_SESSION['INGRESO']['accion']=='MayoresSubCta') 
		{
			require_once("contabilidad/mayores_sub_cuenta.php");
		}
	}else
	{
		echo "<div class='box-body'><img src='../../img/modulo_contable.gif' width='100%'></div>";
	}

?>

    </section>
  </div>
<?php				
  require_once("../headers/footer.php");
?>	
		
	
