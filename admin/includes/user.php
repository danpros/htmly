<?php

function user($key, $user=null) {
	$value = '../../admin/users/' . $user . '.ini';
	static $_config = array();
	if (file_exists($value)) {
		$_config = parse_ini_file($value, true);
		return $_config[$key];
	}
}