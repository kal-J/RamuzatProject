<div id="tab-kin" class="tab-pane biodata">
    <div class="pull-right add-record-btn">
    <?php if(in_array('1', $member_staff_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_nextofkin-modal"><i class="fa fa-edit"></i> Add <?php echo $this->lang->line('cont_nextofkin'); ?> </button>
    <?php } ?>
        <?php $this->load->view('user/nextofkin/nextofkin_modal'); ?>
    </div>
    <div class="table-responsive">
        <table class="table  table-bordered table-hover" id="tblNextOfKin" width="100%" >
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Relationship</th>
                    <th>Share Portion</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Action</th>  
                </tr>
            </thead>
            <tbody>
            </tbody>
              <tfoot>
                <tr>
                    <th >Total Percentage </th>
                    <th colspan="2">&nbsp;</th> 
                    <th></th> 
                    <th colspan="2">&nbsp;</th> 
                </tr>
            </tfoot>
        </table>
    </div>
</div><!-- ==END TAB-NEXT OF KIN =====-->
