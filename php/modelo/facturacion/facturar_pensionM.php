<?php

require_once(dirname(__DIR__,2)."/db/db1.php");
require_once(dirname(__DIR__,2)."/funciones/funciones.php");

class facturar_pensionM
{
	private $db;
	public function __construct(){
    //base de datos
    $this->db = new db();
  }
	
	public function getClientes($query,$ruc=false){
    $sql="  SELECT C.Email,C.T,C.Codigo,C.Cliente,C.Direccion,C.Grupo,C.Telefono,C.CI_RUC,C.TD,SUM(CF.Valor) As Deuda_Total,DireccionT , C.Archivo_Foto, C.CI_RUC_R
            FROM Clientes As C, Clientes_Facturacion As CF 
            WHERE C.T = 'N'
            AND CF.Item = '".$_SESSION['INGRESO']['item']."' 
            AND CF.Num_Mes >= 0
            AND C.Codigo <> '9999999999' 
            AND C.FA <> 0
            AND CF.Codigo = C.Codigo";
    if($ruc)
    {
      $sql.=" AND C.Codigo = '".$ruc."'";
    }
    if($query != 'total' and $query!='' and !is_numeric($query) )
    {
      $sql.=" AND Cliente LIKE '%".$query."%'";
    }else
    {
       $sql.=" AND C.CI_RUC LIKE '".$query."%'";
    }
    $sql.=" GROUP BY C.Email, C.T,C.Codigo,C.Cliente,C.Direccion,C.Grupo,C.Telefono,C.CI_RUC,C.TD,DireccionT, C.Archivo_Foto, C.CI_RUC_R ORDER BY C.Cliente";
    if ($query != 'total') {
      $sql .= " OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY";
    }
    $stmt = $this->db->datos($sql);
    return $stmt;
  }

  public function getClientesMatriculas($codigo=false)
  {
    $sSQL = "SELECT ".Full_Fields("Clientes_Matriculas")." 
              FROM Clientes_Matriculas 
              WHERE Periodo = '".$_SESSION['INGRESO']['periodo']."' 
              AND Item = '".$_SESSION['INGRESO']['item']."'";
    if($codigo)
    {
      $sSQL.=" AND Codigo='".$codigo."'";
    }
    $stmt = $this->db->datos($sSQL);
    return $stmt;
  }


  public function getCatalogoLineas($fecha,$vencimiento,$serie=false){
    $sql="  SELECT * FROM Catalogo_Lineas 
            WHERE Item = '".$_SESSION['INGRESO']['item']."' 
            AND Fact IN ('".G_COMPFACTURA."','".G_COMPNOTAVENTA."')
			      AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
			      AND CONVERT(DATE,Fecha) <= '".$fecha."'
			      AND CONVERT(DATE,Vencimiento) >= '".$vencimiento."' ";
            if($serie)
            {
              $sql.=" AND Serie='".$serie."'";
            }
			      $sql.=" ORDER BY Codigo";

            // print_r($sql);die();
            $stmt = $this->db->datos($sql);
            return $stmt;
  }

    public function getCatalogoLineas13($fecha,$vencimiento){
    $sql="  SELECT * FROM Catalogo_Lineas 
            WHERE Item = '".$_SESSION['INGRESO']['item']."' 
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."' 
            AND Fact IN ('".G_COMPFACTURA."','".G_COMPNOTAVENTA."')
            AND CONVERT(DATE,Fecha) <= '".$fecha."'
            AND CONVERT(DATE,Vencimiento) >= '".$vencimiento."'
            AND len(Autorizacion)>=13
            ORDER BY Codigo";
            $stmt = $this->db->datos($sql);
            return $stmt;
    }

  public function getSerieUsuario($codigoU){
      $sql="SELECT * FROM Accesos WHERE Codigo = '".$codigoU."'";
      // print_r($sql);die();
      $stmt = $this->db->datos($sql);
      return $stmt;
    }

  public function getCatalogoCuentas(){
    $sql="SELECT Codigo, Cuenta As NomCuenta, TC 
       		FROM Catalogo_Cuentas 
       		WHERE TC IN ('C','P','BA','CJ','TJ') 
       		AND DG = 'D' 
       		AND Item = '".$_SESSION['INGRESO']['item']."'
       		AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
       		ORDER BY TC,Codigo";
    $stmt = $this->db->datos($sql);
    return $stmt;
  }

