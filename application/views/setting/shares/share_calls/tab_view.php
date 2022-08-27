<div role="tabpanel" id="tab-share_call" class="tab-pane">
    <div class="panel-body">
    <?php if(in_array('1', $share_issuance_privilege)){ ?>
    <div><a data-toggle="modal" href="#add_share_call-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> Add Share Call</a></div>
    <?php } ?>
    <h3><center>Share Call</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblShare_call" class="table table-striped table-bordered table-hover m-t-md" width="100%">
            <thead>
                <tr>
                      <th>Call Name</th>
                      <th>Percentage</th>
                      <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
              <tfoot>
                <tr>
                    <th >Total Percentage </th>
                    <th> </th>
                    <th>&nbsp;</th> 
                </tr>
            </tfoot>
        </table>
     </div>

    </div><!-- /.table-responsive-->
</div>

