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
			'species'     => 'Zebrafish',
			'access'      => '',
			'version'     => '',
			'description' => '',
			'assemblies'  => array(			
				array( 'id' => '1',  'size' => 60348388 ),
				array( 'id' => '2',  'size' => 60300536 ),
				array( 'id' => '3',  'size' => 63268876 ),
				array( 'id' => '4',  'size' => 62094675 ),
				array( 'id' => '5',  'size' => 75682077 ),
				array( 'id' => '6',  'size' => 59938731 ),
				array( 'id' => '7',  'size' => 77276063 ),
				array( 'id' => '8',  'size' => 56184765 ),
				array( 'id' => '9',  'size' => 58232459 ),
				array( 'id' => '10', 'size' => 46591166 ),
				array( 'id' => '11', 'size' => 46661319 ),
				array( 'id' => '12', 'size' => 50697278 ),
				array( 'id' => '13', 'size' => 54093808 ),
				array( 'id' => '14', 'size' => 53733891 ),
				array( 'id' => '15', 'size' => 47442429 ),
				array( 'id' => '16', 'size' => 58780683  ),
				array( 'id' => '17', 'size' => 53984731  ),
				array( 'id' => '18', 'size' => 49877488  ),
				array( 'id' => '19', 'size' => 50254551  ),
				array( 'id' => '20', 'size' => 55952140  ),
				array( 'id' => '21', 'size' => 44544065  ),
				array( 'id' => '22', 'size' => 42261000  ),
				array( 'id' => '23',  'size' => 46386876 ),
				array( 'id' => '24',  'size' => 43947580 ),
				array( 'id' => '25',  'size' => 38499472 ),
				array( 'id' => 'M',  'size' => 3000000   )
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