  public function getNotasCredito(){
    $sql = "SELECT Codigo, Cuenta As NomCuenta, TC 
			FROM Catalogo_Cuentas 
			WHERE SUBSTRING (Codigo,1,1) = '4' 
			AND DG = 'D'
			AND Item = '".$_SESSION['INGRESO']['item']."'
       		AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
			ORDER BY TC,Codigo";
    $stmt = $this->db->datos($sql);
    return $stmt;
  }

  public function getAnticipos($codigo){
    $sql = "SELECT Codigo, Cuenta As NomCuenta, TC 
            FROM Catalogo_Cuentas 
            WHERE Codigo = '".$codigo."'
            AND DG = 'D'
            AND Item = '".$_SESSION['INGRESO']['item']."'
            AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
            ORDER BY TC,Codigo";
    $stmt = $this->db->datos($sql);
    return $stmt;
  }

  public function getCatalogoProductos($codigoCliente){
    $sql = "SELECT CF.Mes,CF.Num_Mes,CF.Valor,CF.Descuento,CF.Descuento2,CF.Codigo,CF.Periodo As Periodos,CF.Mensaje,CF.Credito_No,CP.*
			FROM Clientes_Facturacion As CF,Catalogo_Productos As CP
			WHERE CF.Codigo = '".$codigoCliente."'
			AND CP.Item = '".$_SESSION['INGRESO']['item']."'
			AND CP.Periodo = '".$_SESSION['INGRESO']['periodo']."'
			AND CF.Mes <> '.'
			AND CF.Item = CP.Item 
			AND CF.Codigo_Inv = CP.Codigo_Inv 
			ORDER BY CF.Periodo,CF.Num_Mes,CP.Codigo_Inv,CF.Credito_No";
    $stmt = $this->db->datos($sql);
    return $stmt;
  }

  public function getSaldoFavor($codigoCliente){
    $SubCtaGen = Leer_Seteos_Ctas("Cta_Anticipos_Clientes");
  	$sql = "SELECT Codigo, SUM(Creditos-Debitos) As Saldo_Pendiente
       		  FROM Trans_SubCtas
       		  WHERE Item = '".$_SESSION['INGRESO']['item']."'
       		  AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
       		  AND Codigo = '".$codigoCliente."'
       		  AND Cta = '".$SubCtaGen."'
       		  AND T = 'N'
       		  GROUP BY Codigo ";
    $stmt = $this->db->datos($sql);
    return $stmt;
  }

  public function getSaldoPendiente($codigoCliente){
		$sql = "SELECT CodigoC,SUM(Saldo_MN) As Saldo_Pend 
              FROM Facturas 
              WHERE Item = '".$_SESSION['INGRESO']['item']."'
              AND Periodo = '".$_SESSION['INGRESO']['periodo']."'
              AND CodigoC = '".$codigoCliente."'
              AND Saldo_MN > 0
              AND T <> 'A'
              GROUP BY CodigoC";
    $stmt = $this->db->datos($sql);
    return $stmt;
  }

  public function updateClientesFacturacion($TxtGrupo,$codigoCliente){
  	$sql = "UPDATE Clientes_Facturacion
                	SET GrupoNo = '".$TxtGrupo."'
                	WHERE Periodo = '".$_SESSION['INGRESO']['periodo']."'
                	AND Item = '".$_SESSION['INGRESO']['item']."'
                	AND Codigo = '".$codigoCliente."' ";
    $stmt = $this->db->String_Sql($sql);
    return $stmt;
  }

  public function updateClientesFacturacion1($Valor,$Anio1,$Codigo1,$Codigo,$Codigo3,$Codigo2){
    $sql = "UPDATE Clientes_Facturacion 
              SET Valor = Valor - ".$Valor." 
              WHERE Item = '".$_SESSION['INGRESO']['item']."' 
              AND Periodo = '".$Anio1."' 
              AND Codigo_Inv = '".$Codigo1."' 
              AND Codigo = '".$Codigo."' 
              AND Credito_No = '".$Codigo3."' 
              AND Mes = '".$Codigo2."' ";
    $stmt = $this->db->String_Sql($sql);
    return $stmt;
  }

