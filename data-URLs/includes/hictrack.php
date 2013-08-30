<?php

function get_table_name($url)
{
	$url = strstr($url, "http://");
	if(!$url) return false;
        $url = substr($url,strlen("http://"));

	$url = strstr($url, "/");
	if(!$url) return false;
        $url = substr($url,1);
	 
	$url = $_SERVER['DOCUMENT_ROOT']."/".$url;
	if(!file_exists($url)) return false;

	$fp = fopen($url, "r");
	if(!$fp) return false;
	$cnt = 0;
	while($line = fgets($fp, 1024))
	{
		$a = strstr($line,"\$table");
		if($a)
		{
			$p1 = strpos($line, "'");
			$p2 = strrpos($line, "'");
			if($p1 && $p2) $db_table = substr($line,$p1+1, $p2-$p1-1);
		}
		$a = strstr($line,"\$db_dir");
		if($a)
		{
			$p1 = strpos($line, "'");
			$p2 = strrpos($line, "'");
			if($p1 && $p2) $db_dir = substr($line,$p1+1, $p2-$p1-1);
		}
		if($db_table && $db_dir) break;
		if($cnt > 15) break;
		$cnt++;
	}
	fclose($fp);
	if($db_dir && !ereg("\/$",$db_dir)) $db_dir = $db_dir."/";

	$fname = $db_dir.$db_table."/".$db_table;
	if(!file_exists($fname.".db01")) return false;
	return $fname;
}
if ($action == 'syndicate')
{
	if (isset($title)) $syndication['service']['title'] = $title;
	if (isset($info))  $syndication['service']['description'] = $info;
	respond($syndication);
}

if ($action == 'range')
{
	if (1.0 * $bases / $pixels >= 5.0)
	{
		get_histogram($urls, $assembly, $left, $right, $bases, $pixels);
	}
	else
	{
		$array = array(
                        'success' => false,
                        'message' => 'Server tried to stream result data that does not exist'
                );

        	$string = json_encode($array);
		echo $string;
	 	exit();
	}
}
//Get read depth as histogram
function get_level_data($db_dir,$table, $assembly, $left, $right, $bases, $pixels)
{
        $query = "select 'read', start, end, strand, 1, 1 from $table where assembly = '$assembly' and start <= $right and end >= $left";

        if (cache_exists($query,$table)) {
                cache_stream($query, $table);
        }

        $result = array();
        $unit = round($bases / $pixels);

        $db = new Db4();
        if($assembly == "X") $db_name = $table.".db22-$unit";
        else if($assembly == "Y") $db_name = $table.".db23-$unit";
        else
        {
                $val = (int)$assembly;
                $db_name = sprintf(".db%02d-%d",$val-1,$unit);
                $db_name = $table.$db_name;
        }

        $db_dir1 = trim($db_dir);
        $len = strlen($db_dir1)-1;
        if($len < 1) return;

        if($db_dir1{$len} == '/') $db_name = $db_dir1.$table."/".$db_name;
        else $db_name = $db_dir1."/".$table."/".$db_name;

        $db->open(null, $db_name, null);
        $key =sprintf("%010d",$left);
        $cursor = $db->cursor();
        $cursor->get($key, $val, DB_SET_RANGE);

        while(1)
        {
                $gpos = (int)$key;
                if($gpos >= $right) break;

                $arr = explode(",",$val);
                $amt1 = (double)$arr[0];
                $amt2 = (double)$arr[1];
                $class = "read";

                //If the class is new then create a series to represent it
                if (!isset($result[$class]))
                {
                        $result[$class] = array();
                }
                if (!isset($result[$class]["$gpos"]))
                {
                        $result[$class]["$gpos"] = array($gpos,0,0);
                }
                if ($amt1 > 0)
                {
                        $result[$class]["$gpos"][1] = $amt1;
                }
                if ($amt2 > 0)
                {
                        $result[$class]["$gpos"][2] = $amt2;
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

function get_histogram_data(&$result, $table, $assembly, $left, $right, $bases, $pixels)
{
	$unit = round($bases / $pixels);

        $db = new Db4();
	if($assembly == "X") $db_name = $table.".db22";
	else if($assembly == "Y") $db_name = $table.".db23";
	else
	{
		$val = (int)$assembly;
		$db_name = sprintf(".db%02d",$val-1);
		$db_name = $table.$db_name;
	}

	if(!file_exists($db_name)) error("No data available");
	
        $db->open(null, $db_name, null);
        $key =sprintf("%010d",$left);
        $cursor = $db->cursor();
        $cursor->get($key, $val, DB_SET_RANGE);

        while(1)
        {
                $ikey = (int)$key;
                if($ikey >= $right) break;
                $val = str_replace("|", "", $val);
		$r = explode(",",$val);
	
		if($r[3] == "" || strlen($r[3]) > 5) 
			$class = 'read';
		else $class = $r[3];

		$start  = $ikey;
		$end    = $r[0] + 0;
		$strand = $r[1];
		$count  = (double)$r[2];
		$copies = (double)$r[2];

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
	$cursor->close();
        $db->close();
        unset($db);
}
function get_histogram($urls, $db_dir, $table, $assembly, $left, $right, $bases, $pixels)
{
	$query = "$urls,$assembly,$right,$left";
	
	if (cache_exists($query)) cache_stream($query);
	$result = array();

	$chrs = explode(",",$urls);
	$fname = "/data/annoj-hic/GSE18199/HIC_gm06690_chr".$chrs[0]."_chr".$chrs[1]."_1000000_obs.txt";
	$fname1 = "/data/annoj-hic/GSE18199/HIC_gm06690_chr".$chrs[1]."_chr".$chrs[0]."_1000000_obs.txt";
	if(!file_exists($fname) && !file_exists($fname1))
	{
		cache_create($query,$result,true);
		return;
	}
	if(file_exists($fname1)) $fname = $fname1;
	
	$fp = fopen($fname, "r");
	if(!$fp)
	{
		cache_create($query,$result,true);
		return;
	}
	$row = 0;
	while(!feof($fp))
	{
		$line = fgets($fp);
		trim($line);
		$line = str_replace("\n","",$line);

		$row++;
		if($row <= 2 || $line == "") continue;

		$r = explode("\t",$line);
		$rr = array_slice($r, 1);
		$result[] = $rr;
		unset($rr);
	}
	foreach ($result as &$content)
	foreach ($content as &$val)
	{
		$val = round($val/200 * 255);
	}
	/*
	$fp = fopen("/tmp/test.txt","a+");
	$string = json_encode($result);
	fwrite($fp, "$action2: $string\n");
	fclose($fp);
	*/
	cache_create($query,$result,true);
}
error('Invalid action requested: ' . $action);
?>
