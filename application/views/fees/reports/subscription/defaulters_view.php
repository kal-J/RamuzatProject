
<div class="row">
    <div class="col-sm-8">
    <label class="col-lg-2 col-form-label">Status<span class="text-danger">*</span></label>
        <div class="col-lg-4 form-group">
            <select class="form-control" name="status_id" id="status_id" data-bind='options: statuses, optionsText: function(data){ return data.name}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: status1, event:{ change: $root.updateData}' style="width: 100%">
            </select>

        </div>

    </div>
    <div class="col-sm-4">
        <div id="reportrange" class="reportrange pull-right">
            <i class="fa fa-calendar"></i>
            <span>January 01, 2019 - December 31, 2020</span> <b class="caret"></b>
        </div>
    </div>
</div>


<h2 style="text-align: center; padding-top: 30px; font-weight:bold">Subscription Fees Summary</h2>


<div class="table">
    <table class="table table-striped table-condensed" id="tblDefaulters_fees_summary2" width="100%">
        <thead>
            <tr>
                <th>No.of Defaulters</th>
                <th>Total Fees Due</th>
                <th>Total Fees Paid</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>

    </table>
</div>
<!--/div> <==END TAB-CLIENT FEES SUMMARY =====-->

<hr class="my-12" />

<div class="table-responsive">
    <table class="table table-striped table-condensed" id="tblDefaulters_subscription_fees" width="100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Fee name</th>
                <th>Amount</th>
                <th>Payment date</th>
                <th>Status ?</th>
                <th>Action</th>
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
                <th>&nbsp;</th>
            </tr>
        </tfoot>

    </table>
</div>
<?php $this->load->view('fees/reports/subscription/pay_modal'); ?>
<!--/div> <==END TAB-CLIENT SUBSCRIPTIONS =====-->