  public function updateClientesMatriculas($TextRepresentante,$TextCI,$TD_Rep,$TxtTelefono,$TxtDireccion,$TxtEmail,$TxtGrupo,$codigoCliente,$CTipoCta,$TxtCtaNo,$CheqPorDeposito,$Caducidad,$DCDebito){
    $sql = "UPDATE Clientes_Matriculas
                	SET T = '".G_NORMAL."',
                  Representante = '".$TextRepresentante."', 
                	Cedula_R = '".$TextCI."', 
                	TD = '".$TD_Rep."', 
                	Telefono_R = '".$TxtTelefono."', 
                	Lugar_Trabajo_R = '".$TxtDireccion."', 
                	Email_R = '".$TxtEmail."', 
                	Grupo_No = '".$TxtGrupo."',
                  Cta_Numero ='".$TxtCtaNo."' ,
                  Tipo_Cta ='".$CTipoCta."' ,
                  Caducidad ='".$Caducidad."' ,
                  Por_Deposito ='".$CheqPorDeposito."' ,
                  Cod_Banco ='".$DCDebito."' 
                	WHERE Periodo = '".$_SESSION['INGRESO']['periodo']."'
                	AND Item = '".$_SESSION['INGRESO']['item']."'
                	AND Codigo = '".$codigoCliente."' ";
    $stmt = $this->db->String_Sql($sql);
    return $stmt;
  }

  public function updateClientes($TxtTelefono,$TxtDirS,$TxtDireccion,$TxtEmail,$TxtGrupo,$codigoCliente){
    $sql = "UPDATE Clientes
                	SET Telefono = '".$TxtTelefono."', 
                	Telefono_R = '".$TxtTelefono."', 
                	DireccionT = '".$TxtDirS."', 
                	Direccion = '".$TxtDireccion."', 
                	Email = '".$TxtEmail."', 
                	Grupo = '".$TxtGrupo."' 
                	WHERE Codigo = '".$codigoCliente."'";
    $stmt = $this->db->String_Sql($sql);
    return $stmt;
  }

  public function deleteAsiento($codigoCliente){//TODO LS SE DEBERIA ESTAR BORRANDO TODO NO LO DE Codigo_Cliente
    $sql = "DELETE
            FROM Asiento_F
            WHERE Item = '".$_SESSION['INGRESO']['item']."' 
            AND Codigo_Cliente = '".$codigoCliente."' 
            AND CodigoU = '". $_SESSION['INGRESO']['CodigoU'] ."' ";
    $stmt = $this->db->String_Sql($sql);
    return $stmt;
  }

   public function deleteAsientoEd($A_No){
    $sql = "DELETE
            FROM Asiento_F
            WHERE Item = '".$_SESSION['INGRESO']['item']."' 
            AND CodigoU = '". $_SESSION['INGRESO']['CodigoU'] ."' 
            AND A_No = ".$A_No;
    $stmt = $this->db->String_Sql($sql);
    return $stmt;
  }

  public function getAsiento(){
    $sql = "SELECT ".Full_Fields("Asiento_F")." 
       			FROM Asiento_F
       			WHERE Item = '".$_SESSION['INGRESO']['item']."' 
       			AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
       			ORDER BY A_No ";
    $stmt = $this->db->datos($sql);
    return $stmt;
  }

