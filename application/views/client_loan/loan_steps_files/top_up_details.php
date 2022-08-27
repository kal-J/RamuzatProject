                    <input name="group_loan_id" id="group_loan_id" type="hidden">
                    <?php if ($type =='group_loan' || (isset($case2) && $case2 =='group_loan')):?>
                        <fieldset class="col-lg-12">     
                        <legend>Group Details</legend>
                        <div class="form-group row">
                                <label class="col-lg-2 col-form-label">Group<span class="text-danger">*</span></label>
                                <div class="col-lg-4 form-group">
                                <?php if (isset($case2) && $case2 =='client_loan'):?>
                                    <input type="hidden" name="group_loan_id" id="group_loan_id" value="<?php echo $group_loan_details['id'];?>">
                                    <input type="text" class="form-control" value="<?php echo $group_loan_details['group_name'];?>" disabled>
                                  
                                <?php elseif(isset($group)): ?>
                                    <label><input type="hidden" name="group_id" id="group_id" value="<?php echo $group['id'];?>">
                                    <input type="text" value="<?php echo $group['group_name'];?>" class="form-control" disabled>
                                    </label>
                                <?php else: ?>
                                    <select class="form-control required" name="group_id" id="group_id" style="width: 100%" >  
                                        <option value="">--select--</option>
                                        <?php
                                        foreach ($groups as $group) {
                                            echo "<option value='" . $group['id'] . "'>" . $group['group_name'] . "</option>";
                                        }?>
                                    </select>
                            <?php endif; ?>
                                </div>
                                <label class="col-lg-2 col-form-label">Loan Type<span class="text-danger">*</span></label>
                                <?php if (isset($case2) && $case2 =='client_loan'):?>
                                <div class="col-lg-4 form-group">
                                    <span class="form-control"> <?php echo $group_loan_details['type_name'];?></span>
                                </div>
                                <?php else: ?>
                                <div class="col-lg-4 form-group">
                                    <select class="form-control required" name="loan_type_id" data-bind="value:loan_type" >  
                                        <option value="">--select--</option>
                                        <?php
                                        foreach ($loan_type as $value) {
                                            echo "<option value='" . $value['id'] . "'>" . $value['type_name'] . "</option>";
                                        }?>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>
                    </fieldset>
                <?php endif; ?>
                        
                    <fieldset class="col-lg-12">     
                        <legend>Application Details</legend>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Loan No<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" required name="loan_no" type="text" data-bind="attr: {value:loan_ref_no, readonly:loan_ref_no()!==false?'readonly':''}" />
                            </div>
                            <?php if ((isset($case2) && $case2 !='group_loan') && ($type =='client_loan' || (isset($case2) && $case2 =='client_loan')) ):?>
                            <label class="col-lg-2 col-form-label">Client<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                            <select class="form-control" id="member_id2" name="member_id" data-bind='options: member_names, optionsText: function(item){ return item.member_name+"-"+item.client_no;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: member_name' required data-msg-required="Member is required" style="width: 100%">
                                </select>
                            </div>
                            <?php elseif (isset($memberloan) && $memberloan == 'member_loan') :?>
                            <label class="col-lg-2 col-form-label">Client<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                            <input type="hidden" name="member_id" value="<?php echo (isset($user['id']) ? $user['id'] : ''); ?>">
                            <input type="text" class="form-control" readonly  value="<?php echo (isset($user['firstname'])? $user['firstname'].' '. $user['lastname'] . ' ' . $user['othernames']. '- ' . $user['client_no'] :'');?>">
                            </div>
                            <?php endif; ?>

                            <label class="col-lg-2 col-form-label">Application date<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <div class="input-group date">
                                    <input class="form-control" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>"  autocomplete="off" required name="application_date" data-bind="datepicker: $root.application_date, attr: {value:$root.application_date()}" type="text"><span  data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>"data-bind="datepicker: $root.application_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Active Loan<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <select style="width:100%;" id="active_loan_id" name="linked_loan_id"  class="form-control" required data-bind='options: $root.filtered_active_loan, optionsText: function(data_item){ return data_item.member_name+"-"+data_item.loan_no;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: $root.selected_active_loan'>
                                </select>
                            </div>

                        <?php if (isset($case2) && $case2 !='My Loans') { ?>
                            <label class="col-lg-2 col-form-label">Credit officer<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <select required class="form-control" id="credit_officer_id2" name="credit_officer_id" style="width: 100%" > 
                                    <option value="">--select--</option>      
                                    <?php
                                    foreach ($staffs as $staff) {
                                        echo "<option value='" . $staff['id'] . "'>" . $staff['salutation'] . ' ' . $staff['firstname'] . ' ' . $staff['lastname'].' '.$staff['othernames'].'-' .$staff['staff_no']."</option>";
                                    }
                                    ?>
                                </select>

                            </div>
                        <?php } ?>
                        </div><!--/row -->
                    </fieldset class="col-lg-12">
                   
                    <style type="text/css">
                        .blueText{color: blue;font-size: 10px;}
                    </style>

                    <fieldset class="col-lg-12" data-bind="template: {name: 'application_details', data: product_name,if: (typeof $root.product_name() !='undefined') && (typeof $root.product_name() !='null')}">   
                    </fieldset>
