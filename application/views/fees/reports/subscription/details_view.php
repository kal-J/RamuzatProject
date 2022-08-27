<div class="pull-right add-record-btn">
<?php if (in_array('1', $member_privilege)) { ?>
    <button class="btn btn-primary btn-sm pull-right" type="button" onclick="location.href='<?php echo site_url(); ?>automated_fees/auto_membership_payment'" >
            <i class="fa fa-money"></i>  </button>
    <?php } ?>
</div>
<style>

</style>
<div class="table-responsive">
    <table class="table table-striped table-condensed" id="tblDetail_subscription_fees" width="100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Fee name</th>
                <th>Amount</th>
                <th>Payment date</th>
                <th>Status ?</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>

    </table>
</div>

<script>
    var dTable = {};
    $(document).ready(function() {
        <?php $this->view("fees/reports/subscription/details_js"); ?>
    });
</script>