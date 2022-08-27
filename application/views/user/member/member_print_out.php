
<div class="col-lg-12">
    <div  class="table-responsive">
         <table class="table table-sm table-bordered" width="100%">
            <tbody >
                <tr style="background-color:#1c84c6;">
                     <td colspan="2">
                         <span style="font-size: 16px;color: #fff;font-weight: bold;"><center><?php echo $user['firstname']." ".$user['lastname'];?></center></span>
                    </td>
                </tr>
            
                </tbody>
        </table>
        <table class="table table-sm  table-stripped ">
            <tbody style="font-size: 12px;">
                <tr>
                    <td><strong>Name</strong></td>
                    <td colspan="3" ><?php echo $user['salutation']." ".$user['firstname']." ".$user['lastname']." ".$user['othernames'];?></td>
                </tr>
                <tr>
                    <td><strong>Gender</strong></td>
                    <td ><?php echo ($user['gender']==1)?'Male':'Female';?></td>
                    <td><strong>Date of Birth</strong></td>
                    <td > <?php echo $user['date_of_birth']?date("d-M-Y",strtotime($user['date_of_birth'])):"None";?></td>
                </tr>
                <tr>
                    <td><strong>Client No.</strong></td>
                    <td colspan="3" ><?php echo $user['client_no']?$user['client_no']:"None";?></td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td colspan="3"><?php echo $user['email']?$user['email']:"None";?></td>
                </tr>
                <tr>
                    <td><strong>Marital Status</strong></td>
                    <td colspan="3" ><?php echo $user['marital_status_name']?$user['marital_status_name']:"None";?></td>
                </tr>
                <?php //if($org['children_comp']==1){ ?>
                <tr>
                    <td><strong>Children</strong></td>
                    <td > <?php echo $user['children_no']?$user['children_no']:"None";?></td>
                    <td><strong>Dependants</strong></td>
                    <td > <?php echo $user['dependants_no']?$user['dependants_no']:"None";?></td>
                </tr>
                <?php //} ?>
                <tr>
                    <td><strong>Disability</strong></td>
                    <td ><?php echo ($user['disability']==1)?'Yes':'No';?></td>
                    <td><strong>CRB Card Number</strong></td>
                    <td><?php echo $user['crb_card_no']?$user['crb_card_no']:"None";?></td> 
                </tr>
                <tr>
                    <td><strong>Ocupation</strong></td>
                    <td ><?php echo $user['occupation']?$user['occupation']:"None";?></td>

                    <td><strong>NIN</strong></td>
                    <td  ><?php echo $user['nid_card_no']?$user['nid_card_no']:"None";?></td>

                </tr>
                <tr>
                    <td><strong>Subscription Plan</strong></td>
                    <td colspan="3"><?php echo $user['plan_name']?$user['plan_name']:"None";?></td>
                </tr>
                <tr>
                    <td><strong>Comment</strong></td>
                    <td colspan="3"><?php echo $user['comment'] ?></td>
                </tr>
            </tbody>
        </table>
            <table class="table table-sm table-bordered" width="100%">
            <tbody >
                <tr class="table-warning">
                     <td colspan="2">
                         <span style="font-size: 15px;font-weight: bold;">Contact Details</span>
                    </td>
                </tr>
                <tr >
                     <th >
                         <span style="font-size: 13px;"><center>Phone Number</center></span>
                    </th>
                     <th >
                         <span style="font-size: 13px;"><center>Contact Type</center></span>
                    </th>
                </tr>
                <?php foreach ($contact as $key => $value) { ?>
                 <tr >
                     <td >
                         <span style="font-size: 13px;"><center><?php echo $value['mobile_number']; ?></center></span>
                    </td>
                   
                    <td >
                         <span style="font-size: 13px;"><center><?php echo $value['contact_type']; ?></center></span>
                    </td>
                </tr>
                <?php } ?>

                </tbody>
        </table>
          <table class="table table-sm table-bordered" width="100%">
            <tbody >
                <tr class="table-warning">
                     <td colspan="5">
                         <span style="font-size: 15px;font-weight: bold;">Address</span>
                    </td>
                </tr>
                <tr >
                     <th >
                         <span style="font-size: 13px;"><center>Plot</center></span>
                    </th>
                     <th >
                         <span style="font-size: 13px;"><center>Road/Street</center></span>
                    </th>
                     <th >
                         <span style="font-size: 13px;"><center>Location</center></span>
                    </th>
                    <th >
                         <span style="font-size: 13px;"><center>Type</center></span>
                    </th>
                </tr>
                <?php foreach ($address as $key => $value) { ?>
                 <tr >
                     <td >
                         <span style="font-size: 13px;"><center><?php echo $value['address1']; ?></center></span>
                    </td>
                    <td >
                         <span style="font-size: 13px;"><center><?php echo $value['address2']; ?></center></span>
                    </td>
                    <td >
                         <span style="font-size: 13px;"><center><?php echo $value['district'].", ".$value['subcounty'].", ".$value['parish']." ".$value['village'];?></center></span>
                    </td>
                     <td >
                         <span style="font-size: 13px;"><center><?php echo $value['address_type_name']; ?></center></span>
                    </td>
                </tr>
                <?php } ?>

                </tbody>
        </table>
        <table class="table table-sm table-bordered" width="100%">
            <tbody >
                <tr class="table-warning">
                     <td colspan="6">
                         <span style="font-size: 15px;font-weight: bold;">Next Of Kin</span>
                    </td>
                </tr>
                <tr >
                     <th >
                         <span style="font-size: 13px;"><center>Name</center></span>
                    </th>
                     <th >
                         <span style="font-size: 13px;"><center>Gender</center></span>
                    </th>
                     <th >
                         <span style="font-size: 13px;"><center>Relationship</center></span>
                    </th>
                    <th >
                         <span style="font-size: 13px;"><center>Address</center></span>
                    </th>
                    <th >
                         <span style="font-size: 13px;"><center>Telphone</center></span>
                    </th>
                    <th >
                         <span style="font-size: 13px;"><center>Share Portion</center></span>
                    </th>
                </tr>
                <?php foreach ($nextofkin as $key => $value) { ?>
                 <tr >
                     <td >
                         <span style="font-size: 13px;"><center><?php echo $value['firstname']." ".$value['lastname']." ".$value['othernames'];?></center></span>
                    </td>
                    <td >
                         <span style="font-size: 13px;"><center><?php echo $value['gender']; ?></center></span>
                    </td>
                    <td >
                         <span style="font-size: 13px;"><center><?php echo $value['relationship_type']; ?></center></span>
                    </td>
                     <td >
                         <span style="font-size: 13px;"><center><?php echo $value['address']; ?></center></span>
                    </td>
                    <td >
                         <span style="font-size: 13px;"><center><?php echo $value['telphone']; ?></center></span>
                    </td>
                    <td >
                         <span style="font-size: 13px;"><center><?php echo $value['share_portion']; ?></center></span>
                    </td>
                </tr>
                <?php } ?>

                </tbody>
        </table>
        <?php if(in_array('6', $modules)){ ?>   
        <table class="table table-sm table-bordered" width="100%">
            <tbody >
                <tr class="table-primary">
                     <td colspan="3">
                         <span style="font-size: 16px;font-weight: bold;"><center>SAVINGS</center></span>
                    </td>
                </tr>
                <tr >
                     <th >
                         <span style="font-size: 13px;"><center>Account No</center></span>
                    </th>
                     <th >
                         <span style="font-size: 13px;"><center>Account Type</center></span>
                    </th>
                    <th >
                         <span style="font-size: 13px;"><center>Amount Available</center></span>
                    </th>
                </tr>
                <?php foreach ($savings_accs as $key => $value) { ?>
                 <tr >
                     <td >
                         <span style="font-size: 13px;"><center><?php echo $value['account_no']; ?></center></span>
                    </td>
                     <td >
                         <span style="font-size: 13px;"><center><?php echo $value['productname']; ?></center></span>
                    </td>
                    <td >
                         <span style="font-size: 13px;"><center><?php echo number_format(round($value['cash_bal'])); ?></center></span>
                    </td>
                </tr>
                <?php } ?>

                </tbody>
        </table>
    <?php } if(in_array('4', $modules)){ ?>
         <table class="table table-sm table-bordered" width="100%">
            <tbody >
                 <tr class="table-primary">
                     <td colspan="5">
                         <span style="font-size: 16px;font-weight: bold;"><center>ACTIVE LOANS</center></span>
                    </td>
                </tr>
                <tr >
                     <th >
                         <span style="font-size: 13px;"><center>Loan No</center></span>
                    </th>
                     <th >
                         <span style="font-size: 13px;"><center>Requested Amount</center></span>
                    </th>
                    <th >
                        <span style="font-size: 13px;"><center>Paid Principal</center></span>
                    </th>
                    <th >
                        <span style="font-size: 13px;"><center>Balance</center></span>
                    </th>
                     <!-- <th >
                        <span style="font-size: 13px;"><center>Status</center></span>
                    </th> -->
                </tr>
                <?php foreach ($loans as $key => $value) { ?>
                 <tr >
                     <td >
                         <span style="font-size: 13px;"><center><?php echo $value['loan_no']; ?></center></span>
                    </td>
                    <td >
                         <span style="font-size: 13px;"><center><?php echo number_format(round($value['expected_principal'])); ?></center></span>
                    </td>
                    <td >
                         <span style="font-size: 13px;"><center><?php echo number_format(round($value['paid_principal'])); ?></center></span>
                    </td>
                     <td >
                         <span style="font-size: 13px;"><center><?php echo number_format(round($value['expected_principal'],2)-round($value['paid_principal'],2)) ?></center></span>
                    </td>
                    <!-- <td >
                         <span style="font-size: 13px;"><center><?php //echo $value['state_name']; ?></center></span>
                    </td> -->
                </tr>
                <?php } ?>
            
                </tbody>
        </table>
        <?php } if(in_array('12', $modules)){ ?>
         <table class="table table-sm table-bordered" width="100%">
            <tbody >
                <tr class="table-primary">
                     <td colspan="3">
                         <span style="font-size: 16px;font-weight: bold;"><center>SHARES</center></span>
                    </td>
                </tr>
                <tr >
                     <th >
                         <span style="font-size: 13px;"><center>Account No</center></span>
                    </th>
                     <th >
                         <span style="font-size: 13px;"><center>No of Shares</center></span>
                    </th>
                    <th >
                         <span style="font-size: 13px;"><center>Total Amount</center></span>
                    </th>
                </tr>
                <?php foreach ($shares as $key => $value) { ?>
                 <tr >
                     <td >
                         <span style="font-size: 13px;"><center><?php echo $value['share_account_no']; ?></center></span>
                    </td>
                     <td >
                         <span style="font-size: 13px;"><center><?php 
                         $total_amount=$value['total_amount']?$value['total_amount']:0;
                         $price_per_share=$value['price_per_share']?$value['price_per_share']:0;
                         $shares=$total_amount/$price_per_share;
                         echo number_format(round($shares));
                          ?></center></span>
                    </td>
                    <td >
                         <span style="font-size: 13px;"><center><?php echo number_format(round($value['total_amount'])); ?></center></span>
                    </td>
                </tr>
                <?php } ?>

                </tbody>
        </table>
    <?php }  ?>
      </div>
</div>
