<?php

/*
Provides the meat for 'read' fetchers. This file should be included at the top of each read fetcher
*/

//Get reads from the database and stream down as JSON
function get_reads($db, $table, $left, $right, $bases, $pixels) {
	if (1.0 * $bases / $pixels > 1.0) {
		get_histogram($db, $table, $left, $right, $bases, $pixels);
	}
	else {
		get_letters($db, $table, $left, $right);
	}

}

//Get stack info as merged histogram
function get_histogram($db, $table, $left, $right, $bases, $pixels) {
	$d = query("select position, ftotal, rtotal from $db.$table where position between $left and $right");

	$data = array();
	
	while ($r = mysql_fetch_row($d)) {
		$pos = "" . round($r[0] * $pixels / $bases);
		
		if (!isset($data[$pos])) {
			$data[$pos] = array(0,0);
		}
		$data[$pos][0] += $r[1];
		$data[$pos][1] += $r[2];
	}
	
	$result = array();
	
	foreach ($data as $key => $value)
	{	
		$result[] = array($key, $value[0], $value[1]);
	}
	respond($result);
}

//Get stack info as individual stacks
function get_letters($db, $table, $left, $right) {
	$d = query("select * from $db.$table where position between $left and $right");

	$all = array();

	while ($r = mysql_fetch_row($d))
	{
		$data = array();
		foreach ($r as $v)
		{
			if (is_numeric($v)) $v += 0;
			$data[] = $v;
		}
		$all[] = $data;
	}
	respond($all);
}

?>