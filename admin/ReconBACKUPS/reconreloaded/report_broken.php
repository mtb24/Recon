<?php
	# example.php
	#
	#	Create a daily summary report for all stores; email it to recipients
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
	$prog = $_SERVER['SCRIPT_NAME'];
	$report_date = 'CURRENT_DATE()';
	$stores = array( 'SR', 'SAU', 'BB', 'PA', 'SF', 'SAC', 'LG', 'PE', 'WC', 'MBD', 'BSW' );
	$recipients = array( '<ken.martin@mikesbikes.com>','<matt.adams@mikesbikes.com>','<mike.gabrys@mikesbikes.com>','<ian.richards@mikesbikes.com>','ken.downey@mikesbikes.com' );
	$storevals = "( '" . implode("', '", $stores) . "' )";
	$mysqli = mysqli_init();
	$mysqli->real_connect($configuration['host'], $configuration['user'], $configuration['pass'], $configuration['db'], $configuration['port']) or exit("Can't open connection: " . $mysqli->error);
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

	$sql = "SELECT code, store_name, total, total_rp FROM viewreconk2 WHERE recdate = $report_date ORDER BY store_name";
	$statementHandler->prepare($sql);
	$ttl = 0;
	$tv  = 0;
	$total = 0;
	$message = '';
	$message .= sprintf("\n");
	$statementHandler->execute();
	$statementHandler->bind_result($code, $store_name, $ttl, $rp);
	while($statementHandler->fetch())
	{
		$var = $ttl - $rp;
		$total += $ttl;
		$tv += $var;
		
		// output to the log
		//printf("%s %s\n\n", $store_name, $recupd);
		$message .=
			sprintf
			(
				"%s %s %s\n\n",
				$code,
				commify($ttl),
				commify($var)
		);
	}
	$statementHandler->close();
	$mysqli->close();
	$statementHandler = NULL;
	$mysqli = NULL;
	$message .=
		sprintf
		(
			"%s %s %s\n",
			"-----",
			"-----------",
			"-----------"
	);
	$message .=
		sprintf
		(
			"%s %s %s\n",
			"===",
			commify($total),
			commify($tv)
	);
	if($total > 0)
	{
		
print( $message);

		mail
		(
			implode(' ', $recipients),
			sprintf("Sales %-10.10s\n", $now),
			$message,
			sprintf
			(
				"From: %s\n",
				"\"Recon Automated Reporting\" <noreply@mikesbikes.com>"
			) . sprintf
				(
					"Bcc: %s\n",
					implode(', ', $recipients)
				) 
		);

	}
	exit(0);
?>