  public function historiaCliente($codigoCliente){
   $SQL1 = "SELECT TC As TD, Fecha, Serie, Factura,'Emision ' + Producto As Detalle, YEAR(Fecha) As Anio, Mes, Total, 0 As Abonos, Mes_No, 
        (ROW_NUMBER() OVER(PARTITION BY Serie, Factura ORDER BY Fecha, Serie, Factura)) As No 
        FROM Detalle_Factura 
        WHERE Item = '".$_SESSION['INGRESO']['item']."' 
        AND CodigoC = '".$codigoCliente."' 
        AND T <> 'A' 
        AND TC IN ('NV','FA') 
        GROUP BY TC, Fecha, Serie, Factura, Producto, Mes_No, Ticket, Mes, Total ";
        
    $SQL2 = "SELECT TC As TD, Fecha, Serie, Factura,'Descuento ' + Producto As Detalle, Ticket As Anio, Mes, 0 As Total, SUM(Total_Desc + Total_Desc2) As Abonos, Mes_No, 
         (ROW_NUMBER() OVER(PARTITION BY Serie, Factura ORDER BY Fecha, Serie, Factura)) As No 
         FROM Detalle_Factura 
         WHERE Item = '".$_SESSION['INGRESO']['item']."' 
         AND CodigoC = '".$codigoCliente."' 
         AND T <> 'A' 
         AND TC IN ('NV','FA') 
         AND (Total_Desc + Total_Desc2) > 0 
         GROUP BY TC, Fecha, Serie, Factura, Producto, Mes_No, Ticket, Mes ";

   $SQL3 = "SELECT TP As TD, Fecha, Serie, Factura, 'Tipo de Abono: ' + Banco As Detalle, YEAR(Fecha) AS Anio, Mes, 0 As Total, Abono As Abonos, Mes_No, 
        (ROW_NUMBER() OVER(PARTITION BY Serie, Factura ORDER BY Serie, Factura, Fecha)) As No 
        FROM Trans_Abonos 
        WHERE Item = '".$_SESSION['INGRESO']['item']."' 
        AND T <> 'A' 
        AND CodigoC = '".$codigoCliente."' 
        GROUP BY TP, Serie, Factura, Fecha,  Mes_No, Mes, Abono, Banco, Cheque ";

   $SQL4 = "SELECT 'PF' As TD, CF.Fecha,'999999' As Serie,'999999999' As Factura, 'Por Facturar ' + CP.Producto As Detalle, CF.Periodo As Anio, CF.Mes,CF.Valor As Total, (CF.Descuento + CF.Descuento2) As Abonos, CF.Num_Mes, (ROW_NUMBER() OVER(PARTITION BY CF.Fecha, CF.Mes ORDER BY CF.Fecha, CF.Mes)) As No 
        FROM Clientes_Facturacion As CF, Catalogo_Productos As CP 
        WHERE CP.Item = '".$_SESSION['INGRESO']['item']."' 
        AND CF.Codigo = '".$codigoCliente."' 
        AND CP.Periodo = '.' 
        AND CP.Item = CF.Item 
        AND CP.Codigo_Inv = CF.Codigo_Inv 
        ORDER BY TD, Fecha, Serie, Factura, Total desc, No ";        
       
        $sql = $SQL1." UNION ".$SQL2." UNION ".$SQL3." UNION ".$SQL4;
    $stmt = $this->db->datos($sql);
    return $stmt;
  }

  public function actualizar_Clientes_Facturacion($Valor,$Anio1,$Codigo,$Codigo1,$Codigo2,$Codigo3)
  {
    $sql="UPDATE Clientes_Facturacion
          SET Valor = Valor - ".$Valor." 
          WHERE Item = '".$_SESSION['INGRESO']['item']."' 
          AND Periodo = '".$Anio1."' 
          AND Codigo_Inv = '".$Codigo1."' 
          AND Codigo = '".$Codigo."' 
          AND Credito_No = '".$Codigo3."' 
          AND Mes = '".$Codigo2."' ";
    $stmt = $this->db->String_Sql($sql);
    return $stmt;

  }

   public function actualizar_Clientes_Facturacion2($Total_Abonos,$Total_Desc,$Anio1,$Codigo,$Codigo1,$Codigo2,$Codigo3)
  {

          // print_r($Total_Abonos);
          // print_r($Total_Desc); die();
    $sql="UPDATE Clientes_Facturacion
          SET Valor = ".((-1*$Total_Abonos) + $Total_Desc)."
          WHERE Item = '".$_SESSION['INGRESO']['item']."' 
          AND Periodo = '".$Anio1."' 
          AND Codigo_Inv = '".$Codigo1."' 
          AND Codigo = '".$Codigo."' 
          AND Credito_No = '".$Codigo3."' 
          AND Mes = '".$Codigo2."' ";
          // print_r($sql);die();
    $stmt = $this->db->String_Sql($sql);
    return $stmt;

  }

