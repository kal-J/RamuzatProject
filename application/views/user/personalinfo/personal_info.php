<div id="tab-personalinfo" class="tab-pane active biodata" >
    <!-- ================== START YOUR CONTENT HERE =============== -->
    <div class="col-md-12">
            <?php if(in_array('6', $member_privilege)){ ?>
         <!-- <div  class="pull-right add-record-btn">
              <a href="#printout_options-modal" data-toggle="modal" class="btn btn-primary btn-sm"> <i class="fa fa-print fa-2x"></i> </a>
          </div>
                        <?php// $this->load->view('user/member/printout_options'); ?>-->
            
        <div  class="pull-right add-record-btn">
            <button id="btn_print_member_bio_data" onclick="handlePrint_member_bio_data()" class="btn btn-sm btn-primary" title="Print member bio data">
                <i class="fa fa-print fa-2x"></i>
            </button>
            <!--<button id="btn_printing_member_bio_data" class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                Printing...
            </button>-->
       </div>
          
      <?php } ?>
    <?php $this->load->view('user/profile_pic_modal.php'); ?>
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
                                    <td><strong><?php echo $this->lang->line('cont_client_name');?> No.</strong></td>
                                    <td colspan="3" data-bind="text: (client_no)?client_no:''"></td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td colspan="3"><a data-bind="attr: {href:'mailto:'+(email)?email:'#'}, text: (email)?email:'None'" title="Click to send email"></a></td>
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
                                    <td data-bind="text: (occupation)?occupation:'None'" ></td>

                                    <td><strong>NIN</strong></td>
                                    <td data-bind="text: (nid_card_no)?nid_card_no:'None'" ></td>

                                </tr>
                                 <tr>
                                    <td><strong>Branch</strong></td>
                                    <td colspan="3" data-bind="text: (branch_name)?branch_name:'None'"></td>
                                </tr>
                                <?php if(in_array('9', $modules)){ ?>
                                <tr>
                                    <td><strong>Subscription Plan</strong></td>
                                    <td colspan="3" data-bind="text: (plan_name)?plan_name:'None'"></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td><strong>Comment</strong></td>
                                    <td colspan="3" data-bind="text: (comment)?comment:'None'"></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php if(in_array('3', $member_privilege)){ ?>
                        <button href="#add_member-modal" data-bind="click: initialize_edit" data-toggle="modal" class="btn btn-primary btn-sm pull-right" type="button"><i class="fa fa-pencil"></i> Edit</button>
                        <?php
                        }
                        $modalTitle = "Edit Member Info";
                        $saveButton = "Update";
                        $this->view('user/member/add_member_model');
                        ?>
                    </div>  
                </div>
            </div>
        </div>
        <hr>
        <div data-bind="with: user" class="row"  style="font-size:12px;">
            <div class="col-md-6">
                <div class="row" >
                <?php if($org['member_referral']==1){?>
                    <div class="col-md-5">
                        <span><strong>Referred By:</strong></span>
                    </div>
                   
                    <div class="col-md-7">
                 
                        <span data-bind="text:referred_by_name"></span>
                      
                    </div>
                    <?php }?>

                   
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <span><strong>Registered By:</strong></span>
                    </div>

                    <div class="col-md-8">
                        <span data-bind="text: registered_salutation+' '+registered_firstname+' '+registered_lastname+' '+ ((registered_othernames)?registered_othernames:'')"></span>
                    </div>

                    <div class="col-md-4">
                        <span><strong>Date:</strong></span>
                    </div>

                    <div class="col-md-8">
                        <span data-bind="text: (date_registered)?moment(date_registered,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';"></span>
                    </div>  
                </div>
            </div>
        </div>
    </div> 
</div>
      
