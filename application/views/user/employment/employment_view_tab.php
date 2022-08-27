<div id="tab-employment" class="tab-pane biodata">
    <div class="pull-right add-record-btn">
        <div class="panel-title">
        <?php if(in_array('1', $member_staff_privilege)){ ?>
            <a href="#add_employment-modal" data-toggle="modal"  class="btn btn-default btn-sm">
                <i class="fa fa-plus-circle"></i> Add</a>
        <?php }?>
        </div>
     <!-- <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_employment-modal"><i class="fa fa-edit"></i> Add an Employment record </button> -->
        <?php $this->load->view('user/employment/employment_modal'); ?>
    </div>
    <div class="table-responsive">


        <table class="table  table-bordered table-hover" id="tblEmployment" width="100%" >
            <thead>
            <tr>
                <th>Position</th>
                <th>Employer</th>
                <th>Year</th>
				<th>Nature </th>
                <th>Start Date</th>
				<th>End Date</th>
				<th>Monthly Salary (Ugx)</th>
				<th>Action</th>
            </tr>
            </thead>
            <tbody>
         
            </tbody>
            
            </table>
			
        </div>
	 </div><!-- ==END TAB-EMPLYMENT =====-->
