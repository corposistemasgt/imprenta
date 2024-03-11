<?php
if (isset($conexion)) {
    ?>
	<div id="mostrarQR" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
			
				<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title"><i class='fa fa-edit'></i> QR </h4>
				</div>

				<div class="modal-body">
					<div class="col-md-12">
						<div class="showQRCode">

						</div>	
					</div>
				</div>

			</div>
			

		</div>	
	</div><!-- /.modal -->
	<?php
}
?>