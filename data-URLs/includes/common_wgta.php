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
	$query = "select start, end, class, score_above, score_below from $table where assembly = '$assembly' and start <= $right and end >= $left order by start asc";
	
	if (cache_exists($query)) cache_stream($query);
	
	//Populate the returning array
	$result = array();
	$d = query($query);

	while ($r = mysql_fetch_row($d))
	{
		$start = $r[0] + 0;
		$end   = $r[1] + 0;
		$class = $r[2];
		$above = $r[3] + 0;
		$below = $r[4] + 0;
		
		//If the class is new then create a series to represent it
		if (!isset($result[$class]))
		{
			$result[$class] = array();
		}
		
		//Do nothing if there is no data to store
		if ($above == 0 && $below == 0) continue;
					
		//Determine the rounded gpos of the feature
		$gpos = "" . round($start * $pixels / $bases) * $bases / $pixels;
		
		//Add an entry in the data series if necessary
		if (!isset($result[$class][$gpos]))
		{
			$result[$class][$gpos] = array($gpos+0,0,0,0);
		}	
		
		//Update values if needed
		$result[$class][$gpos][1] = max($result[$class][$gpos][1], $end-$start);
		$result[$class][$gpos][2] = max($result[$class][$gpos][2], $above);
		$result[$class][$gpos][3] = max($result[$class][$gpos][3], $below);
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
error('Invalid action requested: ' . $action);

?>
