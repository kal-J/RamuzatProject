<?php
  if($this->agent->is_browser('Chrome') && $this->agent->version() > 80){
  }elseif ($this->agent->is_browser('Firefox') && $this->agent->version() > 75) {
  }elseif ($this->agent->is_browser('Edge') && $this->agent->version() > 80) {
  }else{
    redirect('browser');
  }
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?php echo $_SESSION['org_name']; ?> -  <?php echo $this->template->title->default("Financial Management System"); ?></title>
        <meta charset="utf-8">
        <meta name="description" content="<?php echo $this->template->description; ?>">
        <meta name="author" content="">
        <link rel="shortcut icon" href="<?= base_url() ?>/uploads/organisation_1/logo/fav.jpg" />
        <?php echo $this->template->meta; ?>
       <?php 
       $this->load->library('Cache'); 
       $headerCache = new Cache();
        $headerCache->runCache("header.php");
        if ($headerCache->isValid()) {
           echo $headerCache->readCache();
        }else { 
            ?>
            <link href="<?php echo base_url("myassets/css/bootstrap.min.css"); ?>" rel="stylesheet">
            <link href="<?php echo base_url("myassets/font-awesome/css/font-awesome.css"); ?>" rel="stylesheet">
            <link href="<?php echo base_url("myassets/css/animate.css"); ?>" rel="stylesheet">
            <!-- Toastr style -->
            <link href="<?php echo base_url("myassets/css/plugins/toastr/toastr.min.css"); ?>" rel="stylesheet">
            <!-- Sweet Alert -->
            <!-- <link href="<?php //echo base_url("myassets/css/plugins/sweetalert/sweetalert.css"); ?>" rel="stylesheet"> -->
            <link type="text/css" rel="stylesheet" href="<?php echo base_url("myassets/css/plugins/cropping/croppie.css");?> "/>
            <!-- Datatables style -->
            <link href="<?php echo base_url("myassets/css/plugins/dataTables/datatables.min.css"); ?>" rel="stylesheet">
            <!-- Datepicker style -->
            <link href="<?php echo base_url("myassets/css/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet">
            <!-- Mainly scripts -->
            <script src="<?php echo base_url("myassets/js/jquery-3.1.1.min.js"); ?>"></script>
           
            <link href="<?php echo base_url("myassets/css/style.css"); ?>" rel="stylesheet">
            <link href="<?php echo base_url("myassets/css/custom.css"); ?>" rel="stylesheet">
            <link href="<?php echo base_url("myassets/css/custom.1.0.css"); ?>" rel="stylesheet">
            <!-- Sweet alert -->
            <script src="<?php echo base_url("myassets/js/node_modules/sweetalert2/dist/sweetalert2.all.min.js"); ?>"></script>

            <style>            
                .table-responsive .table > tbody > tr > td{
                    border-top: 1px solid #e7eaec !important;
                    line-height: 1.52857 !important;
                    padding: 1px 0.5px !important;
                    vertical-align: middle !important;
                    /*font-size: 1.2em !important;*/
                }
                .table-row-color{
                    background-color: #1c84c6 !important;
                }
                .reportrange{
                    background: #fff;
                    cursor:pointer;
                    padding: 5px 10px;
                    border: 1px solid #ccc
                }

                .overlay{
                    display: none;
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    top: 0;
                    left: 0;
                    z-index: 3000;
                    background: rgba(0,0,0,0.7) url(<?=base_url()?>images/loader.gif) center no-repeat;
                }
                
                /* Turn off scrollbar when body element has the loading class */
                body.loading{
                    overflow: hidden;   
                }
                /* Make spinner image visible when body element has the loading class */
                body.loading .overlay{
                    display: block;
                }
                .swal2-container {
                  z-index: 2200;
                }
            </style>
            <?php 
            $cache_header = ob_get_contents();
            // Save it to the cache for next time
            $headerCache->createCacheFile($cache_header);
        }
        echo $this->template->stylesheet; 
        ?>

    </head>
    <body class="fixed-sidebar"> 
     <div class="overlay"></div>
        <div id="wrapper">
          <?php
          if($_SESSION['curr_interface']=="staff"){
           $this->view('includes/admin_nav');
          }else {
           $this->view('includes/client_nav');
          }
          ?>
          
        </div>

        <?php 
            $footerCache = new Cache();
            $footerCache->runCache("footer.php");
            if ($footerCache->isValid()) {
             echo $footerCache->readCache();
        }else { 
        ?>
        <script src="<?php echo base_url("myassets/js/popper.min.js"); ?>"></script>
        <script src="<?php echo base_url("myassets/js/bootstrap.js"); ?>"></script>
        <script src="<?php echo base_url("myassets/js/plugins/metisMenu/jquery.metisMenu.js"); ?>"></script>
        <script src="<?php echo base_url("myassets/js/plugins/slimscroll/jquery.slimscroll.min.js"); ?>"></script>

        <!-- Custom and plugin javascript -->
        <script src="<?php echo base_url("myassets/js/inspinia.js"); ?>"></script>
        <script src="<?php echo base_url("myassets/js/plugins/pace/pace.min.js"); ?>"></script>

       
        <!-- jQuery UI -->
        <script src="<?php echo base_url("myassets/js/plugins/jquery-ui/jquery-ui.min.js"); ?>"></script>

        <!-- Data tables -->
        <script src="<?php echo base_url("myassets/js/plugins/dataTables/datatables.min.js"); ?>"></script>
        <script src="<?php echo base_url("myassets/js/plugins/dataTables/dataTables.bootstrap4.min.js"); ?>"></script>
        
        <!-- Toastr script -->
        <script src="<?php echo base_url("myassets/js/plugins/toastr/toastr.min.js"); ?>"></script>
        <!-- Bootstrap validator script -->
        <script src="<?php echo base_url("myassets/js/plugins/validate/validator.min.js"); ?>"></script>
        <!-- Moment JScript -->
        <script src="<?php echo base_url("myassets/js/plugins/moment/moment.min.js"); ?>"></script>
        <!-- Moment JScript -->
        <script src="<?php echo base_url("myassets/js/plugins/datepicker/bootstrap-datepicker.js"); ?>"></script>
        <!-- Knockout Jscript -->
        <script src="<?php echo base_url("myassets/js/plugins/knockout/knockout-3.4.2.js"); ?>"></script>
        <?php 
         $cache_footer = ob_get_contents();
        // Save it to the cache for next time
         $footerCache->createCacheFile($cache_footer);
        }
        echo $this->template->javascript; ?>

        <?php $this->view('includes/helpers'); ?>
    </body>
</html>
