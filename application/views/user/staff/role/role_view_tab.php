<div id="tab-role" class="tab-pane biodata" >
<div class="pull-right add-record-btn">
<?php if(in_array('1', $staff_privilege)){ ?>
	<button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_role-modal"><i class="fa fa-edit"></i> Assign Role </button>
<?php } ?>
    </div>
	<?php  $this->load->view('user/staff/role/role_modal'); ?>
    <div class="table-responsive">
        <table class="table  table-hover" id="tblUser_role" width="100%" >
            <thead >
                <tr>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>  
                </tr>
            </thead>
            <tbody>

            </tbody>

        </table>

    </div>
</div>
