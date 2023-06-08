<?php 

require_once(dirname(__DIR__,2)."/db/db1.php");
require_once(dirname(__DIR__,2)."/funciones/funciones.php");

/**
 * 
 */
class cambioeM
{
	private $db;
	
	function __construct()
	{
		$this->db = new db();
	}

	function ciudad($IDempresa)
	{
		$sql ="SELECT Ciudad
			  FROM lista_empresas
			  WHERE ID_Empresa = '".$IDempresa."' group by Ciudad";
	    return $this->db->datos($sql,'MYSQL');

	}

	function entidad($query=false,$IDempresa="",$ciudad=false)
	{

		if($ciudad)
		{
			$sql = "SELECT *
				  FROM lista_empresas
				  WHERE ID_Empresa = '".$IDempresa."' AND Ciudad='".$ciudad."' ";

		}else{
			
			$sql = "SELECT *
				  FROM lista_empresas
				  WHERE ID_Empresa = '".$IDempresa."' ";
		}
		if($query)
		{
			$sql.=" and Empresa like '".$query."%' ";
		}

		$sql.='ORDER BY Empresa;';		
		return $this->db->datos($sql,'MYSQL');
	}

	function datos_empresa($ID)
	{		
		$sql = "SELECT *
		  FROM lista_empresas
		  WHERE ID = '".$ID."';";
		return $this->db->datos($sql,'MYSQL');
	}
	function datos_empresa_sqlserver()
	{		
		$sql = "SELECT *
		  FROM lista_empresas
		  WHERE ID = '".$ID."';";
		return $this->db->datos($sql);
	}
	function estado()
	{
		$sql = 'SELECT Estado,Descripcion FROM lista_estados';
		return $this->db->datos($sql,'MYSQL');
	}

