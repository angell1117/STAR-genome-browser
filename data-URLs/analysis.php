<?php
require_once 'includes/common.php';

function get_table_name($url)
{
    $url = strstr($url, "http://");
    if(!$url) return false;
    $url = substr($url,strlen("http://"));

    $url = strstr($url, "/");
    if(!$url) return false;
    $url = substr($url,1);
     
    $url = $_SERVER['DOCUMENT_ROOT']."/".$url;
    if(!file_exists($url)) return false;

    $fp = fopen($url, "r");
    if(!$fp) return false;
    $cnt = 0;
    while($line = fgets($fp, 1024))
    {
      $a = strstr($line,"\$table");
      if($a)
      {
        $p1 = strpos($line, "'");
        $p2 = strrpos($line, "'");
        if($p1 && $p2) $db_table = substr($line,$p1+1, $p2-$p1-1);
      }
      $a = strstr($line,"\$db_dir");
      if($a)
      {
        $p1 = strpos($line, "'");
        $p2 = strrpos($line, "'");
        if($p1 && $p2) $db_dir = substr($line,$p1+1, $p2-$p1-1);
      }
      if($db_table && $db_dir) break;
      if($cnt > 15) break;
      $cnt++;
    }
    fclose($fp);
    if($db_dir && !ereg("\/$",$db_dir)) $db_dir = $db_dir."/";

    $fname = $db_dir.$db_table;
    if(!file_exists($fname."/".$db_table.".db01")) return false;
    return $fname;
}
function wrLog($msg)
{
    $fname = "/tmp/log".date('Ymd');
    $fp = fopen($fname, "a+");
    fwrite($fp, "$msg\n");
    fclose($fp);
}
if ($action == 'syndicate')
{
    if (isset($title)) $syndication['service']['title'] = $title;
    if (isset($info))  $syndication['service']['description'] = $info;
    respond($syndication);
}

if ($action == 'range')
{
    $action2 = strtolower($action2);
    $url_list = explode(",",$urls);
    $tables = array();
    foreach ($url_list as $url)
    {
      $db_name = get_table_name($url);
      if($db_name) $tables[] = $db_name;
    }
    if (1.0 * $bases / $pixels >= 5.0)
    {
      get_histogram($tables, $action2, $assembly, $left, $right, $bases, $pixels, $fetchers);
    }
    else
    {
      $array = array(
        'success' => false,
        'message' => 'Server tried to stream result data that does not exist'
      );

      $string = json_encode($array);
      echo $string;
      exit();
    }
}

