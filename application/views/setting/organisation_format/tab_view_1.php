<div class="tab-pane" style="margin-top:10px;" id="tab_account_format">
    <div class="ibox-content">
        <fieldset class="col-sm-12">    
            <legend> ACCOUNT Number Format</legend>
            <div class="table-responsive">
                <table data-bind="with: organisationOptions" class="table   table-hover" id="tblOrganisation_format" >
                    <thead>
                        <tr>
                            <th class="border-right">#</th>
                            <th title="Organisation's Initials" class="border-right">Section 1</th>
                            <th title="A unique number " class="border-right">Section 2 </th>
                            <th class="border-right">Section 3</th>
                            <th class="border-right">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="border-right">FORMAT 1:</th>
                            <td data-bind="text: (org_initial)?org_initial:'None'"> </td>
                            <td >xxxxxx</td> 
                            <td >xxxx</td>
                            <td><button id="1" class="btn btn-xs set_it"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 2:</th>
                            <td>xxxxxx</td>
                            <td> xxxx</td>
                            <td data-bind="text: (org_initial)?org_initial:'None'"></td> 
                            <td><button id="2" class="btn btn-xs set_it"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 3:</th>
                            <td> &nbsp;</td>
                            <td >xxxxxx</td> 
                            <td >xxxx</td>
                            <td><button id="3" class="btn btn-xs set_it"></button> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>

        <div class="hr-line-dashed"></div>
        <fieldset class="col-sm-12">    
            <legend> CLIENT Number</legend>
            <div class="table-responsive ">
                <table data-bind="with: org_member_no" class="table   table-hover" id="tblMember_no_format" >
                    <thead>
                        <tr>
                            <th class="border-right">#</th>
                            <th title="Organisation's Initials" class="border-right">Section 1</th>
                            <th title="A unique number " class="border-right">Section 2 </th>
                            <th class="border-right">Section 3</th>
                            <th class="border-right">Example</th>
                            <th class="border-right">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="border-right">FORMAT 1:</th>
                            <td data-bind="text: (org_initial)?org_initial:'None'"> </td>
                            <td >xxxxx</td> 
                            <td >A-Z</td>
                            <td data-bind="text:org_initial+'001A'"></td>
                            <td><button id="1" class="btn btn-xs set_member_no"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 2:</th>
                            <td>A-Z</td>
                            <td>xxxxx</td>
                            <td data-bind="text: (org_initial)?org_initial:'None'"></td> 
                            <td data-bind="text:'A'+'001'+org_initial"></td>
                            <td><button id="2" class="btn btn-xs set_member_no"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 3:</th>
                            <td>A-Z</td>
                            <td>xxxxx</td>
                            <td>&nbsp;</td> 
                            <td data-bind="text:'A'+'001'"></td>
                            <td><button id="3" class="btn btn-xs set_member_no"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 4:</th>
                            <td> &nbsp;</td>
                            <td >xxxxx</td> 
                            <td >A-Z</td>
                            <td data-bind="text:'001'+'A'"></td>
                            <td><button id="4" class="btn btn-xs set_member_no"></button> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>

        <div class="hr-line-dashed"></div>
        <fieldset class="col-sm-12">    
            <legend> STAFF Number</legend>
            <div class="table-responsive ">
                <table data-bind="with: org_staff_no" class="table   table-hover" id="tblStaff_no_format" >
                    <thead>
                        <tr>
                            <th class="border-right">#</th>
                            <th title="Organisation's Initials" class="border-right">Section 1</th>
                            <th title="A unique number " class="border-right">Section 2 </th>
                            <th class="border-right">Section 3</th>
                            <th class="border-right">Example</th>
                            <th class="border-right">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="border-right">FORMAT 1:</th>
                            <td data-bind="text: (org_initial)?org_initial:'None'"> </td>
                            <td >xxxxx</td> 
                            <td >A-Z</td>
                            <td data-bind="text:org_initial+'10001A'"></td>
                            <td><button id="1" class="btn btn-xs set_staff_no"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 2:</th>
                            <td>A-Z</td>
                            <td>xxxxx</td>
                            <td data-bind="text: (org_initial)?org_initial:'None'"></td> 
                            <td data-bind="text:'A'+'001'+org_initial"></td>
                            <td><button id="2" class="btn btn-xs set_staff_no"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 3:</th>
                            <td>A-Z</td>
                            <td>xxxxx</td>
                            <td>&nbsp;</td> 
                            <td data-bind="text:'A'+'001'"></td>
                            <td><button id="3" class="btn btn-xs set_staff_no"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 4:</th>
                            <td> &nbsp;</td>
                            <td >xxxxx</td> 
                            <td >A-Z</td>
                            <td data-bind="text:'001'+'B'"></td>
                            <td><button id="4" class="btn btn-xs set_staff_no"></button> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>

        <div class="hr-line-dashed"></div>
        <fieldset class="col-sm-12">    
            <legend> Group Number</legend>
            <div class="table-responsive ">
                <table data-bind="with: org_group_no" class="table   table-hover" id="tblGroup_no_format" >
                    <thead>
                        <tr>
                            <th class="border-right">#</th>
                            <th title="Organisation's Initials" class="border-right">Section 1</th>
                            <th title="A unique number " class="border-right">Section 2 </th>
                            <th class="border-right">Section 3</th>
                            <th class="border-right">Example</th>
                            <th class="border-right">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="border-right">FORMAT 1:</th>
                            <td data-bind="text: (org_initial)?org_initial:'None'"> </td>
                            <td >xxxxx</td> 
                            <td >A-Z</td>
                            <td data-bind="text:org_initial+'10001A'"></td>
                            <td><button id="1" class="btn btn-xs set_group_no"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 2:</th>
                            <td>A-Z</td>
                            <td>xxxxx</td>
                            <td data-bind="text: (org_initial)?org_initial:'None'"></td> 
                            <td data-bind="text:'A'+'001'+org_initial"></td>
                            <td><button id="2" class="btn btn-xs set_group_no"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 3:</th>
                            <td>A-Z</td>
                            <td>xxxxx</td>
                            <td>&nbsp;</td> 
                            <td data-bind="text:'A'+'001'"></td>
                            <td><button id="3" class="btn btn-xs set_group_no"></button> </td>
                        </tr>
                        <tr>
                            <th class="border-right">FORMAT 4:</th>
                            <td> &nbsp;</td>
                            <td >xxxxx</td> 
                            <td >A-Z</td>
                            <td data-bind="text:'001'+'B'"></td>
                            <td><button id="4" class="btn btn-xs set_group_no"></button> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>

    </div>
