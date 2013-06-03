<?php
include('../configuration.php');
// Form handling
	$action	 =  $_POST['action'];
	
	
	if ($action == 'edit') {

		####################
		##	EDIT OLD REPORT
		####################
		$store		=	$_POST['store_id'];
		$date		=	$_POST['report_date'];
		$date		=	explode('/',$date);
		$date		=	$date[2].'-'.$date[0].'-'.$date[1];
		$comment	=	mysql_real_escape_string($_POST['comment']);
		$username		=	mysql_real_escape_string($_POST['username']);	
		//	if(empty($userid)) {
				// to make compatible with old database tables
		//		$userid	=	0;
		//	}
		$header_id	=	$_POST['headers_id'];
		//$checklist_id	=	$_POST['checklist_id'];
		// Let's update the header table:
		
		$query	=	"Update headers SET
						employee_name = '$username',
						note = '$comment',
						update_ts = NOW()
					WHERE
						id	=	$header_id";
		mysql_query($query) or die(mysql_error().$query);
		
		// Now its time to edit the items...
		foreach ($_POST['item'] as $itemtype => $theArray) {
			$query	=	"select id from itemtypes where name = '$itemtype'";
			$query	=	mysql_query($query);
			$query	=	mysql_fetch_assoc($query);
			$itemID	=	$query['id'];
			
			foreach($theArray as $term => $cash) {
					// First see whether there is already an entry for this datatype
					// Compatibility with old forms <..
					$realterm = $term+1;
					
					$query	=	"Select * from items  WHERE header_id = $header_id 
											AND  term_num = $realterm
											AND  itemtype_id = $itemID";
					
					$res	=	mysql_query($query) or die(mysql_error().$query);
					$number	=	mysql_num_rows($res);
				if ($number == 0) {
				// This record wasn't created by the old script,
				// lets create it now :)
					$query	=	"insert into items (header_id,term_num,itemtype_id,amount,update_ts) 
								VALUES     ($header_id,$realterm,$itemID,'$cash',NOW() ) ";
						mysql_query($query) or die(mysql_error(). ' error: '.$query);				
				
				} else {
				// This record existed, let's update it..
						$query	=	"UPDATE items set
										amount    = '$cash',
										update_ts = NOW()
									WHERE header_id = $header_id 
										AND  term_num    = $realterm
										AND  itemtype_id = $itemID";
					mysql_query($query) or die(mysql_error(). ' error: '.$query);		
				}
			}
		}
		// Now we are gonna edit the Rpro's 
		foreach($_POST['rpro'] as $itemtype => $rprovalue) {
			$query	=	"select id from itemtypes where name = '$itemtype'";
			$query	=	mysql_query($query);
			$query	=	mysql_fetch_assoc($query);
			$itemID	=	$query['id'];
			$term	=	0;
			
			// compatibility with old form:
			// 	->	Check if RPRO value exists, if not add it.
			
			$query	=	"Select * from items  WHERE header_id = $header_id 
								AND term_num = $term
								AND itemtype_id = $itemID";
					$res	=	mysql_query($query);
					$number	=	mysql_num_rows($res);
			if ($number == 0) {
			// Doesn't exist - let's create
				$query	=	"insert into items (header_id,term_num,itemtype_id,amount,update_ts) 
							VALUES     ($header_id,$term,$itemID,'$rprovalue',NOW() ) ";
						mysql_query($query) or die(mysql_error(). ' error: '.$query);
			}  else {
			// Does exist - update
			$query	=	"UPDATE items set
							amount = '$rprovalue',
							update_ts = NOW()
						WHERE header_id = $header_id 
							AND term_num = $term
							AND itemtype_id = $itemID";
			mysql_query($query) or die(mysql_error(). ' error: '.$query);
			}
			
		}
		
		// Time to update the checklist and zones
		$huddle	         =	mysql_real_escape_string($_POST['huddle']);
		$headcount	 =	$_POST['headcount'];
		$labor_completed =	$_POST['labor_completed'];
		if (empty($_POST['close_ac'])) {
		$ac	=	0;
		} else {
		$ac	=	1;
		}
		if (empty($_POST['close_av'])) {
		$av	=	0;
		} else {
		$av	=	1;
		}
		if (empty($_POST['close_rpro'])) {
		$rproclose	=	0;
		} else {
		$rproclose	=	1;
		}
		if (empty($_POST['bike_sales_reviewed'])) {
		$bike_sales_reviewed	=	0;
		} else {
		$bike_sales_reviewed	=	1;
		}
		if (empty($_POST['bike_receipts_accurate'])) {
		$bike_receipts_accurate	=	0;
		} else {
		$bike_receipts_accurate	=	1;
		}
		$query	=	"Update checklists SET
						huddle_topic            = '$huddle',
						service_head_count      = '$headcount',
						service_labor_completed = '$labor_completed',
						ac_off	                = '$ac',
						av_off	                = '$av',
						close_rpro	        = '$rproclose',
						bike_sales_reviewed     = '$bike_sales_reviewed',
						bike_receipts_accurate  = '$bike_receipts_accurate'
					WHERE
						store_id	=	$store
					AND
						date		=	'$date'";
		$run	=	mysql_query($query) or die(mysql_error() . $query);
			
		echo 'Update Successful!';
		
	} else {
		####################
		##	NEW REPORT
		####################
		$store		=	$_POST['store_id'];
		$date		=	$_POST['report_date'];
		$date		=	explode('/',$date);
		$date		=	$date[2].'-'.$date[0].'-'.$date[1];
		$comment	=	mysql_real_escape_string($_POST['comment']);
		$username	=	mysql_real_escape_string($_POST['username']);



		$query	=	"Insert into headers (store_id,date,employee_name,note,update_ts) 
					      VALUES ($store,'$date','$username','$comment',NOW() )";
		mysql_query($query) or die(mysql_error().' error: '.$query);
		$header_id	=	mysql_insert_id();

		// now go on to the items :)

		foreach ($_POST['item'] as $itemtype => $theArray) {
			$query	=	"select id from itemtypes where name = '$itemtype'";
			$query	=	mysql_query($query);
			$query	=	mysql_fetch_assoc($query);
			$itemID	=	$query['id'];
			
			foreach($theArray as $term => $cash) {
			$realterm = $term+1;
			$query	=	"insert into items (header_id,term_num,itemtype_id,amount,update_ts) 
						    VALUES ($header_id,$realterm,$itemID,'$cash',NOW() ) ";
			mysql_query($query) or die(mysql_error(). ' error: '.$query);				
			}
		}

		foreach($_POST['rpro'] as $itemtype => $rprovalue) {
			$query	=	"select id from itemtypes where name = '$itemtype'";
			$query	=	mysql_query($query);
			$query	=	mysql_fetch_assoc($query);
			$itemID	=	$query['id'];
			$term	=	0;
			$query	=	"insert into items (header_id,term_num,itemtype_id,amount,update_ts) 
						    VALUES ($header_id,$term,$itemID,'$rprovalue',NOW() ) ";
			mysql_query($query) or die(mysql_error(). ' error: '.$query);
		}

		// Now insert checklist:
		$huddle	           =   mysql_real_escape_string($_POST['huddle']);
		$headcount	   =   $_POST['headcount'];
		$labor_completed   =   $_POST['labor_completed'];
		if (empty($_POST['close_ac'])) {
		$ac	=	0;
		} else {
		$ac	=	1;
		}
		if (empty($_POST['close_av'])) {
		$av	=	0;
		} else {
		$av	=	1;
		}
		if (empty($_POST['close_rpro'])) {
		$rproclose	=	0;
		} else {
		$rproclose	=	1;
		}
		if (empty($_POST['bike_sales_reviewed'])) {
		$bike_sales_reviewed	=	0;
		} else {
		$bike_sales_reviewed	=	1;
		}
		if (empty($_POST['bike_receipts_accurate'])) {
		$bike_receipts_accurate	=	0;
		} else {
		$bike_receipts_accurate	=	1;
		}

		$query	=   "INSERT INTO `recon`.`checklists` (
							`store_id` ,
							`date` ,
							`huddle_topic` ,
							`service_head_count` ,
							`service_labor_completed` ,
							`ac_off` ,
							`av_off` ,
							`close_rpro`,
							`bike_sales_reviewed`,
							`bike_receipts_accurate`)
						VALUES ('$store', '$date', '$huddle' , '$headcount' , '$labor_completed' , $ac , $av , $rproclose, $bike_sales_reviewed, $bike_receipts_accurate)";
		$run	        =	mysql_query($query);
		$checklist	=	mysql_insert_id();

	        echo 'Success!';		
	}
?>