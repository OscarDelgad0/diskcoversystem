<?php date_default_timezone_set('America/Guayaquil');  //print_r($_SESSION);die();//print_r($_SESSION['INGRESO']);die();?>
<script type="text/javascript">
	window.closeModal = function(){
    $('#myModal_Abonos').modal('hide');
     Autorizar_Factura_Actual();
};
	 let Modificar = false;
   let Bandera = true;
	var PorCodigo=false;
  $(document).ready(function () {
  	var tipo = "<?php echo $_GET['tipo']; ?>";
  	$('#TipoFactura').val(tipo);
  	Eliminar_linea('','');
  	 // lineas_factura();
  	 numero_factura();
  	 DCTipoPago();
  	 DCMod();
  	 DCMedico();
  	 DCGrupo_No();
  	 DCLineas(); 
  	 FPorCodigo();  	
  	 CDesc1();
  	 DCEjecutivo();
  	 DCBodega();
  	 DCMarca();
  	 autocomplete_cliente();
  	 autocomplete_producto();
  	  LstOrden();
  	 // Lineas_De_CxC();
  	  $('#DCCliente').on('select2:select', function (e) {
      var data = e.params.data.datos;
      	$('#LabelCodigo').val(data.Codigo);
  			$('#LabelTelefono').val(data.Telefono);
  			$('#LabelRUC').val(data.CI_RUC);
  			$('#Label21').val(data.Actividad);
  			$('#Label24').val(data.Direccion);
  			$('#TxtEmail').val(data.Email);
  			$('#LblSaldo').val(parseFloat(data.Saldo_Pendiente).toFixed(2));
  			$('#Label13').text('C.I./R.U.C. ('+data.TD+')');
      
      console.log(data);
    });


  $('#cambiar_nombre').on('hide.bs.modal', function () {

     setTimeout(function(){ $('#TextComEjec').focus(); },500);
  	// alert('asda');
	})

		$('#TxtDetalle').keydown(function(e) {
			  var keyCode = e.keyCode || e.which; 
			  if (keyCode == 9) {
						$('#TextComEjec').focus();
					}
		})

  });

  function DCTipoPago()
  {
  	
  	$.ajax({
			type: "POST",
			url: '../controlador/facturacion/facturarC.php?DCTipoPago=true',
			//data: {parametros: parametros},
			dataType:'json',
			success: function(data)
			{
				llenarComboList(data,'DCTipoPago'); 
			}
		});

  }
  function DCMod()
  {
  	
  	$.ajax({
			type: "POST",
			url: '../controlador/facturacion/facturarC.php?DCMod=true',
			//data: {parametros: parametros},
			dataType:'json',
			success: function(data)
			{
				if(data.length>0)
				{
				 llenarComboList(data,'DCMod'); 
				}else
				{
					$('#DCMod').css('display','block');
				}
			}
		});

  }


  function DCMedico()
  {
  	
  	$.ajax({
			type: "POST",
			url: '../controlador/facturacion/facturarC.php?DCMedico=true',
			//data: {parametros: parametros},
			dataType:'json',
			success: function(data)
			{
				if(data.length>0)
				{
					llenarComboList(data,'DCMedico'); 
				}else
				{
					$('#DCMedico').css('display','none');

				}
			}
		});

  }


 function DCGrupo_No()
  {
  	$('#DCGrupo_No').select2({
      placeholder: 'Grupo',
      ajax: {
        url: '../controlador/facturacion/facturarC.php?DCGrupo_No=true',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
          return {
            results: data
          };
        },
        cache: true
      }
    });
  }


  function DCLineas()
  {
  	var parametros = 
  	{
  		'Fecha':$('#MBoxFecha').val(),
  		'TC':$('#TipoFactura').val(),
  	}
  	$.ajax({
		type: "POST",
		url: '../controlador/facturacion/facturarC.php?DCLineas=true',
		data: {parametros: parametros},
		dataType:'json',
		success: function(data)
		{
			llenarComboList(data,'DCLineas');
			$('#Cod_CxC').val(data[0].nombre);  //FA
			Lineas_De_CxC();
		}
	});
  }

  function FPorCodigo()
  {  	
  	$.ajax({
			type: "POST",
			url: '../controlador/facturacion/facturarC.php?PorCodigo=true',
			// data: {parametros: parametros},
			dataType:'json',
			success: function(data)
			{
				if(data!=0)
				{
					PorCodigo = true;
				}			
			}
		});
  }

  function Lineas_De_CxC(TC,cod_CXC)
  { 
  	if(TC!='')
  	{
  		TC = $('#TipoFactura').val();

  	}
  	if(cod_CXC!='')
  	{
  		// cod_CXC = $('#DCLineas option:selected').text();
  		cod_CXC = $('#Cod_CxC').val();
  	}
  	var parametros = 
  	{	'TC' :TC,
			'Fecha': $('#MBoxFecha').val(),
			'Cod_CxC': cod_CXC,
			'Vencimiento':$('#MBoxFechaV').val(),
  	}

  	$.ajax({
			type: "POST",
			url: '../controlador/facturacion/facturarC.php?Lineas_De_CxC=true',
			data:  {parametros:parametros},
			dataType:'json',
			success: function(data)
			{
				console.log(data.TFA);
				$("#TC").val(data.TFA.TC);   //FA
				$("#Autorizacion").val(data.TFA.Autorizacion);   //FA
				$("#CantFact").val(data.TFA.CantFact);   //FA
				$("#Cant_Item").val(data.TFA.Cant_Item_FA);   //FA
				$("#Cod_CxC").val(data.TFA.Cod_CxC);   //FA
				$("#Cta_CxP").val(data.TFA.Cta_CxP);   //FA
				$("#Cta_CxP_Anterior").val(data.TFA.Cta_CxP_Anterior);   //FA
				$("#Cta_Venta").val(data.TFA.Cta_Venta);   //FA
				$("#CxC_Clientes").val(data.TFA.CxC_Clientes);   //FA

				$("#DireccionEstab").val(data.TFA.DireccionEstab);   //FA
				$("#Fecha").val(data.TFA.Fecha);   //FA
				$("#Fecha_Aut").val(data.TFA.Fecha_Aut.date);   //FA
				$("#Fecha_NC").val(data.TFA.Fecha_NC);   //FA
				$("#Imp_Mes").val(data.TFA.Imp_Mes);   //FA


				$("#NoFactura").val(data.TFA.NoFactura);   //FA
				$("#NombreEstab").val(data.TFA.NombreEstab);   //FA
				$("#Porc_IVA").val(data.TFA.Porc_IVA);   //FA
				$("#Porc_Serv").val(data.TFA.Porc_Serv);   //FA
				$("#Pos_Copia").val(data.TFA.Pos_Copia);   //FA
				$("#Pos_Factura").val(data.TFA.Pos_Factura);   //FA
				$("#Serie").val(data.TFA.Serie);   //FA
				$("#TelefonoEstab").val(data.TFA.TelefonoEstab);   //FA
				$("#Vencimiento").val(data.TFA.Vencimiento.date);   //FA

				if(data.respuesta==1)
				{
					console.log(data.TFA);
					Tipo_De_Facturacion(data.TFA);
					$('#Cant_Item').val(data.TFA.Cant_Item_FA ); //FA
				}else if(data.respuesta==2)
				{
					Tipo_De_Facturacion(data.TFA);
					$('#Cant_Item').val(data.TFA.Cant_Item_FA ); //FA
						swal.fire(data.mensaje,'','info');
				}else
				{
						swal.fire(data.mensaje,'','info');
						Tipo_De_Facturacion(data.TFA);
					  $('#Cant_Item').val(data.TFA.Cant_Item_FA ); //FA
				}
				
			}
		});

  }

  function Tipo_De_Facturacion(data)
  {  	 				
  	// console.log(data.Autorizacion);
  	// console.log(data.Serie);
  	// console.log(data.Porc_IVA);
				var TC = $('#TipoFactura').val();
			  if(TC == "NV"){
			     // Facturas.Caption = "INGRESAR NOTA DE VENTA"
			      $('#label2').text(data.Autorizacion+" NOTA DE VENTA No. "+data.Serie+"-");
			     $('#label3').text("I.V.A. 0.00%");
			  }else if(TC == "OP"){
			     // Facturas.Caption = "INGRESAR ORDEN DE PEDIDO"
			     $('#label2').text(data.Autorizacion +" ORDEN No. "+data.Serie+"-");
			     $('#label3').text("I.V.A. 0.00%");
			     $('#TextFacturaNo').val(data.NoFactura);
			  }else{
			     // Facturas.Caption = "INGRESAR FACTURA"
			     $('#label2').text(data.Autorizacion+" FACTURA No. "+data.Serie+"-");
			     $('#label3').text("I.V.A. "+(parseFloat(data.Porc_IVA) * 100).toFixed(2)+"%")
			     $('#TextFacturaNo').val(data.NoFactura);
			  }
			  // 'Facturas.Caption = Facturas.Caption & " (" & FA.TC & ")"
			  $('#label36').text("Serv. "+(data.Porc_Serv * 100).toFixed(2)+"%")
  }

  function DCEjecutivo()
  {  	
  	$.ajax({
			type: "POST",
			url: '../controlador/facturacion/facturarC.php?DCEjecutivo=true',
			//data: {parametros: parametros},
			dataType:'json',
			success: function(data)
			{
				if(data.length>0)
				{
					llenarComboList(data,'DCEjecutivo'); 
				}else
				{
					$('#DCEjecutivo').append($('<option>',{value:'.', text:'.',selected: true }));
					$('#DCMedico').css('display','none');

				}
			}
		});

  }




  function lineas_factura()
  {
  	var parametros = 
  	{
  		'codigoCliente':'',
  	}
  	$.ajax({
		type: "POST",
		url: '../controlador/facturacion/facturarC.php?lineas_factura=true',
		// data: {parametros: parametros},
		dataType:'json',
		beforeSend: function () {	$('#tbl').html('<img src="../../img/gif/loader4.1.gif" width="40%"> ');}, 		
		success: function(data)
		{
			console.log(data);
			$('#tbl').html(data.tbl);
			$('#Mod_PVP').val(data.Mod_PVP);
			if(data.DCEjecutivo==0)
			{
				$('#DCEjecutivoFrom').css('display','none');
			}
			if(data.TextFacturaNo==0)
			{
				$('#TextFacturaNo').attr('readonly',true);
			}

			 $('#LabelSubTotal').val(parseFloat(data.totales.Sin_IVA).toFixed(2));
		   $('#LabelConIVA').val(parseFloat(data.totales.Con_IVA).toFixed(2));
		   $('#TextDesc').val(parseFloat(data.totales.Descuento).toFixed(2));
		   $('#LabelServ').val(parseFloat(data.totales.Servicio).toFixed(2));
		   $('#LabelIVA').val(parseFloat(data.totales.Total_IVA).toFixed(2));
		   $('#LabelTotal').val(parseFloat(data.totales.Total_MN).toFixed(2));
  
		}
	});

  }

  

  function DCBodega()
  {
  	
  	$.ajax({
		type: "POST",
		url: '../controlador/facturacion/facturarC.php?DCBodega=true',
		//data: {parametros: parametros},
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
			url: '../controlador/facturacion/facturarC.php?DCMarca=true',
			//data: {parametros: parametros},
			dataType:'json',
			success: function(data)
			{
				llenarComboList(data,'DCMarca'); 
			}
		});

  }


  function CDesc1()
  {
  	
  	$.ajax({
			type: "POST",
			url: '../controlador/facturacion/facturarC.php?CDesc1=true',
			//data: {parametros: parametros},
			dataType:'json',
			success: function(data)
			{
				llenarComboList(data,'CDesc1'); 
			}
		});

  }



  function autocomplete_cliente()
  {
  	var grupo = $('#DCGrupo_No').val();
  	console.log(grupo);
    $('#DCCliente').select2({
      placeholder: 'Seleccione un cliente',
      ajax: {
        url: '../controlador/facturacion/facturarC.php?DCCliente=true&Grupo='+grupo,
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
          return {
            results: data
          };
        },
        cache: true
      }
    });
  }

  function autocomplete_producto()
  {
  	var marca = $('#DCMarca').val();
  	var cod_marca = $('#DCMarca').val();
  	// console.log(grupo);
    $('#DCArticulos').select2({
      placeholder: 'Seleccione un cliente',
      ajax: {
        url: '../controlador/facturacion/facturarC.php?DCArticulos=true&marca='+marca+'&codMarca='+cod_marca,
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
          return {
            results: data
          };
        },
        cache: true
      }
    });
  }

  function LstOrden()
  {
  	$.ajax({
			type: "POST",
			url: '../controlador/facturacion/facturarC.php?LstOrden=true',
			// data: {parametros: parametros},
			dataType:'json',
			success: function(data)
			{
				console.log(data);
			}
		});

  }

  function numero_factura()
  {  	
  	$.ajax({
		type: "POST",
		url: '../controlador/facturacion/facturarC.php?numero_factura=true',
		// data: {parametros: parametros},
		dataType:'json',
		success: function(data)
		{
			$('#CheqSPFrom').css('display','initial');
					
			console.log(data);
		}
	});


  }

  function DCArticulo_LostFocus()
  {
  	  var parametros = {
  	  	'codigo':$('#DCArticulos').val(),
  	  	'fecha':$('#MBoxFecha').val(),
  	  	'bodega':$('#DCBodega').val(),
  	  	'marca':$('#DCMarca').val(),
  	  	'tipoFactura':$('#TipoFactura').val(),
  	  }
			$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?DCArticulo_LostFocus=true',
			  data: {parametros:parametros }, 
			  dataType:'json',
			  success: function(data)
			  {
			  	$('#TextVUnit').val(data.TextVUnit);
			  	$('#LabelStock').val(data.labelstock);
			  	$('#LabelStockArt').html(data.LabelStockArt);
			  	$('#TextComEjec').val(data.TextComEjec);
			  	$('#TxtDetalle').val(data.TxtDetalle);
			  	$('#BanIVA').val(data.baniva);
			  	// $('#DCArticulos').focus();
			  	// $('#cambiar_nombre').modal('show');
			  
			  $('#cambiar_nombre').on('shown.bs.modal', function () {
					    $('#TxtDetalle').focus();
					})

			   $('#cambiar_nombre').modal('show', function () {
    					$('#TxtDetalle').focus();
					})

			  }
			})
   
  }


  function cerrar_modal_cambio_nombre()
  {

     $('#cambiar_nombre').modal('hide');

     var nuevo = $('#TxtDetalle').val();
     var dcart = $('#DCArticulos').val();
     $('#DCArticulos').append($('<option>',{value:dcart, text:nuevo,selected: true }));
     // $('#TextComEjec').focus();
    
  }
  
  function TextCant_Change()
  {
  	var Real1 = 0;
  	if ($('#TextCant').val() == ""){ $('#TextCant').val(0);}
    if ($('#TextVUnit').val() == ""){ $('#TextVUnit').val(0)}
  
  	if($('#TextCant').val() != 0 && $('#TextVUnit').val() != 0) { var Real1 = $('#TextCant').val() *$('#TextVUnit').val() }
  		$('#LabelVTotal').val(Real1.toFixed(2));
  }

