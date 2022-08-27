<style>
  td,th{padding: 5px;}
  #top{background-color: red;}
  #header2 th {background:whitesmoke;color:#555;}
  #top th{background: #1c84c6;color:#fff;text-transform: uppercase;}
  #subtotal1 th {background: #F1DABB;color:#555}

 .border_none th { border: none;}
 #top_level_header th{background: #fff;}
  
</style>
<section id="printable_portfolio_aging_pdf_printout">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css"

 <?php 
  //fiscal year 
  $fiscal_year = explode("-",$fiscal_period[0]['start_date']);
  $end_fiscal_year = explode("-",$fiscal_period[0]['end_date']);
?>
<br>
<div class="container-fluid" style="background: #fff;">
<div id="div_portfolio_aging_printout" style="display: none;"></div>
<!--<div class="d-flex flex-row-reverse mx-2 pt-1">
            <a target="_blank" id="active-savings-excel-link">
                <a href="<?php echo base_url()."portfolio_aging/export_to_excel" ?>" class="btn btn-sm btn-primary">
                <i class="fa fa-file-excel-o fa-2x"></i>
                </a>
            </a>&nbsp;
            <button data-bind="visible: !isPrinting_active()" onclick="handlePrint_portfolio_aging()" class="btn btn-sm btn-secondary">
        <i class="fa fa-print fa-2x"></i></button>
        </div>-->
 <div class="col-lg-12">
  <div class="table-responsive">
  <table class="table-bordered display nowrap table-hover" id="tblPortfolio_aging" width="100%;background:#fff;padding-top:-10px;" >
    <thead style="background-color: #eef21d;">
      <tr class="text-center pt-4" id="top" class="border_none">
        <th colspan="7">PORTFOLIO AGING REPORT</th>
      </tr>
      <tr id="top_level_header">
        <th>Organisation Name</th>
        <th colspan="6"><?php echo $org_name[0]['name']?></th>
      </tr>
      <tr id="top_level_header">
        <th>FINANCIAL YEAR</th>
        
        <th colspan="6"><?php echo $fiscal_year[0] == $end_fiscal_year[0] ? $end_fiscal_year[1] :$end_fiscal_year[0];?></th>
      </tr>
      <tr id="top_level_header">
        <th>START DATE</th>
        <th colspan="6"><?php echo  $fiscal_period[0]['start_date']?></th>
      </tr>
      <tr id="top_level_header">
        <th>END DATE</th>
        <th colspan="6"><?php echo $fiscal_period[0]['end_date']?></th>
      </tr>
    </thead>
      <br/>
     
      <tr id="header2">
        <th>Classification(Days)</th>
        <th>Number of Accounts</th>
        <th>Outstanding Loan Portfolio (UGX)</th>
        <th>Required Provision (%)</th>
        <th>Required Provision (UGX)</th>
        <th>Action</th>
      </tr>
      <tbody>
       <?php
        foreach ($all_portfolio_details['data'] as $key=>$value){
            foreach($value as $key1=>$value1){
             
          
               ?>
               <tr id="subtotal1">
              <td><?php echo $key1 ?><?php ?></td>
              <?php
              foreach($value1 as $key2 => $value2){
                $value3 = is_numeric($value2) ? number_format($value2) : $value2;
                ?>
             
                <td><?php echo ($value3)?></td>
                <?php
              }
              ?>
              </tr>
              <?php

            }
        }
       
       ?>
         
        <tr id="subtotal1">
        <th>Sub Total</th>
        <th><?php echo number_format($all_portfolio_details['sub_total_level1_num_acc']) ?></th>
        <th><?php echo number_format($all_portfolio_details['sub_total_level1_outstanding_loan_portfolio']) ?></th>
        <th>
        </th>
        <th><?php echo  number_format($all_portfolio_details['sub_total_level1_required_provision_amount']) ?></th>
        <th>
           <?php
          $sub_total = $all_portfolio_details['sub_total_level1_required_provision_amount'];
          $provision_loan_loss_account_id = $all_portfolio_details['provision_loan_loss_account_id'];
          $asset_account_id = $all_portfolio_details['asset_account_id'];
          if($sub_total > 0){
              $url = base_url(). 'Portfolio_aging/renderProvisonModal/'.$sub_total.'/'.$provision_loan_loss_account_id.'/'.$asset_account_id;
              echo "<a data-required_provision_amount='".$sub_total."'data-provision-loan-loss-account-id='".$provision_loan_loss_account_id."' data-asset-account-id='".$asset_account_id."' data-href='".$url."' data-toggle='modal' class='btn btn-sm openBtn' title='Provision this loan amount' style='font-size:12px;color:forestgreen;'>Provision</a>";
          }
          ?>
        </th>
        </tr> 
        <tr class="text text-center" id="top"><th colspan="6">Rescheduled or Reclassified loans</th></tr>
        <!-- another -->
        <?php
        foreach ($all_portfolio_details_2['data'] as $key=>$value){
            foreach($value as $key1=>$value1){
               ?>
               <tr id="subtotal1">
              <td><?php echo $key1; ?></td>
              <?php
              foreach($value1 as $key2 => $value2) {
                $value3 = is_numeric($value2) ? number_format($value2) : $value2;
                ?>
                <td><?php echo $value3; ?></td>
                <?php
              }

              ?>
              </tr>
              <?php

            }
        }
       
       ?>
    
        <tr id="subtotal1">
        <th>Sub Total</th>
        <th><?php echo number_format($all_portfolio_details_2['sub_total_level1_num_acc']) ?></th>
        <th><?php echo number_format($all_portfolio_details_2['sub_total_level1_outstanding_loan_portfolio']) ?></th>
        <th></th>
        <th><?php echo  number_format($all_portfolio_details_2['sub_total_level1_required_provision_amount']) ?></th>

        <th>
          <?php
          $sub_total = $all_portfolio_details_2['sub_total_level1_required_provision_amount'];
          $provision_loan_loss_account_id = $all_portfolio_details_2['provision_loan_loss_account_id'];
          $asset_account_id = $all_portfolio_details_2['asset_account_id'];
          if($sub_total > 0){
              $url = base_url(). 'Portfolio_aging/renderProvisonModal/'.$sub_total.'/'.$provision_loan_loss_account_id.'/'.$asset_account_id;
              echo "<a data-required_provision_amount='".$sub_total."'data-provision-loan-loss-account-id='".$provision_loan_loss_account_id."' data-asset-account-id='".$asset_account_id."' data-href='".$url."' data-toggle='modal' class='btn btn-sm openBtn' title='Provision this loan amount' style='font-size:12px;color:forestgreen;'>Provision</a>";
          }
          ?>
        </th>

        </tr> 
        <tr id="subtotal1">
        <th>Grand Total</th>
        <th><?php echo number_format($all_portfolio_details['sub_total_level1_num_acc']+$all_portfolio_details_2['sub_total_level1_num_acc']) ?></th>
        <th><?php echo number_format($all_portfolio_details['sub_total_level1_outstanding_loan_portfolio']+$all_portfolio_details_2['sub_total_level1_outstanding_loan_portfolio']) ?></th>
        <th></th>
        <th colspan="2"><?php echo  number_format($all_portfolio_details['sub_total_level1_required_provision_amount']+$all_portfolio_details_2['sub_total_level1_required_provision_amount']) ?></th>
        </tr> 

        <tr id="subtotal1">
        <th style='background:lightgreen;'>Net Portfolio</th>
        
        <th colspan="5" style='background:lightgreen;'><?php echo  number_format($all_portfolio_details['total_disbursed_loan_amount'] - ($all_portfolio_details['sub_total_level1_required_provision_amount']+$all_portfolio_details_2['sub_total_level1_required_provision_amount']) ) ?></th>
        </tr> 
  
    </tbody>
  </table> <br><br>
</section>
 