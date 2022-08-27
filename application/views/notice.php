 <style>
   #demo{
   text-align: center;
  font-size: 30px;
  color:green;
   }
   	.content {
 
  background-color:#f8f8f8;
    -webkit-box-shadow:0 0 15px rgba(0,0,0,0.3);
  -moz-box-shadow: 0 0 15px rgba(0,0,0,0.3);
  -o-box-shadow: 0 0 15px rgba(0,0,0,0.3);
  box-shadow: 0 0 15px rgba(0,0,0,0.3);
}
   </style>
<div class="container">
  		<div class="row">
		<br>
  			<div class="col-xs-12 col-lg-12 col-md-12 col-md-offset-2 content" style ="height:500px;">
 <h2><center>Financial management System (FMS) <center></h2> <HR><BR><BR>
 <h3><center>Temporarily Down for Maintenance</center></h3>
 <center>We are performing scheduled maintenance. We should be back in </center>
 <br/>
<p id="demo"></p>

<script>
// Set the date we're counting down to
var countDownDate = new Date("May 10, 2019 17:00:00").getTime();

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
    
    // Output the result in an element with id="demo"
    document.getElementById("demo").innerHTML =hours + " hour(s)  "
    + minutes + " min   " + seconds + " sec  ";
    
    // If the count down is over, write some text 
    if (distance < 0) {
        clearInterval(x);
       // document.getElementById("demo").innerHTML = "redirecting....";
		//window.location = "https://system.rapidrfs.com";
    }
}, 1000);
</script> <br>
 <center><b>We apologize for any inconvenience</b></center>
 <h2><center><a href="https://gmtconsults.com/">GMT Consults Ltd</a><h2>
</div>
</div>
</div>
</body>
</html>
