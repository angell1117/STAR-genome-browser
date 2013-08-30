<?php

require_once 'common.php';

function getmicrotime()
{ 
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
} 
function getrecord($content,$start)
{
	$result = array();
	$pos1 = strpos($content,"|");

	$str1 = substr($content,$pos1+1);
	$pos2 = strpos($str1,"|");
	$str2 = substr($str1,0,$pos2);

	$arr = explode(",",$str2);

	$result[] = '';
	$result[] = "$start";
	$result[] = $arr[1];
	$result[] = 'CDS';
	$result[] = $start;
	$result[] = ((int)$arr[0] - $start)*20;
	return $result;
		
}
if ($action == 'syndicate')
{
	if (isset($title)) $syndication['service']['title'] = $title;
	if (isset($info))  $syndication['service']['description'] = $info;
	respond($syndication);
}

if ($action == 'lookup')
{
	$limit = isset($_GET['limit']) ? clean_int($_GET['limit'])    : false;
	$query = isset($_GET['query']) ? clean_string($_GET['query']) : false;
	$start = isset($_GET['start']) ? clean_int($_GET['start'])    : false;

	if ($limit === false) error('Illegal limit value in lookup request');
	if ($query === false) error('Illegal query value in lookup request');
	if ($start === false) error('Illegal start value in lookup request');
}

if ($action == 'describe')
{
	$id = isset($_GET['id']) ? clean_string($_GET['id']) : false;
	if ($limit === false) error('Illegal id provided in request');
}	

if ($action == 'range')
{
	$query = "select '', id, strand, 'CDS', start, (end-start+1)*20 from $table where assembly='$assembly' and start >= $left and end <= $right order by start asc, end desc";
	
	if (cache_exists($query))
	{
		cache_stream($query);
	}
	
	$fp = fopen("/tmp/running_time.txt","a+");
	$time_start = getmicrotime();

	$data = array();

	$dbenv = new Db4Env(null);
        $dbenv->set_data_dir("/var/anno-j-data");
        $dbenv->open("/var/anno-j-data",DB_CREATE|DB_INIT_MPOOL,null);

        $db = new Db4($dbenv);
        $db->open(null, "h3k18ac.db00", null);

	sprintf($key,"%010d",$left);
        $cursor = $db->cursor();
        $cursor->get($key, $val, DB_SET);

        while( 0 == $cursor->get($key, $val, DB_NEXT))
        {
                $ikey = (int)$key;
		if($ikey >= $right) break;
	
		$r = getrecord($val,$ikey);
		if ($r[0] && round($r[5] * $pixels / $bases) == 0) continue;
		$data[] = $r;
        }

	$time_end = getmicrotime(); 
	$time = $time_end - $time_start; 

        fwrite($fp,"total time:$time seconds!");
        fclose($fp);

	cache_create($query, $data, true, $table);

        $cursor->close();
        $db->close();
	unset($db);
        $dbenv->close(0);
	unset($dbenv);
}

error('Invalid action requested: ' . $action);

?>
