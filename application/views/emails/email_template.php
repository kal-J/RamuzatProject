 
  <?php 
     $logo_url =base_url('uploads/organisation_1/logo/ubteb-staff-sacco.jpg');
     $org_site_url='https://maisha.efinanci.com';
     $org_details='Test org';
     $email_template_css= base_url('myassets/css/email-template.css');
     $neededcss_fa= base_url('myassets/font-awesome/css/font-awesome.css');
     $neededcss_bootstrap= base_url('myassets/css/bootstrap.min.css');


  echo  $message_content="
<!DOCTYPE 
<html>
<head>
<link rel='stylesheet' href=".$email_template_css.">
<link rel='stylesheet' href=".$neededcss_fa.">
<link rel='stylesheet' href=".$neededcss_bootstrap.">

</head>
  <body class=''>
    <span class='preheader'>Your 360<sup>.</sup>financial partner.</span>
    <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='body'>
      <tr>
        <td>&nbsp;</td>
        <td class='container'>
          <div class='content'>

            <!-- start of the centered white container -->
            <table role='presentation' class='main'>

              <!-- Template area -->
              <tr>
                <td class='wrapper'>
                  <table role='presentation' border='0' cellpadding='0' cellspacing='0'>
                     <!--headed logo-->
                    <div style='clear:both;'></div>
                     <div style='width:100%;display:absolute;padding:0;text-align:center;font-size:20px;'>
                     <a href=".$org_site_url." target='_blank'><img src=".$logo_url."  style='min-width:100px;width:14%;margin-left:auto;display:block;margin-right:auto;height:auto' alt=''.$org_details.''></a>
                     </div>
                     <!--end of headed logo-->
                    <tr>
                    <td> 
                        
                  <div class='title_container ' style='background-color:skyblue;color:#fff;'>

                   <h3 style='text-align:both;'>&nbsp;<i class='fa fa-exclamation-triangle'style='color:#fff; box-shadow: 0px 0px 2px #fff;'></i>  
                        <!-- title here-->
                        
                        </h3>

                         </div>
                        <br>
                      <!--body begins-->
                       <div class='container'>

                        ".$the_message="<p><br><br>For enquiries, Please contact us on ".$org_details."</p>

                        ".$final_msg="<p>You are receiving this email because you are a member of ".$org_details." If you are no longer interested, please reply to this email.'
                          <p>Best regards.</p>
                          <p>Management</p>
                      
                        </div>
                        <br>
                        <!--end of body-->

                       <!--Footer Begins-->
                       <div  style='background-color:#f5f7fb;;padding:15px;'>
                        <table role='presentation' border='0' cellpadding='0' cellspacing='0'>
                         <tr>
                        <td>
                        <div class='container'>
                        <div class='row'>
                        <div class='col-md-6'>
                       <!-- <img src='.$logo_url.' class='logo'style='width:100px;'/>-->
                         <p>".$org_details."</p>
                          <p>".$org_details."</p>
                         <p>".$org_details."</p>
                        </div>
                         <div class='col-md-6'>
                        <p>Contact Information.</p>
                         
                        <p>Tel. ".$org_details."</p>
                        <p>Email.<a href='mailto':".$org_details." title='Send email'>.".$org_details."</a> </p>
                        <p>Follow our updates on:</p>
                          <span class='apple-link'><i class='fa fa-whatsapp fa-1x'></i></span>
                          <span class='apple-link'><i class='fa fa-twitter fa-1x'></i></span>
                           <span class='apple-link'><i class='fa fa-linkedin fa-1x'></i></span>
                            <span class='apple-link'><i class='fa fa-facebook fa-1x'></i></span>
                        </div>
                        </div>
                        </div>
                        </td>
                      </tr>
                     
                       </table>
                          <hr/>
                      
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              </div>

            <!-- Footer end  -->
            </table>
            <!-- End of template container -->

          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
  </html>
  ";
  ?>
 