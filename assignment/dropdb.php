<?php
//This script drops the db if it exists

if( php_sapi_name() == 'cli' ) {
	die( 'Can only be run as a command link script.' );
}

require_once __DIR__ . '/src/autoload.php';

call_user_func( function () {
	$dropper = new DatabaseInteractor();
	$result = $dropper->drop();

	if( $result === false ) {
		echo 'Failed to drop DB';
	} else {
		echo 'Done';
	}
} );