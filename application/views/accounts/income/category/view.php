<div role="tabpanel" id="tab-service_category" class="tab-pane ">
     <div class="pull-right add-record-btn">
     <?php if(in_array('1', $accounts_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_service_category-modal"><i class="fa fa-plus-circle"></i> Add Service Category </button>
    <?php } ?>
    </div>
    <h3><center>Service Category</center></h3>
    <div class="table-responsive">
        <table id="tblService_category"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light" >
                <tr>
                    <th>Category</th>
                    <th>Code</th>
                    <th>Linked Account</th>
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
<?php $this->load->view('accounts/income/category/add_modal'); ?>
