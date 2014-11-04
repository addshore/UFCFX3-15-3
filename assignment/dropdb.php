<?php
//This script drops the db if it exists

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