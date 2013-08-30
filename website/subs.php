<?php
require_once 'config.php';

// Extract parameter values from a string of track description.
function extrparam($trackstr){
   $trackstruct = array(
      'name'   => '',
      'type'   => 'ReadsTrack',
      'url'   => ''
      );
      $a_param = explode(';', $trackstr);   // split into parameter=value pairs.
      foreach($a_param as $pair){
         list($param, $val) = explode('=', $pair);
         switch($param){
            case 'name':   $trackstruct['name'] = $val;   break;
            case 'type':   $trackstruct['type'] = $val;   break;
            case 'url':      $trackstruct['url'] = $val;      break;
         }
      }
      // Get the right track name.
      if(empty($trackstruct['name'])){   // track name is not set by user, use php file name.
         $url = $trackstruct['url'];
         $patn = '/\/(\w+)\.php$/';   // pattern for php file name.
         preg_match($patn, $url, $matches);
         $trackstruct['name'] = $matches[1];
      }

      return $trackstruct;
}

// Check whether a track string contains the right format.
function chktrk($s){
   $pattern = '/^((name|type|url)=[^\'\"]+;){0,2}((name|type|url)=[^\'\"]+)$/';
   return (bool)preg_match($pattern, $s);
}

// Format a track description from parameter values.
function formatrack($trackstruct, $t){
   list($name, $type, $path, $url, $height, $shoc) = array_values($trackstruct);
   $trackstr = "   // track configuration.
      {
         id   : 'track$t',
         name : '$name',
         type : '$type',
         path : '$path',
         data : '/proxy/$url',
         height : $height,
         showControls: $shoc
      }";
   return $trackstr;
}

// Format active tracks selection.
function activetracks($t){
   $active = "   active : [";
   for($i = 1; $i <= $t; $i++){
      $active .= ",'track$i'";
   }
   $active .= "],\n";
   return $active;
}

// Read AnnoJ track display specifications for each track type.
function read_aj_spec(){
   global $db;
   $query = "select track_type, aj_path, aj_height from annoj";
   $stmt = $db->prepare($query);
   $stmt->execute() or goBack("Error happened. Please try later.");
   while ($row = $stmt->fetch()) {
      $aj_spec[$row['track_type']] = array('aj_path'   => $row['aj_path'],
                                  'aj_height' => $row['aj_height']
                                 );
   }
   return $aj_spec;
}

// Read default track height from table annoj.
function read_default_ht($trk_id){
   global $db;
   $query = "select aj_height from track left join annoj using(track_type) where track_id = ?";
   $stmt = $db->prepare($query);
   $stmt->bindParam(1, $trk_id);
   $stmt->execute() or goBack("Error happened. Please try later.");
   $row = $stmt->fetch();
   $trk_ht = $row['aj_height'];
   return $trk_ht;
}

// set default annoj height for each track.
function set_default_ht($a_trk){
   foreach($a_trk as $trk_id){
      $a_tht[] = read_default_ht($trk_id);
   }
   return $a_tht;
}

// Get current datetime with default timezone set to Pacific time.
function get_date(){
   if(!date_default_timezone_set('America/Los_Angeles')) {
      trigger_error('Default timezone does not set properly, use server timezone.');
   }
   $datestr = date('Y-m-d H:i:s');   // in mysql datetime format.
   return $datestr;
}

// Display the small SDEC logo.
function write_small_logo(){
   //$output[] = '<div id = "logo" class = "small">'; //by Jie
   //$output[] = '<a href="login.php">';
   $output[] = '<img src="img/SDEC logo small.gif" alt="SDEC logo small" style="border: 0;" />';
   //$output[] = '</a>';
   //$output[] = '</div>'; //by Jie
   return join('', $output);
}

// Display the user toolbar.
function write_toolbar(){
   $output[] = '<p><a href="brwconf.php">Browse configs</a></p>';
   $output[] = '<p><a href="brwtrk.php">Browse tracks</a></p>';
   $output[] = '<p><a href="subtracks.php">Paste tracks</a></p>';
   $output[] = '<p><a href="profile.php">My profile</a></p>';
   return join('', $output);
}

// Display the user menubar.
function write_menubar(){
   //$output[] = '<div id="menubar">';
   $username = $_SESSION['username'];
   $output[] = "<p>Welcome, $username!&nbsp;<a href=\"logout.php\">Logout</a></p>";
   //$output[] = '</div>';
   return join('', $output);
}
?>
