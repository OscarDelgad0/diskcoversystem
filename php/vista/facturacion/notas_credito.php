<script type="text/javascript">
 $(document).ready(function()
  {
  	delete_sientos_nc();
  	DCBodega();
  	DCMarca();
		autocoplete_contraCta()
  	autocoplete_articulos();

  	cargar_tabla();
  	autocoplete_clinete();
  })

function delete_sientos_nc()
{
  	 $.ajax({
      type: "POST",
      url: '../controlador/facturacion/notas_creditoC.php?delete_sientos_nc=true',
      // data: {parametros: parametros},
      dataType:'json', 
      success: function(data)
      {
         //console.log(data);
         //$('#tbl_datos').html(data);
      }
    });
}

function DCBodega()
{
  	 $.ajax({
      type: "POST",
      url: '../controlador/facturacion/notas_creditoC.php?DCBodega=true',
      // data: {parametros: parametros},
      dataType:'json', 
      success: function(data)
      {
      	llenarComboList(data,'DCBodega'); 
      }
    });
}
function DCMarca()
{
  	 $.ajax({
      type: "POST",
      url: '../controlador/facturacion/notas_creditoC.php?DCMarca=true',
      // data: {parametros: parametros},
      dataType:'json', 
      success: function(data)
      {
      	llenarComboList(data,'DCMarca'); 
      }
    });
}

function DCMarca()
{
  	 $.ajax({
      type: "POST",
      url: '../controlador/facturacion/notas_creditoC.php?DCMarca=true',
      // data: {parametros: parametros},
      dataType:'json', 
      success: function(data)
      {
      	llenarComboList(data,'DCMarca'); 
      }
    });
}


function autocoplete_articulos(){
    $('#DCArticulo').select2({
      placeholder: 'Seleccione articulos',
      width:'90%',
      ajax: {
        url:   '../controlador/facturacion/notas_creditoC.php?DCArticulo=true',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
          // console.log(data);
          return {
            results: data
          };
        },
        cache: true
      }
    });
  }

function autocoplete_contraCta(){
    $('#DCContraCta').select2({
      placeholder: 'Seleccione cuenta',
      width:'90%',
      ajax: {
        url: '../controlador/facturacion/notas_creditoC.php?DCContraCta=true',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
          // console.log(data);
          return {
            results: data
          };
        },
        cache: true
      }
    });
  }


function cargar_tabla()
{
  	 $.ajax({
      type: "POST",
      url: '../controlador/facturacion/notas_creditoC.php?tabla=true',
      // data: {parametros: parametros},
      dataType:'json', 
      success: function(data)
      {
         console.log(data);
         $('#tbl_datos').html(data);
      }
    });
}

function autocoplete_clinete(){
      $('#DCClientes').select2({
        placeholder: 'Seleccione una beneficiario',
        width:'90%',
        ajax: {
          url:   '../controlador/facturacion/notas_creditoC.php?cliente=true',
          dataType: 'json',
          delay: 250,
          processResults: function (data) {
            console.log(data);
            return {
              results: data
            };
          },
          cache: true
        }
      });
    }

	
</script>
<div class="row">
	<div class="col-lg-4 col-sm-8 col-md-8 col-xs-12">
		<div class="col-xs-2 col-md-2 col-sm-2">
			 <a  href="<?php $ruta = explode('&' ,$_SERVER['REQUEST_URI']); print_r($ruta[0].'#');?>" title="Salir de modulo" class="btn btn-default">
    		<img src="../../img/png/salire.png">
    	</a>
    	</div>
   </div>
</div>
<div class="row">
	<div class="col-sm-3">
		<div class="form-group">
          <label class="col-sm-4" style="padding:0px">Fecha NC</label>
          <div class="col-sm-8" style="padding:0px">
            <input type="date" name="cta_inventario" id="cta_inventario" class="form-control input-xs">
          </div>
        </div>
	</div>
	<div class="col-sm-9">
		<div class="form-group">
          <label class="col-sm-1" style="padding:0px">Cliente</label>
          <div class="col-sm-11" style="padding:0px">
          	<select class="form-control input-xs" id="DCClientes" name="DCClientes" onchange="">
          		<option>Seleccione cliente</option>
          	</select>
          </div>
        </div>
	</div>
</div>
<div class="row">
	<div class="col-sm-3">
		<b>Lineas de Nota de Credito</b>
		<select class="form-control input-xs">
          	<option>Seleccione cliente</option>
        </select>
	</div>
	<div class="col-sm-3">
		<b>Autorizacion Nota de Credito</b>
		<input type="text" name="" class="form-control input-xs">
	</div>
	<div class="col-sm-1">
		<b>Serie</b>
		<input type="text" name="" class="form-control input-xs">
	</div>
	<div class="col-sm-1" style="padding: 0px;">
		<b>Comp No.</b>
		<input type="text" name="" class="form-control input-xs">
	</div>
	<div class="col-sm-4">
		<b>Contra Cuenta a aplicar a la Nota de Credito</b>
		<select class="form-control input-xs" id="DCContraCta" name="DCContraCta">
      		<option>Seleccione cuenta</option>
      	</select>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="form-group">
          <label class="col-sm-2" style="padding:0px">Motivo de la Nota de credito</label>
          <div class="col-sm-10" style="padding:0px">
            <input type="text" name="cta_inventario" id="cta_inventario" class="form-control input-xs">
          </div>
        </div>
	</div>
