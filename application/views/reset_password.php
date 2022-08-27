<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title><?php echo $org['name']; ?> | Login</title>

        <link href="<?php echo site_url("myassets/css/bootstrap.min.css"); ?>" rel="stylesheet">
        <link href="<?php echo site_url("myassets/font-awesome/css/font-awesome.css"); ?>" rel="stylesheet">

        <link href="<?php echo site_url("myassets/css/animate.css"); ?>" rel="stylesheet">
        <link href="<?php echo site_url("myassets/css/style.css"); ?>" rel="stylesheet">
        <link href="<?php echo site_url("myassets/css/custom.1.0.css"); ?>" rel="stylesheet">
        <script src="<?php echo base_url("myassets/js/jquery-3.1.1.min.js"); ?>"></script>

    </head>
    <body class="gray-bg">

        <div class="loginColumns animated fadeInDown">
            <div class="row login-row-container">

                <div class="col-md-6">
                   <h1 class="font-bold login-title"><?php echo $org['name']; ?></h1>
            
                    <h2 class="medium-title"><?php echo $org['description']; ?></h2>
                </div>
                <div class="col-md-6">
                    <div class="ibox-content">
                <div class="col-md-12 center_col">
                   <img alt="logo" img style="width:95%;"  src="<?php 
                    if (!empty($org['organisation_logo'])) {
                        echo base_url('uploads/organisation_1/logo/'.$org['organisation_logo']);
                    } else { echo base_url("uploads/organisation_1/logo/".$org['organisation_logo']); } ?>"/>
                        </div>
                        <h1 style="text-align:center;"><i class="fa fa-unlock-alt"></i>&nbsp;&nbsp;&nbsp; Reset password </h1>
                        <?php echo $this->session->flashdata('message'); ?>
                        <form id="formLogin" class="m-t" role="form" action="<?php echo site_url("welcome/password_reset_request"); ?>" method="post">
                            <div class="form-group">
                                <div class="input-group m-b">
                                    <div class="input-group-prepend">
                                        <span class="input-group-addon fa fa-user-circle"></span>
                                    </div>
                                    <input type="text" name="username" class="form-control" placeholder="Email address" required="required">
                                    <?php echo form_error('username'); ?>
                                </div>
                            </div>
                           
                            <button type="submit" class="btn btn-primary block full-width m-b" >Submit</button>
                            <!-- <a href="#" class="pull-right" >
                                <small>Forgot password?</small>
                            </a> -->
                             <a href="<?php echo site_url("welcome"); ?>" class="pull-left login" >
                                <span style="font-weight: bold;">Login? Click here </span>
                            </a>

                        </form>
                        <p class="m-t">
                       
                        <p>&nbsp;</p>
                    </div>
                </div>
            </div>
            <hr style="background-color:#E8F0FE;">
            <div class="row">
                <div class="col-md-6">
                   <h3 class="font-bold float-left " ><small>Powered by <a href="http://gmtconsults.com">GMT Consults LTD</a></small></h3>
                </div>
                <div class="col-md-6 text-right">
                    <span>efinanci v2.2 © <?php echo date('Y'); ?> </span>
                </div>
            </div>
        </div>
        <!-- Bootstrap validator script -->
        <script src="<?php echo base_url("myassets/js/plugins/validate/validator.min.js"); ?>"></script>

        <script src="<?php echo base_url("myassets/js/bootstrap.js"); ?>"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('form#formLogin').validator();
                window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, 4000);
            });
        </script>
    </body>
</html>