</div>

<script>
var btnid="<?php echo isset($account_format['account_format'])?$account_format['account_format']:'1';?>";
        $('.set_it').removeClass('btn-primary').text('Make Default').addClass('text-muted');
        $('#tblOrganisation_format #'+btnid).addClass('btn-primary').removeClass('btn-default text-muted').text('Default');

var btn_client="<?php echo isset($member_format['client_format'])?$member_format['client_format']:'1';?>";
        $('.set_member_no').removeClass('btn-primary').text('Make Default').addClass('text-muted');
        $('#tblMember_no_format #'+btn_client).addClass('btn-primary').removeClass('btn-default text-muted').text('Default');

var btn_staff="<?php echo isset($staff_format['staff_format'])?$staff_format['staff_format']:'1';?>";
        $('.set_staff_no').removeClass('btn-primary').text('Make Default').addClass('text-muted');
        $('#tblStaff_no_format #'+btn_staff).addClass('btn-primary').removeClass('btn-default text-muted').text('Default');
var btn_partner="<?php echo isset($partner_format['partner_format'])?$partner_format['partner_format']:'1';?>";
        $('.set_partner_no').removeClass('btn-primary').text('Make Default').addClass('text-muted');
        $('#tblPartner_no_format #'+btn_partner).addClass('btn-primary').removeClass('btn-default text-muted').text('Default');

var btn_group="<?php echo isset($group_format['group_format'])?$group_format['group_format']:'1';?>";
        $('.set_group_no').removeClass('btn-primary').text('Make Default').addClass('text-muted');
        $('#tblGroup_no_format #'+btn_group).addClass('btn-primary').removeClass('btn-default text-muted').text('Default');

$('.set_it').on('click',function(){
    if($(this).hasClass('set_it')){
        var myclass="set_it";
    }
var vv=	setDefault_item("Want to set this format as default?\n All the new account numbers will have this format",$(this).attr('id'),myclass,'<?php echo $account_format['id'];?>', "<?php echo site_url(); ?>Organisation_format/makeDefault");
});

$('.set_member_no').on('click',function(){
    if($(this).hasClass('set_member_no')){
        var myclass="set_member_no";
    }
var vv=	setDefault_item("Want to set this format as default?\n All the new client numbers will have this format",$(this).attr('id'),myclass,'<?php echo $member_format['id'];?>', "<?php echo site_url(); ?>Organisation_format/makeDefault_member_no");
});

$('.set_staff_no').on('click',function(){
    if($(this).hasClass('set_staff_no')){
        var myclass="set_staff_no";
    }
var vv=	setDefault_item("Want to set this format as default?\n All the new staff numbers will have this format",$(this).attr('id'),myclass,'<?php echo $staff_format['id'];?>', "<?php echo site_url(); ?>Organisation_format/makeDefault_staff_no");
});

$('.set_group_no').on('click',function(){
    if($(this).hasClass('set_group_no')){
        var myclass="set_group_no";
    }
var vv=	setDefault_item("Want to set this format as default?\n All the new Group numbers will have this format",$(this).attr('id'),myclass,'<?php echo $group_format['id'];?>', "<?php echo site_url(); ?>Organisation_format/makeDefault_group_no");
});
  function setDefault_item(msg, id, myclass,org_id, url) {
        swal({
            title: "Set Default?",
            text: msg,
            type: "success",
            showCancelButton: true,
            confirmButtonColor: "green",
            confirmButtonText: "Yes!, Set",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false
        },
                function () {
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
                });
    }//End of the deleting function
	
</script>