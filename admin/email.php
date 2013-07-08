<?php
	include('configuration.php');
	
	/* Set timezone for date() */
        date_default_timezone_set('America/Los_Angeles');
	// connect to the DB
$mysqli = mysqli_init();
if (!$mysqli) {
    die('mysqli_init failed');
}

if (!$mysqli->real_connect($configuration['host'], $configuration['user'], $configuration['pass'], $configuration['db'], $configuration['port'])) {
    die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}

echo 'Success... ' . $mysqli->host_info . "<br />\n";

echo "Saving data now...<br />\n";

	$store_recipients = array( 1=>'jesse.huselid@mikesbikes.com',
				   2=>'hank.scholz@mikesbikes.com',
				   3=>'justin.bomben@mikesbikes.com',
				   4=>'ben.jones@mikesbikes.com',
				   5=>'tamara.marsh@mikesbikes.com',
				   6=>'aaron.astle@mikesbikes.com',
				   7=>'bryan.wynn@mikesbikes.com',
				   8=>'dj.campagna@mikesbikes.com',
				   9=>'lizzy.allbut@mikesbikes.com',
				   17=>'drew.powers@mikesbikes.com',
				   19=>'dylan.rinaldi@mikesbikes.com');
        
        foreach($store_recipients as $id=>$email){
            $sql = "UPDATE stores SET report_list = '".$email."' WHERE id = ".$id;
            echo $sql;
            $last = $mysqli->query($sql) or die ("UPDATE not successful");
            echo $last."<br />\n";
        }
        echo "Done";
        
$mysqli->close();
?>