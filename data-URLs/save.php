<?php
require_once 'includes/common.php';

function url2dir($url)
{
  $name = str_replace("://", "", $url);

  $name = strstr($name, "/");
  if(!$name) return $url;
  $name = substr($name,1);
   
  $name = $_SERVER['DOCUMENT_ROOT']."/".$name;
  if(!file_exists($name)) return "";
  return $name;
}
function get_js_name($url)
{
  $url = url2dir($url);

  $fp = fopen($url, "r");
  if(!$fp) return "";
  while($line = fgets($fp, 1024))
  {
    $a = strstr($line,"javascript");
    if(!$a) continue;
    else
    {
      $a = strstr($a,"=");
      $p1 = strpos($a, "'");
      $p2 = strrpos($a, "'");
      if($p1 && $p2) $js_name = substr($a,$p1+1, $p2-$p1-1);
    }
  }
  fclose($fp);

  $p = strrpos($url, "/");
  if($p) $url = substr($url,0, $p+1);

  $url = $url.$js_name;
  if(!file_exists($url)) return "";
  return $url;
}

function msg($success, $name)
{
  $array = array(
     'success' => $success,
     'message' => $name
  );
  $string = json_encode($array);
  echo $string;
  return;
}
if ($action == 'save')
{
  $name = clean_string($_GET['name']);
  $slot = clean_string($_GET['slot']);
  $tracks = json_decode(stripslashes(clean_string($_GET['tracks'])),true);
  $active = json_decode(stripslashes(clean_string($_GET['active'])),true);
  $genome = json_decode(stripslashes(clean_string($_GET['genome'])),true);
  $bookmarks = json_decode(stripslashes(clean_string($_GET['bookmarks'])),true);
  $stylesheets = json_decode(stripslashes(clean_string($_GET['stylesheets'])),true);
  $location = json_decode(stripslashes(clean_string($_GET['location'])),true);
  $settings = json_decode(stripslashes(clean_string($_GET['settings'])),true);
  $admin = json_decode(stripslashes(clean_string($_GET['admin'])),true);

  $conf = "AnnoJ.config = {\n";

  //track section
  $conf = $conf."\ttracks : [\n";
  $first = true;
  foreach ($tracks as $key => $value)
  {
    if(is_array($value))
    {
      if($first){
        $conf = $conf."\t\t{\n";
        $first = false;
      }
      else $conf = $conf."\t\t},\n\t\t{\n";

      $showControl_val = 'true';
      foreach ($value as $id => $val)
      {
        if($id == 'color')
        {
          $ff = true;
          $conf = $conf."\t\t\t$id : {\n";
          foreach ($val as $ii => $vv)
          {
            if($ff)
            {
              $ff = false;
              $conf = $conf."\t\t\t\t$ii : '$vv'";
            } else $conf = $conf.",\n\t\t\t\t$ii : '$vv'";
          }
          $conf = $conf."\n\t\t\t},\n";
        }
        else if($id=='urls' || $id=='action' || $id=='id' || $id=='name' || $id=='type' || $id=='path' ||$id=='data' || $id=='assembly')
        {
          $conf = $conf."\t\t\t$id : '$val',\n";
        }
        else if($id == 'showControls')
        {
          if($val == '1') $showControl_val = 'true';
          else $showControl_val =  'false';
        }
        else if($id == 'single')
        {
          if($val) $conf = $conf."\t\t\t$id : true,\n";
          else $conf = $conf."\t\t\t$id : false,\n";
        }
        else $conf = $conf."\t\t\t$id : $val,\n";
      }
      $conf = $conf."\t\t\tshowControls : $showControl_val\n";
    }
    else
    {
      msg(false, $name);
       exit();
    }
  }
  $conf = $conf."\t\t}\n\t],\n";

  //active section
  $conf = $conf."\tactive : [";
  foreach ($active as $val)
  {
    $conf = $conf.",'$val'";
  }
  $conf = $conf."],\n";
  
  //genome and bookmarks section
  $conf = $conf."\tgenome : '$genome',\n";
  $conf = $conf."\tbookmarks : '$bookmarks',\n";

  //stylesheets section
  $conf = $conf."\tstylesheets : [";
  foreach ($stylesheets as $val)
  {
    $conf = $conf.",'$val'";
  }
  $conf = $conf."],\n";

  $last_val = 1;
  //location section
  $conf = $conf."\tlocation : {\n";
  foreach ($location as $id => $val)
  {
    if($id=='assembly' || $id=='position')
    {
      $conf = $conf."\t\t$id : '$val',\n";
    }
    else if($id == 'pixels')
    {
      $last_val = $val;
    }
    else $conf = $conf."\t\t$id : $val,\n";
  }
  $conf = $conf."\t\tpixels : $last_val\n";
  $conf = $conf."\t},\n";

  //settings section
  $conf = $conf."\tsettings : {\n";
  foreach ($settings as $id => $val)
  {
    if($id == 'yaxis')
    {
      $last_val = $val;
    }
    else $conf = $conf."\t\t$id : $val,\n";
  }
  $conf = $conf."\t\tyaxis : $last_val\n";
  $conf = $conf."\t},\n";

  //admin section
  $conf = $conf."\tadmin : {\n";
  foreach ($admin as $id => $val)
  {
    if($id == 'notes')
    {
      $last_val = $val;
    }
    else $conf = $conf."\t\t$id : '$val',\n";
  }
  $conf = $conf."\t\tnotes : '$last_val'\n";
  $conf = $conf."\t}\n};";

  $name = get_js_name($name);
  if($name == ""){
    msg(false, "Failed to save data!");
     exit();
  }
  if($slot != 'default') 
  {
    $snapshot = $name.".snapshots";
    $name = $name.".".$slot;
  }
  //write new configuration file
  $fp = fopen($name,"w+");
  if(!$fp){
    msg(false, "Failed to save!");
     exit();
  }
  fwrite($fp, "$conf\n");
  fclose($fp);

  if($slot != 'default') 
  {
    $fp = fopen($snapshot,"a+");
    if(!$fp){
      msg(false, "Failed to save!");
       exit();
    }
    fwrite($fp, "$slot\n");
    fclose($fp);
  }
  msg(true, "Successfully saved!");
   exit();
}
if ($action == 'remove')
{
  $name = clean_string($_GET['name']);
  $slot = clean_string($_GET['slot']);

  $name = get_js_name($name);
  if($name == ""){
    msg(false, "Failed!");
     exit();
  }
  $js = $name.".".$slot;
  $name = $name.".snapshots";

  $contents = file_get_contents($name);
  $contents = str_replace("$slot\n", "", $contents);

  $fp = fopen($name,"w+");
  if(!$fp){
    msg(false, "Failed!");
     exit();
  }
  fwrite($fp, "$contents");
  fclose($fp);
  unlink($js);

  msg(true, "Removed");
   exit();
}
if ($action == 'load')
{
  $name = clean_string($_GET['name']);
  $slot = clean_string($_GET['slot']);

  $name = get_js_name($name);
  if($name == ""){
    msg(false, "Failed to load!");
     exit();
  }
  if($slot != 'default' ) $name = $name.".".$slot;

  $settings = file_get_contents($name);
  $settings = str_replace("AnnoJ.config", "Settings", $settings);

  msg(true, $settings);
   exit();
}
if ($action == 'parameter')
{
  $name = clean_string($_GET['name']);

  $name = get_js_name($name);
  if($name == ""){
    msg(false, "Failed to load!");
    exit();
  }
  $name = $name.".snapshots";

  $contents = file_get_contents($name);
  trim($contents);
  $para = split("\n", $contents);
  $s = "{para:[";
  foreach($para as $val)
  {
    $s = $s."\"$val\",";
  }
  $s = $s."]}";
  msg(true, $s);
   exit();
}
error('Invalid action requested: ' . $action);
?>
