<div class="tab-pane" style="margin-top:10px;" id="tab-tax_application">
    <div class="ibox-content">
        <div class="pull-right">
            <?php if (in_array('1', $privileges)) { ?>
                <button data-toggle="modal" class="btn btn-primary  btn-sm" data-target="#add_tax_application-modal"><i class="fa fa-plus"></i> Apply Tax(es)</button>
            <?php } ?>
        </div>
        <h3><center>Tax application</center></h3>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="tblTax_application" style="width: 100%">
                <thead>
                    <tr>                        
                        <th>Applied to</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

