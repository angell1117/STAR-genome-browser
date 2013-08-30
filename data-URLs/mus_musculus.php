<?php
require_once 'includes/common.php';

if ($action == 'syndicate') {
	$genome = array(
		'institution' => array(
			'name'      => 'genes',
			'homepage'  => '?',
			'logo'      => ''
		),
		'curator' => array(
			'name'  => '',
			'email' => ''
		),
		'genome' => array(
			'species'     => 'Mus musculus',
			'access'      => '',
			'version'     => '',
			'description' => '',
			'assemblies'  => array(			
				array( 'id' => '1',  'size' => 197195432 ),
				array( 'id' => '2',  'size' => 181748087 ),
				array( 'id' => '3',  'size' => 159599783 ),
				array( 'id' => '4',  'size' => 155630120 ),
				array( 'id' => '5',  'size' => 152537259 ),
				array( 'id' => '6',  'size' => 149517037 ),
				array( 'id' => '7',  'size' => 152524553 ),
				array( 'id' => '8',  'size' => 131738871 ),
				array( 'id' => '9',  'size' => 124076172 ),
				array( 'id' => '10', 'size' => 129993255 ),
				array( 'id' => '11', 'size' => 121843856 ),
				array( 'id' => '12', 'size' => 121257530 ),
				array( 'id' => '13', 'size' => 120284312 ),
				array( 'id' => '14', 'size' => 125194864 ),
				array( 'id' => '15', 'size' => 103494974 ),
				array( 'id' => '16', 'size' => 98319150  ),
				array( 'id' => '17', 'size' => 95272651  ),
				array( 'id' => '18', 'size' => 90772031  ),
				array( 'id' => '19', 'size' => 61342430  ),
				array( 'id' => 'X',  'size' => 166650296 ),
				array( 'id' => 'Y',  'size' => 15902555  ),
				array( 'id' => 'M',  'size' => 16299     ),
				array( 'id' => 'U',  'size' => 5900358   )
			)
		)	
	);
	respond($genome);
}

$db = '';
$table = '';

if ($action == 'bookmarks_load') {
	$d = query("select bookmarks from $db.$table where ip = '$ip'");
	$r = mysql_fetch_row($d);
	$data = json_decode($r[0]);
	respond($data);
}
else if ($action == 'bookmarks_save') {
	query("replace into $db.$table (ip, bookmarks) values ('$ip', '$bookmarks')");
	respond('');
}

error('Invalid action requested: ' . $action);
	
?>
