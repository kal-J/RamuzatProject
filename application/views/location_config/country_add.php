<div class="modal inmodal fade" id="myModalCountry" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
			  <form class="formValidate" action="<?php echo base_url();?>Country/Create" id="formCountry" method="post">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">Address Form</h4>
					<small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
				</div>
				<div class="modal-body">
			             
                          <div class="form-group row">
								<label class="col-lg-12 col-form-label">Country<span class="text-danger">*</span></label>
								<div class="col-lg-12">						
									<input class="form-control" name="country" type="text">
								</div>
                          </div>
				</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-primary btn-flat" value="Save Member"></button>
			</div>
			  </form>
		</div>
	</div>
</div>

