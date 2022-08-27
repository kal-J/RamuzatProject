<div role="tabpanel" id="tab-items_for_sale" class="tab-pane">
    <br>
    <div class="pull-right add-record-btn">
        <?php if (in_array('1', $deposit_product_privilege)) { ?>
            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#new_item-modal"><i class="fa fa-plus-square" aria-hidden="true"></i> &nbsp; New Item</button>
        <?php } ?>
    </div>
    <?php if (in_array('1', $deposit_product_privilege)) { ?>
        <!--  <button class="btn btn-success btn-sm" type="button" data-toggle="modal" data-target="#post_loans-modal" ><i class="fa fa-plus-circle"></i> Post Loans</button> -->
    <?php } ?>
    <h3>
        <center>Items For Sales</center>
    </h3>
    <div class="hr-line-dashed"></div>
    <div class="panel-body">

        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table-bordered display compact nowrap table-hover" id="tblItemsForSale" width="100%">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>