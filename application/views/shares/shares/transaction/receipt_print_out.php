<?php 
    $logo_url;
    //  print_r(json_encode($trans));
    //  die;
    

if (empty($org['organisation_logo'])) {
    $logo_url = base_url('images/avatar.png');
} else {
    $logo_url = base_url("uploads/organisation_" . $_SESSION['organisation_id'] . "/logo/" . $org['organisation_logo']);
}

?>

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
    <script src="<?php echo base_url("myassets/js/bootstrap.js"); ?>"></script>
    <script src="<?php echo base_url("myassets/js/popper.min.js"); ?>"></script>
    <script src="<?php echo site_url("myassets/js/qz-tray.js"); ?>"></script>

    <style type="text/css">
    #invoice-POS {
        box-shadow: 0 0 1in rgba(0, 0, 0, 0.1);
        padding: 8mm;
        margin: 0 auto;
        width: 120mm;
        background: #FFF;}
    </style>
</head>

<body class="white-bg">

    <div class="loginColumns animated fadeInDown">
        <div class="row">

            <div class="col-md-12">
                <div class="pull-left"><a href="javascript:window.history.go(-1);" class="btn btn-sm btn-primary">
                        <<< Back</a>
                </div>
                <div class="pull-right"><input data-toggle="modal" data-target="#printerModal" type="button" onclick="thermalPrinter()"  value="Print Receipt!" />
                </div>
                <!--  ======================= start print design code =======================-->
                <div id="invoice-POS">

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <img alt="image" style="max-height:35px;max-width:200px;" src="<?php echo $logo_url ?>" />

                            <address>
                                <strong><?php echo $org['name']; ?></strong>
                                <br>
                                <?php echo $branch['branch_name']; ?>
                                <br>
                                <?php echo $branch['physical_address']; ?>

                            </address>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                            <p>
                                <em>Date: <?php echo date('d  F, Y'); ?></em>
                                <br>
                                <abbr title="Phone">Tel:</abbr> <?php echo $branch['office_phone']; ?>
                            </p>
                        </div>
                    </div>
                    <center id="top">
                        <!-- <div class="logo"></div> -->
                        <div class="info">
                            <h3> <?php echo '# Receipt '.$trans['transaction_no']; ?> </h3>
                        </div>
                        <!--End Info-->
                    </center>
                    <!--End InvoiceTop-->
                    <div id="bot">
                        <table class="table ">
                            <tbody>
                                <tr>
                                    <td>Share acc.</td>
                                    <td>
                                        &nbsp; <b>
                                        <?php echo $trans['share_account_no'];?>
                                        </b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Trans Type</td>
                                    <td>
                                        &nbsp; <b>
                                        <?php echo $trans['type_name'];?>
                                        </b>
                                    </td>
                                </tr>

                                <?php if($trans['debit']) { ?>

                                    <tr>
                                        <td>Debit</td>
                                        <td>
                                            &nbsp;shs. &nbsp; <b>
                                                <?php echo number_format($trans['debit']);?>
                                            </b>
                                        </td>
                                    </tr>

                                <?php } ?>
                                <?php if($trans['credit']) { ?>

                                    <tr>
                                        <td>Credit</td>
                                        <td>
                                            &nbsp;shs. &nbsp; <b>
                                                <?php echo number_format($trans['credit']);?>
                                            </b>
                                        </td>
                                    </tr>

                                <?php } ?>

                                <tr>
                                    <td>Payment Mode</td>
                                    <td>
                                        &nbsp; <b>
                                        <?php echo $trans['payment_mode'];?>
                                        </b>
                                    </td>
                                </tr>
 
                                <tr>
                                    <td>Client</td>
                                    <td>&nbsp; <?php echo $trans['firstname'] . ' ' . $trans['lastname'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td>&nbsp;<?php echo $trans['transaction_date']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Naration</td>
                                    <td>&nbsp;<?php echo $trans['narrative']; ?></td>
                                </tr>
                                
                                
                            </tbody>

                        </table>

                        <div id="legalcopy">
                            <p class="legal"><strong>
                                    <center>Thank you!</center>
                                </strong>
                            </p>
                        </div>

                    </div>
                    <!--End InvoiceBot-->
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $org['name']; ?>
                        </div>
                        <div class="col-md-6 text-right">
                            <small>© <?php echo date('Y'); ?></small>
                        </div>
                    </div>
                </div>
                <!--End Invoice-->
                <!-- =====================================End Print design code =================================-->

            </div>

        </div>

    </div>

  

<!-- Modal -->
<div class="modal fade" id="printerModal" tabindex="-1" role="dialog" aria-labelledby="printerModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Select Printer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-group">
              <label for="printers">Select Printer</label>
              <div id="loading-printers" class="d-flex justify-content-center align-items-center">
                  <span class="spinner-border mr-2" role="status"></span> fetching printers...
              </div>
              <select class="form-control" name="printers" id="printers">
              </select>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button id="print" type="button" class="btn btn-primary">Print</button>
      </div>
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

    <script>

    // Print to a local thermal printer
    const thermalPrinter = () => {
        const transaction_type = "<?php echo $trans['type_name']; ?>";
        const transaction_no = "<?php echo $trans['transaction_no']; ?>";
        const share_account_no = "<?php echo $trans['share_account_no']; ?>";
        const payment_mode = "<?php echo $trans['payment_mode']; ?>";
        const transaction_date = "<?php echo $trans['transaction_date']; ?>";
        let debit;
        let credit;

        <?php if($trans['debit']) { ?>
            debit = "<?php echo number_format($trans['debit']);?>";
        <?php } ?>

        <?php if($trans['credit']) { ?>
            credit = "<?php echo number_format($trans['credit']);?>";
        <?php } ?>

        const client_name = "<?php echo $trans['firstname'] . ' '. $trans['lastname']; ?>";

        let print_data = {
            transaction_type,
            transaction_no,
            share_account_no,
            payment_mode,
            transaction_date,
            debit,
            credit,
            client_name,
            org_name: '<?php echo $org['name']; ?>',
            branch_name: '<?php echo $branch['branch_name']; ?>',
            physical_address: '<?php echo $branch['physical_address']; ?>',
            current_date: '<?php echo date('d  F, Y'); ?>',
            office_phone: '<?php echo $branch['office_phone']; ?>',
            narrative: '<?php echo $trans['narrative']; ?>',

        };

        $('#printerModal').show();

        qz.websocket.connect().then(() => {
            qz.printers.find().then(printers => {
                printers.forEach(printer => {
                    $('#loading-printers').css('display', 'none').css('visibility', 'hidden');
                    
                    $('#printers').css('display', 'block').css('visibility', 'visible');

                  document.getElementById('printers').innerHTML += `
                <option value="${printer}">${printer}</option>
                `;
                

                });
                
            }).catch(err => console.log(err));
            
        }).catch(err => console.log(err));

    const htmlFormatedData = `<div
        style="width: 219px; display: flex; margin: auto; flex-direction: column; font-size: 12px">
        <div>
            ${print_data.org_name}
        </div>
        <div>
            ${print_data.branch_name} | ${print_data.physical_address}
        </div>
        <div style="margin-bottom: 1em; ">
            Tel : ${print_data.office_phone}
        </div>

        <div>
            <h3 style="margin: 0;">
                <strong>
                    # Receipt: ${print_data.transaction_no}
                </strong>
            </h3>
            
        </div>
        <div>
            <small>
                Date : ${print_data.current_date}
            </small>

        </div>

        <hr style="margin: 1em 0; height: 4px">

        <div style="font-weight: bold;">
            <div style="display: flex; margin-bottom: 0.5em;">
                <span style="width: 60px;">
                    Share acc.
                </span>
                <span>: ${print_data.share_account_no}</span>
            </div>
            <div style="display: flex; margin-bottom: 0.5em;">
                <span style="width: 60px;">
                    Trans type
                </span>
                <span>: ${print_data.transaction_type}</span>
            </div>

            ${print_data.credit ? `
                <div style="display: flex; margin-bottom: 0.5em;">
                <span style="width: 60px;">
                    Credit
                </span>
                <span>: shs. ${print_data.credit}</span>
            </div>

                ` : ''}

            ${print_data.debit ? `
                <div style="display: flex; margin-bottom: 0.5em;">
                <span style="width: 60px;">
                    Debit
                </span>
                <span>: shs. ${print_data.debit}</span>
            </div>

                ` : ''}

            <div style="display: flex; margin-bottom: 0.5em;">
                <span style="width: 60px;">
                    Pay Mode
                </span>
                <span>: ${print_data.payment_mode}</span>
            </div>

            <div style="display: flex; margin-bottom: 0.5em;">
                <span style="width: 60px;">
                    Client
                </span>
                <span>: ${print_data.client_name}</span>
            </div>

            <div style="display: flex; margin-bottom: 1em;">
                <span style="width: 60px;">Date</span>
                <span>: <small>${print_data.transaction_date}</small>
                </span>
            </div>

            <div style="display: flex; margin-bottom: 0.5em;">
                <span style="width: 60px;">Narration</span>
                <span>: ${print_data.narrative}</span>
            </div>

        </div>


        <hr style="margin: 1em 0; height: 4px">

        <div style="display: flex; justify-content: center; margin-bottom: 1em;">
            <strong>
                Thank You.
            </strong>

        </div>

        <div>
            ${print_data.org_name}
        </div>
    </div>`;

        document.getElementById('print').addEventListener('click', () => {
            if(!$('#printers').val()) {
                return;
            }
            const printer = $('#printers').val();

            const config = qz.configs.create(printer);

            qz.print(config, [{
                type: 'pixel',
                format: 'html',
                flavor: 'plain',
                data: htmlFormatedData,
            }]).then(() => {
                $('#printerModal').modal('hide');
                //return qz.websocket.disconnect();
            }).catch(err => console.log(err));
        });       

    }

    $(document).ready(() => {
        $('#printers').css('display', 'none').css('visibility', 'hidden');
    });
    </script>


</body>

</html>