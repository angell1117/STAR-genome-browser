<?php
ob_start();
require_once 'config.php';
require_once 'subs.php';
require_once "textdefs.php";	// include all text constants.
require_once "constants.php";	// include other constants.

session_start();	// use session variables aj_conf, search_str.


function goBack ($_str_info) {
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
//	$value = str_replace("_","\_",$value);
//	$value = str_replace("%","\%",$value);
	return $value;
}


// Show the summary of the AJ configuration.
function write_aj_conf() {
	$aj_conf = $_SESSION['aj_conf'];

	//$output[] = '<div id="funbox" class="aj_conf">';
	$output[] = '<h1>Your AnnoJ configuration</h1>';

	if (!$aj_conf) {
		$output[] =  '<p>You have no tracks in your AJ configuration</p>';
	}
	else {
		// Parse the aj_conf session variable
		$n_trks = count($aj_conf);
		$s = ($n_trks > 1) ? 's':'';
		$output[] =  '<p>You have <a href="aj_conf.php">'.$n_trks.' track'.$s.' in your AJ configuration</a></p>';
	}
	$output[] = '<p><a href="aj_conf.php?action=clear">Clear</a> your configuration</p>';
	//$output[] = '</div>';
	return join('',$output);
}

// Deterimine whether user has access to the track
function is_trk_disabled($user_id, $group_id, $access){
	$username = $_SESSION['username'];
	$usergrp = $_SESSION['usergrp'];
	$disabled = false;
	if($access == 'private' && $user_id != $username){
		$disabled = true;
	}
	elseif($access == 'group' && $group_id != $usergrp){
		$disabled = true;
	}
	return $disabled;
}


// Show detailed information of tracks in configuration.
function show_aj_conf() {
	global $db;
	$username = $_SESSION['username'];
	$conf_id = $_SESSION['conf_id'];
	$conf_name = $_SESSION['conf_name'] ? $_SESSION['conf_name'] : '';
	$conf_desc = $_SESSION['conf_desc'] ? $_SESSION['conf_desc'] : '';
	$start_chr = $_SESSION['start_chr'];
	$start_pos = $_SESSION['start_pos'];
	$bases = $_SESSION['bases'];
	$pixels = $_SESSION['pixels'];
	$aj_conf = $_SESSION['aj_conf'];
	$confnum = count($aj_conf);
	$output[] = '<p>';
	$output[] = '<input type=button value=\'Browse Configurations\' onClick="window.location.href=\'brwconf.php\'">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type=button value=\'Browse Tracks\' onClick="window.location.href=\'brwtrk.php\'">';
	$output[] = '</p>';
	$output[] = '<h1>';
	$output[] = "$conf_name &nbsp;&nbsp;($confnum";
	if ($confnum <= 1) {
		$output[] = ' track)';
	} else {
		$output[] = ' tracks)';
	}
	$output[] = '</h1>';
	$output[] = '<form action="build.php" method="post">';
	$output[] = "<p>";
	$output[] = '<input type=button value=\'View\' onClick="window.top.location.href=\'build.php?viewit='.$conf_id.'\'">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="save" value = "Update" />';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="addmore" value="Add More Tracks">';
	if (isset($_GET['fin'])) {
		$output[] = '&nbsp;&nbsp;<font color=red>Update successfully.</font>';
	}
	$output[] = "</p>";
	$output[] = '<p>Config name (max: 40 chars): <input type = "text" name = "conf_name" size = "52" maxlength="40" value = "'.$conf_name.'" onmouseover=\'this.focus();this.select()\' /></p>';
	$output[] = "<p>";
	$output[] = "Start Chromosome: <input type='text' name='start_chr' size='2' maxlength='2' value='$start_chr' onmouseover='this.focus();this.select()' />&nbsp;&nbsp;";
	$output[] = "Start Position: <input type='text' name='start_pos' size='11' maxlength='11' value='$start_pos' onmouseover='this.focus();this.select()' />&nbsp;&nbsp;";
	$output[] = "Bases: <input type='text' name='bases' size='4' maxlength='4' value='$bases' onmouseover='this.focus();this.select()' />&nbsp;&nbsp;";
	$output[] = "Pixels: <input type='text' name='pixels' size='2' maxlength='2' value='$pixels' onmouseover='this.focus();this.select()' />";
	$output[] = "</p>";
	$output[] = '<p>Description (max: 150 chars):</p><p> <textarea cols="65" rows="3" name ="conf_desc">'.$conf_desc.'</textarea></p>';
	$output[] = "<p>";
	$output[] = '<input type="submit" name="remove" value="Remove Selected Tracks" onClick="return confirmSubmit(\'Do you really want to delete?\');">';
	$output[] = '</p>';
	$output[] = '<table id="main_table" class="sortable" width="90%" border="2">';
	$output[] = '<thead>';
	$output[] = '<tr><th class="sorttable_nosort" align="center"><input type="checkbox" class="bigCheckbox" name="checkall" onClick="checkedAll(\'main_table\');" /></td><th>Name</th><th>Height</th><th>Type</th><th>Organism</th><th>Access</th><th>Center</th><th>Data</th></tr>';
	$output[] = '</thead>';
	$output[] = '<tbody id="tInfo">';
	foreach ($aj_conf as $trk_id => $trk_ht) {	// read all selected tracks.
		$trk_id = trim($trk_id);
		if($trk_id == "") continue; //added by WT
		if ($duplicateflag[$trk_id] == 1) {
			continue;
		} else {
			$duplicateflag[$trk_id] = 1;
		}
		$query = "select track_name, data_type, access, organism, url_self, mark, center from track where track_id = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $trk_id);
		$stmt->execute() or goBack("Error happened. Please try later.");
		if ($stmt->rowCount() == 0) {
			continue;
		}
		$row = $stmt->fetch();
		$name = $row['track_name'];
		$maxl = strlen($name);
		$maxl = (int)($maxl/(((int)($maxl/30))+1)) + 1;
		$names = str_split($name, $maxl);
		$name = implode("<br>", $names);
		$name = '<font style="font-family:Courier New,Courier,monospace;font-weight:bold">' . $name . '</font>';
		$output[] = '<tr><td nowrap align="center"><input type="checkbox" onClick="change(this)" class="bigCheckbox" name="trkid['.$trk_id.']" value="'.$trk_id.'" ></td>';
		$output[] = '<td nowrap>';
		$url_self = $row['url_self'];
		if (empty($url_self)) {
			$output[] = $name;
		} else {
			$output[] = '<a href = "'.$url_self.'" target="_blank">'.$name.'</a>';
		}
		$output[] = "<td nowrap align=\"center\"><input type='text' size='3' maxlength='3' value='$trk_ht' name='trkht$trk_id' onmouseover='this.focus();this.select()' /></td>";
		$output[] = '<td nowrap align="center">'.$row['data_type'].'</td>';
		$output[] = '<td nowrap align="center">'.$row['organism'].'</td>';
		$output[] = '<td nowrap align="center">'.$row['access'].'</td>';
		$output[] = '<td nowrap align="center">'.$row['center'].'</td>';
		$output[] = '<td nowrap align="center">'.$row['mark'].'</td>';
		$output[] = "</tr>";
	}
	unset($duplicateflag);
	$output[] = '</tbody>';
	$output[] = "</table>";
	$output[] = '<p>';
	$output[] = '<input type="submit" name="remove" value="Remove Selected Tracks">';
	$output[] = '</p>';
	$output[] = '</form>';
	return join('',$output);
}

