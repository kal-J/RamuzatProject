<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Sacco FMS - 404 Page Not Found</title>
        <link href="<?php echo base_url("myassets/css/bootstrap.min.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("myassets/css/font-awesome/css/font-awesome.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("myassets/css/animate.css"); ?>" rel="stylesheet">
        <link href="<?php echo base_url("myassets/css/style.css"); ?>" rel="stylesheet">
    </head>
    <body class="gray-bg">
        <div class="middle-box text-center animated fadeInDown">
            <h1>404</h1>
            <h3 class="font-bold"><?php echo $heading; ?></h3>

            <div class="error-desc">
                <p><?php echo $message; ?></p>
                <p>
                    Try checking the URL for error, then hit the refresh button on your browser or try to search for something else in the system.</p>
                <form class="form-inline m-t" role="form">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Search for page">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>
        <!-- Mainly scripts -->
        <script src="<?php echo base_url("myassets/js/jquery-3.1.1.min.js"); ?>"></script>
        <script src="<?php echo base_url("myassets/js/popper.min.js"); ?>"></script>
        <script src="<?php echo base_url("myassets/js/bootstrap.js"); ?>"></script>
    </body>
</html>