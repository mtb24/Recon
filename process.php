<?php
include('configuration.php');
include('functions.php');
$con = mysql_connect($configuration['host'],$configuration['user'],$configuration['pass']) or die (mysql_error());
$db  = mysql_select_db($configuration['db'],$con) or die(mysql_error());
#######################################################################
## This is the processing script for the Nightly Recon submissions   ##
## Only allows updates within same day                               ##
#######################################################################

$date		 = explode('-', $_POST['date']);
$date		 = $date[2].'-'.$date[0].'-'.$date[1];
$username	 = mysql_real_escape_string($_POST['username']);
$comment	 = mysql_real_escape_string($_POST['comment']);
$huddle	         = mysql_real_escape_string($_POST['huddle']);
$headcount	 = $_POST['headcount'];
$labor_completed = $_POST['labor_completed'];
$store		 = $_POST['store'];
$headerID        = $_POST['header_id'];
$errors          = array();
$itemTypes       = array();

$result = mysql_query("select id, name from itemtypes where active = 1");
while($row = mysql_fetch_assoc($result)){
	$itemTypes[ $row['name'] ] = $row['id'];
}

switch( $_POST['action'] )
{
	case "new":
		// Insert headers
		$query = "Insert into headers (store_id,date,employee_name,note,update_ts) 
					      VALUES ($store,'$date','$username','$comment',NOW() )";

		(mysql_query($query)) ? true : $errors[] = mysql_error(). ' error: '.$query.'\n';
		$headerID	=	mysql_insert_id();
	
		// Insert the actuals
		foreach ($_POST['item'] as $itemtype => $theActual) {
			$term   =       1;
			$query	=	"insert into items (header_id,term_num,itemtype_id,amount,update_ts) 
						    VALUES ('$headerID', '$term', '$itemTypes[$itemtype]', '$theActual', NOW() ) ";
			(mysql_query($query)) ? true : $errors[] .= mysql_error(). ' error: '.$query.'\n';
		}
	
		// Insert the Rpro values
		foreach($_POST['rpro'] as $itemtype => $rprovalue) {
			$term	=	0;
			$query	=	"insert into items (header_id,term_num,itemtype_id,amount,update_ts) 
						    VALUES ('$headerID','$term','$itemTypes[$itemtype]','$rprovalue',NOW() ) ";

			(mysql_query($query)) ? true : $errors[] .= mysql_error(). ' error: '.$query.'\n';
		}
		
		// Insert the Rpro GiftCard values
		foreach($_POST['gc_rpro'] as $itemtype => $gc_rpro_value) {
			$term	=	0;
			$query	=	"insert into items (header_id,term_num,itemtype_id,amount,update_ts) 
						    VALUES ('$headerID','$term','$itemTypes[$itemtype]','$gc_rpro_value',NOW() ) ";

			(mysql_query($query)) ? true : $errors[] .= mysql_error(). ' error: '.$query.'\n';
		}
	
		// Insert checklist:
		$query	=   "INSERT INTO `checklists` (`store_id` ,`date` ,`huddle_topic` ,`service_head_count` ,`service_labor_completed`)
					       VALUES ('$store', '$date', '$huddle' , '$headcount' , '$labor_completed')";

		(mysql_query($query)) ? true : $errors[] .= mysql_error(). ' error: '.$query.'\n';
	        break;

	case "update":
		// Update headers
		$query = "UPDATE headers
		                        SET
		                            employee_name = '$username',
					    note = '$comment',
					    update_ts = NOW()
					WHERE
					    id = $headerID";

		(mysql_query($query)) ? true : $errors[] .= mysql_error(). ' error: '.$query.'\n';

	
		// Insert the actuals
		foreach ($_POST['item'] as $itemtype => $theActual) {
			$term   =       1;
			$query	=	"UPDATE items
			                                SET
							        amount = '$theActual',
								update_ts = NOW()
							WHERE
							        header_id = '$headerID'
							    AND term_num = '$term'
							    AND itemtype_id = '$itemTypes[$itemtype]'";

			(mysql_query($query)) ? true : $errors[] .= mysql_error(). ' error: '.$query.'\n';
		}
	
		// Insert the Rpro values
		foreach($_POST['rpro'] as $itemtype => $rprovalue) {
			$term	=	0;
			$query	=	"UPDATE items
			                                SET
							        amount = '$rprovalue',
								update_ts = NOW()
							WHERE
							        header_id = '$headerID'
							    AND term_num = '$term'
							    AND itemtype_id = '$itemTypes[$itemtype]'";

			(mysql_query($query)) ? true : $errors[] .= mysql_error(). ' error: '.$query.'\n';
		}
		
		// Insert the Rpro GiftCard values
		foreach($_POST['gc_rpro'] as $itemtype => $gc_rpro_value) {
			$term	=	0;
			$query	=	"UPDATE items
			                                SET
							        amount = '$gc_rpro_value',
								update_ts = NOW()
							WHERE
							        header_id = '$headerID'
							    AND term_num = '$term'
							    AND itemtype_id = '$itemTypes[$itemtype]'";

			(mysql_query($query)) ? true : $errors[] .= mysql_error(). ' error: '.$query.'\n';
		}
	
		// Insert checklist:
		$query	=   "UPDATE checklists
		                                SET
						        huddle_topic = '$huddle',
							service_head_count = '$headcount',
							service_labor_completed = '$labor_completed'
						WHERE
						        store_id = '$store'
						    AND date = '$date'";

		(mysql_query($query)) ? true : $errors[] .= mysql_error(). ' error: '.$query.'\n';
		$checklistID	=	mysql_insert_id();
		break;
}
	// If everything went as planned...
	if ( !$errors ) {
		$query = mysql_query("select name from stores where id = $store");
		$query = mysql_fetch_assoc($query);
		echo "<div class=\"alert alert-success\">Today's Recon successfully submitted for <strong>".$query['name']."</strong>!</div>";
	} else {
		echo "<div class=\"alert alert-error\"><strong>Uh Oh! Something went wrong</strong><br />";
		foreach( $errors as $error){
			echo "<br />".$error."<br />";
		}
		echo "</div>";
	}
?>