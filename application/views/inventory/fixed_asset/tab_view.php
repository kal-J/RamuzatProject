<div role="tabpanel" id="tab-fixed_asset" class="tab-pane active">
     <div class="pull-right add-record-btn">
     <?php if(in_array('1', $accounts_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_asset-modal"><i class="fa fa-plus-circle"></i> Add Asset </button>
    <?php } ?>
    </div>
    <h3><center>Asset Register</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblInventory"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light" >
                <tr>
                    <th>Name</th>
                    <!--<th>Linked Account</th>-->
                    <th>Purchase Date</th>
                    <th>Purchase Cost</th>
                    <th title="Salvage Value">Salvage Value</th>
                    <th>Age</th>
                    <th>Expected Age</th>
                    <th title="Accumulated Depreciation or Appreciation">Depn / Appn</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- /.table-responsive-->
</div>
