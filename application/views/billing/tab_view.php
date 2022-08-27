<div role="tabpanel" id="tab-sms" class="tab-pane active">
    <div class="d-flex flex-row-reverse mx-2 my-2">
        <a onclick="print_sms_billing(event)" class="ml-1">
            <button class="btn btn-secondary btn-sm"><i class="fa fa-print fa-2x"></i></button>
        </a>

        <a target="_blank" id="print_sms_billing">
            <button class="btn btn-primary btn-sm">
                <i class="fa fa-file-excel-o fa-2x"></i>
            </button>
        </a>

    </div>
    <br>
    <div class="table-responsive">
        <table class="table table-striped table-condensed" id="tblBilling" width="100%">
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