<div class="white-bg ">
<br>
        <ul class="list-group elements-list" style="padding-left:40px;" >
            <li class="list-group-item">
                <a class="nav-link active"  data-toggle="tab" href="#tab-personalinfo" data-bind="click: display_table"> <span class="fa fa-user"></span>  Personal Information
                </a> 
            </li>
            <li class="list-group-item">
                <a class="nav-link "  data-toggle="tab" href="#tab-contact" data-bind="click: display_table"><span class="fa fa-phone"></span>  Contact
                </a> 
            </li>
            <li class="list-group-item">
                <a class="nav-link "  data-toggle="tab" href="#tab-address" data-bind="click: display_table"> <span class="fa fa-address-card"></span>  Address
                </a> 
            </li>
            <?php if ($type == "member") { ?>
                <?php if($org['children_comp']==1){ ?>
                <li class="list-group-item">
                    <a class="nav-link"  data-toggle="tab" href="#tab-children" data-bind="click: display_table"><span class="fa fa-users"></span>  Children
                    </a> 
                </li>
                <?php } } 
                if(($type == "staff") || ($org['nextofkin_comp']==1)){
                ?>
            <li class="list-group-item">
                <a class="nav-link "  data-toggle="tab" href="#tab-kin" data-bind="click: display_table"> <span class="fa fa-users"></span> <?php echo $this->lang->line('cont_nextofkin'); ?>
                </a> 
            </li>
            <?php }  if(($type == "staff") || ($org['employ_hist_comp']==1)){  ?>
            <li class="list-group-item">
                <a class="nav-link"  data-toggle="tab" href="#tab-employment" data-bind="click: display_table"> <span class="fa fa-tasks"></span>  Employment History
                </a> 
            </li>
            <?php } if ($type == "member") { ?>
                <?php if($org['business_comp']==1){ ?>
                <li class="list-group-item">
                    <a class="nav-link"  data-toggle="tab" href="#tab-business" data-bind="click: display_table"><span class="fa fa-briefcase"></span>  Business
                    </a> 
                </li>
                <?php } }  if ($type == "staff") { ?>
                <!-- <li class="list-group-item">
                    <a class="nav-link "  data-toggle="tab" href="#tab-position" data-bind="click: display_table"> <span class="fa fa-snowflake-o"></span>  Position
                    </a> 
                </li> -->
                <li class="list-group-item">
                    <a class="nav-link " data-toggle="tab" href="#tab-role" data-bind="click: display_table"> <span class="fa fa-puzzle-piece"></span>System Role
                    </a> 
                </li>
            <?php } ?>
            <li class="list-group-item">
                <a class="nav-link "  data-toggle="tab" href="#tab-password" data-bind="click: display_table"> <span class="fa fa-key"></span>  Password
                </a> 
            </li>
            <li class="list-group-item">
                <a class="nav-link "  data-toggle="tab" href="#tab-document" data-bind="click: display_table"> <span class="fa fa-file"></span>  Documents
                </a> 
            </li>
            <li class="list-group-item">
                <a class="nav-link "  data-toggle="tab" href="#tab-signature" data-bind="click: display_table"> <span class="fa fa-file"></span>  Signature
                </a> 
            </li>
        </ul>
    </div>
