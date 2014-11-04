<?php

/**
 * Recursively include all src files once while keeping global scope clean
 */
call_user_func( function () {
	$dir = new RecursiveDirectoryIterator(__DIR__);
	$ite = new RecursiveIteratorIterator($dir);
	$files = new RegexIterator($ite, '/.*\.php/', RegexIterator::GET_MATCH);
	$fileList = array();
	foreach($files as $file) {
		$fileList = array_merge($fileList, $file);
	}
	foreach( $fileList as $fileLocation ) {
		include_once $fileLocation;
	}
} );