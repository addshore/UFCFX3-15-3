<?php
//This script creates the db scheme. This will only work once unless you run dropdb.php

if( php_sapi_name() == 'cli' ) {
	die( 'Can only be run as a command link script.' );
}

require_once __DIR__ . '/src/autoload.php';

call_user_func( function () {
	$creator = new DatabaseInteractor();
	$result = $creator->create();

	if( $result === false ) {
		echo 'Failed to create DB';
	} else {
		echo 'Done';
	}
} );