  public function actualizar_asiento_F($Valor,$ID_Reg)
  {
    $sql = "UPDATE Asiento_F
           SET TOTAL = ".$Valor.", PRECIO = ".$Valor.", Total_Desc = 0, Total_Desc2 = 0
           WHERE Item = '".$_SESSION['INGRESO']['item']."'
           AND CodigoU = '".$_SESSION['INGRESO']['CodigoU']."'
           AND A_No = ".$ID_Reg." ";
    $stmt = $this->db->String_Sql($sql);
    return $stmt;
  }

   function historial_cliente($codigoCliente)
  {

    $SQL1 = "SELECT TC As TD, Fecha, Serie, Factura,'Emision ' + Producto As Detalle, YEAR(Fecha) As Anio, Mes, Total, 0 As Abonos, Mes_No, 
        (ROW_NUMBER() OVER(PARTITION BY Serie, Factura ORDER BY Fecha, Serie, Factura)) As No 
        FROM Detalle_Factura 
        WHERE Item = '".$_SESSION['INGRESO']['item']."' 
        AND CodigoC = '".$codigoCliente."' 
        AND T <> 'A' 
        AND TC IN ('NV','FA') 
        GROUP BY TC, Fecha, Serie, Factura, Producto, Mes_No, Ticket, Mes, Total ";
        
    $SQL2 = "SELECT TC As TD, Fecha, Serie, Factura,'Descuento ' + Producto As Detalle, Ticket As Anio, Mes, 0 As Total, SUM(Total_Desc + Total_Desc2) As Abonos, Mes_No, 
         (ROW_NUMBER() OVER(PARTITION BY Serie, Factura ORDER BY Fecha, Serie, Factura)) As No 
         FROM Detalle_Factura 
         WHERE Item = '".$_SESSION['INGRESO']['item']."' 
         AND CodigoC = '".$codigoCliente."' 
         AND T <> 'A' 
         AND TC IN ('NV','FA') 
         AND (Total_Desc + Total_Desc2) > 0 
         GROUP BY TC, Fecha, Serie, Factura, Producto, Mes_No, Ticket, Mes ";

   $SQL3 = "SELECT TP As TD, Fecha, Serie, Factura, 'Tipo de Abono: ' + Banco As Detalle, YEAR(Fecha) AS Anio, Mes, 0 As Total, Abono As Abonos, Mes_No, 
        (ROW_NUMBER() OVER(PARTITION BY Serie, Factura ORDER BY Serie, Factura, Fecha)) As No 
        FROM Trans_Abonos 
        WHERE Item = '".$_SESSION['INGRESO']['item']."' 
        AND T <> 'A' 
        AND CodigoC = '".$codigoCliente."' 
        GROUP BY TP, Serie, Factura, Fecha,  Mes_No, Mes, Abono, Banco, Cheque ";

   $SQL4 = "SELECT 'PF' As TD, CF.Fecha,'999999' As Serie,'999999999' As Factura, 'Por Facturar ' + CP.Producto As Detalle, CF.Periodo As Anio, CF.Mes,CF.Valor As Total, (CF.Descuento + CF.Descuento2) As Abonos, CF.Num_Mes, (ROW_NUMBER() OVER(PARTITION BY CF.Fecha, CF.Mes ORDER BY CF.Fecha, CF.Mes)) As No 
        FROM Clientes_Facturacion As CF, Catalogo_Productos As CP 
        WHERE CP.Item = '".$_SESSION['INGRESO']['item']."' 
        AND CF.Codigo = '".$codigoCliente."' 
        AND CP.Periodo = '.' 
        AND CP.Item = CF.Item 
        AND CP.Codigo_Inv = CF.Codigo_Inv 
        ORDER BY TD, Fecha, Serie, Factura, Total desc, No ";

        $sSQL = $SQL1." UNION ".$SQL2." UNION ".$SQL3." UNION ".$SQL4;
         $stmt = $this->db->datos($sSQL);
        return $stmt;


  }

