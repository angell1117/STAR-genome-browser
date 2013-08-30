<?php

require_once 'common.php';

if ($action == 'syndicate')
{
	if (isset($title)) $syndication['service']['title'] = $title;
	if (isset($info))  $syndication['service']['description'] = $info;
	respond($syndication);
}

if ($action == 'range')
{
	if ($append_assembly)
	{
		$table .= $assembly;
	}
	if (1.0 * $bases / $pixels >= 5.0)
	{
		get_histogram($table, $assembly, $left, $right, $bases, $pixels);
	}
	else if (1.0 * $bases / $pixels >= 0.2)
	{
		get_boxes($table, $assembly, $left, $right, false);
	}
	else
	{
		get_boxes($table, $assembly, $left, $right, true);
	}
}

//Get read depth as histogram
function get_histogram($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = "select 'read', start, end, strand, locations, copies from $table where assembly = '$assembly' and start <= $right and end >= $left";

	if (cache_exists($query)) cache_stream($query);

	$result = array();
	$unit = round($bases / $pixels);
	$d = query($query);
	
	while ($r = mysql_fetch_row($d))
	{
		$class  = $r[0];
		$start  = $r[1] + 0;
		$end    = $r[2] + 0;
		$strand = $r[3];
		$count  = $r[4] + 0;
		$copies = $r[5] + 0;
				
		//If the class is new then create a series to represent it
		if (!isset($result[$class]))
		{
			$result[$class] = array();
		}
							
		//Determine the range of x positions covered by the read
		$x1 = floor($start * $pixels / $bases);
		$x2 = ceil($end * $pixels / $bases);
		
		//For each of the x positions, convert to a genome position and add to the count
		for ($x=$x1; $x<=$x2; $x++)
		{
			$gpos = $x * $bases / $pixels;
		
			if (!isset($result[$class]["$gpos"]))
			{
				$result[$class]["$gpos"] = array($gpos,0,0);
			}
			
			$amt = 0;
			
			if ($gpos < $start)
			{
				$amt = $gpos + $unit - $start;
			}
			else if ($gpos + $unit > $end && $gpos < $end)
			{
				$amt = $end - $gpos;
			}
			else
			{
				$amt = $unit;
			}
			
			$amt *= $copies;
			
			if ($strand == '+')
			{
				$result[$class]["$gpos"][1] += $amt;
			}
			else 
			{
				$result[$class]["$gpos"][2] += $amt;
			}
		}
	}
	
	//Simplify the data stream
	foreach ($result as $class => $data)
	{
		$clean = array();
		
		foreach ($data as $datum)
		{
			$clean[] = $datum;
		}
		$result[$class] = $clean;
	}

	//Create cache and stream to user
	cache_create($query,$result,true,$table);
}


//Get reads as boxes (flag for including sequence information)
function get_boxes($table, $assembly, $left, $right, $seq)
{
	$seq = $seq ? "sequence" : "''";
	
	$query = "select id, 'read', start, end, strand, $seq, copies, locations from $table where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";
	
	if (cache_exists($query)) cache_stream($query);

	$result = array();
	$d = query($query);
	
	while ($r = mysql_fetch_row($d))
	{
		$id     = $r[0];
		$class  = $r[1];
		$start  = $r[2] + 0;
		$length = ($r[3] + 0) - $start;
		$strand = $r[4] == '+' ? 'watson' : 'crick';
		$seq    = $r[5];
		$copies = $r[6] + 0;
		$count  = $r[7] + 0;
				
		if (!isset($result[$class]))
		{
			$result[$class] = array
			(
				'watson' => array(), 
				'crick'  => array()
			);
		}
		$result[$class][$strand][] = array($id, $start, $length, $count, $copies, $seq); 
	}

	//Create cache and stream to user
	cache_create($query,$result,true,$table);
}

error('Invalid action requested: ' . $action);

?>