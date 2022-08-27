   <?php 
    $logo_url =base_url('uploads/organisation_1/logo/efinanci-logo.jpg');
    $twitter_logo= base_url('images/icon-twitter.png');
    $fb_logo= base_url('images/icon-fb.png');
    $whatsapp_logo= base_url('images/icon-whatsapp.png');
    $org_site_url='https://maisha.efinanci.com';
     $org_details='Test org';
     $the_message;
     $final_message;
     $org_details = array(
      'name' => 'E-Financi ,Your 360 financial partner',
      'site_url'=>'https://efinanci.com'
      ); 

     $branch_details = array(
                      'office_phone' => '0779947306' ,
                      'physical_address'=>'Bukoto Street Plot 102',
                      'postal_address'=>'P.O.Box 2767, Kampala',
                      'email_address'=>'support@efinanci.com',
    );
     $message="Bank Deposit";
    
   echo  $message_template = '
  <div style="width:100%;background-color:#fafbfc;height:100%;">
   <div style="width:620px; margin:auto;text-align:justify;margin-left:1px solid #CCC;background-color:#ffffff;font-family: arial,sans-serif;
    font-size:24px;color:#102231;">&nbsp;
                   <div align="center" style="background-color:#fff;padding:0px; width:49%%;padding:1px;">
                           <a href="' .$org_site_url . '"><img width="40%"  src="' . $logo_url . '" alt="' . $org_details['name'] . '"></a>
                     
                    </div><br>
                  
                     <table  width="100%%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#3498db;padding:30px 40px 20px 40px">
                    <tbody style="background-color:#3498db;padding:30px 40px 20px 40px">
                    <tr>
                    <td>
                    <div style="width:80%%;background-color:#3498db;color:#fff;text-align:center;padding:0px;line-height: 0px;font-weight: lighter;" >
                      <h3 style="font-size:19px;padding:5px;font-family:sans-serif;line-height:middle">
                      '.$subject=$message.'</h3>
                    </div>
                      </div>
                    </tbody>
                    </table>
                     
                     <div style="width:620px; margin:auto;text-align:justify;margin-left:1px solid #CCC;background-color:#ffffff;font-family: arial,sans-serif;font-size: 80%;color:#102231;padding:1px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);font-size:18px;">
                    
                   <table class="col-620" width="100%%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#fff;padding:30px 40px 20px 40px">
                    <tbody>
                    <tr style="font-family: arial,sans-serif;font-size:18px;color:#102231;line-height:20px;margin:0 25px 25px 0;background-color:#ffffff;">
                    <td>
                     <p style="color:#525c65;font-size:18px;font-family: arial,sans-serif">
                     %s 
                     </p>
                     <p>
                      '.$the_message=$message."<br><br>For enquiries, Please contact us on ".$branch_details['office_phone'] .'</p>
                     <p>Best regards.</p> 
                     <p>Management</p>
                      </td>
                      </tr>
                  
                    </tbody>
                    </table>
                    </div>


                     <div style="width:620px; margin:auto;text-align:justify;margin-left:1px solid #CCC;background-color:#ffffff;font-family: arial,sans-serif;font-size: 80%;color:#102231;padding:1px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0.3, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);font-size:18px;">
                    
                    <table class="col-600" width="100%%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color: rgb(245, 247, 249);padding:30px;">
                    <tbody>
                    <tr>
                    <td style="font-family: arial,sans-serif;font-size:16px;color:#102231;line-height:20px;">
                     
                   <p style="margin-bottom:4px;margin-top: 18px;"><strong>' . $org_details['name'] . '</strong><br/> 
                    ' . $branch_details['physical_address'] . '<br/>
                    ' . $branch_details['postal_address'] . '</p>
                    <p style="margin-bottom:4px;margin-top: 12px;">
                    Tel: ' . $branch_details['office_phone'] . '<br/>
                    Email: <a href="mailto:' . $branch_details['email_address'] . '" title="Send email">' . $branch_details['email_address'] .'</p>
                   </td>
                   </tr>
                    </tbody>
                    </table>
                    </div>

                      <div style="width:620px; margin:auto;text-align:justify;margin-left:1px solid #CCC;background-color:#ffffff;font-family: arial,sans-serif;font-size: 80%;color:#102231;padding:1px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);font-size:18px;">
                    
                    <table class="col-600" width="100%%" border="0" align="center" cellpadding="0" cellspacing="0" style=" ;padding:30px;color:#fff;background-color: rgb(125, 151, 173);">
                    <tbody>
                    <tr>
                    <td style="font-family: arial,sans-serif;font-size:12px;color:#fff;">
                    '.$final_msg="<p><small>You are receiving this email because you are a member of ".$org_details['name']." If you are no longer interested, please reply to this email.";'</small></p>
                    <p>%s</p>
                   </td>
                   </tr>
                    </tbody>
                    </table>
                    </div>
                  
                    
                
                   
                    
                   </div>
                </div>';