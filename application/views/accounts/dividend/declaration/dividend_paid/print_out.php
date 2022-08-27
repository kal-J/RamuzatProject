<!DOCTYPE html>
<html>
<head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta charset="utf-8">
    <title><?php echo $filename; ?></title>
    <link href="<?php echo base_url(); ?>myassets/css/bootstrapv4.4.1.css" type="text/css" rel="stylesheet" />
</head>
<body>
<table width="100%" >
    <tbody>
        <tr>
            <td >
            <img style="width: 200px;height: 40px;" src='<?php echo base_url("uploads/organisation_".$_SESSION['organisation_id']."/logo/".$org['organisation_logo']);  ?>' />
            <br>
             <span  style="font-size: 16px; text-align:left; font-weight: bold;"><?php echo $org['name']; ?> </span><br />
            <span  style="font-size: 12px; text-align:left;"><?php echo $org['description']; ?></span><br />
        </span>
            </td>
            <td>
            <span style="float: right; font-size: 12px; ">
            <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?><br />
            <?php echo $branch['postal_address']; ?><br />
            <b>Tel:</b> <?php echo $branch['office_phone']; ?><br/>
            <b>Email:</b> <?php echo $branch['email_address']; ?>
            </td>
        </tr>
    </tbody>
</table>
<br>
<h6 class="text-success"><center><u>DIVIDENDS PAID REPORT</u></center></h6>
<p class="card-text"><center>Financial Year ( <b><?php echo date("d-M-Y",strtotime($fiscal['start_date'])); ?> — <?php echo date("d-M-Y",strtotime($fiscal['end_date'])); ?></b> ) </center></p>
<hr>
<div class="col-lg-12">
    <div class="table-responsive">
         <table class="table  table-bordered table-sm"  width="100%" >
          dividends  <thead>
                <tr> 
                    <th style="font-size: 12px;"> Share A/C NO</th>
                    <th style="font-size: 12px;">Account Name</th>
                    <th style="font-size: 12px;">Dividend/Share (UGX)</th> 
                    <th style="font-size: 12px;">No for Shares</th> 
                    <th style="font-size: 12px;">Dividend Paid (UGX)</th> 
                    <th style="font-size: 12px;">Date Paid</th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dividends as $key => $div) { ?>
                 <tr> 
                    <td style="font-size: 12px;"> <?php echo $div['share_account_no']; ?></td>
                    <td style="font-size: 12px;"><?php echo $div['firstname']." ".$div['lastname']; ?></td>
                    <td style="font-size: 12px;"><?php echo number_format($div['dividend_per_share']); ?></td> 
                    <td style="font-size: 12px;"><?php echo number_format($div['total_amount']/$div['price_per_share']); ?></td> 
                    <td style="font-size: 12px;"><?php echo number_format($div['amount']); ?></td> 
                    <td style="font-size: 12px;"><?php echo date("d-M-Y",strtotime($div['date_paid'])); ?></td> 
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>