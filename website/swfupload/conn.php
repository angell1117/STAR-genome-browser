<?php

$link =mysql_connect("localhost","epigenome","epigenome"); 

if(!$link){ 
    die("error");
}

mysql_select_db("epigenome", $link);  
mysql_query("SET NAMES UTF8"); 

?>