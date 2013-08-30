<?php
$host  = $_SERVER['HTTP_HOST'];
// Preset AJ configuration text;
$conf_head = "AnnoJ.config = {

	tracks : [

		// Your tracks go here:";

$conf_tail1 = "	genome    : 'http://$host/fetchers/homo_sapiens.php',
	bookmarks : 'http://$host/fetchers/homo_sapiens.php',
	stylesheets : [
	],
	location : {
";
$conf_tail3 = "	genome    : 'http://$host/fetchers/mus_musculus.php',
	bookmarks : 'http://$host/fetchers/mus_musculus.php',
	stylesheets : [
	],
	location : {
";
$conf_tail4 = "	genome    : 'http://$host/fetchers/zv9meta.php',
	bookmarks : 'http://$host/fetchers/zv9meta.php',
	stylesheets : [
	],
	location : {
";
$conf_tail2 = "
	},
	admin : {
		name  : 'Julian Tonti-Filippini, Tao Wang',
		email : 'tontij01@student.uwa.edu.au',
		notes : 'Perth, Western Australia (UTC +8)'
	}
};
";

// Preset AJ html text.
$html_left = "<html>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
	<title>Genome Browser</title>

	<!-- ExtJS Dependencies -->
	<link type='text/css' rel='stylesheet' href='http://$host/annoj-js/resources/css/ext-all.css' />
	<script type='text/javascript' src='http://$host/annoj-js/ext-base-3.2.js'></script>
	<script type='text/javascript' src='http://$host/annoj-js/ext-all-3.2.js'></script>

	<!-- Anno-J -->
	<link type='text/css' rel='stylesheet' href='http://www.annoj.org/css/viewport.css' />
	<link type='text/css' rel='stylesheet' href='http://www.annoj.org/css/plugins.css' />
	<script type='text/javascript' src='http://$host/annoj-js/excanvas.js'></script>
	<script type='text/javascript' src='http://$host/annoj-js/aj-min5-src.js'></script>
		
	<!-- Config -->
	<script type='text/javascript' src='";

$html_right = "'></script>
	
</head>

<body>

	<!-- Message for people who do not have JS enabled -->
	<noscript>
		<table id='noscript'><tr>
			<td><img src='http://www.annoj.org/img/logo.jpg' /></td>
			<td>
				<p>Anno-J cannot run because your browser is currently configured to block JavaScript.</p>
				<p>To use the application please access your browser settings or preferences, turn JavaScript support back on, and then refresh this page.</p>
				<p>Thankyou, and enjoy the application!<br />- Julian</p>
			</td>
		</tr></table>
	</noscript>

	<!-- You can insert Google Analytics here if you wish -->

</body>

</html>
";

?>
