<div class="panel-body"><br>
    <div class="col-lg-12">
        <p>
            <center><strong>Income Transactions</strong></center>
            <?php if (in_array('3', $accounts_privilege)) { ?>
                <a  data-toggle="modal" href="#add_income-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-edit"></i> Add Income</a>
            <?php } ?>
        </p>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table class="table-bordered  display compact nowrap table-hover" id="tblAsset_income" width="100%" >
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Income Type</th>
                        <th>Transn. Date</th>
                        <th>Type</th>
                        <th>Amount </th>
                        <th>Income Account </th>
                        <th>Narrative</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">Totals</th>
                         
                        <th>0 </th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th> 
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php
$this->load->view('inventory/fixed_asset/income/add_modal');
$this->load->view('inventory/fixed_asset/income/edit_income_transaction');
?>
