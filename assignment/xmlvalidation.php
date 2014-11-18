<?php
// Validates and shows validation of xml file

require_once __DIR__ . '/src/autoload.php';

$interactor = new DatabaseInteractor();
$generator = new XmlGenerator();

$xml = $generator->generate(
	$interactor->getChampions()
);

$dom = new DOMDocument;
$dom->loadXML( $xml );
$result = $dom->schemaValidate( __DIR__ . '/xml.xsd' );

// Provide some basic output....
if( $result ) {
	echo "Validation was successfull...";
} else {
	echo "Validation failed!";
}