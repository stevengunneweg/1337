function setCookie(cname, cvalue, exdays) {
    var d = new Date();
	var expires = "";
	if (exdays > 0) {
    	d.setTime(d.getTime() + (exdays*24*60*60*1000));
    	expires = "expires="+d.toGMTString();
	}
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    }
    return "";
}

function setUsername(name) {
	setCookie("username", name, 50);
}
function getUsername() {
	var name = getCookie("username");
	if (name != "") {
		return name;
	} else {
		return false;
	}
}

