<?php
include('functionsDaily.php');
#######################################################################
## This is the processing script for the Nightly Recon submissions   ##
##                                                                   ##
#######################################################################

	$store		 = $_POST['store_id'];
	$date		 = $_POST['date'];
	$date		 = explode('-',$date);
	$date		 = $date[2].'-'.$date[0].'-'.$date[1];
	$username	 = mysql_real_escape_string($_POST['username']);
	$comment	 = mysql_real_escape_string($_POST['comment']);
	$huddle	         = mysql_real_escape_string($_POST['huddle']);
	$headcount	 = $_POST['headcount'];
	$labor_completed = $_POST['labor_completed'];
	$headerID        = '';
	$itemInsertID    = ''; 
	$rproInsertID    = '';
	$GCrproInsertID  = '';
	$checklistID     = '';
	
	// Insert headers
	$query	=	"Insert into headers (store_id,date,employee_name,note,update_ts) 
				      VALUES ($store,'$date','$username','$comment',NOW() )";
	mysql_query($query) or die(mysql_error().' error: '.$query);
	$headerID	=	mysql_insert_id();

	// Insert the actuals
	foreach ($_POST['item'] as $itemtype => $theActual) {
		$query	=	"select id from itemtypes where name = '$itemtype'";
		$query	=	mysql_query($query);
		$query	=	mysql_fetch_assoc($query);
		$itemID	=	$query['id'];
		$term   =       1;
		$query	=	"insert into items (header_id,term_num,itemtype_id,amount,update_ts) 
					    VALUES ('$headerID', '$term', '$itemID', '$theActual', NOW() ) ";
		$itemInsertID = mysql_query($query) or die(mysql_error(). ' error: '.$query);				
	}

	// Insert the Rpro values
	foreach($_POST['rpro'] as $itemtype => $rprovalue) {
		$query	=	"select id from itemtypes where name = '$itemtype'";
		$query	=	mysql_query($query);
		$query	=	mysql_fetch_assoc($query);
		$itemID	=	$query['id'];
		$term	=	0;
		$query	=	"insert into items (header_id,term_num,itemtype_id,amount,update_ts) 
					    VALUES ('$headerID','$term','$itemID','$rprovalue',NOW() ) ";
		$rproInsertID = mysql_query($query) or die(mysql_error(). ' error: '.$query);
	}
	
	// Insert the Rpro GiftCard values
	foreach($_POST['gc_rpro'] as $itemtype => $gc_rpro_value) {
		$query	=	"select id from itemtypes where name = '$itemtype'";
		$query	=	mysql_query($query);
		$query	=	mysql_fetch_assoc($query);
		$itemID	=	$query['id'];
		$term	=	1;
		$query	=	"insert into items (header_id,term_num,itemtype_id,amount,update_ts) 
					    VALUES ('$headerID','$term','$itemID','$gc_rpro_value',NOW() ) ";
		$GCrproInsertID = mysql_query($query) or die(mysql_error(). ' error: '.$query);
	}

	// Insert checklist:
	$query	=   "INSERT INTO `checklists` (`store_id` ,`date` ,`huddle_topic` ,`service_head_count` ,`service_labor_completed`)
				       VALUES ('$store', '$date', '$huddle' , '$headcount' , '$labor_completed')";
	mysql_query($query);
	$checklistID	=	mysql_insert_id();
	
	// If everything went as planned...
	if ( $headerID && $itemInsertID && $rproInsertID && $GCrproInsertID && $checklistID ) {
		$query = mysql_query("select name from stores where id = $store");
		$query = mysql_fetch_assoc($query);
		echo "Today's Recon successfully submitted for <strong>".$query['name']."</strong>! <br />";
	} else {
		echo "Uh Oh! Something went wrong.<br />Unable to save tonight's Recon<br />";
	}
?>