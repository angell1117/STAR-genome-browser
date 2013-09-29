<?php
ob_start();
require_once 'config.php';
require_once 'subs.php';
require_once "textdefs.php";    // include all text constants.
require_once "constants.php";    // include other constants.

session_start();    // use session variables aj_conf, search_str.


function Msg($success, $msg) {
    $ret = json_encode(array(
       'success' => $success,
       'responseText' => $msg
    ));
    echo $ret;
    exit;
}
function goBack ($_str_info) {
    Msg(false, $_str_info);
    echo '<div style="text-align: center">';
    echo "<p>$_str_info</p>";
    echo "<form>";
    echo "Want to go back?&nbsp;";
    echo "<input type=\"button\" value=\"Yes\" onClick='window.history.back()'>";
    echo "</form>";
    echo '</div>';
    exit;
}

function check_value($value,$method) {
    if ($method == 'w') {
        $fp = fopen('/tmp/data.txt', 'w');
    } else {
        $fp = fopen('/tmp/data.txt', 'a');
    }
    fwrite($fp, "$value\n");
    fclose($fp);
}

function check_input($value) {
    if(get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    if(!is_numeric($value)) {
        $value = mysql_real_escape_string($value);
    }
    return $value;
}

// Show all track option categories in database.
function show_track_option() {
    global $db;
    $query = "select  organism as name, 'organism' as category from organism union select mark as name, 'mark' as category from mark union select type as name, 'cell' as category from type union select data_type as name, 'experiment' as category from data_type union select center as name, 'center' as category from center";
    $stmt = $db->prepare($query);
    $stmt->execute() or goBack("Error happened. Please try later.");
 
    $list = array();
    $num_rows = 0;
    while ($r = $stmt->fetch()) {
        $list[] = $r;
        $num_rows++;
    }
    $result_list = json_encode(array(
       'success' => true,
       'count' => $num_rows,
       'rows' => $list
    ));
    return $result_list;
}

function show_annotation() {
    global $db;
    $query = "select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where track_type='ModelsTrack' and access='public'";
    $stmt = $db->prepare($query);
    $stmt->execute() or goBack("Error happened. Please try later.");
 
    $list = array();
    $num_rows = 0;
    while ($r = $stmt->fetch()) {
       $list[] = $r;
       $num_rows++;
    }
    $result_list = json_encode(array(
      'success' => true,
      'count' => $num_rows,
      'rows' => $list
    ));
  return $result_list;
}

function show_track() {
    global $db;

    $username = $_SESSION['username'];
    $direction  = 'asc';
    $sort_field = 'upload_date';
    if (!empty($_POST['sort'])) $sort_field = $_POST['sort'];
    if (!empty($_POST['dir'])) $direction = $_POST['dir'];

    $start = 0;
    $limit = 40;
    if(!empty($_POST['start'])) $start = $_POST['start'];
    if(!empty($_POST['limit'])) $limit = $_POST['limit'];

    $postfix = " order by $sort_field  $direction limit $start, $limit";
    $action = $_POST['action'];
    if ($action == 'create') {
        $trkids = $_POST['trkids'];
        $trkhts = $_POST['trkhts'];
        $conf_name = $_POST['conf_name'];
        $conf_desc = $_POST['conf_desc'];
        $datestr = get_date();
        $start_chr = 1;
        $start_pos = 1;
        $bases = 200;//default value for each track
        $pixels = 1;

        if (strlen($trkids)>1500) {
            goBack("Too many tracks!");
        }
	$view_cnt = 0;
        $query = "insert into configuration (conf_name, build_date, description, start_chr, start_pos, bases, pixels, trk_ids, trk_hts, user_id, lastview_date, view_count) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $conf_name);
        $stmt->bindParam(2, $datestr);
        $stmt->bindParam(3, $conf_desc);
        $stmt->bindParam(4, $start_chr);
        $stmt->bindParam(5, $start_pos);
        $stmt->bindParam(6, $bases);
        $stmt->bindParam(7, $pixels);
        $stmt->bindParam(8, $trkids);
        $stmt->bindParam(9, $trkhts);
        $stmt->bindParam(10, $username);
        $stmt->bindParam(11, $datestr);
        $stmt->bindParam(12, $view_cnt);
        $stmt->execute() or goBack("Database error!");
        Msg(true, 'OK!');
    }
    if ($action == 'removetrack') {
        $trkids = $_POST['trkids'];
        $a_trk = explode(',', $trkids);
        $count  = 0;
        foreach($a_trk as $track_id)
        {
            $track_id = trim($track_id);
            if($track_id == '') continue;

            $query = "select user_id,track_url from track where track_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $track_id);
            $stmt->execute() or goBack("Database error!");
            $r = $stmt->fetch();
            if(!$r) continue;
            if($username != $r['user_id']) continue;

            $count++;
            rm_trk_conf($track_id);
            rm_trk_files($r['track_url']);
        }
        Msg(true, "$count tracks removed!");
    }
    if ($action == 'update') {
        $trkids = $_POST['trkids'];
        $trkhts = $_POST['trkhts'];
        $conf_name = $_POST['conf_name'];
        $conf_desc = $_POST['conf_desc'];
        $conf_id = $_POST['conf_id'];

	//for encode by jhy
        $temp = $_POST['temp'];
	$myusername=$username;
	if($temp==1){
		$myusername=$myusername.".temp";
	}
	//for encode by jhy
	
        $datestr = get_date();

        if (strlen($trkids)>1500) {
            goBack("Too many tracks!");
        }
        $query = "update configuration set conf_name = ?, build_date = ?, description = ?, trk_ids = ?, trk_hts = ? where conf_id = ? and user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $conf_name);
        $stmt->bindParam(2, $datestr);
        $stmt->bindParam(3, $conf_desc);
        $stmt->bindParam(4, $trkids);
        $stmt->bindParam(5, $trkhts);
        $stmt->bindParam(6, $conf_id);
        $stmt->bindParam(7, $myusername);
        $stmt->execute() or goBack("Database error!");
        Msg(true, "OK!");
    }
    if (!empty($_POST['showmy'])) {
        $query = "select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where user_id = ?";
        $sql_cnt = "select count(*) from track where user_id = '$username'";
        $stmt = $db->query($sql_cnt);
        if(!$stmt) goBack("Error happened. Please try later.");
        $num_rows = $stmt->fetchColumn();

        $query .= $postfix;
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute() or goBack("Error happened. Please try later.");
    }
    if (!empty($_POST['showall'])) {
        $qstr = "select group_id from user where user_id = ?";
        $stmt = $db->prepare($qstr);
        $stmt->bindParam(1, $username);
        $stmt->execute() or goBack("Database error!");
        $r = $stmt->fetch();
        $group_id = $r['group_id'];

        $sql_cnt = "select sum(c) from (select count(*) as c from track where access='public' union select count(*) from track where access='private' and user_id = '$username' union select count(*) from track where access='group' and user_id in (select user_id from user where group_id = $group_id)) as total";
        $stmt1 = $db->query($sql_cnt);
        if(!$stmt1) goBack("Error happened. Please try later.");
        $num_rows = $stmt1->fetchColumn();

        $query = "select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where access='public' union select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where access='private' and user_id = ? union select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where access='group' and user_id in (select user_id from user where group_id = ?)";
        $query .= $postfix;
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $group_id);
        $stmt->execute() or goBack("Error happened. Please try later.");
    }
    if (!empty($_POST['showsdec'])) {
        $query = "select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where category = 'SDEC'";
        $sql_cnt = "select count(*) from track where category = 'SDEC'";
        $stmt = $db->query($sql_cnt);
        if(!$stmt) goBack("Error happened. Please try later.");
        $num_rows = $stmt->fetchColumn(0);

        $query .= $postfix;
        $stmt = $db->prepare($query);
        $stmt->execute() or goBack("Error happened. Please try later.");
    }
    if (!empty($_POST['keyword'])) {
        $keyword = $_POST['keyword'];
        $newkeyword = trim($keyword);
        $newkeyword = strtolower($newkeyword);
        $patterns = array("/\(/","/\)/");
        $replacements = array('','');
        $splitkeyword = preg_replace($patterns,$replacements,$newkeyword);
        $allkeyword = preg_split("/ /", $splitkeyword);
        $newskw = "";
        foreach ($allkeyword as $skw) {
            $skw = trim($skw);
            if($skw == 'and' || $skw == 'or' || $skw == 'not' || $skw == '') continue;
            if (!preg_match("/\w/", $skw)) continue;

            $newskw .= " and ((track_name like \"%$skw%\") or (upload_date like \"%$skw%\") or (track_type like \"%$skw%\") or (user_id like \"%$skw%\") or (access like \"%$skw%\") or (organism like \"%$skw%\") or (center like \"%$skw%\") or (mark like \"%$skw%\") or (type like \"%$skw%\") or (data_type like \"%$skw%\") or (data_author like \"%$skw%\"))";
        }

            $qstr = "select group_id from user where user_id = ?";
            $stmt = $db->prepare($qstr);
            $stmt->bindParam(1, $username);
            $stmt->execute() or goBack("Database error!");
            $r = $stmt->fetch();
            $group_id = $r['group_id'];

        $query = "select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where access='public' $newskw union select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where access='private' and user_id = ? $newskw union select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where access='group' $newskw and user_id in (select user_id from user where group_id = ?)";
        $sql_cnt = "select sum(c) from (select count(*) as c from track where access='public' $newskw union select count(*) from track where access='private' and user_id = '$username' $newskw union select count(*) from track where access='group' $newskw and user_id in (select user_id from user where group_id = $group_id)) as total";
        $stmt = $db->query($sql_cnt);
        if(!$stmt) goBack("Error happened. Please try later.");
        $num_rows = $stmt->fetchColumn(0);

        $query .= $postfix;
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $group_id);
        $stmt->execute() or goBack("Error happened. Please try later.");
    }

    if (!empty($_POST['searchoption'])) {
        $type = stripslashes($_POST['type']);
        $mark = stripslashes($_POST['mark']);
        $organism = stripslashes($_POST['organism']);
        $data_type = stripslashes($_POST['data_type']);
        $center = stripslashes($_POST['center']);

        $cond = '';
        if($type != '') $cond .= " and type in ($type)";
        if($mark != '') $cond .= " and mark in ($mark)";
        if($organism != '') $cond .= " and organism in ($organism)";
        if($data_type != '') $cond .= " and data_type in ($data_type)";
        if($center != '') $cond .= " and center in ($center)";

        $qstr = "select group_id from user where user_id = ?";
        $stmt = $db->prepare($qstr);
        $stmt->bindParam(1, $username);
        $stmt->execute() or goBack("Database error!");
        $r = $stmt->fetch();
        $group_id = $r['group_id'];

        $sql_cnt = "select sum(c) from (select count(*) as c from track where access='public' $cond union select count(*) from track where access='private' and user_id = '$username' $cond union select count(*) from track where access='group' and user_id in (select user_id from user where group_id = $group_id) $cond) as total";
        
        //print_r($sql_cnt);
        $stmt = $db->query($sql_cnt);
        if(!$stmt) goBack("Error happened. Please try later.");
        $num_rows = $stmt->fetchColumn(0);

        $query = "select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where access='public' $cond union select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where access='private' and user_id = ? $cond union select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where access='group' and user_id in (select user_id from user where group_id = ?) $cond";
        $query .= $postfix;
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $group_id);
        $stmt->execute() or goBack("Error happened. Please try later.");
    }
        $list = array();
        while ($r = $stmt->fetch()) {
                $list[] = $r;
        }
        $result_list = json_encode(array(
                'success' => true,
                'count' => $num_rows,
                'rows' => $list
        ));

    return $result_list;
}

