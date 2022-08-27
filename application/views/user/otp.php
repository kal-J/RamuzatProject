<?php

  $date=$users['login_time'];
 ?>

<!DOCTYPE html>
<html>
<head>
        <link href="<?php echo site_url("myassets/css/bootstrap.min.css"); ?>" rel="stylesheet">
        <link href="<?php echo site_url("myassets/css/style.css"); ?>" rel="stylesheet">
        <script src="<?php echo base_url("myassets/js/jquery-3.1.1.min.js"); ?>"></script>
         <style type="text/css">
  @import url('https://fonts.googleapis.com/css?family=Raleway:200');

$BaseBG: #0f0f1a;

body, html {
  height: 100%;
  margin: 0;;

  font-family: 'Raleway', sans-serif;
  font-weight: 200}

body {
  background-color: $BaseBG;
  display: flex;
  justify-content: center;
  flex-direction: column;
}
.digit-group1{
   width: 50px;
    font-size: 24px;
    line-height: 50px;
    text-align: center;
    margin: 0 2px;
    height: 50px;
}
.digit-group {
  input {
    width: 50px;
    height: 50px;
    background-color: lighten($BaseBG, 5%);
    border: none;
    line-height: 50px;
    text-align: center;
    font-family: 'Raleway', sans-serif;
    font-weight: 200;
    color: white;
    margin: 0 2px;
  }

  .splitter {
    padding: 0 5px;
    color: white;
    font-size: 24px;
  }
}

.prompt {
  margin-bottom: 20px;
  font-size: 14px;
  color: white;
}
  #counter{
   text-align: center;
  font-size: 14px;
  color:gray;
   }
   .resend_code{
   
    font-weight: bold;
    background:whitesmoke;
    font-family:tahoma;

   }
   #display_message{
    color:#fff;
    padding: 0px;
     font-size: 14px;
     text-align: center;
   }
</style>
</head>
<body style="background-color: #3C89D0;">
<div class="row">
  <div class="col-lg-4 col-md-3 col-sm-12 col-xs-12" >
  </div>
  <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
    <center>
  <div class="prompt">
  Enter the code sent on your <?php echo $form_name; ?> to log in!
</div>

<form method="post" class="digit-group" id="passcode" data-group-name="digits" data-autosubmit="false" autocomplete="off">
  <input class="digit-group1"  type="text" id="digit-1" name="d1" data-next="digit-2" />
  <input class="digit-group1" type="text" id="digit-2" name="d2" data-next="digit-3" data-previous="digit-1" />
  <span class="splitter">&ndash;</span>
  <input class="digit-group1" type="text" id="digit-3" name="d3" data-next="digit-4" data-previous="digit-2" />
  <input class="digit-group1" type="text" id="digit-4" name="d4" data-previous="digit-3" />
  <input type="hidden"  name="email"  value="<?php  echo $users['email'];?>" /> <br/><br>
</form>
  
  <form action="">
  <input type="submit" value="Resend"class="btn btn-default btn-xs" id="resend_code"onclick="resend_code();"/>
  </form>
 <p id="display_message" class="mt-1">Resending code in:&nbsp;<span id="counter" style="color:#fff;" class="mt-1">&nbsp;</span></p>
 
 
</center>
</div>
<div class="col-lg-4 col-md-3 col-sm-12 col-xs-12" >
  </div>
</div>

<script type="text/javascript">
  $('.digit-group').find('input').each(function() {
  $(this).attr('maxlength', 1);
  $(this).on('keyup', function(e) {
    var parent = $($(this).parent());
    
    if(e.keyCode === 8 || e.keyCode === 37) {
      var prev = parent.find('input#' + $(this).data('previous'));
      
      if(prev.length) {
        $(prev).select();
      }
    } else if((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
      var next = parent.find('input#' + $(this).data('next'));
      
      if(next.length) {
        $(next).select();
      } else {
        /* Get from elements values */
       var values = $("#passcode").serializeArray();

       $.ajax({
              url: "verify",
              type: "post",
              data: values ,
              success: function (response) {
                  location.reload();
              },
              error: function(jqXHR, textStatus, errorThrown) {
                 console.log(textStatus, errorThrown);
              }
          });

        // if(parent.data('autosubmit')) {
        //   parent.submit();
        // }
      }
    }
  });
});

//counter 
 
// Set the date we're counting down to

$(function(){

$("#resend_code").hide();
$("#display_message").hide();
  

var countDownDate = new Date("<?php echo $date; ?>").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

    // Get todays date and time
    var now = new Date().getTime();
    
    // Find the distance between now an the count down date
    var distance = countDownDate - now;
    
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
   
    //minute and second prefixes.
    var mprefix =minutes< 10 ?'0':'';
    var sprefix =seconds<10?'0':'';

    document.getElementById("counter").innerHTML = mprefix+minutes +":"+sprefix+seconds;
    
    // If the count down is over, write some text 
     
    if (distance < 0) {
        clearInterval(x);
          $("#counter").hide();
          $("#display_message").hide();
          $("#resend_code").show().addClass('resend_code');
    }
    else{
       $("#display_message").show();
    }
}, 1000);
 



});
 
var resend_code=function(){
var url="<?php echo site_url('browswer/resend_code')?>";
window.location.href=
}
</script>

</body>
</html>