<!DOCTYPE html>
<html>

<head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta charset="utf-8">
    <title><?php echo $filename; ?></title>
    <link href="<?php echo base_url(); ?>myassets/css/bootstrapv4.4.1.css" type="text/css" rel="stylesheet" />
</head>
<style>
    .footer {
        position: fixed;
        bottom: 0px;
    }

    .pagenum:before {
        content: counter(page);
    }

    .footer:before {
        content: " ";
        display: block;
        border-bottom: 1px solid #bdbdbd;
    }
    .thumbnail {
      border: 0;
      background: transparent;
     }
</style>

<body>
    <div class="footer">
        <span style="font-size: 12px;">
            <center>Page: <span class="pagenum"></span></center>
        </span>
    </div>
    <table width="100%">
        <tbody>
            <tr>
                <td>
                    <img style="width: 200px;height: 40px;" src="<?php echo base_url('uploads/organisation_' . $_SESSION['organisation_id'] . '/logo/' . $org['organisation_logo']);  ?>" />
                    <br>
                    <span style="font-size: 16px; text-align:left; font-weight: bold;"><?php echo $org['name']; ?> </span><br />
                    <!-- <span  style="font-size: 12px; text-align:left;"><?php echo $org['description']; ?></span><br /> -->
                   
                        <span style="font-size: 12px;">
                        <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?><br />
                        <?php echo $branch['postal_address']; ?><br />
                        <b>Tel:</b> <?php echo $branch['office_phone']; ?><br />
                        <b>Email:</b> <?php echo $branch['email_address']; ?>
                        </span>
                </td>
                 <?php if($_SESSION['id'] == 1){?>
                <td style="float: right;margin:0">
                
               <img src="<?php echo base_url('uploads/client-photo/platin.PNG') ?>"  class="img thumbnail" style="width:110px; height:110px;text-align: right;
                display: block;border:solid 1px #1c84c6 ;padding:10px">
              
               
                </td>
                <?php }?>
            </tr>
        </tbody>
    </table>
    <br>
    <?php echo $pdf_data; ?>
</body>

</html>