function TextVUnit_LostFocus()
{
	if($('#DCCliente').val()=='')
	{
		Swal.fire('Seleccione un cliente','','info');
		return false;
	}
	  var parametros = {
  	  	'codigo':$('#DCArticulos').val(),
  	  	'fecha':$('#MBoxFecha').val(),
  	  	'fechaV':$('#MBoxFechaV').val(),
  	  	'fechaVGR':$('#MBoxFechaV').val(), //ojo poner el verdadero
  	  	'TxtDetalle':$('#TxtDetalle').val(),
  	  	'bodega':$('#DCBodega').val(),
  	  	'marca':$('#DCMarca').val(),
  	  	'Cliente':$('#DCCliente').val(),
  	  	'Cant_Item_FA':$('#Cant_Item').val(),
  	  	'tipoFactura':$('#TipoFactura').val(),
  	  	'Mod_PVP':$('#Mod_PVP').val(),
  	  	'DatInv_Serie_No':$('#DatInv_Serie_No').val(),
  	  	'TextVUnit':$('#TextVUnit').val(),
  	  	'TextCant':$('#TextCant').val(),
  	  	'TextFacturaNo':$('#TextFacturaNo').val(),
  	  	'TextComision':$('#TextComision').val(),
  	  	'CDesc1':$('#CDesc1').val(),
  	  	'BanIVA':$('#BanIVA').val(),
  	  	'TextComEjec':$('#TextComEjec').val(),
  	  	'SubCta':'.',
  	  	'Cod_Ejec':$('#DCEjecutivo').val(),
  	  	'CodigoL': $('#DCLineas').val(),
  	  	'MBFechaIn':$('#MBoxFechaV').val(), //ojo poner el verdadero
  	  	'MBFechaOut':$('#MBoxFechaV').val(), //ojo poner el verdadero
  	  	'TxtCantRooms':'.',//$('#MBoxFechaV').val(), //ojo poner el verdadero  	  	
  	  	'TxtTipoRooms':'.',//$('#MBoxFechaV').val(), //ojo poner el verdadero
  	  	'LstOrden':'.',//$('#MBoxFechaV').val(), //ojo poner el verdadero
  	  	'Sec_Public':$('#CheqSP').prop('checked'),
  	  }
			$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?TextVUnit_LostFocus=true',
			  data: {parametros:parametros }, 
			  dataType:'json',
			  beforeSend: function () {	$('#tbl').html('<img src="../../img/gif/loader4.1.gif" width="40%"> ');}, 		
			  success: function(data)
			  {
			  	if(data==1)
			  	{
			  		lineas_factura();
			  	}else
			  	{
			  		swal.fire(data,'','info');
			  	}
			  	
			  }
			})
}


