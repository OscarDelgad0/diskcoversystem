<?php
  require_once "../modelo/facturacion/facturar_pensionM.php";
  $facturar = new facturar_pensionM();
  $periodo = $facturar->getPeriodoAbierto();
  if(count($periodo)>0){
    $dataperiodo = explode(" ", $periodo[0]['Detalle']);
  }
?>
<style type="text/css">
  .check-group-xs{
    padding: 3px 6px !important;
    font-size: 5px !important;
  }
  .padding-3{
    padding: 3px !important;
  }

  .padding-l-5{
    padding-left: 5px !important;
  }

  #swal2-content{
    font-weight: 500;
    font-size: 1.3em;
  }
  .text-left{
    text-align: left !important;
  }
  .text-center{
    text-align: center !important;
  }
  .strong {
    font-weight: bold;
  }
  .contenedor_item_center{
    display: flex;
      justify-content: center;
      gap: 10px;"
  }
.no-visible{
  visibility: hidden;
}
</style>
<div class="box box-info">
    <div class="box-header">
        <h4>Ingreso de Consumo de Agua</h4>
    </div>
    <div class="box-body">    
        <div class="row">
            <div class="col-lg-offset-2 col-lg-8">
                <form class="form-horizontal"  id="FIngresoConsumoAgua" name="FIngresoConsumoAgua">
                  <fieldset>
                    <label>Digite Código del Medidor</label>
                    <div class="form-group">
                      <label for="CMedidor" class="col-xs-2 control-label">Código Medidor</label>
                      <div class="col-xs-10">
                        <input  onkeydown="if (event.keyCode === 13) $('#CMedidor').blur()" type="text" class="form-control input-xs " name="CMedidor" id="CMedidor" placeholder="0" style="max-width: 150px;display: inline-block;" tabindex="1">
                        <input type="hidden"  name="codigoCliente" id="codigoCliente">
                      <!-- <label class="text-red">Con servicio y medidor</label> -->
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="NameUsuario" class="col-xs-2 control-label">Usuario</label>
                      <div class="col-xs-10">
                        <input type="text" class="form-control input-xs " name="NameUsuario" id="NameUsuario" readonly>
                      </div>
                    </div>
                    <hr style="margin: 0px;">
                    <div class="form-group">
                      <label class="col-xs-12 control-label text-red text-center labelUltimaLectura no-visible">Ultima lectura <span id="FechaUltimaLectura"></span> =>(<span id="UltimaLectura"></span>)</label>
                    </div>
                    <div class="form-group">
                      <label class="col-xs-2 control-label">Año</label>
                      <div class="col-xs-4">
                        <input type="text" class="form-control input-xs " value="<?php echo @$dataperiodo[0] ?>" name="anio" id="anio" readonly>
                      </div>
                      <label class="col-xs-2 control-label">Mes</label>
                      <div class="col-xs-4">
                        <input type="text" class="form-control input-xs " value="<?php echo @$dataperiodo[1] ?>" name="mes" id="mes" readonly>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-lg-7">
                        <div class="radio">
                          <label>
                            <input type="radio" name="optionrango"  value="menos15" checked="" >
                            Menos de 15.000 metros cúbicos.
                          </label>
                        </div>
                        <div class="radio">
                          <label>
                            <input type="radio" name="optionrango"  value="mas15" >
                            Más de 15.000 metros cúbicos.
                          </label>
                        </div>
                      </div>
                      <div class="col-lg-5  text-right">
                        <!-- <div class="checkbox">
                          <label>
                            <input type="checkbox" name="optionsRadios" id="optionsRadios2" value="option2">
                            Medidor vuelve a 0.
                          </label>
                        </div> -->
                      </div>
                    </div>
                    <div class="form-group" style=" margin-bottom: 2px;">
                      <!-- <label for="inputPassword" class="col-xs-12 col-lg-6 control-label text-red text-left">Ingrese lectura de Noviembre/2022</label> -->
                      <div class="col-xs-12 col-lg-6  strong text-right" >
                        <input onkeydown="if (event.keyCode === 13) GuardarConsumoAgua()"  style="max-width: 120px;display: inline-block;" type="tel" class="form-control input-xs " name="Lectura" id="Lectura" tabindex="2"> m<sup>3</sup>
                        <label class="errorLectura" style="color: red;"></label>
                      </div>
                    </div>
                    <div class="form-group" style=" margin-bottom: 2px;">
                      <!-- <label for="inputPassword" class="col-lg-12 control-label text-red text-left">Promedio de Consumo: 67</label> -->
                    </div>
                    <div class="form-group" style=" margin-bottom: 2px;">
                      <!-- <label for="inputPassword" class="col-lg-12 control-label text-red text-left">Consumo: 20 (47 Bajo el Promedio)</label> -->
                    </div>
                    <!-- <div class="form-group">
                      <label for="inputPassword" class="col-xs-3 control-label ">Multas:</label>
                      <div class="col-xs-4">
                        <input type="tel" class="form-control input-xs " name="Multa" id="Multa" placeholder="0.00">
                      </div>
                    </div> -->
                  </fieldset>
                </form>
            </div>
        </div>

        <div class="row contenedor_item_center">
            <button class="btn btn-success" title="Guardar Consumo" onclick="GuardarConsumoAgua()">
              <img  src="../../img/png/grabar.png" width="25" height="30" tabindex="3">
            </button>
            
            </button>
            <a href="./inicio.php?mod=<?php echo @$_GET['mod']?>" class="btn btn-warning" id="btnSalirModuloPF" title="Salir del Modulo" data-dismiss="modal">
              <img  src="../../img/png/salire.png" width="25" height="30">
            </a>
        </div>
    </div>
