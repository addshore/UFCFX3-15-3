<?php
// This script grabs data from the DB and outputs an XML file
// @ASSIGNMENT 1.2

require_once __DIR__ . '/src/autoload.php';

$interactor = new DatabaseInteractor();
$generator = new HtmlGenerator();
$wikidata = new WikidataInteractor();

$champions = $interactor->getChampions();
$wikidataItems = $wikidata->getExtraData( $champions );

echo $generator->generate(
	$champions,
	$wikidataItems
);