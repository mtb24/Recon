<?php
include('configuration.php');

// check whether form has already been submitted
$date	=	$_POST['date'];
$date	=	explode('-',$date);
$date	=	$date[2].'-'.$date[0].'-'.$date[1];
$store	=	$_POST['store'];
$query	=	"select * from headers where date = '$date' and store_id = '$store'";
$query	=	mysql_query($query) or die(mysql_error());
if (mysql_num_rows($query) == 1) {
	$header	=	mysql_fetch_assoc($query);
	$header	=	$header['id'];
	echo $header;
} else {
	echo 'new';
}
?>