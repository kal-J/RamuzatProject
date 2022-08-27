<div id="tab-document" class="tab-pane biodata">
   <div class="pull-right add-record-btn">
   <?php if(in_array('1', $member_staff_privilege)){ ?>
	 <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_document-modal"><i class="fa fa-edit"></i> Add Document </button>
   <?php } ?>
	<?php  $this->load->view('user/document/document_modal'); ?>
	 </div>
	<div class="table-responsive">
	<table id="tblDocument" class="table table-striped  table-hover"  width="100%">
	   <thead>
		<tr>
			<th>&nbsp;</th> 
			<th>Type</th>
			<th>Description</th>
			<th>Action</th>   
			<th>&nbsp;</th>   
		</tr>
		</thead>
		<tbody>
	 
		</tbody>
		</table>
		
	</div>
 </div><!-- ==END TAB-document =====-->
