<div role="tabpanel" id="tab-guarantors" class="tab-pane">
    <div class="panel-body">
        <div class="pull-left add-record-btn">
            <div class="panel-title">
                <center>
                    <h3 style="font-weight: bold;">Guarantors</h3>
                </center>
            </div>
        </div>
        <?php if (in_array('1', $client_loan_privilege)) { ?>
            <a data-toggle="modal" href="#add_member_guarantor-modal" class="btn btn-primary btn-sm pull-right mx-2 mb-1"><i class="fa fa-plus-circle"></i> Add Guarantor</a>
            
           

        <div class="table-responsive">
        <div class="form-group row" style="margin: 0 15%;padding:0">
            <label for="gender" class="form-label"><strong>&nbsp;&nbsp;Gender:</strong></label>
                 <div class="form-group col-lg-2">
                     <?php $gender = array(2,1,0); ?>
                     <select name="gender" id="gender" class="form-control">
                         <?php foreach($gender as $option){ ?> 
                         <option value="<?php echo $option ?>"><?php echo $option == 0 ? "Female":( $option== 1 ? "Male": "All")?></option>
                         <?php }?>
                     </select>
                    
                 </div>
                 <span><button class="btn btn-primary" onclick="get_guarantors(event)" >Preview</button></span>
             </div>
            <?php } ?>
            <table id="tblGuarantor" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                <thead>
                    <tr>
                        <th>Guarantor</th>
                        <th>Type</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>National ID No.</th>
                        <th>Relationship Type</th>
                        <th>Comment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div><!-- /.table-responsive-->
    </div>
</div>