function Eliminar_linea(ln_No,Cod)
{
	  var parametros = {
  	  	'codigo':Cod,  	  	
  	  	'ln_No':ln_No,
  	  }
			$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?Eliminar_linea=true',
			  data: {parametros:parametros }, 
			  dataType:'json',
			  success: function(data)
			  {
			  	if(data==1)
			  	{
			  	  lineas_factura();			  	
			    }
			  }
			})
   

}

  function addCliente(){
    $("#myModal_cliente").modal("show");
    var src ="../vista/modales.php?FCliente=true";
     $('#FCliente').attr('src',src).show();
  }

function DCLinea_LostFocus()
{ 
	  Lineas_De_CxC();
}

function boton1()
{
	var TC = 	$('#TipoFactura').val();
	var FAC = $('#TextFacturaNo').val();
	 if(TC=='OP')
	 {
	 	Mensajes = "La Orden de Producción No. "+FAC;
	 }else
	 {
	 	 Mensajes = "La Factura No. "+FAC
	 }
	  Swal.fire({
     title: 'Esta Seguro que desea grabar?',
     text: Mensajes,
     type: 'warning',
     showCancelButton: true,
     confirmButtonColor: '#3085d6',
     cancelButtonColor: '#d33',
     confirmButtonText: 'Si!'
   }).then((result) => {
     if (result.value==true) {
      Grabar_Factura_Actual();
     }
   })

}

function Grabar_Factura_Actual()
{
	var FA = $("#FA").serialize();
	var parametros = {
		'TextObs':$('#TextObs').val(),
		'TextNota':$('#TextNota').val(),
		'TxtCompra':$('#TxtCompra').val(),
		'TxtPedido':$('#TxtPedido').val(),
		'TxtZona':$('#TxtZona').val(),
		'TxtLugarEntrega':$('#TxtLugarEntrega').val(),
		'TextComision':$('#TextComision').val(),
		'MBoxFechaV':$('#MBoxFechaV').val(),
		'Check1':$('#Check1').prop('checked'),
		'CheqSP':$('#CheqSP').prop('checked'),
		// 'DCTipoPago':$('#DCTipoPago option:selected').text(),
		'DCTipoPago':$('#DCTipoPago').val(),
		'TextFacturaNo':$('#TextFacturaNo').val(),
		'DCMod':$('#DCMod').val(),
		'Reprocesar':$('#Reprocesar').val(),
		'Cliente':$('#DCCliente').val(),
		'Total':$('#LabelTotal').val(),
	}
		$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?Grabar_Factura_Actual=true&'+FA,
			  data: {parametros:parametros}, 
			  dataType:'json',
			  success: function(data)
			  {
			  	console.log(data);
			  	if(data.res==-2)
			  	{
			  		alerta_reprocesar('ADVERTENCIA',data.men);
			  	}else if(data.res==-3)
			  	{
			  		alerta_reprocesar('Formulario de Confirmación',data.men);
			  	}else if(data.res==1)
			  	{
			  		Abonos();
			  	}
			  	
			  }
			})
	}

function Autorizar_Factura_Actual2()
{
	var FA = $("#FA").serialize();
	var parametros = {
		'TextObs':$('#TextObs').val(),
		'TextNota':$('#TextNota').val(),
		'TxtCompra':$('#TxtCompra').val(),
		'TxtPedido':$('#TxtPedido').val(),
		'TxtZona':$('#TxtZona').val(),
		'TxtLugarEntrega':$('#TxtLugarEntrega').val(),
		'TextComision':$('#TextComision').val(),
		'MBoxFechaV':$('#MBoxFechaV').val(),
		'Check1':$('#Check1').prop('checked'),
		'CheqSP':$('#CheqSP').prop('checked'),
		// 'DCTipoPago':$('#DCTipoPago option:selected').text(),
		'DCTipoPago':$('#DCTipoPago').val(),
		'TextFacturaNo':$('#TextFacturaNo').val(),
		'DCMod':$('#DCMod').val(),
		'Reprocesar':$('#Reprocesar').val(),
		'Cliente':$('#DCCliente').val(),
		'Total':$('#LabelTotal').val(),
	}

	// var url=  '../controlador/facturacion/facturarC.php?Autorizar_Factura_Actual=true&'+FA+'&'+parametros.serialize();;
	// window.open(url, '_blank'); 
		$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?Autorizar_Factura_Actual=true&'+FA,
			  data: {parametros:parametros}, 
			  dataType:'json',
			  success: function(data)
			  {
			  	var url=  '../vista/TEMP/'+data+'.pdf';
					window.open(url, '_blank'); 				  	
			  }
			})
	}


function Autorizar_Factura_Actual()
{
	$('#myModal_espera').modal('show');
	var FA = $("#FA").serialize();
	var parametros = {
		'TextObs':$('#TextObs').val(),
		'TextNota':$('#TextNota').val(),
		'TxtCompra':$('#TxtCompra').val(),
		'TxtPedido':$('#TxtPedido').val(),
		'TxtZona':$('#TxtZona').val(),
		'TxtLugarEntrega':$('#TxtLugarEntrega').val(),
		'TextComision':$('#TextComision').val(),
		'MBoxFechaV':$('#MBoxFechaV').val(),
		'Check1':$('#Check1').prop('checked'),
		'CheqSP':$('#CheqSP').prop('checked'),
		// 'DCTipoPago':$('#DCTipoPago option:selected').text(),
		'DCTipoPago':$('#DCTipoPago').val(),
		'TextFacturaNo':$('#TextFacturaNo').val(),
		'DCMod':$('#DCMod').val(),
		'Reprocesar':$('#Reprocesar').val(),
		'Cliente':$('#DCCliente').val(),
		'Total':$('#LabelTotal').val(),
	}
		$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?Autorizar_Factura_Actual=true&'+FA,
			  data: {parametros:parametros}, 
			  dataType:'json',
			  success: function(data)
			  {

			$('#myModal_espera').modal('hide');
			  	if(data.AU.respuesta==1)
			  	{
			  		var url=  '../../TEMP/'+data.pdf+'.pdf';
					   window.open(url, '_blank'); 				  	
					   	Swal.fire({
								type:'success',
							  title: 'Factura Autorizada',
							  confirmButtonText: 'Ok!',
							  allowOutsideClick: false,
							}).then((result) => {
							  /* Read more about isConfirmed, isDenied below */
							  if (result.value) {
							  	location.reload();
							  } 
							})

			  	}else if(data.AU.respuesta==3)
			  	{
			  		 Swal.fire('Factura Autorizada','','success');
			  	} 	
			  }
			})
	}


	function imprimir_multiple()
	{
		var FA = $("#FA").serialize();
		var parametros = {
			'TextObs':$('#TextObs').val(),
			'TextNota':$('#TextNota').val(),
			'TxtCompra':$('#TxtCompra').val(),
			'TxtPedido':$('#TxtPedido').val(),
			'TxtZona':$('#TxtZona').val(),
			'TxtLugarEntrega':$('#TxtLugarEntrega').val(),
			'TextComision':$('#TextComision').val(),
			'MBoxFechaV':$('#MBoxFechaV').val(),
			'Check1':$('#Check1').prop('checked'),
			'CheqSP':$('#CheqSP').prop('checked'),
			// 'DCTipoPago':$('#DCTipoPago option:selected').text(),
			'DCTipoPago':$('#DCTipoPago').val(),
			'TextFacturaNo':$('#TextFacturaNo').val(),
			'DCMod':$('#DCMod').val(),
			'Reprocesar':$('#Reprocesar').val(),
			'Cliente':$('#DCCliente').val(),
			'Total':$('#LabelTotal').val(),
		}
		$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?imprimir_factura=true&'+FA,
			  data: {parametros:parametros}, 
			  dataType:'json',
			  success: function(data)
			  {
			  	if(data.AU.respuesta==1)
			  	{
			  		 Swal.fire('Factura Autorizada','','success');
				  	console.log(data);
				  	imprimir();			 
			  	} 	
			  }
			})	  
	}



