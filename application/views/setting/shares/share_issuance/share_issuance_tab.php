<div id="tab-share_issuance" class="tab-pane">
   
    <div class="pull-right add-record-btn">
    <?php if(in_array('1', $share_issuance_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_share_issuance-modal"><i class="fa fa-plus-circle"></i> Share Issuance </button>
    <?php } ?>
    </div>
    <h3><center>Share Issuance</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table class="table  table-bordered table-hover" id="tblShare_issuance" width="100%" style="overflow: auto;">
            <thead>
                <tr>
                    <th>Name / Category</th>
                    <th>Category Code</th>
                    <th>Total to Shares</th>
                    <th>Price per share</th>
                    <th>Date of Issue</th>
                    <th>Min Shares Per.App</th>
                    <th>Linked Account</th> 
                    <th>Status</th> 
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
     <?php $this->load->view('setting/shares/share_issuance/add_share_issuance'); ?>
</div>

