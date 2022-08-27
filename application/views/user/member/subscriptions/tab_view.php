<!--div id="tab-client_subscriptions" class="tab-pane client_subscriptions"-->
<div class="pull-right add-record-btn">
    <?php if (in_array('1', $subscription_privilege)) { ?>
        <button class="btn btn-primary btn-sm pull-right" type="button" data-toggle="modal" data-target="#add_client_subscription-modal"><i class="fa fa-edit"></i> Pay <?php echo $this->lang->line('cont_subscription');  ?> </button>
    <?php } ?>
</div>
<div class="table-responsive">
    <table class="table table-striped table-condensed" id="tblClient_subscription" width="100%" >
        <thead>
            <tr>
                <th>Transaction</th>
                <th>Fee Name</th>
                <th>Expected Date</th>
                <th>Payment Date</th>
                <th>Payment Mode</th>
                <th>Amount</th>
                <th>Narrative</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5">Total</th>
                <th>0</th>
                <th colspan="2">&nbsp;</th>
            </tr>
        </tfoot>
    </table>
</div>
<?php $this->load->view('user/member/subscriptions/add_modal'); ?>
<?php $this->load->view('user/member/subscriptions/pay_sub_fee'); ?>
<?php $this->load->view('user/member/subscriptions/reverse_sub_modal'); ?>
<!--/div> <==END TAB-CLIENT SUBSCRIPTIONS =====-->