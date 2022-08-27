<div id="tab-contact" class="tab-pane biodata">
    <div class="pull-right add-record-btn">
    <?php if(in_array('1', $member_staff_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_contact-modal"><i class="fa fa-edit"></i> Add Contact </button>
    <?php } ?>
        <?php $this->load->view('user/contact/contact_modal'); ?>
    </div>
    <div class="table-responsive">
        <table id="tblContact" class="table table-striped  table-hover"  width="100%">
            <thead>
                <tr>
                    <th>Phone Number</th>
                    <th>Type</th>
                    <th>Action</th>   
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div><!-- ==END TAB-CONTACT =====-->