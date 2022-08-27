<div class="panel-body">
    <div>
        <strong><?php echo $title; ?> details</strong> 
        <?php if (in_array('3', $accounts_privilege)) { ?>
                <!--a data-toggle="modal" href="#add_asset-modal"  class="btn btn-sm btn-primary pull-right"><i class="fa fa-pencil"></i> Update</a-->
        <?php } ?>
    </div>
    <table class="table table-stripped  m-t-md">
        <tbody data-bind="with: bill_detail">
            <tr>
                <td>
                    <i class="fa fa-houzz text-navy"></i> Bill REF
                </td>
                <td data-bind="text: ref_no" class="text-muted">
                </td>
                <td class="no-borders">
                    <i class="fa fa-briefcase text-navy"></i>Supplier Account:
                </td>
                <td class="text-muted">
                    <a title='Click to view account details' data-bind='attr: {href:"<?php echo site_url("accounts/view");?>/" +liability_account_id}, text: "["+account_code+ "]  "+account_name'></a>
                </td>
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-user-circle text-navy"></i>Supplier/Vendor
                </td>
                <td class="text-muted" colspan="3">
                    <a title='Click to view details' data-bind='attr: {href:"<?php echo site_url("supplier/view");?>/" +supplier_id}, text: supplier_names'></a>
                </td>
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-calendar text-navy"></i> Billing date
                </td>
                <td class="text-muted" data-bind="text: moment(billing_date, 'YYYY-MM-DD').format('D-MMM-YYYY')">
                </td>
                <td class="no-borders">
                    <i class="fa fa-calendar text-navy"></i> Due date
                </td>
                <td class="text-muted" data-bind="text: moment(due_date, 'YYYY-MM-DD').format('D-MMM-YYYY')">
                </td>
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-money text-navy"></i>Bill Amount:
                </td>
                <td data-bind="text: curr_format(total_amount*1)" class="text-muted">
                </td>
                <td class="no-borders">
                    <i class="fa fa-money text-navy"></i>Paid Amount:
                </td>
                <td data-bind="text: curr_format(amount_paid*1)" class="text-muted">
                </td>
            </tr>
            <tr>
                <td>
                    <i class="fa fa-hashtag text-navy"></i> Description
                </td>
                <td colspan="3" class="text-muted" data-bind="text: description">
                </td>
            </tr>
            <tr>
                <td class="no-borders">
                    <i class="fa fa-image text-navy"></i>Attachment
                </td>
                <td class="text-muted">
                    <img data-bind="attr:{src: '<?php echo base_url();?>/' +attachment_url}" title="attachment"class="img-md"/>
                </td>
                <td class="no-borders">
                    <i class="fa fa-hashtag text-navy"></i> Balance
                </td>
                <td class="text-muted">
                    <span data-bind="text: curr_format(total_amount*1 - amount_paid*1)"></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php //$this->load->view('accounts/fixed_asset/add_modal');