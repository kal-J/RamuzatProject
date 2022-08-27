<div class="tab-pane" style="margin-top:10px;" id="tab_account_format">
    <div class="ibox-content">
        <form id="savings_account_formats" action="<?php echo site_url("organisation_format/savings_account");?>" role="form">
            <fieldset class="col-sm-12">    
                <legend> Savings Account Number</legend>
                <div class="row" data-bind="template: { name: 'formatTemplate', data: savings_account_format_model }">
              </div>
            </fieldset>
        </form>
        <div class="hr-line-dashed"></div>
        <form id="loan_account_format" action="<?php echo site_url("organisation_format/loan_account");?>" role="form">
            <fieldset class="col-sm-12">    
                <legend> Loan Account Number</legend>
                <div class="row" data-bind="template: { name: 'formatTemplate', data: loan_account_format_model }">
                </div>
            </fieldset>
        </form>
        <div class="hr-line-dashed"></div>
        <form id="client_number_format" action="<?php echo site_url("organisation_format/client_number");?>" role="form">
            <fieldset class="col-sm-12">    
                <legend> Client Number</legend>
                <div class="row" data-bind="template: { name: 'formatTemplate', data: client_number_format_model }">
                </div>
            </fieldset>
        </form>
        <div class="hr-line-dashed"></div>
        <form id="staff_number_format" action="<?php echo site_url("organisation_format/staff_number");?>" role="form">
            <fieldset class="col-sm-12">    
                <legend> Staff Number</legend>
                <div class="row" data-bind="template: { name: 'formatTemplate', data: staff_number_format_model }">
                </div>
            </fieldset>
        </form>
        <div class="hr-line-dashed"></div>
        <form id="group_number_format" action="<?php echo site_url("organisation_format/group_number");?>" role="form">
            <fieldset class="col-sm-12">    
                <legend> Group Number</legend>
                <div class="row" data-bind="template: { name: 'formatTemplate', data: group_number_format_model }">
                </div>
            </fieldset>
        </form>
    </div>
</div>

<script>
function  submit_formats() {
        $.post(
                url,
                {id: id,org_id:org_id},
                function (response) {
                    if (response.success) {
                        setTimeout(function () {
                            toastr.success(response.message, "Success!");
                            if(myclass==="set_it"){
                            $('.set_it').removeClass('btn-primary').text('Make Default').addClass('text-muted');
                            $('#tblOrganisation_format #'+id).addClass('btn-primary').removeClass('btn-default text-muted').text('Default');
                            }else if(myclass==="set_staff_no"){
                            $('.set_staff_no').removeClass('btn-primary').text('Make Default').addClass('text-muted');
                            $('#tblStaff_no_format #'+id).addClass('btn-primary').removeClass('btn-default text-muted').text('Default');
                            }

                            else if(myclass==="set_member_no"){
                            $('.set_member_no').removeClass('btn-primary').text('Make Default').addClass('text-muted');
                            $('#tblMember_no_format #'+id).addClass('btn-primary').removeClass('btn-default text-muted').text('Default');
                            }else{
                            $('.set_group_no').removeClass('btn-primary').text('Make Default').addClass('text-muted');
                            $('#tblGroup_no_format #'+id).addClass('btn-primary').removeClass('btn-default text-muted').text('Default');
                            }

                            }, 1000);
                    } else {
                        toastr.error("", "Operation failed. Reason(s):<ol>" + response.message + "</ol>", "Setting Failed!");
                    }
                },
                'json').fail(function (jqXHR, textStatus, errorThrown) {
            network_error(jqXHR, textStatus, errorThrown, $("#myform"));
        });
        swal.close();
    }
</script>