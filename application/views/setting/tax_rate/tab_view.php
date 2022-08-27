<div class="tab-pane active" style="margin-top:10px;" id="tab-tax_rate">
    <div class="ibox-content">
        <div class="pull-right">
        <?php if(in_array('1', $privileges)){ ?>
            <button data-toggle="modal" class="btn btn-primary  btn-sm" data-target="#add_tax_rate-modal"><i class="fa fa-plus"></i> New Tax Rate</button>
        <?php } ?>
        </div>
        <h3><center>Tax rate</center></h3>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="tblTax_rate" style="width: 100%">
                <thead>
                    <tr>
                        <th>Rate</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
