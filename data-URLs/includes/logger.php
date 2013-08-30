<?php

new logger(true);

class logger
{
	var $link;
	var $ip;

	function logger($act=false)
	{
		$this->link = mysql_connect('localhost','username','password');
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->create();
		if ($act) $this->act();
	}
	
	//Create tables
	function create()
	{
		mysql_query("create table if not exists logs.users (ip varchar(60) unique, visits int unsigned, requests int unsigned)", $this->link);
		mysql_query("create table if not exists logs.visits (ip varchar(60), visited datetime)", $this->link);
		mysql_query("create table if not exists logs.lookups (ip varchar(60), lookup varchar(60), visited datetime)", $this->link);
	}
	
	//Act based on what the user is doing
	function act()
	{
		$self = $_SERVER['PHP_SELF'];
		
		if (strpos($self,'arabidopsis'))
		{
			$this->log_visit();
		}
		else if (isset($_REQUEST['action']) && isset($_REQUEST['query']) && $_REQUEST['action'] == 'lookup')
		{
			$this->log_lookup(mysql_real_escape_string($_REQUEST['query'], $this->link));
		}
		else if (count($_REQUEST) > 0)
		{
			$this->log_request();
		}
	}
	
	//Log a visit from a user
	function log_visit()
	{		
		$d = mysql_query("select * from logs.users where ip = '{$this->ip}'", $this->link);
		
		if (mysql_num_rows($d) == 0)
		{
			mysql_query("replace into logs.users values ('{$this->ip}', 1, 0)", $this->link);
		}
		else
		{
			$r = mysql_fetch_row($d);
			mysql_query("replace into logs.users values ('{$this->ip}', {$r[1]}+1, {$r[2]})", $this->link);
		}
		mysql_query("insert into logs.visits values ('{$this->ip}', now())", $this->link);
	}
	
	//Log a request from a user
	function log_request()
	{
		$d = mysql_query("select * from logs.users where ip = '{$this->ip}'", $this->link);
		
		if (mysql_num_rows($d) == 0)
		{
			mysql_query("replace into logs.users values ('{$this->ip}', 1, 1)", $this->link);
		}
		else
		{
			$r = mysql_fetch_row($d);
			mysql_query("replace into logs.users values ('{$this->ip}', {$r[1]}, {$r[2]}+1)", $this->link);
		}
	}
	
	//Log a request for a gene lookup
	function log_lookup($lookup)
	{
		mysql_query("insert into logs.lookups values ('{$this->ip}', '$lookup', now())", $this->link);
	}
}
