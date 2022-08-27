		<!-- Deposit Product Fees -->
			   <div class="modal inmodal fade" id="add_interest_payment_method_modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">          
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title">Interest Payment Point Form</h4>
							<small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
						</div>
						<form class="formValidate" id="formInterest_Payment_points" action="Interest_Payment_points/create">
									<div class="modal-body ">
										<div class="row" >
											<input type="hidden" name="tbl"  value="tblInterest_Payment_points">
											<input type="hidden" name="id" id="id">
											
												<label class="col-lg-4 control-label">Fee Name<span class="text-danger">*</span></label>
												<div class="col-lg-8 form-group">
													<input type="text" name="interest_point_name" id="interest_point_name" placeholder="Payment Point" class="form-control" data-msg-required="Payment point is required" required /> 
												</div>
												<label class="col-lg-4 control-label">Description <span class="text-danger">*</span></label>
												<div class="col-lg-8 form-group">
													<input type="text" name="interest_point_description" id="interest_point_description" placeholder="description" class="form-control" data-msg-required="Description is required" required /> 
												</div>
										</div>
									
									</div>
						 
											<div class="modal-footer">
												<div class="pull-right">
													<button class="btn btn-sm btn-primary save" type="submit">Submit</button>
												</div>
											</div>
						</form>
				</div>
			</div>
		</div>
