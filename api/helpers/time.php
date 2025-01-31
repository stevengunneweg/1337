<?php
function getCurrentServerTime() {
	$cur_micro = microtime(true);
	$micro = sprintf('%06d', ($cur_micro - floor($cur_micro)) * 1000000);
	$date = new DateTime(date('Y-m-d H:i:s.'.$micro, intval($cur_micro)));
	return $date->format('Y-m-d H:i:s.u');
}
?>
