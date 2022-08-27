<div class="modal inmodal fade" id="myModalDistrict" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
			  <form class="formValidate" action="<?php echo base_url();?>District/Create" id="formDistrict" method="post">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">District Form</h4>
					<small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
				</div>
				<div class="modal-body">
			             
						 <div class="form-group row">
								<label class="col-lg-2 col-form-label">Country<span class="text-danger">*</span></label>
								<div class="col-lg-4">						
									<select class="form-control" name="country" type="text">
										<option value="USA">USA</option>
										<option value="Uganda">Uganda</option>
									</select>
								</div>
                        
								<label class="col-lg-2 col-form-label">District<span class="text-danger">*</span></label>
								<div class="col-lg-4">						
									<input class="form-control" name="district" type="text">
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

