<?php
require_once 'includes/common.php';

$table = 'demo.20000';
$title = 'demo.20000';
$info  = 'demo.20000';
$db_dir  = '/data/annoj-hic/';

$file_track_type = 'HicTrack';
set_time_limit(0);
ini_set('memory_limit','512M');

preg_match("/\.([0-9]+)$/",$table, $resolution);
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
		get_histogram($urls, $db_dir, $table, $resolution, $assembly, $left, $right, $bases, $pixels);
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
function get_histogram($urls, $db_dir, $table, $resolution, $assembly, $left, $right, $bases, $pixels)
{
	$query = "$urls,$table,$right,$left";

	if (cache_exists($query)) cache_stream($query);
	$result = array();

	$chrs = explode(",",$urls);
	$fname = $db_dir.$table."/chr".$chrs[0]."-chr".$chrs[1];
	$fname1 = $db_dir.$table."/chr".$chrs[1]."-chr".$chrs[0];
	if(!file_exists($fname) && !file_exists($fname1))
	{
		cache_create($query,$result,true);
		return;
	}
	if(file_exists($fname1)) $fname = $fname1;
	
	$left_idx = floor($left/$resolution[1]);
	$right_idx = ceil($right/$resolution[1]);
	$threshold = 2;
	if($bases == 100) $threshold = 5;
	if($bases == 1000) $threshold = 10;
	if($bases == 10000) $threshold = 20;

	$content_array = file($fname);
	if(!$content_array)
	{
		cache_create($query,$result,true);
		return;
	}
	$result['resolution'] = array($chrs[0],$chrs[1],(int)$resolution[1]);
	for($i = $left_idx; $i < $right_idx; $i++)
	{
		$r = explode("\t",$content_array[$i]);
		$rr = array();
		for($j = $left_idx; $j < $right_idx; $j++)
		{
			if($r[$j] <= $threshold) continue;
			$rr[$j] = (int)$r[$j];
		}
		if(count($rr) > 0) $result[$i] = $rr;
		unset($rr);
	}
	/*
	foreach ($result as &$content)
	foreach ($content as &$val)
	{
		$val = round($val/200 * 255);
	}
	$fp = fopen("/tmp/test.txt","a+");
	$string = json_encode($result);
	fwrite($fp, "$action2: $string\n");
	fclose($fp);
	*/
	cache_create($query,$result,true);
}
error('Invalid action requested: ' . $action);
?>
