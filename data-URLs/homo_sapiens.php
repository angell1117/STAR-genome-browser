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
			'species'     => 'Homo Sapiens',
			'access'      => '',
			'version'     => '',
			'description' => '',
			'assemblies'  => array(			
				array( 'id' => '1',  'size' => 247249719 ),
				array( 'id' => '2',  'size' => 242951149 ),
				array( 'id' => '3',  'size' => 199501827 ),
				array( 'id' => '4',  'size' => 191273063 ),
				array( 'id' => '5',  'size' => 180857866 ),
				array( 'id' => '6',  'size' => 170899992 ),
				array( 'id' => '7',  'size' => 158821424 ),
				array( 'id' => '8',  'size' => 146274826 ),
				array( 'id' => '9',  'size' => 140273252 ),
				array( 'id' => '10', 'size' => 135374737 ),
				array( 'id' => '11', 'size' => 134452384 ),
				array( 'id' => '12', 'size' => 132349534 ),
				array( 'id' => '13', 'size' => 114142980 ),
				array( 'id' => '14', 'size' => 106368585 ),
				array( 'id' => '15', 'size' => 100338915 ),
				array( 'id' => '16', 'size' => 88827254  ),
				array( 'id' => '17', 'size' => 78774742  ),
				array( 'id' => '18', 'size' => 76117153  ),
				array( 'id' => '19', 'size' => 63811651  ),
				array( 'id' => '20', 'size' => 62435964  ),
				array( 'id' => '21', 'size' => 46944323  ),
				array( 'id' => '22', 'size' => 49691432  ),
				array( 'id' => 'X',  'size' => 154913754 ),
				array( 'id' => 'Y',  'size' => 57772954  ),
				array( 'id' => 'M',  'size' => 16571     ),
				array( 'id' => 'L',  'size' => 48502     )
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
