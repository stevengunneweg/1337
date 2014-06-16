var loader = null,
	error = false;
window.onload = function() {
	loader = document.createElement("img");
	loader.src = "assets/loading.gif";
	loader.width = 32;
	loader.height = 10;

	getName();
	startServerSyncedClock();

	printScore('table_today', 'getToday', 0);
	// setInterval(function() {
	// 	printScore('table_today', 'getToday', 0);
	// }, 1000);

	printYesterday('time');
	printScore('table_score', 'getYesterday', 1);
}
function showError() {
	if (!error) {
		// alert('There is a problem on the server. Please try again later');
	}
	error = true;
}
function send() {
	if (!document.getElementById('name_field').value) {
		alert('fill in a name!\nHURRRYYY!!');
		document.getElementById('name_field').focus();
	} else {
		if (!error) {
			$.ajax({
			    data: 'action=new' + '&data=' + document.getElementById('name_field').value,
				url: 'manager.php',
				method: 'POST', // or GET
				success: function(msg) {
					alert(msg)
					location.reload();
				},
				error: function(e) {
					showError();
				}
			});
		}
	}
}
function getName() {
	if (!error) {
		$.ajax({
		    data: 'action=getNameWithIp',
			url: 'manager.php',
			method: 'POST', // or GET
			success: function(msg) {
				document.getElementById('name_field').value = JSON.parse(msg)[0]['name'];
			},
			error: function(e) {
				showError();
			}
		});
	}
}

function printToday(target) {
	var today = new Date();
	document.getElementById(target).innerHTML = today.getDate() + '/' + (today.getMonth() + 1) + '/' + today.getFullYear();
}
function printYesterday(target) {
	var yesterday = new Date();
	yesterday.setDate(yesterday.getDate() - 1);
	document.getElementById(target).innerHTML = '<b>' + yesterday.getDate() + '/' + (yesterday.getMonth() + 1) + '/' + yesterday.getFullYear() + '</b>';
}
function printWeek(target) {
	var date = new Date();
	document.getElementById(target).innerHTML = 'Best score of <b>week ' + getWeekNumber(date) + '</b>';
}
function printMonth(target) {
	var date = new Date();
	var months = ['January', 'February', 'March', 'April', 'May', 'June', 
		'July', 'August', 'September', 'October', 'November', 'December'];
	document.getElementById(target).innerHTML = 'Best scores of <b>' + months[date.getMonth()] + '</b>';
}
function printYear(target) {
	var date = new Date();
	document.getElementById(target).innerHTML = 'Best scores of <b>' + date.getFullYear() + '</b>';
}
function printText(target, text) {
	document.getElementById(target).innerHTML = text;
}
function printScore(target, action) {
	var targetTable = document.getElementById(target);
	targetTable.innerHTML = '';
	
	var _loader = loader.cloneNode();
	targetTable.parentNode.appendChild(_loader);
	
	if (!error) {
		$.ajax({
		    data: 'action=' + action,
			url: 'manager.php',
			method: 'POST', // or GET
			success: function(msg) {
				targetTable.parentNode.removeChild(_loader);
				
				result = JSON.parse(msg);
				if (result.length === 0) {
					emptyList(targetTable);
				} else {
					for (var i = 0; i < result.length; i++) {
						addToList(targetTable, result[i].name, result[i].moment, result[i].day);
					}
				}
			},
			error: function(e) {
				showError();
			}
		});
	}
}
function emptyList(table) {
	var row = table.insertRow(-1);
	var cell = row.insertCell(-1);
	cell.className = 'empty';
	cell.innerHTML = 'This list is currently empty';
}

function addToList(table, name, timestamp, day) {
	var row = table.insertRow(-1);
	
	var cell_name = row.insertCell(-1);
	cell_name.className = 'name';
	cell_name.innerHTML = name;
	
	var cell_score = row.insertCell(-1);
	cell_score.className = 'score';
	cell_score.setAttribute('title', 'posted on ' + day);
	cell_score.innerHTML = timestamp;
}

function getWeekNumber(d) {
	// Copy date so don't modify original
	d = new Date(+d);
	d.setHours(0,0,0);
	// Set to nearest Thursday: current date + 4 - current day number
	// Make Sunday's day number 7
	d.setDate(d.getDate() + 4 - (d.getDay()||7));
	// Get first day of year
	var yearStart = new Date(d.getFullYear(),0,1);
	// Calculate full weeks to nearest Thursday
	var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7)
	// Return array of year and week number
	return weekNo;
}
function makeActive(button) {
	var buttons = document.getElementsByTagName('button');
	for (var ding in buttons) {
		buttons[ding].className = '';
	}
	button.className = 'active';
}
function startServerSyncedClock() {
	var updatesPerSecond = 3,
		origTitle = document.title;
	var updateFunc = function() {
		if (!error) {
			$.ajax({
			    data: 'action=getServerTime',
				url: 'manager.php',
				method: 'POST', // or GET
				success: function(msg) {
					var time = msg.split(' ')[1].split('.')[0];
					$('#serverClock').text(time);
					document.title = origTitle + " - " + time;
				},
				error: function(e) {
					showError();
				}
			});
		}
	};
	setInterval(updateFunc, 1000/updatesPerSecond);
}