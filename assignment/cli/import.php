<?php
// This script grabs the data for the assignment and imports it into the db

require_once( __DIR__ . '/../viewsource.php' );

if( php_sapi_name() == 'cli' ) {
	die( 'Can only be run as a command link script.' );
}

require_once __DIR__ . '/../src/autoload.php';

$fetcher = new DataFetcher( 'http://www.cems.uwe.ac.uk/~p-chatterjee/2014-15/modules/atwd1/assignment/chess_world_champions.html' );
$champions = $fetcher->fetch();

$interactor = new DatabaseInteractor();
$insertionResult = $interactor->insertChampions( $champions );

if( $insertionResult ) {
	echo "Import Done";
}