function Abonos(FA)
{
	Swal.fire({
     title: 'PAGO AL CONTADO',
     text: '',
     type: 'warning',
     showCancelButton: true,
     confirmButtonColor: '#3085d6',
     cancelButtonColor: '#d33',
     confirmButtonText: 'Si!'
   }).then((result) => {
     if (result.value==true) {

     	  if(FA == "OP"){
     	  	// la otra opcion
        }else{
        	src ="../vista/modales.php?FAbonos=true";
					$('#frame').attr('src',src).show();
         	$('#myModal_Abonos').modal('show');	  
      		// Autorizar_Factura_Actual();
        }
     }else
     {
	  	 Autorizar_Factura_Actual();
     }
   })
}


function alerta_reprocesar(tit,mensaje)
{
	  Swal.fire({
     title: tit,
     text: mensaje,
     type: 'warning',
     showCancelButton: true,
     confirmButtonColor: '#3085d6',
     cancelButtonColor: '#d33',
     confirmButtonText: 'Si!'
   }).then((result) => {
     if (result.value==true) {
	  	$('#Reprocesar').val(1)
      Grabar_Factura_Actual();
     }else
     {
	  	$('#Reprocesar').val(0)
     }
   })

}

function alerta_abonos(tit,mensaje)
{
	  Swal.fire({
     title: tit,
     text: mensaje,
     type: 'warning',
     showCancelButton: true,
     confirmButtonColor: '#3085d6',
     cancelButtonColor: '#d33',
     confirmButtonText: 'Si!'
   }).then((result) => {
     if (result.value==true) {
	  	$('#Reprocesar').val(1)
      Grabar_Factura_Actual();
     }else
     {
	  	$('#Reprocesar').val(0)
     }
   })

}

function Grabar_Abonos()
{
	var FA = $("#FA").serialize();
	var parametros = {
		'TextObs':$('#TextObs').val(),
		'TextNota':$('#TextNota').val(),
		'TxtCompra':$('#TxtCompra').val(),
		'TxtPedido':$('#TxtPedido').val(),
		'TxtZona':$('#TxtZona').val(),
		'TxtLugarEntrega':$('#TxtLugarEntrega').val(),
		'TextComision':$('#TextComision').val(),
		'MBoxFechaV':$('#MBoxFechaV').val(),
		'Check1':$('#Check1').prop('checked'),
		'CheqSP':$('#CheqSP').prop('checked'),
		// 'DCTipoPago':$('#DCTipoPago option:selected').text(),
		'DCTipoPago':$('#DCTipoPago').val(),
		'TextFacturaNo':$('#TextFacturaNo').val(),
		'DCMod':$('#DCMod').val(),
		'Reprocesar':$('#Reprocesar').val(),
		'Cliente':$('#DCCliente').val(),
	}
		$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?Grabar_Factura_Actual=true&'+FA,
			  data: {parametros:parametros}, 
			  dataType:'json',
			  success: function(data)
			  {
			  	console.log(data);
			  	if(data.res==-2)
			  	{
			  		alerta_reprocesar('ADVERTENCIA',data.men);
			  	}else if(data.res==-3)
			  	{
			  		alerta_reprocesar('Formulario de Confirmación',data.men);
			  	}
			  	
			  }
			})
	}

function boton2()
{	
  DCBodega();
  DCMarca();
  autocomplete_producto();
	
}
function boton3()
{
	Listar_Ordenes();	
}
function boton4()
{
	$('#myModal_guia').modal('show');
	DCCiudadI();
	DCCiudadF();
	AdoPersonas();
	DCEmpresaEntrega();
	
}
function boton5()
{
	$('#myModal_suscripcion').modal('show');
	$('#LblClienteCod').val($('#LabelCodigo').val());
	$('#LblCliente').val($('#DCCliente option:selected').text());
	delete_asientoP();
	DGSuscripcion();
	DCCtaVenta();
	DCEjecutivoModal();
	
}
function boton6()
{




	// src ="../vista/modales.php?FAbonos=true";
	// $('#frame').attr('src',src).show();
	// $('#myModal_Abonos').modal('show');

	// $.ajax({
	// 		  type: "POST",
	// 		  url: '../controlador/facturacion/facturarC.php?imprimir=true',
	// 		  // data: {parametros:parametros }, 
	// 		  dataType:'json',
	// 		  success: function(data)
	// 		  {
	// 		  	if(data.length>0)
	// 		  	{

	// 		  		//llena un alista
	// 		  	}else
	// 		  	{
	// 		  		Swal.fire('No existe Ordenes para procesar','','info');
	// 		  	}
			  	
	// 		  }
	// 		})



}
//---------------Listar_Ordenes()------------
function Listar_Ordenes()
{
			$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?Listar_Ordenes=true',
			  // data: {parametros:parametros }, 
			  dataType:'json',
			  success: function(data)
			  {
			  	if(data.length>0)
			  	{

			  		//llena un alista
			  	}else
			  	{
			  		Swal.fire('No existe Ordenes para procesar','','info');
			  	}
			  	
			  }
			})

}

//---------------fin Listar_Ordenes()--------
//------------------ guia-------------

function DCCiudadI()
	{
    $('#DCCiudadI').select2({
      placeholder: 'Seleccione un cliente',
      ajax: {
       url: '../controlador/facturacion/facturarC.php?DCCiudadI=true',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
          return {
            results: data
          };
        },
        cache: true
      }
    });
	}
	function DCCiudadF()
	{
    $('#DCCiudadF').select2({
      placeholder: 'Seleccione un cliente',
      ajax: {
       url: '../controlador/facturacion/facturarC.php?DCCiudadF=true',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
          return {
            results: data
          };
        },
        cache: true
      }
    });
	}

	function AdoPersonas()
	{
    $('#DCRazonSocial').select2({
      placeholder: 'Seleccione un cliente',
      ajax: {
       url: '../controlador/facturacion/facturarC.php?AdoPersonas=true',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
          return {
            results: data
          };
        },
        cache: true
      }
    });
	}
	function DCEmpresaEntrega()
	{
    $('#DCEmpresaEntrega').select2({
      placeholder: 'Seleccione un cliente',
      ajax: {
       url: '../controlador/facturacion/facturarC.php?DCEmpresaEntrega=true',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
          return {
            results: data
          };
        },
        cache: true
      }
    });
	}
	function MBoxFechaGRE_LostFocus()
	{
		 var parametros = {
  	  	'MBoxFechaGRE':$('#MBoxFechaGRE').val(),  	  
  	  }
			$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?MBoxFechaGRE_LostFocus=true',
			  data: {parametros:parametros }, 
			  dataType:'json',
			  success: function(data)
			  {
			  	if(data.length>0)
			  	{
			  		llenarComboList(data,'DCSerieGR');
			  	}
			  	
			  }
			})
	} 

	function DCSerieGR_LostFocus()
	{
		var DCserie = $('#DCSerieGR').val();
		serie = DCserie.split('_');
		 var parametros = {
  	  	'DCSerieGR':serie[1],
  	  	'MBoxFechaGRE':$('#MBoxFechaGRE').val(),  	  	  
  	  }
			$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?DCSerieGR_LostFocus=true',
			  data: {parametros:parametros }, 
			  dataType:'json',
			  success: function(data)
			  {
			  	if(data.length>0)
			  	{
			  		llenarComboList(data,'DCSerieGR');
			  	}
			  	
			  }
			})

	}




