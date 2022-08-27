<div role="tabpanel" id="tab-holiday" class="tab-pane">
    <div><h3><center>Holidays And Non-working Days</center></h3></div>
    <div class="hr-line-dashed"></div>
    <div class="row">
        <div class="col-lg-6">    
        <?php if(in_array('1', $privileges)){ ?>
            <div><h3><center>Holidays<a data-toggle="modal" href="#add_holiday-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> New Holiday</a></center></h3></div>
        <?php } ?>        
            <div class="table-responsive">
                <table id="tblHoliday" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                    <thead>
                        <tr>
                            <th>Holiday Date</th>
                            <th>Holiday Name</th>
                            <th>&nbsp; </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.table-responsive-->
        </div>
        <div class="col-lg-6">
            <div class="ibox ">
                <div class="ibox-title  back-change">
                    <h3 class="text-uppercase text-center">Non Working Days</h3>
                    <div  class="text-center"><small class="font-bold">Note: Please only check the days you don't expect repayment of a loan</small></div>
                </div>
                <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <form method="post" enctype="multipart/form-data" action="<?php echo base_url('non_working_days/create'); ?>" id="formNonworkingdays" > 
                            <fieldset class="col-lg-12">     
                                <legend><small><strong>Check Non-working Days</strong></small></legend>
                            <!-- ko with: $root.non_working_days-->
                                <input type="hidden" name="id" data-bind="value: parseInt(id)">
                                <input type="checkbox" name="monday" id="monday" data-bind="checked: (parseInt(monday)==parseInt(1))?true:false" ><span>Monday</span><br>
                                <input type="checkbox" name="tuesday" id="tuesday" data-bind="checked: (parseInt(tuesday)==parseInt(1))?true:false" ><span>Tuesday</span><br>
                                <input type="checkbox" name="wednesday" id="wednesday" data-bind="checked: (parseInt(wednesday)==parseInt(1))?true:false" ><span>Wednesday</span><br>
                                <input type="checkbox" name="thursday" id="thursday" data-bind="checked: (parseInt(thursday)==parseInt(1))?true:false" ><span>Thursday</span><br>
                                <input type="checkbox" name="friday" id="friday" data-bind="checked: (parseInt(friday)==parseInt(1))?true:false" ><span>Friday</span><br>
                                <input type="checkbox" name="saturday" id="saturday" data-bind="checked: (parseInt(saturday)==parseInt(1))?true:false" ><span>Saturday</span><br>
                                <input type="checkbox" name="sunday" id="sunday" data-bind="checked: (parseInt(sunday)==parseInt(1))?true:false"><span>Sunday</span><br>
                            <!--/ko -->
                                <div class="col-lg-12 modal-footer">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Save</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