// Show all configs in database.
function show_conf() {
    global $db;
    $username = $_SESSION['username'];
    $query = "select * from configuration where (user_id = '$username' or user_id = 'demo')";
    $stmt = $db->query($query);
    if(!$stmt) goBack("Error happened. Please try later.");
    $num_rows = $stmt->fetchColumn(0);

    $query = "select conf_id, conf_name, build_date, if (length(description) > 25, concat(substr(description, 1, 25), '...'), description) as conf_desc, lastview_date, view_count from configuration where (user_id = :username or user_id = 'demo')";

    $direction  = 'asc';
    $sort_field = 'upload_date';
    if (!empty($_POST['sort'])) $sort_field = $_POST['sort'];
    if (!empty($_POST['dir'])) $direction = $_POST['dir'];

    $postfix = " order by $sort_field  $direction";

    $start = 0;
    $limit = 40;
    if(!empty($_POST['start'])) $start = $_POST['start'];
    if(!empty($_POST['limit'])) $limit = $_POST['limit'];

    $query .= $postfix;
    $sql_cnt = "select count(*) from configuration where (user_id = '$username' or user_id = 'demo')";
    $stmt = $db->query($sql_cnt);
    if(!$stmt) goBack("Error happened. Please try later.");
    $num_rows = $stmt->fetchColumn(0);

    $query .= " limit $start, $limit";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute() or goBack("Error happened. Please try later.");

    if ($num_rows == 0) {
    }
    $list = array();
    while ($r = $stmt->fetch()) {
        $list[] = $r;
    }
    $result_list = json_encode(array(
       'success' => true,
       'count' => $num_rows,
       'rows' => $list
    ));
    return $result_list;
}

