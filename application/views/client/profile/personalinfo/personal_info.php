<div id="tab-personalinfo" class="tab-pane active biodata" >
    <!-- ================== START YOUR CONTENT HERE =============== -->
    <div class="col-md-12">
    <?php 
    $this->load->view('user/profile_pic_modal.php');
    
    ?>
        <div class="profile-info">
            <div class="row">
                <div class="col-lg-12">
                    <div data-bind="with: user" class="row">
                        <div class="col-md-6">
                            <span><strong>Name</strong></span>
                            <h3 data-bind="text: salutation+' '+firstname+' '+lastname+' '+ ((othernames)?othernames:'')"></h3>
                        </div>

                        <div class="col-md-3">
                            <span><strong>Gender</strong></span>
                            <p data-bind="text: (parseInt(gender)==1)?'Male':'Female';"></p>
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
                                    <td><strong>Client No.</strong></td>
                                    <td colspan="3" data-bind="text: (client_no)?client_no:''"></td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td colspan="3"><a data-bind="text: (email)?email:'None'" ></a></td>
                                </tr>
                                <tr>
                                    <td><strong>Marital Status</strong></td>
                                    <td colspan="3" data-bind="text: (marital_status_name)?marital_status_name:'None'"></td>
                                </tr>
                                <?php if($org['children_comp']==1){ ?>
                                <tr>
                                    <td><strong>Children</strong></td>
                                    <td data-bind="text: children_no"> </td>
                                    <td><strong>Dependants</strong></td>
                                    <td data-bind="text: (dependants_no)?dependants_no:'None'"> </td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td><strong>Disability</strong></td>
                                    <td data-bind="text: (disability==1)?'Yes':'No'"></td>
                                    <td><strong>CRB Card Number</strong></td>
                                    <td data-bind="text: (crb_card_no)?crb_card_no:'None'"></td> 
                                </tr>
                                <tr>
                                    <td><strong>Ocupation</strong></td>
                                    <td colspan="3" data-bind="text: (occupation)?occupation:'None'" ></td>

                                </tr>
                              
                                <tr>
                                    <td><strong>Comment</strong></td>
                                    <td colspan="3" data-bind="text: (comment)?comment:'None'"></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <button href="#add_member-modal" data-bind="click: initialize_edit" data-toggle="modal" class="btn btn-primary btn-sm pull-right" type="button"><i class="fa fa-pencil"></i> Edit</button>
                        <?php
                        
                        $saveButton = "Update";
                        $this->view('client/profile/add_member_modal');
                        ?>
                    </div>  
                </div>
            </div>
        </div>
        <hr>
        
    </div> 
</div>
