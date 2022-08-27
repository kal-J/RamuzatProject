<div class="pull-right add-record-btn">
    <?php if (in_array('1', $member_privilege)) { ?>
        <button class="btn btn-primary btn-sm pull-right" type="button" data-toggle="modal" data-target="#attach_member_fees-modal"><i class="fa fa-edit"></i> Pay/Attach Fee(s) </button>
    <?php } ?>
</div>
<style>

</style>
<div class="table-responsive">
    <table class="table table-striped table-condensed" id="tblApplied_member_fees" width="100%" >
        <thead>
            <tr>
                <th>Transaction #</th>
                <th>Fee name</th>
                <th>Amount</th>
                <th>Payment date</th>
                <th>Mode</th>
                <th>Status ?</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total</th>
                <th colspan="3">&nbsp;</th>
            </tr>
        </tfoot>

    </table>
</div>
<?php $this->load->view('user/member/member_fees/add_modal'); ?>
<?php $this->load->view('user/member/member_fees/attach_member_fees'); ?>
<!--/div> <==END TAB-CLIENT SUBSCRIPTIONS =====-->