<div id="tab-personalinfo" class="tab-pane active biodata" >
    <!-- ================== START YOUR CONTENT HERE =============== -->
    <div class="col-md-12">
        <?php $this->load->view('user/profile_pic_modal.php'); ?>
        <div class="profile-info">
            <div class="panel-title pull-right">
            <?php if(in_array('3', $staff_privilege)){ ?>
                <a href="#add_staff-modal" data-bind="click: initialize_edit" data-toggle="modal"  class="btn btn-default btn-sm">
                    <i class="fa fa-pencil"></i> Edit</a>
            <?php } ?>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div data-bind="with: user" class="row">
                        <div class="col-md-6">
                            <span><strong>Name</strong></span>
                            <h3 data-bind="text: salutation+' '+firstname+' '+lastname+' '+ ((othernames)?othernames:'')"></h3>
                        </div>

                        <div class="col-md-3">
                            <span><strong>Gender</strong></span>
                            <p data-bind="text: (gender=='1')?'Male':'Female';"></p>
                        </div>

                        <div class="col-md-3">
                            <span><strong>Date of Birth</strong></span>
                            <p data-bind="text: (date_of_birth)?moment(date_of_birth,'YYYY-MM-DD').format('D-MMM-YYYY'):'None'"></p>
                        </div>
                    </div>
                    <div class="modal-body">

                        <table class="table table-user-information  table-stripped  m-t-md">
                            <tbody data-bind="with: user">
                                <tr>
                                    <td><strong>Staff No.</strong></td>
                                    <td colspan="3" data-bind="text: (staff_no)?staff_no:''"></td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td colspan="3"><a data-bind="attr: {href:'mailto:'+(email)?email:'#'}, text: (email)?email:'None'" title="Click to send email"></a></td>
                                </tr>
                                <tr>
                                    <td><strong>Marital Status</strong></td>
                                    <td colspan="3" data-bind="text: (marital_status_name)?marital_status_name:''"></td>
                                </tr>
                                <tr>
                                    <td><strong>Children</strong></td>
                                    <td data-bind="text: (children_no)?children_no:'None'"> </td>
                                    <td><strong>Dependants</strong></td>
                                    <td data-bind="text: (dependants_no)?dependants_no:'None'"> </td>
                                </tr>
                                <tr>
                                    <td><strong>Disability</strong></td>
                                    <td data-bind="text: (disability==1)?'Yes':'No'"></td>
                                    <td><strong>CRB Card Number</strong></td>
                                    <td data-bind="text: (crb_card_no)?crb_card_no:'None'"></td> 
                                </tr>
                                <tr>
                                    <td><strong>Position</strong></td>
                                    <td colspan="3" data-bind="text: position"></td>
                                </tr>
                                <tr>
                                    <td><strong>Comment</strong></td>
                                    <td colspan="3" data-bind="text: (comment)?comment:'None'"></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php
                        $modalTitle = "Edit Staff Info";
                        $saveButton = "Update";
                        $this->load->view('user/staff/staff_user-modal');
                        ?>
                    </div>  
                </div>
            </div>
        </div>

        <hr>
        <div data-bind="with: user" class="row"  style="font-size:12px;">
            <div class="col-md-6">
                <div class="row" >
                    <div class="col-md-5">
                        <span><strong></strong></span>
                    </div>

                    <div class="col-md-7">
                        <span></span>
                    </div>

                    <div class="col-md-5">
                        <span><strong></strong></span>
                    </div>

                    <div class="col-md-7">
                        
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <span><strong>Created By:</strong></span>
                    </div>

                    <div class="col-md-8">
                        <span data-bind="text: created_by_salutation+' '+created_by_firstname+' '+created_by_lastname+' '+ ((created_by_othernames)?created_by_othernames:'')"></span>
                    </div>

                    <div class="col-md-4">
                        <span><strong>Date:</strong></span>
                    </div>

                    <div class="col-md-8">
                        <span data-bind="text: (date_created)?moment(date_created,'X').format('D-MMM-YYYY'):'None';"></span>
                    </div>  
                </div>
            </div>
        </div>
    </div> 

</div>
