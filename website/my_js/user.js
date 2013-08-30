function formCheck(formobj){
	// Enter name of mandatory fields
	var fieldRequired = Array("user_name", "user_pwd", "user_pwd_conf","user_tname","user_institute","user_address","user_email", "passcode");
	// Enter field description to appear in the dialog box
	var fieldDescription = Array("User ID", "Password", "Re-enter Password","Your Name","Institute","Address","Valid Email Address","Verify Code");
	// dialog message
	var alertMsg = "Please input:\n\n";
	var l_Msg = alertMsg.length;
	for (var i = 0; i < fieldRequired.length; i++){
		var ele = document.getElementById(fieldRequired[i]);
		if(!ele) continue;
		var obj = formobj.elements[fieldRequired[i]];
		if (obj){
			switch(obj.type){
			case "select-one":
				if (obj.value == "0"){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
				break;
			case "select-multiple":
				if (obj.selectedIndex == -1){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
				break;
			case "text":
			case "textarea":
				if (obj.value == "" || obj.value == null){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
				break;
			case "password":
				if (obj.value == "" || obj.value == null){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
				break;
			default:
			}
			if (obj.type == undefined){
				var blnchecked = false;
				for (var j = 0; j < obj.length; j++){
					if (obj[j].checked){
						blnchecked = true;
					}
				}
				if (!blnchecked){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
			}
		}
		if (obj.name == "user_email"){
			var reg_name = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/;
			if(!reg_name.test(obj.value))
			alertMsg += " - invalid email address!\n";
		}
	}

	if (alertMsg.length == l_Msg) {
		return true;
	} else {
		alert(alertMsg);
		return false;
	}
}

function checkedAll(obj) {
	var cb = document.getElementById(obj).getElementsByTagName("input");
	checked = cb[0].checked;
	for (var i = 1; i < cb.length; i++) {
		if (cb[i].type == "checkbox") {
			cb[i].checked = checked;
		}
	}
	var tr = document.getElementById(obj).getElementsByTagName("tr");
	if (checked) {
		for (var i = 1; i < tr.length; i++) {
			tr[i].className += ' checked';
		}
	} else {
		for (var i = 1; i < tr.length; i++) {
			tr[i].className = tr[i].className.replace(/\ *checked/ig, "");
		}
	}
}

function checkAll(obj) {
	var cb = document.getElementById(obj).getElementsByTagName("input");
	checked = cb[0].checked;
	for (var i = 1; i < cb.length; i++) {
		cb[i].checked = checked;
	}
	if (document.getElementById('aa')) { document.getElementById('aa').onClick = subt('aa'); }
	if (document.getElementById('bb')) { document.getElementById('bb').onClick = subt('bb'); }
	if (document.getElementById('cc')) { document.getElementById('cc').onClick = subt('cc'); }
	if (document.getElementById('dd')) { document.getElementById('dd').onClick = subt('dd'); }
	if (document.getElementById('ee')) { document.getElementById('ee').onClick = subt('ee'); }
	if (document.getElementById('ff')) { document.getElementById('ff').onClick = subt('ff'); }
	if (document.getElementById('gg')) { document.getElementById('gg').onClick = subt('gg'); }
}

function subt(obj) {
	var d = document.getElementById(obj);
	var name = obj + obj;
	var c = document.getElementById(name).getElementsByTagName("input");
	for (var i = 0; i < c.length; i++) {
		c[i].checked = d.checked;
	}
}

function subtt(obj) {
	var d = document.getElementById(obj);
	var name = obj + obj;
	var c = document.getElementById(name);
	if (d.checked == true) {
		c.style.display = 'block';
	} else {
		c.style.display = 'none';
	}
}

function subttt(obj) {
	var d = document.getElementById(obj);
	var name = obj + obj;
	var c = document.getElementById(name);
	if (d.value == "More \u00BB") {
		d.value = "Fewer \u00AB";
		c.style.display = 'block';
	} else {
		d.value = "More \u00BB";
		c.style.display = 'none';
	}
}

function changeDisplay(obj) {
	var name1 = obj + '1';
	var name2 = obj + '2';
	var name3 = obj + '3';
	var name4 = obj + '4';
	var d1 = document.getElementById(name1);
	var d2 = document.getElementById(name2);
	var d3 = document.getElementById(name3);
	var d4 = document.getElementById(name4);
	if (d1.checked == true) {
		d2.style.display = 'block';
	} else {
		d2.style.display = 'none';
	}
	if (d3.checked == true) {
		d4.style.display = 'block';
	} else {
		d4.style.display = 'none';
	}
}

function change(obj) {
	var tr = obj.parentNode.parentNode;
	if (obj.checked) {
		tr.className += ' checked';
	} else {
		tr.className = tr.className.replace(/\ *checked/ig, "");
    }
}

function main_table(obj){
	var table_obj=document.getElementById(obj);
	if(table_obj == null) return;

	var tr=document.getElementById(obj).getElementsByTagName("tr");
	var th = tr[0].getElementsByTagName("th");
	for(var i=0;i<th.length;i++){
		th[i].onmouseover = function() {
			if (this.className != "sorttable_nosort") {
				this.className += ' selected';
			}
		}
		th[i].onmouseout = function() {
			this.className = this.className.replace(/\ *selected/ig, "");
		}
	}
	for(var i=1;i<tr.length;i++){
		tr[i].onmouseover = function() {
			this.className += ' selected';
		}
		tr[i].onmouseout = function() {
			this.className = this.className.replace(/\ *selected/ig, "");
		}
	}
}

function confirmLocation(msg,link) {
	if (confirm(msg)) {
		window.location.href=link;
	}
}

function confirmSubmit(msg) {
	return confirm(msg);
}