	function editar_datos_empresa($parametros)
	{

		$sql = "UPDATE lista_empresas set 
		Estado='".$parametros['Estado']."',
		Mensaje='".$parametros['Mensaje']."',
		Fecha_CE='".$parametros['Fecha']."' ,
		IP_VPN_RUTA='".$parametros['Servidor']."',
		Base_Datos='".$parametros['Base']."' ,
		Usuario_DB='".$parametros['Usuario']."',
		contrasena_DB='".$parametros['Clave']."' ,
		Tipo_Base='".$parametros['Motor']."',
	    Puerto='".$parametros['Puerto']."',
	    Fecha='".$parametros['FechaR']."',
	    Fecha_VPN='".$parametros['FechaV']."',
	    Fecha_DB='".$parametros['FechaDB']."',
	    Fecha_P12='".$parametros['FechaP12']."', 
	    Tipo_Plan='".$parametros['Plan']."' 
	    WHERE ID='".$parametros['empresas']."' ";

	    // print_r($parametros);die();

	    $em = $this->datos_empresa($parametros['empresas']);
	    if(count($em)>0)
	    {
	    	if($em[0]['IP_VPN_RUTA']!='.' && $em[0]['IP_VPN_RUTA']!='')
	    	{
	            $conn = $this->db->modulos_sql_server($em[0]['IP_VPN_RUTA'],$em[0]['Usuario_DB'],$em[0]['Contrasena_DB'],$em[0]['Base_Datos'],$em[0]['Puerto']);
	            // print_r($conn);die();
	            if($conn!=-1)
	            {
	            	$fe =  date("Y-m-d",strtotime($parametros['Fecha']."- 1 year"));
	            	$sql2 = "UPDATE Catalogo_Lineas 
		    		SET Vencimiento = '".$parametros['Fecha']."',Fecha = '".$fe."' 
		    		WHERE Item = '".$em[0]['Item']."' AND Periodo = '.'  AND TL <> 0 AND len(Autorizacion)>=13";
		    		$ambiente = 1;
		    		if($parametros['optionsRadios']=='option2')
		    		{
		    			$ambiente = 2;
		    		}

		    		$sql3 = "UPDATE Empresas SET Fecha_CE = '".$parametros['Fecha']."',
		    		Estado = '".$parametros['Estado']."',
		    		Codigo_Contribuyente_Especial = '".$parametros['TxtContriEspecial']."',
		    		Web_SRI_Recepcion = '".$parametros['TxtWebSRIre']."',
		    		Web_SRI_Autorizado = '".$parametros['TxtWebSRIau']."',
		    		Ruta_Certificado = '".$parametros['TxtEXTP12']."',
		    		Clave_Certificado = '".$parametros['TxtContraExtP12']."',
		    		Email_Conexion = '".$parametros['TxtEmailGE']."',
		    		Email_Contraseña = '".$parametros['TxtContraEmailGE']."',
		    		Email_Conexion_CE = '".$parametros['TxtEmaiElect']."',
		    		Email_Contraseña_CE = '".$parametros['TxtContraEmaiElect']."',
		    		Email_Procesos = '".$parametros['TxtCopiaEmai']."',
		    		RUC_Operadora = '".$parametros['TxtRUCOpe']."',
		    		LeyendaFA = '".$parametros['txtLeyendaDocumen']."',
		    		LeyendaFAT = '".$parametros['txtLeyendaImpresora']."',
		    		Ambiente = '".$ambiente."'
		    		WHERE Item='".$em[0]['Item']."'";

		    		// print_r($sql3);
		    		// print_r($sql2);

	            	$r = $this->db->ejecutar_sql_terceros($sql2,$em[0]['IP_VPN_RUTA'],$em[0]['Usuario_DB'],$em[0]['Contrasena_DB'],$em[0]['Base_Datos'],$em[0]['Puerto']);
	            	$r = $this->db->ejecutar_sql_terceros($sql3,$em[0]['IP_VPN_RUTA'],$em[0]['Usuario_DB'],$em[0]['Contrasena_DB'],$em[0]['Base_Datos'],$em[0]['Puerto']);

	            	// print_r($r);die();
	            }
	        }


	    }
	    $resp = $this->db->String_Sql($sql,'MYSQL');
	    return array('res'=>$resp,'empresa'=>$parametros['empresas']);
	}
	function mensaje_masivo($parametros)
	{		
		// print_r($parametros);die();
		$sql = "UPDATE lista_empresas set Mensaje='".$parametros['Mensaje']."'; ";
		return $this->db->String_Sql($sql,'MYSQL');
	}
	function mensaje_grupo($parametros)
	{		
		
		if($parametros['ciudad']=='')
		{
			 $sql = "UPDATE lista_empresas set Mensaje='".$parametros['Mensaje']."' WHERE ID_Empresa='".$parametros['entidad']."' ";
		}
		else
		{
			 $sql = "UPDATE lista_empresas set Mensaje='".$parametros['Mensaje']."' WHERE ID_Empresa='".$parametros['entidad']."'  AND Ciudad='".$parametros['ciudad']."'";
		}
		return $this->db->String_Sql($sql,'MYSQL');
	}
	function mensaje_indi($parametros)
	{
		$sql = "UPDATE lista_empresas set Mensaje='".$parametros['Mensaje']."' WHERE ID_Empresa='".$parametros['entidad']."' AND ID='".$parametros['empresas']."'";
		return $this->db->String_Sql($sql,'MYSQL');
	}
	function guardar_masivo($parametros)
	{
		$sql = "UPDATE lista_empresas set Fecha='".$parametros['FechaR']."' , Fecha_VPN='".$parametros['FechaV']."' , Fecha_CE='".$parametros['Fecha']."'  
		WHERE ID_Empresa='".$parametros['entidad']."'";

		$em = $this->entidad($query=false,$parametros['entidad'],$ciudad=false);
		// print_r($em);die();
		if(count($em)>0)
		{
			foreach ($em as $key => $value) {
				if($value['IP_VPN_RUTA']!='.' && $value['IP_VPN_RUTA']!='')
				{
					$conn = $this->db->modulos_sql_server($value['IP_VPN_RUTA'],$value['Usuario_DB'],$value['Contrasena_DB'],$value['Base_Datos'],$value['Puerto']);
		            // print_r($conn);die();
		            if($conn!=-1)
		            {
		            	$fe =  date("Y-m-d",strtotime($parametros['Fecha']."- 1 year"));
		            	$sql2 = "UPDATE Catalogo_Lineas 
			    		SET Vencimiento = '".$parametros['Fecha']."',Fecha = '".$fe."' 
			    		WHERE Item = '".$value['Item']."' AND Periodo = '.'  AND TL <> 0 AND len(Autorizacion)>=13";

			    		// print_r($sql2);die();
			    		$sql3 = "UPDATE Empresas SET Fecha_CE = '".$parametros['Fecha']."' WHERE Item='".$value['Item']."'";

		    		// print_r($sql3);

	            	    $r = $this->db->ejecutar_sql_terceros($sql2,$value['IP_VPN_RUTA'],$value['Usuario_DB'],$value['Contrasena_DB'],$value['Base_Datos'],$value['Puerto']);

		            	$r = $this->db->ejecutar_sql_terceros($sql3,$value['IP_VPN_RUTA'],$value['Usuario_DB'],$value['Contrasena_DB'],$value['Base_Datos'],$value['Puerto']);

		            	// print_r($r);die();
		            }
	        	}
			}
		}

		// print_r($em);die();


		return $this->db->String_Sql($sql,'MYSQL');
	}

	function asignar_clave($parametros)
	{
		$sql="Update Clientes set Clave = SUBSTRING(CI_RUC,1,10)where Codigo <> '.' and LEN(Clave)<=1";		
		// print_r($parametros);die();
		return $this->db->ejecutar_sql_terceros($sql,$parametros['Servidor'],$parametros['Usuario'],$parametros['Clave'],$parametros['Base'],$parametros['Puerto']);
	}

	function todos_modulos()
	{
		$sql = "SELECT modulo,aplicacion FROM modulos WHERE modulo <> '.' and modulo <> 'VS' ORDER BY aplicacion ASC";
		return $this->db->datos($sql,'MYSQL');
	}

	function paginas($modulo)
	{
		$sql = "SELECT ID,CodMenu,descripcionMenu FROM menu_modulos WHERE codMenu like '".$modulo.".%' AND LENGTH(codMenu)>4 ORDER BY descripcionMenu ASC";
		return $this->db->datos($sql,'MYSQL');
	}

	function datos_sql_terceros($parametros,$host,$user,$pass,$base,$Puerto)
	{
		// print_r($parametros);die();
		$sql = "SELECT * FROM Empresas WHERE Item = '".$parametros['Item']."' AND RUC = '".$parametros['RUC_CI_NIC']."'";
		// print_r($sql);die();
		return  $this->db->consulta_datos_db_sql_terceros($sql,$host,$user,$pass,$base,$Puerto);
	}



}

?>