<div class="panel-body">
    <div>
        <strong>Declaration Details</strong> 
        <?php if (in_array('3', $accounts_privilege)) { ?>
                <!--a data-toggle="modal" href="#add_asset-modal"  class="btn btn-sm btn-primary pull-right"><i class="fa fa-pencil"></i> Update</a-->
        <?php } ?>
    </div>
    <table class="table table-stripped  m-t-md">
        <tbody data-bind="with: dividend_declaration">
            <tr>
                <td>
                    <i class="fa fa-houzz text-navy"></i>  Type
                </td>
                <td data-bind="text: (cash_stock==1)?'Cash':'Stock'" class="text-muted">
                </td>
                <td rowspan="2">
                    <i class="fa fa-hashtag text-navy"></i> Description
                </td>
                <td class="text-muted" data-bind="text: notes">
                </td>
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i> Paid To
                </td>
                <td class="text-muted" >
                    <span data-bind="visible:  parseInt(paying_ordinary_sh) ==parseInt(1), text:'Ordinary Shareholders'"></span> ,
                    <span data-bind="visible: parseInt(paying_preference_sh) ==parseInt(1),text:'Cumulative Prefence Shareholders'"></span>

                </td>
                
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>  Total Dividends
                </td>
                <td data-bind="text: curr_format(total_dividends*1)" class="text-muted">
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Dividends Per Share
                </td>
                <td class="text-muted" data-bind="text: curr_format(dividend_per_share*1)">
                </td>
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>  Declaration Date:
                </td>
                <td data-bind="text: moment(declaration_date, 'YYYY-MM-DD').format('D-MMM-YYYY')" class="text-muted">
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i>  Date of Record
                </td>
                <td class="text-muted" data-bind="text: moment(record_date, 'YYYY-MM-DD').format('D-MMM-YYYY')">
                </td>
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Date of Payment
                </td>
                <td class="text-muted" data-bind="text: moment(payment_date, 'YYYY-MM-DD').format('D-MMM-YYYY')">
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Status
                </td>
                <td class="text-muted" data-bind="text:(status_id==1)?'Unpaid':'Paid Out'">
                </td>
            </tr>
           
              <tr>
                <td class="no-borders">
                    <i class="fa fa-houzz text-navy"></i>Fund Source Account:
                </td>
                <td class="text-muted">
                     <a title='Click to view account details' data-bind='attr: {href:"<?php echo site_url("accounts/view");?>/" +dividends_cash_acc_id}, text: "["+dc_acc_code+ "]  "+dc_acc_name'></a>
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Retained Earnings Account
                </td>
                 <td class="text-muted">
                    <a title='Click to view account details' data-bind='attr: {href:"<?php echo site_url("accounts/view");?>/" +retained_earnings_acc_id}, text: "["+re_acc_code+ "]  "+re_acc_name'></a>
                </td>
            </tr>
            <tr >
                <td class="no-borders" >
                    <i class="fa fa-houzz text-navy"></i>Dividends Payable Account:
                </td>
                <td class="text-muted">
                    <a title='Click to view account details' data-bind='attr: {href:"<?php echo site_url("accounts/view");?>/" +dividends_payable_acc_id}, text: "["+dp_acc_code+ "]  "+dp_acc_name'></a>
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Attachment
                </td>
                <td class="text-muted">
                    <a title='Click to view attached file' data-bind='attr: {href:"<?php echo site_url();?>uploads/organisation_<?php echo $_SESSION['organisation_id'];?>/accounts/dividend_declaration/" +attachment_url}, text: attachment_url '></a>
                </td>

            </tr>
        </tbody>
    </table>
</div>