//---------------------fin de guia-------------
//--------------sucripcion--------------
  function DGSuscripcion()
  {
	 		$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?DGSuscripcion=true',
			  // data: {parametros:parametros }, 
			  dataType:'json',
			  beforeSend: function () {	$('#tbl_suscripcion').html('<img src="../../img/gif/loader4.1.gif" width="40%"> ');}, 	
			  success: function(data)
			  {
			  	$('#tbl_suscripcion').html(data);
			  	// console.log(data);
			  }
			})
  }

	function DCCtaVenta()
	{
	 		$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?DCCtaVenta=true',
			  // data: {parametros:parametros }, 
			  dataType:'json',
			  success: function(data)
			  {
			  	// console.log(data);
			  	llenarComboList(data,'DCCtaVenta');
			  }
			})
	}

	function DCEjecutivoModal()
	{
    $('#DCEjecutivoModal').select2({
      placeholder: 'Seleccione un cliente',
      ajax: {
       url: '../controlador/facturacion/facturarC.php?DCEjecutivoModal=true',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
          return {
            results: data
          };
        },
        cache: true
      }
    });
	}

	function TextComision_LostFocus()
	{
		var datos = $('#form_suscripcion').serialize();
		$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?TextComision_LostFocus=true',
			  data: datos, 
			  dataType:'json',
			  success: function(data)
			  {
			  	$('#txtperiodo').val(data);
			  	DGSuscripcion();
			  	// console.log(data);
			  }
			})
	}

	function Command1()
	{
		var datos = $('#form_suscripcion').serialize();
		$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?Command1=true',
			  data: datos, 
			  dataType:'json',
			  success: function(data)
			  {
			  	$('#myModal_suscripcion').modal('hide');
			  	// delete_asientoP();
			  }
			})
	}
	function delete_asientoP()
	{
		$('#TextContrato').val('.');
		$('#TextSector').val('.');
		$('#TxtHasta').val('0.00');
		$('#TextTipo').val('.');
		$('#TextFact').val('0.00');
		$('#TextValor').val('0.00');

		$.ajax({
			  type: "POST",
			  url: '../controlador/facturacion/facturarC.php?delete_asientoP=true',
			  // data: datos, 
			  dataType:'json',
			  success: function(data)
			  {
			  	// DGSuscripcion();
			  	// console.log(data);
			  }
			})
	}
//------------------fin de suscripcion------------------

</script>

  <div class="row">
    <div class="col-lg-7 col-sm-10 col-md-6 col-xs-12">
       <div class="col-xs-2 col-md-2 col-sm-2 col-lg-1">
            <a  href="<?php $ruta = explode('&' ,$_SERVER['REQUEST_URI']); print_r($ruta[0].'#');?>" title="Salir de modulo" class="btn btn-default">
              <img src="../../img/png/salire.png">
            </a>
        </div>
        <div class="col-xs-2 col-md-2 col-sm-2 col-lg-1">
            <button type="button" class="btn btn-default" title="Grabar factura" onclick="boton1()"><img src="../../img/png/grabar.png"></button>
        </div>
        <div class="col-xs-2 col-md-2 col-sm-2 col-lg-1">
            <button type="button" class="btn btn-default" title="Actualizar Productos, Marcas y Bodegas" onclick="boton2()"><img src="../../img/png/update.png"></button>
        </div>
        <div class="col-xs-2 col-md-2 col-sm-2 col-lg-1">
            <button type="button" class="btn btn-default" title="Asignar orden de trabajo" onclick="boton3()"><img src="../../img/png/taskboard.png"></button>
        </div>
        <div class="col-xs-2 col-md-2 col-sm-2 col-lg-1">
            <button type="button" class="btn btn-default" title="Asignar guia de remision" onclick="boton4()"><img src="../../img/png/ats.png"></button>
        </div>
        <div class="col-xs-2 col-md-2 col-sm-2 col-lg-1">
            <button type="button" class="btn btn-default" title="Asignar suscripcion / contrato" onclick="boton5()"><img src="../../img/png/file2.png"></button>
        </div>
        <div class="col-xs-2 col-md-2 col-sm-2 col-lg-1">
            <button type="button" class="btn btn-default" title="Asignar reserva" onclick="boton6()"><img src="../../img/png/archivero2.png"></button>
        </div>
         <!-- <div class="col-xs-2 col-md-2 col-sm-2 col-lg-1">
            <a href="#" class="btn btn-default" title="Asignar reserva" onclick="Autorizar_Factura_Actual2();" target="_blank" ><img src="../../img/png/archivero2.png"></a>
        </div> -->
 </div>