// Modify an AJ configuration according to user actions.
function modify_aj_conf() {
    $action = $_POST['action'];
    if ($action) {
    switch ($action) {
        case 'load':
            load_aj_conf();
            exit;
            break;
        case 'remove':
            rm_aj_conf();
            break;
        case 'search':
            search_aj_conf();
            break;
        case 'genhtml':
            gen_aj_inst();
            break;
    }
  }
}


function search_aj_conf(){
    global $db;
    $keyword = $_POST['keyword'];
    $kw = "%$keyword%";

    $username = $_SESSION['username'];
    $query = "select * from configuration where (user_id = '$username' or user_id = 'demo')";
    $stmt = $db->query($query);
    if(!$stmt) goBack("Error happened. Please try later.");
    $num_rows = $stmt->fetchColumn(0);

    $query = "select conf_id, conf_name, build_date, if (length(description) > 25, concat(substr(description, 1, 25), '...'), description) as conf_desc, lastview_date, view_count from configuration where (user_id = :username or user_id = 'demo')";

    $direction  = 'asc';
    $sort_field = 'upload_date';
    if (!empty($_POST['sort'])) $sort_field = $_POST['sort'];
    if (!empty($_POST['dir'])) $direction = $_POST['dir'];

    $postfix = " order by $sort_field  $direction";

    $start = 0;
    $limit = 40;
    if(!empty($_POST['start'])) $start = $_POST['start'];
    if(!empty($_POST['limit'])) $limit = $_POST['limit'];

    $query .= " and ((conf_name like :keyword) or (build_date like :keyword) or (description like :keyword))";
    $query .= $postfix;
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':keyword', $kw);
    $stmt->execute() or goBack("Error happened. Please try later.");
    $num_rows = 0;
    while ($r = $stmt->fetch()) {
       $num_rows++;
    }

    $query .= " limit $start, $limit";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':keyword', $kw);
    $stmt->execute() or goBack("Error happened. Please try later.");

    if ($num_rows == 0) {
        goBack("No items founded.");
    }
    $list = array();
    while ($r = $stmt->fetch()) {
       $list[] = $r;
    }
    $result_list = json_encode(array(
       'success' => true,
       'count' => $num_rows,
       'rows' => $list
    ));
    echo $result_list;
    exit;
}
function rm_aj_conf(){
    global $db;

    $username = $_SESSION['username'];
    $confids = $_POST['confids'];

    $a_trk = explode(',', $confids);
    $count  = 0;
    foreach($a_trk as $conf_id)
    {
        $conf_id = trim($conf_id);
        if($conf_id == '') continue;

        $query = "select user_id from configuration where conf_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $conf_id);
        $stmt->execute() or goBack("Database error!");

        $r = $stmt->fetch();
        if(!$r['user_id']) continue;
        if($username != $r['user_id']) continue;

        $query = "delete from configuration where conf_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $conf_id);
        $stmt->execute() or goBack("Database error!");
        $count++;
    }
    Msg(true, "$count configurations removed!");
}

