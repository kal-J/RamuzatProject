<div class="panel-body">
    <div>
        <strong>Asset Details</strong> 
        <?php if (in_array('3', $accounts_privilege)) { ?>
                <!--a data-toggle="modal" href="#add_asset-modal"  class="btn btn-sm btn-primary pull-right"><i class="fa fa-pencil"></i> Update</a-->
        <?php } ?>
    </div>
    <table class="table table-stripped  m-t-md">
        <tbody data-bind="with: fixed_asset_detail">
            <tr>
                <td>
                    <i class="fa fa-houzz text-navy"></i> Asset Name
                </td>

                <td data-bind="text: asset_name" class="text-muted">
                </td>

                <td rowspan="2">
                    <i class="fa fa-hashtag text-navy"></i> Description
                </td rowspan="2">
                <td class="text-muted" data-bind="text: description">
                </td>


            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>Asset Account:
                </td>
                <td class="text-muted">
                    <a title='Click to view account details' data-bind='attr: {href:"<?php echo site_url("accounts/view");?>/" +asset_account_id}, text: "["+account_code+ "]  "+account_name'></a>
                </td>
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>Identification No (S/N)
                </td>
                <td data-bind="text: identity_no" class="text-muted">
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Purchase date
                </td>
                <td class="text-muted" data-bind="text: moment(purchase_date, 'YYYY-MM-DD').format('D-MMM-YYYY')">
                </td>
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>Purchase Cost:
                </td>
                <td data-bind="text: curr_format(purchase_cost*1)" class="text-muted">
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Date When Put To Use
                </td>
                <td class="text-muted" data-bind="text: moment(date_when, 'YYYY-MM-DD').format('D-MMM-YYYY')">
                </td>
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Expected Age
                </td>
                <td class="text-muted" data-bind="text: curr_format(expected_age)+' yrs'">
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Age elapsed since purchase
                </td>
                <td class="text-muted" data-bind="text: (moment(<?php echo time();?>,'X').diff(moment(purchase_date, 'YYYY-MM-DD'),'years')+' yrs')">
                </td>
            </tr>
            <tr>
                <td class="no-borders" title="Salvage Value">
                    <i class="fa fa-houzz text-navy"></i>Salvage Value:
                </td>
                <td data-bind="text: curr_format(salvage_value*1)" class="text-muted">
                </td>
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>Type:
                </td>
                <td style="font-weight: bold;" data-bind="text: depre_appre" class="text-muted">
                </td>
            </tr>
            <!-- Assets disposal status-->
             <tr data-bind="visible:  parseInt(status_id) ==parseInt(4)">
                <td class="no-borders" title="Salvage Value">
                    <i class="fa fa-houzz text-navy"></i> Disposal Status:
                </td>
                <td class="no-borders">
               <span class="badge badge-danger text-light" style="background-color: #dc3545;"> Disposed off</span>
                </td>
                <td style="font-weight: bold;" data-bind="text: depre_appre" class="text-muted">
                </td>
            </tr>
            <tr data-bind="visible:  parseInt(depre_appre_id) ==parseInt(1)">
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>Depreciation Method:
                </td>
                <td data-bind="text: method_name" class="text-muted">
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Depreciation Rate
                </td>
                <td class="text-muted" data-bind="text: curr_format(depreciation_rate*1)+'%'">
                </td>
            </tr>
            <tr data-bind="visible:  parseInt(depre_appre_id) ==parseInt(1)">
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>Depreciation Account:
                </td>
                <td class="text-muted">
                    <a title='Click to view account details' data-bind='attr: {href:"<?php echo site_url("accounts/view");?>/" +depreciation_account_id}, text: "["+depreciation_account_code+ "]  "+depreciation_account_name'></a>
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Depreciation Expense Account
                </td>
                <td class="text-muted">
                    <a title='Click to view account details' data-bind='attr: {href:"<?php echo site_url("accounts/view");?>/" +expense_account_id}, text: "["+expense_account_code+ "]  "+expense_account_name '></a>
                </td>
            </tr>
            <tr data-bind="visible:  parseInt(depre_appre_id) ==parseInt(1)">
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>Depreciation Value:
                </td>
                <td data-bind="text: curr_format(cumm_dep*1)" class="text-muted">
                </td>
            </tr>

             <tr data-bind="visible:  parseInt(depre_appre_id) ==parseInt(2)">
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>Appreciation Value:
                </td>
                <td data-bind="text: curr_format(cumm_app*1)" class="text-muted">
                </td>
            </tr>

              <tr data-bind="visible:  parseInt(depre_appre_id) ==parseInt(2)">
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>Appreciation Rate:
                </td>
                <td data-bind="text: appreciation_rate" class="text-muted">
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Appreciation Account
                </td>
                 <td class="text-muted">
                    <a title='Click to view account details' data-bind='attr: {href:"<?php echo site_url("accounts/view");?>/" +appreciation_account_id}, text: "["+appreciation_account_code+ "]  "+appreciation_account_name'></a>
                </td>
            </tr>
            <tr data-bind="visible:  parseInt(depre_appre_id) ==parseInt(2)">
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Appreciation Income Account
                </td>
                <td class="text-muted">
                    <a title='Click to view account details' data-bind='attr: {href:"<?php echo site_url("accounts/view");?>/" +income_account_id}, text: "["+income_account_code+ "]  "+income_account_name '></a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php //$this->load->view('accounts/fixed_asset/add_modal');