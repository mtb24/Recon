<?php
# report.php
#
#	Create a daily summary email report for all stores.
#	Starts running at 7pm on weekdays and 6pm on weekends (closing time).
#	Polls to find all stores reported and sends email as soon as all have.
#	If all haven't reported by 9:15pm, sends the values for the stores
#	that have reported.
#
#       This script is run via a CRON job with command: curl -sL http://192.168.168.31:8888/reconreloaded/report.php
#       Results are reported to /private/var/lognightlyrecon.out and /private/var/lognightlyrecon.err
#
#ini_set('display_errors',1);
#error_reporting(E_ALL|E_STRICT);
include('configuration.php');

/* Set timezone for date() */
date_default_timezone_set('America/Los_Angeles');

function commify($val)
{
	$val = sprintf("%.0f", $val);
	$val = preg_replace('/([0-9])([0-9]{3})\./', '\1,\2.', $val, 1);
	$val = preg_replace('/([0-9])([0-9]{3})$/', '\1,\2', $val, 1);
	$val = preg_replace('/([0-9])([0-9]{3})/', '\1,\2', $val);
	$val = sprintf("%7.7s", $val);
	return $val;
}

$report_date = 'CURRENT_DATE()';
$stores = array( 'SR', 'SAU', 'BB', 'PA', 'SF', 'SAC', 'LG', 'PE', 'WC', 'SJ', 'PLE', 'MBD', 'BSW', 'FLD');
$recipients = 'ken.martin@mikesbikes.com,
	       matt.adams@mikesbikes.com,
	       mike.gabrys@mikesbikes.com,
	       mitch.todd@mikesbikes.com,
	       huiping.dai@mikesbikes.com,
	       jose.morales@mikesbikes.com, 
	       ken.downey@mikesbikes.com';

$storevals = "( '" . implode("', '", $stores) . "' )";

$message = '';
$managers_log = array();

// connect to the DB
//$mysqli = new mysqli($configuration['host'], $configuration['user'], $configuration['pass'], $configuration['db'], $configuration['port']);

/* check connection */
//if (mysqli_connect_errno()) {
//    printf("Connect failed: %s\n", mysqli_connect_error());
//    exit();
//}

$sql = "SELECT NOW(),
	       MAX( update_ts ),
	       COUNT(*),
	       CURRENT_TIME() > IF(WEEKDAY(CURRENT_DATE()) IN (5, 6), '19:00:00', IF(CURRENT_TIME() >= '20:15:00','21:45:00','20:00:00'))
	FROM
	       headers h LEFT JOIN stores s ON h.store_id = s.id
	WHERE
	       DATE = $report_date 
	    AND
	       update_ts < NOW() - INTERVAL 20 MINUTE
	    AND
	       code IN $storevals";
	       
$statementHandler = $mysqli->stmt_init();
$statementHandler->prepare($sql);
$afteronehour = 0;
$nstores = 0;
$rstores = count($stores) + 1;
while(($afteronehour == 0) && ($nstores < $rstores))
{
	$statementHandler->execute();
	$statementHandler->bind_result($now, $last, $nstores, $afteronehour);
	$statementHandler->fetch();
	if($nstores < $rstores)
	{
	/*
	printf
		(
			"%s %d of %d stores reported (last %s) - wait (%d)\n",
			$now,
			$nstores,
			$rstores,
			$last,
			$afteronehour
		);
	*/
		if($afteronehour == 0)
		{
			sleep(60);
		}
	}
}
$statementHandler->execute();
$statementHandler->bind_result($now, $last, $nstores, $afteronehour);
$statementHandler->fetch();
printf
(
	"%s %d of %d stores reported (last %s) - running now\n",
	$now,
	$nstores,
	$rstores,
	$last
);

$statementHandler->reset();

// fetch store information
$sql2 = "SELECT id, code, name, report_list FROM stores ORDER BY id";
$statementHandler->prepare($sql2);
$statementHandler->execute();
$statementHandler->bind_result($storeid, $storecode, $storename, $reportlist);

while($statementHandler->fetch())
{
	$managers_log[$storeid]['code'] = $storecode;
	$managers_log[$storeid]['name'] = $storename;
	$managers_log[$storeid]['list'] = $reportlist;
	$managers_log[$storeid]['message'] = '';
}
$statementHandler->reset();

// get view
$sql3 = "SELECT s.id, v.code, v.store_name, v.total, v.total_rp FROM viewreconk2 v join stores s on s.code = v.code WHERE v.recdate = $report_date ORDER BY s.id";
$statementHandler->prepare($sql3);
$ttl = 0;
$tv  = 0;
$total = 0;
$message = "<br /><br /><table cellpadding=\"5\"><caption>Recon Nightly Report</caption>";
$message .= "<tr><th>Store</th><th>Sales</th><th>Var</th></tr>";
$log = "Nightly Recon Log for $now\n\n";
$statementHandler->execute();
$statementHandler->bind_result($id, $code, $store_name, $ttl, $rp);
while($statementHandler->fetch())
{
	$var = $ttl - $rp;
	$total += $ttl;
	$tv += $var;
	
	// output to the executive email
	$message .=
		sprintf
		(
			"<tr><td>%s</td><td>%s</td><td>%s</td></tr>",
			$code,
			commify($ttl),
			commify($var)
		);
		

	// output to the store managers email
	$managers_log[$id]['message'] = "<br /><br /><table cellpadding=\"5\"><caption>Your Recon Store Report</caption>";
	$managers_log[$id]['message'] .= "<tr><th>Store</th><th>Sales</th><th>Var</th></tr>";
	$managers_log[$id]['message'] .=
					sprintf
					(
						"<tr><td>%s</td><td>%s</td><td>%s</td></tr>",
						$code,
						commify($ttl),
						commify($var)
					);
	$managers_log[$id]['message'] .= "</table>";

	// output to server's console log 'nightly recon out'
	$log .=
		sprintf
		(
			"%s %s %s\n",
			$code,
			commify($ttl),
			commify($var)
		);
}
$statementHandler->close();
$mysqli->close();
$statementHandler = NULL;
$mysqli = NULL;
// add total columns
$message .=
	sprintf
	(
		"<tr><td>%s</td><td>%s</td><td>%s</td></tr>",
		"-----",
		"-----------",
		"-------"
	);
$message .=
	sprintf
	(
		"<tr><td>%s</td><td>%s</td><td>%s</td></tr>",
		"===",
		commify($total),
		commify($tv)
	);
$message .= "</table>";

$log .=
	sprintf
	(
		"%s %s %s\n",
		"-----",
		"-----------",
		"-------"
	);
$log .=
	sprintf
	(
		"%s %s %s\n",
		"===",
		commify($total),
		commify($tv)
	);
if($total > 0)
// Send report emails
{
	// output to log
	print($log);
	
	// build email headers
	$eheaders = "MIME-Version: 1.0" . "\r\n";
	$eheaders .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$eheaders .= 'From: "Recon Automated Reporting" <noreply@mikesbikes.com>' . "\r\n";
	$subject = sprintf("Sales %-10.10s\n", $now);

	// send email to executives
	mail($recipients,$subject,$message,$eheaders);

	// send email to store managers
	for($i=1; $i < (sizeof($stores)); $i++)
	{
		$to = explode(',',$managers_log[$i]['list']);
		$to = "'<".implode( ">','<",$to).">'";
		$message = $managers_log[$i]['message'];
		mail($to,$subject,$message,$eheaders);
	}
}
// Done!
exit;
?>