</div>


<script type="text/javascript">
  $(document).ready(function () {
    $("#CMedidor").focus();
    $("#Lectura").on('blur',function () {
      if(parseFloat($("#Lectura").val())<parseFloat($("#UltimaLectura").text())){
        $("#Lectura").select();
        $(".errorLectura").text("La lectura actual no puede ser inferior a la anterior")
      }else{
        $(".errorLectura").text("") 
        if(parseFloat($("#Lectura").val())-parseFloat($("#UltimaLectura").text())>15){
          $('input[name="optionrango"][value="mas15"]').prop('checked', true);
        }else{
          $('input[name="optionrango"][value="menos15"]').prop('checked', true);
        }
      }
    })
    $("#CMedidor").on("blur", function(){
      let medidor = $("#CMedidor").val();
      if(medidor!=""){
        $.ajax({
          type: "POST",                 
          url: '../controlador/facturacion/facturar_pensionC.php?BuscarClienteCodigoMedidor='+medidor,
          dataType:'json', 
          beforeSend: function () {   
            $('#myModal_espera').modal('show');
          },    
          success: function(response)
          {
            $('#myModal_espera').modal('hide');  
            if(response.rps){
              $("#NameUsuario").val(response.data.Cliente);
              $("#codigoCliente").val(response.data.Codigo);
              $("#FechaUltimaLectura").text(response.data.fechaUltimaMedida);
              $("#UltimaLectura").text(response.data.ultimaMedida);
              $("#Lectura").focus();
              $(".labelUltimaLectura").removeClass('no-visible')
            }else{
              Swal.fire('¡Oops!', response.mensaje, 'warning')
            }        
          },
          error: function () {
            $('#myModal_espera').modal('hide');
            alert("Ocurrio un error inesperado, por favor contacte a soporte.");
          }
        });
      }
    })
  });
  
  function OpenModalIngresoConsumoAgua(){
      $('.myModalNuevoCliente').modal('hide');
      $('#myModalIngresoConsumoAgua').modal('show');
  }


  function GuardarConsumoAgua() {
    let medidor = $("#CMedidor").val();
    $("#CMedidor").focus();
    if(medidor!=""){
      $('#myModal_espera').modal('show');

      $.ajax({
          type: "POST",                 
          url: '../controlador/facturacion/facturar_pensionC.php?GuardarConsumoAgua=true',
          data: $("#FIngresoConsumoAgua").serialize(),
          dataType:'json', 
          beforeSend: function () {   
            $('#myModal_espera').modal('show');
          },    
          success: function(response)
          {
            $('#myModal_espera').modal('hide');  
            if(response.rps){
              $("#FIngresoConsumoAgua")[0].reset();
              Swal.fire('¡Bien!', response.mensaje, 'success');
              $("#FechaUltimaLectura").text('');
              $("#UltimaLectura").text('');
              $(".labelUltimaLectura").addClass('no-visible')
            }else{
              Swal.fire('¡Oops!', response.mensaje, 'warning')
            }        
          },
          error: function () {
            $('#myModal_espera').modal('hide');
            alert("Ocurrio un error inesperado, por favor contacte a soporte.");
          }
        });
    }else{
      Swal.fire('¡Oops!', "No ha seleccionado ningun producto.", 'info')
    }
  }
</script>