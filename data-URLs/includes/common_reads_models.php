<?php

require_once 'common.php';

function getmicrotime()
{ 
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
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
	
	// select count(*) directly. use parent='' to speed up.
	// use pattern match for id only. fulltext search for (id,description).
	// when count, do not sort the results for the sake of speed.
	$d = query("select count(if(parent='' and (id like '%$query%' or match(id,description) against('$query')), 1, null)) as gcnt from $table");
	$r = mysql_fetch_assoc($d);
	$count = $r['gcnt'];
	// by: Li Shen.
	
	// use parent='' to speed up.
	// use pattern match for id only. fulltext search for (id,description). results sorted by match scores.
	// when count, do not sort the results for the sake of speed.
	$d = query("select id, assembly, start, end, 
		concat(substring(description,1,120), '...') as description  
		from $table 
		where parent = '' and (id like '%$query%' or match(id,description) against('$query')) 
		order by match(id,description) against('$query') desc 
		limit $start,$limit");
	// by: Li Shen.
	$list = array();
	
	while ($r = mysql_fetch_assoc($d)) {
		$list[] = $r;
	}
	die(json_encode(array(
		'success' => true,
		'count' => $count,
		'rows' => $list
	)));	
}

if ($action == 'describe')
{
	$id = isset($_GET['id']) ? clean_string($_GET['id']) : false;
	if ($limit === false) error('Illegal id provided in request');
	$d = query("select id, assembly, start, end, description from $table where id = '$id'");

	if (mysql_num_rows($d) == 0) respond(null);
	
	$r = mysql_fetch_assoc($d);
	respond($r);
}	

if ($action == 'range')
{
	get_models($table, $assembly, $left, $right, $pixels, $bases);
}
function get_models($table, $assembly, $left, $right, $pixels, $bases)
{
	$query = "select parent, id, strand, class, start, end-start+1 from $table where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";

        if (cache_exists($query))
        {
                cache_stream($query);
        }


        $d = query($query);
        $data = array();

        while ($r = mysql_fetch_row($d))
        {
                $r[4] += 0;
                $r[5] += 0;

                //Skip if too small to be seen anyway
                if ($r[0] && round($r[5] * $pixels / $bases) == 0) continue;
                $data[] = $r;
        }

        cache_create($query, $data, true, $table);
}
error('Invalid action requested: ' . $action);
?>
