<?php 

function friendly_string($string, $separator = '-')
{
    $string = trim($string);
    $string = strtolower($string); // convert to lowercase text
    $string = trim(ereg_replace("[^ A-Za-z0-9_]", " ", $string));
    $string = str_replace(" ", $separator, $string);
    $string = preg_replace("[ -]", "-", $string);
    return $string;
}

?>
