<?php
$start_date = date('d-m-Y', strtotime($fiscal_active['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_active['end_date']));
?>

  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2> <?php echo $title; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo base_url('u/home')?>">Home</a>
            </li>
            <li class="breadcrumb-item active">
                <strong><?php echo $title; ?></strong>
            </li>
        </ol>
    </div>
    
</div>
<div class="row white-bg">
 <div class="col-lg-12">
    <br>
     <div class="panel-title">
        <center><h3 style="font-weight: bold;">Share Accounts</h3></center>
    </div>
    <div class="table-responsive">
               <table class="table table-striped table-bordered table-hover dataTables-example" id="tblActive_client_share" style="width: 100%">
            <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Share A/C No (UGX)</th>
                  <th>Price Per Shares (UGX)</th>
                  <th>Total Amount (UGX)</th>
                  <th>Paid Amount (UGX)</th>
                  <th>Action</th>
                </tr>
            </thead>
          <tbody>
        </tbody>
      <tfoot>
          <tr>
              <th></th>
              <th >Totals</th>
              <th>0</th>
              <th>0</th>
              <th>0</th>
              <th colspan="3">&nbsp;</th>
          </tr>
      </tfoot>
      </table>
    </div>

</div>
</div>
<script>
    var dTable = {};
    var response='';
    var optionSet1 = {}
    var displayed_tab = '';
    var client_loanModel = {}, TableManageButtons;
</script>