function get_histogram_data(&$result, $table, $assembly, $left, $right, $bases, $pixels)
{
    $unit = round($bases / $pixels);

    $db = new Db4();
    $p = strrpos($table, "/");
    $table = $table."/".substr($table, $p+1);

    if($assembly == "X") $db_name = $table.".db22";
    else if($assembly == "Y") $db_name = $table.".db23";
    else if($assembly == "M") $db_name = $table.".db24";
    else if($assembly == "L") $db_name = $table.".db25";
    else
    {
        $val = (int)$assembly;
        $db_name = sprintf(".db%02d",$val-1);
        $db_name = $table.$db_name;
    }

    $level_db_name = $db_name."-$unit";
    if(!file_exists($level_db_name) && !file_exists($db_name)) return;

    //determine whether levelized data exists
    $level_db = false;
    if(file_exists($level_db_name)){
       $db_name = $level_db_name;
       $level_db = true;
     }
    
    $db->open(null, $db_name, null);
    $key =sprintf("%010d",$left);
    $cursor = $db->cursor();
    $cursor->get($key, $val, DB_SET_RANGE);

    while(1)
    {
        $ikey = (int)$key;
        if($ikey >= $right) break;

        if($level_db)
        {
             $gpos = $ikey;
             $class = 'read';
             $arr = explode(",",$val);
             
             $group = floor(count($arr)/2);
             for ($i = 0; $i < $group; $i++)
             {
                  $amt1 = (double)$arr[$i*2];
                  $amt2 = (double)$arr[$i*2+1];

                  if($amt1 <= 0 && $amt2 <= 0) continue;
                  //If the class is new then create a series to represent it
                  if (!isset($result[$class]))
                  {
                      $result[$class] = array();
                  }
                  if (!isset($result[$class]["$gpos"]))
                  {
                      $result[$class]["$gpos"] = array($gpos,0,0);
                  }
                  if ($amt1 > 0)
                  {
                     $result[$class]["$gpos"][1] += $amt1;
                  }
                  if ($amt2 > 0)
                  {
                     $result[$class]["$gpos"][2] += $amt2;
                  }
             }
        }
        else{
            $val = str_replace("|", "", $val);
            $r = explode(",",$val);

            //for simplicity, multiple seriese will be represented as one series    
            $class = 'read';

            $start  = $ikey;
            $end    = $r[0] + 0;
            $strand = $r[1];
            $count  = (double)$r[2];
            $copies = (double)$r[2];

            //If the class is new then create a series to represent it
            if (!isset($result[$class]))
            {
               $result[$class] = array();
            }
                            
            //Determine the range of x positions covered by the read
            $x1 = floor($start * $pixels / $bases);
            $x2 = ceil($end * $pixels / $bases);
        
            //For each of the x positions, convert to a genome position and add to the count
            for ($x=$x1; $x<$x2; $x++)
            {
               $gpos = $x * $bases / $pixels;
        
               if (!isset($result[$class]["$gpos"]))
               {
                  $result[$class]["$gpos"] = array($gpos,0,0);
               }
            
               $amt = 0;
            
               if ($gpos < $start)
               {
                  $amt = $gpos + $unit - $start;
               }
               else if ($gpos + $unit > $end && $gpos < $end)
               {
                  $amt = $end - $gpos;
               }
               else
               {
                  $amt = $unit;
               }
            
               $amt *= $copies / $unit;
            
               if ($strand == '+')
               {
                  $result[$class]["$gpos"][1] += $amt;
               }
               else 
               {
                  $result[$class]["$gpos"][2] += $amt;
               }
            }
        }
        if($cursor->get($key, $val, DB_NEXT) != 0) break;
    }
    $cursor->close();
    $db->close();
    unset($db);    
}
function get_histogram($tables, $action2, $assembly, $left, $right, $bases, $pixels, $fetchers)
{
    $table_names = '';
    foreach ($tables as $table)
    {
      $p = strrpos($table, "/");
      $table_names = $table_names.substr($table, $p+1).',';
    }
    if($table_names == '') return;
    $query = "$table_names,$assembly,$right,$left,$action2";
    //if (cache_exists($query)) cache_stream($query);
    if($action2 == "intersection")
    {
      $result = array();
      foreach ($tables as $table)
      {
        $result1 = array();    
        get_histogram_data($result1, $table, $assembly, $left, $right, $bases, $pixels);
        if(!isset($result1['read'])) continue;
        if(!isset($result2))
        {
          $result2 = array(); 
          $result2 = $result1['read'];
        }
        else $result2 = array_intersect_key($result2,$result1['read']);
        unset($result1);
      }

      $clean = array();
      foreach ($result2 as $pos => $datum)
      {
        $clean[] = $datum;
      }
      $result["0"] = $clean;
      unset($clean);
    }
    if($action2 == "merge")
    {
      $result = array();
      $group = 0;
      foreach ($tables as $table)
      {
        $result1 = array();    
        get_histogram_data($result1, $table, $assembly, $left, $right, $bases, $pixels);

        foreach ($result1 as $class => $data)
        {
          $clean = array();
          foreach ($data as $datum)
          {
            $clean[] = $datum;
          }
          $group++;
          $result[$group] = $clean;
          unset($clean);
        }
        unset($result1);
      }
    }
    if($action2 == "summation" || $action2 == "subtract")
    {
      $result = array();
      foreach ($tables as $table)
      {
        $result1 = array();    
        get_histogram_data($result1, $table, $assembly, $left, $right, $bases, $pixels);
        foreach ($result1 as $class => $data)
        {
          $clean = array();
          foreach ($data as $datum)
          {
            $clean[] = $datum;
          }
          if(!isset($result2)) $result2 = $clean;
          else
          {
            $i = 0;
            $j = 0;
            $clean2 = array();
            while(1)
            {
              if(isset($clean[$i]) && isset($result2[$j]))
              {
                if($clean[$i][0] < $result2[$j][0]) 
                {
                  if($action2 == "summation") $clean2[] = $clean[$i];
                  $i++;
                }
                else if($clean[$i][0] > $result2[$j][0]) 
                {
                  $clean2[] = $result2[$j];
                  $j++;
                }
                else if($clean[$i][0] == $result2[$j][0]) 
                {
                  if($action2 == "summation")
                  {
                    $a = $clean[$i][1] + $result2[$j][1];
                    $b = $clean[$i][2] + $result2[$j][2];
                  }
                  if($action2 == "subtract")
                  {
                    $a = $result2[$j][1] - $clean[$i][1];
                    $b = $result2[$j][2] - $clean[$i][2];
                    if($b < 0) $b = -$b;
                  }
                  if($a > 0) $clean2[] = array($clean[$i][0], $a, $b);
                  else $clean2[] = array($clean[$i][0], 0, $b - $a);
                  $j++;
                  $i++;
                }
              }
              else if(isset($clean[$i]))
              {
                if($action2 == "summation") $clean2[] = $clean[$i];
                $i++;
              }
              else if(isset($result2[$j]))
              {
                $clean2[] = $result2[$j];
                $j++;
              }
              else break;
            }
            $result2 = $clean2;
            unset($clean2);
          }
          unset($clean);
        }
        unset($result1);
      }
      if($action2 == 'summation') $result["1"] = $result2;
      if($action2 == 'subtract') $result["2"] = $result2;
      unset($result2);
    }
    if($action2 == "correlation")
    {
        $result = array();
        foreach ($tables as $table)
        {
            $result1 = array();    
            get_histogram_data($result1, $table, $assembly, $left, $right, $bases, $pixels);
            foreach ($result1 as $class => $data)
            {
                $clean = array();
                foreach ($data as $datum)
                {
                    $clean[] = $datum;
                }
                if(!isset($result2)) $result2 = $clean;
                else
                {
                    $i = 0;
                    $j = 0;
                    $x = 0;
                    $y = 0;
                    $x2 = 0;
                    $y2 = 0;
                    $xy = 0;
                    $wd = 0;

                    while(1)
                    {
                      $wd++;
                      if(isset($clean[$i]) && isset($result2[$j]))
                      {
                        if($clean[$i][0] < $result2[$j][0]) 
                        {
                          $a = $clean[$i][1] + $clean[$i][2];
                          $x += $a;
                          $x2 += $a * $a;
                          $i++;
                        }
                        else if($clean[$i][0] > $result2[$j][0]) 
                        {
                          $a = $result2[$j][1] + $result2[$j][2];
                          $y += $a;
                          $y2 += $a * $a;
                          $j++;
                        }
                        else if($clean[$i][0] == $result2[$j][0]) 
                        {
                          $a = $clean[$i][1] + $clean[$i][2];
                          $b = $result2[$j][1] + $result2[$j][2];
                          $x += $a;
                          $x2 += $a * $a;
                          $y += $b;
                          $y2 += $b * $b;
                          $xy += $a * $b;
                          $j++;
                          $i++;
                        }
                      }
                      else if(isset($clean[$i]))
                      {
                        $a = $clean[$i][1] + $clean[$i][2];
                        $x += $a;
                        $x2 += $a * $a;
                        $i++;
                      }
                      else if(isset($result2[$j]))
                      {
                        $a = $result2[$j][1] + $result2[$j][2];
                        $y += $a;
                        $y2 += $a * $a;
                        $j++;
                      }
                      else break;
                    }
                    $aa = $xy - $x*$y/$wd;
                    $bb = sqrt(($x2-$x*$x/$wd)*($y2-$y*$y/$wd));
                    $result[] = $aa / $bb;
                }
                unset($clean);
            }
            unset($result1);
        }
        unset($result2);
    }
    if($action2 == "intensity")
    {
        $result = array();
        $max = 0;
        foreach ($tables as $table)
        {
            $result1 = array();    
            get_histogram_data($result1, $table, $assembly, $left, $right, $bases, $pixels);
            foreach ($result1 as $class => $data)
            {
                $sum = 0;
                foreach ($data as $datum)
                {
                    $sum += $datum[1];
                    $sum += $datum[2];
                }
                $result[] = $sum;
                if($max < $sum) $max = $sum;
            }
            unset($result1);
        }
        foreach ($result as &$value)
        {
           $value /= $max;
        }
    }
    if($action2 == "Peakcall" || $action2 == "peakcall")
    {
        $document_root  = $_SERVER['DOCUMENT_ROOT'];
        $host  = $_SERVER['HTTP_HOST'];
        $p = strrpos($tables[0], "/");
        $newdb = "peakcall-".substr($tables[0], $p+1);
        $url = "/proxy/http://$host/fetchers/analysis/$newdb.php";
        $fw = fopen("$document_root/fetchers/analysis/$newdb.php", "w+");
        if(!$fw) return false;

        fwrite($fw, "<?php\n");
        fwrite($fw, "require_once '../includes/common.php';\n\n");
        fwrite($fw, "\$table = '$newdb';\n");
        fwrite($fw, "\$title = '$newdb';\n");
        fwrite($fw, "\$info = '$newdb';\n");
        fwrite($fw, "\$db_dir  = '/data/annoj-analysis/';\n");
        fwrite($fw, "\$file_track_type = 'IntensityTrack';\n");
        fwrite($fw, "require_once '../includes/intensitytrack.php';\n");
        fwrite($fw, "?>");

        fclose($fw);

        $log = "/tmp/log".date('Ymd');
        $cmd = "/usr/local/star/ngsproc/callMacs $tables[0] $newdb 1>>$log 2>>$log &";
        system($cmd);
        wrLog($cmd);
        respond($url);
    }
    cache_create($query,$result,true);
}
error('Invalid action requested: ' . $action . "," . $action2);
?>
