
<?php
include "../funciones.php";
?>
<div class="modal fade" id="modal_consultar_nit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='fa fa-edit'></i> Datos de certificación:</h4>
            </div>
            <div class="modal-body" >
                    <form id="form_consultar_nit" name="form_consultar_nit">
                     <div class="form-group">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="rnc" name="rnc" placeholder="NIT del cliente">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-info waves-effect waves-light" onclick='buscar_nit();'>
                                                <span class="fa fa-search"></span> Buscar</button>
                                            </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <span id="loader"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput2">Nombre:</label>
                            <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" placeholder="Nombre del cliente">
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput2">Direccion:</label>
                            <input type="text" class="form-control" id="direccion_cliente" name="direccion_cliente" placeholder="Direccion del cliente">
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput2">Telefono:</label>
                            <input type="text" class="form-control" id="telefono_cliente" name="telefono_cliente" placeholder="Telefono del cliente">
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput2">Correo:</label>
                            <input type="text" class="form-control" id="correo_cliente" name="correo_cliente" placeholder="Correo del cliente">
                        </div>
                        <input type="hidden" class="form-control" id="pageActual" name="pageActual" placeholder="pageActual">
                
                        <input type="hidden" class="form-control" id="idfactura" name="idfactura" placeholder="IDFACTURA">
                        <button class="btn btn-primary" type="submit">Facturar</button>
                    </form>
            </div>
        </div>        
    </div>
</div>