<?php
include('configuration.php');
include('functions.php');
date_default_timezone_set('America/Los_Angeles');
$con = mysql_connect($configuration['host'],$configuration['user'],$configuration['pass']) or die (mysql_error());
$db  = mysql_select_db($configuration['db'],$con) or die(mysql_error());
$header = '';
$today  = date("Y-m-d");
$testDate = '2013-06-03';
$store	= $_GET['store'];
$query	= "select * from headers where date = '$today' and store_id = '$store'";
$query	= mysql_query($query) or die(mysql_error());

// check whether form has already been submitted
if (mysql_num_rows($query)) {
	
	// This is a update action so we are grabbing data
	$header	= mysql_fetch_assoc($query);
	
	// create the information array - will be filled up with the amounts etc
	// format: $items['item type']['term number']	=   amount of $
	$items = array();
	$defaultStore = $header['store_id'];
	
	// Get more info now on all cash items
	
	$id = $header['id'];
	$query		=	"select * from items where header_id = $id";
	$headerData	=	mysql_query($query) or die(mysql_error());
	while	($r	=	mysql_fetch_assoc($headerData)) { 
		$query	=	"select name from itemtypes where id = ".$r['itemtype_id'];
		$name	=	mysql_query($query) or die(mysql_error() . $query);
		$result	=	mysql_fetch_assoc($name);
		$name	=	str_replace(" ","_",$result['name']); // remove spaces from keys
		$items[$name][$r['term_num']] = $r['amount'];
	}
	foreach($items as $itemname => $array) {
		$count	=	0;
		// If there are no amounts in here, it means there were no 
		// transactions saved for this one:
		if (!is_array($array)) { 
		$items[$itemname]['total'] = 0;
		continue;
		}
		
		foreach($array as $term => $amount) {
			if ($term == 0) continue;
			$count += $amount;
		}
		$items[$itemname]['total'] = $count;
	}

	// grab the checklist items
	$date = $header['date'];
	$query	        =	"select * from checklists where store_id = $defaultStore and date = '$date'";
	$checklist	=	mysql_query($query) or die(mysql_error().$query);
	$checklist	=	mysql_fetch_assoc($checklist);
		
	// Grab all item types except giftcards
	$query	        =	"Select * from itemtypes where active = 1 and id not in (2,3,4,5,6)";
	$allTypes	=	mysql_query($query);
	
	// grab gift card item types
	$query          =       "Select * from itemtypes where id in (2,3)";
	$giftcards      =       mysql_query($query);
	

	$output = array();
	$output['header'] = $header;
	$output['items'] = $items;
	$output['checklist'] = $checklist;
	$output['itemtypes'] = $allTypes;
	$output['giftcards'] = $giftcards;
	
	echo json_encode($output);

} else {
	echo '{"action":"new"}';
}