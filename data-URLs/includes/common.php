<?php
ob_start("ob_gzhandler");

if (!function_exists('json_encode') || !function_exists('json_decode'))
{
  include 'json.php';
  $json = new Services_JSON();
  
  if (!function_exists('json_encode'))
  {
    function json_encode($obj) {
      global $json;
      return $json->encode($obj);
    }
  }
  if (!function_exists('json_decode'))
  {
    function json_decode($s) {
      global $json;
      return $json->decode($s);
    }
  }
}

$host = 'localhost';
$user = 'sdepig';
$pass = 'sandiego2009';
$cache_dir = '/aj_cache/';

if (count($_GET) == 0) {
  $_GET = $_POST;
}
//Do nothing if no action has been specified
if (!isset($_GET['action'])) {
  error('Illegal access. A valid action must be specified');  
}

//Connect to the database
$link = mysql_connect($host, $user, $pass);

if (!$link) {
  error('Database error. Unable to connect using specified username and password');
}

//Get the user's IP address and currently executing file
$ip = $_SERVER['REMOTE_ADDR'];
$self = $_SERVER['PHP_SELF'];
$date = date("F j, Y, g:i a");

//Get the action
$action = clean_string($_GET['action']);

//Global syndication parameters (override in fetchers as necessary)
if ($action == 'syndicate')
{
  $syndication = array
  (
    'institution' => array
    (
      'name' => 'UCSD',
      'url'  => 'http://wanglab.ucsd.edu/',
      'logo' => ''
    ),
    'engineer' => array
    (
      'name'  => 'Li Shen',
      'email' => 'shen@ucsd.edu'
    ),
    'service' => array
    (
      'title'       => 'No title provided',
      'species'     => 'Homo Sapiens',
      'access'      => 'public',
      'version'     => 'Unspecified',
      'format'      => 'Unspecified',
      'server'      => '',
      'description' => 'No information available'
    )
  );
}

//Check input parameters if the action is a range request
if ($action == 'range')
{
  $assembly = isset($_GET['assembly']) ? clean_string($_GET['assembly']) : false; 
  $left     = isset($_GET['left'])     ? clean_int($_GET['left'])        : false; 
  $right    = isset($_GET['right'])    ? clean_int($_GET['right'])       : false; 
  $bases    = isset($_GET['bases'])    ? clean_int($_GET['bases'])       : false;
  $pixels   = isset($_GET['pixels'])   ? clean_int($_GET['pixels'])      : false;
  $action2 = isset($_GET['action2']) ? clean_string($_GET['action2']) : false; 
  $urls = isset($_GET['urls']) ? clean_string($_GET['urls']) : false; 
  $fetchers = isset($_GET['table']) ? clean_string($_GET['table']) : false; 

  if(preg_match("/^chr(\d+)/", $assembly, $matchs)){
    $assembly = $matchs[1];
  }
  if ($assembly === false) {
    error('Illegal assembly value in range request');
  }
  if ($left === false) {
    error('Illegal left value in range request');
  }
  if ($right === false) {
    error('Illegal right value in range request');
  }
  if ($bases === false) {
    error('Illegal base value in range request');
  }
  if ($pixels === false) {
    error('Illegal pixel value in range request');
  }
}

//Handle requests involving bookmarks
if ($action == 'bookmarks_save')
{
  $bookmarks = isset($_POST['bookmarks']) ? clean_string($_POST['bookmarks']) : false;

  if ($bookmarks === false) {
    error('Illegal bookmarks specified');
  }
}

//Dump with a JSON error
function error($message)
{
  $array = array(
    'success' => false,
    'message' => $message
  );
  echo json_encode($array);
  exit();
}

//Respond with a JSON string
function respond($data)
{
  $array = array(
    'success' => true,
    'data' => $data
  );
  echo json_encode($array);
  exit();
}  

//Ensure that the value is a safe string
function clean_string($string)
{
  global $link;
  if (strlen($string) > 100000) return false;
  return mysql_real_escape_string($string, $link);
}

//Ensure that the value is a safe integer
function clean_int($int)
{
  $int += 0;
  if (!is_int($int)) return false;
  if ($int < 0) return false;
  return $int;
}

//Ensure that the value is a safe float
function clean_float($float)
{
  $float += 0;
  if (!is_float($float)) return false;
  if ($float < 0) return false;
  return $float;
}

//Do a database query bombing on error or no results
function query($query)
{
  global $link;
  $d = mysql_query($query, $link);
  if (mysql_error()) error('A problem occurred when querying the database -> ' . mysql_error());
  if (mysql_num_rows($d) == 0) respond('');
  return $d;
}

//Process a mysql result and return as JSON
function process($d)
{
  $data = array();

  while ($r = mysql_fetch_row($d)) {
    $data[] = $r;
  }
  respond($data);
}

//Determine if a cache exists
function cache_exists($query, $table = '')
{
  global $cache_dir;
  $id = md5($query) . '.gz';
  if (!$table)
  {
    return file_exists($cache_dir . $id);
  }
  return file_exists($cache_dir . $table . '/' . $id);
}

//Create a cache file using a query and its associated result
function cache_create($query, &$data, $autostream = false, $table = '')
{
  if ($autostream && cache_exists($query, $table))
  {
    cache_stream($query, $table);
    return;
  }
  global $cache_dir;
  
  if ($table)
  {
    if (!is_dir($cache_dir . $table))
    {
      mkdir($cache_dir . $table);
    }
  }
  
  $file = $cache_dir . ($table ? $table . '/' : '') . md5($query) . '.gz';
  
  $array = array(
    'success' => true,
    'data' => $data
  );
  $string = json_encode($array);
  
  $fp = gzopen($file, 'w');  
  gzwrite($fp,$string);
  gzclose($fp);
  
  if ($autostream) cache_stream($query, $table);
}

//Stream a cache to the user
function cache_stream($query, $table = '')
{
  if (!cache_exists($query, $table))
  {
    $array = array(
      'success' => false,
      'message' => 'Server tried to stream result data that does not exist'
    );
    die(json_encode($array));
  }
  global $cache_dir;
  
  $file = $cache_dir . ($table ? $table . '/' : '') . md5($query) . '.gz';

  $fp = gzopen($file, 'r');

  while (!gzeof($fp))
  {
    echo gzgets($fp);
  }
  gzclose($fp);
  exit();
}
?>
