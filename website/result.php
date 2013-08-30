<?php

include("my_conn/conn.php");

$action=$_GET["action"];

switch ($action){

case "register":
include("my_inc/addusercode.php");
break;

case "delete":
break;

case "modify":
break;

case "save":
break;

}

?>
