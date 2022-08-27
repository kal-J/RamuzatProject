<div role="tabpanel" id="tab-alert-setting" class="tab-pane alert-setting">
    <div class="row d-flex flex-row-reverse mt-3 mr-4">
       

         <div class="">
            <div class="panel-title" style="color:#fff;">
                <?php if (in_array('1', $privileges)) { ?>
                    <a href="#custom_email-modal" data-toggle="modal" id="add_custom_emia_setting" class="btn btn-primary btn-lg"> <i class="fa fa-plus-circle" ></i> Send Email</a>
                <?php } ?>
            </div>
            
        </div>&nbsp;&nbsp;&nbsp;
        <div class="">
            <div class="panel-title" style="color:#fff;">
                <?php if (in_array('1', $privileges)) { ?>
                    <a href="#alert_setting-modal" data-toggle="modal" id="add_alert_setting" class="btn btn-primary btn-lg"> <i class="fa fa-plus-circle" ></i> New Alert Setting</a>
                <?php } ?>
            </div>
            
        </div>
         

    </div>


    <br>
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table  table-bordered table-hover" id="tblAlert_setting" width="100%">
                <thead>
                    <tr>
                        <th>Alert Method</th>
                         <th>Alert Type</th>
                        <th>No. of days to duedate</th>
                        <th>Interval of reminder</th>
                        <!--<th>Action</th>-->
                    </tr>
                </thead>
                <tbody>
                </tbody>
                 
            </table>
        </div>
    </div>
</div>