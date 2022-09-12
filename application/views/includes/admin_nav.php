<?php 
if(empty($this->session->userdata('id'))){
    redirect('welcome');
} 
//$this->session->set_userdata('page_url',  current_url());
$data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
$data['payment_engine'] = $this->payment_engine_model->get($this->session->userdata('organisation_id'));

$data['active_year']=$this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
 
 
if(empty($data['module_list'])){
    redirect('welcome/logout/4');
} else {
    $modules =array_column($data['module_list'],"module_id");
}

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
                                    <span class="text-muted text-xs block"><?php echo $_SESSION['role']; ?><b class="caret"></b></span>
                                </a>
                                <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                    <li><a class="dropdown-item" href="<?php echo site_url("staff/staff_data/").$_SESSION['staff_id']; ?>">View Profile</a></li>
                                    <li class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo site_url("welcome/logout"); ?>">Logout</a></li>
                                </ul>
                            </div>
                            <div class="logo-element">
                                FMS
                            </div>
                        </li>
                        <li class="<?php  echo ($this->uri->segment(1) == 'dashboard')?'active':'';?>">
                            <a href="<?php echo site_url("dashboard"); ?>"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span></a>
                        </li>
                    
                     <?php if(in_array('6', $modules)){
                      ?>
                        <?php if(!isset($data['payment_engine']['payment_id'])){?>
                            <li class="<?php  echo ($this->uri->segment(1) == 'Savings_account')?'active':'';?>">
                                <a href="<?php echo site_url("Savings_account");?>"><i class="fa fa-database"></i> <span class="nav-label">Savings  </span></a>
                            </li>
                        <?php }else{ ?>
                          <li class="<?php  echo ($this->uri->segment(1) == 'Savings_account')?'active':'';?>">
                                <a href="#"><i class="fa fa-database"></i> <span class="nav-label">Savings  </span><span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <li >
                                        <a href="<?php echo site_url("Savings_account");?>"><i class="fa fa-database"></i> <span class="nav-label">Savings Account</span></a>
                                    </li>
                                   
                                    <li>
                                        <a href="<?php echo site_url("Savings_account/mobile_deposits"); ?>"><i class="fa fa-users"></i> <span class="nav-label">Mobile Deposit</span></a>
                                    </li>
                                </ul>
                            </li>
                          <?php }
                        } if(in_array('4', $modules)){ ?>
                        <li class="<?php  echo ($this->uri->segment(1) == 'client_loan')?'active':'';?>">
                            <a href="#"><i class="fa fa-users"></i> <span class="nav-label">Loans  </span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li>
                                    <a href="<?php echo site_url("client_loan"); ?>"><i class="fa fa-user"></i> <span class="nav-label">Individual Loans</span></a>
                                </li>
                                <?php if(in_array('14', $modules)){ ?>
                                <li>
                                    <a href="<?php echo site_url("group_loan"); ?>"><i class="fa fa-users"></i> <span class="nav-label">Group Loans</span></a>
                                </li>
                                 <?php } ?>
                                <li>
                                    <a href="<?php echo site_url("loan_collateral"); ?>"><i class="fa fa-cubes"></i> <span class="nav-label">Collateral Mgt</span></a>
                                </li>
                                <?php if(in_array('11', $modules)){ ?>
                                <!--<li>-->
                                <!--    <a href="<?php // echo site_url("loan_reversal"); ?>"><i class="fa fa-cubes"></i> <span class="nav-label">Loan Reversals</span></a>-->
                                <!--</li>-->
                                <?php } ?>
                            </ul>
                        </li>

                        <li class="<?php  echo ($this->uri->segment(1) == 'loan_reports' || $this->uri->segment(1) == 'portfolio_aging')?'active':'';?>">
                            <a href="#"><i class="fa fa-line-chart"></i> <span class="nav-label">Loan Reports  </span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li>
                                    <a href="<?php echo site_url("loan_reports"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">In-Arrears</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url("portfolio_aging"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">In-Arrears By Age</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url("loan_reports/written_off"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Written Off</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url("loan_reports/member_loan_history"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Repayment History</span></a>
                                </li>
                                
                                
                                
                            </ul>
                        </li>

                      <?php }  if(in_array('8', $modules)){  ?>
                    
                        <li class="<?php  echo (($this->uri->segment(1) == 'accounts')||($this->uri->segment(1) == 'fees')||($this->uri->segment(1) == 'inventory')||($this->uri->segment(1) == 'journal_transaction'))?'active':'';?>">
                            <a href="#"><i class="fa fa-money"></i> <span class="nav-label">Accounts </span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li>
                                    <a href="<?php echo site_url("accounts"); ?>"><i class="fa fa-list"></i> <span class="nav-label">Accounts & Entries</span></a>
                                </li>   
                                <?php  if(in_array('21', $modules)){  ?>
                                <li>
                                    <a href="<?php echo site_url("fees"); ?>"><i class="fa fa-credit-card-alt"></i> <span class="nav-label">Fees</span></a>
                                </li>
                                <?php } ?>
                                <li>
                                    <a href="<?php echo site_url("inventory"); ?>"><i class="fa fa-cubes"></i> <span class="nav-label">Assets</span></a>
                                </li>

                            </ul>
                        </li>
                         <?php } if((in_array('2', $modules)) && (in_array('13', $modules))){ ?>
                        <li  class="<?php  echo (($this->uri->segment(1) == 'member')||($this->uri->segment(1) == 'group'))?'active':'';?>">
                            <a href="#"><i class="fa fa-users"></i> <span class="nav-label"><?php echo $this->lang->line('cont_client_name_p');?> </span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li>
                                    <a href="<?php echo site_url("member"); ?>"><i class="fa fa-user"></i> <span class="nav-label">Individual <?php echo $this->lang->line('cont_client_name_p');?></span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url("group"); ?>"><i class="fa fa-users"></i> <span class="nav-label">Groups/Companies</span></a>
                                </li>
                               
                            </ul>
                        </li>
                      <?php } else {
                       if(in_array('2', $modules)){
                             ?>
                                <li  class="<?php  echo ($this->uri->segment(1) == 'member')?'active':'';?>">
                                    <a href="<?php echo site_url("member"); ?>"><i class="fa fa-user"></i> <span class="nav-label"><?php echo $this->lang->line('cont_client_name_p');?></span></a>
                                </li>
                                <?php }   if(in_array('13', $modules)){ ?>
                                <li  class="<?php  echo ($this->uri->segment(1) == 'group')?'active':'';?>">
                                    <a href="<?php echo site_url("group"); ?>"><i class="fa fa-users"></i> <span class="nav-label">Groups</span></a>
                                </li>
                      <?php } } 
                        if(in_array('10', $modules)){ ?>
                        <li class="<?php  echo ($this->uri->segment(1) == 'reports')?'active':'';?>">
                            <a href="#"><i class="fa fa-line-chart"></i> <span class="nav-label">Reports  </span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li>
                                    <a href="<?php echo site_url("reports"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">General Reports</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url("RevenuePerformance"); ?>">
                                    <i class="fa fa-line-chart"></i>
                                     <span class="nav-label">Revenue Reports</span></a>
                                </li>
                              

                            <?php  if(in_array('26', $modules)){ ?>
                                <li>
                                    <a href="<?php echo site_url("till"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Cash Register</span></a>
                                </li>
                            <?php  } ?>
                                <li>
                                    <a href="<?php echo site_url("reports/loans"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Loan Reports</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url("reports/accounts"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Account Reports</span></a>
                                </li>
                                <?php if(in_array('6', $modules)){ ?>
                                 <li>
                                    <a href="<?php echo site_url("reports/savings"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-lab el">Savings Reports</span></a>
                                </li>
                                <?php } ?>
                                  <li>
                                    <a href="<?php echo site_url("reports/assets"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Assets Reports</span></a>
                                </li>
                                
                                <li>
                                    <a href="<?php echo site_url("SummaryReports"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Daily Reports</span></a>
                                </li>
                                   <li>
                                    <a href="<?php  echo site_url("reports/receivables"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Aging Receivables</span></a>
                                </li> 
                                <li>
                                    <a href="<?php  echo site_url("portfolio_aging"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Portfolio Aging </span></a>
                                </li> 
                                <li>
                                    <a href="<?php echo site_url("ActivityLogs"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Activity Logs</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url("FinancialReturns"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Financial Returns</span></a>
                                </li>
                                
                               <!--  <li>
                                    <a href="<?php //echo site_url("reports/payables"); ?>"><i class="fa fa-line-chart"></i> <span class="nav-label">Aging Payables</span></a>
                                </li> -->

                               
                            </ul>
                        </li>
                    <?php } if(in_array('12', $modules)){ ?>
                         <li class="<?php  echo ($this->uri->segment(1) == 'shares')?'active':'';?>">
                            <a href="<?php echo site_url("shares"); ?>"><i class="fa fa-money"></i> <span class="nav-label">Shares</span></a>
                        </li>

                        <?php  } if(in_array('1', $modules)){ ?>
                        <li class="<?php  echo ($this->uri->segment(1) == 'staff')?'active':'';?>">
                            <a href="<?php echo site_url("staff");?>"><i class="fa fa-user"></i> <span class="nav-label">Staff</span></a>
                        </li>
                      <?php }  if(in_array('11', $modules)){ ?>
                        <li class="<?php  echo ($this->uri->segment(1) == 'setting')?'active':'';?>">
                            <a href="<?php echo site_url("setting"); ?>"><i class="fa fa-cogs"></i> <span class="nav-label">Settings</span></a>
                        </li>
                         <?php }  if(in_array('25', $modules)){ ?>
                        <li class="<?php  echo ($this->uri->segment(1) == 'billing')?'active':'';?>">
                            <a href="<?php echo site_url("billing"); ?>"><i class="fa fa-money"></i> <span class="nav-label">Billing</span></a>
                        </li>
                        <?php }  if((in_array('11', $modules)) &&($_SESSION['mystatus']==9)){ ?>
                        <li class="<?php  echo ($this->uri->segment(1) == 'organisation')?'active':'';?>">
                            <a href="<?php echo site_url("organisation"); ?>"><i class="fa fa-cog"></i> <span class="nav-label">Organisation Settings</span></a>
                        </li>
                        <?php }  ?>
                    </ul>

                </div>
            </nav>
            <div id="page-wrapper" class="gray-bg">
                <div class="row border-bottom">
                    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">                   

                    <div class="col-md-8 navbar-header">
                            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                            <div class="row wrapper white-bg page-heading" style="min-width:600px;padding-top:5px;">
                               
                                <div class="col-md-12 col-sm-12" style="padding-top:5px; text-align: center !important;">
                                    <a href="#modalSearch" data-toggle="modal" data-target="#modalSearch">
                                        <span id="searchGlyph" class="glyphicon glyphicon-search"></span> <span class="hidden-sm hidden-md hidden-lg"><?php echo $this->lang->line('cont_client_name');?> Search</span>
                                    </a>                         
                                </div>

                            </div>
                        </div>

                        <ul class="nav navbar-top-links navbar-right">
                            <li>
                                <span class="m-r-sm text-muted welcome-message">Welcome to <?php echo $_SESSION['org_name']; ?></span>
                            </li>
                           <!--  <li class="dropdown">
                                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                    <i class="fa fa-envelope"></i>  <span class="label label-warning">0</span>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                    <i class="fa fa-bell"></i>  <span class="label label-primary">0</span>
                                </a>
                              
                            </li>  -->
                            <li>
                                <a href="<?php echo site_url("welcome/logout"); ?>">
                                    <i class="fa fa-sign-out"></i> Log out
                                </a>
                            </li>
                           <!--  <li>
                                <a class="right-sidebar-toggle">
                                    <i class="fa fa-tasks"></i>
                                </a>
                            </li> -->
                        </ul>

                    </nav>
                    <?php if($data['active_year']['close_status']==0){ ?>
                    <div class ="label label-warning" style="width: 100%;"><center><b>You are now browsing a closed Financial year. </b> &nbsp; &nbsp;&nbsp; New entries are not recommended</center></div>
                    <?php } else if($data['active_year']['end_date'] < date('Y-m-d')){ ?>
                    <div class ="label label-danger" style="width: 100%;"><center><b>Your financial year End Date has passed, </b> &nbsp; &nbsp;&nbsp; <a href="<?php echo site_url("accounts");?>" class="btn btn-xs btn-flat btn-success">Close the Financial Year</a></center></div>
                    <?php } else { } ?>
                   <!-- //=================NOTIFICATION======================== -->
                   <?php // $this->load->view('alert'); ?>

                </div>
                    <?php
                    // This is the main content partial
                    echo $this->template->content;
                    ?>

                
                <?php $this->view('locked'); ?>
                </div>
                
<div id="modalSearch" class="modal inmodal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
   <div class="modal-dialog  modal-md"> <!-- modal-lg -->

       <!-- Modal content-->
       <div class="modal-content">
            <form role="search"  action="search_results.html">
            <div class="modal-header">
                <h4 class="modal-title">Search for a <?php echo $this->lang->line('cont_client_name');?>.</h4>
                <small class="font-bold">Note: You can search by <?php echo $this->lang->line('cont_client_name');?> Name, <?php echo $this->lang->line('cont_client_name');?> No, Phone number or Email <span class="text-danger">*</span></small>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-body" style="padding: 20px 10px 0px 10px;">
                    <div class="form-group">
                        <input type="text" placeholder="Search from here..." autofocus class="form-control form-rounded" name="top-search" id="top-search">
                    </div>  
                    <div  id="result">
                    
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form> 

   </div>
</div>
</div>
