<div id="tab-password" class="tab-pane biodata" style="margin-top:100px;" >
 <div class="form-group row" >
	<div class="col-lg-3">
	</div>
	<div class="col-lg-6">
	 <!-- ko with: user -->
	    <div class="panel panel-default"  data-bind="visible: password===null">
		 <div class="panel-heading text-center"><h4 class="text-danger">Password Not Set !</h4></div>
			<div class="panel-body">
					<button class="btn btn-block btn-success btn-md" type="button" data-toggle="modal" data-target="#password-modal"><i class="fa fa-edit"></i> Set Password </button>
			</div>
		</div>
		<div class="panel panel-default"  data-bind="visible: password!==null">
		 <div class="panel-heading text-center"><h4 class="text-green"><i class="fa fa-check"></i> Password already Set </h4></div>
			<div class="panel-body">
					<button class="btn btn-block btn-primary btn-md" type="button" data-toggle="modal" data-target="#password-modal"><i class="fa fa-edit"></i> Reset Password</button>
			</div>
		</div>
    <!--/ko -->
	<?php $this->load->view('client/profile/password/password_modal'); ?>
		</div>
		<div class="col-lg-3">
	</div>
	</div>
</div>
