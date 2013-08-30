<?php

/*
Provides the meat for 'read' fetchers. This file should be included at the top of each read fetcher
*/

//Get reads from the database and stream down as JSON
function get_reads($db, $table, $assembly, $left, $right, $bases, $pixels)
{
	if (1.0 * $bases / $pixels >= 10.0) {
		get_histogram($db, $table, $assembly, $left, $right, $bases, $pixels);
	}
	else if (1.0 * $bases / $pixels >= 0.2) {
		get_boxes($db, $table, $assembly, $left, $right, false);
	}
	else {
		get_boxes($db, $table, $assembly, $left, $right, true);
	}

}

//Get read depth as histogram
function get_histogram($db, $table, $assembly, $left, $right, $bases, $pixels)
{
	$data = array();
	$gap = round($bases / $pixels);

	$d= query("select start1, end1, start2, end2, strand from $db.$table where assembly = '$assembly' and start1 <= $right and end2 >= $left");
	
	while ($r = mysql_fetch_row($d))
	{
		$strand = $r[4];

		$start = $r[0] + 0;
		$end = $r[1] + 0;		
		
		//Determine the checkpoints
		$A = ceil($start * $pixels / $bases);
		$B = floor($end * $pixels / $bases);
		
		//Determine the genomic equivalents of the checkpoints
		$a = $A * $bases / $pixels;
		$b = $B * $bases / $pixels;
		
		//Determine the overhangs
		$over_a = $a - $start - round($gap/2);
		$over_b = $end - $b - round($gap/2);
		
		//Add counts
		for ($i=$A; $i<=$B; $i++) {
			$pos = "" . $i;
			
			if (!isset($data[$pos])) {
				$data[$pos] = array(0,0);
			}
			if ($strand == '+') {
				$data[$pos][0] += $gap;
			}
			else {
				$data[$pos][1] += $gap;
			}
		}
		
		//Adjust the left end if necessary
		$pos = "" . ($A-1);
		
		if ($over_a > 0) {	
			if (!isset($data[$pos])) {
				$data[$pos] = array(0,0);
			}
			if ($strand == '+') {
				$data[$pos][0] += $over_a;
			}
			else {
				$data[$pos][1] += $over_a;
			}
		}
		else {
			if (!isset($data["" . $A])) {
				$data["" . $A] = array(0,0);
			}
			if ($strand == '+') {
				$data["" . $A][0] += $over_a;
			}
			else {
				$data["" . $A][1] += $over_a;
			}
		}

		//Adjust the left end if necessary
		$pos = "" . ($B+1);
		
		if ($over_b > 0) {	
			if (!isset($data[$pos])) {
				$data[$pos] = array(0,0);
			}
			if ($strand == '+') {
				$data[$pos][0] += $over_b;
			}
			else {
				$data[$pos][1] += $over_b;
			}
		}
		else {
			if ($strand == '+') {
				$data["" . $B][0] += $over_b;
			}
			else {
				$data["" . $B][1] += $over_b;
			}
		}
		
		//Now do for the other end
		$start = $r[2] + 0;
		$end = $r[3] + 0;		
		
		//Determine the checkpoints
		$A = ceil($start * $pixels / $bases);
		$B = floor($end * $pixels / $bases);
		
		//Determine the genomic equivalents of the checkpoints
		$a = $A * $bases / $pixels;
		$b = $B * $bases / $pixels;
		
		//Determine the overhangs
		$over_a = $a - $start - round($gap/2);
		$over_b = $end - $b - round($gap/2);
		
		//Add counts
		for ($i=$A; $i<=$B; $i++) {
			$pos = "" . $i;
			
			if (!isset($data[$pos])) {
				$data[$pos] = array(0,0);
			}
			if ($strand == '+') {
				$data[$pos][0] += $gap;
			}
			else {
				$data[$pos][1] += $gap;
			}
		}
		
		//Adjust the left end if necessary
		$pos = "" . ($A-1);
		
		if ($over_a > 0) {	
			if (!isset($data[$pos])) {
				$data[$pos] = array(0,0);
			}
			if ($strand == '+') {
				$data[$pos][0] += $over_a;
			}
			else {
				$data[$pos][1] += $over_a;
			}
		}
		else {
			if (!isset($data["" . $A])) {
				$data["" . $A] = array(0,0);
			}
			if ($strand == '+') {
				$data["" . $A][0] += $over_a;
			}
			else {
				$data["" . $A][1] += $over_a;
			}
		}

		//Adjust the left end if necessary
		$pos = "" . ($B+1);
		
		if ($over_b > 0) {	
			if (!isset($data[$pos])) {
				$data[$pos] = array(0,0);
			}
			if ($strand == '+') {
				$data[$pos][0] += $over_b;
			}
			else {
				$data[$pos][1] += $over_b;
			}
		}
		else {
			if ($strand == '+') {
				$data["" . $B][0] += $over_b;
			}
			else {
				$data["" . $B][1] += $over_b;
			}
		}
	}

	$result = array();
	
	foreach ($data as $key => $value) {
		$result[] = array($key, $value[0], $value[1]);
	}
	respond($result);
}


//Get reads as boxes (flag for including sequence information)
function get_boxes($db, $table, $assembly, $left, $right, $seq)
{
	if ($seq)
	{
		$d = query("select id, strand, start1, end1, sequence1, start2, end2, sequence2 from $db.$table where assembly='$assembly' and start1 <= $right and end2 >= $left order by start1 asc, end2 desc");
	}
	else 
	{
		$d = query("select id, strand, start1, end1, '', start2, end2, '' from $db.$table where assembly='$assembly' and start1 <= $right and end2 >= $left order by start1 asc, end2 desc");
	}

	$data = array();

	$num = 0;
	
	while ($r = mysql_fetch_row($d))
	{
		$r[2] = 0 + $r[2];
		$r[3] = 0 + $r[3];
		$r[5] = 0 + $r[5];
		$r[6] = 0 + $r[6];
		$data[] = $r;
	}
	respond($data);
}

?>