// Show all configs in database.
function show_track() {
	$modelid1 = '64'; //64 | Gene models (hg18)
	$modelid11 = '8601'; //8601 | Gene models (hg19)
	$modelid2 = '844'; //844 | Gene models (mm9)
	global $db;
	$username = $_SESSION['username'];
	$page = $_GET['page'];
	if (empty($page)) { $page = 1; }
	else { $_POST = $_SESSION['POST']; }
	$trkkid = $_POST['trkid'];
	if ((!empty($_POST['create'])) or (!empty($_POST['addmore'])) or (!empty($_POST['delete']))) {
		if (empty($trkkid)) {
			goBack("Please select the tracks by using the checkbox first.");
		}
		if (!empty($_POST['addmore'])) {
			$conf_id = $_SESSION['conf_id'];
			if (empty($conf_id)) {
				goBack("Can not add more tracks. Please create a new configuration instead.");
			}
			$query = "select trk_ids, trk_hts from configuration where conf_id = ?";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $conf_id);
			$stmt->execute() or goBack("Error happened. Please try later.");
			$row = $stmt->fetch();
			$newids = $row['trk_ids'];
			$newhts = $row['trk_hts'];
			foreach ($trkkid as $tid => $ttp) {
				$matchid1 = '/^'.$tid.',/';
				$matchid2 = '/,'.$tid.'$/';
				$matchid3 = '/,'.$tid.',/';
				if ((!preg_match($matchid1, $newids)) and (!preg_match($matchid2, $newids)) and (!preg_match($matchid3, $newids))) {
					$newids .= ','.$tid;
					if ($ttp == 'ModelsTrack') {
						$ttpv = 100;
					} else {
						$ttpv = 80;
					}
					$newhts .= ','.$ttpv;
				}
			}
			if (strlen($newids)>1500) {
				goBack("Too many tracks in one configuration. Try less tracks please.");
			}
			$query = "update configuration set trk_ids = ?, trk_hts = ?  where conf_id = ?";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $newids);
			$stmt->bindParam(2, $newhts);
			$stmt->bindParam(3, $conf_id);
			$stmt->execute() or goBack("Error happened. Please try later.");
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			$extra = 'aj_conf.php?action=load&id='.$conf_id;
			header("Location: http://$host$uri/$extra");
			exit;
		}
		if (!empty($_POST['create'])) {
			$conf_name = 'new_configuration';
			$datestr = get_date();
			$conf_desc = '';
			$start_chr = 1;
			$start_pos = 1;
			$bases = 200;
			$pixels = 1;
			$ids = '';
			$hts = '';
			$flag = '';
			foreach ($trkkid as $tid => $ttp) {
				if ($tid == $modelid1 || $tid == $modelid11) {
					$flag .= '1';
				}
				if ($tid == $modelid2) {
					$flag .= '2';
				}
				if (empty($ids)) {
					$ids .= $tid;
				} else {
					$ids .= ','.$tid;
				}
				if ($ttp == 'ModelsTrack') {
					$ttpv = 100;
				} else {
					$ttpv = 80;
				}
				if (empty($hts)) {
					$hts .= $ttpv;
				} else {
					$hts .= ','.$ttpv;
				}
			}
			$query = "select * from track where track_id in ($ids) and organism='Mus musculus'";
			$stmt = $db->prepare($query);
			#$stmt->bindParam(1, $ids);
			$stmt->execute() or goBack("Error happened. Please try later.");
			if (($stmt->rowCount() >= 1) and (!preg_match('/2/', $flag))) {
				$ids = $modelid2 . ',' . $ids;
				$hts = '100,' . $hts;
			}
			$query = "select * from track where track_id in ($ids) and organism='Homo sapiens'";
			$stmt = $db->prepare($query);
			#$stmt->bindParam(1, $ids);
			$stmt->execute() or goBack("Error happened. Please try later.");
			if (($stmt->rowCount() >= 1) and (!preg_match('/1/', $flag))) {
				$ids = $modelid1 . ',' . $modelid11 . ',' . $ids;
				$hts = '100,100,' . $hts;
			}
			if (strlen($ids)>1500) {
				goBack("Too many tracks in one configuration. Try less tracks please.");
			}
			$query = "insert into configuration (conf_name, build_date, description, start_chr, start_pos, bases, pixels, trk_ids, trk_hts, user_id) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $conf_name);
			$stmt->bindParam(2, $datestr);
			$stmt->bindParam(3, $conf_desc);
			$stmt->bindParam(4, $start_chr);
			$stmt->bindParam(5, $start_pos);
			$stmt->bindParam(6, $bases);
			$stmt->bindParam(7, $pixels);
			$stmt->bindParam(8, $ids);
			$stmt->bindParam(9, $hts);
			$stmt->bindParam(10, $username);
			$stmt->execute() or goBack("Error happened. Please try later.");
			$newid = $db->lastInsertId();
			$_SESSION['conf_id'] = $newid;
			$_SESSION['conf_name'] = $conf_name;
			$_SESSION['conf_desc'] = $conf_desc;
			$_SESSION['start_chr'] = $start_chr;
			$_SESSION['start_pos'] = $start_pos;
			$_SESSION['bases'] = $bases;
			$_SESSION['pixels'] = $pixels;
			$_SESSION['newflag'] = $newid;
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			$extra = 'aj_conf.php?action=load&id='.$newid;
			header("Location: http://$host$uri/$extra");
			exit;
		}
		if (!empty($_POST['delete'])) {
			foreach ($trkkid as $tid => $ttp) {
				$query = "select user_id from track where track_id = ?";
				$stmt = $db->prepare($query);
				$stmt->bindParam(1, $tid);
				$stmt->execute() or goBack("Error happened. Please try later.");
				$row = $stmt->fetch();
				if (($username != $row['user_id']) and !(preg_match('/guest/i', $row['user_id']))) {
					goBack("You can not delete the track because only the owner can delete the track.");
				}
			}
			foreach ($trkkid as $tid => $ttp) {
				rm_trk_conf($tid);
			}
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			$extra = 'brwtrk.php';
			header("Location: http://$host$uri/$extra");
			exit;
		}
	}
	$query = "select track_list from usertrack where user_id = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $username);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$row = $stmt->fetch();
	$allids = $row['track_list'];
	$result_num = count(explode(',', $allids));
	if ($result_num == 0) {
		$output[] = 'You haven\'t had any tracks available yet.';
		return join('', $output);
	}
	//$output[] = '<p><input type=button value=\'Browse Configurations\' onClick="window.location.href=\'brwconf.php\'"></p>';
	//if ((empty($_POST['showall'])) and (empty($_POST['searchnew'])) and (!empty($trkkid))) {
		//$allids = implode(',', array_keys($trkkid));
	//}
	if ((empty($_POST['showall'])) and (empty($_POST['showmy'])) and (empty($_POST['showsdec'])) and (empty($_POST['searchnew']))) {
		$allids = implode(',', array_keys($_SESSION['refineids']));
	}
	if (!empty($_POST['showmy'])) {
		$query = "select track_id from track where user_id = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $username);
		$stmt->execute() or goBack("Error happened. Please try later.");
		$num_rows = $stmt->rowCount();
		if ($num_rows == 0) {
			$showmyidszero = 0;
		} else {
			$showmyidszero = 1;
			while($row = $stmt->fetch()){
				$id = $row['track_id'];
				$showmyids[$id] = $id;
			}
			$allids = implode(",", array_keys($showmyids));
		}
	}
	if (!empty($_POST['showsdec'])) {
		$query = "select track_id from track where track_id in (select track_id from sdectrack)";
		$stmt = $db->prepare($query);
		$stmt->execute() or goBack("Error happened. Please try later.");
		$num_rows = $stmt->rowCount();
		if ($num_rows == 0) {
			$showsdecidszero = 0;
		} else {
			$showsdecidszero = 1;
			while($row = $stmt->fetch()){
				$id = $row['track_id'];
				$showsdecids[$id] = $id;
			}
			$allids = implode(",", array_keys($showsdecids));
		}
	}
	$query = "select track_id, track_name, track_type, data_type, user_id, access, organism, url_self, mark, type, center, date_format(upload_date,'%Y-%m-%d') as uploaddate from track where track_id in ($allids)";
	//$query = "select track_id, track_name, track_type, data_type, user_id, access, organism, url_self, date_format(upload_date, '%y-%m-%d') as fmtdate from track where track_id in (:allids)";
	$others = "'Others'";
	$trkorg = $_POST['organism'];
	if ((empty($_POST['showall'])) and (empty($_POST['showmy'])) and (empty($_POST['showsdec'])) and (!empty($trkorg))) {
		$alllimit = implode(",", array_keys($trkorg));
		$query .= " and (organism in ($alllimit)";
		if (!empty($trkorg[$others])) {
			$query .= " or (organism not in (select organism from organism)) or (organism is NULL)";
		}
		$query .= ')';
	}
	$trkcen = $_POST['center'];
	if ((empty($_POST['showall'])) and (empty($_POST['showmy'])) and (empty($_POST['showsdec'])) and (!empty($trkcen))) {
		$alllimit = implode(",", array_keys($trkcen));
		$query .= " and (center in ($alllimit)";
		if (!empty($trkcen[$others])) {
			$query .= " or (center not in (select center from center)) or (center is NULL)";
		}
		$query .= ')';
	}
	$trkmar = $_POST['mark'];
	if ((empty($_POST['showall'])) and (empty($_POST['showmy'])) and (empty($_POST['showsdec'])) and (!empty($trkmar))) {
		$alllimit = implode(",", array_keys($trkmar));
		$query .= " and (mark in ($alllimit)";
		if (!empty($trkmar[$others])) {
			$query .= " or (mark not in (select mark from mark)) or (mark is NULL)";
		}
		$query .= ')';
	}
	$trktyp = $_POST['type'];
	if ((empty($_POST['showall'])) and (empty($_POST['showmy'])) and (empty($_POST['showsdec'])) and (!empty($trktyp))) {
		$alllimit = implode(",", array_keys($trktyp));
		$query .= " and (type in ($alllimit)";
		if (!empty($trktyp[$others])) {
			$query .= " or (type not in (select type from type)) or (type is NULL)";
		}
		$query .= ')';
	}
	$trkdat = $_POST['data_type'];
	if ((empty($_POST['showall'])) and (empty($_POST['showmy'])) and (empty($_POST['showsdec'])) and (!empty($trkdat))) {
		$alllimit = implode(",", array_keys($trkdat));
		$query .= " and (data_type in ($alllimit)";
		if (!empty($trkdat[$others])) {
			$query .= " or (data_type not in (select data_type from data_type)) or (data_type is NULL)";
		}
		$query .= ')';
	}
	$keywordexample = "Example: (gsm[title] or homo sapiens[organism]) and not chipseq";
	$keyword = $_POST['keyword'];
	if ($keyword == $keywordexample) {
		unset($keyword);
	}
	$keyfield = $_POST['keyfield'];
	if ((empty($_POST['showall'])) and (empty($_POST['showmy'])) and (empty($_POST['showsdec'])) and (!empty($keyword))) {
		//$query .= " and ((track_name like :keyword) or (upload_date like :keyword) or (track_type like :keyword) or (user_id like :keyword) or (access like :keyword) or (organism like :keyword) or (url_self like :keyword) or (url_meta like :keyword) or (center like :keyword) or (mark like :keyword) or (type like :keyword) or (data_type like :keyword) or (data_format like :keyword) or (data_author like :keyword))";
		$newkeyword = trim($keyword);
		$newkeyword = strtolower($newkeyword);
		$patterns = array("/\(/","/\)/");
		$replacements = array('','');
		$splitkeyword = preg_replace($patterns,$replacements,$newkeyword);
		$allkeyword = preg_split("/( and | or |not )/", $splitkeyword);
		foreach ($allkeyword as $skw) {
			$skw = trim($skw);
			if (!preg_match("/\w/", $skw)) {
				continue;
			}
			if (preg_match("/\[[^\[\]]+\]$/", $skw, $matches)) {
				$newkeyfield = $matches[0];
				$patterns = array("/^\[/","/\]$/");
				$replacements = array('','');
				$newkeyfield = preg_replace($patterns,$replacements,$newkeyfield);
				$patterns = array("/\[$newkeyfield\]/");
				$replacements = array('');
				$skw = preg_replace($patterns,$replacements,$skw);
				$newkeyword = preg_replace($patterns,$replacements,$newkeyword);
				$newkeyfield = trim($newkeyfield);
				if ($newkeyfield == 'title') {
					$newkeyfield = 'track_name';
				} elseif ($newkeyfield == 'type') {
					$newkeyfield = 'data_type';
				} elseif ($newkeyfield == 'cell') {
					$newkeyfield = 'type';
				} elseif ($newkeyfield == 'tissue') {
					$newkeyfield = 'type';
				} elseif ($newkeyfield == 'institute') {
					$newkeyfield = 'center';
				} elseif ($newkeyfield == 'organism') {
				} elseif ($newkeyfield == 'mark') {
				} else {
					$newkeyfield = $keyfield[0];
				}
			} else {
				$newkeyfield = $keyfield[0];
			}
			if ($newkeyfield == 'all') {
				$newskw = "((track_name like \"%$skw%\") or (upload_date like \"%$skw%\") or (track_type like \"%$skw%\") or (user_id like \"%$skw%\") or (access like \"%$skw%\") or (organism like \"%$skw%\") or (url_self like \"%$skw%\") or (url_meta like \"%$skw%\") or (center like \"%$skw%\") or (mark like \"%$skw%\") or (type like \"%$skw%\") or (data_type like \"%$skw%\") or (data_format like \"%$skw%\") or (data_author like \"%$skw%\") or (info like \"%$skw%\"))";
			} else {
				$patterns = array("/-/");
				$replacements = array('');
				$shortskw = preg_replace($patterns,$replacements,$skw);
				$newskw = "($newkeyfield like \"%$shortskw%\")";
			}
			$patterns = array("/$skw/");
			$replacements = array($newskw);
			$newkeyword = preg_replace($patterns,$replacements,$newkeyword);
		}
		$query .= " and ($newkeyword)";
	}
	$output[] = '<form name="brwtrk" action="brwtrk.php#track_table" method="post">';
	$output[] = '<p>';
	//$output[] = '<input type="submit" name="searchnew" value="Search" tabindex=1>';
	//$output[] = '&nbsp;&nbsp;';
	//$output[] = '<input type="submit" name="refine" value="Refine Selected Tracks">';
	//$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="showsdec" value="SDEC Tracks">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="showall" value="All Tracks">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="showmy" value="My Tracks">';
	$output[] = '<table width="90%" border="0">';
	$output[] = '<tr><td nowrap class="noborder">';
	$output[] = '<font class="bold">Keyword:</font>';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<select name="keyfield[]">';
	$output[] = '<option value="all" selected>All Fields</option>';
	$output[] = '<option value="track_name">Title</option>';
	$output[] = '<option value="organism">Organism</option>';
	$output[] = '<option value="mark">Mark</option>';
	$output[] = '<option value="data_type">Type</option>';
	$output[] = '<option value="type">Cell</option>';
	$output[] = '<option value="type">Tissue</option>';
	$output[] = '<option value="center">Institute</option>';
	$output[] = '</select>';
	$output[] = '&nbsp;&nbsp;';
	if (empty($keyword)) {
		$output[] = '<input type="text" name="keyword" size="60" value="'.$keywordexample.'" onmouseover="this.focus();this.select()" onkeydown="if(event.keyCode==13){return false;}"/>';
	} else {
		$output[] = '<input type="text" name="keyword" size="60" value="'.$keyword.'" onmouseover="this.focus();this.select()" onkeydown="if(event.keyCode==13){return false;}"/>';
	}
	$output[] = '</td></tr></table>';
	$qquery = "select organism from organism";
	$sstmt = $db->prepare($qquery);
	$sstmt->execute() or goBack("Error happened. Please try later.");
	$output[] = '<table id="organism" border="0">';
	$output[] = '<tr><td nowrap class="noborder" colspan="5"><font class="bold">Organism:&nbsp;&nbsp;<input type="checkbox" onClick="checkAll(\'organism\');"';
	//if ($sstmt->rowCount() == count($trkorg)) {
	//	$output[] = ' checked';
	//}
	$output[] = '>All</font></td></tr>';
	$output[] = "<tr>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"checkbox\" name=\"organism['Homo sapiens']\" value=\"Homo sapiens\"";
	if (!empty($trkorg["'Homo sapiens'"])) {
		$output[] = ' checked';
	}
	$output[] = ">Homo sapiens</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"checkbox\" name=\"organism['Mus musculus']\" value=\"Mus musculus\"";
	if (!empty($trkorg["'Mus musculus'"])) {
		$output[] = ' checked';
	}
	$output[] = ">Mus musculus</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"checkbox\" name=\"organism['Drosophila melanogaster']\" value=\"Drosophila melanogaster\"";
	if (!empty($trkorg["'Drosophila melanogaster'"])) {
		$output[] = ' checked';
	}
	$output[] = ">Drosophila melanogaster</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"checkbox\" name=\"organism['Arabidopsis thaliana']\" value=\"Arabidopsis thaliana\"";
	if (!empty($trkorg["'Arabidopsis thaliana'"])) {
		$output[] = ' checked';
	}
	$output[] = ">Arabidopsis thaliana</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\">&nbsp;&nbsp;<input class=\"nodisp\" id=\"gg\" type=\"button\" value=\"More &raquo\" onClick=\"subttt('gg')\"></td>";
	$output[] = "</tr></table>";
	$output[] = "<table id=\"gggg\" width=\"90%\" style=\"display:";
	$displayflag = "none";
	$ooutput = array();
	$tcount = 0;
	while ($row = $sstmt->fetch()) {
		$organism = $row['organism'];
		if (($organism == "Arabidopsis thaliana") or ($organism == "Homo sapiens") or ($organism == "Mus musculus") or ($organism == "Drosophila melanogaster")) {
			continue;
		}
		if ($tcount == 0) {
			$ooutput[] = '<tr>';
		}
		$ooutput[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"checkbox\" name=\"organism['$organism']\" value=\"$organism\"";
		$qorganism = "'$organism'";
		if (!empty($trkorg[$qorganism])) {
			$ooutput[] = ' checked';
			$displayflag = "block";
		}
		$ooutput[] = ">$organism</td>";
		if ($tcount == 3) {
			$tcount = 0;
			$ooutput[] = '</tr>';
		} else {
			$tcount++;
		}
	}
	$output[] = $displayflag;
	$output[] = "\" border=\"0\">";
	for ($i=0;$i<count($ooutput);$i++) {
		$output[] = $ooutput[$i];
	}
	unset($ooutput);
	$output[] = '</table>';
	$qquery = "select mark from mark";
	$sstmt = $db->prepare($qquery);
	$sstmt->execute() or goBack("Error happened. Please try later.");
	$output[] = '<table id="mark" border="0">';
	$output[] = '<tr><td nowrap class="noborder" colspan="5"><font class="bold">Mark:&nbsp;&nbsp;<input type="checkbox" onClick="checkAll(\'mark\');"';
	//if ($sstmt->rowCount() == count($trkmar)) {
	//	$output[] = ' checked';
	//}
	$output[] = '>All</font></td></tr><tr>';
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"mark\" type=\"checkbox\" name=\"mark['Ctcf']\" value=\"Ctcf\"";
	if (!empty($trkmar["'Ctcf'"])) {
		$output[] = ' checked';
	}
	$output[] = ">Ctcf</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"mark\" type=\"checkbox\" name=\"mark['H3K4me1']\" value=\"H3K4me1\"";
	if (!empty($trkmar["'H3K4me1'"])) {
		$output[] = ' checked';
	}
	$output[] = ">H3K4me1</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"mark\" type=\"checkbox\" name=\"mark['H3K4me2']\" value=\"H3K4me2\"";
	if (!empty($trkmar["'H3K4me2'"])) {
		$output[] = ' checked';
	}
	$output[] = ">H3K4me2</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"mark\" type=\"checkbox\" name=\"mark['H3K4me3']\" value=\"H3K4me3\"";
	if (!empty($trkmar["'H3K4me3'"])) {
		$output[] = ' checked';
	}
	$output[] = ">H3K4me3</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\">&nbsp;&nbsp;<input class=\"nodisp\" id=\"ee\" type=\"button\" value=\"More &raquo\" onClick=\"subttt('ee')\"></td>";
	$output[] = "</tr></table>";
	$output[] = "<table id=\"eeee\" width=\"90%\" style=\"display:";
	$displayflag = "none";
	$ooutput = array();
	$tcount = 0;
	while ($row = $sstmt->fetch()) {
		$mark = $row['mark'];
		if (($mark == "Ctcf") or ($mark == "H3K4me1") or ($mark == "H3K4me2") or ($mark == "H3K4me3") or ($mark == "input")) {
			continue;
		}
		if ($tcount == 0) {
			$ooutput[] = '<tr>';
		}
		$ooutput[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"mark\" type=\"checkbox\" name=\"mark['$mark']\" value=\"$mark\"";
		$qmark = "'$mark'";
		if (!empty($trkmar[$qmark])) {
			$ooutput[] = ' checked';
			$displayflag = "block";
		}
		$ooutput[] = ">$mark</td>";
		if ($tcount == 3) {
			$tcount = 0;
			$ooutput[] = '</tr>';
		} else {
			$tcount++;
		}
	}
	$output[] = $displayflag;
	$output[] = "\" border=\"0\">";
	for ($i=0;$i<count($ooutput);$i++) {
		$output[] = $ooutput[$i];
	}
	unset($ooutput);
	$output[] = '</table>';
	$qquery = "select data_type from data_type";
	$sstmt = $db->prepare($qquery);
	$sstmt->execute() or goBack("Error happened. Please try later.");
	$output[] = '<table id="data_type" border="0">';
	$output[] = "<tr><td nowrap class=\"noborder\" colspan=\"5\"><font class=\"bold\">Type:&nbsp;&nbsp;<input type=\"checkbox\" onClick=\"checkAll('data_type');\"";
	//if ($sstmt->rowCount() == count($trkdat)) {
	//	$output[] = ' checked';
	//}
	$output[] = '>All</font></td></tr>';
	$output[] = "<tr>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\" type=\"checkbox\" name=\"data_type['ChIPSeq']\" value=\"ChIPSeq\"";
	if (!empty($trkdat["'ChIPSeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">ChIPSeq</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\" id=\"cc\" type=\"checkbox\" name=\"data_type['Chromatin Accessibility']\" value=\"Chromatin Accessibility\" onClick=\"subtt('cc')\"";
	if (!empty($trkdat["'Chromatin Accessibility'"])) {
		$output[] = ' checked';
	}
	$output[] = ">Chromatin Accessibility</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\" id=\"aa\" type=\"checkbox\" name=\"data_type['Methylation']\" value=\"Methylation\" onClick=\"subtt('aa')\"";
	if (!empty($trkdat["'Methylation'"])) {
		$output[] = ' checked';
	}
	$output[] = ">Methylation</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\" id=\"bb\" type=\"checkbox\" name=\"data_type['RNASeq']\" value=\"RNASeq\" onClick=\"subtt('bb')\"";
	if (!empty($trkdat["'RNASeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">RNASeq</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\">&nbsp;&nbsp;<input class=\"nodisp\" id=\"dd\" type=\"button\" value=\"More &raquo\" onClick=\"subttt('dd')\"></td>";
	$output[] = "</tr>";
	$output[] = "</table>";
	$output[] = "<table id=\"cccc\" width=\"90%\" style=\"display:";
	if ((!empty($trkdat["'DGF'"])) or (!empty($trkdat["'DNaseSeq'"])) or (!empty($trkdat["'FAIRESeq'"])) or (!empty($trkdat["'SonoSeq'"]))) {
		$output[] = 'block';
	} else {
		$output[] = 'none';
	}
	$output[] = "\" border=\"0\"><tr><td nowrap class=\"noborder\" align=\"left\">Chromatin Accessibility:&nbsp;</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['DGF']\" value=\"DGF\"";
	if (!empty($trkdat["'DGF'"])) {
		$output[] = ' checked';
	}
	$output[] = ">DGF</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['DNaseSeq']\" value=\"DNaseSeq\"";
	if (!empty($trkdat["'DNaseSeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">DNaseSeq</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['FAIRESeq']\" value=\"FAIRESeq\"";
	if (!empty($trkdat["'FAIRESeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">FAIRESeq</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['SonoSeq']\" value=\"SonoSeq\"";
	if (!empty($trkdat["'SonoSeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">SonoSeq</td>";
	$output[] = "</tr></table>";
	$output[] = "<table id=\"aaaa\" width=\"90%\" style=\"display:";
	if ((!empty($trkdat["'BisulfiteSeq'"])) or (!empty($trkdat["'MeDIPSeq'"])) or (!empty($trkdat["'MeDIPSeq'"])) or (!empty($trkdat["'MRESeq'"])) or (!empty($trkdat["'RRBS'"]))) {
		$output[] = 'block';
	} else {
		$output[] = 'none';
	}
	$output[] = "\" border=\"0\"><tr><td nowrap class=\"noborder\" align=\"left\">DNA Methylation:&nbsp;</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['BisulfiteSeq']\" value=\"BisulfiteSeq\"";
	if (!empty($trkdat["'BisulfiteSeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">BisulfiteSeq</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['MeDIPSeq']\" value=\"MeDIPSeq\"";
	if (!empty($trkdat["'MeDIPSeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">MeDIPSeq</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['MethylCSeq']\" value=\"MethylCSeq\"";
	if (!empty($trkdat["'MethylCSeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">MethylCSeq</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['MRESeq']\" value=\"MRESeq\"";
	if (!empty($trkdat["'MRESeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">MRESeq</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['RRBS']\" value=\"RRBS\"";
	if (!empty($trkdat["'RRBS'"])) {
		$output[] = ' checked';
	}
	$output[] = ">RRBS</td>";
	$output[] = "</tr></table>";
	$output[] = "<table id=\"bbbb\" width=\"90%\" style=\"display:";
	if ((!empty($trkdat["'mRNASeq'"])) or (!empty($trkdat["'smRNASeq'"]))) {
		$output[] = 'block';
	} else {
		$output[] = 'none';
	}
	$output[] = "\" border=\"0\"><tr><td nowrap class=\"noborder\" align=\"left\">RNA sequencing:&nbsp;</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['mRNASeq']\" value=\"mRNASeq\"";
	if (!empty($trkdat["'mRNASeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">mRNASeq</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['smRNASeq']\" value=\"smRNASeq\"";
	if (!empty($trkdat["'smRNASeq'"])) {
		$output[] = ' checked';
	}
	$output[] = ">smRNASeq</td>";
	$output[] = "</tr></table>";
	$output[] = "<table id=\"dddd\" width=\"90%\" style=\"display:";
	if ((!empty($trkdat["'CAGE'"])) or (!empty($trkdat["'SAGE'"])) or (!empty($trkdat["'Others'"]))) {
		$output[] = 'block';
	} else {
		$output[] = 'none';
	}
	$output[] = "\" border=\"0\"><tr><td nowrap class=\"noborder\" align=\"left\">More:&nbsp;</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['CAGE']\" value=\"CAGE\"";
	if (!empty($trkdat["'CAGE'"])) {
		$output[] = ' checked';
	}
	$output[] = ">CAGE</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['SAGE']\" value=\"SAGE\"";
	if (!empty($trkdat["'SAGE'"])) {
		$output[] = ' checked';
	}
	$output[] = ">SAGE</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\"  type=\"checkbox\" name=\"data_type['Others']\" value=\"Others\"";
	if (!empty($trkdat["'Others'"])) {
		$output[] = ' checked';
	}
	$output[] = ">Others</td>";
	$output[] = "</tr></table>";
//	$tcount = 1;
//	while ($row = $sstmt->fetch()) {
//		$data_type = $row['data_type'];
//		if ($tcount == 0) {
//			$output[] = '<tr>';
//		}
//		$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"data_type\" type=\"checkbox\" name=\"data_type['$data_type']\" value=\"$data_type\"";
//		$qdata_type = "'$data_type'";
//		if (!empty($trkdat[$qdata_type])) {
//			$output[] = ' checked';
//		}
//		$output[] = ">$data_type</td>";
//		if ($tcount == 4) {
//			$tcount = 0;
//			$output[] = '</tr>';
//		} else {
//			$tcount++;
//		}
//	}
	$qquery = "select type from type";
	$sstmt = $db->prepare($qquery);
	$sstmt->execute() or goBack("Error happened. Please try later.");
	$output[] = '<table id="type" border="0">';
	$output[] = '<tr><td nowrap class="noborder" colspan="5"><font class="bold">Cell/Tissue:&nbsp;&nbsp;<input type="checkbox" onClick="checkAll(\'type\');"';
	//if ($sstmt->rowCount() == count($trktyp)) {
	//	$output[] = ' checked';
	//}
	$output[] = '>All</font></td></tr><tr>';
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"type\" type=\"checkbox\" name=\"type['H1']\" value=\"H1\"";
	if (!empty($trktyp["'H1'"])) {
		$output[] = ' checked';
	}
	$output[] = ">H1</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"type\" type=\"checkbox\" name=\"type['IMR90']\" value=\"IMR90\"";
	if (!empty($trktyp["'IMR90'"])) {
		$output[] = ' checked';
	}
	$output[] = ">IMR90</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"type\" type=\"checkbox\" name=\"type['ES-I3']\" value=\"ES-I3\"";
	if (!empty($trktyp["'ES-I3'"])) {
		$output[] = ' checked';
	}
	$output[] = ">ES-I3</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"type\" type=\"checkbox\" name=\"type['iPS-20b']\" value=\"iPS-20b\"";
	if (!empty($trktyp["'iPS-20b'"])) {
		$output[] = ' checked';
	}
	$output[] = ">iPS-20b</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\">&nbsp;&nbsp;<input class=\"nodisp\" id=\"ff\" type=\"button\" value=\"More &raquo\" onClick=\"subttt('ff')\"></td>";
	$output[] = "</tr></table>";
	$output[] = "<table id=\"ffff\" width=\"90%\" style=\"display:";
	$displayflag = "none";
	$ooutput = array();
	$tcount = 0;
	while ($row = $sstmt->fetch()) {
		$type = $row['type'];
		if (($type == "H1") or ($type == "IMR90") or ($type == "iPS-20b") or ($type == "ES-I3")) {
			continue;
		}
		if ($tcount == 0) {
			$ooutput[] = '<tr>';
		}
		$ooutput[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"type\" type=\"checkbox\" name=\"type['$type']\" value=\"$type\"";
		$qtype = "'$type'";
		if (!empty($trktyp[$qtype])) {
			$ooutput[] = ' checked';
			$displayflag = "block";
		}
		$ooutput[] = ">$type</td>";
		if ($tcount == 3) {
			$tcount = 0;
			$ooutput[] = '</tr>';
		} else {
			$tcount++;
		}
	}
	$output[] = $displayflag;
	$output[] = "\" border=\"0\">";
	for ($i=0;$i<count($ooutput);$i++) {
		$output[] = $ooutput[$i];
	}
	unset($ooutput);
	$output[] = '</table>';
	$qquery = "select center from center";
	$sstmt = $db->prepare($qquery);
	$sstmt->execute() or goBack("Error happened. Please try later.");
	$output[] = '<table id="center" border="0">';
	$output[] = '<tr><td nowrap class="noborder" colspan="5"><font class="bold">Institute:&nbsp;&nbsp;<input type="checkbox" onClick="checkAll(\'center\');"';
	//if ($sstmt->rowCount() == count($trkcen)) {
	//	$output[] = ' checked';
	//}
	$output[] = '>All</font></td></tr>';
	$output[] = '<a name="track_table"></a>';
	$tcount = 0;
	while ($row = $sstmt->fetch()) {
		$center = $row['center'];
		if ($tcount == 0) {
			$output[] = '<tr>';
		}
		$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"center\" type=\"checkbox\" name=\"center['$center']\" value=\"$center\"";
		$qcenter = "'$center'";
		if (!empty($trkcen[$qcenter])) {
			$output[] = ' checked';
		}
		$output[] = ">$center</td>";
		if ($tcount == 4) {
			$tcount = 0;
			$output[] = '</tr>';
		} else {
			$tcount++;
		}
	}
	$output[] = '</table>';
	$output[] = '<br>';
	$output[] = '<input type="submit" name="searchnew" value="Search" tabindex=1>';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="refine" value="Refine Selected Tracks">';
	//$output[] = '&nbsp;&nbsp;';
	//$output[] = '<input type="submit" name="showsdec" value="SDEC Tracks">';
	//$output[] = '&nbsp;&nbsp;';
	//$output[] = '<input type="submit" name="showall" value="All Tracks">';
	//$output[] = '&nbsp;&nbsp;';
	//$output[] = '<input type="submit" name="showmy" value="My Tracks">';
	$output[] = '</p>';
	if (empty($_POST) and empty($_GET) and empty($_SESSION['submitids'])) {
		return join('', $output);
	}
	unset($_SESSION['submitids']);
	//if ((!empty($_POST['refine'])) and (empty($trkkid))) {
	//	$output[] = '<br>Please select the trackes first.<br>';
	//	return join('', $output);
	//}
	if ((!empty($_POST['showmy'])) and ($showmyidszero == 0)) {
		$output[] = '<br>You have no your own track.<br>';
		return join('', $output);
	}
	if ((!empty($_POST['showsdec'])) and ($showsdecidszero == 0)) {
		$output[] = '<br>There is no sdec track available.<br>';
		return join('', $output);
	}
	if (((!empty($_POST['refine'])) or (!empty($_POST['searchnew']))) and (empty($keyword)) and (empty($trkorg)) and (empty($trkmar)) and (empty($trktyp)) and (empty($trkcen)) and (empty($trkdat))) {
		$output[] = '<br>Please select the search criteria.<br>';
		return join('', $output);
	}
	$stmt = $db->prepare($query);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$num_rows = $stmt->rowCount();
	if ($num_rows == 0) {
		$output[] = '<br>No item matches.<br>';
		return join('', $output);
	}
	$output[] = '<h1>Accessible Tracks &nbsp;&nbsp;(';
	if ($num_rows == 1) {
		$output[] = $num_rows . ' track)</h1>';
	} else {
		$output[] = $num_rows . ' tracks)</h1>';
	}
	while($row = $stmt->fetch()){
		$id = $row['track_id'];
		$refineids[$id] = $id;
	}
	$_SESSION['refineids'] = $refineids;
	if (!empty($_POST['asctype'])) {
		$query .= ' order by data_type asc';
	} elseif (!empty($_POST['dsctype'])) {
		$query .= ' order by data_type desc';
	} elseif (!empty($_POST['ascorga'])) {
		$query .= ' order by organism asc';
	} elseif (!empty($_POST['dscorga'])) {
		$query .= ' order by organism desc';
	} elseif (!empty($_POST['ascacce'])) {
		$query .= ' order by access asc';
	} elseif (!empty($_POST['dscacce'])) {
		$query .= ' order by access desc';
	} elseif (!empty($_POST['asccent'])) {
		$query .= ' order by center asc';
	} elseif (!empty($_POST['dsccent'])) {
		$query .= ' order by center desc';
	} elseif (!empty($_POST['asccell'])) {
		$query .= ' order by type asc';
	} elseif (!empty($_POST['dsccell'])) {
		$query .= ' order by type desc';
	} elseif (!empty($_POST['ascmark'])) {
		$query .= ' order by mark asc';
	} elseif (!empty($_POST['dscmark'])) {
		$query .= ' order by mark desc';
	} elseif (!empty($_POST['ascuplo'])) {
		$query .= ' order by upload_date asc';
	} elseif (!empty($_POST['dscuplo'])) {
		$query .= ' order by upload_date desc';
	} elseif (!empty($_POST['ascname'])) {
		$query .= ' order by track_name asc';
	} elseif (!empty($_POST['dscname'])) {
		$query .= ' order by track_name desc';
	} else {
		$query .= ' order by upload_date desc';
	}
	$limit = 100;
	$maxpage = $num_rows%$limit;
	if ($maxpage == 0) {
		$maxpage = $num_rows/$limit;
	} else {
		$maxpage = ($num_rows+100-$maxpage)/$limit;
	}
	if ($page > $maxpage) { $page = $maxpage; }
	elseif ($page < 1) { $page = 1; }
	$start = ($page-1)*$limit;
	$query .= " limit $start, $limit";
	$stmt = $db->prepare($query);
	$stmt->bindParam(':keyword', $kw);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$_SESSION['POST'] = $_POST;
	$output[] = '<p>';
	$output[] = '<input type="submit" name="create" value="Create A New Configuration">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="addmore" value="Add More Tracks">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="delete" value="Delete Selected Tracks" onClick="return confirmSubmit(\'Do you really want to delete?\');">';
	$output[] = '</p>';
	if ($maxpage > 1) {
		$output[] = '<table><tr><td align="left" class="noborder">';
		if ($page == 1) {
			$ppage = $page+1;
			$output[] = "Previous&nbsp;&nbsp;<a href=brwtrk.php?page=$ppage#track_table>Next</a>&nbsp;&nbsp;";
		} elseif ($page == $maxpage) {
			$ppage = $page-1;
			$output[] = "<a href=brwtrk.php?page=$ppage#track_table>Previous</a>&nbsp;&nbsp;Next&nbsp;&nbsp;";
		} else {
			$ppage = $page-1;
			$output[] = "<a href=brwtrk.php?page=$ppage#track_table>Previous</a>&nbsp;&nbsp;";
			$ppage = $page+1;
			$output[] = "<a href=brwtrk.php?page=$ppage#track_table>Next</a>&nbsp;&nbsp;";
		}
		if ($maxpage < 17) {
			for ($counter = 1; $counter <= $maxpage; $counter++) {
				if ($counter == $page) {
					$output[] = "$counter&nbsp;&nbsp;";
				} else {
					$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
				}
			}
		} else {
			if ($page < 9) {
				for ( $counter = 1; $counter <= 9; $counter++) {
					if ($counter == $page) {
						$output[] = "$counter&nbsp;&nbsp;";
					} else {
						$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
					}
				}
				$output[] = '...&nbsp;&nbsp;';
				for ( $counter = $maxpage-1; $counter <= $maxpage; $counter++) {
					$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
				}
			} elseif ($page > $maxpage-8) {
				for ( $counter = 1; $counter <= 2; $counter++) {
					$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
				}
				$output[] = '...&nbsp;&nbsp;';
				for ( $counter = $maxpage-8; $counter <= $maxpage; $counter++) {
					if ($counter == $page) {
						$output[] = "$counter&nbsp;&nbsp;";
					} else {
						$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
					}
				}
			} else {
				for ( $counter = 1; $counter <= 2; $counter++) {
					$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
				}
				$output[] = '...&nbsp;&nbsp;';
				for ( $counter = $page-5; $counter <= $page+5; $counter++) {
					if ($counter == $page) {
						$output[] = "$counter&nbsp;&nbsp;";
					} else {
						$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
					}
				}
				$output[] = '...&nbsp;&nbsp;';
				for ( $counter = $maxpage-1; $counter <= $maxpage; $counter++) {
					$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
				}
			}
		}
		$output[] = '</td></tr></table>';
	}
	$output[] = '<table id="main_table">';
	$output[] = '<thead>';
	//$output[] = '<tr><th class="sorttable_nosort" align="center"><input type="checkbox" class="bigCheckbox" name="checkall" onClick="checkedAll(\'main_table\');" checked></th><th>Name</th><th>Type</th><th>organism</th><th>Access</th><th>Center</th><th>Data</th><th>Upload</th></tr>';
	$output[] = '<tr><th align="center" width=25px><input type="checkbox" class="bigCheckbox" name="checkall" onClick="checkAll(\'main_table\');" checked></th>';
	if (!empty($_POST['dscname'])) {
		$output[] = '<th width=300px><input type="submit" name="ascname" value="Title" class="btn"></th>';
	} else {
		$output[] = '<th width=300px><input type="submit" name="dscname" value="Title" class="btn"></th>';
	}
	if (!empty($_POST['ascorga'])) {
		$output[] = '<th width=100px><input type="submit" name="dscorga" value="Organism" class="btn"></th>';
	} else {
		$output[] = '<th width=100px><input type="submit" name="ascorga" value="Organism" class="btn"></th>';
	}
	if (!empty($_POST['ascdata'])) {
		$output[] = '<th width=100px><input type="submit" name="dscmark" value="Mark" class="btn" style="width:100%;"></th>';
	} else {
		$output[] = '<th width=100px><input type="submit" name="ascmark" value="Mark" class="btn" style="width:100%;"></th>';
	}
	if (!empty($_POST['asctype'])) {
		$output[] = '<th width=100px><input type="submit" name="dsctype" value="Type" class="btn"></th>';
	} else {
		$output[] = '<th width=100px><input type="submit" name="asctype" value="Type" class="btn"></th>';
	}
	if (!empty($_POST['asccell'])) {
		$output[] = '<th width=115px><input type="submit" name="dsccell" value="Cell/Tissue" class="btn"></th>';
	} else {
		$output[] = '<th width=115px><input type="submit" name="asccell" value="Cell/Tissue" class="btn"></th>';
	}
	if (!empty($_POST['asccent'])) {
		$output[] = '<th width=100px><input type="submit" name="dsccent" value="Institute" class="btn"></th>';
	} else {
		$output[] = '<th width=100px><input type="submit" name="asccent" value="Institute" class="btn"></th>';
	}
	if (!empty($_POST['ascacce'])) {
		$output[] = '<th width=75px><input type="submit" name="dscacce" value="Access" class="btn"></th>';
	} else {
		$output[] = '<th width=75px><input type="submit" name="ascacce" value="Access" class="btn"></th>';
	}
	if (!empty($_POST['ascuplo'])) {
		$output[] = '<th width=100px><input type="submit" name="dscuplo" value="Upload" class="btn"></th>';
	} else {
		$output[] = '<th width=100px><input type="submit" name="ascuplo" value="Upload" class="btn"></th>';
	}
	$output[] = '</tr>';
	$output[] = '</thead>';
	$output[] = '<tbody id="tInfo">';
	while($row = $stmt->fetch()){
		$id = $row['track_id'];
		$user_id = $row['user_id'];
		$trktype = $row['track_type'];
		$output[] = '<tr class="checked"><td nowrap align="center"><input type="checkbox" onClick="change(this)" class="bigCheckbox" name="trkid['.$id.']" value="'.$trktype.'" checked></td>';
		$name = $row['track_name'];
		$maxl = strlen($name);
		if ($username == $user_id) {
			$maxl = $maxl+4;
		}
		$maxl = (int)($maxl/(((int)($maxl/30))+1)) + 1;
		$names = str_split($name, $maxl);
		$name = implode("<br>", $names);
		$name = '<font style="font-family:Courier New,Courier,monospace;font-weight:bold">' . $name . '</font>';
		$output[] = '<td nowrap>';
		if (empty($row['url_self'])) {
			$output[] = $name;
		} else {
			$output[] = '<a href = "'.$row['url_self'].'" target="_blank">'.$name.'</a>';
		}
		if ($username == $user_id) {
			$output[] = '&nbsp;[<a href = "editrk.php?id='.$id.'"><font size="2" color="blue">Edit</font></a>]';
		}
		$output[] = '</td>';
		$output[] = '<td align="center">'.$row['organism'].'</td>';
		$output[] = '<td align="center">'.$row['mark'].'</td>';
		$output[] = '<td align="center">'.$row['data_type'].'</td>';
		$output[] = '<td align="center">'.$row['type'].'</td>';
		$output[] = '<td align="center">'.$row['center'].'</td>';
		$output[] = '<td align="center">'.$row['access'].'</td>';
		$output[] = '<td align="center" nowrap>'.$row['uploaddate'].'</td>';
		$output[] = '</tr>';
	}
	$output[] = '</tbody>';
	$output[] = '</table>';
	if ($maxpage > 1) {
		$output[] = '<table  border="0" cellspacing="0" cellpadding="0" bordercolordark="#0099FF" bordercolorlight="#0099FF"><tr><td align="left" class="noborder">';
		if ($page == 1) {
			$ppage = $page+1;
			$output[] = "Previous&nbsp;&nbsp;<a href=brwtrk.php?page=$ppage#track_table>Next</a>&nbsp;&nbsp;";
		} elseif ($page == $maxpage) {
			$ppage = $page-1;
			$output[] = "<a href=brwtrk.php?page=$ppage#track_table>Previous</a>&nbsp;&nbsp;Next&nbsp;&nbsp;";
		} else {
			$ppage = $page-1;
			$output[] = "<a href=brwtrk.php?page=$ppage#track_table>Previous</a>&nbsp;&nbsp;";
			$ppage = $page+1;
			$output[] = "<a href=brwtrk.php?page=$ppage#track_table>Next</a>&nbsp;&nbsp;";
		}
		if ($maxpage < 17) {
			for ($counter = 1; $counter <= $maxpage; $counter++) {
				if ($counter == $page) {
					$output[] = "$counter&nbsp;&nbsp;";
				} else {
					$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
				}
			}
		} else {
			if ($page < 9) {
				for ( $counter = 1; $counter <= 9; $counter++) {
					if ($counter == $page) {
						$output[] = "$counter&nbsp;&nbsp;";
					} else {
						$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
					}
				}
				$output[] = '...&nbsp;&nbsp;';
				for ( $counter = $maxpage-1; $counter <= $maxpage; $counter++) {
					$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
				}
			} elseif ($page > $maxpage-8) {
				for ( $counter = 1; $counter <= 2; $counter++) {
					$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
				}
				$output[] = '...&nbsp;&nbsp;';
				for ( $counter = $maxpage-8; $counter <= $maxpage; $counter++) {
					if ($counter == $page) {
						$output[] = "$counter&nbsp;&nbsp;";
					} else {
						$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
					}
				}
			} else {
				for ( $counter = 1; $counter <= 2; $counter++) {
					$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
				}
				$output[] = '...&nbsp;&nbsp;';
				for ( $counter = $page-5; $counter <= $page+5; $counter++) {
					if ($counter == $page) {
						$output[] = "$counter&nbsp;&nbsp;";
					} else {
						$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
					}
				}
				$output[] = '...&nbsp;&nbsp;';
				for ( $counter = $maxpage-1; $counter <= $maxpage; $counter++) {
					$output[] = "<a href=brwtrk.php?page=$counter#track_table>$counter</a>&nbsp;&nbsp;";
				}
			}
		}
		$output[] = '</td></tr></table>';
	}
	$output[] = '<p>';
	$output[] = '<input type="submit" name="create" value="Create A New Configuration">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="addeore" value="Add More Tracks">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="delete" value="Delete Selected Tracks" onClick="return confirmSubmit(\'Do you really want to delete?\');">';
	$output[] = '</p>';
	$output[] = '</form>';
	return join('', $output);
}

function show_sdec_track() {
	global $db;
	$query = "select track_id from track where track_id in (select track_id from sdectrack)";
	$stmt = $db->prepare($query);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$num_rows = $stmt->rowCount();
	while($row = $stmt->fetch()){
		$id = $row['track_id'];
		$refineids[$id] = $id;
	}
	$_SESSION['refineids'] = $refineids;
	$_SESSION['submitids'] = $refineids;
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'brwtrk.php#track_table';
	header("Location: http://$host$uri/$extra");
	exit;
}

// Show all configs in database.
function show_conf() {
	global $db;
	$username = $_SESSION['username'];
	$query = "select * from configuration where user_id = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $username);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$num_rows = $stmt->rowCount();
	$output[] = '<p>';
	$output[] = '<input type=button value=\'Create A New Configuration\' onClick="window.location.href=\'brwtrk.php\'">';
	$output[] = '</p>';
	$output[] = '<h1>All Configurations &nbsp;&nbsp;(' . $num_rows;
	if ($num_rows <= 1) {
		$output[] = ' configuration)</h1>';
	} else {
		$output[] = ' configurations)</h1>';
	}
	if ($num_rows == 0) {
		$output[] = 'You have not saved any configurations yet. Please click "Create A New Configuration".';
		return join('', $output);
	}
	$query = "select conf_id, conf_name, build_date, if (length(description) > 25, concat(substr(description, 1, 25), '...'), description) as conf_desc from configuration where user_id = :username";
	$keyword = $_POST['keyword'];
	if ((empty($_POST['dorm'])) and (empty($_POST['showall'])) and (!empty($keyword))) {
		$flag1 = 1;
	} else {
		$flag1 = 0;
	}
	$conffid = $_POST['confid'];
	if ((empty($_POST['dorm'])) and (empty($_POST['showall'])) and (!empty($conffid))) {
		$allids = implode(",", $conffid);
		$flag2 = 1;
	} else {
		$flag2 = 0;
	}
	$kw = "%$keyword%";
	if (($flag1==0) and ($flag2==0)) {
		$query .= ' order by build_date desc';
		$stmt = $db->prepare($query);
		$stmt->bindParam(':username', $username);
		$stmt->execute() or goBack("Error happened. Please try later.");
	} elseif (($flag1==1) and ($flag2==0)) {
		$query .= " and ((conf_name like :keyword) or (build_date like :keyword) or (description like :keyword))";
		$query .= ' order by build_date desc';
		$stmt = $db->prepare($query);
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':keyword', $kw);
		$stmt->execute() or goBack("Error happened. Please try later.");
	} elseif (($flag1==0) and ($flag2==1)) {
		//$query .= " and conf_id in (:allids)";
		$query .= " and conf_id in ($allids)";
		$query .= ' order by build_date desc';
		$stmt = $db->prepare($query);
		$stmt->bindParam(':username', $username);
		//$stmt->bindParam(':allids', $allids);
		$stmt->execute() or goBack("Error happened. Please try later.");
	} else {
		$query .= " and ((conf_name like :keyword) or (build_date like :keyword) or (description like :keyword))";
		//$query .= " and conf_id in (:allids)";
		$query .= " and conf_id in ($allids)";
		$query .= ' order by build_date desc';
		$stmt = $db->prepare($query);
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':keyword', $kw);
		//$stmt->bindParam(':allids', $allids);
		$stmt->execute() or goBack("Error happened. Please try later.");
	}
	if ($stmt->rowCount() == 0) {
		goBack("No items founded.");
	}
	$output[] = '<form action="brwconf.php" method="post">';
	$output[] = '<p>';
	$output[] = '<input type="text" name="keyword" size="30" value="'.$keyword.'" onmouseover=\'this.focus();this.select()\'/>';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" value="Search" />';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="showall" value="Show All">';
	$output[] = '</p>';
	$output[] = '<p>';
	$output[] = '<input type="submit" name="dorm" value="Delete Selected Configurations" onClick="return confirmSubmit(\'Do you really want to delete?\');">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="dorm" value="Merge Selected Configurations"">';
	$output[] = '</p>';
	$output[] = '<table id="main_table" class="sortable" width="90%" border="2">';
	$output[] = '<thead>';
	$output[] = '<tr><th class="sorttable_nosort" align="center"><input type="checkbox" class="bigCheckbox" name="checkall" onClick="checkedAll(\'main_table\');" /></td><th class="sorttable_nosort" width=150>[V]</th><th nowrap>Config name</th><th nowrap>Build date</th><th>Description</th></tr>';
	$output[] = '</thead>';
	$output[] = '<tbody id="tInfo">';
	while($row = $stmt->fetch()){
		$id = $row['conf_id'];
		if ((!empty($_SESSION['newflag'])) and ($_SESSION['newflag'] == $id)) {
			//unset($_SESSION['newflag']);
			$output[] = '<tr class="highlight">';
			$newflag = '<img alt="new logo" src="img/new.gif" />';
		} else {
			$output[] = '<tr>';
			$newflag = '';
		}
		$output[] = '<td nowrap align="center"><input type="checkbox" onClick="change(this)" class="bigCheckbox" name="confid['.$id.']" value="'.$id.'"';
		if (!empty($conffid[$id])) {
			$output[] = ' checked';
		}
		$output[] = '></td><td nowrap align="center"><a href = "build.php?viewit='.$id.'" target=_top>View</a></td>';
		$output[] = '<td nowrap>';
		$output[] = $newflag;
		$output[] = '<a href = "aj_conf.php?action=load&id='.$id.'">';
		if (empty($row['conf_name'])) {
			$output[] = '&nbsp;';
		} else {
			$output[] = $row['conf_name'];
		}
		$output[] = '</a>';
		$output[] = '</td>';
		$output[] = '<td nowrap align="center">';
		if (empty($row['build_date'])) {
			$output[] = '&nbsp;';
		} else {
			$output[] = $row['build_date'];
		}
		$output[] = '</td>';
		$output[] = '<td nowrap>';
		if (empty($row['conf_desc'])) {
			$output[] = '&nbsp;';
		} else {
			$output[] = $row['conf_desc'];
		}
		$output[] = '</td>';
		$output[] = '</tr>';
	}
	$output[] = '</tbody>';
	$output[] = '</table>';
	$output[] = '<p>';
	$output[] = '<input type="submit" name="dorm" value="Delete Selected Configurations" onClick="return confirmSubmit(\'Do you really want to delete?\');">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type="submit" name="dorm" value="Merge Selected Configurations"">';
	$output[] = '</p>';
	$output[] = '</form>';
	return join('', $output);
}

// Show track types in database.
function show_type(){
	$browse = $_GET['browse'];
	if($browse == 'type'){
		// Select track types and count for each type.
		$username = $_SESSION['username'];
		$usergrp = $_SESSION['usergrp'];
		global $db;
		$query = "select track_type, count(*) as cnt from track left join user using(user_id) where user_id = ? or (access='group' and group_id = ?) or access='public' group by track_type";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $username);
		$stmt->bindParam(2, $usergrp);
		$stmt->execute() or goBack("Error happened. Please try later.");
		$output[] = '<table id="main_table" class="sortable" width="90%" border="2">';
		$output[] = '<thead>';
		$output[] = '<tr><th>Type</th><th>Number</th></tr>';
		$output[] = '</thead>';
		$output[] = '<tbody id="tInfo">';
		while($row = $stmt->fetch()){	// list track types in a column.
			$output[] = '<tr><td nowrap><a href = "brwtrk.php?type='.$row['track_type'].'">'.$row['track_type']
			.'</a></td><td nowrap>'.$row['cnt'].'</td></tr>';
		}
		$output[] = '</tbody>';
		$output[] = '</table>';
		return join('', $output);
	}
}

// Set default AJ genomic positions and zoom levels in session variables.
function set_default_pos_zoom(){
	$_SESSION['start_chr'] = '1';
	$_SESSION['start_pos'] = 1;
	$_SESSION['bases'] = 200;
	$_SESSION['pixels'] = 1;
}

// Modify an AJ configuration according to user actions.
// AJ configuration stored in session variables.
function modify_aj_conf() {
	// Process actions. The AJ configuration is stored in session variable aj_conf.
	$action = $_GET['action'];
	if ($action) {	// only if user issued an action.
		switch ($action) {
			case 'new':	// Clear all session variables before create a new configuration.
				unset($_SESSION['aj_conf']);
				unset($_SESSION['conf_id']);
				unset($_SESSION['conf_name']);
				unset($_SESSION['conf_desc']);
				set_default_pos_zoom();
				unset($_SESSION['conf_pub']);
				unset($_SESSION['conf_url']);
				break;
			case 'add':
				$trk_id = $_GET['id'];
				if(!isset($_SESSION['aj_conf'])){
					set_default_pos_zoom();
				}
				$_SESSION['aj_conf'][$trk_id] = read_default_ht($trk_id);	// use default height for new.
				break;
			case 'delete':
				if ($_SESSION['aj_conf']) {
					unset($_SESSION['aj_conf'][$_GET['id']]);
				}
				break;
			case 'clear':
				unset($_SESSION['aj_conf']);
				set_default_pos_zoom();
				break;
			case 'load':
				load_aj_conf();
				break;
			case 'remove':
				rm_aj_conf();
				break;
		}
	}
	$action = $_POST['dorm'];
	if ($action) {	// only if user issued an action.
		$conffid = $_POST['confid'];
		if (empty($conffid)) {
			goBack("Please select the configurations by using the checkbox first.");
		}
		global $db;
		switch ($action) {
			case 'Delete Selected Configurations':
				foreach ($conffid as $cid) {
					$query = "delete from configuration where conf_id = ?";
					$stmt = $db->prepare($query);
					$stmt->bindParam(1, $cid);
					$stmt->execute() or goBack("Error happened. Please try later.");
				}
				unset($_SESSION['conf_id']);
				unset($_SESSION['conf_name']);
				unset($_SESSION['conf_desc']);
				unset($_SESSION['conf_url']);
				unset($_SESSION['conf_pub']);
				unset($_SESSION['aj_conf']);
				unset($_SESSION['start_chr']);
				unset($_SESSION['start_pos']);
				unset($_SESSION['bases']);
				unset($_SESSION['pixels']);
				set_default_pos_zoom();
				break;
			case 'Merge Selected Configurations':
				if (count($conffid) <= 1) {
					goBack("You have to select at least two configurations.");
				}
				foreach ($conffid as $cid) {
					$query = "select * from configuration where conf_id = ?";
					$stmt = $db->prepare($query);
					$stmt->bindParam(1, $cid);
					$stmt->execute() or goBack("Error happened. Please try later.");
					$row = $stmt->fetch();
					if (empty($start_chr)) {
						$start_chr = $row['start_chr'];
					}
					if (empty($start_pos)) {
						$start_pos = $row['start_pos'];
					}
					if (empty($bases)) {
						$bases = $row['bases'];
					}
					if (empty($pixels)) {
						$pixels = $row['pixels'];
					}
					$trk_ids = explode(",", $row['trk_ids']);
					$trk_hts = explode(",", $row['trk_hts']);
					foreach ($trk_ids as $loc => $trk_id) {
						$trk_ids_new[$trk_id] = $trk_hts[$loc];
					}
				}
				foreach ($trk_ids_new as $ids => $hts) {
					if (empty($ids_new)) {
						$ids_new .= $ids;
					} else {
						$ids_new .= ',' . $ids;
					}
					if (empty($hts_new)) {
						$hts_new .= $hts;
					} else {
						$hts_new .= ',' . $hts;
					}
				}
				if (strlen($ids_new)>1500) {
					goBack("Too many tracks in one configuration. Try less tracks please.");
				}
				$conf_name = 'com_configuration';
				$datestr = get_date();
				$conf_desc = '';
				$user_id = $_SESSION['username'];
				$query = "insert into configuration (conf_name, build_date, description, start_chr, start_pos, bases, pixels, trk_ids, trk_hts, user_id) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$stmt = $db->prepare($query);
				$stmt->bindParam(1, $conf_name);
				$stmt->bindParam(2, $datestr);
				$stmt->bindParam(3, $conf_desc);
				$stmt->bindParam(4, $start_chr);
				$stmt->bindParam(5, $start_pos);
				$stmt->bindParam(6, $bases);
				$stmt->bindParam(7, $pixels);
				$stmt->bindParam(8, $ids_new);
				$stmt->bindParam(9, $hts_new);
				$stmt->bindParam(10, $user_id);
				$stmt->execute() or goBack("Error happened. Please try later.");
				$newid = $db->lastInsertId();
				$_SESSION['newflag'] = $newid;
				unset($_SESSION['conf_id']);
				unset($_SESSION['conf_name']);
				unset($_SESSION['conf_desc']);
				unset($_SESSION['conf_url']);
				unset($_SESSION['conf_pub']);
				unset($_SESSION['aj_conf']);
				unset($_SESSION['start_chr']);
				unset($_SESSION['start_pos']);
				unset($_SESSION['bases']);
				unset($_SESSION['pixels']);
				set_default_pos_zoom();
				$host  = $_SERVER['HTTP_HOST'];
				$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
				$extra = 'aj_conf.php?action=load&id='.$newid;
				header("Location: http://$host$uri/$extra");
				exit;
		}
	}
}


// Remove an AJ configuration from database.
function rm_aj_conf(){
	if($_SESSION['conf_id']){
		$conf_id = $_SESSION['conf_id'];
		global $db;
		$query = "delete from configuration where conf_id = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $conf_id);
		$stmt->execute() or goBack("Error happened. Please try later.");
		unset($_SESSION['aj_conf']);
		unset($_SESSION['conf_id']);
		unset($_SESSION['conf_name']);
		unset($_SESSION['conf_desc']);
		set_default_pos_zoom();
		unset($_SESSION['conf_pub']);
		unset($_SESSION['conf_url']);
	}
}

// Load a configuration from database using config id.
function load_aj_conf(){
	if ($_GET['id']) {
		$conf_id = $_GET['id'];
		global $db;
		$query = "select conf_id, conf_name, description, trk_ids, trk_hts, user_id, start_chr, start_pos, bases, pixels from configuration where conf_id = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $conf_id);
		$stmt->execute() or goBack("Error happened. Please try later.");
		$row = $stmt->fetch();
		// Determine whether user has access to the configuration.
		$user_id = $row['user_id'];
		$username = $_SESSION['username'];
		if($user_id != $username){
			trigger_error("Invalid user id. Illegal access to this configuration.");
			exit;
		}
		// Load config.
		$_SESSION['conf_id'] = $row['conf_id'];
		$_SESSION['conf_name'] = $row['conf_name'];
		$_SESSION['conf_desc'] = $row['description'];
		$_SESSION['start_chr'] = $row['start_chr'];
		$_SESSION['start_pos'] = $row['start_pos'];
		$_SESSION['bases'] = $row['bases'];
		$_SESSION['pixels'] = $row['pixels'];
		$a_trk = explode(',', $row['trk_ids']);	// track ids.
		$a_tht = explode(',', $row['trk_hts']);	// track heights.
		// use default height if array of height is off sanity.
		if(empty($a_tht[0]) || count($a_trk)!=count($a_tht)){
			$a_tht = set_default_ht($a_trk);
		}
		$a_id_ht = array_combine($a_trk, $a_tht);	// array of id => height.
		foreach($a_id_ht as $trk_id => $trk_ht){
			$trk_id = trim($trk_id);
			if (!empty($aj_conf[$trk_id])) {
				continue;
			}
			$query = "select track_id from track where track_id = ?";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $trk_id);
			$stmt->execute() or goBack("Error happened. Please try later.");
			if ($stmt->rowCount() == 0) {
				continue;
			}
			$aj_conf[$trk_id] = $trk_ht;
		}
		$_SESSION['aj_conf'] = $aj_conf;
	} else{
		trigger_error("No valid configuration ID given. AJ configuration was not loaded.");
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

// Save or update the configuration into database. Update the session variables as well.
function save_conf($conf_name, $conf_desc, $start_chr, $start_pos, $bases, $pixels){
	global $db;
	$datestr = get_date();	// datetime in mysql format.
	if(!$_SESSION['aj_conf']){
		goBack("Your AJ configuration contains no track.");
	}
	$trk_ids = implode(',', array_keys($_SESSION['aj_conf']));	// string stored: trk_id1,trk_id2,...,trk_idn
	$trk_hts = implode(',', array_values($_SESSION['aj_conf']));	// trk_ht1,trk_ht2,...,trk_htn

	// Check whether the configuration has been stored.
	if(isset($_SESSION['conf_id'])){
		$conf_id = $_SESSION['conf_id'];
		$query = "update configuration set conf_name = ?, description = ?, start_chr = ?, start_pos = ?, bases = ?, pixels = ?, trk_ids = ?, trk_hts = ? where conf_id = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $conf_name);
		$stmt->bindParam(2, $conf_desc);
		$stmt->bindParam(3, $start_chr);
		$stmt->bindParam(4, $start_pos);
		$stmt->bindParam(5, $bases);
		$stmt->bindParam(6, $pixels);
		$stmt->bindParam(7, $trk_ids);
		$stmt->bindParam(8, $trk_hts);
		$stmt->bindParam(9, $conf_id);
		$stmt->execute() or goBack("Error happened. Please try later.");
	} else {
		// Add a new record in configuration table.
		$user_id = $_SESSION['username'];
		$query = "insert into configuration (conf_name, build_date, description, start_chr, start_pos, bases, pixels, trk_ids, trk_hts, user_id) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $conf_name);
		$stmt->bindParam(2, $datestr);
		$stmt->bindParam(3, $conf_desc);
		$stmt->bindParam(4, $start_chr);
		$stmt->bindParam(5, $start_pos);
		$stmt->bindParam(6, $bases);
		$stmt->bindParam(7, $pixels);
		$stmt->bindParam(8, $trk_ids);
		$stmt->bindParam(9, $trk_hts);
		$stmt->bindParam(10, $user_id);
		$stmt->execute() or goBack("Error happened. Please try later.");
		$_SESSION['conf_id'] = $db->lastInsertId();
	}
	$conf_id = $_SESSION['conf_id'];
	$query = "select conf_name, description, start_chr, start_pos, bases, pixels, trk_ids, trk_hts, user_id from configuration where conf_id = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $conf_id);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$row = $stmt->fetch();
	// Update other session variables.
	$_SESSION['conf_name'] = $row['conf_name'];
	$_SESSION['conf_desc'] = $row['description'];
	$_SESSION['start_chr'] = $row['start_chr'];
	$_SESSION['start_pos'] = $row['start_pos'];
	$_SESSION['bases'] = $row['bases'];
	$_SESSION['pixels'] = $row['pixels'];
}

// Generate a new AJ instance: a html and a js file.
function gen_aj_inst($conf_id) {
	global $db;
	global $gen_directory,$gen_directory1,$url_root;	// AJ instance location.
	global $conf_head, $conf_tail1, $conf_tail2, $conf_tail3, $html_left, $html_right;	// AJ instance files contents.
	// Always assume the configuration has been saved to the database before the instance is created.
	$query = "select user_id,start_chr,start_pos,bases,pixels,trk_ids,trk_hts from configuration where conf_id = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $conf_id);
	$stmt->execute() or goBack("Error happened. Please try later.");
	if ($stmt->rowCount() == 0) {
		goBack("Database is inconsistent: configuration ID was not found...\n");
	}
	$row = $stmt->fetch();
	$conf_name = $row['user_id'];	// Name will be used for both configuration and html.
	$a_trk = explode(',', $row['trk_ids']);	// trk1,trk2,...,trkn.
	$a_tht = explode(',', $row['trk_hts']);	// ht1,ht2,...,htn.
	if(empty($a_tht[0]) || count($a_trk)!=count($a_tht)){
		$a_tht = set_default_ht($a_trk);
	}
	$a_id_ht = array_combine($a_trk, $a_tht);	// array of id => height.

	// Iterate through selected tracks in session. Format and output in a configuration file.
	$confpool = $conf_head;	// pooled strings of configuration file.
	$trackpool = "";	// string containing all tracks' format.
	$t = 0;	// track index number.
	// $aj_conf = $_SESSION['aj_conf'];
	$aj_spec = read_aj_spec();
	$dis_num = 0;	// number of disabled tracks.
	$organism = 0;
	foreach ($a_id_ht as $trk_id => $trk_ht) {	// read all selected tracks.
		$qq = "select track_name, track_type, track_url from track where track_id = ?";
		$ss = $db->prepare($qq);
		$ss->bindParam(1, $trk_id);
		$ss->execute() or goBack("Error happened. Please try later.");
		$rr = $ss->fetch();
		$t++;	// number of tracks to be displayed.
		if ($trk_id == 64) {
			$organism = 1;
		}
		$type = $rr['track_type'];
		$trackstruct = array(
		'name'	=> $rr['track_name'],
		'type'	=> $type,
		'path'	=> $aj_spec[$type]['aj_path'],
		'url'	=> $rr['track_url'],
		// Use AJ default height if empty.
		'height'  => empty($trk_ht)? $aj_spec[$type]['aj_height']:$trk_ht,
		'showCtrl'=> 'true'
		);
		$comma = $t > 1 ? ',' : '';	// add comma for 2nd track and up.
		$trackpool .= $comma.formatrack($trackstruct, $t);
	}
	$confpool .= $trackpool;	// all tracks go here.
	$confpool .= "\n	],\n";	// enclosing mark for tracks.

	$confpool .= activetracks($t);	// Concatenate active tracks selection.

	// Add a timestamp to configuration name for both js and html files.
	// This mechanism avoids the file names confliction when two users give the
	// same name for their configurations.
	$conf_name .= $conf_id;
	$conf_name = base64_encode($conf_name);
	$conf_name = ereg_replace("=","X",$conf_name);
	// Add tail and write js file.
	if ($organism == 1) {
		$confpool .= $conf_tail1;
	} else {
		$confpool .= $conf_tail3;
	}
	$start_chr = $row['start_chr'];
	$start_pos = $row['start_pos'];
	$bases = $row['bases'];
	$pixels = $row['pixels'];
	$confpool .= "		assembly : '$start_chr',\n";
	$confpool .= "		position : '$start_pos',\n";
	$confpool .= "		bases    : $bases,\n";
	$confpool .= "		pixels   : $pixels";
	$confpool .= $conf_tail2;

	$file_name_base = $gen_directory . $conf_name;
	file_put_contents($file_name_base.'.js', $confpool);

	// Generate html file.
	$htmlpool = $html_left . $conf_name . '.js' . $html_right;
	file_put_contents($file_name_base.'.html', $htmlpool);

	// return the generated html.
	$target_url = $url_root . $gen_directory1 . $conf_name . '.html';
	return array($target_url, $t);
}

function make_available($id,$avail) {
	if(!$id){
		trigger_error("Trying to remove invalid track id from configuration!");
	}
	global $db;
	$aavail = explode(',',$avail);
	unset($groupname);
	foreach ($aavail as $loc=> $avaname) {
		$avaname = trim($avaname);
		$aavail[$loc] = $avaname;
		$avaname = strtoupper($avaname);
		if ($avaname == 'SDEC') {
			$query = "select group_name from sdecgroup";
			$stmt = $db->prepare($query);
			$stmt->execute() or goBack("Error happened. Please try later.");
			while ($row = $stmt->fetch()) {
				$group_name = $row['GROUP_NAME'];
				$group_name = "'$group_name'";
				$groupname[$group_name] = $group_name;
			}
		} else {
			$query = "select user_id from user where account_group = ? or user_id = ?";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $avaname);
			$stmt->bindParam(2, $avaname);
			$stmt->execute() or goBack("Error happened. Please try later.");
			if ($stmt->rowCount() == 0) {
				unset($aavail[$loc]);
				continue;
			}
		}
		$avaname = "'$avaname'";
		$groupname[$avaname] = $avaname;
	}
	$allname = implode(',',$groupname);
	//$query = "select user_id from user where (account_group in (?)) or (user_id in (?))";
	$query = "select user_id from user where (account_group in ($allname)) or (user_id in ($allname))";
	$stmt = $db->prepare($query);
	//$stmt->bindParam(1, $allname);
	//$stmt->bindParam(2, $allname);
	$stmt->execute() or goBack("Error happened. Please try later.");
	if ($stmt->rowCount() == 0) {
		goBack("The name list contains no valid name.");
	}
	while($row = $stmt->fetch()) {
		$user_id = $row['user_id'];
		$user_id = strtoupper($user_id);
		if ($user_id == 'SDEC') {
			continue;
		}
		$qq = "select track_list from usertrack where user_id = ?";
		$ss = $db->prepare($qq);
		$ss->bindParam(1, $user_id);
		$ss->execute() or goBack("Error happened. Please try later.");
		if ($ss->rowCount() == 0) {
			goBack("$user_id has wrong information.");
		}
		$rr = $ss->fetch();
		$track_list = $rr['track_list'];
		$patterns = array("/^$id,/","/,$id,/","/,$id$/");
		$replacements = array('',',','');
		$track_list = preg_replace($patterns,$replacements,$track_list);
		$track_list .= ','.$id;
		$qq = "update usertrack set track_list = ? where user_id = ?";
		$ss = $db->prepare($qq);
		$ss->bindParam(1, $track_list);
		$ss->bindParam(2, $user_id);
		$ss->execute() or goBack("Error happened. Please try later.");
	}
	$allname = implode(',',$aavail);
	$query = "update track set track_user = ? where track_id = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $allname);
	$stmt->bindParam(2, $id);
	$stmt->execute() or goBack("Error happened. Please try later.");
}

function make_nonavailable($id,$avail,$tuser) {
	if(!$id){
		trigger_error("Trying to remove invalid track id from configuration!");
	}
	global $db;
	$aavail = explode(',',$avail);
	foreach ($aavail as $avaname) {
		$avaname = trim($avaname);
		$avaname = strtoupper($avaname);
		$aavail[$avaname] = $avaname;
	}
	$oavail = explode(',',$tuser);
	unset($groupname);
	foreach ($oavail as $loc=> $avaname) {
		$avaname = strtoupper($avaname);
		if (!empty($aavail[$avaname])) {
			continue;
		}
		if ($avaname == 'SDEC') {
			$query = "select group_name from sdecgroup";
			$stmt = $db->prepare($query);
			$stmt->execute() or goBack("Error happened. Please try later.");
			while ($row = $stmt->fetch()) {
				$group_name = $row['group_name'];
				$group_name = "'$group_name'";
				$groupname[$group_name] = $group_name;
			}
		}
		$avaname = "'$avaname'";
		$groupname[$avaname] = $avaname;
	}
	if (empty($groupname)) {
		return;
	}
	$allname = implode(',',$groupname);
	//$query = "select user_id from user where (account_group in (?)) or (user_id in (?))";
	$query = "select user_id from user where (account_group in ($allname)) or (user_id in ($allname))";
	$stmt = $db->prepare($query);
	//$stmt->bindParam(1, $allname);
	//$stmt->bindParam(2, $allname);
	$stmt->execute() or goBack("Error happened. Please try later.");
	while($row = $stmt->fetch()) {
		$user_id = $row['user_id'];
		$user_id = strtoupper($user_id);
		if ($user_id == 'SDEC') {
			continue;
		}
		$qq = "select track_list from usertrack where user_id = ?";
		$ss = $db->prepare($qq);
		$ss->bindParam(1, $user_id);
		$ss->execute() or goBack("Error happened. Please try later.");
		if ($ss->rowCount() != 1) {
			goBack("$user_id has wrong information.");
		}
		$rr = $ss->fetch();
		$track_list = $rr['track_list'];
		$patterns = array("/^$id,/","/,$id,/","/,$id$/");
		$replacements = array('',',','');
		$track_list = preg_replace($patterns,$replacements,$track_list);
		$qq = "update usertrack set track_list = ? where user_id = ?";
		$ss = $db->prepare($qq);
		$ss->bindParam(1, $track_list);
		$ss->bindParam(2, $user_id);
		$ss->execute() or goBack("Error happened. Please try later.");
		$qq = "select conf_id, trk_ids, trk_hts from configuration where ((trk_ids regexp '^$id') or (trk_ids regexp ',$id,') or (trk_ids regexp '$id$') or (trk_ids regexp '^$id$')) and user_id='$user_id'";
		$ss = $db->prepare($qq);
		$ss->execute() or goBack("Error happened. Please try later.");
		while($rr = $ss->fetch()) {
			$conf_id = $rr['conf_id'];
			$trk_ids = $rr['trk_ids'];
			$trk_hts = $rr['trk_hts'];
			$a_trk = explode(',', $trk_ids);
			$a_htk = explode(',', $trk_hts);
			foreach($a_trk as $loc => $trk_id){
				if($trk_id == $id){
					unset($a_trk[$loc]);
					unset($a_htk[$loc]);
				}
			}
			if (count($a_trk) == 0) {
				$qqq = "delete from configuration where conf_id = ?";
				$sss = $db->prepare($qqq);
				$sss->bindParam(1, $conf_id);
				$sss->execute() or goBack("Error happened. Please try later.");
				unset($_SESSION['conf_id']);
				unset($_SESSION['conf_name']);
				unset($_SESSION['conf_desc']);
				unset($_SESSION['conf_url']);
				unset($_SESSION['conf_pub']);
				unset($_SESSION['aj_conf']);
				unset($_SESSION['start_chr']);
				unset($_SESSION['start_pos']);
				unset($_SESSION['bases']);
				unset($_SESSION['pixels']);
				set_default_pos_zoom();
			} else {
				$trk_ids = implode(',', $a_trk);
				$trk_hts = implode(',', $a_htk);
				$qqq = "update configuration set trk_ids = ?, trk_hts = ? where conf_id = ?";
				$sss = $db->prepare($qqq);
				$sss->bindParam(1, $trk_ids);
				$sss->bindParam(2, $trk_hts);
				$sss->bindParam(3, $conf_id);
				$sss->execute() or goBack("Error happened. Please try later.");
				$aj_conf = $_SESSION['aj_conf'];
				if(isset($aj_conf[$id])){	// also clean AJ configuration.
					unset($aj_conf[$id]);
					$_SESSION['aj_conf'] = $aj_conf;
				}
			}
		}
	}
}

// Remove the track id from the configurations that contain it.
function rm_trk_conf($id) {
	if(!$id){
		trigger_error("Trying to remove invalid track id from configuration!");
	}
	global $db;
	$query = "delete from track where track_id = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $id);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$query = "select conf_id, trk_ids, trk_hts from configuration where (trk_ids regexp '^$id') or (trk_ids regexp ',$id,') or (trk_ids regexp '$id$') or (trk_ids regexp '^$id$')";
	$stmt = $db->prepare($query);
	$stmt->execute() or goBack("Error happened. Please try later.");
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
			$ss->execute() or goBack("Error happened. Please try later.");
			unset($_SESSION['conf_id']);
			unset($_SESSION['conf_name']);
			unset($_SESSION['conf_desc']);
			unset($_SESSION['conf_url']);
			unset($_SESSION['conf_pub']);
			unset($_SESSION['aj_conf']);
			unset($_SESSION['start_chr']);
			unset($_SESSION['start_pos']);
			unset($_SESSION['bases']);
			unset($_SESSION['pixels']);
			set_default_pos_zoom();
		} else {
			$trk_ids = implode(',', $a_trk);
			$trk_hts = implode(',', $a_htk);
			$qq = "update configuration set trk_ids = ?, trk_hts = ? where conf_id = ?";
			$ss = $db->prepare($qq);
			$ss->bindParam(1, $trk_ids);
			$ss->bindParam(2, $trk_hts);
			$ss->bindParam(3, $conf_id);
			$ss->execute() or goBack("Error happened. Please try later.");
			$aj_conf = $_SESSION['aj_conf'];
			if(isset($aj_conf[$id])){	// also clean AJ configuration.
				unset($aj_conf[$id]);
				$_SESSION['aj_conf'] = $aj_conf;
			}
		}
	}
	$query = "select user_id, track_list from usertrack where (track_list regexp '^$id,') or (track_list regexp ',$id,') or (track_list regexp ',$id$') or (track_list regexp '^$id$')";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $id);
	$stmt->execute() or goBack("Error happened. Please try later.");
	while($row = $stmt->fetch()){
		$user_id = $row['user_id'];
		$track_list = $row['track_list'];
		$patterns = array("/^$id,/","/,$id,/","/,$id$/");
		$replacements = array('',',','');
		$track_list = preg_replace($patterns,$replacements,$track_list);
		$qq = "update usertrack set track_list = ? where user_id = ?";
		$ss = $db->prepare($qq);
		$ss->bindParam(1, $track_list);
		$ss->bindParam(2, $user_id);
		$ss->execute() or goBack("Error happened. Please try later.");
	}
}

function show_single_track() {
	global $db;
	$id = $_GET['id'];	// the track id.
	if(!$id){
		goBack("Page URL error: No valid track id specified!");
	}

	$action = $_GET['action'];
	switch($action){
		case 'save':
			$query = "select access, user_id, track_user from track where track_id = ?";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $id);
			$stmt->execute() or goBack("Error happened. Please try later.");
			$row = $stmt->fetch();
			$tuser = $row['track_user'];
			$type = $_POST['trk_type'];
			$organism = $_POST['organism'];
			$center = $_POST['center'];
			$access = $_POST['access'];
			if ($access != $row['access']) {
				$user_id = $row['user_id'];
				change_access($id,$user_id,$access);
			}
			$name = trim($_POST['trk_name']);
			if (strlen($name)>80) {
				goBack("Track name ($name) is too long.");
			} elseif (strlen($name)==0) {
				goBack("Track name can not be empty.");
			}
			$query = "select track_name from track where track_name = ? and track_id != ?";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $name);
			$stmt->bindParam(2, $id);
			$stmt->execute() or goBack("Error happened. Please try later.");
			if ($stmt->rowCount() != 0) {
				goBack("The track name ($name) already exists. Please try different name.");
			}
			$url = trim($_POST['trk_url']);
			if (strlen($url)>120) {
				goBack("Track URL ($url) is too long.");
			} elseif (strlen($url)==0) {
				goBack("Track URL can not be empty.");
			}
			if ((!preg_match('/^http/', $url)) or (!preg_match('/\.php$/', $url))) {
				goBack("The URL ($url) is not correct format.");
			}
			$query = "select track_name from track where track_url = ? and track_id != ?";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $url);
			$stmt->bindParam(2, $id);
			$stmt->execute() or goBack("Error happened. Please try later.");
			if ($stmt->rowCount() != 0) {
				goBack("The track URL ($url) already exists.");
			}
			$query = "update track set track_name = ?, track_type = ?, track_url = ?, access = ?, organism = ?, center = ? where track_id = ?";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $name);
			$stmt->bindParam(2, $type);
			$stmt->bindParam(3, $url);
			$stmt->bindParam(4, $access);
			$stmt->bindParam(5, $organism);
			$stmt->bindParam(6, $center);
			$stmt->bindParam(7, $id);
			$stmt->execute() or goBack("Error happened. Please try later.");
			$avail = trim($_POST['avail']);
			if ($avail == $tuser) {
			} elseif (empty($avail)) {
				make_nonavailable($id,$avail,$tuser);
				$query = "update track set track_user = '' where track_id = ?";
				$stmt = $db->prepare($query);
				$stmt->bindParam(1, $id);
				$stmt->execute() or goBack("Error happened. Please try later.");
			} elseif (empty($tuser)) {
				make_available($id,$avail);
			} else {
				make_nonavailable($id,$avail,$tuser);
				make_available($id,$avail);
			}
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			$extra = 'editrk.php?fin=1&id='.$id;
			header("Location: http://$host$uri/$extra");
			exit;
			break;
		case 'delete':
			rm_trk_conf($id);
			// Offer the user some feedbacks.
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			$extra = 'brwtrk.php';
			header("Location: http://$host$uri/$extra");
			exit;
			break;
		default:
			break;
	}

	// Fetch track information according to track id.
	$query = "select track_name, track_type, track_url, upload_date, user_id, access, organism, center, track_user from track where track_id = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $id);
	$stmt->execute() or goBack("Error happened. Please try later.");
	if ($stmt->rowCount() == 0) {
		goBack("This track was deleted already");
	}
	$row = $stmt->fetch();
	// First, determine whether the track is editable to the user or not.
	$name = $row['track_name'];
	$type = $row['track_type'];
	$url = $row['track_url'];
	$date = $row['upload_date'];
	$user_id = $row['user_id'];
	$access = $row['access'];
	$organism = $row['organism'];
	$center = $row['center'];
	$tuser = $row['track_user'];
	$output[] = '<p>';
	$output[] = '<input type=button value=\'Browse Configurations\' onClick="window.location.href=\'brwconf.php\'">';
	$output[] = '&nbsp;&nbsp;';
	$output[] = '<input type=button value=\'Browse Tracks\' onClick="window.location.href=\'brwtrk.php\'">';
	$output[] = '</p>';
	$output[] = "<h1>Edit Track Information</h1>";
	$output[] = "<form action='editrk.php?action=save&id=$id' method='post'>";
	// Upload date.
	$output[] = "<p>Uploaded at: $date</p>";
	// Track access.
	$checked1 = $access == 'private' ? " checked" : "";
	$checked2 = $access == 'group' ? " checked" : "";
	$checked3 = $access == 'public' ? " checked" : "";
	$output[] = "<p>Access: ";
	$output[] = "<input type='radio' name='access' value='private'".$checked1.">Private&nbsp;&nbsp;";
	$output[] = "<input type='radio' name='access' value='group'".$checked2;
	if (preg_match('/guest/i', $user_id)) {
		$output[] = ' disabled';
	}
	$output[] = ">Group&nbsp;&nbsp;";
	$output[] = "<input type='radio' name='access' value='public'".$checked3;
	if (preg_match('/guest/i', $user_id)) {
		$output[] = ' disabled';
	}
	$output[] = ">Public&nbsp;&nbsp;";
	$output[] = '</p>';
	// Track type.
	$query = "select distinct track_type from annoj";
	$stmt = $db->prepare($query);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$output[] = "<p>Track type: ";
	while($row = $stmt->fetch()){
		$type1 = $row['track_type'];
		if ($type1 == 'ModelsTrack') {
			continue;
		}
		$checked = $type1 == $type ? " checked" : "";
		$output[] = "<input type='radio' name='trk_type' value='$type1'".$checked.">$type1&nbsp;&nbsp;";
	}
	$output[] = '</p>';
	$query = "select organism from organism";
	$stmt = $db->prepare($query);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$output[] = '<table border="0"><tr>';
	$output[] = '<td class="noborder">Organism:</td>';
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"radio\" name=\"organism\" value=\"$organism\" checked>$organism</td>";
	if ($organism != 'Homo sapiens') {
		$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"radio\" name=\"organism\" value=\"Homo sapiens\">Homo sapiens</td>";
	}
	if ($organism != 'Mus musculus') {
		$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"radio\" name=\"organism\" value=\"Mus musculus\">Mus musculus</td>";
	}
	if ($organism != '') {
		$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"radio\" name=\"organism\" value=\"Drosophila melanogaster\">Drosophila melanogaster</td>";
	}
	$output[] = "<td nowrap class=\"noborder\" align=\"left\">&nbsp;&nbsp;<input class=\"nodisp\" id=\"gg\" type=\"button\" value=\"More &raquo\" onClick=\"subttt('gg')\"></td>";
	$output[] = "</tr></table>";
	$output[] = "<table id=\"gggg\" width=\"90%\" style=\"display:none\">";
	$tcount = 0;
	while ($row = $stmt->fetch()) {
		$organism1 = $row['organism'];
		if (($organism1 == $organism) or ($organism1 == "Homo sapiens") or ($organism1 == "Mus musculus")) {
			continue;
		}
		if ($tcount == 0) {
			$output[] = '<tr>';
		}
		$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input type='radio' name='organism' value='$organism1'>$organism1</td>";
		if ($tcount == 3) {
			$tcount = 0;
			$output[] = '</tr>';
		} else {
			$tcount++;
		}
	}
	$output[] = '</table>';
	$output[] = "<p>Center (max: 80 chars): <input type='text' size='80' maxlength='80' name='center' value='$center' onmouseover='this.focus();this.select()' /></p>";
	$output[] = "<p>Track name (max: 80 chars): <input type='text' size='80' maxlength='80' name='trk_name' value='$name' onmouseover='this.focus();this.select()' /></p>";
	$output[] = "<p>Track URL (max: 120 chars): <input type='text' size='80' maxlength='120' name='trk_url' value='$url' onmouseover='this.focus();this.select()' /></p>";
	if ($checked3 == '') {
		$output[] = "<p>Grant access to (usernames or group names terminated by \",\"): <input type='text' size='80' name='avail' value='$tuser' onmouseover='this.focus();this.select()'";
		if (preg_match('/guest/i', $user_id)) {
			$output[] = ' disabled';
		}
		$output[] = "></p>";
	}
	// Save changes and enclose form.
	$output[] = "<p>";
	$output[] = "<input type='submit' value='Update' />";
	$output[] = '&nbsp;&nbsp;';
	$link  = "'editrk.php?action=delete&id=$id'";
	$output[] = "<input type=button value='Delete' onClick=\"confirmLocation('Do you really want to delete?', $link);\">";
	$output[] = "</p>";
	$output[] = "</form>";
	if (isset($_GET['fin'])) {
		$output[] = '<font color=red>Update successfully.';
	}
	return join('', $output);
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
	$output[] = '<td><a href="http://www.biomedcentral.com/1471-2105/9/547">Kyoung-Jae Won, Iouri Chepelev, Bing Ren and Wei Wang,	Prediction of Regulatory Elements in Mammalian Genomes Using Chromatin Signatures, BMC Bioinformatics, 2008, 9, 547</a></td>';
	$output[] = '</tr>';
	$output[] = '<tr>';
	$output[] = '<td nowrap><a href="dl_page.php?fn=5">Chromia<br>(TFBS identification)</a></td>';
	$output[] = '<td>Python</td>';
	$output[] = '<td>2010-02-01</td>';
	$output[] = '<td><a href="http://genomebiology.com/2010/11/1/R7">Kyoung-Jae Won, Bing Ren and Wei Wang,	Genome-wide prediction of transcription factor binding sites using an integrated model. Genome Biol. 2010 Jan 22;11(1):R7.</a></td>';
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
	if(!$id){
		trigger_error("Trying to remove invalid track id from configuration!");
	}
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
	$query = "select user_id, track_list from usertrack where (user_id != '$user_id') and ((track_list regexp '^$id,') or (track_list regexp ',$id,') or (track_list regexp ',$id$') or (track_list regexp '^$id$'))";
	$stmt = $db->prepare($query);
	$stmt->execute() or goBack("Error happened. Please try later.");
	while($row = $stmt->fetch()){
		$this_user_id = $row['user_id'];
		$track_list = $row['track_list'];
		$patterns = array("/^$id,/","/,$id,/","/,$id$/");
		$replacements = array('',',','');
		$track_list = preg_replace($patterns,$replacements,$track_list);
		$qq = "update usertrack set track_list = ? where user_id = ?";
		$ss = $db->prepare($qq);
		$ss->bindParam(1, $track_list);
		$ss->bindParam(2, $this_user_id);
		$ss->execute() or goBack("Error happened. Please try later.");
	}
}
function public_trk($id,$user_id) {
	if(!$id){
		trigger_error("Trying to remove invalid track id from configuration!");
	}
	global $db;
	$query = "select user_id, track_list from usertrack where (user_id != ?) and (user_id not in (select user_id from user where account_group='reviewer'))";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $user_id);
	$stmt->execute() or goBack("Error happened. Please try later.");
	while($row = $stmt->fetch()) {
		$this_user_id = $row['user_id'];
		$track_list = $row['track_list'];
		$patterns = array("/^$id,/","/,$id,/","/,$id$/");
		$replacements = array('',',','');
		$track_list = preg_replace($patterns,$replacements,$track_list);
		$track_list .= ','.$id;
		$qq = "update usertrack set track_list = ? where user_id = ?";
		$ss = $db->prepare($qq);
		$ss->bindParam(1, $track_list);
		$ss->bindParam(2, $this_user_id);
		$ss->execute() or goBack("Error happened. Please try later.");
	}
}

function group_trk($id,$user_id) {
	if(!$id){
		trigger_error("Trying to remove invalid track id from configuration!");
	}
	global $db;
	$query = "select account_group from user where user_id = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $user_id);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$row = $stmt->fetch();
	$groupid = $row['account_group'];
	unset($users);
	if ($groupid == 'SDEC') {
		$query = "select user_id from user where (user_id != ?) and ((account_group in (select group_name from sdecgroup)) or (account_group = 'sdec'))";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $user_id);
		$stmt->execute() or goBack("Error happened. Please try later.");
		while ($row = $stmt->fetch()) {
			$this_user_id = $row['user_id'];
			$this_user_id = "'$this_user_id'";
			$users[$this_user_id] = $this_user_id;
		}
	} elseif (($groupid == 'PUBLIC') or ($groupid == 'REVIEWER')) {
	} else {
		$query = "select user_id from user where user_id != ? and account_group = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $user_id);
		$stmt->bindParam(2, $groupid);
		$stmt->execute() or goBack("Error happened. Please try later.");
		while ($row = $stmt->fetch()) {
			$this_user_id = $row['user_id'];
			$this_user_id = "'$this_user_id'";
			$users[$this_user_id] = $this_user_id;
		}
	}
	$allusers = implode(',',$users);
	//$query = "select user_id, track_list from usertrack where user_id in (?)";
	$query = "select user_id, track_list from usertrack where user_id in ($allusers)";
	$stmt = $db->prepare($query);
	//$stmt->bindParam(1, $allusers);
	$stmt->execute() or goBack("Error happened. Please try later.");
	while ($row = $stmt->fetch()) {
		$this_user_id = $row['user_id'];
		$track_list = $row['track_list'];
		$patterns = array("/^$id,/","/,$id,/","/,$id$/");
		$replacements = array('',',','');
		$track_list = preg_replace($patterns,$replacements,$track_list);
		$track_list .= ','.$id;
		$qq = "update usertrack set track_list = ? where user_id = ?";
		$ss = $db->prepare($qq);
		$ss->bindParam(1, $track_list);
		$ss->bindParam(2, $this_user_id);
		$ss->execute() or goBack("Error happened. Please try later.");
	}
	$query = "select user_id, track_list from usertrack where (user_id not in ($allusers)) and (user_id != '$user_id') and ((track_list regexp '^$id,') or (track_list regexp ',$id,') or (track_list regexp ',$id$') or (track_list regexp '^$id$'))";
	$stmt = $db->prepare($query);
	$stmt->execute() or goBack("Error happened. Please try later.");
	while ($row = $stmt->fetch()) {
		$this_user_id = $row['user_id'];
		$track_list = $row['track_list'];
		$patterns = array("/^$id,/","/,$id,/","/,$id$/");
		$replacements = array('',',','');
		$track_list = preg_replace($patterns,$replacements,$track_list);
		$qq = "update usertrack set track_list = ? where user_id = ?";
		$ss = $db->prepare($qq);
		$ss->bindParam(1, $track_list);
		$ss->bindParam(2, $this_user_id);
		$ss->execute() or goBack("Error happened. Please try later.");
	}
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
	$user_id = $_SESSION['username'];
	if ($_POST['upload']) {
		$uploadyn = $_POST['uploadyn'];
		$access = $_POST['access'];
		$type = $_POST['trk_type'];
		$organism = $_POST['organism'];
		$center = $_POST['center'];
		$avail = trim($_POST['avail']);
		if ((!empty($avail)) and (!preg_match('/^e\.g\.\ /',$avail)) and ($access != 'public')) {
			$availflag = 1;
		} else {
			$availflag = 0;
		}
		if ($uploadyn == 'uploadn') {
			$cname = trim($_POST['trk_name']);
			if (preg_match('/^e\.g\.\ /',$cname)) {
				goBack("Please input track name.");
			}
			$aname = explode("\n", $cname);
			foreach ($aname as $loc => $name) {
				$name = trim($name);
				$aname[$loc] = $name;
				if (strlen($name)==0) {
					goBack("Track name can not be empty.");
				}
			}
			$curl = trim($_POST['trk_url']);
			if (preg_match('/^e\.g\.\ /',$curl)) {
				goBack("Please input track URL.");
			}
			$aurl = explode("\n", $curl);
			if (count($aname) != count($aurl)) {
				goBack("The number of track name(s) is not same with the number of track URL(s).");
			}
			foreach ($aurl as $loc => $url) {
				$url = trim($url);
				$aurl[$loc] = $url;
				if (strlen($url)==0) {
					goBack("Track URL can not be empty.");
				}
				if ((!preg_match('/^http/', $url)) or (!preg_match('/\.php$/', $url))) {
					goBack("The URL ($url) is not correct format.");
				}
			}
		} elseif ($uploadyn == 'uploady') {
			$sendemail = $_POST['sendemail'];
			$uploaddir = session_id();
			$uploaddir = '/tmp/uploads/' . $uploaddir . '/';
			if (!($handle = opendir($uploaddir))) {
				goBack("Upload failed.");
			}
			while (false !== ($file = readdir($handle))) {
				if ($file == "." || $file == "..") {
					continue;
				}
				$patterns = array("/\.[a-zA-Z]+$/");
				$replacements = array('');
				$newfile = preg_replace($patterns,$replacements,$file);
				rename("$uploaddir$file","$uploaddir$newfile");
				$aname[] = $newfile;
				$aurl[] = "http://$host/fetchers/upload_php/$newfile.php";
			}
			closedir($handle);
		}
		if ($access == 'group') {
			$query = "select account_group from user where user_id = ?";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $user_id);
			$stmt->execute() or goBack("Error happened. Please try later.");
			$row = $stmt->fetch();
			$groupid = $row['account_group'];
			unset($users);
			if ($groupid == 'SDEC') {
				$query = "select user_id from user where account_group in (select group_name from sdecgroup) or account_group = 'sdec'";
				$stmt = $db->prepare($query);
				$stmt->execute() or goBack("Error happened. Please try later.");
				while ($row = $stmt->fetch()) {
					$this_user_id = $row['user_id'];
					$this_user_id = "'$this_user_id'";
					$users[$this_user_id] = $this_user_id;
				}
			} elseif (($groupid == 'PUBLIC') or ($groupid == 'REVIEWER')) {
				$this_user_id = "'$user_id'";
				$users[$this_user_id] = $this_user_id;
			} else {
				$query = "select user_id from user where account_group = ?";
				$stmt = $db->prepare($query);
				$stmt->bindParam(1, $groupid);
				$stmt->execute() or goBack("Error happened. Please try later.");
				while ($row = $stmt->fetch()) {
					$this_user_id = $row['user_id'];
					$this_user_id = "'$this_user_id'";
					$users[$this_user_id] = $this_user_id;
				}
			}
			$allusers = implode(',',$users);
		}
		foreach ($aurl as $loc => $url) {
			$name = $aname[$loc];
			$newname = $name;
			$query = "select track_name from track where track_url = ?";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $url);
			$stmt->execute() or goBack("Error happened. Please try later.");
			if ($stmt->rowCount() != 0) {
				$tstamp = time();
				$newname .= '_' . $tstamp;
				$url = "http://$host/fetchers/upload_php/$newname.php";
			}
			$query = "insert into track (track_name, upload_date, track_url, track_type, user_id, access, organism, center) values (?, now(), ?, ?, ?, ?, ?, ?)";
			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $name);
			$stmt->bindParam(2, $url);
			$stmt->bindParam(3, $type);
			$stmt->bindParam(4, $user_id);
			$stmt->bindParam(5, $access);
			$stmt->bindParam(6, $organism);
			$stmt->bindParam(7, $center);
			$stmt->execute() or goBack("Error happened. Please try later.");
			$id = $db->lastInsertId();
			$refineids[$id] = $id;
			if ($uploadyn == 'uploady') {
				rename("$uploaddir$name","/data/upload-raw-data/$newname") or goBack("Can not read uploaded files.");
				$modelfile = 'model.php';
				if ($type == 'IntensityTrack') {
					$modelfile = 'intensity_model.php';
				}
				$pfile = fopen("/var/www/html/fetchers/upload_php/$newname.php", 'w') or goBack("Can not generate php file");
				fwrite($pfile, "<?php\n");
				fwrite($pfile, "require_once '../includes/common.php';\n\n");
				fwrite($pfile, "\$table = '$newname';\n");
				fwrite($pfile, "\$title = '$name';\n");
				fwrite($pfile, "\$info = '$name';\n");
				$mfile = fopen($modelfile, 'r');
				while(! feof($mfile)) {
					fwrite($pfile,fgets($mfile));
				}
				fclose($mfile);
				fclose($pfile);
				continue;
			}
			if ($access == 'private') {
				$query = "select track_list from usertrack where user_id = ?";
				$stmt = $db->prepare($query);
				$stmt->bindParam(1, $user_id);
				$stmt->execute() or goBack("Error happened. Please try later.");
				$row = $stmt->fetch();
				$track_list = $row['track_list'] . ',' . $id;
				$query = "update usertrack set track_list = ? where user_id = ?";
				$stmt = $db->prepare($query);
				$stmt->bindParam(1, $track_list);
				$stmt->bindParam(2, $user_id);
				$stmt->execute() or goBack("Error happened. Please try later.");
			} elseif ($access == 'public') {
				$query = "select user_id, track_list from usertrack where user_id not in (select user_id from user where account_group='reviewer')";
				$stmt = $db->prepare($query);
				$stmt->execute() or goBack("Error happened. Please try later.");
				while ($row = $stmt->fetch()) {
					$this_user_id = $row['user_id'];
					$track_list = $row['track_list'];
					$track_list .= ",$id";
					$qq = "update usertrack set track_list = ? where user_id = ?";
					$ss = $db->prepare($qq);
					$ss->bindParam(1, $track_list);
					$ss->bindParam(2, $this_user_id);
					$ss->execute() or goBack("Error happened. Please try later.");
				}
			} elseif ($access == 'group') {
				//$query = "select user_id, track_list from usertrack where user_id in (?)";
				$query = "select user_id, track_list from usertrack where user_id in ($allusers)";
				$stmt = $db->prepare($query);
				//$stmt->bindParam(1, $allusers);
				$stmt->execute() or goBack("Error happened. Please try later.");
				while ($row = $stmt->fetch()) {
					$this_user_id = $row['user_id'];
					$track_list = $row['track_list'] . ',' . $id;
					$qq = "update usertrack set track_list = ? where user_id = ?";
					$ss = $db->prepare($qq);
					$ss->bindParam(1, $track_list);
					$ss->bindParam(2, $this_user_id);
					$ss->execute() or goBack("Error happened. Please try later.");
				}
			}
			if ($availflag == 1) {
				make_available($id,$avail);
			}
		}
		if ($uploadyn == 'uploady') {
			echo "<br>Please check $sendemail. It may take several minutes.<br>";
			exit;
		} else {
			$_SESSION['refineids'] = $refineids;
			$_SESSION['submitids'] = $refineids;
			//$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			$extra = 'brwtrk.php#track_table';
			header("Location: http://$host$uri/$extra");
			exit;
		}
	}
	$output[] = "<form action='subtracks.php' method='post'>";
	$output[] = "<h3>Access: </h3>";
	$output[] = "<input type='radio' name='access' value='private' checked>Private <font style=\"font-size:12px\">(Only you can access to this track.)</font>";
	$output[] = "<br>";
	$output[] = "<input type='radio' name='access' value='group'";
	if (preg_match('/guest/i', $user_id)) {
		$output[] = ' disabled';
	}
	$output[] = ">Group <font style=\"font-size:12px\">(All but only your group members can access to this track.)</font>";
	$output[] = "<br>";
	$output[] = "<input type='radio' name='access' value='public'";
	if (preg_match('/guest/i', $user_id)) {
		$output[] = ' disabled';
	}
	$output[] = ">Public <font style=\"font-size:12px\">(Everyone can access to this track.)</font>";
	$query = "select track_type, aj_path from annoj";
	$stmt = $db->prepare($query);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$output[] = "<h3>Track type: </h3>";
	while($row = $stmt->fetch()){
		$type = $row['track_type'];
		if ($type == 'ModelsTrack') {
			continue;
		}
		$aj_path = $row['aj_path'];
		if ($type == 'ReadsTrack') {
			$checked = ' checked';
		} else {
			$checked = '';
		}
		$output[] = "<input type='radio' name='trk_type' value='$type'$checked>$type <font style=\"font-size:12px\">($aj_path)</font>";
		$output[] = '<br>';
	}
	$query = "select organism from organism";
	$stmt = $db->prepare($query);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$output[] = "<h3>Organism: </h3>";
	$output[] = '<table border="0">';
	$output[] = "<tr>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"radio\" name=\"organism\" value=\"Homo sapiens\" checked>Homo sapiens</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"radio\" name=\"organism\" value=\"Mus musculus\">Mus musculus</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"radio\" name=\"organism\" value=\"Drosophila melanogaster\">Drosophila melanogaster</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input class=\"organism\" type=\"radio\" name=\"organism\" value=\"Arabidopsis thaliana\">Arabidopsis thaliana</td>";
	$output[] = "<td nowrap class=\"noborder\" align=\"left\">&nbsp;&nbsp;<input class=\"nodisp\" id=\"gg\" type=\"button\" value=\"More &raquo\" onClick=\"subttt('gg')\"></td>";
	$output[] = "</tr></table>";
	$output[] = "<table id=\"gggg\" width=\"90%\" style=\"display:none\">";
	$tcount = 0;
	while ($row = $stmt->fetch()) {
		$organism = $row['organism'];
		if (($organism == "Arabidopsis thaliana") or ($organism == "Homo sapiens") or ($organism == "Mus musculus") or ($organism == "Drosophila melanogaster")) {
			continue;
		}
		if ($tcount == 0) {
			$output[] = '<tr>';
		}
		$output[] = "<td nowrap class=\"noborder\" align=\"left\"><input type='radio' name='organism' value='$organism'> $organism</td>";
		if ($tcount == 3) {
			$tcount = 0;
			$output[] = '</tr>';
		} else {
			$tcount++;
		}
	}
	$output[] = '</table>';
	$output[] = "<p><h3>Center <font style=\"font-size:12px\">(max: 80 chars)</font>:</h3></p><p><input type='text' size='80' maxlength='80' name='center' value='e.g. UCSD' onmouseover='this.focus();this.select()' /></p>";
	$output[] = "<p><h3>If necessary grant access to <font style=\"font-size:12px\">(usernames or group names terminated by \",\")</font>:</h3></p><p><input type='text' size='80' name='avail' value='e.g. username1,username2,groupname1,groupname2' onmouseover='this.focus();this.select()'";
	if (preg_match('/guest/i', $user_id)) {
		$output[] = ' disabled';
	}
	$output[] = " /></p>";
	$output[] = "<div>";
	$output[] = "<h3><input id='uploadraw1' type='radio' name='uploadyn' value='uploadn' checked onClick=\"changeDisplay('uploadraw')\">Upload data information";
	$output[] = "&nbsp;&nbsp;<input id='uploadraw3' type='radio' name='uploadyn' value='uploady' onClick=\"changeDisplay('uploadraw')\">Upload raw data</h3>";
	$output[] = "<div id=\"uploadraw2\" class=\"indented\" style=\"display:block\">";
	$output[] = "<p><h3>Track name <font style=\"font-size:12px\">(max: 80 chars each, terminated by newline)</font>:</h3></p><p><textarea rows = '3' cols = '90' name = 'trk_name' onmouseover='this.focus();this.select()'/>e.g. trackname1\n     trackname2\n     trackname3</textarea></p>";
	$output[] = "<p><h3>Track URL <font style=\"font-size:12px\">(max: 120 chars each, terminated by newline)</font>:</h3></p><p><textarea rows = '3' cols = '90' name = 'trk_url' onmouseover='this.focus();this.select()'/>e.g. http://urdomain/urtrk1.php\n     http://urdomain/urtrk2.php\n     http://urdomain/urtrk3.php</textarea></p>";
	$output[] = "</div>";
	$output[] = "<div id=\"uploadraw4\" class=\"indented\" style=\"display:none\">";
	$output[] = "<div> <div id=\"spanButtonPlaceHolder\"></div> <input id=\"btnCancel\" type=\"button\" value=\"Cancel Queue 1\" onclick=\"swfu.cancelQueue();\" disabled=\"disabled\" style=\"margin-left: 2px; font-size: 8pt; height: 22px;\" /> </div> <div> <div id=\"spanButtonPlaceHolder1\"></div> <input id=\"btnCancel1\" type=\"button\" value=\"Cancel Queue 2\" onclick=\"swfu1.cancelQueue();\" disabled=\"disabled\" style=\"margin-left: 2px; font-size: 8pt; height: 22px;\" /> <div id=\"divStatus\">0 Files Uploaded</div> </div> <div class=\"fieldset flash\" id=\"fsUploadProgress\"> </div>";
	$output[] = "<p><h3><label for=\"element_4\">Email address <font style=\"font-size:12px\">(for notification of status)</font>:</label></h3><div> <input id=\"element_4\" type=\"text\" maxlength=\"255\" name=\"sendemail\" value=\"@\"";
	$output[] = "</div>";
	$output[] = "</div></div>";
	$output[] = "<p><input type='submit' name= 'upload' value='Submit' /></p>";
	$output[] = "</form>";
	return join('', $output);
}
?>
