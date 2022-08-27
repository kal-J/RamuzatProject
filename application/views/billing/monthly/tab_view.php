<div role="tabpanel" id="tab-sms-monthly" class="tab-pane">
    <div class="row d-flex justify-content-center align-items-center">
        <label for="month">Filter by Month : &nbsp;</label>
        <input class="p-1" style="font-weight: bold;" onchange="filter_by_month()" id="month_filter" name="month" type="month" min="2000-01" value="<?php echo date('Y-m'); ?>" />
    </div>

    <div class="d-flex flex-row-reverse mx-2 my-2">
        <a onclick="print_sms_billing_monthly(event)" class="ml-1">
            <button class="btn btn-secondary btn-sm"><i class="fa fa-print fa-2x"></i></button>
        </a>

        <a target="_blank" id="print_sms_billing_monthly">
            <button class="btn btn-primary btn-sm">
                <i class="fa fa-file-excel-o fa-2x"></i>
            </button>
        </a>
    </div>
    <br>
    <div class="table-responsive">
        <table class="table table-striped table-condensed" id="tblBilling_monthly_sms" width="100%">
            <thead>
                <tr>
                    <th># Client NO.</th>
                    <th>Name</th>
                    <th>Mobile no.</th>
                    <th>Number of messages</th>
                    <th>Total cost (ugx)</th>

                </tr>
            </thead>
            <tbody>
            </tbody>



        </table>
    </div>
</div>