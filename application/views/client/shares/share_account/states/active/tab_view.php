<div role="tabpanel" id="tab-share_active_accounts" class="tab-pane active">
    <div class="row d-flex flex-row-reverse mt-3 mr-4">

        

        <div class="ml-2">
            <button id="btn_print_active_shares" onclick="handlePrint_active_shares()" class="btn btn-sm btn-secondary">
                <i class="fa fa-print fa-2x"></i>
            </button>
            
        </div>

        <div class="ml-2">
            <a target="_blank" href="shares/export_excel/7/1">
                <button class="btn btn-sm btn-secondary">
                    <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </a>
        </div>
        
    </div>



    <br>
    <div class="col-lg-12">
    <div class="table-responsive">
                  <table class="table table-striped table-bordered table-hover  table-hover" id="tblShares_Active_Account" width="100%" >
                    <thead>
                    <tr> 
                        <th>Share A/C NO</th>
                        <th>Account Name</th>
                        <th>Price Per Share (UGX)</th> 
                        <th>No for Shares</th> 
                        <th>Total Amount (UGX)</th> 
                      </tr>
                  </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
    </div>
</div>