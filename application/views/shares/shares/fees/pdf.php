<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">
		
		<table border="0" cellspacing="0" cellpadding="3"><tr><td class="center"><b>SHARE FEES PAYMENT RECIEPT</b></td></tr></table>
		<table border="0" cellspacing="0" cellpadding="2">
			<tr><td></td>
			<td>Date : <?php foreach ( $single_receipt_items as $single_receipt_item ) { echo date('n M Y', $single_receipt_item['date_created']);  } ?> <br />
				Receipt # : <span style="color:red"><?php foreach ( $single_receipt_items as $single_receipt_item ) { echo $single_receipt_item['transaction_no']; } ?></span><br />
				Customer ID : <?php echo $member['client_no']; ?><br />
				Branch : <?php echo $member['branch_name']; ?><hr>
			</td></tr>

		</table>
<br><br>
		<table border="0" cellspacing="0" cellpadding="2">
			<tr><td>RECIEVED FROM:</td><td></td><td><?php echo $member['salutation'] .' '. $member['firstname'] .' '. $member['lastname'] .' '. $member['othernames']; ?></td></tr>
		</table><hr><br ><br>

		<table border="0" cellspacing="0" cellpadding="2">
			<tr>	
				<td>Item description</td>
				<td>Quantity</td>
				<td>Amount (UGX)</td>
			</tr>
			<?php foreach( $receipt_items as $receipt_item ){ ?>
			<tr>	
				<td colspan="1"><?php if( isset($receipt_item['feename']) ) {echo $receipt_item['feename'];}else{echo '';}?></td>
				<td>1</td>
				<td><?php setlocale(LC_MONETARY,"en_US"); if( isset($receipt_item['amount']) ) {echo number_format( round($receipt_item['amount'],1));}else{echo '';}?></td>
			</tr>
			<?php } ?>
		</table><br ><hr>
		<table border="0" cellspacing="0" cellpadding="2">
			<tr><td colspan="2">TOTAL</td><td colspan="1"><?php if( isset($receipt_item_sum['total']) ) {echo number_format( round($receipt_item_sum['total'],1));}else{echo '';}?></td></tr>
		</table><br ><br>
		<table><b>FOR: <?php echo strtoupper( $member['name'] ); ?></b></table>

