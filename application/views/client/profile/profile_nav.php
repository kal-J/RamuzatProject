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
                <?php if($org['children_comp']==1){ ?>
                <li class="list-group-item">
                    <a class="nav-link"  data-toggle="tab" href="#tab-children" data-bind="click: display_table"><span class="fa fa-users"></span>  Children
                    </a> 
                </li>
                <?php } 
                if($org['nextofkin_comp']==1){
                ?>
            <li class="list-group-item">
                <a class="nav-link "  data-toggle="tab" href="#tab-kin" data-bind="click: display_table"> <span class="fa fa-users"></span>  Next of Kin
                </a> 
            </li>
            <?php }  if($org['employ_hist_comp']==1){  ?>
            <li class="list-group-item">
                <a class="nav-link"  data-toggle="tab" href="#tab-employment" data-bind="click: display_table"> <span class="fa fa-tasks"></span>  Employment History
                </a> 
            </li>
                <?php  } if($org['business_comp']==1){ ?>
                <li class="list-group-item">
                    <a class="nav-link"  data-toggle="tab" href="#tab-business" data-bind="click: display_table"><span class="fa fa-briefcase"></span>  Business
                    </a> 
                </li>
                <?php } ?>
             
            <li class="list-group-item">
                <a class="nav-link "  data-toggle="tab" href="#tab-password" data-bind="click: display_table"> <span class="fa fa-key"></span>  Change Password
                </a> 
            </li>
            <li class="list-group-item">
                <a class="nav-link "  data-toggle="tab" href="#tab-document" data-bind="click: display_table"> <span class="fa fa-file"></span>  Documents
                </a> 
            </li>
        </ul>
    </div>
