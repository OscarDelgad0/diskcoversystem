<?php  @session_start(); 
include("../db/chequear_seguridad.php"); 
include("../controlador/panel.php");
include_once("../funciones/funciones.php");

       $f =date('Y-m-d');
       // print_r($_SESSION);die();
       if(isset($_SESSION['INGRESO']['Fecha']))
       {
          $f =$_SESSION['INGRESO']['Fecha'];
       }
      $date1 = new DateTime(date('Y-m-d'));
      $date2 = new DateTime($f);
      $diff = date_diff($date1, $date2)->format('%R%a días');
      // $interval = date_diff($date1, $date2);
      // echo $interval->format('%R%a días');
      $color='white';
      $estado = 'Infefinido';
      if($diff> 241)
      {
        $color = 'success';
        $estado = 'Licencia activa';

      }else if($diff >= 121 and  $diff <= 240)
      {

        $estado = 'Licencia activa';
        $color = 'success';
      }else if($diff >= 1 and $diff<=120)
      {

        $estado = 'Casi por renovar';
        $color = 'warning';
      }else if($diff <= 0 and isset($_SESSION['INGRESO']['item']))
      {
        $estado = 'licencia vencida';
        $color='danger';
      }

       $f1 =date('Y-m-d');
       if(isset($_SESSION['INGRESO']['Fecha_ce']))
       {
          $f1 =$_SESSION['INGRESO']['Fecha_ce'];
       }
      $date11 = new DateTime(date('Y-m-d'));
      $date21 = new DateTime($f1);
      $diff1 = date_diff($date11, $date21)->format('%R%a días');
      $color1='white';
      $estado1 = 'Infefinido';
      if($diff1 > 241)
      {
        $color1 = 'success';
        $estado1 = 'Comp-Elec. activo';

      }else if($diff1 >= 121 and  $diff1 <= 240)
      {

        $estado1 = 'Comp-Elec. activo';
        $color1 = 'success';
      }else if($diff1 >= 1 and $diff1<=120)
      {

        $estado1 = 'Comp-Elec. por renovar';
        $color1 = 'warning';
      }else if($diff1 <= 0 and isset($_SESSION['INGRESO']['item']))
      {
        $estado1 = 'Comp-Elec. vencida';
        $color1='danger';
      }


?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Diskcover System | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../../bower_components/Ionicons/css/ionicons.min.css">

  <link rel="stylesheet" href="../../bower_components/select2/dist/css/select2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="../../dist/css/style_acordeon.css">
  <link rel="stylesheet" href="../../dist/css/jquery-ui.css">
  <link rel="stylesheet" href="../../dist/css/sweetalert2.min.css">
  <link rel="stylesheet" href="../../dist/css/creados.css">

  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="../../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <script src="../../bower_components/jquery/dist/jquery.min.js"></script>
  <script src="../../bower_components/select2/dist/js/select2.js"></script>
  <script src="../../dist/js/jquery-ui.js"></script>
  <script src="../../dist/js/sweetalert2.js"></script>
  <script src="../../dist/js/js_globales.js?<?php echo date('y') ?>"></script>

  <script type="text/javascript">
  var formato = "<?php if(isset($_SESSION['INGRESO']['Formato_Cuentas'])){echo $_SESSION['INGRESO']['Formato_Cuentas'];}?>";
  function addCliente(){
    $("#myModal").modal("show");
    var src ="../vista/modales.php?FCliente=true";
     $('#FCliente').attr('src',src).show();
  }

  function validador_correo(imput)
  {
      var campo = $('#'+imput).val();   
      var emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
      //Se muestra un texto a modo de ejemplo, luego va a ser un icono
      if (emailRegex.test(campo)) {
        // alert("válido");
        return true;

      } else {
        Swal.fire('Email incorrecto','','info').then(function(){$('#'+imput).select()});
        console.log(campo);
        return false;
      }
  }



  function logout()
  { 
     
     $.ajax({
      // data:  {parametros:parametros},
      url:   '../controlador/login_controller.php?logout=true',
      type:  'post',
      dataType: 'json',
      /*beforeSend: function () {   
           var spiner = '<div class="text-center"><img src="../../img/gif/proce.gif" width="100" height="100"></div>'     
         $('#tabla_').html(spiner);
      },*/
        success:  function (response) { 
          console.log(response);
        if(response == 1)
        {
          location.href = 'login.php';          
        }     
      }
    });
  }

  function cambiar_empresa()
  {      
     $.ajax({
      // data:  {parametros:parametros},
      url:   '../controlador/panel.php?salir_empresa=true',
      type:  'post',
      dataType: 'json',
        success:  function (response) { 
          console.log(response);
        if(response == 1)
        {
          location.href = 'panel.php';          
        }     
      }
    });
  }

  </script>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="">

 
 