// Load a configuration from database using config id.
function load_aj_conf(){
    global $db;
    if ($_POST['id']) {
        $conf_id = $_POST['id'];
        $query = "select conf_id, conf_name, description, trk_ids, trk_hts, user_id, start_chr, start_pos, bases, pixels from configuration where conf_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $conf_id);
        $stmt->execute() or goBack("Database error!");
        $row = $stmt->fetch();
        $user_id = $row['user_id'];
        $username = $_SESSION['username'];
        if($user_id != $username){
            goBack("No permission to access this configuration!");
        }

        $list = array();
        $a_trk = explode(',', $row['trk_ids']);    // track ids.
        foreach($a_trk as $trk_id){
            $trk_id = trim($trk_id);
            if($trk_id == '') continue;

            $query = "select track_id, track_name, track_type, track_user, track_url, data_type, user_id, access, organism, url_self, data_author, mark, type, center, date_format(upload_date,'%Y-%m-%d %h:%m:%s') as uploaddate from track where track_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $trk_id);
            $stmt->execute() or goBack("Database error!");

            while ($r = $stmt->fetch()) {
                $list[] = $r;
            }
        };

            $result_list = json_encode(array(
                    'success' => true,
                    'conf_name' => $row['conf_name'],
                    'conf_id' => $row['conf_id'],
                    'description' => $row['description'],
                    'user_id' => $row['user_id'],
                    'start_chr' => $row['start_chr'],
                    'start_pos' => $row['start_pos'],
                    'pixels' => $row['pixels'],
                    'bases' => $row['bases'],
                    'rows' => $list
            ));

        echo $result_list;
        return;
    }
}

// Read tracks' heights from inputs and update session variables.
function read_trk_input_ht(){
    $aj_conf = $_SESSION['aj_conf'];
    foreach($aj_conf as $trk_id => $trk_ht){
        $input_ht = $_POST["trkht$trk_id"];
        if($input_ht){
            $aj_conf[$trk_id] = $input_ht;
        }
    }
    $_SESSION['aj_conf'] = $aj_conf;
}

// Generate a new AJ instance: a html and a js file.
function gen_aj_inst() {
    global $db;
    $conf_id = $_POST['id'];

    global $gen_directory,$gen_directory1,$url_root;    // AJ instance location.
    global $conf_head, $conf_tail1, $conf_tail2, $conf_tail3, $conf_tail4, $html_left, $html_right;    // AJ instance files contents.
    // Always assume the configuration has been saved to the database before the instance is created.
    $query = "select user_id,start_chr,start_pos,bases,pixels,trk_ids,trk_hts from configuration where conf_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $conf_id);
    $stmt->execute() or goBack("Error happened. Please try later.");
    $row = $stmt->fetch();
    if (!$row['user_id']) {
        goBack("Configuration ID not existing\n");
    }
    $conf_name = $row['user_id'];    // Name will be used for both configuration and html.
    $a_trk = explode(',', $row['trk_ids']);    // trk1,trk2,...,trkn.
    $a_tht = explode(',', $row['trk_hts']);    // ht1,ht2,...,htn.
    if(empty($a_tht[0]) || count($a_trk)!=count($a_tht)){
        $a_tht = set_default_ht($a_trk);
    }
    $a_id_ht = array_combine($a_trk, $a_tht);    // array of id => height.

    // Iterate through selected tracks in session. Format and output in a configuration file.
    $confpool = $conf_head;    // pooled strings of configuration file.
    $trackpool = "";    // string containing all tracks' format.
    $t = 0;    // track index number.
    // $aj_conf = $_SESSION['aj_conf'];
    $aj_spec = read_aj_spec();
    $dis_num = 0;    // number of disabled tracks.
    $organism = 0;
    foreach ($a_id_ht as $trk_id => $trk_ht) {    // read all selected tracks.
        $qq = "select track_name, track_type, track_url from track where track_id = ?";
        $ss = $db->prepare($qq);
        $ss->bindParam(1, $trk_id);
        $ss->execute() or goBack("Error happened. Please try later.");
        $rr = $ss->fetch();
        $t++;    // number of tracks to be displayed.
        $type = $rr['track_type'];
        $trk_name = $rr['track_name'];
        if((ereg("hg18",$trk_name) || ereg("hg19",$trk_name)) && $type == 'ModelsTrack'){
            $organism = 0;
        }
        if((ereg("mm8",$trk_name) || ereg("mm9",$trk_name)) && $type == 'ModelsTrack'){
            $organism = 1;
        }
        if(ereg("zv9",$trk_name) && $type == 'ModelsTrack'){
            $organism = 2;
        }
        $trackstruct = array(
        'name'    => $rr['track_name'],
        'type'    => $type,
        'path'    => $aj_spec[$type]['aj_path'],
        'url'    => $rr['track_url'],
        // Use AJ default height if empty.
        'height'  => empty($trk_ht)? $aj_spec[$type]['aj_height']:$trk_ht,
        'showCtrl'=> 'true'
        );
        $comma = $t > 1 ? ',' : '';    // add comma for 2nd track and up.
        $trackpool .= $comma.formatrack($trackstruct, $t);
    }
    $confpool .= $trackpool;    // all tracks go here.
    $confpool .= "\n    ],\n";    // enclosing mark for tracks.

    $confpool .= activetracks($t);    // Concatenate active tracks selection.

    // Add a timestamp to configuration name for both js and html files.
    // This mechanism avoids the file names confliction when two users give the
    // same name for their configurations.
    $conf_name .= $conf_id;
    $conf_name = base64_encode($conf_name);
    $conf_name = ereg_replace("=","X",$conf_name);
    // Add tail and write js file.
    if ($organism == 0) $confpool .= $conf_tail1;
    if ($organism == 1) $confpool .= $conf_tail3;
    if ($organism == 2) $confpool .= $conf_tail4;

    $start_chr = $row['start_chr'];
    $start_pos = $row['start_pos'];
    $bases = $row['bases'];
    $pixels = $row['pixels'];
    $confpool .= "        assembly : '$start_chr',\n";
    $confpool .= "        position : '$start_pos',\n";
    $confpool .= "        bases    : $bases,\n";
    $confpool .= "        pixels   : $pixels";
    $confpool .= $conf_tail2;

    $file_name_base = $gen_directory . $conf_name;
    file_put_contents($file_name_base.'.js', $confpool);

    // Generate html file.
    $htmlpool = $html_left . $conf_name . '.js' . $html_right;
    file_put_contents($file_name_base.'.html', $htmlpool);

    $qq = "update configuration set lastview_date = ?, view_count = (view_count + 1) where conf_id = ?";
    $stmt = $db->prepare($qq);
    $datestr = get_date();
    $stmt->bindParam(1, $datestr);
    $stmt->bindParam(2, $conf_id);
    $stmt->execute() or goBack("Database error!");

    // return the generated html.
    $target_url = $url_root . $gen_directory1 . $conf_name . '.html';
    Msg(true, $target_url);
}

