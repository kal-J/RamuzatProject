<div id="tab-address" class="tab-pane biodata">
    <div class="pull-right add-record-btn">
    <?php if(in_array('1', $member_staff_privilege)){ ?>
        <button class="btn btn-primary btn-sm pull-right" type="button" data-toggle="modal" data-target="#add_address-modal"><i class="fa fa-edit"></i> Add an Address </button>
    <?php } ?>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="tblAddress" width="100%">
            <thead>
                <tr>
                    <th>Plot</th>
                    <th>Road</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Period</th>
                    <th>action</th>   
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

    </div>
</div>

<?php $this->load->view('user/address/address_add'); ?>


<!-- ==END TAB-ADDRESS =====-->