  function deleteClientes_FacturacionProductoClienteAnioMes($codigoCliente, $CodigoInv, $Anio, $NoMes){
     $sSQL = "DELETE 
          FROM Clientes_Facturacion 
          WHERE Item = '".$_SESSION['INGRESO']['item']."' 
          AND Codigo = '$codigoCliente' 
          AND Codigo_Inv = '$CodigoInv' 
          AND Periodo = '$Anio' 
          AND Num_Mes = '$NoMes'";
    $stmt = $this->db->String_Sql($sSQL);
    return $stmt;
  }

  function insertClientes_FacturacionProductoClienteAnioMes($codigoCliente, $CodigoInv, $Valor, $GrupoNo, $NoMes, $Anio, $Mifecha, $Total_Desc, $Total_Desc2){
    SetAdoAddNew('Clientes_Facturacion');
    SetAdoFields("T", G_NORMAL);
    SetAdoFields("Codigo", $codigoCliente);
    SetAdoFields("Codigo_Inv", $CodigoInv);
    SetAdoFields("Valor", $Valor);
    SetAdoFields("Descuento", $Total_Desc);
    SetAdoFields("Descuento2", $Total_Desc2);
    SetAdoFields("GrupoNo", $GrupoNo);
    SetAdoFields("Num_Mes", "$NoMes");
    SetAdoFields("Mes", MesesLetras($NoMes));
    SetAdoFields("Periodo", $Anio);
    SetAdoFields("Fecha", $Mifecha);
    $stmt = SetAdoUpdate();
    Eliminar_Nulos_SP("Clientes_Facturacion");
    return $stmt;
  }

  public function insertClientesMatriculas($TextRepresentante,$TextCI,$TD_Rep,$TxtTelefono,$TxtDireccion,$TxtEmail,$TxtGrupo,$codigoCliente,$CTipoCta,$TxtCtaNo,$CheqPorDeposito,$Caducidad,$DCDebito){

    SetAdoAddNew('Clientes_Matriculas');
    SetAdoFields("Representante", $TextRepresentante);
    SetAdoFields("Cedula_R", $TextCI);
    SetAdoFields("TD", $TD_Rep);
    SetAdoFields("Telefono_R", $TxtTelefono);
    SetAdoFields("Lugar_Trabajo_R", $TxtDireccion);
    SetAdoFields("Email_R", $TxtEmail);
    SetAdoFields("Grupo_No", $TxtGrupo);
    SetAdoFields("Cta_Numero", "$TxtCtaNo");
    SetAdoFields("Tipo_Cta", $CTipoCta);
    SetAdoFields("Caducidad", $Caducidad);
    SetAdoFields("Por_Deposito", $CheqPorDeposito);
    SetAdoFields("Cod_Banco", $DCDebito);
    SetAdoFields("Periodo", $_SESSION['INGRESO']['periodo']);
    SetAdoFields("Codigo", $codigoCliente);
    $stmt = SetAdoUpdate();
    Eliminar_Nulos_SP("Clientes_Matriculas");
    return $stmt;
  }

  public function getBancos($campos, $id, $filtro, $limit)
  {
    $columnas = implode(',', $campos);
    if($columnas==""){
      $columnas ="Codigo, Descripcion";
    }
    $sSQL = "SELECT $columnas
          FROM Tabla_Referenciales_SRI 
          WHERE Tipo_Referencia = 'BANCOS Y COOP'
          ".(($filtro!="")?" AND Descripcion like '%$filtro%' ":"")."
          ".(($id!="")?" AND Codigo = $id ":"")."
          AND Codigo >= '0'
          ORDER BY Descripcion";

    if ($limit) {
      $sSQL .= " OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY";
    }

    $stmt = $this->db->datos($sSQL);
    return $stmt;
  }

  public function getDireccionByGrupo($grupo)
  {
    $sSQL = "SELECT Direccion 
          FROM Clientes 
          WHERE Grupo = '$grupo' 
          AND LEN(Direccion) > 1 
          AND FA <> 0

          ".(($_SESSION['INGRESO']['Mas_Grupos'])?" AND DirNumero = '".$_SESSION['INGRESO']['item']."' ":"")."

          GROUP BY Direccion
          ORDER BY Direccion";
    $stmt = $this->db->datos($sSQL);
    return $stmt;
  }

