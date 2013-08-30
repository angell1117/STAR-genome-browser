<?php

require_once 'common.php';

if ($action == 'range')
{
	if($left < 0) $left = 0;
	$query = "select parent, id, strand, class, start, end-start+1 from chr$assembly where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";

        if (cache_exists($query))
        {
                cache_stream($query);
        }

        $data = array();
	$seq = GetSeqRegion($db_dir, $assembly, $left, $right);
       	$data[0] = "GENOME";
       	$data[1] = $left;
       	$data[2] = $seq;

        cache_create($query, $data, true, $table);
}
function GetSeqRegion($db_dir, $assembly, $left, $right)
{
	$fname = $db_dir."chr".$assembly.".fa";
	$fp = fopen($fname, 'r');
	if(!$fp) return;

	$lines = intval($left / 50);
	$start = $left - $lines*50;
	$len = $right - $left;
	$f_pos = strlen($assembly) + 5 + 51*$lines + $start;

	fseek($fp, $f_pos, SEEK_SET);
	$buf_len = $len + intval($len / 50) + 16;
	$buf = fread($fp, $buf_len);

	$seq = "";
	$count = 0;
	for($i = 0;$i < strlen($buf);$i++)
	{
		if($buf[$i] != "\n")
		{
			$seq = $seq.$buf[$i];
			$count++;
			if($count >= $len) break;
		}
	}
        fclose($fp);
	return $seq;
}
error('Invalid action requested: ' . $action);
?>
