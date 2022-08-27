<div id="tab-business" class="tab-pane biodata">
    <div class="pull-right add-record-btn">
    <?php if(in_array('1', $member_privilege)){ ?>
        <button class="btn btn-primary btn-sm pull-right" type="button" data-toggle="modal" data-target="#add_business-modal"><i class="fa fa-edit"></i> Assign business </button>
    <?php }?>
        <?php $this->load->view('user/member/business/business_modal'); ?>
    </div>
    <div class="table-responsive">
        <table class="table table-striped" id="tblBusiness" width="100%" >
            <thead>
                <tr>
                    <th>Business</th>
                    <th>Nature</th>
                    <th>Location</th>
                    <th>Employees</th>
                    <th>Net worth</th>
                    <th>URSB Number</th>
                    <th>Certification</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>

        </table>

    </div>
</div><!-- ==END TAB-BUSINESS =====-->