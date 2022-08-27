<div id="tab-deposit_product" class="tab-pane">
    <div class="pull-right add-record-btn">
    <?php if(in_array('1', $deposit_product_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_deposit_product-modal"><i class="fa fa-plus-circle"></i> Add Savings Product </button>
    <?php } ?>
    </div>
    <h3><center>Savings Products</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table class="table  table-bordered table-hover" id="tblDepositProduct" width="100%" >
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Type</th>
                    <th>Avalable To</th>
                    <th>Is Interest Paid?</th>
                    <th>Status</th>  
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
     <?php $this->load->view('setting/savings/deposit_product/add_deposit_product'); ?>
</div>