function rm_trk_files($url) {
    $host  = $_SERVER['HTTP_HOST'];
    $document_root  = $_SERVER['DOCUMENT_ROOT'];
    $patterns = array("/\/$/");
    $replacements = array('');
    $document_root = preg_replace($patterns,$replacements,$document_root);
    $patterns = array("/http:\/\/$host/");
    $replacements = array("$document_root");
    $filename = preg_replace($patterns,$replacements,$url);

        if(!file_exists($filename)) return;

        $fp = fopen($filename, "r");
        if(!$fp) return;
        $cnt = 0;
    $db_table = null;
    $db_dir = null;
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
    system("rm -rf $fname");
    unlink($filename);
        return;
}
// Remove the track id from the configurations that contain it.
function rm_trk_conf($id) {
    if(!$id) return;
    global $db;
    $query = "delete from track where track_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute() or goBack("Database error!");
    $query = "select conf_id, trk_ids, trk_hts from configuration where (trk_ids regexp '^$id') or (trk_ids regexp ',$id,') or (trk_ids regexp '$id$') or (trk_ids regexp '^$id$')";
    $stmt = $db->prepare($query);
    $stmt->execute() or goBack("Database error!");
    while ($row = $stmt->fetch()) {
        $conf_id = $row['conf_id'];
        $trk_ids = $row['trk_ids'];
        $trk_hts = $row['trk_hts'];
        $a_trk = explode(',', $trk_ids);
        $a_htk = explode(',', $trk_hts);
        foreach($a_trk as $loc => $trk_id){
            if($trk_id == $id){
                unset($a_trk[$loc]);
                unset($a_htk[$loc]);
            }
        }
        if (count($a_trk) == 0) {
            $qq = "delete from configuration where conf_id = ?";
            $ss = $db->prepare($qq);
            $ss->bindParam(1, $conf_id);
            $ss->execute() or goBack("Database error!");
        } else {
            $trk_ids = implode(',', $a_trk);
            $trk_hts = implode(',', $a_htk);
            $qq = "update configuration set trk_ids = ?, trk_hts = ? where conf_id = ?";
            $ss = $db->prepare($qq);
            $ss->bindParam(1, $trk_ids);
            $ss->bindParam(2, $trk_hts);
            $ss->bindParam(3, $conf_id);
            $ss->execute() or goBack("Database error!");
        }
    }
}

function show_single_track() {
    global $db;
    $track_id = $_POST['id'];
    if(!$track_id) Msg(false, 'No id');

    $action = $_POST['action'];
    switch($action){
        case 'save':
            $query = "select access, user_id, track_user from track where track_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $track_id);
            $stmt->execute() or Msg(false, "Database error");
            $row = $stmt->fetch();
            $tuser = $row['track_user'];

            $track_type = $_POST['track_type'];
            $track_name = trim($_POST['track_name']);
            $track_url = trim($_POST['track_url']);
            $organism = $_POST['organism'];
            $center = $_POST['center'];
            $access = $_POST['access'];
            $mark = $_POST['mark'];
            $data_type = $_POST['data_type'];
            $cell_type = $_POST['type'];
            $url_self = $_POST['url_self'];

            if ($access != $row['access']) {
                $user_id = $row['user_id'];
                change_access($track_id,$user_id,$access);
            }
            if (strlen($track_name)>80) {
                goBack("Track name ($track_name) is too long.");
            } elseif (strlen($track_name)==0) {
                goBack("Track name can not be empty.");
            }

            if (strlen($track_url)>120) {
                goBack("Track URL ($track_url) is too long.");
            } elseif (strlen($track_url)==0) {
                goBack("Track URL can not be empty.");
            }
            if (!preg_match('/^http/', $track_url)) {
                goBack("The URL ($track_url) is not correct format.");
            }

            $query = "update track set track_name = ?, track_type = ?, track_url = ?, access = ?, organism = ?, center = ?, mark = ?, data_type = ?, type = ?, url_self = ? where track_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $track_name);
            $stmt->bindParam(2, $track_type);
            $stmt->bindParam(3, $track_url);
            $stmt->bindParam(4, $access);
            $stmt->bindParam(5, $organism);
            $stmt->bindParam(6, $center);
            $stmt->bindParam(7, $mark);
            $stmt->bindParam(8, $data_type);
            $stmt->bindParam(9, $cell_type);
            $stmt->bindParam(10, $url_self);
            $stmt->bindParam(11, $track_id);

            $stmt->execute() or goBack("Error happened. Please try later.");
            Msg(true, "OK!");
            exit;
        default:
            break;
    }
}