</div>
<div class="row">
	<div class="col-sm-1">
		<b>T.D.</b>
		<select class="form-control input-xs">
      		<option>Seleccione cliente</option>
      	</select>
	</div>
	<div class="col-sm-1">
		<b>Serie</b>
		<select class="form-control input-xs">
      		<option>Seleccione cliente</option>
      	</select>
	</div>
	<div class="col-sm-2">
		<b>No.</b>
		<select class="form-control input-xs">
      		<option>Seleccione cliente</option>
      	</select>
	</div>
	<div class="col-sm-4">
		<b>Autorizacion del documento</b>
		<input type="text" name="cta_inventario" id="cta_inventario" class="form-control input-xs">
	</div>
	<div class="col-sm-2">
		<b>Total de Factura</b>
		<input type="text" name="cta_inventario" id="cta_inventario" class="form-control input-xs">
	</div>
	<div class="col-sm-2">
		<b>Saldo de Factura</b>
		<input type="text" name="cta_inventario" id="cta_inventario" class="form-control input-xs">
	</div>	
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
          <label class="col-sm-2" style="padding:0px">Bodega</label>
          <div class="col-sm-10" style="padding:0px">
            <select class="form-control input-xs" id="DCBodega" name="DCBodega">
	      		<option>Seleccione bodega</option>
	      	</select>
          </div>
        </div>
	</div>
	<div class="col-sm-6">
		<div class="form-group">
          <label class="col-sm-2" style="padding:0px">Marca</label>
          <div class="col-sm-10" style="padding:0px">
            <select class="form-control input-xs" id="DCMarca" name="DCMarca">
	      		<option>Seleccione marca</option>
	      	</select>
          </div>
        </div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		
		<div class="panel panel-primary" style="margin-bottom: 0px;">			
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-7">
						<b>Producto</b>
						<select class="form-control input-xs" id="DCArticulo" name="DCArticulo">
				          	<option>Seleccione producto</option>
				        </select>
					</div>
					<div class="col-sm-1">
						<b>Cantidad</b>
						<input type="text" name="" class="form-control input-xs">
					</div>
					<div class="col-sm-1">
						<b>P.V.P.</b>
						<input type="text" name="" class="form-control input-xs">
					</div>
					<div class="col-sm-1">
						<b>DESC</b>
						<input type="text" name="" class="form-control input-xs">
					</div>
					<div class="col-sm-2">
						<b>TOTAL</b>		
						<input type="text" name="" class="form-control input-xs">
					</div>
				</div>
			</div>
		</div>
		<!-- <div class="panel panel-primary">			
			<div class="panel-body">
				<div class="row" style="height: 200px;">
					<div class="col-sm-12" id="tbl_datos">
						
					</div>	
				</div>
			</div>
		</div> -->
	</div>
</div>
<div class="row" style="height: 200px;">
					<div class="col-sm-12" id="tbl_datos">
						
					</div>	
				</div>


<div class="row">
	<div class="col-sm-3">
		
	</div>
	<div class="col-sm-3">
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">
		          <label class="col-sm-6" style="padding:0px">sub total sin iva</label>
		          <div class="col-sm-6" style="padding:0px">
		            <input type="text" name="" class="form-control input-xs">
		          </div>
		        </div>
			</div>
			<div class="col-sm-12">
				<div class="form-group">
		          <label class="col-sm-6" style="padding:0px">sub total con iva</label>
		          <div class="col-sm-6" style="padding:0px">
		            <input type="text" name="" class="form-control input-xs">
		          </div>
		        </div>
			</div>
			<div class="col-sm-12">
				<div class="form-group">
		          <label class="col-sm-6" style="padding:0px">Total descuento</label>
		          <div class="col-sm-6" style="padding:0px">
		            <input type="text" name="" class="form-control input-xs">
		          </div>
		        </div>
			</div>
		</div>		
	</div>
	<div class="col-sm-3">
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">
		          <label class="col-sm-6" style="padding:0px">Sub total</label>
		          <div class="col-sm-6" style="padding:0px">
		            <input type="text" name="" class="form-control input-xs">
		          </div>
		        </div>
			</div>
			<div class="col-sm-12">
				<div class="form-group">
		          <label class="col-sm-6" style="padding:0px">Total del I.V.A</label>
		          <div class="col-sm-6" style="padding:0px">
		            <input type="text" name="" class="form-control input-xs">
		          </div>
		        </div>
			</div>
			<div class="col-sm-12">
				<div class="form-group">
		          <label class="col-sm-6" style="padding:0px">Total Nota Credito</label>
		          <div class="col-sm-6" style="padding:0px">
		            <input type="text" name="" class="form-control input-xs">
		          </div>
		        </div>
			</div>
		</div>		
	</div>
	<div class="col-sm-3">		
		<button class="btn btn-default">
			<img src="../../img/png/grabar.png">
			<br>
			Nota de credito
		</button>
	</div>
</div>