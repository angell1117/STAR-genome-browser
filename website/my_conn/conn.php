<?php

$link =mysql_connect("localhost","sdepig","sandiego2009"); 

if(!$link){ 
    die("error");
}

mysql_select_db("sdec", $link);  
mysql_query("SET NAMES UTF8"); 

?>