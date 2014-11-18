<?php

/**
 * Settings that should be loaded by everything
 */
mb_internal_encoding("UTF-8");
ini_set("default_charset", 'utf-8');

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

// Load the composer autoload file
require_once dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Load the libs
require_once dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'lib/php-json-ld/jsonld.php';
