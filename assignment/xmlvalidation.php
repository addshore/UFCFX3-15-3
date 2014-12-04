<?php
// Validates and shows validation of xml file

require_once( __DIR__ . '/viewsource.php' );

require_once __DIR__ . '/src/autoload.php';

$interactor = new DatabaseInteractor();
$wikidataInteractor = new WikidataInteractor();
$generator = new XmlGenerator();

$champions = $interactor->getChampions();
$xml = $generator->generate(
	$champions,
	$wikidataInteractor->getExtraData( $champions )
);

$dom = new DOMDocument;
$dom->loadXML( $xml );
$result = $dom->schemaValidate( __DIR__ . '/xml.xsd' );

echo "<html><head></head><body>";

// Provide some basic output....
if( $result ) {
	echo "<p>Validation was <strong>successful</strong>...</p>";
} else {
	echo "<p>Validation <strong>failed</strong>!</p>";
}

echo "<p>To view the source that decided this click <a href='?viewsource' >here</a></p>";

echo "</body></html>";