</div>
<div class="row">
<div class="col-sm-12">	
	<!-- //valiable  -->
	<input type="hidden" name="Mod_PVP" id="Mod_PVP" value="0">
	<input type="hidden" name="DatInv_Serie_No" id="DatInv_Serie_No" value="">
	<input type="hidden" name="BanIVA" id="BanIVA">
	<input type="hidden" name="Reprocesar" id="Reprocesar" = value="0">

	<form id="FA" style="display:none;">

		<input type="text" name="TC" id="TC" value="">
		<input type="text" name="Cant_Item" id="Cant_Item">

		<input type="text" name="Autorizacion" id="Autorizacion">
		<input type="text" name="CantFact" id="CantFact">
		<!-- <input type="text" name="Cant_Item" id="Cant_Item"> -->
		<input type="text" name="Cod_CxC" id="Cod_CxC">
		<input type="text" name="Cta_CxP" id="Cta_CxP">
		<input type="text" name="Cta_CxP_Anterior" id="Cta_CxP_Anterior">
		<input type="text" name="Cta_Venta" id="Cta_Venta">
		<input type="text" name="CxC_Clientes" id="CxC_Clientes">

		<input type="text" name="DireccionEstab" id="DireccionEstab">	
		<input type="text" name="Fecha" id="Fecha" value="">
		<input type="text" name="Fecha_Aut" id="Fecha_Aut">
		<input type="text" name="Fecha_NC" id="Fecha_NC">
		<input type="text" name="Imp_Mes" id="Imp_Mes">


		<input type="text" name="NoFactura" id="NoFactura">
		<input type="text" name="NombreEstab" id="NombreEstab">
		<input type="text" name="Porc_IVA" id="Porc_IVA">
		<input type="text" name="Porc_Serv" id="Porc_Serv">
		<input type="text" name="Pos_Copia" id="Pos_Copia">
		<input type="text" name="Pos_Factura" id="Pos_Factura">
		<input type="text" name="Serie" id="Serie">
		<input type="text" name="TelefonoEstab" id="TelefonoEstab">
		<input type="text" name="Vencimiento" id="Vencimiento">

		<!-- guia -->
		<input type="text" name="ClaveAcceso_GR" id="ClaveAcceso_GR" value="">
		<input type="text" name="Autorizacion_GR" id="Autorizacion_GR" value="">
		<input type="text" name="Serie_GR" id="Serie_GR" value="">
		<input type="text" name="Remision" id="Remision" value="">
		<input type="text" name="FechaGRE" id="FechaGRE" value="">
		<input type="text" name="FechaGRI" id="FechaGRI" value="">
		<input type="text" name="FechaGRF" id="FechaGRF" value="">
		<input type="text" name="Placa_Vehiculo" id="Placa_Vehiculo" value="">
		<input type="text" name="Lugar_Entrega" id="Lugar_Entrega" value="">
		<input type="text" name="Zona" id="Zona" value="">
		<input type="text" name="CiudadGRI" id="CiudadGRI" value="">
		<input type="text" name="CiudadGRF" id="CiudadGRF" value="">
		<input type="text" name="Comercial" id="Comercial" value="">
		<input type="text" name="CIRUCComercial" id="CIRUCComercial" value="">
		<input type="text" name="Entrega" id="Entrega" value="">
		<input type="text" name="CIRUCEntrega" id="CIRUCEntrega" value="">
		<input type="text" name="Dir_EntregaGR" id="Dir_EntregaGR" value="">
		<!-- fin guia -->
	</form>


	<!-- //fin de variables -->
	<input type="hidden" name="TipoFactura" id="TipoFactura">
	<div class="row">
		<div class="col-sm-2">
			<label><input type="checkbox" name="Check1" id="Check1"> Factura en MN</label>
		</div>
		<div class="col-sm-2" id="CheqSPFrom" style="display: none;">
			<label><input type="checkbox" name="CheqSP" id="CheqSP"> Sector publico</label>
		</div>
		<div class="col-sm-4">
			<b class="col-sm-4 control-label" style="padding: 0px">Orden Compra No</b>
			<div class="col-sm-8">
				<input type="" name="TxtCompra" id="TxtCompra" class="form-control input-xs" value="0">
			</div>
		</div>
		<div class="col-sm-4">
			<select class="form-control input-xs" id="DCMod" name="DCMod">
					<option value="">Seleccione</option>
				</select>
		</div>
		<div class="col-sm-2">
			<input type="text" name="LabelCodigo" id="LabelCodigo" class="form-control input-xs" readonly="" value=".">
		</div>		
	</div>
	<div class="row">		
		<div class="col-sm-4">
			<b class="col-sm-4 control-label" style="padding: 0px">Cuenta x Cobrar</b>
			<div class="col-sm-8" style="padding: 0px">
				<select class="form-control input-xs" id="DCLineas" name="DCLineas" onchange="DCLinea_LostFocus()">
					<option value="">Seleccione</option>
				</select>

				<input type="hidden" name="DCLineasV" id="DCLineasV">
			</div>			
		</div>
		<div class="col-sm-4">
			<div class="row">
				<div class="col-sm-9" style="padding-right: 0px;">
					<b style="color:red" id="label2">0000000000000 NOTA DE VENTA No. 001001-</b>					
				</div>
				<div class="col-sm-3" style="padding-left: 0px;">
					<input type="text" name="TextFacturaNo" id="TextFacturaNo" class="form-control input-xs" value="0">	
				</div>				
			</div>
		</div>
		<div class="col-sm-4">
			<b class="col-sm-4 control-label" style="padding: 0px">Saldo pendiente</b>
			<div class="col-sm-6">
				<input type="text" name="LblSaldo" id="LblSaldo" class="form-control input-xs" value="0.00">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-3">
			<b class="col-sm-5 control-label" style="padding: 0px">Fecha Emision</b>
			<div class="col-sm-7" style="padding: 0px">
				<input type="date" name="MBoxFecha" id="MBoxFecha" class="form-control input-xs" value="<?php echo date('Y-m-d'); ?>">
			</div>
		</div>
		<div class="col-sm-4">
			<b class="col-sm-6 control-label" style="padding: 0px">Fecha Vencimiento</b>
			<div class="col-sm-6" style="padding: 0px">
				<input type="date" name="MBoxFechaV" id="MBoxFechaV" class="form-control input-xs">
			</div>
		</div>
		<div class="col-sm-5">
			<b class="col-sm-3 control-label" style="padding: 0px">Tipo de pago</b>
			<div class="col-sm-8">
				<select class="form-control input-xs" id="DCTipoPago" name="DCTipoPago">
					<option value="">Seleccione</option>
				</select>				
			</div>			
		</div>
	</div>	
	<div class="row">
		<div class="col-sm-2">
			<b>Grupo</b>
			<select class="form-control input-xs" id="DCGrupo_No" name="DCGrupo_No" onchange="autocomplete_cliente()">
				<option value="">Seleccione</option>
			</select>
		</div>
		<div class="col-sm-3">
			<b>Cliente</b>
			<div class="input-group">
	      <select class="form-control input-xs" id="DCCliente" name="DCCliente">
					<option value="">Seleccione</option>
				</select>
	          <span class="input-group-btn">
	            <button type="button" class="btn btn-info btn-flat btn-xs" onclick="addCliente();"><i class="fa fa-plus"></i></button>
	          </span>
	    </div>
		</div>
		<div class="col-sm-2">
			<b id="Label13">C.I / R.U.C</b>
			<input type="text" name="LabelRUC" id="LabelRUC" class="form-control input-xs" readonly="" value=".">
		</div>
		<div class="col-sm-2">
			<b>Telefono</b>
			<input type="text" name="LabelTelefono" id="LabelTelefono" class="form-control input-xs" readonly="" value=".">
		</div>
		<div class="col-sm-3">
			<b>ACTUALICE SU CORREO ELECTRONICO</b>
			<input type="text" name="TxtEmail" id="TxtEmail" class="form-control input-xs">
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<b>Direccion</b>
			<input type="text" name="Label24" id="Label24" class="form-control input-xs" value="" readonly="">			
		</div>
		<div class="col-sm-4">
			<b>No</b>
			<input type="text" name="Label21" id="Label21" class="form-control input-xs" value="" readonly="">			
		</div>
		<div class="col-sm-4"> <br>
			<select class="form-control input-xs" id="DCMedico" name="DCMedico">
					<option value="">Seleccione</option>
				</select>		
		</div>		
	</div>
	<div class="row">
		<div class="col-sm-6" >
			<div id="DCEjecutivoFrom">
			<b class="col-sm-3 control-label" style="padding: 0px"><input type="checkbox" name=""> Ejecutivo de venta</b>
			<div class="col-sm-9">
				<select class="form-control input-xs" name="DCEjecutivo" id="DCEjecutivo">
					<option value="">Seleccione</option>
				</select>
			</div>
			</div>
		</div>
		<div class="col-sm-2">
			<div id="TextComisionForm" style="display:none;">
			<b class="col-sm-4 control-label" style="padding: 0px">comision%</b>
			<div class="col-sm-8">
				<input type="text" name="TextComision" id="TextComision" value="0" class="form-control input-xs">
			</div>
			</div>			
		</div>
		<div class="col-sm-4">
			<b class="col-sm-2 control-label" style="padding: 0px">Bodega</b>
			<div class="col-sm-9">
				<select class="form-control input-xs" name="DCBodega" id="DCBodega">
					<option value="">Seleccione</option>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<b>Observacion</b>
			<input type="text" name="TextObs" id="TextObs" class="form-control input-xs">
		</div>
		<div class="col-sm-6">
			<b>Nota</b>
			<input type="text" name="TextNota" id="TextNota" class="form-control input-xs">
		</div>
	</div>	
	<div class="row">
		<div class="col-sm-2">
			<b>Marca</b>
			<select class="form-control input-xs" id="DCMarca" name="DCMarca">
				<option value="">Seleccione</option>
			</select>
		</div>
		<div class="col-sm-4">
			<b id="LabelStockArt">Producto</b>
			<select class="form-control input-xs" name="DCArticulos" id="DCArticulos" onchange="DCArticulo_LostFocus()">
				<option value="">Seleccione</option>
			</select>
		</div>
		<div class="col-sm-1">
			<b>Stock</b>
			<input type="text" name="LabelStock" id="LabelStock" class="form-control input-xs" readonly="">
		</div>
		<div class="col-sm-1">
			<b>Ord./lote</b>
			<input type="text" name="TextComEjec" id="TextComEjec" class="form-control input-xs">
		</div>
		<div class="col-sm-1">
			<b>Desc%</b>
			<select class="form-control input-xs" id="CDesc1" name="CDesc1">
				<option value="">Seleccione</option>
			</select>
		</div>
		<div class="col-sm-1">
			<b>Cantidad</b>
			<input type="text" name="TextCant" id="TextCant" class="form-control input-xs" onblur="TextCant_Change()" value="0">
		</div>
		<div class="col-sm-1">
			<b>P.V.P</b>
			<input type="text" name="TextVUnit" id="TextVUnit" class="form-control input-xs" onblur="TextVUnit_LostFocus()" value="0">
		</div> 
		<div class="col-sm-1">
			<b>TOTAL</b>
			<input type="text" name="LabelVTotal" id="LabelVTotal" class="form-control input-xs" readonly="" value="0"> 
		</div>
	</div>
	<div class="row"><br>
		<div class="col-sm-12 text-center">
			<div id="tbl">				
        	
			</div>
			
		</div>		
	</div>
	<div class="row">
		<div class="col-sm-1" style="padding: 2px;">
			<b>Total sin Iva</b>
			<input type="text" name="LabelSubTotal" id="LabelSubTotal" class="form-control input-xs">
		</div>
		<div class="col-sm-1" style="padding: 2px;">
			<b>Total con IVA</b>
			<input type="text" name="LabelConIVA" id="LabelConIVA" class="form-control input-xs">
		</div>
		<div class="col-sm-1" style="padding: 2px;">
			<b>Total Desc</b>
			<input type="text" name="TextDesc" id="TextDesc" class="form-control input-xs">
		</div>
		<div class="col-sm-1" style="padding: 2px;">
			<b id="label36">Serv. 0.00%</b>
			<input type="text" name="LabelServ" id="LabelServ" class="form-control input-xs">
		</div>
		<div class="col-sm-1" style="padding: 2px;">
			<b id="label3">I.V.A 12.00%</b>
			<input type="text" name="LabelIVA" id="LabelIVA" class="form-control input-xs">
		</div>
		<div class="col-sm-2" style="padding: 2px;">
			<b>Total Facturado</b>
			<input type="text" name="LabelTotal" id="LabelTotal" class="form-control input-xs">
		</div>
		<div class="col-sm-5">
			<!-- <b>P.V.P</b> --> 
			<br>
			<input type="text" name="LblGuia" id="LblGuia" class="form-control input-xs" readonly>
		</div>
	</div>
  
