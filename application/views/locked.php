<div id="myLockscreen" class="modal lockmodal fade" tabindex="-1"  role="dialog">
<div class="modal-dialog lockmodal-dialog" style="max-width:300px;">
	<div class="modal-content">
	<div class="text-center lockscreen animated fadeInDown bg-gray-light lockscreen">
            <div class="m-b-md">
            <img alt="image" style="max-height:100px;" class="rounded-circle circle-border" src="<?php 
                                        if (empty($_SESSION['photograph'])) {
                                            echo base_url('images/avatar.png');
                                        } else {
                                        echo base_url("uploads/organisation_".$_SESSION['organisation_id']."/user_docs/profile_pics/".$_SESSION['photograph']); }?>">
            </div>
            <h3><?php echo $this->session->userdata('firstname')." ".$this->session->userdata('lastname'); ?></h3>
            <p class="text-maroon"><b>Locked</b> due to inactivity, enter your password to <b>Unlock</b>.</p>
            <form method="post" autocomplete="off"  class="formValidate" action="<?php echo site_url("welcome/unlock");?>" id="formWelcome">
                <div class="form-group">
                       <input type="password" name="password" required class="form-control" placeholder="Password" autofill="off">
                    <span  class="help-block with-errors" aria-hidden="true"></span>
                </div>
                <button id="btn-submit" type="submit" class="btn btn-primary block full-width save_data"><i class="fa fa-unlock"></i> Unlock</button>
            </form>
            <p style="padding-top:10px;"><a href="<?php echo site_url("welcome/logout"); ?>">Login with a different account</a></p>
           
    </div>
	</div>
 </div>
</div>
<script>
$(document).ready(function () {
        $('form#formWelcome').validator().on('submit', saveData);
});
</script>