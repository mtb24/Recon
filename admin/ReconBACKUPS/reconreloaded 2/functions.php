<?php 

$host	=	'localhost:8889';
$user	=	'root';
$pass	=	'br54!6A';
$db	=	'recon';

$con	=	mysql_connect($host,$user,$pass) or die (mysql_error());
$db	=	mysql_select_db($db,$con) or die(mysql_error());

function friendly_seo_string($string, $separator = '-')
{
$string = trim($string);

$string = strtolower($string); // convert to lowercase text

$string = trim(ereg_replace("[^ A-Za-z0-9_]", " ", $string));

$string = str_replace(" ", $separator, $string);

$string = preg_replace("[ -]", "-", $string);

return $string;
}

?>
