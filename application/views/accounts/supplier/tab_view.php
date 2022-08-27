<div role="tabpanel" id="tab-supplier" class="tab-pane ">
     <div class="pull-right add-record-btn">
     <?php if(in_array('1', $accounts_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_supplier-modal"><i class="fa fa-plus-circle"></i> New Supplier/Vendor </button>
    <?php } ?>
    </div>
    <h3><center>Suppliers/Vendors</center></h3>
    <div class="hr-line-dashed"></div>
                <div class="table-responsive">
                    <table class="table table-striped table-condensed table-hover" id="tblSupplier" width="100%">
                        <thead>
                            <tr>
                                <th>Names</th>
                                <th>TIN</th>
                                <th>Type</th>
                                <th>Sales Count</th>
                                <th>Phone</th>
                                <!--th>Phone2</th-->
                                <th>Email</th>
                                <!--th>Email2</th>
                                <th>Postal Address</th>
                                <th>Physical Address</th-->
                                <th>Country</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
        <?php $this->load->view('accounts/supplier/add_supplier_modal'); ?>
    </div><!-- /.tab-pane -->