function download () {
    $output[] = '<p>';
    $output[] = '<input type=button value=\'Browse Configurations\' onClick="window.location.href=\'brwconf.php\'">';
    $output[] = '&nbsp;&nbsp;';
    $output[] = '<input type=button value=\'Browse Tracks\' onClick="window.location.href=\'brwtrk.php\'">';
    $output[] = '</p>';
    $output[] = '<table id="main_table" class="sortable" width="90%" border="2">';
    $output[] = '<thead>';
    $output[] = '<tr><th>Name</th><th>Language</th><th>Update</th><th>Reference</th></tr>';
    $output[] = '</thead>';
    $output[] = '<tbody id="tInfo">';
    $output[] = '<tr>';
    $output[] = '<td nowrap><a href="dl_page.php?fn=1">ChromaSig</a></td>';
    $output[] = '<td>Perl</td>';
    $output[] = '<td>2010-05-25</td>';
    $output[] = '<td><a href="http://www.ploscompbiol.org/article/info:doi/10.1371/journal.pcbi.1000201">Gary Hon, Bing Ren and Wei Wang, ChromaSig: a probabilistic approach to finding common chromatin signatures in the human genome, PLoS Computational Biology, 2008, 4, 10, e1000201</a></td>';
    $output[] = '</tr>';
    $output[] = '<tr>';
    $output[] = '<td nowrap><a href="dl_page.php?fn=2">Chromia<br>(promoter/enhancer)</a></td>';
    $output[] = '<td>Python</td>';
    $output[] = '<td>2009-12-01</td>';
    $output[] = '<td><a href="http://www.biomedcentral.com/1471-2105/9/547">Kyoung-Jae Won, Iouri Chepelev, Bing Ren and Wei Wang,    Prediction of Regulatory Elements in Mammalian Genomes Using Chromatin Signatures, BMC Bioinformatics, 2008, 9, 547</a></td>';
    $output[] = '</tr>';
    $output[] = '<tr>';
    $output[] = '<td nowrap><a href="dl_page.php?fn=5">Chromia<br>(TFBS identification)</a></td>';
    $output[] = '<td>Python</td>';
    $output[] = '<td>2010-02-01</td>';
    $output[] = '<td><a href="http://genomebiology.com/2010/11/1/R7">Kyoung-Jae Won, Bing Ren and Wei Wang,    Genome-wide prediction of transcription factor binding sites using an integrated model. Genome Biol. 2010 Jan 22;11(1):R7.</a></td>';
    $output[] = '</tr>';
    $output[] = '<tr>';
    $output[] = '<td nowrap><a href="dl_page.php?fn=3">GBNet</a></td>';
    $output[] = '<td>C++</td>';
    $output[] = '<td>2009-12-01</td>';
    $output[] = '<td><a href="http://www.biomedcentral.com/1471-2105/9/395">Li Shen, Jie Liu, Wei Wang, GBNet: Deciphering regulatory rules in the co-regulated genes using a Gibbs sampler enhanced Bayesian network approach, BMC Bioinformatics 2008, 9:395</a></td>';
    $output[] = '</tr>';
    $output[] = '<tr>';
    $output[] = '<td nowrap><a href="dl_page.php?fn=4">TRANSMODIS</a></td>';
    $output[] = '<td>Perl</td>';
    $output[] = '<td>2009-12-01</td>';
    $output[] = '<td><a href="http://www.plosone.org/article/info:doi%2F10.1371%2Fjournal.pone.0001821">Ron X. Yu, Jie Liu, Nick True, Wei Wang, Identification of direct target genes using joint sequence and expression likelihood with application to DAF-16. PLoS ONE. 2008;3:e1821</a></td>';
    $output[] = '</tr>';
    $output[] = '</tbody>';
    $output[] = "</table>";
    return join('', $output);
}
function change_access($id,$user_id,$access) {
    switch ($access) {
        case 'private':
            private_trk($id,$user_id);
            break;
        case 'public':
            public_trk($id,$user_id);
            break;
        case 'group':
            group_trk($id,$user_id);
            break;
    }
}
function private_trk($id, $user_id) {
    if(!$id) return;
    global $db;
    $query = "select conf_id, trk_ids, trk_hts from configuration where (user_id != '$user_id') and ((trk_ids regexp '^$id,') or (trk_ids regexp ',$id,') or (trk_ids regexp ',$id$') or (trk_ids regexp '^$id$'))";
    $stmt = $db->prepare($query);
    $stmt->execute() or goBack("Error happened. Please try later.");
    while($row = $stmt->fetch()){
        $conf_id = $row['conf_id'];
        $trk_ids = $row['trk_ids'];
        $trk_hts = $row['trk_hts'];
        $a_trk = explode(',', $trk_ids);
        $a_htk = explode(',', $trk_hts);
        foreach($a_trk as $loc => $trk_id){
            if($trk_id == $id){
                unset($a_trk[$loc]);
                unset($a_htk[$loc]);
            }
        }
        $trk_ids = implode(',', $a_trk);
        $trk_hts = implode(',', $a_htk);
        $qq = "update configuration set trk_ids = ?, trk_hts = ? where conf_id = ?";
        $ss = $db->prepare($qq);
        $ss->bindParam(1, $trk_ids);
        $ss->bindParam(2, $trk_hts);
        $ss->bindParam(3, $conf_id);
        $ss->execute() or goBack("Error happened. Please try later.");
    }
}
function public_trk($id,$user_id) {
    return;
}