  public function getDCGrupo($query=false)
  {
    $sql = "SELECT Grupo 
            FROM Clientes 
            WHERE FA <> 0

            ".(($_SESSION['INGRESO']['Mas_Grupos'])?" AND DirNumero = '".$_SESSION['INGRESO']['item']."' ":"")."
            ".(($query)?" AND Grupo LIKE '%".$query."%'  ":"")."

            GROUP BY Grupo 
            ORDER BY Grupo";       
            return $this->db->datos($sql);
  }

  public function Actualiza_Datos_Cliente($data)
  {
    $sSQL = "SELECT Codigo " .
        "FROM Clientes_Matriculas " .
        "WHERE Codigo = '" . $data['codigoCliente'] . "' " .
        "AND Periodo = '" . $_SESSION['INGRESO']['periodo'] . "' " .
        "AND Item = '" . $_SESSION['INGRESO']['item'] . "' ";
    $AdoAux = $this->db->datos($sSQL);
    $MBFecha = (($data['MBFecha']!="")?UltimoDiaMes2("01/".$data['MBFecha'], 'Ymd'):null);

    if (count($AdoAux) <= 0) {
        SetAdoAddNew("Clientes_Matriculas");
        SetAdoFields("T", G_NORMAL);
        SetAdoFields("Codigo", $data['codigoCliente']);
        SetAdoFields("Grupo_No", $data['Grupo_No']);
        SetAdoFields("Lugar_Trabajo_R", $data['TxtDireccion']);
        SetAdoFields("Email_R", $data['TxtEmail']);
        SetAdoFields("Representante", $data['TextRepresentante']);
        SetAdoFields("Cedula_R", $data['TextCI']);
        SetAdoFields("TD", (isset($data['Label18'])?$data['Label18']:$data['TD_Rep']));
        SetAdoFields("Telefono_R", $data['TxtTelefono']);
        SetAdoFields("Cta_Numero", $data['TxtCtaNo']);
        SetAdoFields("Tipo_Cta", $data['CTipoCta']);
        SetAdoFields("Caducidad", $MBFecha);
        SetAdoFields("Por_Deposito", (bool)$data['CheqPorDeposito']);
        SetAdoFields("Cod_Banco", $data['Documento']);
        SetAdoFields("Periodo", $_SESSION['INGRESO']['periodo']);
        SetAdoFields("Item", $_SESSION['INGRESO']['item']);
        SetAdoUpdate();
    } else {
      self::updateClientesMatriculas(
        $data['TextRepresentante'],
        $data['TextCI'],
        (isset($data['Label18'])?$data['Label18']:$data['TD_Rep']),
        $data['TxtTelefono'],
        $data['TxtDireccion'],
        $data['TxtEmail'],
        $data['Grupo_No'],
        $data['codigoCliente'],
        $data['CTipoCta'],
        $data['TxtCtaNo'],
        $data['CheqPorDeposito'],
        $MBFecha,
        (isset($data['Documento'])?$data['Documento']:$data['DCDebito']));
    }
    Leer_Datos_Clientes2($data['codigoCliente']);
    return true;
  }

  public function Reporte_Cartera_Clientes_PDF_Data($CodigoCliente)
  {
    $sSQL = "SELECT C.Cliente, RCC.T, RCC.TC, RCC.Serie, RCC.Factura, RCC.Fecha, RCC.Detalle, RCC.Anio, RCC.Mes, RCC.Cargos, RCC.Abonos, RCC.Saldo, RCC.CodigoC, " .
    "C.Email, C.Email2, C.EmailR, C.Direccion " .
    "FROM Reporte_Cartera_Clientes AS RCC, Clientes AS C " .
    "WHERE RCC.Item = '" . $_SESSION['INGRESO']['item'] . "' " .
    "AND RCC.CodigoU = '" . $CodigoCliente . "' " .
    "AND RCC.T <> 'A' " .
    "AND RCC.CodigoC = C.Codigo " .
    "ORDER BY C.Cliente, RCC.TC, RCC.Serie, RCC.Factura, RCC.Anio, RCC.Mes, RCC.ID ";
    return $this->db->datos($sSQL);
  }
}

?>