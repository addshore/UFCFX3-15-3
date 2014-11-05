<?php
// This script grabs data from the DB and outputs an XML file
// @ASSIGNMENT 1.2

require_once __DIR__ . '/src/autoload.php';

$interactor = new DatabaseInteractor();
$generator = new XmlGenerator();

header( 'Content-Type:text/xml' );

echo $generator->generate(
	$interactor->getChampions()
);