function group_trk($id,$user_id) {
    if(!$id) return;

    global $db;
    $query = "select user_id from user where account_group in (select account_group from user where user_id = ?)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $user_id);
    $stmt->execute() or goBack("Error happened. Please try later.");
    
    unset($users);
    while ($row = $stmt->fetch()) {
        $this_user_id = $row['user_id'];
        $this_user_id = "'$this_user_id'";
        $users[$this_user_id] = $this_user_id;
    }
    $allusers = implode(',',$users);

    $query = "select conf_id, trk_ids, trk_hts from configuration where (user_id not in ($allusers)) and (user_id != '$user_id') and ((trk_ids regexp '^$id,') or (trk_ids regexp ',$id,') or (trk_ids regexp ',$id$') or (trk_ids regexp '^$id$'))";
    $stmt = $db->prepare($query);
    $stmt->execute() or goBack("Error happened. Please try later.");
    while($row = $stmt->fetch()){
        $conf_id = $row['conf_id'];
        $trk_ids = $row['trk_ids'];
        $trk_hts = $row['trk_hts'];
        $a_trk = explode(',', $trk_ids);
        $a_htk = explode(',', $trk_hts);
        foreach($a_trk as $loc => $trk_id){
            if($trk_id == $id){
                unset($a_trk[$loc]);
                unset($a_htk[$loc]);
            }
        }
        $trk_ids = implode(',', $a_trk);
        $trk_hts = implode(',', $a_htk);
        $qq = "update configuration set trk_ids = ?, trk_hts = ? where conf_id = ?";
        $ss = $db->prepare($qq);
        $ss->bindParam(1, $trk_ids);
        $ss->bindParam(2, $trk_hts);
        $ss->bindParam(3, $conf_id);
        $ss->execute() or goBack("Error happened. Please try later.");
    }
}
function submit_track() {
    // uploaddir: /tmp/uploads/
    // phpdir: /var/www/html/fetchers/upload_php/
    // rawdatadir: /data/upload-raw-data/
    global $db;
    $host  = $_SERVER['HTTP_HOST'];
    $document_root  = $_SERVER['DOCUMENT_ROOT'];
    $patterns = array("/\/$/");
    $replacements = array('');
    $document_root = preg_replace($patterns,$replacements,$document_root);
    
    $user_id = $_SESSION['username'];
    $uploadyn = $_POST['datasource'];
    $access = $_POST['access'];
    $type = $_POST['tracktype'];
    $organism = $_POST['organism'];
    $center = $_POST['institute'];
    if ($uploadyn == 'uploadn') {
        $cname = trim($_POST['x-trackname']);
        if (preg_match('/^e\.g\.\ /',$cname)) {
            goBack("Please input track name!");
        }
        $aname = explode("\n", $cname);
        foreach ($aname as $loc => $name) {
            $name = trim($name);
            $aname[$loc] = $name;
            if (strlen($name)==0) {
                goBack("Illegal track name list!");
            }
        }
        $curl = trim($_POST['x-trackurl']);
        if (preg_match('/^e\.g\.\ /',$curl)) {
            goBack("Please input track URL.");
        }
        $aurl = explode("\n", $curl);
        if (count($aname) != count($aurl)) {
            goBack("Track name and URL not matched!");
        }
        foreach ($aurl as $loc => $url) {
            $url = trim($url);
            $aurl[$loc] = $url;
            if (strlen($url)==0) {
                goBack("Illegal track URL list!");
            }
            if ((!preg_match('/^http/', $url))) {
                goBack("Illegal URL string!");
            }
        }
    } elseif ($uploadyn == 'uploady') {//upload files
        $uploaddir = session_id();
        $uploaddir = '/tmp/uploads/' . $uploaddir . '/';
        //generate php fetchers
        if (!($handle = opendir($uploaddir))) {
            goBack("Failed to access uploaded data.");
        }
        while (false !== ($file = readdir($handle))) {
            if ($file == "." || $file == "..") {
                continue;
            }
            if (preg_match("/\.format_$/", $file)){ 
                continue;
            }

            $patterns = array("/\.[a-zA-Z]+$/");
            $replacements = array('');
            $newfile = preg_replace($patterns,$replacements,$file);
            rename("$uploaddir$file","$uploaddir$newfile");
            rename("$uploaddir$file.format_","$uploaddir$newfile.format_");
            $aname[] = $newfile;
            $aurl[] = "http://$host/fetchers/upload_php/$newfile.php";
        }
        closedir($handle);
    }
    foreach ($aurl as $loc => $url) {
        $name = $aname[$loc];
        $tstamp = time();
        $newname = $name;
        if ($uploadyn == 'uploady'){ 
            $newname = $name.'_' . $tstamp;
            $url = "http://$host/fetchers/upload_php/$newname.php";
        }

        $query = "insert into track (track_name, upload_date, track_url, track_type, user_id, access, organism, center,category) values (?, now(), ?, ?, ?, ?, ?, ?, 'uploaded')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $url);
        $stmt->bindParam(3, $type);
        $stmt->bindParam(4, $user_id);
        $stmt->bindParam(5, $access);
        $stmt->bindParam(6, $organism);
        $stmt->bindParam(7, $center);
        $stmt->execute() or goBack("Database error!");
        $id = $db->lastInsertId();
        if ($uploadyn == 'uploady') {
            rename("$uploaddir$name","/data/upload-raw-data/$newname") or goBack("Failed to access uploaded files.");
            rename("$uploaddir$name.format_","/data/upload-raw-data/$newname.format_") or goBack("Failed to access uploaded files.");
            $pfile = fopen("$document_root/fetchers/upload_php/$newname.php", 'w') or goBack("Failed to generate data URL!");
            fwrite($pfile, "<?php\n");
            fwrite($pfile, "require_once '../includes/common.php';\n\n");
            fwrite($pfile, "\$table = '$newname';\n");
            fwrite($pfile, "\$title = '$name';\n");
            fwrite($pfile, "\$info = '$name';\n");
            fwrite($pfile, "\$db_dir  = '/data/annoj-upload/';\n");
            fwrite($pfile, "\$file_track_type = '$type';\n");
            if($type == 'ReadsTrack') fwrite($pfile, "require_once '../includes/readstrack.php';\n");
            if($type == 'HiCTrack') fwrite($pfile, "require_once '../includes/hictrack.php';\n");
            if($type == 'MethTrack') fwrite($pfile, "require_once '../includes/methtrack.php';\n");
            if($type == 'IntensityTrack') fwrite($pfile, "require_once '../includes/intensitytrack.php';\n");
            fwrite($pfile, "?>");

            fclose($pfile);
            continue;
        }
        if ($uploadyn == 'uploadn') {
            $content = file_get_contents("$document_root/.htaccess");
            if(!$content) continue;
            $newurl = strtolower($url);
            $pa = strpos($newurl, "http://");
            if($pa < 0) continue;
            $newurl = substr($newurl, $pa+7);
            $pb = strpos($newurl, "/");
            if($pb < 0) continue;
            $domain = substr($newurl, 0, $pb);
            if(ereg("$domain/\(\.\*\)", $content)) continue;
            $rule = "\nRewriteRule ^proxy/http:/$domain/(.*)$ http://$domain/$1 [P,L]";
            $content = $content.$rule;
            file_put_contents("$document_root/.htaccess", $content);
        }
    }
    Msg(true, 'tracks submitted!');
}
function check_track() {
    global $db;
    $inquiry = $_POST['inquiry'];
    $format = $_POST['x-format'];
    $selfdefine = $_POST['selfdefine'];

    $uploaddir = session_id();
    $uploaddir = '/tmp/uploads/' . $uploaddir . '/';

    //uncompress uploaded files
    $filelist = scandir($uploaddir);
    $currdir = getcwd();
    chdir($uploaddir);
    foreach($filelist as $file)
    {
        if($file == "." || $file == "..") continue;
        if(is_dir($file)) continue;
        if (preg_match("/\.tar$/", $file)){ 
           exec("tar xvf $file", $result);
           unlink($file);
           continue;
        }
        if (preg_match("/\.gz$/", $file)){ 
           exec("gzip -d $file", $result);
           unlink($file);
           continue;
        }
        if (preg_match("/\.zip$/", $file)){ 
           exec("unzip $file", $result);
           unlink($file);
           continue;
        }
        if (preg_match("/\.tar\.gz$/", $file)){ 
           exec("tar xzvf $file", $result);
           unlink($file);
           continue;
        }
    }
    chdir($currdir);

    //check format
    if (!($handle = opendir($uploaddir))) {
        goBack("Failed to access uploaded data.");
    }
    
    $aname = array();
    while (false !== ($file = readdir($handle))) {
        if ($file == "." || $file == "..") {
            continue;
        }
        if (preg_match("/\.format_$/", $file)) continue;

        if(!file_exists("$uploaddir$file.format_"))
        {
            exec("./check_format.pl $uploaddir$file",$output); 
            $fformat = $output[0];
            if($selfdefine) $fformat = $selfdefine;
            if($format == 'BED') $fformat = 'BED';

            if($fformat == 'unknow')
            {
                unlink("$uploaddir$file");
                $aname[] = "Verify:$file...................invalid format(skipped)!";
                continue;
            }
            $aname[] = "Verify:$file...................$fformat format";

            $pfile = fopen("$uploaddir$file.format_", 'w+') or goBack("Failed to check file!");
            if($selfdefine) fwrite($pfile, "$selfdefine");
            else fwrite($pfile, "$format");
            fclose($pfile);
            continue;
        }

        if(file_exists("$uploaddir$file.format_"))
        {
            $format_t = filemtime("$uploaddir$file.format_");
            $file_t = filemtime("$uploaddir$file");
            if($file_t > $format_t){
                exec("./check_format.pl $uploaddir$file",$output); 
                $fformat = $output[0];
                if($fformat == 'unknow')
                {
                    unlink("$uploaddir$file");
                    unlink("$uploaddir$file.format_");
                    $aname[] = "Verify:$file...................invalid format(skipped)!";
                    continue;
                }
                if($selfdefine) $fformat = $selfdefine;
                if($format == 'BED') $fformat = 'BED';

                $pfile = fopen("$uploaddir$file.format_", 'w+') or goBack("Failed to check file!");
                if($selfdefine) fwrite($pfile, "$selfdefine");
                else fwrite($pfile, "$format");
                fclose($pfile);
            }
            else $fformat = file_get_contents("$uploaddir$file.format_");
            $aname[] = "verify:$file...................$fformat format";
        }
    }
    closedir($handle);
    $all = implode('<br>', $aname);
    Msg(true, $all);
}
?>
