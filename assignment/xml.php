<?php
// This script grabs data from the DB and outputs an XML file
// @ASSIGNMENT 1.2

require_once __DIR__ . '/src/autoload.php';

$interactor = new DatabaseInteractor();
$champions = $interactor->getChampions();

header( 'Content-Type:text/xml' );

$xml = new SimpleXMLElement( '<chesschampions/>' );
foreach( $champions as $champion ) {
	$championXml = $xml->addChild( 'champion' );
	$championXml->addAttribute( 'id', $champion->getId() );
	$championXml->addAttribute( 'name', $champion->getName() );
	$championXml->addAttribute( 'enwikilink', $champion->getEnwikilink() );

	foreach( $champion->getReigns() as $reign ) {
		$reignXml = $championXml->addChild( 'reign' );
		$reignXml->addAttribute( 'start', $reign->getStartYear() );
		$reignXml->addAttribute( 'end', $reign->getEndYear() );
		if( !is_null( $reign->getType() ) ) {
			$reignXml->addAttribute( 'type', $reign->getType() );
		}
	}

	foreach( $champion->getLocations() as $location ) {
		$locationXml = $championXml->addChild( 'location' );
		$locationXml->addAttribute( 'country', $location->getCountry() );
		if( !is_null( $location->getCountryLink() ) ) {
			$locationXml->addAttribute( 'country_link', $location->getCountryLink() );
		}
		if( !is_null( $location->getFlagUrl() ) ) {
			$locationXml->addAttribute( 'flag', $location->getFlagUrl() );
		}
		if( !is_null( $location->getHistorical() ) ) {
			$locationXml->addAttribute( 'historical', $location->getHistorical() );
		}
		if( !is_null( $location->getHistoricalLink() ) ) {
			$locationXml->addAttribute( 'historical_link', $location->getHistoricalLink() );
		}
	}
}

$dom = new DOMDocument;
$dom->preserveWhiteSpace = FALSE;
$dom->loadXML( $xml->asXML() );
$dom->formatOutput = TRUE;
echo $dom->saveXml();