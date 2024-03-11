		$(document).ready(function() {
		    load(1);
		});

		
		$('#dataDelete').on('show.bs.modal', function(event) {
		    var button = $(event.relatedTarget) // Botón que activó el modal
		    var id = button.data('id') // Extraer la información de atributos de datos
		    var modal = $(this)
		    modal.find('#id_factura').val(id)
		})
		$("#eliminarDatos").submit(function(event) {
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/eliminar_factura.php",
		        data: parametros,
		        beforeSend: function(objeto) {
		            $(".datos_ajax_delete").html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        },
		        success: function(datos) {
		            $(".datos_ajax_delete").html(datos);
		            $('#dataDelete').modal('hide');
		            //desaparecer la alerta
		            $(".alert-success").delay(400).show(10, function() {
		                $(this).delay(2000).hide(10, function() {
		                    $(this).remove();
		                });
		            }); // /.alert
		            load(1);
		        }
		    });
		    event.preventDefault();
		});

		function imprimir_factura(id_factura) {
			
			//alert("Descargar la factura de Corposistemas");
			//VentanaCentrada('../pdf/documentos/ver_factura.php?id_factura=' + id_factura, 'Factura', '', '724', '568', 'true');
			//VentanaCentrada('../pdf/documentos/ver_factura.php?id_factura=' + id_factura, 'Factura', '', '724', '568', 'true');

			/*$.ajax({
				
				type: 'POST',
				url: '../pdf/documentos/ver_factura.php?id_factura=' + id_factura,
				xhrFields: {
					responseType: 'blob'
				},
				data: {
					ajax: true,
				},
				success: function (json) {
					var a = document.createElement('a');
					var url = window.URL.createObjectURL(json);
					a.href = url;
					a.download = 'your_pdf_name.pdf';
					a.click();
					window.URL.revokeObjectURL(url);
					alert("success");
				},
				error: function() {
					console.log("Error");
				}
			});
			event.preventDefault();*/

			$.ajax({
				url: '../pdf/documentos/ver_factura.php?id_factura=' + id_factura,
				type: 'GET',
				dataType: 'text',
				success: function(response) {
					//alert("Hola peter"+response);


					var d = new Date();

					

					let date = new Date();
					//console.log('Date in India: ' + date);
					let formatter = new Intl.DateTimeFormat('en-US', options, { timeZone: "America/Guatemala" });  
					
					let usDate = formatter.format(date);
					let text = usDate.replace("/", "-");
					let textoFinal = text.replace(":", "-");
					/*var datestring = usDate.getDate()  + "-" + (usDate.getMonth()+1) + "-" + usDate.getFullYear() + " " +
					usDate.getHours() + "-" + usDate.getMinutes();
					console.log('Date in Guatemala: ' + datestring);*/
					var link = document.createElement('a');
					link.href = '../pdf/documentos/descargar_factura.php?id_factura='+id_factura;
					link.download = 'dte '+textoFinal+'.pdf';
					link.dispatchEvent(new MouseEvent('click'));
				} // /success function
			});

			


			/*$.ajax({
				url: '../pdf/documentos/descargar_factura.php',
				type: 'post',
				data: {
					id_factura: id_factura
				},
				dataType: 'text',
				success: function(response) {
					//alert("Hola peter"+response);
					var link = document.createElement('a');
					link.href = response;
					link.download = 'file.pdf';
					link.dispatchEvent(new MouseEvent('click'));
				} // /success function
			});*/




		}
		// print order function
		function printOrder(id_factura) {
		    if (id_factura) {
		        $.ajax({
		            url: '../pdf/documentos/imprimir_factura.php',
		            type: 'post',
		            data: {
		                id_factura: id_factura
		            },
		            dataType: 'text',
		            success: function(response) {
		                var mywindow = window.open('', 'Stock Management System', 'height=400,width=600');
		                mywindow.document.write('<html><head><title>Facturación</title>');
		                mywindow.document.write('</head><body>');
		                mywindow.document.write(response);
		                mywindow.document.write('</body></html>');
		                mywindow.document.close(); // necessary for IE >= 10
		                mywindow.focus(); // necessary for IE >= 10
		                mywindow.print();
		                mywindow.close();
		            } // /success function
		        }); // /ajax function to fetch the printable order
		    } // /if orderId
		} // /print order function
		// print order function
		function print_ticket(id_factura) {
		    if (id_factura) {
		        $.ajax({
		            url: '../pdf/documentos/imprimir_venta_edit.php',
		            type: 'post',
		            data: {
		                id_factura: id_factura
		            },
		            dataType: 'text',
		            success: function(response) {
		                var mywindow = window.open('', 'Stock Management System', 'height=400,width=600');
		                mywindow.document.write('<html><head><title>Facturación</title>');
		                mywindow.document.write('</head><body>');
		                mywindow.document.write(response);
		                mywindow.document.write('</body></html>');
		                mywindow.document.close(); // necessary for IE >= 10
		                mywindow.focus(); // necessary for IE >= 10
		                mywindow.print();
		                mywindow.close();
		            } // /success function
		        }); // /ajax function to fetch the printable order
		    } // /if orderId
		} // /print order function

		function anular_factura(id_factura) {
		    if (id_factura) {
		        $.ajax({
		            url: '../pdf/documentos/anular_factura.php',
		            type: 'post',
		            data: {
		                id_factura: id_factura
		            },
		            dataType: 'text',
		            success: function(response) {
		                var mywindow = window.open('', 'Stock Management System', 'height=400,width=600');
		                mywindow.document.write('<html><head><title>Facturación</title>');
		                mywindow.document.write('</head><body>');
		                mywindow.document.write(response);
		                mywindow.document.write('</body></html>');
		                mywindow.document.close(); // necessary for IE >= 10
		                mywindow.focus(); // necessary for IE >= 10
		                mywindow.print();
		                mywindow.close();
		            } // /success function
		        }); // /ajax function to fetch the printable order
		    } // /if orderId
		}
		