</div>
</div>

 <div class="modal fade" id="cambiar_nombre" role="dialog" data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog modal-dialog-centered modal-sm" style="margin-left: 300px; margin-top: 345px;">
      <div class="modal-content">
        <div class="modal-body text-center">
        	<textarea class="form-control" style="resize: none;" rows="4" id="TxtDetalle" name="TxtDetalle" onblur="cerrar_modal_cambio_nombre()"></textarea> 		
        </div>
      </div>
    </div>
  </div>

  <!-- Modal cliente nuevo -->
<div id="myModal_guia" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 30%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Datos de guia de remision</h4>
      </div>
      <div class="modal-body">
      	<div class="row">						
						<div class="col-sm-12">
							<b class="col-sm-6 control-label" style="padding: 0px">Fecha de emision de guia</b>
							<div class="col-sm-6" style="padding: 0px">
								<input type="date" name="MBoxFechaGRE" id="MBoxFechaGRE" class="form-control input-xs" value="<?php echo date('Y-m-d'); ?>" onblur="MBoxFechaGRE_LostFocus()">
							</div>
						</div>
						<div class="col-sm-12">
							<b class="col-sm-6 control-label" style="padding: 0px">Guia de remision No.</b>
							<div class="col-sm-3" style="padding: 0px">
								<select class="form-control input-xs" id="DCSerieGR" name="DCSerieGR" onchange="DCSerieGR_LostFocus()">
									<option value="">No Existe</option>
								</select>
							</div>
							<div class="col-sm-3" style="padding: 0px">
								<input type="text" name="LblGuiaR" id="LblGuiaR" class="form-control input-xs" value="000000">
							</div>
						</div>
						<div class="col-sm-12">
							<b>AUTORIZACION GUIA DE REMISION</b>
							<input type="text" name="LblAutGuiaRem" id="LblAutGuiaRem" class="form-control input-xs" value="0">							
						</div>
						<div class="col-sm-12">
							<b class="col-sm-6 control-label" style="padding: 0px">Iniciacion del traslados</b>
							<div class="col-sm-6" style="padding: 0px">		
								<input type="date" name="MBoxFechaGRI" id="MBoxFechaGRI" class="form-control input-xs" value="<?php echo date('Y-m-d'); ?>">
							</div>			
						</div>
						<div class="col-sm-12">
							<b class="col-sm-3 control-label" style="padding: 0px">Ciudad</b>
							<div class="col-sm-9" style="padding: 0px">		
								<select class="form-control input-xs" id="DCCiudadI" name="DCCiudadI">
									<option value=""></option>
								</select>
							</div>			
						</div>
						<div class="col-sm-12">
							<b class="col-sm-6 control-label" style="padding: 0px">Finalizacion del traslados</b>
							<div class="col-sm-6" style="padding: 0px">		
								<input type="date" name="MBoxFechaGRF" id="MBoxFechaGRF" class="form-control input-xs" value="<?php echo date('Y-m-d'); ?>">
							</div>			
						</div>
						<div class="col-sm-12">
							<b class="col-sm-3 control-label" style="padding: 0px">ciudad</b>
							<div class="col-sm-9" style="padding: 0px">		
								<select class="form-control input-xs" id="DCCiudadF" name="DCCiudadF">
									<option value=""></option>
								</select>
							</div>			
						</div>
						<div class="col-sm-12">
							<b>Nombre o razon socila (Transportista)</b>
								<select class="form-control input-xs" id="DCRazonSocial" name="DCRazonSocial">
									<option value=""></option>
								</select>				
						</div>
						<div class="col-sm-12">
							<b>Empresa de Transporte</b>
							<select class="form-control input-xs" id="DCEmpresaEntrega" name="DCEmpresaEntrega">
									<option value=""></option>
								</select>			
						</div>
						<div class="col-sm-4">
							<b>Placa</b>
							<input type="text" name="TxtPlaca" id="TxtPlaca" class="form-control input-xs" value="XXX-999">							
						</div>
						<div class="col-sm-4">
							<b>Pedido</b>
							<input type="text" name="TxtPedido" id="TxtPedido" class="form-control input-xs">							
						</div>
						<div class="col-sm-4">
							<b>Zona</b>
							<input type="text" name="TxtZona" id="TxtZona" class="form-control input-xs">							
						</div>
						<div class="col-sm-12">
							<b>Lugar entrega</b>
							<input type="text" name="TxtLugarEntrega" id="TxtLugarEntrega" class="form-control input-xs">							
						</div>
					</div>	


      </div>
      <div class="modal-footer">
      	<button class="btn btn-primary"onclick="Command8_Click();">Aceptar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
	function Command8_Click()
	{
		if($('#DCCiudadI').val()=='' || $('#DCCiudadF').val()=='' || $('#DCRazonSocial').val()=='' || $('#DCEmpresaEntrega').val()=='')
		{
			swal.fire('Llene todo lso campos','','info');
			return false;
		}
		$('#ClaveAcceso_GR').val('.');
    $('#Autorizacion_GR').val($('#LblAutGuiaRem').val());
    var DCserie = $('#DCSerieGR').val();
    if(DCserie==''){DCserie = '0_0'; }
		var serie = DCserie.split('_');
    $('#Serie_GR').val(serie[1]);
    $('#Remision').val($('#LblGuiaR').val());
    $('#FechaGRE').val($('#MBoxFechaGRE').val());
    $('#FechaGRI').val($('#MBoxFechaGRI').val());
    $('#FechaGRF').val($('#MBoxFechaGRF').val());
    $('#Placa_Vehiculo').val($('#TxtPlaca').val());
    $('#Lugar_Entrega').val($('#TxtLugarEntrega').val());
    $('#Zona').val($('#TxtZona').val());
    $('#CiudadGRI').val($('#DCCiudadI option:selected').text());
    $('#CiudadGRF').val($('#DCCiudadF option:selected').text());

    var nom = $('#DCRazonSocial').val(); 
    ci = nom.split('_');    
     $('#Comercial').val($('#DCRazonSocial option:selected').text());
     $('#CIRUCComercial').val(ci[0]);
    var nom1 = $('#DCEmpresaEntrega').val(); 
    ci1 = nom1.split('_');    
    $('#Entrega').val($('#DCEmpresaEntrega option:selected').text());
    $('#CIRUCEntrega').val(ci1[0]);
    $('#Dir_EntregaGR').val(ci1[1]);
    sms = "Guia de Remision: "+serie[1]+"-"+$('#LblGuiaR').val()+"  Autorizacion: "+$('#LblAutGuiaRem').val(); 
    $('#LblGuia').val(sms);
    $('#myModal_guia').modal('hide');

	}
