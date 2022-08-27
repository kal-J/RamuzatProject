<div role="tabpanel" id="tab-chart_of_accounts" class="tab-pane">
     <div class="pull-right add-record-btn">
     <?php if(in_array('1', $accounts_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_account-modal"><i class="fa fa-plus-circle"></i> Add Account </button>
    <?php } ?>
    </div>
    
    <h3><center>Chart of Accounts</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblAccounts"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light" >
                <tr>
                    <th>Account Name</th>
                    <th>Parent Account</th>
                    <th>Normal Side</th>
                    <th>Manual Entry?</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- /.table-responsive-->
</div>