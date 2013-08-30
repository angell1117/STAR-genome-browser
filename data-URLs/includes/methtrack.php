<?php
if ($action == 'syndicate')
{
	if (isset($title)) $syndication['service']['title'] = $title;
	if (isset($info))  $syndication['service']['description'] = $info;
	respond($syndication);
}

if ($action == 'range')
{
       	$unit = round($bases / $pixels);
       	if($assembly == "X") $db_name = $table.".db22-$unit";
       	else if($assembly == "Y") $db_name = $table.".db23-$unit";
       	else if($assembly == "M") $db_name = $table.".db24-$unit";
       	else if($assembly == "L") $db_name = $table.".db25-$unit";
       	else
       	{
               	$val = (int)$assembly;
               	$db_name = sprintf(".db%02d-%d",$val-1,$unit);
               	$db_name = $table.$db_name;
       	}
	if(!ereg("\/$",$db_dir)) $db_dir = $db_dir."/"; 
       	$db_name = $db_dir.$table."/".$db_name;

	if(file_exists($db_name))
		get_level_data($db_dir,$table, $assembly, $left, $right, $bases, $pixels);
	else get_histogram($db_dir,$table, $assembly, $left, $right, $bases, $pixels);
}

//Get read depth as histogram
function get_level_data($db_dir,$table, $assembly, $left, $right, $bases, $pixels)
{
        $query = "select 'read', start, end, strand, 1, 1 from $table where assembly = '$assembly' and start <= $right and end >= $left";

        $result = array();
        $unit = round($bases / $pixels);

        $db = new Db4();
        if($assembly == "X") $db_name = $table.".db22-$unit";
        else if($assembly == "Y") $db_name = $table.".db23-$unit";
       	else if($assembly == "M") $db_name = $table.".db24-$unit";
       	else if($assembly == "L") $db_name = $table.".db25-$unit";
        else
        {
                $val = (int)$assembly;
                $db_name = sprintf(".db%02d-%d",$val-1,$unit);
                $db_name = $table.$db_name;
        }

        $db_name = $db_dir.$table."/".$db_name;
	if(!file_exists($db_name)) error("No data available");
        if (cache_exists($query,$table)) {
                cache_stream($query, $table);
        }

        $db->open(null, $db_name, null);
        $key =sprintf("%010d",$left);
        $cursor = $db->cursor();
        $cursor_ret = $cursor->get($key, $val, DB_SET_RANGE);
        if($cursor_ret != 0)
        {
            $result['CG'] = array();
            cache_create($query,$result,true,$table);

            $cursor->close();
            $db->close();
            unset($db);
            return;
        }
 
        while(1)
        {
                $gpos = (int)$key;
                if($gpos >= $right) break;

                $arr = explode(",",$val);
                $amt1 = (double)$arr[0];
                $amt2 = (double)$arr[1];
                $amt3 = (double)$arr[2];
                $amt4 = (double)$arr[3];
                $amt5 = (double)$arr[4];
                $amt6 = (double)$arr[5];
                $class = "CG";
                $class1 = "CHG";
                $class2 = "CHH";

                //If the class is new then create a series to represent it
                if (!isset($result[$class]))
                {
                        $result[$class] = array();
                }
                if (!isset($result[$class]["$gpos"]))
                {
                        $result[$class]["$gpos"] = array($gpos,0,0);
                }
                if (!isset($result[$class1]))
                {
                        $result[$class1] = array();
                }
                if (!isset($result[$class1]["$gpos"]))
                {
                        $result[$class1]["$gpos"] = array($gpos,0,0);
                }
                if (!isset($result[$class2]))
                {
                        $result[$class2] = array();
                }
                if (!isset($result[$class2]["$gpos"]))
                {
                        $result[$class2]["$gpos"] = array($gpos,0,0);
                }
                if ($amt1 > 0)
                {
                        $result[$class]["$gpos"][1] = $amt1;
                }
                if ($amt2 > 0)
                {
                        $result[$class]["$gpos"][2] = $amt2;
                }
                if ($amt3 > 0)
                {
                        $result[$class1]["$gpos"][1] = $amt3;
                }
                if ($amt4 > 0)
                {
                        $result[$class1]["$gpos"][2] = $amt4;
                }
                if ($amt5 > 0)
                {
                        $result[$class2]["$gpos"][1] = $amt5;
                }
                if ($amt6 > 0)
                {
                        $result[$class2]["$gpos"][2] = $amt6;
                }

                if($cursor->get($key, $val, DB_NEXT) != 0) break;
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

        //Create cache and stream to r
        cache_create($query,$result,true,$table);

        $cursor->close();
        $db->close();
        unset($db);
}

//Get read depth as histogram
function get_histogram($db_dir,$table, $assembly, $left, $right, $bases, $pixels)
{
	$query = "select 'read', start, end, strand, 1, 1 from $table where assembly = '$assembly' and start <= $right and end >= $left";
	
	$result = array();
	$unit = $bases / $pixels;

        $db = new Db4();
	if($assembly == "X") $db_name = $table.".db22";
	else if($assembly == "Y") $db_name = $table.".db23";
       	else if($assembly == "M") $db_name = $table.".db24";
       	else if($assembly == "L") $db_name = $table.".db25";
	else
	{
		$val = (int)$assembly;
		$db_name = sprintf(".db%02d",$val-1);
		$db_name = $table.$db_name;
	}
	
	$db_name = $db_dir.$table."/".$db_name;
	if(!file_exists($db_name)) error("No data available");
	if (cache_exists($query, $table)) cache_stream($query, $table);

        $db->open(null, $db_name, null);
	
        $key =sprintf("%010d",$left);
        $cursor = $db->cursor();
        $cursor_ret = $cursor->get($key, $val, DB_SET_RANGE);
        if($cursor_ret != 0)
        {
            $result['CG'] = array();
            cache_create($query,$result,true,$table);

            $cursor->close();
            $db->close();
            unset($db);
            return;
        }
 
        while(1)
        {
                $ikey = (int)$key;
                if($ikey >= $right) break;
                $val = str_replace("|", "", $val);
                $r = explode(",",$val);
	
		$start  = $ikey;
		$end    = $r[0] + 0;
		$strand = $r[1];
		$count  = 1;
		$copies = (double)$r[2];
		$class = $r[3];
		if($class == "") $class = "CG";

		//If the class is new then create a series to represent it
		if (!isset($result[$class]))
		{
			$result[$class] = array();
		}
							
		//Determine the range of x positions covered by the read
		$x1 = floor($start * $pixels / $bases);
		$x2 = ceil($end * $pixels / $bases);
		
		//For each of the x positions, convert to a genome position and add to the count
		for ($x=$x1; $x<$x2; $x++)
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
			
			$amt *= $copies / $unit;
			
			if ($strand == '+')
			{
				$result[$class]["$gpos"][1] += $amt;
			}
			else 
			{
				$result[$class]["$gpos"][2] += $amt;
			}
		}
		if($cursor->get($key, $val, DB_NEXT) != 0) break;
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

	//Create cache and stream to r
	cache_create($query,$result,true,$table);

	$cursor->close();
        $db->close();
        unset($db);
}

error('Invalid action requested: ' . $action);
?>