</script>

<div id="myModal_suscripcion" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 55%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">FORMULARIO DE SUSCRIPCION</h4>
      </div>
      <div class="modal-body">
      	<div class="row">	
	      	<div class="col-sm-10">
	      		<form id="form_suscripcion">
	      			<input type="hidden" name="LblClienteCod" id="LblClienteCod">
	      		<div class="row">
	      			<div class="col-sm-12">
	      				<input type="text" name="LblCliente" id="LblCliente" class="form-control input-xs" readonly>
	      				<select class="form-control input-xs" id="DCCtaVenta" name="DCCtaVenta">
									<option value="">Seleccione</option>
								</select>			
	      			</div>
	      		</div>
	      		<div class="row">
	      			<div class="col-sm-8">
	      				<div class="row">
	      					<div class="col-sm-7">
	      						<b>Periodo</b>
	      						<div class="row">
	      							<div class="col-sm-6" style="padding-right: 1px;">
	      								 <input type="date" name="MBDesde"  id="MBDesde" class="form-control input-xs" style="font-size: 10.5px; padding: 2px;" value="<?php echo date('Y-m-d') ?>">
	      							</div>
	      							<div class="col-sm-6" style="padding-left: 1px;">
	      								<input type="date" name="MBHasta"  id="MBHasta" class="form-control input-xs"  style="font-size: 10.5px; padding: 2px;" value="<?php echo date('Y-m-d') ?>">	      							      								
	      							</div>
	      						</div>	      						
	      					</div>
	      					<div class="col-sm-3" style="padding: 1px;">
	      						<b>Contrato No.</b>
	      						<input type="text" name="TextContrato"  id="TextContrato" class="form-control input-xs" value=".">
	      					</div>
	      					<div class="col-sm-2" style="padding-left: 1px; padding-top: 1px;">
	      						<b>Sector</b>
	      						<input type="text" name="TextSector"  id="TextSector" class="form-control input-xs" value=".">
	      					</div>
	      				</div>
	      				<div class="row">
	      					<div class="col-sm-3" style="padding-right: 1px; padding-top: 1px;">
	      						<b>Ent. hasta</b>
	      						<input type="text" name="TxtHasta" id="TxtHasta" class="form-control input-xs" value="0.00">	      						
	      					</div>	 
	      					<div class="col-sm-3" style="padding: 1px;">
	      						<b>Tipo</b>
	      						<input type="text" name="TextTipo" id="TextTipo" class="form-control input-xs" value=".">	      						
	      					</div>	 
	      					<div class="col-sm-3" style="padding: 1px;">
	      						<b>Comp. Venta</b>
	      						<input type="text" name="TextFact" id="TextFact" class="form-control input-xs" value="0.00">	      						
	      					</div>	 
	      					<div class="col-sm-3" style="padding-left: 1px;  padding-top: 1px;">
	      						<b>Valor suscr</b>
	      						<input type="text" name="TextValor" id="TextValor" class="form-control input-xs" value="0.00">	      						
	      					</div>				
	      				</div>
	      				<div class="row">
	      					<div class="col-sm-12">
	      						<b> Atención /Entregar a:</b>
	      						<input type="text" name="TxtAtencion" id="TxtAtencion" class="form-control input-xs">	  	      						
	      					</div>	      					
	      				</div>	      				
	      			</div>
	      			<div class="col-sm-4">
	      				<div class="row">	      					
	      					<div class="col-sm-6" style="padding: 0px;">	      						
                    <div class="checkbox">
                      <label style="padding: 0px;">
                        <input type="radio" name="opc" value="OpcMensual" id="OpcMensual" checked> Mensual
                      </label>
                    </div>
	      					</div>
	      					<div class="col-sm-6" style="padding: 0px;">	      						
                    <div class="checkbox">
                      <label style="padding: 0px;">
                        <input type="radio" name="opc" value="OpcAnual" id="OpcAnual"> Anual
                      </label>
                    </div>
	      					</div>
	      				</div>
	      				<div class="row">	      					
	      					<div class="col-sm-6" style="padding: 0px;">	      						
                    <div class="checkbox">
                      <label style="padding: 0px;">
                        <input type="radio" name="opc" value="OpcQuincenal" id="OpcQuincenal">Quincenal
                      </label>
                    </div>
	      					</div>
	      					<div class="col-sm-6" style="padding: 0px;">	      						
                    <div class="checkbox">
                      <label style="padding: 0px;">
                        <input type="radio" name="opc" value="OpcTrimestral" id="OpcTrimestral"> Trimestral
                      </label>
                    </div>
	      					</div>
	      				</div>
	      				<div class="row">	      					
	      					<div class="col-sm-6" style="padding: 0px;">	      						
                    <div class="checkbox">
                      <label style="padding: 0px;">
                        <input type="radio" name="opc" value="OpcSemanal" id="OpcSemanal"> Semanal
                      </label>
                    </div>
	      					</div>
	      					<div class="col-sm-6" style="padding: 0px;">	      						
                    <div class="checkbox">
                      <label style="padding: 0px;">
                        <input type="radio" name="opc" value="OpcSemestral" id="OpcSemestral"> Semestral
                      </label>
                    </div>
	      					</div>
	      				</div>	      				
	      				<!-- checks -->
	      			</div>
	      			<div class="col-sm-12">
	      				<div class="row">
		      					<div class="col-sm-6">
		      						<b> Ejecutivo de Venta</b>
		      						<select class="form-control input-xs" id="DCEjecutivoModal" name="DCEjecutivoModal">
												<option value="">Seleccione</option>
											</select>	
		      					</div>
		      					<div class="col-sm-3">
		      						<b>Comision %</b>
		      						<input type="" name="TextComisionModal" id="TextComisionModal" class="form-control input-xs" onblur="TextComision_LostFocus()">      						
		      					</div>
		      					<div class="col-sm-3">
			      					<div class="checkbox">
		                      <label style="padding: 0px;">
		                        <input type="radio" name="opc2" value='OpcN' id="OpcN" checked> Nuevo
		                      </label>
		                    </div>
		                    <div class="checkbox">
		                      <label style="padding: 0px;">
		                        <input type="radio" name="opc2" value='OpcR' id="OpcR"> Renovacion
		                      </label>
		                    </div>			      					
			      				</div>

			      		</div>
	      
	      			</div>
	      		</div>
	      		<div class="row">
	      			<div class="col-sm-12 text-center" id="tbl_suscripcion" style="height:170px">
	      			</div>
	      			<br>
	      			<label>Periodo:<input type="texto" name="txtperiodo" id="txtperiodo"></label>
	      		</div>	
	      		</form>      		
	      	</div>
	      	<div class="col-sm-2">
	      		<div class="row">
	      			<div class="col-sm-12">
	      				 <button class="btn btn-default" id="btn_g"> <img src="../../img/png/grabar.png"  onclick="Command1();"><br> Guardar</button>
	      			</div>	
	      			<div class="col-sm-12">
	      				  <button class="btn btn-default" data-dismiss="modal" onclick="delete_asientoP();"><img src="../../img/png/bloqueo.png" ><br> Cancelar</button>
	      			</div>	      			
	      		</div>
	      		
	      	</div>
				</div>	
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> -->
      </div>
    </div>

  </div>
</div>


<div id="myModal_Abonos" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="padding: 6px 0px 6px 15px;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">INGRESO DE CAJA</h4>
      </div>
      <div class="modal-body">
      	<iframe src="" id="frame" width="100%" height="560px" marginheight="0" frameborder="0"></iframe>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> -->
      </div>
    </div>

  </div>
</div>



 <!-- Fin Modal cliente nuevo-->
