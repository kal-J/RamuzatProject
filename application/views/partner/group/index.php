<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5> Investiment Groups 
                    <div class="pull-right add-record-btn">
                    <?php if(in_array('1', $group_privilege)){ ?>
                        <button  type="button" data-toggle="modal" class="btn btn-sm btn-primary" data-target="#add_group-modal"><i class="fa fa-plus-circle"></i> New Group</button>
                    <?php } ?>
                    </div>
                </h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tblGroup">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Group Name</th>
                                <th>No. of members</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->view("partner/group/add_modal");
$this->view("partner/group/group_js");