<?php
require_once(dirname(__DIR__,2)."/db/db1.php");
include(dirname(__DIR__,2).'/funciones/funciones.php');
class crear_empresaM
{
    private $db;
    function __construct()
    {
        $this->db = new db();
    }

    function lista_empresas($query=false, $item=false)
    {
        $sql = "SELECT *
        FROM Empresas WHERE Item <> '000'";
        if($query)
        {
            $sql.=" AND Empresa like '%".$query."%'";
        }
        if($item)
        {
            $sql.=" AND Item ='".$item."'";
        }
        return  $this->db->datos($sql);
    }
    function delete_empresa($id)
	{
        $sql= "DELETE FROM Empresas WHERE Item = '".$id."'";
		return $this->db->String_Sql($sql);
	}
    function usuario($CI)
    {
        $sql = "SELECT Codigo, Usuario, Clave
        FROM Accesos        
        WHERE Codigo = '".$CI."'";
        return  $this->db->datos($sql);
    }
    
}
?>