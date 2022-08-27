<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>eFMS | Print</title>

        <link href="<?php echo site_url("myassets/css/bootstrap.min.css"); ?>" rel="stylesheet">
        <link href="<?php echo site_url("myassets/font-awesome/css/font-awesome.css"); ?>" rel="stylesheet">

        <link href="<?php echo site_url("myassets/css/animate.css"); ?>" rel="stylesheet">
        <link href="<?php echo site_url("myassets/css/style.css"); ?>" rel="stylesheet">
        <script src="<?php echo base_url("myassets/js/jquery-3.1.1.min.js"); ?>"></script>
        <style type="text/css">
            #invoice-POS{
              box-shadow: 0 0 1in rgba(0, 0, 0, 0.1);
              padding:8mm;
              margin: 0 auto;
              width: 120mm;
              background: #FFF;
              

            }
        </style>
    </head>

    <body class="white-bg">

        <div class="loginColumns animated fadeInDown">
            <div class="row">

         <div class="col-md-12">
            <div class="pull-left"><a href="javascript:window.history.go(-1);" class="btn btn-sm btn-primary"><<< Back</a></div>
            <div class="pull-right"><input type="button" onclick="printDiv('invoice-POS')" value="Print Receipt!" /></div>
                   <!--  ======================= start print design code =======================-->
  <div id="invoice-POS">

    <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                <img alt="image" style="max-height:35px;max-width:200px;"  src="<?php 
                    if (empty($org['organisation_logo'])) {
                        echo base_url('images/avatar.png');
                    } else {
                        echo base_url("uploads/organisation_".$_SESSION['organisation_id']."/logo/".$org['organisation_logo']); } ?>"/>
                    <address>
                        <strong><?php echo $org['name']; ?></strong>
                        <br>
                        <?php echo $trans['branch_name']; ?>
                        <br>
                        <?php echo $trans['physical_address']; ?>
                       
                    </address>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                    <p>
                        <em>Receipt #: <b><?php echo $trans['transaction_no']; ?></b></em>
                         <br>
                          <em>Date: <?php echo date('d  F, Y'); ?></em>
                    <br>
                        <abbr title="Phone">Tel:</abbr> <?php echo $trans['office_phone']; ?>
                    </p>
                </div>
            </div>
<center id="top">
      <!-- <div class="logo"></div> -->
      <div class="info"> 
        <h3><?php if($trans['transaction_type_id']==2){ echo "Deposit Receipt"; } else {
            echo "Withdraw Receipt"; } ?></h3>
      </div><!--End Info-->
    </center><!--End InvoiceTop-->
    <div id="bot"> 
                <table class="table ">
                    <tbody>
                        <tr >
                            <td >Amount</td>
                            <td >&nbsp;UGX &nbsp;&nbsp; <b><?php if($trans['transaction_type_id']==2){ echo number_format($trans['credit'],2); } else {
                                echo number_format($trans['debit'],2);
                            } ?></b></td>
                        </tr>
                        <tr >
                            <td >Account Number</td>
                            <td >&nbsp;<?php echo $trans['account_no']; ?></td>
                        </tr>
                        <tr >
                            <td >Account Name</td>
                            <td >&nbsp;<?php echo $trans['member_name']; ?></td>
                        </tr>
                        <tr >
                            <td >Date</td>
                            <td >&nbsp;<?php echo $trans['transaction_date']; ?></td>
                        </tr>
                        <tr >
                            <td >Naration</td>
                            <td >&nbsp;<?php echo $trans['narrative']; ?></td>
                        </tr>
                        <tr >
                            <td >Staff No.</td>
                            <td >&nbsp;<b><?php echo $trans['staff_no']; ?></b></td>
                        </tr>
                        <tr >
                            <td >Tran Type</td>
                            <td >&nbsp;<?php if($trans['transaction_type_id']==2){ echo "CREDIT"; } else {
                                echo "DEBIT";
                            } ?></td>
                        </tr>
                    </tbody>
                
                </table>

                <div id="legalcopy">
                    <p class="legal"><strong><center>Thank you!</center></strong> 
                    </p>
                </div>

                </div><!--End InvoiceBot-->
                <hr>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $org['name']; ?>
                </div>
                <div class="col-md-6 text-right">
                    <small>© <?php echo date('Y'); ?></small>
                </div>
            </div>
              </div><!--End Invoice-->
<!-- =====================================End Print design code =================================-->

                </div>
               
            </div>
            
        </div>
        <!-- Bootstrap validator script -->
        <script src="<?php echo base_url("myassets/js/plugins/validate/validator.min.js"); ?>"></script>
        <script type="text/javascript">
         function printDiv(divName) {
             var printContents = document.getElementById(divName).innerHTML;
             var originalContents = document.body.innerHTML;
             
             document.body.innerHTML = printContents;

             window.print();

             document.body.innerHTML = originalContents;
        }
        </script>
       
    </body>
</html>
