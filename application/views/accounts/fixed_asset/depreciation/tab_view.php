<div class="panel-body"><br>
    <div class="col-lg-12">
        <p>
            <strong>Depreciation</strong>
            <?php if (in_array('3', $accounts_privilege)) { ?>
            <!--ko with:fixed_asset_detail-->
            <a data-bind="visible: (purchase_date?(moment(<?php echo time();?>,'X').diff(moment(purchase_date,'YYYY-MM-DD'),'years')):0)<=parseInt(expected_age)" data-toggle="modal" href="#add_depreciation-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-edit"></i> Add Depreciation</a>
            <!--/ko-->
            <?php } ?>
        </p>
        <p data-bind="with: fixed_asset_detail"><strong>Method:</strong> <span data-bind="text: method_name"></span>.
            <strong>Rate:</strong> <span data-bind="text: curr_format(depreciation_rate*1)"></span>%
        </p>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table class="table  table-bordered table-hover" id="tblDepreciation" width="100%" >
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Fin. Year</th>
                        <th>Amount </th>
                        <th>Narrative</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Totals</th>
                        <th>0 </th>
                        <th colspan="2">&nbsp;</th> 
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php
$this->load->view('accounts/fixed_asset/depreciation/add_modal');
