  
  <?php 
     $logo_url =           base_url('uploads/organisation_1/logo/efinanci-logo.jpg');
     $bg_cover_url =       base_url('uploads/organisation_1/logo/efinanci_bg.png');
     $neededcss_bootstrap= base_url('myassets/css/bootstrap.min.css');
     //$jquery=              base_url('myassets/js/jquery-3.3.1.min.js');
     $custom_email=        base_url('myassets/css/custom_email.css');
     $neededcss_fa=        base_url('myassets/font-awesome/css/font-awesome.css');
     $form_action=         base_url('examplecontroller/save_emails');
     $base_url=            base_url();

  echo  $custom_template="
<!DOCTYPE 
<html>
<head>
<link rel='stylesheet' href=".$neededcss_bootstrap.">
<link rel='stylesheet' href=".$custom_email.">
<link rel='stylesheet' href=".$neededcss_fa.">

</head>
<br>
<br>
<div class='container bootdey'>
<div class='email-app'>
   <nav>
   <a href=".$base_url."><img src=".$logo_url." style='width:100%;' class='logo'></a>
        <a href='#' class='btn btn-primary btn-block'>Compose</a>
        <ul class='nav'>
           
            <li class='nav-item'>
                <a class='nav-link' href='#'><i class='fa fa-rocket'></i> Sent</a>
            </li>
            
        </ul>
    </nav> 
    <main>
        
        <form action=".$form_action." method='post' id='customEmails'>
          <div class='form-row mb-3'>
                <label for='to' class='col-2 col-sm-1 col-form-label'>From:</label>
                <div class='col-10 col-sm-11'>
                    <input type='email' name='sender' id='sender' class='form-control' id='to' placeholder='Sender' required>
                </div>
            </div>
            <div class='form-row mb-3'>
                <label for='to' class='col-2 col-sm-1 col-form-label'>To:</label>
                <div class='col-10 col-sm-11'>
                    <input type='email' name='receiver' id='receiver' class='form-control' id='to' placeholder='Reciever'required>
                </div>
            </div>
            <div class='form-row mb-3'>
                <label for='cc' class='col-2 col-sm-1 col-form-label'>CC:</label>
                <div class='col-10 col-sm-11'>
                    <input type='email' name='copy_to' id='copy_to' class='form-control' id='cc' placeholder='Copy to email'>
                </div>
            </div>
            <div class='form-row mb-3'>
                <label for='bcc' class='col-2 col-sm-1 col-form-label'>Subject:</label>
                <div class='col-10 col-sm-11'>
                    <input type='text' class='form-control' id='subject' name='subject' placeholder='Email subject' required>
                </div>
            </div>
       
        <div class='row'>
            <div class='col-sm-11 ml-auto'>
                <div class='toolbar' role='toolbar'>
                    <div class='btn-group'>
                       
                   
                       
                    <button type='button' id='attach_file' class='btn btn-light'>
                        <span class='fa fa-paperclip fa-1x'></span>
                        
                    </button>&nbsp;
                     <input type='file' class='form-control col-10 col-sm-11' id='attachment' name='attachment' placeholder='Email subject'>

                 
                        <div class='dropdown-menu'>
                            <a class='dropdown-item' href='#'>add label <span class='badge badge-danger'> Home</span></a>
                            <a class='dropdown-item' href='#'>add label <span class='badge badge-info'> Job</span></a>
                            <a class='dropdown-item' href='#'>add label <span class='badge badge-success'> Clients</span></a>
                            <a class='dropdown-item' href='#'>add label <span class='badge badge-warning'> News</span></a>
                        </div>
                    </div>
                </div>
                <div class='form-group mt-4'>
                    <textarea class='form-control' id='message' name='message' rows='12' placeholder='Click here to compose'required></textarea>
                </div>
                <div class='form-group'>
                    <button type='submit' class='btn btn-primary'>Send</button>
                    <button   id='reset' class='btn btn-danger'>Discard</button>
                </div>
            </div>
        </div>
        </form>
    </main>
</div>
</div>

";?>
<script src="<?php echo base_url('myassets/js/jquery-3.3.1.js')?>"></script>
<script>
 $(document).ready(function() {

  $('#addEmail').click(function() {
    var adem = $('#preAlertEmail').val();
    var inph = '<input type="hidden" name="email[]" value="'+adem+'" /><b>'+adem+'</b><br />';
    $('#inph').append(inph);
  });
  $('#attachment').hide();
  $('#attach_file').on('click',function(){
    $('#attachment').show();
  });
  //resets form feilds.
  $("#reset").click(function() {
    $(this).closest('form').find("input[type=text], textarea").val("");
});
});
</script>