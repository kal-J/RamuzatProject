<?php if(empty($this->session->userdata('id'))){
    redirect('welcome');
} 
$data['savings_module']=$this->organisation_model->get_module_access(6,$this->session->userdata('organisation_id'));
$data['loan_module']=$this->organisation_model->get_module_access(4,$this->session->userdata('organisation_id'));
?>
  <nav class="navbar-default navbar-static-side" role="navigation" style="background-color: #d54735">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <img alt="image" style="max-height:90px;" class="rounded-circle" 
                    src="<?php 
                            if (empty($_SESSION['photograph'])) {
                                echo base_url('images/avatar.png');
                            } else {
                                echo base_url("uploads/organisation_".$_SESSION['organisation_id']."/user_docs/profile_pics/".$_SESSION['photograph']); } ?>"/>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold"><?php echo $this->session->userdata('firstname')." ".$this->session->userdata('lastname'); ?></span>
                        <span class="text-muted text-xs block">Client <b class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="dropdown-item" href="<?php echo site_url("u/profile")?>">View Profile</a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo site_url("welcome/logout"); ?>">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    FMS
                </div>
            </li>
        
            <li class="<?php  echo ($this->uri->segment(2) == 'home')?'active':'';?>">
                <a href="<?php echo site_url("u/home"); ?>"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span></a>
            </li>
            <?php
                if(!empty($data['savings_module'])){
             ?>
            <li class="<?php  echo ($this->uri->segment(2) == 'savings')?'active':'';?>">
                <a href="<?php echo site_url("u/savings");?>"><i class="fa fa-database"></i> <span class="nav-label">Savings Account</span></a>
            </li>
            <?php
                }
                if(!empty($data['loan_module'])){
             ?>
           <li class="<?php  echo ($this->uri->segment(2) == 'loans')?'active':'';?>">
                <a href="<?php echo site_url("u/loans"); ?>"><i class="fa fa-user"></i> <span class="nav-label">Loans</span></a>
            </li>
            <?php
                }
             ?>
           <!--  <li>
                <a href="<?php //echo site_url("u/loans"); ?>"><i class="fa fa-users"></i> <span class="nav-label">Group Loans</span></a>
            </li> -->
                
             <li class="<?php  echo ($this->uri->segment(2) == 'shares')?'active':'';?>">
                <a href="<?php echo site_url("u/shares"); ?>"><i class="fa fa-money"></i> <span class="nav-label">Shares</span></a>
            </li>
             <li class="<?php  echo ($this->uri->segment(2) == 'profile')?'active':'';?>">
                <a href="<?php echo site_url("u/profile")?>"><i class="fa fa-user"></i> <span class="nav-label">My Profile </span></a>
            </li>

           
        </ul>

    </div>
</nav>

<div id="page-wrapper" class="gray-bg">
    <div class="row border-bottom">
        <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                <div class="wrapper border-bottom white-bg page-heading" style="min-width:500px;padding-top:10px;">

                    <span style="font-weight:bold; color:gray; padding-left:10px; font-size:14px;"><?php echo $title; ?></span>
                  
                </div>
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <span class="m-r-sm text-muted welcome-message"><?php echo $_SESSION['org_name']; ?></span>
                </li>
              <!--   <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope"></i>  <span class="label label-warning">0</span>
                    </a>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell"></i>  <span class="label label-primary">0</span>
                    </a>
                  
                </li> -->


                <li>
                    <a href="<?php echo site_url("welcome/logout"); ?>">
                        <i class="fa fa-sign-out"></i> Log out
                    </a>
                </li>
              <!--   <li>
                    <a class="right-sidebar-toggle">
                        <i class="fa fa-tasks"></i>
                    </a>
                </li> -->
            </ul>

        </nav>
    </div>
    
        <?php
        // This is the main content partial
        echo $this->template->content;
        ?>

    <div class="footer">
        <div class="float-right">
            Powered by <strong><?php echo sprintf("<a href='%s' title='ICT Department' target='_blank'>GMT Consults LTD</a>", "http://gmtconsults.com") ?> </strong> <?php echo ("&copy; ".  date('Y')); ?>
        </div>
           <!-- <strong>
                <?php
                // Show the footer partial, and prepend copyright message
                //echo (date('Y') . " &copy;");
                ?>
            </strong> -->
    </div>
    <?php $this->view('locked'); ?>
    </div>